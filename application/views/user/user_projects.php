<!-- Manage area for users -->

<div class="container">

  <div class="row row-offcanvas row-offcanvas-left">
    
     <div class="col-xs-6 col-sm-3 sidebar-offcanvas" id="sidebar" role="navigation">
       <p class="visible-xs">
        <button type="button" class="btn btn-primary btn-xs" data-toggle="offcanvas"><i class="glyphicon glyphicon-chevron-left"></i></button>
      </p>
      <div class="well sidebar-nav">
        <ul class="nav">

          <li><a href=<?php echo(base_url() . "user/projects"); ?> ><h4>My Projects</h4></a></li>
          <!-- <li><a href="#"><h4>Account</h4></a></li> -->
        </ul>
      </div><!--/.well -->
    </div><!--/span-->
    
    <div class="col-xs-1 visible-xs">
        <button type="button" class="btn btn-primary btn-xs" data-toggle="offcanvas" id="btnShow"><i class="glyphicon glyphicon-chevron-right"></i></button>
    </div>


    <div class="col-md-6">

      <h2><u>Projects</u></h2>

      <ul class="media-list results">
        
        <?php if($result != NULL){ ?> 

          <?php foreach($result as $data):?>
            <?php $this->load->view('user/user_project_item', array('data'=> $data))?>
          <?php endforeach; ?>
        
        <?php }else{ ?>
            <div class="alert alert-warning">You have no saved projects.</div>
        <?php } ?>
      </ul>

    </div>

</div>



