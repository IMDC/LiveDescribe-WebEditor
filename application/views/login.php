

<!-- <div class="container"> -->

<!-- Only show this page if the user is not logged on -->
<?php
  
  $userName = $this->session->userdata('userName');
  $userID   = $this->session->userdata('userId');
  //$token    = $this->session->userdata('token');

  if($this->session->userdata('logged_in')){
    redirect(base_url(), 'refresh');
  }


  echo('<div class="container">');
  echo('<div id="login_area" class="span6 offset3 login-reg">');
  echo('<h2>Login to LiveDescribe</h2>');

  $attributes = array('id' => 'loginForm');
  echo form_open(base_url('user/login_user')  , $attributes);
  echo form_label("Username:", 'uname');
  $data = array(
                'id' => 'uname', 
                'name' => 'uname',
                'type' => 'text' ,
                'size' => '30',
                'autofocus' => 'autofocus',
                'style' => 'height:45px;width: 100%;',
                'required' => ''
              );
  echo form_input($data);
  echo form_label("Password:", 'pword');
  $data = array(
                'id' => 'pword', 
                'name' => 'pword',
                'size' => '30',
                'style' => 'height:45px;width: 100%;',
                'required' => ''
              );
  echo form_password($data);
  echo('<br />');
  $msg = isset($error)?$error : null;
  echo('<div id="errorBox" name="errorBox" style="color: red;">'.$msg.'</div>');
  echo form_hidden('request_type', 'login');
  echo form_close();
  echo('
    <div id="loginLoad"> 
       <img id="loadImg" src="../assets/img/loading.gif" 
       style="display: none;" width=40 alt="loading"/> 
    </div>
    ');

  $data = array(
                'class' => 'btn btn-primary pull-right',
                'id' => 'loginRequest',
                'value' => 'login',
                'form' => 'loginForm'
               );
  echo form_submit($data);
  echo('</div>');
  echo('</div>');

?>


  <!-- the above php code should output a similar markup as below

  <div id="login_area" class="span6 offset3 login-reg">

    <h2>Login to LiveDescribe</h2>
    <form id="loginForm" method="post" >
        <label>Username:</label>
        <input id="uname" type="text" name="uname" size="30" autofocus="autofocus" style="height:45px; width:100%;" required/><br />
        <label>Password:</label>
        <input id="pword" type="password" name="pword" size="30" style="height:45px;width: 100%;"  required/> 
        <br/>

        <div id="errorBox" name="errorBox" style="color: red;"></div>

        
        <input id="request_type" name="request_type" type="hidden" value="login">

    </form>
    <div id="loginLoad"></div>
    <input class="btn btn-primary pull-right" id="loginRequest" type="submit" value="Login" form="loginForm"/>
  </div>
</div>
-->

