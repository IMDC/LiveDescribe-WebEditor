/**
*	Player Initialization
*/

/**
*   Get the information from the player controler
*   required for setup.
*/
$(document).ready(function(){
  console.log("Base URL: " + base_url);
  console.log("Video ID: " + playerID);

  /* Check for descriptions */
  $.ajax({
  	type: "POST",
  	url: base_url + "player/getDescriptions",
    dataType: "json",
  	data: { vID: playerID },

  	success: function(json){
      console.log("JSON: " + json);

  	},
  	error: function(error){
  		console.log("Error Retrieving Descriptions " + error );
  	}

  });
  
});