/**
 *  Main functions of the video controls and
 *   initial loading of the editing page
 *
 *  
 */
 

///
//  Variables required for the YouTube player
///
var tag            = document.createElement('script');
tag.src            = "//www.youtube.com/iframe_api";
var firstScriptTag = document.getElementsByTagName('script')[0];
firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);


///
//  Global Variables used by the 
//  Player and recording functions
///
var player;
var video_id_flash; //ignore for now...cannot get the url from video unless using the flash player
var video_id           = null;//'Hrwbpb7Lk1w';//'MeLp2qr2iCg';//'rU7iYYpSrlo';
var descriptionPlaying = false;
var dragging           = false;
var videoDuration;
var muted              = false;
var previousVol;
var selectedSpace      = null;
var positionTimeout    = 1;
var posInt             = null;

/**
*   The canvas elements
*/
var markerCanvas;
var timeCanvas;
var canvas;
var ctx;
var tcx;
var mtx;

/**
*   Array to contain the audio sample data 
*/
var audioSamples = new Array();
var spaces       = new Array();


/**
*   when the browser window is resized, the 
*   canvas width is adjusted accordingly
*/
$(window).resize(function() {
    if( document.readyState === 'complete' ){
        console.log("window resized");
        var canvasWidth  = canvas.clientWidth;
        ctx.canvas.width = canvasWidth;
        
        //the size has changed so the paging needs to be changed
        setUpPaging();
        
        bottomSectionResize();
        
        //when the canvas size changes, the drawn objects get 
        //removed so we need to go through the array of description 
        //objects and re-draw them
        reDrawSpaces();
        
        var currentTime = player.getCurrentTime();
        moveMarker(currentTime);

        createWaveform(audioSamples);
        drawRecomendedSpaces();
    }
});



///
//allows the position marker to be dragged along the timeline
///
$(function() {
    $( "#positionMarker" ).draggable(
    {axis: "x"},
    {containment: '#segments'},

    {drag: function(event,ui){ 
            if(player.getPlayerState() != 2){
              player.pauseVideo();
            }     

            moveTo(event);
            dragging = true;
            //console.log(dragging);
          }
    },

    {stop: function(event){
            dragging = false;
          }
    }
    );
});


///
//Volume control slider, calls the changeVolume 
//function when the slider is changed
///
$(function() {
    $( "#slider" ).slider(
        {value: 30 },
        {change: function( event, ui ) {
                    var value = $( "#slider" ).slider( "value" );
                    changeVolume(value);
                }
        }
    );

    //Drop down menus enabled
    $('.dropdown-toggle').dropdown();
});


/**
*  Canvas Listener, Used to move the description spaces, initialized 
*  when 'editor' is finished loading
*
*  NOTE: the clickTime variable is always zero when using html5 video
*  this is probably a problem with the YouTUbe API for the html5 video trial
*/
jQuery.fn.setUpCanvas = function(){
    
    var found = false;
    var dStart;
    var dLength;
    var i;
    var previous = null;
    var next = null;

    //handles the click and drag on visual description spaces
    $('#segments').mousedown(function(event){
        
        var clickTime = getClickTime(event);
        console.log('click time: '  + clickTime);

        //checking to see if mouse is within a description
        for(i=0; i < descriptionList.length ; i++){
            var desc = descriptionList[i];
            
            //if a description is selected
            if(clickTime >= desc.startTime && clickTime <= desc.endTime ){
                found = true;

                if(i > 0)
                  previous = descriptionList[i -1];
                if(i != descriptionList.legth)
                  next = descriptionList[i + 1];

                selectedSpace = desc;
                dStart = desc.startTime;
                dLength = desc.endTime - desc.startTime;
                break;
            }
            else{
                selectedSpace = null;
            }
        }

        $('#segments').bind('mousemove',function(event){
             if(found){
                clear();

                clickTime = getClickTime(event);

                //check if this position interferes with the adjacent descriptions
                //left of the description
                if((previous != null) && (clickTime <= previous.endTime)){
                  selectedSpace.startTime = previous.endTime + 0.1; //offset by 0.1
                  selectedSpace.endTime = previous.endTime + dLength + 0.1;
                  updateDescriptionList(selectedSpace,selectedSpace.startTime, selectedSpace.endTime);
                }

                //right of the description
                else if( (next != null) && ((clickTime + dLength) >= next.startTime)){
                  selectedSpace.startTime = next.startTime - dLength - 0.1; //offset by 0.1
                  selectedSpace.endTime = next.startTime - 0.1;
                  updateDescriptionList(selectedSpace,selectedSpace.startTime, selectedSpace.endTime);
                }

                //otherwise there's no interference
                else{
                  selectedSpace.startTime = clickTime;
                  selectedSpace.endTime = clickTime + dLength;
                  updateDescriptionList(selectedSpace,selectedSpace.startTime, selectedSpace.endTime);
                }
                
                reDrawSpaces();
             }
        });
  
     });
    
    $('#segments').mouseup(function(){
        $('segments').unbind('mousemove');
        found    = false;
        previous = null;
        next     = null;

        if(selectedSpace != null){
            console.log('Selected file: ' + selectedSpace.filename);
        }
    });

};


