/**
*
*   This JS file takes care of the setup required for the editor 
*   page of LiveDescribe.
*/

var audio_context;
var recorder;

var record_canvas; //displays mic input
var r_ctx;

$(document).ready(function(){  
  

  getCanvasElements();
              
  //set the size of the bottom section to fill the remainder of the screen
  //bottomSectionResize();

  $('#segments').setUpCanvas();

  //receive the video duration
  $.ajax({
       type:'GET',
       async: false,
       url: base_url + "app/getDuration", 
       data:{
              vID: video_id
            },
       success: function(response){
            videoDuration = response;
            console.log("duration: " + videoDuration);
       },
       error: function(json){
        $('#segments').css('visibility', 'visible');
        $('#timelineLoad').css('visibility', 'hidden');
        $('#timelineLoad').remove();

        alert( "An error occured while retrieving the videos audio data. Some information may not be displayed.");
      }   
   });

  //receive previous descriptions, if they exist
  $.ajax({
    type: 'POST',
    url: base_url + "app/getDescriptionData",
    dataType: 'json',
    cache: false,
    data: {vID: video_id},

    success: function(json){
      if(json != null){
        for(var i in json){
          console.log(json[i]);
          var descID = json[i].desc_id;
          var timeStart = json[i].start;
          var timeFinished = json[i].end;
          var filename = json[i].filename;
          var descriptionText = json[i].desc_text;
          var extended = json[i].extended == "0" ? 0 : 1;
          createDescription(descID, timeStart, timeFinished, descriptionText, filename, extended);
        }
        
      }
      else{
        console.log("No Previous Descriptions");
      }
    },
    error: function(response){
      console.log("Description retrieving error." + response);
    }
  });


   //receive the data for the video audio
  $.ajax({
      type:'GET',
      url:  base_url + "app/getAudioInfo", 
      dataType: 'json',
      cache: false,
      data:{
            vID: video_id,
      },
      success: function(json){
        console.log("Success: " + json);
        $('#segments').css('visibility', 'visible');
        $('#timelineLoad').css('visibility', 'hidden');
        $('#timelineLoad').remove();

        audioHeader = json;
        audioSamples = json.sampleValues;
        spaces = json.spaces;
      },
      error: function(json){
        $('#segments').css('visibility', 'visible');
        $('#timelineLoad').css('visibility', 'hidden');
        $('#timelineLoad').remove();
        console.log("Error: " + json);
        alert( "An error occured while retrieving the videos audio data. Some information may not be displayed.");
      }

   }).complete(function(){
        bottomSectionResize();
        createWaveform();
        drawRecomendedSpaces();
   });

  jsRecorderInit(); //set up the JS recorder by default

  //  Confirm when the user leaves the editor page
  window.onbeforeunload = function(e){
      return "If you leave this page any unsaved data will be lost!";
  }

});



function jsRecorderInit(){
  try {
      // webkit shim
      window.AudioContext = window.AudioContext || window.AudioContext || window.mozAudioContext;
      navigator.getUserMedia = navigator.getUserMedia || navigator.webkitGetUserMedia ||
                            navigator.mozGetUserMedia || navigator.msGetUserMedia;
      window.URL = window.URL || window.webkitURL;
      
      audio_context = new AudioContext();
      console.log('Audio context set up.');
      console.log('navigator.getUserMedia ' + (navigator.getUserMedia ? 'available.' : 'not present!'));
  } 
  catch (e) {
      alert('No web audio support in this browser. You will not be able to record audio!');
  }
    
  navigator.getUserMedia({audio: true}, startUserMedia, function(e) {
    console.log('No live audio input: ' + e);
  });
}


function startUserMedia(stream) {
  var input = audio_context.createMediaStreamSource(stream);
  console.log('Media stream created.' + input);

  //create the canvas that the audio will be analysed for
  var _rCanvas = document.createElement('canvas');
  _rCanvas.id = "analyser_render";
  var parent = document.getElementById("AVControls");
  parent.appendChild(_rCanvas);

  var zeroGain = audio_context.createGain();
  zeroGain.gain.value = 0;
  input.connect(zeroGain);
  zeroGain.connect(audio_context.destination);
  console.log("Input connected to muted gain node connected to audio context destination.")
  visualiser(input, audio_context); //set up freq. analyser

  /* This causes feedback, replaced with the zero gain node.*/
  //input.connect(audio_context.destination);
  //console.log('Input connected to audio context destination.');

  recorder = new Recorder(input);
  console.log('Recorder initialised.');

  document.getElementById("recordButton").disabled = false;
  recordIMG = document.images["record"];
  recordIMG.src = base_url + "assets/img/recordButton.png";
}


function startRecording() {
  recorder && recorder.record();
  console.log('Recording...');
}

function stopRecording(video_id , descID) {
  recorder && recorder.stop();
  console.log('Stopped recording.');
  
  recorder.exportWAV(function(blob) {
    uploadToServer(blob, video_id , descID);
  });

  recorder.clear();
}


function uploadToServer(blob , video_id , descID){

  var url = URL.createObjectURL(blob);
  var fd = new FormData();

  fd.append('data', blob);
  fd.append('id', video_id);
  fd.append('descID', descID);
  fd.append('userID', userID);

  $.ajax({
      type: 'POST',
      url: base_url + 'app/recordAudio',
      data: fd,
      processData: false,
      contentType: false,
	success: function() { //REMOVE THIS LATER
		alert("call recordAudio");
	}
  }).done(function(data) {
      console.log(" " + data);
  });
}


/**
*   Creates a frequency distribution graph 
*   from the given audio input 
*/
function visualiser(source, context){
    analyser      = context.createAnalyser(); // AnalyserNode method
    record_canvas = document.getElementById('analyser_render');
    r_ctx         = record_canvas.getContext('2d');
    source.connect(analyser);
    frameLooper();
}

/** 
* frameLooper() animates any style of graphics you wish to the audio frequency
*   Looping at the default frame rate that the browser provides(approx. 60 FPS)
*/
function frameLooper(){

  window.requestAnimationFrame = window.requestAnimationFrame || window.mozRequestAnimationFrame ||
                             window.webkitRequestAnimationFrame || window.msRequestAnimationFrame;
  requestAnimationFrame(frameLooper);

  fbc_array = new Uint8Array(analyser.frequencyBinCount);
  analyser.getByteFrequencyData(fbc_array);
  r_ctx.clearRect(0, 0, record_canvas.width, record_canvas.height); // Clear the canvas
  r_ctx.fillStyle = '#00CCFF'; // Color of the bars
  
  bars = 100;
  for (var i = 0; i < bars; i++) {
      bar_x = i * 3;
      bar_width = 2;
      bar_height = -(fbc_array[i] / 2);

      //fillRect( x, y, width, height ) // Explanation of the parameters below
      r_ctx.fillRect(bar_x, record_canvas.height, bar_width, bar_height);
  }
}
