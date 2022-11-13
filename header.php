<?php

  // software

    $software_version = "1.1.324";
    if (file_exists("src/eprism3d/ver.php")) include ("src/eprism3d/ver.php");
    $software_name = "EPISol";
    $servername = "EPISol";
    $passwdsalt = "localhost";

  // server and client

    $client_ip=$_SERVER['REMOTE_ADDR']??"";

  // security control

    // $accessroot = getenv('HOME');   // by default, only allow to scan files and subfolders in the home folder of current user
    // $accessroot = getcwd();  // uncomment this line to allow scanning files and subfolders of the software home folder
    $accessroot = "/";       // uncomment this line to allow scanning the whole file system

  // styles

    $css = "style.css"; $theme_banner = "logo_512.png"; $theme_bbanner = "";

    $dir_show_detail = true;

  // stdout

    $src_stdout = "src/stdout.txt";
    $run_stdout = "run/stdout.txt";

  // time zone

    $timezonestring = (int)shell_exec('date +%z');
    $timezonesecond = ($timezonestring>=0?1:-1) * (((int)(abs($timezonestring)/100))*3600 + abs($timezonestring)%100*60);

?>
