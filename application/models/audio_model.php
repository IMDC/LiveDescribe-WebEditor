<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Audio_Model extends CI_Model {

	private $fileName; 
	private $audioData = array();
	private $fp;
	private $sampleData = array();
	private $duration = null; 
	private $sampleRate;

	public function __construct(){
		// Call the Model constructor
		parent::__construct();
	}

	public function initialise($file){
		$this->fileName = $file;
		define("DETAIL", 5);
	}

	/**
	*	@return array audioData
	*	Reads the header of the wav file and then calls 
	*/
	public function readData(){
		ini_set('memory_limit', '1024M');
		$fields = join('/',array('H8ChunkID', 'VChunkSize', 'H8Format',
		'H8Subchunk1ID', 'VSubchunk1Size',
		'vAudioFormat', 'vNumChannels', 'VSampleRate',
		'VByteRate', 'vBlockAlign', 'vBitsPerSample',
		'vSubchunk2ID', 'vSubchunk2Size', 'vdata'));
		
		$this->fp        = fopen($this->fileName,'rb');
		$header          = fread($this->fp,44);
		$this->audioData = unpack($fields,$header);

		$this->sampleRate = $this->audioData['SampleRate'];
		$this->duration = ($this->audioData['ChunkSize'] * 8 ) / ( $this->audioData['SampleRate'] * $this->audioData['BitsPerSample']  * $this->audioData['NumChannels']);

        $this->getSamples();
      	$this->audioData['sampleValues'] = $this->sampleData;
      	// $spaces = $this->findSpaces($this->duration);
      	$spaces = array(); //not fully implemented so leave as empty array for now
        $this->audioData['spaces'] = $spaces;

      	return $this->audioData;
	}

	/**	 
	*	@return double $val
	*	finds the sample value based on the bytes 
	*	passed to the function.
	**/
	private function findValues($byte1, $byte2){
	    $byte1 = hexdec(bin2hex($byte1));                        
	    $byte2 = hexdec(bin2hex($byte2));
	    $val   = $byte1 + ($byte2*256);
	    return $val;
	 }

	 /**
	 *	Reads the wav file file fom the end of the header 
	 *	onwards until finished and stores the 
	 *	sample values in the array sampleData
	 */
	private function getSamples(){
		$bps = $this->audioData['BitsPerSample'];
		
		$byte = $bps / 8;

		$channel = $this->audioData['NumChannels'];

		$ratio = ($channel == 2 ? 40 : 80);

		$data_size = floor((filesize($this->fileName) - 44) / ($ratio + $byte) + 1);
		$data_point = 0;
		$index = 0;

		while(!feof($this->fp) && $data_point < $data_size){
			
			if ($data_point++ % DETAIL == 0) {

				// get number of bytes depending on bitrate
				for ($i = 0; $i < $byte; $i++)
					$bytes[$i] = fgetc($this->fp);
						  
				switch($byte){
					//8-bit
					case 1:
					  $data = $this->findValues($bytes[0], $bytes[1]);
					  break;

					// 16
					case 2:
					  if(ord($bytes[1]) & 128)
					    $temp = 0;
					  else
					    $temp = 128;

					  $temp = chr((ord($bytes[1]) & 127) + $temp);
					  $data = floor($this->findValues($bytes[0], $temp) / 256);
					  break;
				}

				// skip bytes for memory optimization
				fseek($this->fp, $ratio, SEEK_CUR);

				//value of the data point normalized
				$value =  ($data / 255);

				$this->sampleData[$index++] = $value;         
			} 
			else {
			  // skip this one due to lack of detail
			  fseek($this->fp, $ratio + $byte, SEEK_CUR);
			}
		}
		fclose($this->fp);
	}
	
	/**
	*	@return array spaces
	*	using the methods of voice activity detection (VAD),
	*	the spaces containing speech and non-speech will be
	*   located 
	*
	*/
	public function findSpaces($duration){

		$spaces = array();
		$min_e  = null;
		$min_f  = null;
		$min_sf = null;

		//check if sample data exists
		if(!$this->sampleData){
			//throw new Exception("No sample data exists.");
		}

		$frameDecision = array();		
		/*Initial base values*/
		$energy_primeThresh                = 1; //default
		$zcr_primeThresh				   = 30; //default

		$frameSize_expected                = 0.1; //100 milleseconds
		$numberOfFrames_expected           = round($duration / $frameSize_expected);
		$dimTotal                          = count($this->sampleData);

		$samplesPerFrame                   = round($dimTotal / $numberOfFrames_expected);
				
		/*actual values*/
		$frames                            = array_chunk($this->sampleData, $samplesPerFrame );
		$numberOfFrames                    = count($frames);
		$frameSize 						   = $duration / $numberOfFrames;
		$framesNeeded                      = 1 / $frameSize; //number of frames for 1 second


		//read first 10 frames as a benchmark 
		//(assuming the first 10 or so frames contain silence)
		for($f=0; $f < 10 ; $f++){
			$frame = $frames[$f];
			$frameDecision[$f] = 0; //mark frame as silence
			$_energy[$f] = $this->frameEnergy($frame);
			$_zcr[$f] = $this->zeroCrossingRate($frame);
		}
	
		$energy_avg = array_sum($_energy) / count($_energy);
		$zcr_min = min($_zcr);
		$zcr_primeThresh = $zcr_min;
		$energy_primeThresh = $energy_avg;
		
		for($f=10 ; $f < $numberOfFrames; $f++){
		
			$frame = $frames[$f];
			$energy = $this->frameEnergy($frame);
			$zcr = $this->zeroCrossingRate($frame);
			

			if(($energy <= $energy_primeThresh) && ($zcr >= $zcr_primeThresh)){ //then this frame should be unvoiced 
				$frameDecision[$f] = 0;
			}
			else{
				$frameDecision[$f] = 1;
			}
		}
		
		//print_r($frameDecision);
		$spaces = $this->labelTime($frameDecision, $duration);
		// print_r($spaces);
		return  $spaces;
	}

	/**
	*	@param array $frameDescision the frames that are marked as either voiced or unvoiced (1 , 0)
	*	@param int $duration the duration of the audio file in seconds
	*	@return array $timeMarks each entry represents one second of the video and will be marked as 1 or 0
	*
	*	This function will iterate throught the given array to find the silence points
	*	and will mark these points with a 0 or a 1 otherwise.
	*/
	private function labelTime($frameDecision, $duration){
		$timeMarks = array();
		$frameToSeconds = floor(count($frameDecision) / $duration);
		$v_count = 0;
		$s_count = 0;
		$seconds = 0;
		
		for($i = 10; $i < count($frameDecision) ; $i++){
			$frameDecision[$i] == 0 ? $s_count++ : $v_count++;
			if( $i % $frameToSeconds == 0 && $s_count >= 10){
				$timeMarks[$seconds] = 0;
				$seconds++;
				$s_count = 0;
				$v_count = 0;
			}
			else if($i % $frameToSeconds == 0 && $s_count < 10){
				$timeMarks[$seconds] = 1;
				$seconds++;
				$s_count = 0;
				$v_count = 0;
			}
		}

		return $timeMarks;
	}

	/**
	*	@param array $frame the PCM data of a given frame
	*	@return int $energy the energy calculated from the frame
	*/
	private function frameEnergy($frame){
		$energy = 0;
		for($i=0; $i < count($frame); $i++){
			//$energy += pow(255 * $frame[$i] - 128 ,2);
			$energy += pow($frame[$i] - 0.5 ,2) ;
		}
		return $energy;
	}

	/**
	*	@param array $frame the PCM data of a given frame
	*	@return double $zcr zero crossing rate calculated from the frame
	*/
	private function zeroCrossingRate($frame){
		$curr;
		$prev;
		$zcr = 0;
		for($i=1; $i < count($frame) ; $i++){
			$curr = $frame[$i] - 0.5;
			$prev = $frame[$i - 1] - 0.5;
			if($curr * $prev < 0){
				$zcr++;
			}
		}
		return $zcr;
	}
	
	/**
	*	@param array $frame the PCM data of a given frame
	*	@return int $arithmeticMean the arithmeticMean calculated from the frame
	*/
	private function arithmeticMean($frame){
		$arithmeticMean = 0;
		for($i=0; $i < count($frame); $i++){
			// $arithmeticMean += abs(255 * $frame[$i] - 128);
			$arithmeticMean += abs($frame[$i]);
		}
		return $arithmeticMean / count($frame);
	}

	/**
	*	@param array $frame the PCM data of a given frame
	*	@return int $geometricMean the geometricMean calculated from the frame
	*/
	private function geometricMean($frame){
		$geometricMean = 1;
		for($i=0; $i < count($frame); $i++){
			// $geometricMean *= abs(255 * $frame[$i] - 128);
			$geometricMean *= abs($frame[$i]);
		}
		return pow($geometricMean, 1/count($frame));
	}

}
?>

