/**
*
*   This JS file takes care of the setup required for the editor 
*   page of LiveDescribe.
*/

var audio_context;
var recorder;

$(document).ready(function(){  
  

  getCanvasElements();
              
  //set the size of the bottom section to fill the remainder of the screen
  bottomSectionResize();

  $('#segments').setUpCanvas();

  //receive the video duration
   $.ajax({
       type:'POST',
       async: false,
       url: "/projects/livedescribe/testing/ytRequest.php",
       data:{
              id: video_id,
              request_type: "videoDuration"
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

   


   //receive the data for the video audio
   $.ajax({
      type:'POST',
      url:  "/projects/livedescribe/testing/ytRequest.php",
      dataType: 'json',
      cache: false,
      data:{
            id: video_id,
            request_type: "strip",
            duration: videoDuration
      },
      success: function(json){
        $('#segments').css('visibility', 'visible');
        $('#timelineLoad').css('visibility', 'hidden');
        $('#timelineLoad').remove();

        //put all sampleData values into an array
        for(var i in json.sampleValues){
          audioSamples.push(json.sampleValues[i]);
        }
        
        //put all space values into an array
        for(var i in json.spaces){
          spaces.push(json.spaces[i]);
        }
        
        createWaveform();
        drawRecomendedSpaces();
      },
      error: function(json){
        $('#segments').css('visibility', 'visible');
        $('#timelineLoad').css('visibility', 'hidden');
        $('#timelineLoad').remove();

        alert( "An error occured while retrieving the videos audio data. Some information may not be displayed.");
      }

   });

  jsRecorderInit(); //set up the JS recorder by default

  ///
  //  Confirm when the user leaves the editor page
  ///
  window.onbeforeunload = function(e){
      return "If you leave this page any unsaved data will be lost!";
  }

});



function jsRecorderInit(){
  try {
      // webkit shim
      window.AudioContext = window.AudioContext || window.webkitAudioContext;
      navigator.getUserMedia = navigator.getUserMedia || navigator.webkitGetUserMedia;
      window.URL = window.URL || window.webkitURL;
      
      audio_context = new AudioContext;
      console.log('Audio context set up.');
      console.log('navigator.getUserMedia ' + (navigator.getUserMedia ? 'available.' : 'not present!'));
  } 
  catch (e) {
      //alert('No web audio support in this browser! Will now Load Flash Recorder');
      //check the status on the Wami recorder every 10 seconds
      interval = setInterval(function(){checkMic();}, 10000);
      setupRecorder(); // sets up the wami recorder
  }
    
  navigator.getUserMedia({audio: true}, startUserMedia, function(e) {
    console.log('No live audio input: ' + e);
  });
}


function startUserMedia(stream) {
  var input = audio_context.createMediaStreamSource(stream);
  console.log('Media stream created.');

  input.connect(audio_context.destination);
  console.log('Input connected to audio context destination.');

  recorder = new Recorder(input);
  console.log('Recorder initialised.');

  document.getElementById("recordButton").disabled = false;
  recordIMG = document.images["record"];
  recordIMG.src = "/projects/livedescribe/testing/images/recordButton.png";
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

  $.ajax({
      type: 'POST',
      url: 'record.php',
      data: fd,
      processData: false,
      contentType: false
  }).done(function(data) {
         console.log(" " + data);
  });
}