function updateDescriptionList(description, newStart, newEnd){
    var start        = convertTime(newStart);
    var end          = convertTime(newEnd);
    var newTimeStamp = start + ' - ' + end;

    $('#timeStamp_' + description.id).html(newTimeStamp);
}

/*
  Will need to introduce paging functionality here
*/
function getClickTime(event){
    var clickStart  = event.offsetX; 
    var canvasWidth = canvas.clientWidth;
    var clickTime   = (clickStart /canvasWidth)* player.getDuration();
    return clickTime;
}

function clear(){
    var segments       = document.getElementById("segments");
    var segmentsWidth  = segments.clientWidth;
    var segmentsHeight = segments.clientHeight;
   
    ctx.clearRect(0,(segmentsHeight / 2) + 40, segmentsWidth * 2 , segmentsHeight * 2);
}


/**
*    Sets all the canvas elements to the global variables
*/
function getCanvasElements(){
  markerCanvas         = document.getElementById("positionMarker");
  timeCanvas           = document.getElementById("timeBar");
  canvas               = document.getElementById("segments");
  ctx                  = canvas.getContext('2d');
  tcx                  = timeCanvas.getContext('2d');
  mtx                  = markerCanvas.getContext('2d');
}



/**
*   Called when the youtube frame has loaded successfully
*   createds the marker which indicates the posiiton of the video on 
*   the timeline canvas
*/
function drawMarker(){
    var canvasWidth  = canvas.clientWidth;
    var headerHeight = $('#header').height();
    var upperHeight  = $('#AVControls').height();
    var screenHeight = $(window).height();
    var bottomHeight = screenHeight - headerHeight - upperHeight - 7;
    var half         = (canvas.clientHeight / 2) + 30;
    
    ctx.canvas.width = canvasWidth;
    mtx.canvas.width = 30;

    mtx.lineWidth = 5;

    mtx.beginPath();
    mtx.moveTo(0,0);
    mtx.lineTo(0,canvas.clientHeight);
    mtx.stroke();

    mtx.beginPath();
    mtx.moveTo(0,0);
    mtx.lineTo(0,25);
    mtx.lineTo(25,0);
    mtx.fill();


    ctx.lineWidth = 5;
    ctx.beginPath();
    ctx.moveTo(0,half);
    ctx.lineTo(canvasWidth,half);
    ctx.stroke();

    timeCanvas.addEventListener('click', moveTo, false);
    
 
}

