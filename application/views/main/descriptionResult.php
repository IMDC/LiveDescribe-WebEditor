<!-- Described Video Feed -->
<?php foreach($described_feed as $key => $value): ?>

	<?php $link = base_url()."player?vID=".$value['vID']."&uID=".$value["user_id"]; ?>		
	<li id="resultItem" class="media">
		<div id="resultContainer" class="panel-group">
			
            <div class="panel panel-default">
            	<div class="panel-heading" style="min-height: 140px;"> 
                
	                <a class="pull-left" href="#">
	                   <img class="media-object" src=<?php echo($value['thumbnail']); ?> width="125px" style="padding-bottom: 20px; margin: 5px;" alt=<?php echo($title); ?> /> 
	                </a>
	                
	                <div id="resultInfo" class="media-body">
	                	<h4 class="">
	                    	<a class="media-heading" id="videoTitle" href= <?php echo($link); ?> > <?php echo($value["project_name"]); ?> </a>
	                	</h4>
	                    <h6 id="author">By: <?php echo($value["username"]); ?></h6>   
	                </div>

            	</div>

            	 <div id="videoOptions" class="panel-body pull-left">
                    <a id="videoPlay" role="button" class="btn btn-default" href=<?php echo($link);?> > 
                        Play Video
                    </a>
                </div>

        	</div>
	       
		</div>
	</li>

<?php endforeach; ?>
