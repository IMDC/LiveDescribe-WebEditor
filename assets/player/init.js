/**
*	Player Initialization
*/

var user_id;

/**
*   Get the information from the player controler
*   required for setup.
*/
$(document).ready(function(){
  console.log("Base URL: " + base_url);
  console.log("Video ID: " + playerID);
  console.log("User ID: " + getURLParameter("uID"));

  /* Check for descriptions */
  $.ajax({
  	type: "POST",
  	url: base_url + "player/getDescriptions",
    dataType: "json",
  	data: { vID: playerID, uID: getURLParameter("uID") },

  	success: function(json){
      if(json != null){
        user_id = json.project_info.user_id;
        addProjectData(json.project_info.project_name, 
                       json.project_info.project_description, 
                       json.project_info.username
                      );

        console.log(json.description_data);
        for(var i in json.description_data){
          console.log(json.description_data[i]);
          var descID          = json.description_data[i].desc_id;
          var timeStart       = json.description_data[i].start;
          var timeFinished    = json.description_data[i].end;
          var filename        = json.description_data[i].filename;
          var descriptionText = json.description_data[i].desc_text;
          createDescription(descID, timeStart, timeFinished, descriptionText, filename);
        }
      }
      else{
        console.log("No Project data");
        var msg = $('<div/>', {
                      id: 'no_desc',
                      text: 'No descriptions are available for this video.'
                  }).appendTo('#description_area');
        $('#description_area').html(msg);
      }
  	},
  	error: function(error){
  		console.log("Error Retrieving Descriptions " + error );
  	}

  });
  
});

/**
*   Parse the url to receive parameters
*/
function getURLParameter(name) {
  return decodeURIComponent((new RegExp('[?|&]' + name + '=' + '([^&;]+?)(&|#|;|$)').exec(location.search)||[,""])[1].replace(/\+/g, '%20'))||null
}

/**
*   Add the project info to the corresponding feilds
*/
function addProjectData(project_name, project_description, username){

  $('#project_title').html(project_name);
  $('#project_description').html("Description: " + project_description);
  $('#author').html("By: " + username);

}