/**
*    Sets up the paging functionality for the timeline
*/
function setUpPaging(){
    var canvasWidth          = document.getElementById("timeline").clientWidth;
    var seconds_between_tics = 1;
    var mainTic              = 5;
    var maxCanvasWidth       = 4096;//8192;//32000;
    var pageTime             = 30; //default
    var pages;
    var t;
    var width_total;
    

    if(videoDuration < pageTime){
      pageTime = videoDuration;
    }
    
    //find out paging info
    pages = videoDuration / pageTime;
    width_total = pages * canvasWidth;

    if(width_total > maxCanvasWidth){//width is too large and need to find an appropriate pageTime
        pageTime             = Math.ceil(((videoDuration * canvasWidth)/maxCanvasWidth)/10)*10;
        width_total          = maxCanvasWidth;
        mainTic              = Math.ceil(pageTime/100) *10;
        seconds_between_tics = Math.floor(mainTic / 10);
    }

    //setting the widths
    canvas.style.width     = width_total + "px";
    timeCanvas.style.width = width_total + "px";
    ctx.canvas.width       = width_total;
    tcx.canvas.width       = width_total;

    //draw the numbers on the time bar
    /*
      need: 
                  time, seconds_between_tics & lenght_in_px
      calculate:
                  #ofTics = time / seconds_between_tics
                  px_between_tics = length_in_px / #ofTics
    */
    var numTics         =  videoDuration / seconds_between_tics;
    var px_between_tics = width_total / numTics;
    var tic             = 0; //start at the 0 position
    positionTimeout     = (1000 * seconds_between_tics)/px_between_tics; 

    

    tcx.fillStyle = "black";
    tcx.font      = "bold 14px calibri";
    tcx.strokeStyle = "white";
    tcx.lineWidth = 2;

    for(t=1; t < videoDuration ; t++){

      if(t % mainTic == 0){
        tic += px_between_tics;
        
        //draw the tic mark large
        tcx.beginPath();
        tcx.moveTo(tic, 2);
        tcx.lineTo(tic, 28);
        tcx.stroke();
        tcx.fillText(convertTime(t), tic - 18 , 20,40);
      }
      else if(t % seconds_between_tics == 0){
        tic += px_between_tics;

        if(px_between_tics > 10){
          //draw the tic mark small
          tcx.beginPath();
          tcx.moveTo(tic, 10);
          tcx.lineTo(tic, 20);
          tcx.stroke();
        }
        if((px_between_tics > 60) && (t % 2 == 0)){ //also draw time on small tics
          tcx.fillText(convertTime(t), tic - 18 , 20,40);
        }
      }
    }
  
    // if(posInt != null){
    //   clearInterval(posInt);
    // }
    // posInt = setInterval(positionUpdate, positionTimeout);
    positionInterval = setInterval(positionUpdate, 42);
    drawMarker();
}


/**
*      This function creates an <iframe> (and YouTube player)
*      after the API code downloads.
*/
function onYouTubeIframeAPIReady(){
    player = new YT.Player
            ('player',
                {
                    height: '340',
                    width: '560',
                    videoId: video_id,
                    playerVars: { 'autoplay': 0, 
                                  'controls': 0,
                                  'iv_load_policy': 3,
                                  'rel': 0,
                                  'showinfo':0
                                },
                    events: {
                                 'onReady': onPlayerReady,
                                 'onStateChange': onPlayerStateChange
                             }
                });
}

/**
* The YT API will call this function when the video player is ready.
*/
function onPlayerReady(event) {
    var url        = player.getVideoUrl();
    var urlChunks  = url.split('=');
    video_id_flash = urlChunks[urlChunks.length - 1];
    //videoDuration  = player.getDuration();
     
 
    //Call the function timeupdate every 10 milliseconds.
    //So that the progressbar can expand gradually.
    var delay = 10; 
    window.setInterval(function(){
                            timeupdate(); 
                            checkForDescription(); 
                            checkMarker();
                        } , delay);
    setUpPaging();
    
}


