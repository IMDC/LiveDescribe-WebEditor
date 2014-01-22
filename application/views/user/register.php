<div class="container">

    <div id="reg_area" class="col-md-6 col-md-offset-3 login-reg">

        <h2>Register with LiveDescribe</h2>
        <?php $attributes = array('id' => 'regForm', 'role' => 'form'); ?>
        <?php echo form_open(base_url('user/register_user')  , $attributes); ?>
       
        <div class="form-group">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" value="<?php echo set_value('name'); ?>" style="height:45px; width:100%;" class="form-control" required/>
        </div>

        <div class="form-group">
            <label for="user_name">User Name:</label>
            <input type="text" id="user_name" name="user_name" value="<?php echo set_value('user_name'); ?>" style="height:45px; width:100%;" class="form-control" required/>
        </div>

        <div class="form-group">
            <label for="email_address">Your Email:</label>
            <input type="text" id="email_address" name="email_address" value="<?php echo set_value('email_address'); ?>" style="height:45px; width:100%;" class="form-control" required/>
        </div>

        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" value="<?php echo set_value('password'); ?>" style="height:45px; width:100%;" class="form-control" required/>
        </div>

        <div class="form-group">
            <label for="con_password">Confirm Password:</label>
            <input type="password" id="con_password" name="con_password" value="<?php echo set_value('con_password'); ?>" style="height:45px; width:100%;" class="form-control" required/>
        </div>

        <div class="form-group">
            <label for="question">Secret Question:</label>
            <input type="text" id="question" name="question" value="<?php echo set_value('question'); ?>" style="height:45px; width:100%;" class="form-control" required/>
        </div>

        <div class="form-group">
            <label for="answer">Answer to Secret Question:</label>
            <input type="text" id="answer" name="answer" value="<?php echo set_value('answer'); ?>" style="height:45px; width:100%;" class="form-control" required/>
        </div>

        <?php echo validation_errors('<div id="errorBox" name="errorBox" style="color: red;">'); ?>

        <?php
            $msg = isset($error)?$error : null;
            if($msg != null){
                echo('<div id="errorBox" name="errorBox" style="color: red;">'.$msg.'</div>');
            }
        ?>
            
        <!-- </form> -->
        <?php echo form_close(); ?>

        <?php
        echo('
            <div id="loginLoad"> 
               <img id="loadImg" src="../assets/img/loading.gif" 
               style="display: none;" width=40 alt="loading"/> 
            </div>
        ');

        $data = array(
                    'class' => 'btn btn-primary pull-right',
                    'id'    => 'regRequest',
                    'value' => 'Register',
                    'form'  => 'regForm'
                );
        echo form_submit($data);
        ?>
       
    </div>
  
</div> <!-- /container -->