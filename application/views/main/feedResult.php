 <li id="resultItem" class="media">		        
    <div id="resultContainer" class="accordion-group">
        <div class="accordion-heading">
            <?php echo("<div class=\"accordion-toggle\" data-toggle=\"collapse\" data-parent=\"#searchResults\" href='#" .$videoId."'>"); ?>
                <a class="pull-left" href="#">
                   <?php echo("<img class=\"media-object\" src=\"" .$thumbnail. "\" width=\"124px\" style=\"padding-bottom: 20px;\" alt=\"" .$title. "\" />"); ?> 
                </a>
                
                <div id="resultInfo" class="media-body">
                    <a class="media-heading" id="videoTitle"> <?php echo($title); ?> </a>
                    <p id="videoDesc"> <?php echo($description); ?> </p>    
                </div>

            </div>
        </div>

        <?php echo("<div id=\"" .$videoId. "\" class=\"accordion-body collapse out\">"); ?>
            <div id="videoOptions" class="accordion-inner">
                
                <!-- Display edit button if user is logged in -->
                <?php 
                	if($this->session->userdata('logged_in') == TRUE ){
                        $base_url = base_url();
                		echo("<a id=\"videoEdit\" role=\"button\" class=\"btn\" href=\"{$base_url}app/editor?vID=" . $videoId . "\"\>");
	                	echo("Add Description");
	            		echo("</a>");	
                	}
                	echo("<a id=\"videoPlay\" role=\"button\" class=\"btn\" href=\"{$base_url}player?vID=" . $videoId . "\">"); 
                ?>
                    Play Video
                </a>
            </div>
        </div>
    </div>
</li>