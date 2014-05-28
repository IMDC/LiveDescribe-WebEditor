<!--  -->
<div id="wami" style="margin-left: 0%;position:relative;top:32px;"></div>
<div id="load"></div>

<div id="app" class="container-fluid">

  <div id="mainUpper" >
      
      <div id="controls">
          
          <div id="AVControls">
            <div id="playpauseControl">
                <button name="playpause" class="controls" onclick="play_pause();">
                    <img name="playpause" src=<?php echo base_url("/assets/img/playButton3.png") ?>  class="imgcontrols"  />
                </button>
            </div>

            <div id="recordControl">
                <button name="record" id="recordButton" class="controls" onclick="recordAudio();" disabled="true">
                    <img name="record" src=<?php echo base_url("/assets/img/loading.gif") ?>  class="imgcontrols"  />
                </button>
            </div>
            
            <div id="volumeControl">
                <img id="volumeimg" name="volume" src=<?php echo base_url("/assets/img/volume_img.png" )?> onclick="mute();" />
                <!--<input id="volume" type="range" name="volume" class="controls" min="0" max="100" value="30" step="1" onchange="changeVolume(this.value);" />-->
                <div id="slider"></div>
            </div>
            
            <!-- Canvas Displays frequency analized audio animation (added to DOM with JS if JS recording is available) -->
            

            <div id="timeInfo">
            </div>

          </div>

          <div id="projectControl">
            <!-- Button trigger modal -->
            <button id="saveProject" class="btn btn-primary" data-toggle="modal" data-target="#saveModal" data-backdrop="static" data-keyboard="false">
              Save Project
            </button>

            <!-- Modal -->
            <div class="modal fade" id="saveModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
              <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">Save Project</h4>
                  </div>
                  <div class="modal-body">
                    
                    <!-- Fields to be filled out before saving -->
                    <?php 
                      $project_name = isset($project_name) ? $project_name : "";
                      $project_description = isset($project_description) ? $project_description : "";
                      $vID  = $this->input->get('vID');

                      $attributes = array('id' => 'saveForm', 'role' => 'form');
                      echo form_open('', $attributes);

                      echo('<div class="form-group">');
                      echo form_label("Project Name (Required):", 'projName');
                      $data = array(
                                    'id' => 'projName', 
                                    'name' => 'projName',
                                    'type' => 'text' ,
                                    'size' => '30',
                                    'autofocus' => 'autofocus',
                                    'style' => 'height: 45px; width: 100%;',
                                    'class' => 'form-control',
                                    'required' => '',
                                    'value' => $project_name
                                  );
                      echo form_input($data);
                      echo('</div>');

                      echo('<div class="form-group">');
                      echo form_label("Project Description:", 'projDesc');
                      $data = array(
                                    'id' => 'projDesc', 
                                    'name' => 'projDesc',
                                    'rows' => '30',
                                    'cols' => '30',
                                    'autofocus' => 'autofocus',
                                    'style' => 'height: 75px; width: 100%;',
                                    'class' => 'form-control',
                                    'required' => 'false',
                                    'value' => $project_description
                                  );
                      echo form_textarea($data);
                      echo form_hidden("vID", $vID);
                      echo('</div>');

                      echo('<div id="errorBox" name="errorBox" style="color: red;"></div>');
                      echo form_close();
                    ?>

                    <div id="saveLoad"> 
                      <img id="loadImg" src=<?php echo base_url("/assets/img/loading.gif" )?>
                            style="display: none;" width=40 alt="loading"/> 
                    </div>

                  </div>
                  
                  <div class="modal-footer">

                    <button id="saveClose" type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button id="saveButton" type="button" class="btn btn-primary" onclick="saveProject();"> Save </button>
                  
                  </div> <!-- /.modal-footer -->
                </div><!-- /.modal-content -->
              </div><!-- /.modal-dialog -->
            </div><!-- /.modal -->
          </div>

         
      </div>
      
      <div id="videoPlayer">
          <!--  The <iframe> (and video player) will replace this <div> tag. -->
          <div id="player"></div>
      </div>
      
      <div id="descriptionInfo">
         <div id="transcriptBox">

             <h4>Descriptions</h4>

             <div id="navigation" style="padding:5px; background-color:#FAF9F8; border:2px black solid;">
                 <ol id="descriptions">

                 </ol>
             </div>

         </div>
      </div>

  </div>
  
  
  <div id="mainLower" >
      
      <div id="descriptionControls" style="position: absolute;
                                               border: solid;
                                               border-width: 1px;
                                               width: 200px;
                                               height: 230px;
                                               left: 0px;">   
          
          <h6 style="position: relative;left: 6px;">
              Description Text
          </h6>
          <textarea id="transcript" cols="10" rows="10" style="width: 180px;
                                                              position: relative;
                                                              left: 6px;
                                                              height: 80%;
                                                              max-width: 180px;"></textarea>
                    
      </div>
      
      
      <div  id="timeline"  style="position: absolute;
                                                  left: 200px;
                                                  min-width: 900px;
                                                  height:230px;
                                                  right: 0px;
                                                  overflow-x:scroll;">
        <div id="timelineLoad">
          <img name="load" id="load" src=<?php echo base_url("/assets/img/loading.gif") ?> />
          <h5>Loading .....</h5>
        </div>

        <canvas id="segments"  height="230" ></canvas>
        <canvas id="timeBar" height="30"></canvas>
        <canvas id="positionMarker"></canvas>        
         
      </div>

  </div>
</div>