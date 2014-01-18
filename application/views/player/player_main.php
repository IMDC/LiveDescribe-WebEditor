<div class="container-fluid">
  <div class="row-fluid">

    <?php 
    	$data['title'] = $title;
      $this->load->view('player/player',$data);
      $data['related_projects'] = $related_projects;
      $this->load->view('player/player_related', $data);
    ?>
  
  </div>
</div>


<!-- 
<div class="container-fluid">
  <div class="row-fluid">
    <div class="span2">
      Sidebar content
    </div>
    <div class="span10">
    </div>
  </div>
</div> -->
