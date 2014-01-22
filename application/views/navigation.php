<div class="navbar navbar-fixed-top navbar-inverse" role="navigation">
  <div class="container">
    
    <div class="navbar-header">
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href=<?php echo base_url() . "main/"?>>LiveDescribe</a>
    </div>

    <div class="collapse navbar-collapse">
    
      <ul class="nav navbar-nav">
        <li><a href=<?php echo base_url() . "main/"?>>Home</a></li>
          <li><a href="#about">About</a></li>
      </ul>

      <div class="col-sm-5 col-md-5">

        <form action=<?php echo base_url() . "main/videoFeed"?> role="search" class="navbar-form" id="searchArea" method="POST" >
          <div class="input-group">
              <input type="text"  id="searchBar" name="searchBar" class="form-control" placeholder="Search" autocomplete="on" required>
              <div class="input-group-btn">
                <button type="submit" id="search" class="btn btn-primary"><i class="glyphicon glyphicon-search"></i></button> 
            </div>
          </div>
        </form> 

      </div>

       
      <ul id="navItems" class="nav navbar-nav pull-right">

        <?php
          $userName = $this->session->userdata('userName');
          $userID   = $this->session->userdata('userId');
          //show the login and registration options if the user is not logged on 
          if(!$this->session->userdata('logged_in') ){
        ?>
              <li><a href=<?php echo base_url() . "user/login"?>>Login</a></li>
              <li><a href=<?php echo base_url() . "user/register"?>>Register</a></li>
               
          <?php 
            }   
            else{  
          ?>
              <li class="dropdown">
                <a class="dropdown-toggle" data-toggle="dropdown">
                  <img src=<?php echo base_url()."assets/img/3mic.png"?> height="40" width="40" />
                  <?php echo $userName;?>
                  <b class="caret"></b>
                </a>

                 <ul id="dropList" class="dropdown-menu pull-right" >
                      <li>
                          <a id="myVideos" role="menu" href=<?php echo base_url() . "user/"?>>
                              My Videos
                          </a>
                      </li>

                      <li>
                          <a id="logout" role="menu" href=<?php echo base_url() . "user/logout"?>>
                              Log Out
                          </a>
                      </li>
                  </ul>

              </li>

            <?php  
            } 
            ?>

        </ul>

    </div><!-- /.nav-collapse -->


  </div><!-- /.container -->
</div><!-- /.navbar -->


