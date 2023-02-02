<?php
    $title = "install";

    function compare_version_strings($ver1, $ver2){
        if (empty($ver1) && empty($ver2)) return 0;
        if (empty($ver1)) return -1;
        if (empty($ver2)) return 1;
        $vera1 = explode(".", $ver1);
        $vera2 = explode(".", $ver2);
        if (($vera1[0]??0)<($vera2[0]??0)) return -1; else if (($vera1[0]??0)>($vera2[0]??0)) return 1;
        if (($vera1[1]??0)<($vera2[1]??0)) return -1; else if (($vera1[1]??0)>($vera2[1]??0)) return 1;
        if (($vera1[2]??0)<($vera2[2]??0)) return -1; else if (($vera1[2]??0)>($vera2[2]??0)) return 1;
        return 0;
    }

    include ("header.php");
    include ("page_head.php");
    echo ("<script>\n");
    echo ("</script>\n");
    include ("page_header_element.php");

    $fftw = $_POST["fftw"]??"";
    $main = $_POST["main"]??"";
    $working = (empty($fftw) && empty($main)) ? "" : "busy";
    // echo ('<pre>['.$fftw."]\n[".$main.']</pre>');

    if (!empty($fftw)){
        shell_exec('cd src/fftw; bash run-build-fftw.sh');
    } else if (!empty($main)){
        // shell_exec('cd src/rismhi3d; bash run-build-core.sh');
        shell_exec('cd src/kernel; bash run-build-core.sh');
    }

    $check_kernel_update = $_GET["update"]??"";

    $kernel_version = ""; if (!empty($rismhi3d)) $kernel_version = shell_exec($rismhi3d." --version|tr -d '\n'");
    $ver_rismhi3d = ""; if (!empty($rismhi3d)) $ver_rismhi3d = shell_exec($rismhi3d);
    $ver_ts4sdump = ""; if (!empty($ts4sdump)) $ver_ts4sdump = shell_exec($ts4sdump);
    $ver_gmxtop2solute = ""; if (!empty($gmxtop2solute)) $ver_gmxtop2solute = shell_exec($gmxtop2solute);
    $ver_gensolvent = ""; if (!empty($gensolvent)) $ver_gensolvent = shell_exec($gensolvent);
    $ver_heatmap = ""; if (!empty($heatmap)) $ver_heatmap = shell_exec($heatmap);
    $ver_generate_idc = ""; if (!empty($generate_idc)) $ver_generate_idc = $ver_rismhi3d;

    $bin_tar = shell_exec("command -v tar");
    $bin_make = shell_exec("command -v make");

    //echo ('<div class="container" style="text-align:right"><small><a href="install.php?update=yes"><u>Check update</u></a><br></small></div>'."\n");

    //$ver_fftw = ""; if (file_exists("src/fftw/fftw3/lib/libfftw3.a")) $ver_fftw = "3";

    $ver_fftw = ""; if (file_exists("src/fftw/fftw3/lib/libfftw3.a") && file_exists("src/fftw/fftw3/bin/fftw-wisdom-to-conf")) $ver_fftw = shell_exec('src/fftw/fftw3/bin/fftw-wisdom-to-conf -V | head -n1 | awk \'{print $NF}\'');

    echo ('<center>'."\n");

    echo ('<div class="container" style="margin:5 auto;">'."\n");


    if ($check_kernel_update=="yes"){
        $main_update_tip = "";
        $latest_main_version = shell_exec("curl https://raw.githubusercontent.com/EPISOLrelease/EPISOL/main/header.php 2>/dev/null | grep software_version | tr -d '\"' | tr -d ';' | awk '{print \$NF}' | tr -d '\n'");
        if (!empty($latest_main_version)){
            $main_ver_compare = compare_version_strings($software_version, $latest_main_version);
            if ($main_ver_compare==0){
                $main_update_tip = $software_name." is up to date";
            } else if ($main_ver_compare<0){
                $main_update_tip = 'A newer version '.$software_name.' <a href="https://github.com/EPISOLrelease/EPISOL"><u>'.$latest_main_version."</u></a> is available";
            } else {
                $main_update_tip = 'Your have a newer version of '.$software_name;
            }
        }

        $kernel_update_tip = "";
        $latest_kernel_version = shell_exec("curl https://raw.githubusercontent.com/seechin/EPRISM3D/main/configure.ac 2>/dev/null | grep AC_INIT | tr -d '(' | tr -d ')' | tr -d '[' | tr -d ']' | awk '{print \$NF}' | tr -d '\n'");
        if (!empty($latest_kernel_version)){
            $kernel_ver_compare = compare_version_strings($kernel_version, $latest_kernel_version);
            if ($kernel_ver_compare==0){
                $kernel_update_tip = "Kernel is up to date";
            } else if ($kernel_ver_compare<0){
                $kernel_update_tip = 'A new kernel <a href="https://github.com/EPISOLrelease/EPISOL/tree/main/src/kernel"><u>'.$latest_kernel_version."</u></a> is available";
            } else {
                $kernel_update_tip = 'Your have a newer version of kernel';
            }
        }

        if (!empty($main_update_tip)) echo('<br>'.$main_update_tip);
        if (!empty($kernel_update_tip)) echo('<br>'.$kernel_update_tip);
        if (($main_ver_compare??0)<0 || ($kernel_ver_compare??0)<0) echo ('<br><small><a href="help.php#update"><u>click me</u></a> to see how to update</small>');
        echo ("<br>\n");
    } else if (!empty($iet_bin)){
         echo ('<br><a href="install.php?update=yes">Click me to <u>check for update</u></a><br>'."\n");
    }


    if (!empty($ver_fftw)){
        echo ('<br><b>fftw '.$ver_fftw.' installed</b><br>'."\n");
    } else {
        if (empty($iet_bin)) echo ('<br><font color=red>Please install FFTW3, which is required by kernel</font><br>'."\n");
        else echo ('<br>FFTW3 not installed<br>'."\n");
    }

    if (!empty($iet_bin)){
        $kernel_components = 0;
        if (!empty($ver_rismhi3d)) $kernel_components ++;
        if (!empty($ver_ts4sdump)) $kernel_components ++;
        if (!empty($ver_gmxtop2solute)) $kernel_components ++;
        if (!empty($ver_gensolvent)) $kernel_components ++;
        if (!empty($ver_heatmap)) $kernel_components ++;
        if (!empty($ver_generate_idc)) $kernel_components ++;
        echo ('<br><b>'.$software_name.' kernel '.$kernel_version.' installed</b><small><br>'.$kernel_components." components installed</small>\n");
        echo ('<br>'."\n");
        //echo ('<table>'."\n");
        //echo ('<tr><td> <li>'.$ver_rismhi3d.'</td></tr>');
        //echo ('<tr><td> <li>'.$ver_ts4sdump.'</td></tr>');
        //echo ('<tr><td> <li>'.$ver_gmxtop2solute.'</td></tr>');
        //if (!empty($ver_gensolvent)) echo ('<tr><td> <li>'.$ver_gensolvent.'</tr></td>');
        //if (!empty($ver_heatmap)) echo ('<tr><td> <li>'.$ver_heatmap.'</tr></td>');
        //echo ('</table>'."\n");
    } else {
        echo ('<br><font color=red><b>Kernel not installed</b></font><br>'."\n");
    }

    $bin_gnuplot = shell_exec("command -v gnuplot");
    $ver_gnuplot = empty($bin_gnuplot)? "" : shell_exec("gnuplot --version|head -n1|awk '{print $2}'");
    if (!empty($ver_gnuplot)){
        echo ("<br>gnuplot ".$ver_gnuplot." installed<br>");
    } else {
        echo ('<br>Gnuplot not installed<br><small>skip this if you don\'t want to plot RDFs</small><br>'."\n");
    }

    $bin_convert = shell_exec("command -v convert");
    $ver_convert = empty($bin_convert)? "" : shell_exec("convert --version|head -n1|awk '{print $3}'");
    if (!empty($ver_convert)){
        echo ("<br>ImageMagick ".$ver_convert." installed<br>");
        echo ('<small>Please <a href="help.php#analysis_convert_eps" style="color:blue"><u>click me</u></a> to see how to configue<br> ImageMagick to write EPS/PNG</small><br>');
    } else {
        echo ('<br>ImageMagick not installed<br><small>skip this if you don\'t want to plot images</small><br>'."\n");
    }

    echo ('  <br>');
    echo ('  <form action="install.php" method="post" style="display:inline;">'."\n");

    if (!empty($bin_tar) && !empty($bin_make)){
        echo ('    <input type="submit" '.(empty($fftw)?'':'style="color:red"').' name="fftw" value="'.(empty($ver_fftw)?'install':'reinstall').' fftw3" '.(empty($working)?'':'disabled="disabled"').' />'."\n");
        if (empty($ver_fftw)){
            echo ('    <input type="submit" '.(empty($main)?'':'style="color:red"').' disabled value="'.(empty($iet_bin)?'install':'reinstall').' kernel" />'."\n");
        } else {
            echo ('    <input type="submit" '.(empty($main)?'':'style="color:red"').' name="main" value="'.(empty($iet_bin)?'install':'reinstall').' kernel" '.(empty($working)?'':'disabled="disabled"').' />'."\n");
        }
    } else {
        echo ('    <input type="submit" '.(empty($fftw)?'':'style="color:red"').' disabled value="'.(empty($ver_fftw)?'install':'reinstall').' fftw3" />'."\n");
        echo ('    <input type="submit" '.(empty($main)?'':'style="color:red"').' disabled value="'.(empty($iet_bin)?'install':'reinstall').' kernel" />'."\n");
        echo ('    <br><font color=red>Can\'t '.(empty($iet_bin)?'install':'reinstall').', missing:'.(empty($bin_tar)?' <a href="https://www.gnu.org/software/tar/" style="color:red"><u><b>tar</b></u></a>':"").(empty($bin_tar)&&empty($bin_make)?" and":"").(empty($bin_make)?' <a href="https://www.gnu.org/software/make/" style="color:red"><u><b>make</b></u></a>':"").'</font>'."\n");
    }

    if (empty($bin_tar) || empty($bin_make)){
    } else if (!empty($fftw)||!empty($main)){
        echo ('    <br><br><input type="submit" name="stop" value="Has installation finished? Click me to check"/>'."\n");
    } else if (empty($iet_bin)){
        echo ('    <br><br><input type="submit" name="stop" value="Has installation finished? Click me to check"/>'."\n");
    } else {
        #echo ('    <br><br><input type="submit" name="stop" value="Kernel installed, click me won\'t change anything"/>'."\n");
        #echo ('    <br><br>Kernel installed, go to <a href="iet.php">the next step</a>'."\n");
    }
    echo ('  </form><br>'."\n");

    if (file_exists("src/stdout.txt")){
        echo ('<br><a href="viewfile.php?scroll=bottom&u='.$software_home.'/src/stdout.txt&reload=3" target="_blank">Click me the see the <u>installation log</u></a>'."\n");
    } else if (empty($iet_bin)){
        echo ('<br><small><a href="help.php#cannot_write_folder" style="color:#0080FF">Click me if you clicked installation buttons but<br> these two lines of blue text still exist</a></small>'."\n");
    }

    echo ('</div><br>');

    echo ('</center>');

    include ("page_footer.php");

?>
