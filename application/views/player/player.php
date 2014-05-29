 <!-- Player View -->
 <div id="player_column" class="col-md-6 col-md-offset-2">
    
    <div id="video_area" class="row">
    	<h3 id="videoTitle"> <?php echo $title; ?> </h3>

        <!--  The <iframe> (and video player) will replace this <div> tag. -->
        <div id="player"></div>
    </div>


    <div id="description" class="row">

        <h4 class="col-md-offset-1"><u>Description Options</u></h4>
        

        <div id="description_area" class="row">
        	<!-- Controls the volume of the descriptions -->
        	<div id="volumeControl" class="col-md-3 col-md-offset-1">
                <img id="volumeimg" name="volume" src=<?php echo base_url("/assets/img/volume_img.png" )?> onclick="mute();" />
                <div id="slider" class="ui-slider ui-slider-vertical ui-widget ui-widget-content ui-corner-all" aria-disabled="false"></div>
            </div>

            <!-- Displays frequency analized audio animation -->
        	<canvas id="analyser_render" class="col-md-3"></canvas>
        </div>

        <div class="row">
        	<div id="projectInfo" class="col-md-10 col-md-offset-1">

                <h4 id="project_title"></h4>
                <h6 id="author"></h6>
                
                <p id="project_description"></p>

        	</div>
        </div>

        <div id="rating" class="row">
            <!-- <div class="col-md-6 col-md-offset-1">
                <h4>Rate this Project:</h4>
            </div> -->
            <div id="like" class="col-md-1 col-md-offset-1">
                <img id="like" src=<?php echo base_url("/assets/img/like.png"); ?> alt="like" height="24px"/>
                <p id="like_count"></p>
            </div>
            <div id="dislike" class="col-md-1">
                <img id="dislike" src=<?php echo base_url("/assets/img/dislike.png"); ?> alt="dislike" height="24px"/>
                <p id="dislike_count"></p>
            </div>
            <div id="rate_msg" class="col-md-6"></div>
        </div>

    </div>
</div>