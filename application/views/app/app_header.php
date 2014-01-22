<!-- App specific Header -->
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>LiveDescribe</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
 
    <!-- Set up global url variable -->
    <?php
        $base_url = base_url();
        echo(" 
            <script type=\"text/javascript\"> 
                var base_url = \"{$base_url}\";
            </script>
        ");
    ?> 

    <!-- Mic icon -->
    <link rel="icon" type="image/png" href=<?php echo base_url('/assets/img/mic.png')?> />

    <!-- Bootstrap CSS -->
    <link href=<?php echo base_url('/assets/lib/bootstrap3/css/bootstrap.css')?> rel="stylesheet">

    <style type="text/css">
      body {
        padding-top: 46px;
        padding-bottom: 0px;
      }
    </style>

   
    <!-- CSS -->
    <link rel="stylesheet" type="text/css" href=<?php echo base_url('/assets/css/header.css')?> />
    <link rel="stylesheet" type="text/css" href=<?php echo base_url('/assets/css/navigation.css')?> />
    <link rel="stylesheet" type="text/css" href=<?php echo base_url("/assets/css/mediaStyles.css")?> />
    
    <link rel="stylesheet" href="http://code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css" />

    <!-- JQUERY -->
    <script src="http://code.jquery.com/jquery-1.10.2.js"></script>
    <script src="http://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
    
    <!-- Bootstrap JS -->
    <script src=<?php echo base_url('/assets/lib/bootstrap3/js/bootstrap.js')?>></script>
   
   <!--  -->
    
    <!-- swfobject is a commonly used library to embed Flash content -->
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/swfobject/2.2/swfobject.js"></script>

    <!-- Setup the JS recorder interface -->
    <script type="text/javascript" src=<?php echo base_url("/assets/js/app/recorderJS/recorder.js")?> ></script>

    <!-- Setup the FLASH recorder interface -->
    <script type="text/javascript" src=<?php echo base_url('/assets/js/app/recorderFLASH.js') ?> ></script>

    <!-- basic recording operations -->
    <script type="text/javascript" src=<?php echo base_url("/assets/js/app/recordOperations.js") ?> ></script>
    
    <!-- Video Controls and various others -->
    <script type="text/javascript" src=<?php echo base_url("/assets/js/app/editorOperations.js") ?> ></script>
    
    <!-- Objects -->
    <script type="text/javascript" src=<?php echo base_url("/assets/js/app/objects.js") ?> ></script>

    <!--Main site calls, for login etc.-->
    <script type="text/javascript" src=<?php echo base_url("/assets/js/site.js") ?> ></script>


    <!--Setup Recording Tools-->
    <script type="text/javascript" src=<?php echo base_url("/assets/js/app/editorSetup.js") ?> ></script>
    
    <!-- Insert the video id as a javascript variable so that the player can access it.
    $vID is passed in as a variable when the view is loaded-->
    <?php 
        echo("
            <script  type=\"text/javascript\">
               var userID = {$userID};
               var video_id = \"{$vID}\";
            </script>
        ");
    ?>

  </head>

  <body>

   
