<div class="container">

    <div id="reg_area" class="span6 offset3 login-reg">

        <h2>Register with LiveDescribe</h2>
        <form id="regForm" method="post" >
            <label>Username:</label>
            <input id="uname" type="text" name="uname" size="30" style="height:45px; width:100%;" required/><br />
            <label>Password:</label>
            <input id="pword" type="password" name="pword" size="30" style="height:45px;width: 100%;"  required/> <br/>
            <label>Confirm Password:</label>
            <input type="password" id="pword2" name="pword2" size="30" style="height:45px;width: 100%;"  required/> <br/>
            <label>E-Mail:</label>
            <input id="email" type="email" name="email" size="30" style="height:45px;width: 100%;"  required/> <br/>

            <div id="errorBox" name="errorBox" style="color: red;"></div>

            <!-- sending the type of form -->
            <input id="request_type" name="request_type" type="hidden" value="registerUser">
        </form>
        <input class="btn btn-primary pull-right" id="regRequest" type="submit" value="Register"  form="regForm"/>
    </div>
  
</div> <!-- /container -->