function play_pause() {
    var sStatus =player.getPlayerState();
    var playpause = document.images["playpause"];
  
    if(!isRecording){
        //pauses the video
        if (sStatus == 1){                        
            player.pauseVideo();
            playpause.src = base_url + "assets/img/playButton3.png" ;
        }     

        //plays the video
        else {
            playpause.src = base_url + "assets/img/pauseButton2.png" ;  
            player.playVideo();
        }	
    }
}


function stopVideo() {
    var playpause = document.images["playpause"]
    playpause.src = base_url + "assets/img/playButton3.png" ;

    player.pauseVideo();	 
}



/**
*  The API calls this function when the player's state changes.
*    The function indicates that when playing a video (state=1)
*/
function onPlayerStateChange(event){
    var playpause = document.images["playpause"];
    //if the player was stopped or paused, and the state is changed to 
    //play, then change the play image to the pause image
    if (player.getPlayerState() == 1 ){
        playpause.src = base_url + "assets/img/pauseButton2.png" ;
    }

    //if the player was playing, and the state is changed to 
    //paused, then change the pause image to the play image
    if (player.getPlayerState() == 2) {
        playpause.src = base_url + "assets/img/playButton3.png" ;
    }
}

/**
*  This function is called on a set interval and it updates the position
*  marker on the timeline and the time display
*/
function timeupdate() { 
    if(!dragging){
        var currentTime = player.getCurrentTime();
        var duration    = player.getDuration();

        timeInfo.innerHTML = convertTime(currentTime) +" / "+ convertTime(duration);
    }
}


/**
*  Update the position marker
*/
function positionUpdate(){
  
    if(!dragging && (player.getPlayerState() == 1)){
        //var markerPos = document.getElementById("positionMarker").offsetLeft;
        //document.getElementById("positionMarker").style.left = markerPos +  1  + "px";

        var currentTime = player.getCurrentTime();
        var duration    = player.getDuration();
        var percentage  = canvas.width * (currentTime / duration);
        document.getElementById("positionMarker").style.left = percentage  + "px";
        
    }
}

/**
*  Converts the time to the form hh:mm:ss
*/
function convertTime(org){
    var minute = Math.floor(org / 60);
    var second = Math.floor(org % 60);
    
    if(minute < 10)
        minute = "0" + minute;
    if(second < 10)
        second = "0" + second;

    timeCode = minute + " : " + second;
    
    return timeCode;
}



/**
*  finds where to seek to in the video given the mouse location
*  Will need to introduce paging functionality here
*/
function getSeekLocation(xPos){
    var canvasWidth  = document.getElementById('segments').clientWidth;
    var vidPercent   = (xPos)/canvasWidth;
    var vidLength    = player.getDuration();
    var seekLocation = vidLength * vidPercent;
    return seekLocation;
}

//navigates to the click location on the progress bar
function moveTo(event){
  var scrolledPixels = $('#timeline').scrollLeft();
  var seekLocation = getSeekLocation(event.clientX - 200 + scrolledPixels);
  console.log("seekLocation " + seekLocation);
  moveMarker(seekLocation);
  player.seekTo(seekLocation);  
}

function playAt(event){
    var seekLocation = getSeekLocation(event.offsetX);
    //player.seekTo(seekLocation); 
    player.playVideoAt(seekLocation);
}


function changeVolume(volume){
    player.setVolume(volume);
    console.log("Volume level: " + volume);
}


/**
*   Mute the audio
*/
function mute(){
    previousVol =  $( "#slider" ).slider( "value" );
    console.log('Mute: '+previousVol);
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
    
    if(!dragging && !isRecording && !descriptionPlaying && sStatus==1){

       for(var i=0; i < descriptionList.length; i++){
           if(descriptionList[i].startTime >= (currentTime - tollerance) &&
            descriptionList[i].startTime <= (currentTime + tollerance) ){
                console.log("description detected");
                playAudio(descriptionList[i], video_id);
            }
       }
    }    
}

