<div id="navBar" class="navbar navbar-inverse navbar-fixed-top">
  <div class="navbar-inner">
    <div class="container">
      <button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="brand" href=<?php echo base_url() . "main/"?>>LiveDescribe</a>

      <div class="nav-collapse collapse">
        <ul class="nav">
          
          <li><a href=<?php echo base_url() . "main/"?>>Home</a></li>
          <li><a href="#about">About</a></li>
          
        </ul>

      </div><!--/.nav-collapse -->
      <form action=<?php echo base_url() . "main/videoFeed"?> class="nav form-search"  id="searchArea" method="POST" >
            <div class="input-append">
                <input type="text"  id="searchBar" name="searchBar" class="span5 search-query" placeholder="Enter KeyWords" required>
                <button type="submit" id="search" class="btn"><i class="icon-search"></i></button> 
            </div>
        </form> 

        <nav id="navigator" class="nav pull-right">
            <ul id="navItems">
<?php
                $base_url = base_url();
                $userName = $this->session->userdata('userName');
                $userID   = $this->session->userdata('userId');

                 //show the login and registration options if the user is not logged on 
                 if(!$this->session->userdata('logged_in') ){
                    echo("<li class=\"navButton\">"); 
                    echo("<a href=\"{$base_url}user/login\" role=\"button\" class=\"btn\" >Login</a>");
                    echo("</li>");

                    echo("<li class=\"navButton\">");
                    echo("<a href=\"{$base_url}user/register\" role=\"button\" class=\"btn\">Register</a>");
                    echo("</li>");
                 }
                 else{
                    echo("<li class=\"dropdown\">");

                    echo <<<DROP
                    
                    <a class="btn" data-toggle="dropdown" role="button">
                        <img src="{$base_url}assets/img/mic.png" height="16" width="16" />
                        {$userName}
                        <b class="caret"></b>
                    </a>

                    <ul id="dropList" class="dropdown-menu pull-right" role="menu">
                        <li>
                            <a id="myVideos" role="menu" href="#">
                                My Videos
                            </a>
                        </li>

                        <li>
                            <a id="logout" role="menu" href="{$base_url}user/logout">
                                Log Out
                            </a>
                        </li>
                    </ul>

DROP;
                    echo("</li>"); 
                 } 

?>
            </ul>
        </nav>
    </div>
  </div>
</div>
