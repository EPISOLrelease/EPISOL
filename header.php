<?php

  // software version

    $software_version = "1.1.326";
    // $software_version = "1.2";

  // software

    $software_name = "EPISol";
    $servername = "EPISol";

  // multi-user server settings

    $singleuser_mode = true;
    $maintenance_mode = true;

    if ($singleuser_mode) $maintenance_mode = true;

  // folders

    $software_home = getcwd();
    $solvent_folder = $software_home.'/solvent';
    $solute_folder = $software_home.'/solute';
    $run_folder = $software_home.'/run';
    $src_folder = $software_home.'/src';

  // security control
    // This will set the folders that EPISOL can see. EPISOL will not read or write files outside this folder.

    // $accessroot = getcwd();          // the EPISOL folder
    // $accessroot = getenv('HOME');    // your home folder, typically /home/you_user_name
    $accessroot = "/";                  // your whole server

    if ($singleuser_mode) $accessroot = "/";

  // styles

    $css = "style.css"; $theme_banner = "logo_512.png"; $theme_bbanner = "";
    $dir_show_detail = true;

  // stdout

    $src_stdout = "src/stdout.txt";
    $run_stdout = "run/stdout.txt";

  // time zone

    $timezonestring = -600;
    if (empty($timezonestring)){
        $timezonestring = (int)shell_exec('date +%z');
    }
    $timezonesecond = ($timezonestring>=0?1:-1) * (((int)(abs($timezonestring)/100))*3600 + abs($timezonestring)%100*60);

?>
