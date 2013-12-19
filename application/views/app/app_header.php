<!-- App specific Header -->
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>LiveDescribe</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <!-- Mic icon -->
    <link rel="icon" type="image/png" href=<?php echo base_url('/assets/img/mic.png')?> />

    <!-- Bootstrap CSS -->
    <link href=<?php echo base_url('/assets/lib/bootstrap/css/bootstrap.css')?> rel="stylesheet">
    <link href=<?php echo base_url('/assets/lib/bootstrap/css/bootstrap-responsive.css')?> rel="stylesheet">

 
    <style type="text/css">
      body {
        padding-top: 60px;
        padding-bottom: 40px;
      }
    </style>

   
    <!-- CSS -->
    <link rel="stylesheet" type="text/css" href=<?php echo base_url('/assets/css/header.css')?> />
    <link rel="stylesheet" type="text/css" href=<?php echo base_url('/assets/css/navigation.css')?> />
    

    <!-- JQUERY -->
    <script src="http://code.jquery.com/jquery-1.8.3.js"></script>
    <script src="http://code.jquery.com/ui/1.9.2/jquery-ui.js"></script>
    
    <!-- Bootstrap JS -->
    <script src=<?php echo base_url('/assets/lib/bootstrap/js/bootstrap.js')?>></script>
    
    <!--Main site calls, for login etc.-->
    <script type="text/javascript" src=<?php echo base_url('/assets/js/site.js')?> ></script>


     <!-- CSS -->
    <link rel="stylesheet" type="text/css" href=<?php echo base_url("/assets/css/mediaStyles.css")?> />
    <link rel="stylesheet" type="text/css" href=<?php echo base_url("/assets/css/navigation.css")?> />
    <link rel="stylesheet" type="text/css" href=<?php echo base_url("/assets/css/player.css")?>  />
    <link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />

    <!-- JQUERY -->
    <script src="http://code.jquery.com/jquery-1.8.3.js"></script>
    <script src="http://code.jquery.com/ui/1.9.2/jquery-ui.js"></script>
    
    <!-- Bootstrap JS -->
    <script src=<?php echo base_url("/assets/lib/bootstrap/js/bootstrap.js")?>></script>


    
    <!-- swfobject is a commonly used library to embed Flash content -->
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/swfobject/2.2/swfobject.js"></script>

    <!-- Setup the JS recorder interface -->
   <!-- <script type="text/javascript" src="js/recorder.js"></script>-->

    <!-- Setup the FLASH recorder interface -->
    <script type="text/javascript" src=<?php echo base_url('/assets/app/recorderFLASH.js') ?> ></script>

    <!-- basic recording operations -->
    <script type="text/javascript" src=<?php echo base_url("/assets/app/recordOperations.js") ?> ></script>
    
    <!-- Video Controls and various others -->
    <script type="text/javascript" src=<?php echo base_url("/assets/app/editorOperations.js") ?> ></script>
    
    <!-- Objects -->
    <script type="text/javascript" src=<?php echo base_url("/assets/app/objects.js") ?> ></script>

    <!--Main site calls, for login etc.-->
    <script type="text/javascript" src=<?php echo base_url("/assets/js/site.js") ?> ></script>

    <!-- Insert the video id as a javascript variable so that the player can access it. -->
    <?php echo("<script  type=\"text/javascript\">
                    video_id = \"{$vID}\";
                </script>"
            );
    ?>

    <!--Setup Recording Tools-->
    <script type="text/javascript" src=<?php echo base_url("/assets/app/editorSetup.js") ?> ></script>
    

  </head>

  <body>

