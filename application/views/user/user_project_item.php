 <!--User video result  -->
<li id="resultItem" class="media">  

    <div id="resultContainer" class="panel-group">
        <div class="media-content panel panel-default">
            <div class="panel-heading" style="min-height: 125px;">
               
                    <a class="pull-left" href="#">
                        <?php
                            $thumbnail = $data['thumbnail'];
                            if(!isset($data['thumbnail'])){
                                $thumbnail = base_url() . "assets/img/image-not-found.png"; 
                            }
                        ?>
                       <img class="media-object" src=<?php echo $thumbnail;?> width="124px" height="120px" style="padding-bottom: 20px; margin: 5px;" alt=<?php echo($data['title']);?> /> 
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

            <div id="projectOptions" >
                <div id="videoOptions" class="btn-group panel-body pull-left">
       
                  <a id="videoEdit" role="button" class="btn btn-primary" href=<?php echo(base_url() . "app/editor?vID=" . $data['videoID']); ?> >
                      Edit Video
                  </a>  
                      
                  <a id="videoPlay" role="button" class="btn btn-success" href=<?php echo(base_url() . "player?vID=" . $data['videoID'] . "&uID=" . $data['userID']);?> > 
                      Play Video
                  </a>

                </div>

                <div class="panel-body pull-right">
                    <form id="deleteProject" method="POST" action=<?php echo(base_url() . "user/deleteProject"); ?> onsubmit="return confirm('Are you sure you want to delete this project?')">
                        <input type="hidden" name="projectID" value= <?php echo($data['projectID']); ?> />
                        <input type="submit" value="Delete Project" role="button" class="btn btn-danger" />
                    </form>
                </div>
            </div>
        </div>
    </div>

</li>

