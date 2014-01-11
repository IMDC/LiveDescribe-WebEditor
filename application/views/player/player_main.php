<div class="container-fluid wrapper">
  <div class="row-fluid columns content">

    <?php 
    	$data['title'] = $title;
        $this->load->view('player/options', $data);
        $this->load->view('player/player');
    ?>
  
  </div>
</div>