/**
*   plays the given audio file
*
*   File location: 
*    http://imdc.ca/projects/livedescribe/res-www/uploads/user/ userID / video_id / filename
*/
function playAudio(description, video_id){
    var init_vol =  $( "#slider" ).slider( "value" );
    descriptionPlaying = true;
    $("#slider").slider("value",5);

    console.log("Playing Description: " + description.filename);
    var audio = new Audio();
    audio.src = 'http://imdc.ca/projects/livedescribe/res-www/uploads/user' 
                + userID + '/' + video_id + '/'
                + description.filename;
    audio.play();
    descriptionLengthMS = (description.endTime - description.startTime) * 1000;
    setTimeout(function(){
                    descriptionPlaying = false;     
                    $("#slider").slider("value",init_vol);
                },descriptionLengthMS
            );
}

/**
*  Re-draws the description spaces on the canvas
*/
function reDrawSpaces(){
    
    var videoDuration  = player.getDuration();
    var segments       = document.getElementById("segments");
    var segmentsWidth  = segments.clientWidth;
    var segmentsHeight = segments.clientHeight;

    for (var i=0; i<descriptionList.length;i++){
        descriptionList[i].draw(videoDuration,segmentsWidth,segmentsHeight);
    }  
}


/**
*   Delete the seleceted desription from the description list
*   and remove any corresponding elements in the document
*/
function deleteDescription(descID){

    //remove the description from the array
    for(i=0; i < descriptionList.length; i++){
        if(descriptionList[i].id == descID){//then remove the description
            console.log('Removed from list: ' + descriptionList[i].id);
            descriptionList.splice(i,1);
            break;
        }
    }
    deleteRemoteFile(video_id, descID);    
}


/**
*   Deletes the file from the server with the given videio id and description id
*   if it is successful, then the visual description elements are removed from the page
*/
function deleteRemoteFile(video_id, description_ID){
    $.ajax({
       type:'POST',
       url: base_url + "app/removeFile",
       data:{
              vID: video_id,
              descID: description_ID,
            },
       success: function(response){
            console.log(response);
            //remove the list item
            $('#' + description_ID).fadeOut("slow", function(){
                $('#' + description_ID).remove();
            });
            //remove the visual description area
            clear();
            reDrawSpaces();
       }    
    });              
}


/*
    Gets called by an event on the text area, and calls the 
*/
function changeDescription(id){

  var newText = $('#text_' + id).val();

  for(i=0; i < descriptionList.length; i++){

      if(descriptionList[i].id == id){
        descriptionList[i].textDescription = newText;
        break;
      }
  }
}


/**
*    This will check if the marker is within view of the 
*    canvas element, if it is about to be then it will
*    scroll the canvas in order for it to be in view 
*/
function checkMarker(){

  var timelineWidth  = $('#timeline').width();
  var scrolledPixels = $('#timeline').scrollLeft();
  var markerPosition = $('#positionMarker').position().left;
  var maxPosition    = 0.95 * timelineWidth;
  var sStatus        = player.getPlayerState();

  if( markerPosition > maxPosition && sStatus == 1){
      $('#timeline').scrollLeft(scrolledPixels + maxPosition);
  }

  if(sStatus == 0 ){//go back to the begining
    player.seekTo(0);
    moveMarker(0);
    $('#timeline').scrollLeft(0);
    player.pauseVideo();
  }
}



/**
*  Gets the video file, given the video ID using youtube-dl stored
*  in /media/storage/projects/livedescribe/public_html/testing/yt.
*  Strips the audio from the video file using ffmpeg.
*/
function stripAudio(){

  if(video_id != null){
    $.ajax({
      type: "POST",
      url: "ytRequest.php",
      data: {"id": video_id,"request_type": "strip"},
      success: function(response){
        console.log("Strip audio: " + response);
      }
    });
  }
}

