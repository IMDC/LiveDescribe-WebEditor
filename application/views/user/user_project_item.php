 <!--User video result  -->
<li id="resultItem" class="media">  

    <div id="resultContainer" class="panel-group">
        <div class="panel panel-default">
            <div class="panel-heading" style="min-height: 125px;">
                <div data-toggle="collapse" data-parent="#resultContainer" href=<?php echo('#' .$data['videoID']);?> >

                    <a class="pull-left" href="#">
                       <img class="media-object" src=<?php echo($data['thumbnail']);?> width="124px" style="padding-bottom: 20px; margin: 5px;" alt=<?php echo($data['title']);?> /> 
                    </a>
                    
                    <div id="resultInfo" class="media-body">
                        <h4>
                            <a class="media-heading" id="videoTitle"> <?php echo($data['title']); ?> </a>
                        </h4>
                        <div id="timestamp">
                            <?php 
                                $this->load->helper('date'); 
                                $date = mysql_to_unix($data['date']);
                                $ds   =  "%d / %m / %Y";
                                $date =  mdate($ds, $date);
                            ?>
                            <h6><?php echo("Last Modified: " . $date); ?></h6>
                        </div>
                        <p id="videoDesc"> <?php echo($data['description']); ?> </p>    
                    </div>

                </div>
            </div>

            <div id=<?php echo($data['videoID']); ?> class="panel-collapse collapse out">
                <div id="videoOptions" class="panel-body pull-left">
       
                  <a id="videoEdit" role="button" class="btn btn-default" href=<?php echo(base_url() . "app/editor?vID=" . $data['videoID']); ?> >
                      Edit Video
                  </a>  
                      
                  <a id="videoPlay" role="button" class="btn btn-default" href=<?php echo(base_url() . "player?vID=" . $data['videoID'] . "&uID=" . $data['userID']);?> > 
                      Play Video
                  </a>

                </div>
            </div>
        </div>
    </div>

</li>

