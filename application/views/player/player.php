 <!-- Player View -->
 <div id="player_column" class="col-md-6 col-md-offset-2">
    
    <div id="video_area" class="row">
    	<h3 id="videoTitle"> <?php echo $title; ?> </h3>

        <!--  The <iframe> (and video player) will replace this <div> tag. -->
        <div id="player"></div>
    </div>


    <div id="description_area" class="row">

        <h4><u>Description Options</u></h4>

    	<!-- Controls the volume of the descriptions -->
    	<div id="volumeControl" class="col-md-3">
            <img id="volumeimg" name="volume" src=<?php echo base_url("/assets/img/volume_img.png" )?> onclick="mute();" />
            <div id="slider" class="ui-slider ui-slider-vertical ui-widget ui-widget-content ui-corner-all" aria-disabled="false"></div>
        </div>

        <!-- Displays frequency analized audio animation -->
    	<canvas id="analyser_render" class="col-md-3"></canvas>

    	<div id="projectInfo" class="col-md-10 col-md-offset-1">

            <h4 id="project_title"></h4>
            <h6 id="author"></h6>
            <!-- <div class="rating">
                <h6>Rate This Description:</h6>
                <span id="r1">&#9734</span><span id="r2">&#9734</span><span id="r3">&#9734</span><span id="r4">&#9734</span><span id="r5">&#9734</span>
            </div> -->

            <p id="project_description"></p>

    	</div>


    </div>

</div>