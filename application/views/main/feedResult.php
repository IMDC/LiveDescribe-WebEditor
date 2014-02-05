 <li id="resultItem" class="media">	

    <div id="resultContainer" class="panel-group">
        <div class="panel panel-default">
            <div class="panel-heading" style="min-height: 140px;">
                
                <div data-toggle="" data-parent="#resultContainer" href=<?php echo('#' .$videoId);?> >
                <!-- <div data-toggle="collapse" data-parent="#resultContainer" href=<?//php echo('#' .$videoId);?> > -->
                   
                    <a class="pull-left" href="#">
                       <img class="media-object" src=<?php echo($thumbnail);?> width="124px" style="padding-bottom: 20px; margin: 5px;" alt=<?php echo($title);?> /> 
                    </a>
                    
                    <div id="resultInfo" class="media-body">
                        <h4>
                            <a class="media-heading" id="videoTitle"> <?php echo($title); ?> </a>
                        </h4>
                        <p id="videoDesc"> <?php echo($description); ?> </p>    
                    </div>

                </div>
            </div>

            <div id=<?php echo($videoId); ?> class="">
            <!-- <div id=<?php //echo($videoId); ?> class="panel-collapse collapse out"> -->
                <div id="videoOptions" class="panel-body pull-left">
                    
                    <!-- Display edit button if user is logged in -->
                    <?php 
                        $base_url = base_url();
                    	if($this->session->userdata('logged_in') == TRUE ){
                            
                    		echo("<a id=\"videoEdit\" role=\"button\" class=\"btn btn-default\" href=\"{$base_url}app/editor?vID=" . $videoId . "\"\>");
    	                	echo("Add Description");
    	            		echo("</a>");	
                    	}
                    ?>
                    <a id="videoPlay" role="button" class="btn btn-default" href=<?php echo($base_url . "player?vID=" . $videoId );?> > 
                        Play Video
                    </a>
                </div>
            </div>
        </div>
    </div>

</li>

