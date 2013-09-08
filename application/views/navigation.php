<div id="navBar" class="navbar navbar-inverse navbar-fixed-top">
  <div class="navbar-inner">
    <div class="container">
      <button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="brand" href="./index.php">LiveDescribe</a>

      <div class="nav-collapse collapse">
        <ul class="nav">
          
          <li><a href="index.php">Home</a></li>
          <li><a href="#about">About</a></li>
          
        </ul>

      </div><!--/.nav-collapse -->
      <form action="main/test" class="nav form-search"  id="searchArea" method="POST" >
            <div class="input-append">
                <input type="text"  id="searchBar" name="searchBar" class="span2 search-query" placeholder="Enter KeyWords or URL" required>
                <button type="submit" id="search" class="btn"><i class="icon-search"></i></button> 
            </div>
        </form> 

        <nav id="navigator" class="nav pull-right">
            <ul id="navItems">
<?php

                 //show the login and registration options if the user is not logged on 
                 if(!isset($_SESSION['userId']) && !isset($_SESSION['token'])){
                    echo("<li class=\"navButton\">"); 
                    echo("<a href=\"./login.php\" role=\"button\" class=\"btn\" >Login</a>");
                    echo("</li>");

                    echo("<li class=\"navButton\">");
                    echo("<a href=\"./register.php\" role=\"button\" class=\"btn\">Register</a>");
                    echo("</li>");
                 }
                 else{
                    $user = $_SESSION['userName'];
                    echo("<li class=\"dropdown\">");

                    echo <<<DROP
                    
                    <a class="btn" data-toggle="dropdown" role="button">
                        <img src="images/mic.png" height="16" width="16" />
                        $user
                        <b class="caret"></b>
                    </a>

                    <ul id="dropList" class="dropdown-menu pull-right" role="menu">
                        <li>
                            <a id="myVideos" role="menu" href="#">
                                My Videos
                            </a>
                        </li>

                        <li>
                            <a id="logout" role="menu" href="#">
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
