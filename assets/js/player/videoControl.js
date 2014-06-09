/*
*	This file is meant to handle the YouTube video player for
*	the video selected by the user
*/


/**
*  Variables required for the YouTube player
*/
var tag            = document.createElement('script');
tag.src            = "//www.youtube.com/iframe_api";
var firstScriptTag = document.getElementsByTagName('script')[0];
firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
var player;
var playerID;


/**
*   This function creates an <iframe> (and YouTube player)
*   after the API code downloads.
*/
function onYouTubeIframeAPIReady(){
  player = new YT.Player
          ('player',
          {
            height: '340',
            width: '560',
            videoId: playerID,
            playerVars: { 
                          'autoplay': 0, 
                          'autohide': 1,
                          'controls': 2,
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
*   The API will call this function when the video player is ready.
*/
function onPlayerReady(event) {
    var url        = player.getVideoUrl();
    videoDuration  = player.getDuration();

    //Call the function timeupdate every 10 milliseconds.
    var delay = 10; 
    window.setInterval(function(){checkForDescription();} , delay);   
}



/**
*   The API calls this function when the player's state changes.
*   The function indicates that when playing a video (state=1)
*/
function onPlayerStateChange(event){
	//nothing yet
}


/**
*   Toggles the play / pause function on the player
*/
function play_pause() {
    var sStatus = player.getPlayerState();
  
    //pauses the video
    if (sStatus == 1){                        
        player.pauseVideo();
    }     

    //plays the video
    else { 
        player.playVideo();
    }
}
