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
       type:'GET',
       async: false,
       url: base_url + "app/getDuration", //"/projects/livedescribe/testing/ytRequest.php",
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
          createDescription(descID, timeStart, timeFinished, descriptionText, filename);
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
      url:  base_url + "app/getAudioInfo", ///projects/livedescribe/testing/ytRequest.php",
      dataType: 'json',
      cache: false,
      data:{
            vID: video_id,
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

  var zeroGain = audio_context.createGain();
  zeroGain.gain.value = 0;
  input.connect(zeroGain);
  zeroGain.connect(audio_context.destination);
  console.log("Input connected to muted gain node connected to audio context destination.")
  
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
      contentType: false
  }).done(function(data) {
      console.log(" " + data);
  });
}