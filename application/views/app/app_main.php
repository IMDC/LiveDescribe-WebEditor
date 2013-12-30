<!-- Only show this page if the user is not logged on -->
<?php
  // if($this->session->userdata('logged_in')){
  //   redirect(base_url(), 'refresh');
  // }
?>
<!-- 
<div>
	Hello, the video id is: <script type="text/javascript">alert(video_id);</script>
	<?php //echo($vID); ?>
</div> 

<script type="text/javascript">alert(video_id);</script>
-->

<div id="wami" style="margin-left: 0%;position:relative;top:32px;"></div>
<div id="load"></div>

<div id="app">

  <section id="mainUpper">
      
      <section id="AVControls">
          
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
          
          <div id="timeInfo">
          </div>

         
          <a href="#" id="saveProject" role="button" class="btn" data-toggle="modal">
              Save Project
          </a>

         
      </section>
      
      <section id="videoPlayer">
          <div id="videoscreen"></div>
          <!--  The <iframe> (and video player) will replace this <div> tag. -->
          <div id="player"></div>
      </section>
      
      <section id="descriptionInfo">
         <div id="transcriptBox">

             <h5>Descriptions</h5>

             <div id="navigation" style="padding:5px; background-color:#FAF9F8; border:2px black solid;">
                 <ol id="descriptions">

                 </ol>
             </div>

         </div>
      </section>

  </section>
  
  
  <section id="mainLower" >
      
      <section id="descriptionControls" style="position: absolute;
                                               border: solid;
                                               border-width: 1px;
                                               width: 200px;
                                               height: 230px;
                                               left: 0px;">   
          
          <h6 style="position: relative;left: 6px;">
              Description Text
          </h6>
          <textarea id="transcript" cols="10" rows="10"
                    style="
                          width: 180px;
                          position: relative;
                          left: 6px;
                          height: 80%;
                          max-width: 180px;
          "></textarea>
          
          
          
          
      </section>
      
      
      <section  id="timeline"  style="position: absolute;
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
              
         
      </section>

  </section>
</div>