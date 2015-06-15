<!-- Described Video Feed -->
<?php foreach($described_feed as $key => $value): ?>
<?php $link = base_url()."player?vID=".$value['vID']."&uID=".$value["user_id"]; ?>		
<li id="resultItem" class="media">
	<div id="resultContainer" class="panel-group">

		<div class="panel panel-default">
			<div class="panel-heading" style="min-height: 140px;"> 

				<a class="pull-left">
					<img class="media-object" src=<?php echo($value['thumbnail']); ?> width="125px" style="padding-bottom: 20px; margin: 5px;" alt=<?php echo($value['project_name']); ?> /> 
				</a>

				<div id="resultInfo" class="media-body">
					<h4><a class="media-heading" id="videoTitle" href= <?php echo($link); ?> > <?php echo($value["project_name"]); ?> </a></h4>
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
				</div>
			</div>

			<div id="videoOptions" class="panel-body pull-left">
				<a id="videoPlay" role="button" class="btn btn-default" href=<?php echo($link);?> >Play Video</a>

			</div>

			<div id="rating" class="panel-body pull-right">
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
</li>

<?php endforeach; ?>
