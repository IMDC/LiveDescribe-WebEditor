<!-- Standard Video -->
<?php foreach($standard_feed as $key => $value): ?>
<li id="resultItem" class="media">	

	<div id="resultContainer" class="panel-group">
		<div class="panel panel-default">
			<div class="panel-heading" style="min-height: 140px;">

				<div data-toggle="" data-parent="#resultContainer" href=<?php echo('#' . $value['videoId']);?> >
					<!-- <div data-toggle="collapse" data-parent="#resultContainer" href=<?//php echo('#' .$videoId);?> > -->

					<a class="pull-left">
						<img class="media-object" src=<?php echo($value['thumbnail']);?> width="124px" style="padding-bottom: 20px; margin: 5px;" alt=<?php echo($value['title']);?> /> 
					</a>
					<div id="resultInfo" class="media-body">
						<h4><a class="media-heading" id="videoTitle" href=<?php echo(base_url() . "player?vID=" . $value['videoId'] );?> > <?php echo($value['title']); ?> </a></h4>
						<p id="videoDesc"> <strong>Duration:</strong>
						<?php 
							$time = $value['duration'];
							$seconds = $time % 60; 
							$minutes = ($time / 60) % 60;
							$hours = (($time/60)/60) % 60;

							if($seconds < 10)
								$seconds = "0" . $seconds;
							if($minutes < 10)
								$minutes = "0" . $minutes;
							if($hours < 10)
								$hours = "0" . $hours;

							if($hours > 0)
								echo("{$hours} : {$minutes} : {$seconds}");
							else
								echo("{$minutes} : {$seconds}");
						?> 
						</p>    
					</div>
				</div>
			</div>

			<div id="videoOptions" class="panel-body pull-left">

				<!-- Display edit button if user is logged in -->
				<?php if($this->session->userdata('logged_in') == TRUE ): ?>

				<a id="videoEdit" role="button" class="btn btn-default" href=<?php echo(base_url() . "app/editor?vID=" . $value['videoId'] );?> >Add Description</a>	

				<?php endif;?>
				<a id="videoPlay" role="button" class="btn btn-default" href=<?php echo(base_url() . "player?vID=" . $value['videoId'] );?> >Play Video</a>
			</div>
		</div>
	</div>
</li>
<?php endforeach; ?>

