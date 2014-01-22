/**
*
*   Main Recording operations 
*
*/

var isRecording          = false; //flag that indicates whether or not the flash object is recording
var timeStart            =null;
var timeFinished         =null;
var descriptionFiles     = new Array();
var timeCodes            = new Array();
var descriptionList      = new Array();
var descriptionCollision = false; //flag to indicate if descriptions conflict
var flash_loaded         = false;
var init_vol;
var globalTimeStart;
var descID;


/**
*   Sets up the flash object for the recording functionality 
*/
function setupRecorder(){
   Wami.setup({
        id: "wami",
        onReady: wamiReady,
        onLoaded: wamiLoad
    });
}


/**
*    Set to run this function every ten seconds until a microphone has been connected
*   the timer is sec in 'editor.php' when the DOM has been loaded
*/
function checkMic(){
  if(!flash_loaded){
    var r = confirm("Please connect a microphone to your computer and refresh this page.");
    window.location.reload(true); //refresh the page
  }
  else{
    window.clearInterval(interval);
  }
}



/**
*    Called after the setup of the wami recorder has completed,
*    recording can now proceed.
*/
function wamiReady(){
  flash_loaded = true;
  console.log('Wami ready: ' + flash_loaded);
  document.getElementById("recordButton").disabled = false;
  recordIMG = document.images["record"];
  recordIMG.src = base_url + "assets/img/recordButton.png";
}

/**
*  If the wami is loaded, then it means we have a connected microphone,
*  so we just need to wait for access confirmation, so the 
*/
function wamiLoad(){
  flash_loaded = true;
  window.clearInterval(interval);
  console.log('Wami ready: ' + flash_loaded);
}

       
/**
*   records the audio and uploads it to the server 
*   with the time codes and the audio clip
*/
function recordAudio(){
  var sStatus = player.getPlayerState();
  var videoDuration = player.getDuration();
  var descTag = document.getElementById("descriptions");
  var segments = document.getElementById("segments");
  var segmentsWidth = segments.clientWidth;
  var segmentsHeight = segments.clientHeight;
  var recordIMG = null;

  if( !isRecording ){
    if(sStatus != 1){ //video is stopped, so start playing it.
      play_pause();
    }    
    console.log("Recording...");
    init_vol = $( "#slider" ).slider( "value" );
    $( "#slider" ).slider("value",1);
    timeStart       = player.getCurrentTime();
    globalTimeStart = timeStart;
    descID          = createID();

    if(flash_loaded){
      Wami.startRecording(base_url +
        'app/recordAudio?'+
        'name=description.wav&'+
        'id=' + video_id + '&' +
        'descID=' + descID + '&' +
        'userID=' + userID
      );
    }
    else{
      startRecording();
    }

    recordIMG = document.images["record"];
    recordIMG.src = base_url + "assets/img/stopButton.png" ;
    isRecording = true;
  }
  else{

    if(flash_loaded){
        Wami.stopRecording();
      }
    else{
      stopRecording(video_id , descID);
    }
     
     $("#slider").slider("value",init_vol);
     recordIMG = document.images["record"];
     recordIMG.src = base_url + "assets/img/loading.gif";
     isRecording = false;
     timeFinished = player.getCurrentTime();
     stopVideo();
     checkForCollision(timeStart,timeFinished);

     if(descriptionCollision){
       alert("Ooops! The description you just recorded is conflicting" +
           "with an existing one. Please try again.");
     }
     else if((timeFinished-timeStart) > 0 && !descriptionCollision){
        var descriptionText = document.getElementById( "transcript" ).value;
        var filename        = 'description_' + userID + '_' + video_id +'_'+ descID + '.wav';
        createDescription(descID, timeStart, timeFinished, descriptionText, filename);
     } 

    timeStart = null;
    timeFinished = null;
    recordIMG.src = base_url + "assets/img/recordButton.png" ;
    descriptionCollision = false; //reset the flag
  }
}

/**
*   Creates all aspects corresponding to a description
*/
function createDescription(descID, timeStart, timeFinished, descriptionText, filename){
  var descTag        = document.getElementById("descriptions");
  var segments       = document.getElementById("segments");
  var segmentsWidth  = segments.clientWidth;
  var segmentsHeight = segments.clientHeight;

  //create text description area
  var recordStart    = convertTime(timeStart);
  var recordFinished = convertTime(timeFinished);
  var newText        = updateDescriptionText(recordStart, recordFinished,
                          timeStart, timeFinished, descID, descriptionText);

  addDescOrdered(descTag, newText, timeStart);
  //descTag.appendChild(newText);

  // creating new description objects and inserting them into an array.
  // the array is then sorted based on the start time of the description
  var description = new Description(
                          filename,
                          timeStart, timeFinished,
                          descriptionText, 
                          descID
                    );
  descriptionList.push(description);
  descriptionList = sortDescriptionObjectList(descriptionList);

  console.log("Added description: " +  description.filename);
  
  description.draw(videoDuration, segmentsWidth, segmentsHeight);
}

