/**
*	This file handles all the rating functions
*/

$(document).ready(function(){
	$('img#like').click(function(){
		rateProject(1);
	});

	$('img#dislike').click(function(){
		rateProject(-1);
	});	
});

/**
*	Makes a post request to rate a project.
*	The project ratings are updated if the
*	POST request is successful.
*/
function rateProject(like_dislike){
	$.ajax({
	  	type: "POST",
	  	url: base_url + "player/addRating",
	    dataType: "json",
	  	data: { vID: playerID, user_id: user_id, rating: like_dislike},

	  	success: function(json){
	      console.log("Success");

	      if(json != null){
	      	$('#like_count').html(json.likes);
  			$('#dislike_count').html(json.dislikes);
	      }
	      else{
	      	console.log("Not Logged-in");
	        var msg = $('<p/>', {
            	          id: 'no_login',
                	      text: 'You must login to rate this project!'
                  		});

        	msg.addClass('alert alert-danger');
        	displayError(msg);
	      }
	  	},
	  	error: function(error){
	  		console.log("Error Adding Rating");
	  	}
  	});
}

/**
*	Displays the given object that contains an 
*	error msg to the user, and fades out after
*	a few seconds.
*/
function displayError(msg_object){
	$('#rate_msg').html(msg_object); //add it to the DOM

	$(msg_object).fadeIn(1000, 
		function(){
			$(msg_object).fadeOut(5000);
		}
	);
}