<!-- Related Video Feed -->
<div class="col-md-4">
	<h4>Related Projects</h4>
	<?php if($related_projects != NULL): ?>

	<ul id="relatedResults" class="media-list">

	<?php foreach($related_projects as $key => $value): ?>

		<?php $link = base_url()."player?vID=".$vID."&uID=".$value["user_id"]; ?>		
		<li id="resultItem" class="media">
			<div id="resultContainer" class="panel-group">
				
	            <div class="media-content panel panel-default">
	            	<div class="panel-heading" style="min-height: 125px;"> 
	                
		                <a class="pull-left" href= <?php echo($link); ?> >
		                   <img class="media-object" src=<?php echo($thumbnail); ?> width="125px" style="padding-bottom: 20px; margin: 5px;" alt=<?php echo($title); ?> /> 
		                </a>
		                
		                <div id="resultInfo" class="media-body">
		                	<h4 class="">
		                    	<a class="media-heading" id="videoTitle" href= <?php echo($link); ?> > <?php echo($value["project_name"]); ?> </a>
		                	</h4>
		                    <h6 id="author">By: <?php echo($value["username"]); ?></h6>
		                    <div id="timestamp">
	                            <?php 
	                                $this->load->helper('date'); 
	                                $date = mysql_to_unix($value['date_modified']);
	                                $ds   =  "%d / %m / %Y";
	                                $date =  mdate($ds, $date);
	                            ?>
	                            <h6><?php echo("Last Modified: " . $date); ?></h6>
	                        </div>
	                        <p id="videoDesc"> <?php echo($value['project_description']); ?> </p> 

	                        <div id="rating" class="pull-right">
			                	<div id="related_like" class="col-md-1">
					                <img src=<?php echo base_url("/assets/img/like.png"); ?> alt="like" height="16px"/>
					                <p id="like_count"> <?php echo($value['rating']['likes']); ?> </p>
					            </div>

					            <div id="related_dislike" class="col-md-1">
					                <img src=<?php echo base_url("/assets/img/dislike.png"); ?> alt="dislike" height="16px"/>
					                <p id="dislike_count"> <?php echo($value['rating']['dislikes']); ?> </p>
					            </div>
			                </div>

		                </div>

	            	</div>

            	</div>
		       
			</div>
		</li>
		
	<?php endforeach;?>
	
	<?php else: ?>
		<h5 id="no_proj" class="alert alert-warning">No other projects are available for this video.​</h5>
	<?php endif; ?>
	</ul>
</div>