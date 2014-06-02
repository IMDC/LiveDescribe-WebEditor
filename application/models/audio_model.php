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
      	$spaces = $this->findSpaces($this->duration);
      	//$spaces = array(); //not fully implemented so leave as empty array for now
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
	*	@param double : duration of the audio file
	*	@return array spaces
	*	using the methods of voice activity detection (VAD),
	*	the spaces containing speech and non-speech will be
	*   located 
	*
	*/
	public function findSpaces($duration){

		$spaces = array();
		$ratio = $this->audioData['NumChannels'] == 2 ? 40 : 80;
		$samples_per_second = $this->audioData['SampleRate'] * ($this->audioData['BlockAlign'] / ($ratio * DETAIL));

		$zcr_histogram = array();
		$energy_histogram = array();
		$windowSize = 1; // 1 SECOND
		$window = 0;
		$bin = array(); //section of the audio data

		//check if sample data exists
		if(!$this->sampleData){
			return $spaces; // an empty array
		}

		while($window < $duration){
			$start = round($window * ($windowSize * $samples_per_second));

			if($start + $samples_per_second < count($this->sampleData)){
				$bin = array_slice($this->sampleData, $start, round($samples_per_second));
				array_push($zcr_histogram, $this->zeroCrossingRate($bin));
				array_push($energy_histogram, $this->frameEnergy($bin));
			}
			$window++;
		}

		$zcr_factor = 1; //0.8;
		$energy_factor = 0.4;

		$zcr_avg = $zcr_factor * array_sum($zcr_histogram) / count($zcr_histogram);
		$energy_avg = $energy_factor * array_sum($energy_histogram) / count($energy_histogram);
		
		//decide if a window should be marked as speech or not
		for($i = 0; $i < count($zcr_histogram) ; $i++){

			if( $zcr_histogram[$i] == 0 || 
				$energy_histogram[$i] == 0 || 
				($zcr_histogram[$i] > $zcr_avg && $energy_histogram[$i] < $energy_avg)){
				//no speech
				//check consecutive windows to find the end of this space
				for($j = $i + 1; $j < count($zcr_histogram); $j++){
					if( ! ($zcr_histogram[$j] == 0 || $energy_histogram[$j] == 0 || 
					($zcr_histogram[$j] > $zcr_avg && $energy_histogram[$j] < $energy_avg))){
						array_push($spaces, 0);
						$i = $j;
						break;
					}
				}
			}
			else{
				array_push($spaces, 1);
			}
		}
		return  $spaces;
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
			if( ($curr * $prev) < 0 ){
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

