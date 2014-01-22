<!-- Common Header -->
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
    
    <link href=<?php //echo base_url('/assets/lib/bootstrap/css/bootstrap-responsive.css')?> rel="stylesheet">
 
    <!-- CSS -->
    <link rel="stylesheet" type="text/css" href=<?php echo base_url('/assets/css/header.css')?> />
    <link rel="stylesheet" type="text/css" href=<?php echo base_url('/assets/css/navigation.css')?> />
    <link rel="stylesheet" href="http://code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css" />
    

    <!-- JQUERY -->
    <script src="http://code.jquery.com/jquery-1.10.2.js"></script>
    <script src="http://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
    
    <!-- Bootstrap JS -->
    <script src=<?php echo base_url('/assets/lib/bootstrap3/js/bootstrap.js')?>></script>
    
    <!--Main site calls, for login etc.-->
    <script type="text/javascript" src=<?php echo base_url('/assets/js/site.js')?> ></script>

  </head>

  <body>
