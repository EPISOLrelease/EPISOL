<?php

  echo ('<body>'."\n");

    if (!empty($theme_banner)||!empty($theme_bbanner)){
        echo('<center><div class="logobanner"'.(empty($theme_bbanner)?'':' style="background-image:url(/images/'.$theme_bbanner.')"').'><div class="container">'.(empty($theme_banner)?'':'<a href="index.php"><img src="images/'.$theme_banner.'" class="logo" /></a>').'</div></div></center>'."\n");
    }
    echo ('<div class="menubar"><div class="container">'."\n");
    if (strcasecmp($title, "Expanded Package for IET-based Solvation")==0){
        echo ('  <a href="index.php"><div class="navba">Home</div></a>'."\n");
    } else {
        echo ('  <a href="index.php"><div class="navbb">Home</div></a>'."\n");
    }
    if (empty($iet_bin)){
        if (strcasecmp($title,"Install")==0){
            echo ('  <a href="install.php"><div class="navba">Install</div></a>'."\n");
        } else {
            echo ('  <a href="install.php"><div class="navbb">Install</div></a>'."\n");
        }
    } else {
        if (strcasecmp($title,"Install")==0){
            echo ('  <a href="install.php"><div class="navba">Update</div></a>'."\n");
        } else {
            echo ('  <a href="install.php"><div class="navbb">Update</div></a>'."\n");
        }
    }
    /*if (!empty($gmxtop2solute)){
        if (strcasecmp($title,"top2solute")==0){
            echo ('  <a href="gmxtop2solute.php"><div class="navba">Top2Solute</div></a>'."\n");
        } else {
            echo ('  <a href="gmxtop2solute.php"><div class="navbb">Top2Solute</div></a>'."\n");
        }
    }*/
    if (!empty($rismhi3d)){
        if (strcasecmp($title,"IET")==0){
            echo ('  <a href="iet.php"><div class="navba">IET</div></a>'."\n");
        } else {
            echo ('  <a href="iet.php"><div class="navbb">IET</div></a>'."\n");
        }
    }
    if (!empty($ts4sdump)){
        if (strcasecmp($title,"analysis")==0){
            echo ('  <a href="analysis.php"><div class="navba">Analysis</div></a>'."\n");
        } else {
            echo ('  <a href="analysis.php"><div class="navbb">Analysis</div></a>'."\n");
        }
    }

    if (strcasecmp($title,"help")==0){
        echo ('  <a href="help.php"><div class="navba">Help</div></a>'."\n");
    } else {
        echo ('  <a href="help.php"><div class="navbb">Help</div></a>'."\n");
    }

    echo ('</div></div>'."\n");

?>
