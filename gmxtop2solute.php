<?php

    $title = "top2solute";

    include ("header.php");
    include ("page_head.php");

    echo ("<script>\n");
    echo ('function set_text_from_iframe(id_text, id_iframe){
        var iframe = document.getElementById(id_iframe);
        var elmnt = iframe.contentWindow.document.getElementById("address");
        document.getElementById(id_text).value = elmnt.innerHTML;
}'."\n");
    echo ("</script>\n");

    include ("page_header_element.php");

    $top = $_POST["top"]??"";           if (empty($top)) $top = $_GET["top"]??"";
    $lib = $_POST["lib"]??"";           if (empty($lib)) $lib = $_GET["lib"]??"";
    $btop = $_POST["btop"]??"";
    $solutename = $_POST["sname"]??"";  if (empty($solutename)) $solutename = $_GET["sname"]??"";

    if (empty($lib)){
        $gmx_source_lib = getenv('GMXDATA');
        if (!empty($gmx_source_lib)){
            if (file_exists($gmx_source_lib."/top") && is_dir($gmx_source_lib."/top")){
                $lib = $gmx_source_lib."/top";
            } else if (file_exists($gmx_source_lib."/gromacs/top") && is_dir($gmx_source_lib."/gromacs/top")){
                $lib = $gmx_source_lib."/gromacs/top";
            }
        }
    }

    echo ('<div class="contentcontainer"><div class="container">'."\n");

    echo ('<center>'."\n");

    echo ('<h4>GROMACS TOP to solute</h4>'."\n");

    echo ('<form action="gmxtop2solute.php" method="post" style="display:inline;">'."\n");
      echo ('<table><tr><td>'."\n");
        echo ('$GMXDATA folder:</td><td>');
        echo ('<input type="text" id="lib" name="lib" value="'.(empty($lib)?"":$lib).'" style="width:350" placeholder="The folder defined in $GMXDATA. Source GMXRC or type it here."/><br>');
        echo ('</td></tr><tr><td>'."\n");
        echo ('Gromacs top file:</td><td>');
        echo ('<input type="text" id="top" name="top" value="" style="width:350"/>');
        echo ('</td></tr><tr><td>'."\n");
        echo ('Name of solute:</td><td>');
        echo ('<input type="text" id="sname" name="sname" value="'.(empty($solutename)?"":$solutename).'" style="width:160"  placeholder="use name of top file"/>');
        echo ('<span class="detailfont"> </span>');
        echo ('<input type="submit" style="float:right" id="btop" name="btop" value="generate"/>'."\n");
      echo ('</td></tr></table>'."\n");
    echo ('</form><br>'."\n");

    if (!empty($top)){
        echo ('<table style="width:90%;height:300;table-layout:fixed"><tr><td style="width:40%">'."\n");
    }
    echo ('<iframe name="gmxdir" id="gmxdir" src="dir.php?c=.top&path='.(empty($top)?getcwd():$top).'" width='.(empty($top)?"90%":"100%").' height='.(empty($top)?"300":"100%").' frameborder=0 onLoad="set_text_from_iframe(\'top\', \'gmxdir\')"></iframe>'."\n");
    if (!empty($btop)){
        echo ('</td><td style="width:60%;">'."\n");
        echo ('<div style="width:100%;height:100%;background-color:#F8F8F8;word-wrap:break-word;overflow-y:scroll">'."\n");

        if (is_dir($top)){
            echo ('error : '.$top.' is a directory'."\n");
        } else if (strcasecmp(pathinfo($top, PATHINFO_EXTENSION), "top")!=0){
            echo ('gmxtop2solute : error : cannot handle non .top file: '.$top."\n");
        } else {
            if (empty($solutename)){
                $topfilename = basename($top);
                $solutefilename = substr($topfilename, 0, strlen($topfilename)-3)."solute";
            } else {
                $solutefilename = $solutename.".solute";
            }
            echo("<pre>");
            if (!empty($lib)){
                if (!file_exists($lib)) echo ('warning : '.$lib." does not exist\n");
                else if (!is_dir($lib)) echo ('warning : '.$lib." is not a folder\n");
            }
            echo(shell_exec($gmxtop2solute." -ab -top ".$top.(empty($lib)?"":(" -ffpath ".$lib)).' -o solute/'.$solutefilename.' 2>&1'));
            if (file_exists('solute/'.$solutefilename)){
                echo ('<b>solute/'.$solutefilename.' successfully generated from '.$top.'</b>'."\n");
                echo (shell_exec("cat ".'solute/'.$solutefilename));
              // do IDC
                $idcsolutefilename = $solutename.".idc.solute";
                if (file_exists($generate_idc)){
                    $idc_lines = (shell_exec('bash '.$generate_idc.' solute/'.$solutefilename.' serial | grep sigmas= | wc -l'));
                    if (intval($idc_lines)>0){
                        shell_exec('bash '.$generate_idc.' solute/'.$solutefilename.' serial > solute/'.$idcsolutefilename);
                        if (file_exists("solute/".$solutename.".idc.solute")){
                            echo ('<br><b>solute/'.$idcsolutefilename.' successfully generated from solute/'.$solutefilename.'</b>'."\n");
                            echo (shell_exec("cat ".'solute/'.$idcsolutefilename));
                        }
                    }
                }
            }
            echo("</pre>\n");
        }
        echo ("</div>\n");
        echo ('</td></tr></table>'."\n");
    }

    echo ('</center>'."\n");

    echo ('</div><br></div>'."\n");

    include ("page_footer.php");

?>
