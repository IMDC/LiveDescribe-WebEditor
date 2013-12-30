/**
*
*	The description object represents a single 
*	clip of audio and the text description associated with it
*
*/
function Description(filename, startTime,endTime, textDescription, id){
    
    this.filename = filename;
    this.startTime = startTime;
    this.endTime = endTime;
    this.textDescription = textDescription;
    this.id = id;


	/**
	*	Creates a highlighted section within the timeline 
	*	to indicate a recorded description
	*/
	this.draw = function(videoDuration,segmentsWidth, segmentsHeight){
	    var startPercentage = this.startTime / videoDuration  ;
	    var endPercentage = this.endTime / videoDuration  ;
	    var descriptionWidth = (endPercentage - startPercentage) * segmentsWidth;
	    var descriptionStartPoint = startPercentage * segmentsWidth;
	    
	    var canvas = document.getElementById('segments');
	    var context = canvas.getContext('2d');

	    context.lineWidth = 2;
	    context.beginPath();
	    context.rect(descriptionStartPoint, (segmentsHeight / 2) + 50, descriptionWidth, (segmentsHeight / 2) - 60 );
	    context.fillStyle = "0088cc";
	    context.fill();
	    context.strokeStyle = 'orange';
	    context.stroke();
	    context.strokeStyle = 'black';
	     
	}
}

