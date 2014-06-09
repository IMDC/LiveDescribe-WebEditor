/**
*	Description controls
*/
var descriptionList    = new Array();
var descriptionPlaying = false;
var muted              = false;
var previousVol;
var audio = null;


// Establish all variables that the Analyser will use
var canvas, ctx, source, context, analyser, fbc_array, bars, bar_x, bar_width, bar_height;


// Initialize the MP3 player after the page loads all of its HTML into the window
//window.addEventListener("load", initPlayer, false);

/**
*   Creates a frequency distribution graph 
*   from the given audio input 
*/
function visualiser(audio){
    window.AudioContext = window.AudioContext || window.webkitAudioContext || window.mozAudioContext;
    context = new AudioContext(); // AudioContext object instance
    analyser = context.createAnalyser(); // AnalyserNode method
    canvas = document.getElementById('analyser_render');
    ctx = canvas.getContext('2d');

    // Re-route audio playback into the processing graph of the AudioContext
    source = context.createMediaElementSource(audio); 
    source.connect(analyser);
    analyser.connect(context.destination);
    frameLooper();
}

/** 
*	frameLooper() animates any style of graphics you wish to the audio frequency
*   Looping at the default frame rate that the browser provides(approx. 60 FPS)
*/
function frameLooper(){
    window.requestAnimationFrame = window.requestAnimationFrame || window.mozRequestAnimationFrame ||
                             window.webkitRequestAnimationFrame || window.msRequestAnimationFrame;
    requestAnimationFrame(frameLooper);
    fbc_array = new Uint8Array(analyser.frequencyBinCount);
    analyser.getByteFrequencyData(fbc_array);
    ctx.clearRect(0, 0, canvas.width, canvas.height); // Clear the canvas
    ctx.fillStyle = '#00CCFF'; // Color of the bars
    
    bars = 100;
    for (var i = 0; i < bars; i++) {
        bar_x = i * 3;
        bar_width = 2;
        bar_height = -(fbc_array[i] / 2);

        //fillRect( x, y, width, height ) // Explanation of the parameters below
        ctx.fillRect(bar_x, canvas.height, bar_width, bar_height);
    }
}

/**
*   Volume control slider, calls the changeVolume 
*   function when the slider is changed
*/
$(function() {
    $( "#slider" ).slider({
        orientation: "horizontal",
        range: "min",
        min: 0.0,
        max: 1.0,
        value: 0.75,
        step: 0.1
    },
    {
        change: function( event, ui ) {
                    var value = $( "#slider" ).slider( "value" );
                    changeVolume(value);
                }
    }
    );
});

function changeVolume(volume){
    if(audio !== null){
        audio.volume = volume;
    }
    console.log("Volume level: " + volume);
}


/**
*   Mute the audio
*/
function mute(){
    previousVol =  $( "#slider" ).slider( "value" );
    console.log('Mute: ' + previousVol);
    if(!muted){
        document.getElementById("volumeimg").src = base_url + "assets/img/vol-mute.png";
        changeVolume(0);
        muted = true;
    }
    else{
        document.getElementById("volumeimg").src = base_url + "assets/img/volume_img.png";
        changeVolume(previousVol);
        muted = false;
    }
}


/**
*  Checks if there is a recording at the current time of the video
*  if a recording exists, play it with the video
*/
function checkForDescription(){
    var currentTime = player.getCurrentTime();
    var tollerance  = 0.1; //play a description if currentTime is +/- tollerance
    var sStatus     = player.getPlayerState();
    
    if(!descriptionPlaying && sStatus == 1){

       for(var i=0; i < descriptionList.length; i++){
           if(descriptionList[i].startTime >= (currentTime - tollerance) &&
            descriptionList[i].startTime <= (currentTime + tollerance) ){
                console.log("Description Detected");
                if(!descriptionList[i].extended)
                    playAudio(descriptionList[i], playerID);
                else
                    playExtended(descriptionList[i], playerID);
            }
       }
    }    
}

/**
*   plays the given audio file
*
*   File location: 
*   http://imdc.ca/projects/livedescribe/res-www/uploads/user/ userID / video_id / filename
*/
function playExtended(description, video_id){
    var sStatus = player.getPlayerState();
    var init_vol =  player.getVolume();
    player.setVolume(5);
    descriptionPlaying = true;

    console.log("Playing Extended Description: " + description.filename);
    audio = new Audio();
    visualiser(audio);
    audio.src = 'http://imdc.ca/projects/livedescribe/res-www/uploads/user' 
                + user_id + '/' + video_id + '/'
                + description.filename;
    audio.volume = $('#slider').slider('value');
    
    if(sStatus == 1) 
        play_pause();

    audio.play();
    
    descriptionLengthMS = (description.endTime - description.startTime) * 1000;
    setTimeout(function(){            
                    player.setVolume(init_vol);

                    if(player.getPlayerState() != 1)
                        play_pause();

                     var time = parseFloat(description.startTime) + 0.2;
                    player.seekTo(time);
                    descriptionPlaying = false;
                   
                },descriptionLengthMS);
}


/**
*   plays the given audio file
*
*   File location: 
*    http://imdc.ca/projects/livedescribe/res-www/uploads/user/ userID / video_id / filename
*/
function playAudio(description, video_id){
    var init_vol =  player.getVolume();
    player.setVolume(5);
    descriptionPlaying = true;

    console.log("Playing Description: " + description.filename);
    audio = new Audio();
    visualiser(audio);
    audio.src = 'http://imdc.ca/projects/livedescribe/res-www/uploads/user' 
                + user_id + '/' + video_id + '/'
                + description.filename;
    audio.volume = $('#slider').slider('value');
    audio.play();
    descriptionLengthMS = (description.endTime - description.startTime) * 1000;
    setTimeout(function(){
                    descriptionPlaying = false;     
                    player.setVolume(init_vol);
                },descriptionLengthMS);
}


/**
*   Creates all aspects corresponding to a description
*/
function createDescription(descID, timeStart, timeFinished, descriptionText, filename, extended){
    console.log("Description Added.");
  // creating new description objects and inserting them into an array.
  var description = new Description(
                          filename,
                          timeStart, timeFinished,
                          descriptionText, 
                          descID,
                          extended
                    );
  descriptionList.push(description);
  console.log("Added description: " +  description.filename);
}