/**
*   This assumes the description list is already stored 
*   in order by the start time. This then inserts the text
*   description tag in the proper spot in the view.
*   
*/
function addDescOrdered(parent, child, childStart){
  var i, t, currID;
  
  for(i=0; i < descriptionList.length; i++){
    
    t = descriptionList[i].startTime;
    currID = "" + descriptionList[i].id;
    if( childStart > t) continue;
    else{
      parent.insertBefore(child, document.getElementById(currID));
      break;
    }
  }

  if(i === descriptionList.length || descriptionList.length === 0){
    parent.appendChild(child);
  }
}

/**
*   NOTE: Unused. Replaced with the draw mehtod in the description object
*   Creates a highlighted section within the timeline 
*   to indicate a recorded description
*/
function drawDescriptionSpace(timeStart, timeFinished, videoDuration,segmentsWidth, segmentsHeight){
    var startPercentage = timeStart / videoDuration  ;
    var endPercentage = timeFinished / videoDuration  ;
    var descriptionWidth = (endPercentage - startPercentage) * segmentsWidth;
    var descriptionStartPoint = startPercentage *  segmentsWidth;
    
    var canvas = document.getElementById('segments');
    var context = canvas.getContext('2d');

    drawRect(descriptionStartPoint,32,descriptionWidth, segmentsHeight,context);      
}

/**
*   Creates a rectangle with given parameters
*   for the given context. Will have a Blue interior
*   with a thin, orange border.
*/
function drawRect(x,y, width, height, context){
    context.beginPath();
    context.rect(x , y, width  , height - 32);
    context.fillStyle = "0088cc";
    context.fill();
    context.strokeStyle = 'orange';
    context.stroke();
}
///////////////////////////////////////////////////////////

/**
*   Uses insertion sort to sort the array of description objects
*   in chronological order
*/
function sortDescriptionObjectList(descriptionList){
    var len = descriptionList.length, min, i, j;

    for (i=0; i < len; i++){
        min = i;
        for (j=i+1; j < len; j++){
            if (descriptionList[j].startTime < descriptionList[min].startTime){
                min = j;
            }
        }
        if (i != min){
            var temp = descriptionList[i];
            descriptionList[i] = descriptionList[min];
            descriptionList[min] = temp;
        }
    }
    return descriptionList;
}


/**
*  Creates a text box that will contain the script information for a description
*  with the start and end time of the description (replaced above function)
*/
function updateDescriptionText(recordStart, recordFinished,timeStart, timeFinished, id, desc_text){
    var newItem             = document.createElement("LI");
    var text                = document.createElement("textarea");
    var deleteButton        = document.createElement("button");
    
    newItem.id              = id;
    deleteButton.id         = "delete";
    deleteButton.value      = "Delete";
    deleteButton.innerHTML  = "Delete";
    deleteButton.onclick    = function(){deleteDescription(id);};
    text.id                 = 'text_' + id;
    text.style.width        = "80%";
    text.style.maxWidth     = "80%";
    text.style.position     = "relative";
    text.style.marginBottom = "10px";
    text.style.left         = "5px";
    text.onkeyup            = function(){changeDescription(id);};
    newItem.innerHTML       = "<h6 id=" + "timeStamp_" + id +">" + recordStart+ " - "+ recordFinished + "</h6>";
    text.innerHTML          = desc_text;
    
    newItem.appendChild(text);
    newItem.appendChild(deleteButton);
    
    newItem.style.backgroundColor = "#0088cc";
    newItem.style.position        = "relative";
    newItem.style.left            = "0px";
    document.getElementById("transcript").value = "";

    return newItem;
}

///
// Stops audio recording and video playing
///
function stop(){
    Wami.stopRecording();
    Wami.stopPlaying();
}

/**
*  Checks if the current recorded description conflicts with already
*  recorded descriptions. the description collision flag will then be set 
*  to true if there is a conflict
*/
function checkForCollision(newStart, newEnd){
    
    for(var i=0; i < descriptionList.length; i++){
        existingStart = descriptionList[i].startTime;
        existingEnd = descriptionList[i].endTime;
        
        //new description starts in an esisting description
        if(newStart > existingStart && newStart < existingEnd){
            descriptionCollision = true;
        }
        //new description runs into and existing description
        if(newEnd > existingStart && newEnd < existingEnd){
            descriptionCollision = true;
        }
        //new description completely covers an existing one
        if(newStart < existingStart && newEnd > existingEnd){
            descriptionCollision = true;
        }       
    }
}


/**
*  Creates a unique id that is assigned to the description
*  used as a link between the visual description space and 
*  the text area for the description 
*/
function createID() {
  return ("" + 1e10).replace(/[018]/g, function(a) {
    return (a ^ Math.random() * 16 >> a / 4).toString(16)
  });
}




//////////////////////////////////////TEST FUNCTIONS///////////////////////////////////////////////////

function test2(){
  var list= '';
  for(i=0; i < descriptionList.length ; i++){
    list = list + '\n' + descriptionList[i].startTime; 
  }
  alert(list);
}

function showText(){

  var list= '';
  for(i=0; i < descriptionList.length ; i++){
    list = list + '\n' + descriptionList[i].filename + ': \n' + descriptionList[i].textDescription + '\n'; 
  }
  alert(list);
}
////////////////////////////////////////////////////////////////////////////////////////////////////////