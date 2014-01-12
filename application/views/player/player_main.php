<div class="container-fluid">
  <div class="row-fluid">

    <?php 
    	$data['title'] = $title;
        $this->load->view('player/options', $data);
        $this->load->view('player/player');
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
