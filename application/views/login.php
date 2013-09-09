

<div class="container">

<!-- Only show this page if the user is not logged on -->
  <?php
     if(isset($_SESSION['userId']) && isset($_SESSION['token'])){
        header("Location: ");
     }
  ?>

  <div id="login_area" class="span6 offset3 login-reg">

    <h2>Login to LiveDescribe</h2>
    <form id="loginForm" method="post" >
        <label>Username:</label>
        <input id="uname" type="text" name="uname" size="30" autofocus="autofocus" style="height:45px; width:100%;" required/><br />
        <label>Password:</label>
        <input id="pword" type="password" name="pword" size="30" style="height:45px;width: 100%;"  required/> 
        <br/>

        <div id="errorBox" name="errorBox" style="color: red;"></div>

        <!-- sending the type of form -->
        <input id="request_type" name="request_type" type="hidden" value="login">

    </form>
    <div id="loginLoad"></div>
    <input class="btn btn-primary pull-right" id="loginRequest" type="submit" value="Login" form="loginForm"/>
  </div>

</div> <!-- /container -->