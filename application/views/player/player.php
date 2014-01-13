 <!-- Player View -->
 <div class="span6 offset2">
    
    <div id="video_area" class="row-fluid">
    	<h4 id="videoTitle"> <?php echo $title; ?> </h4>

        <!--  The <iframe> (and video player) will replace this <div> tag. -->
        <div id="player"></div>
    </div>


    <div id="description_area" class="row-fluid">

        <h4><u>Description Options</u></h4>

    	<!-- Controls the volume of the descriptions -->
    	<div id="volumeControl" class="span3">
            <img id="volumeimg" name="volume" src=<?php echo base_url("/assets/img/volume_img.png" )?> onclick="mute();" />
            <div id="slider" class="ui-slider ui-slider-vertical ui-widget ui-widget-content ui-corner-all" aria-disabled="false"></div>
        </div>

        <!-- Displays frequency analized audio animation -->
    	<canvas id="analyser_render" class="span3"></canvas>

    	<div id="changeSource">

    	</div>


    </div>

</div>