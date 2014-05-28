<!-- Standard Video -->
<?php foreach($standard_feed as $key => $value): ?>
 <li id="resultItem" class="media">	

    <div id="resultContainer" class="panel-group">
        <div class="panel panel-default">
            <div class="panel-heading" style="min-height: 140px;">
                
                <div data-toggle="" data-parent="#resultContainer" href=<?php echo('#' . $value['videoId']);?> >
                <!-- <div data-toggle="collapse" data-parent="#resultContainer" href=<?//php echo('#' .$videoId);?> > -->
                   
                    <a class="pull-left" href="#">
                       <img class="media-object" src=<?php echo($value['thumbnail']);?> width="124px" style="padding-bottom: 20px; margin: 5px;" alt=<?php echo($title);?> /> 
                    </a>
                    
                    <div id="resultInfo" class="media-body">
                        <h4>
                            <a class="media-heading" id="videoTitle"> <?php echo($value['title']); ?> </a>
                        </h4>
                        <p id="videoDesc"> <?php echo($value['description']); ?> </p>    
                    </div>

                </div>
            </div>

            <div id="videoOptions" class="panel-body pull-left">
                
                <!-- Display edit button if user is logged in -->
                <?php if($this->session->userdata('logged_in') == TRUE ): ?>
                        
                	<a id="videoEdit" role="button" class="btn btn-default" href=<?php echo(base_url() . "app/editor?vID=" . $value['videoId'] );?> >
	                	Add Description
	            	</a>	
                	
                <?php endif;?>
                <a id="videoPlay" role="button" class="btn btn-default" href=<?php echo(base_url() . "player?vID=" . $value['videoId'] );?> > 
                    Play Video
                </a>
            </div>
        </div>
    </div>
</li>
<?php endforeach; ?>

