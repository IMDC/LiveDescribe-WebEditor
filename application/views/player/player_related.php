<!-- Related Video Feed -->
<div class="span4 ">
	<h4>Related Videos</h4>
	<?php //print_r($related_projects);?>

	<ul id="relatedResults" class="media-list">

	<?php foreach($related_projects as $key => $value){ ?>

		<?php $link = base_url()."player?vID=".$vID."&uID=".$value["user_id"]; ?>		
		<li id="resultItem" class="media">
			<div id="resultContainer" class="accordion-group">
				
	            <div class="media-content">
	                
	                <a class="pull-left" href= <?php echo($link); ?> >
	                   <img class="media-object" src=<?php echo($thumbnail); ?> width="125px" style="padding-bottom: 20px;" alt=<?php echo($title); ?> /> 
	                </a>
	                
	                <div id="resultInfo" class="media-body">
	                    <a class="media-heading" id="videoTitle" href= <?php echo($link); ?> > <?php echo($value["project_name"]); ?> </a>
	                    <h6 id="author">By: <?php echo($value["username"]); ?></h6>   
	                </div>

            	</div>
		       
			</div>
		</li>


	<?php } ?>

	</ul>
</div>