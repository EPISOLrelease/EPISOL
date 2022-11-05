<?php

    $gmxtop2solute = file_exists("src/kernel/bin/gmxtop2solute")? "src/kernel/bin/gmxtop2solute" : "";
    $rismhi3d = file_exists("src/kernel/bin/eprism3d")? "src/kernel/bin/eprism3d" : "";
    $ts4sdump = file_exists("src/kernel/bin/ts4sdump")? "src/kernel/bin/ts4sdump" : "";
    $gensolvent = file_exists("src/kernel/bin/gensolvent")? "src/kernel/bin/gensolvent" : "";
    $heatmap = file_exists("src/kernel/bin/heatmap")? "src/kernel/bin/heatmap" : "";
    $generate_idc = file_exists("src/kernel/bin/generate-idc.sh")? "src/kernel/bin/generate-idc.sh" : "";
    $iet_bin = (!empty($rismhi3d) && !empty($gmxtop2solute) && !empty($ts4sdump))? "src/kernel/bin" : "";
    $kernel_version = ""; if (!empty($rismhi3d)) $kernel_version = shell_exec($rismhi3d." --version|tr -d '\n'");

  echo("<html><head>\n");
    echo ('<meta charset="utf-8" />'."\n");
    echo ('<link rel="apple-touch-icon" href="images/favicon.png">'."\n");
    $title_folder_name = !empty($url)? explode('/',$url) : "";
    echo("<title>".(!empty($title)?($title." - "):"").$servername."</title>\n");
    echo('<meta name="viewport" content="width='.(empty($page_width)?"device-width":$page_width).', initial-scale=1.0">'."\n");
    if (!empty($auto_reload)) echo('<meta http-equiv="refresh" content="'.$auto_reload.'" />'."\n");
    echo ('<link href="'.$css.'" rel="stylesheet" type="text/css">'."\n");
  echo("</head>"."\n");

?>