/**
*   Find the remaining height of the window and fills\
*   it with the bottom section of the app
*/
function bottomSectionResize(){
  var headerHeight = $('#navBar').height();
  var upperHeight  = $('#mainUpper').height();
  var screenHeight = $(window).height();
  var bottomHeight = screenHeight - headerHeight - upperHeight;

  if(bottomHeight >= 240){
    $('#mainLower').height(bottomHeight);
    $('#descriptionControls').height(bottomHeight);
    $('#timeline').height(bottomHeight);
    $('#segments').height(bottomHeight - 25);
    $('#positionMarker').height(bottomHeight - 25);
    ctx.canvas.height = bottomHeight - 25;
    mtx.canvas.height = bottomHeight - 25;

    drawMarker(); //reinitialize the marker

  }
}

/**
*  Moves the marker to the given location
*/
function moveMarker(time){
  var duration    = player.getDuration();
  var canvasWidth  = canvas.clientWidth;
  var timePercent = Math.round(((time/ duration) *  canvasWidth));
  document.getElementById("positionMarker").style.left = timePercent + "px";
}


/**
*   Draws the waveform of the YouTube audio on the canvas element
*   using the values given in the JSON object
*/
function createWaveform(){

  var width  = ctx.canvas.width;
  var height = (canvas.clientHeight / 2) + 30;
  var xpos   = 0;

  var chunks = Math.round(audioSamples.length / width);

  for(var p = 0 ; p < width; p++){
    
    var chunkSet = audioSamples.slice(chunks * p, (chunks * p + chunks));
    var min      = Math.min.apply(null,chunkSet);
    var max      = Math.max.apply(null,chunkSet);
    drawLine(p,p,min*height,max*height);
  }

}

/**
*   Draws a line on the canvas element
*/
function drawLine(x1,x2,y1,y2){
  ctx.lineWidth = 1;
  ctx.beginPath();
  ctx.moveTo(x1,y1);
  ctx.lineTo(x2,y2);
  ctx.stroke();
}


/**
*  Draws the recommended spaces for description
*  based on the array "spaces" where an index in 
*  the array represents one second of the video and 
*  a zeo in the index represents a non-speech section
*/
function drawRecomendedSpaces(){
  var t;
  var end = Math.round(videoDuration);
  var pixelsPerSecond = $('#segments').width() / videoDuration;
  
  ctx.fillStyle = "#28F249";
  ctx.globalAlpha = 0.4;

  for(t = 0; t < end; t++){
    if(spaces[t] == 0){
      ctx.beginPath();
      ctx.fillRect(t * pixelsPerSecond, 30, pixelsPerSecond, canvas.clientHeight / 2 - 5);
      ctx.fill();
    }
  }
}

/**
*
*
*/
function saveProject(){
    $( '#errorBox' ).text("");
    $( '#saveForm' ).submit(function(e){
        e.preventDefault();
    });

    var name = $( '#projName' ).val();  
    if(name === "" || name.length === 0){
        $( '#projName' ).focus();
        $( '#errorBox' ).text("Fill in the Required Fields!");
        console.log("Project Name not entered.");
    }
    else{
        $( '#errorBox' ).text("");
        $( '#loadImg' ).css("display", "block");
        $( '#saveClose' ).attr("disabled", true);        
        var from = $( '#saveForm' );
        
        var data = {};
        data.formData = JSON.stringify(from.serializeArray());

        if(descriptionList.length >= 1){
            data.descriptionData = JSON.stringify(descriptionList);
        }

        console.log("Save Submit...");

        $.ajax({
          type: "POST",
          url: base_url + "app/saveProject",
          //dataType: "json",
          data: {"saveData": JSON.stringify(data)},

          success: function(response){
            console.log("Saved: " + response);
          },
          error: function(response){
            console.log("Save Error");
          }

        }).complete(function(){
            $( '#loadImg' ).css("display", "none");
            $( '#saveClose' ).removeAttr("disabled"); 
            $( '#saveModal' ).modal('hide');
        });
    } 
}
