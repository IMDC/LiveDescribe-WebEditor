 <!-- Player View -->
 <div class="span6 content-area">
    
    <section id="video_area">
    	<h4 id="videoTitle"> <?php echo $title; ?> </h4>

        <!--  The <iframe> (and video player) will replace this <div> tag. -->
        <div id="player"></div>
    </section>


    <section id="description_area">
    	<!-- Controls the volume of the descriptions -->
    	<div id="volumeControl" class="span3">
            <img id="volumeimg" name="volume" src=<?php echo base_url("/assets/img/volume_img.png" )?> onclick="mute();" />
            <div id="slider"></div>
        </div>

        <!-- Displays frequency analized audio animation -->
    	<canvas id="analyser_render" class="span3"/>

    	<div id="changeSource">

    	</div>


    </section>

</div>