<?php

    $phpurl = $_SERVER['PHP_SELF']?? "";
    $phpname = "viewfile.php";
    $rooturl = "/"; if (!empty($phpurl)) $rooturl = substr($phpurl, 0, strlen($phpurl)-strlen($phpname)); if ($rooturl=="/") $rooturl="";

    function menu_bar($class, $alpha, $phpurl, $url, $scroll, $url_allow_edit, $filename, $filetype, $fn_noext, $dirname, $original, $reload){
        $chagereload = empty($reload)?"&reload=3":"";

        echo ('<body'.(empty($scroll)?'':' onload="document.body.scrollTop='.($scroll=="bottom"?99999999999:$scroll).'"').'>'."\n");
        echo ('<div class="'.$class.'" style="opacity:'.$alpha.'"><div class="container">'."\n");
        $path_in_http = (substr($url, 0, strlen(getcwd())+1)==getcwd().'/')? substr($url, strlen(getcwd())+1) : "";
        if (!empty($path_in_http) && !is_dir($url)){
            echo ('  <a href="'.$path_in_http.'" target="_blank" onclick="return confirm(\'Sure to download file?\n\n'.$filename.'\')"><div class="navbc2" style="float:left" >'.$filename.'</div></a>');
        } else {
            echo ('  <div class="navbc2" style="float:left" >'.$filename.'</div>');
        }
        echo ('<div style="display:inline-block;float:right;">');
        //if ($url_allow_edit){
        if (empty($original)){
            echo ('  <div class="navbc" onclick=reload_page("'.$phpurl.'?u='.$url.$chagereload.'")>'.(empty($reload)?'Refresh':'StopRefreshing')."</div>\n");
        } else {
            echo ('  <div class="navbc" onclick=reload_page("'.$phpurl.'?u='.$url.$chagereload.'&original='.$original.'")>'.(empty($reload)?'Refresh':'StopRefreshing')."</div>\n");
        }
        if ($url_allow_edit){
            if ($filetype==102||$filetype==103){
                if (!empty($original) && $original=="on"){
                    echo ('  <div class="navbc" onclick=reload_page("'.$phpurl.'?u='.$url.'")>ViewPlot</div>'."\n");
                } else {
                    echo ('  <div class="navbc" onclick=reload_page("'.$phpurl.'?u='.$url.'&original=on")>ViewText</div>'."\n");
                }
            }
            if (is_writable($url)){
                echo ('  <div class="navbc" onclick="reload_page(\''.$phpurl.'?u='.$url.'&edit=lock\')" >Lock</div>'."\n");
            } else {
                echo ('  <div class="navbc" onclick="reload_page(\''.$phpurl.'?u='.$url.'&edit=unlock\')" >Unlock</div>'."\n");
            }
            echo ('  <div style="color:#E00000" class="navbc" onclick="confirm_reload(\'Are you sure to delete the following file from you disk?\n\n'.$filename.'\n\nCaution: this operation cannot be undone.\', \''.$phpurl.'?u='.$url.'&edit=delete\')" >Delete</div>'."\n");
            echo ('  <div class="navbc" onclick="confirm_reload(\'Make another copy of '.$filename.'?\', \''.$phpurl.'?u='.$url.'&edit=duplicate\')" >Duplicate</div>'."\n");
            echo ('  <div class="navbc" onclick="confirm_rename(\''.$url.'\', \''.(is_dir($url)?$filename:$fn_noext).'\')" >Rename</div>'."\n");
            echo ('  <div class="navbc" onclick="confirm_move(\''.$url.'\', \''.$dirname.'\')" >Move</div>'."\n");
        }
        echo ('</div>');
        echo ('</div></div>');
        echo ("</body>\n");

        return $path_in_http;
    }

  // outer keywords
    $keys = array( "u", "url" );
    foreach($keys as $keyi){ $key = $keyi; $url = $_GET[$key]??""; if (!empty($url)) break; }
    $rebuild = $_GET["rebuild"]??"";
  // internal filenames
    $dirname = dirname($url);
    $filename = basename($url);
    $ext = is_dir($url)? "" : pathinfo($url,PATHINFO_EXTENSION);
    $url_noext = substr($url, 0, strlen($url) - strlen($ext) - (substr($url,strlen($url)-strlen($ext)-1,1)=='.'?1:0));
    $fn_noext = substr($filename, 0, strlen($filename) - strlen($ext) - (substr($filename,strlen($filename)-strlen($ext)-1,1)=='.'?1:0));
  // inner keywords
    $frame = $_GET["frame"]?? "0";
    $original = $_GET["original"]??"";
    $edit = $_GET["edit"]??"";  // copy/move/rename/delete
    $dest = $_GET["dest"]??"";  // destination of copy/move/rename/delete
    $scroll = $_GET["scroll"]??"";
    $reload = $_GET["reload"]??""; // seconds to reload

    $phpurl = "viewfile.php";

    function assess_access_in($pwd, $accessroot) {
        if (empty($pwd) || empty($accessroot)) return true;
        if ($accessroot=='/') return $pwd=='/'? false : true;
        if (strlen($pwd) <= strlen($accessroot)){
            return false;
        } else {
            if (substr($pwd, 0, strlen($accessroot)+1) == $accessroot."/") return true;
            else return false;
        }
    }

    $title = $url;
    include ("header.php");
    echo ('<script>
    function reload_page(url){
        window.location.replace(url+"&scroll="+'.($scroll=="bottom"?'"bottom"':'window.scrollY').');
    }
    function confirm_reload(text, url){
        if (confirm(text)) reload_page(url);
    }
    function confirm_rename(url, filename){
        newname = prompt("Pick a new name of file:", filename); if (newname != null && newname != "" && newname!=filename) {
            reload_page("'.$phpurl.'?u='.$url.'&edit=rename&dest="+newname);
        }
    }
    function confirm_move(url, dirname){
        newname = prompt("Pick a new desitation:", dirname); if (newname != null && newname != "" && newname!=dirname) {
            reload_page("'.$phpurl.'?u='.$url.'&edit=move&dest="+newname);
        }
    }
</script>'."\n");
    if (!empty($reload)) if ($reload>0){
        echo ('<script>
    function auto_reload(){
        location.reload();
        document.body.scrollTop='.($scroll=="bottom"?99999999999:$scroll).';
    }
    setTimeout(auto_reload, '.($reload*1000).');
</script>'."\n");
    }


    $filetype = 0;
    if (!is_dir($url)){
        $finfo = finfo_open(FILEINFO_MIME);
        $ftype = finfo_file($finfo, $dirname.'/'.$filename);
        if (substr($ftype, 0, 4) == 'text'){
            $filetype = 1;
        } else if (substr($ftype, 0, 5) == 'image'){
            $filetype = 3; 
        }
        if (strcasecmp($ext, "htm")==0){
            $filetype = 2; 
        } else if (strcasecmp($ext, "html")==0){
            $filetype = 2; 
        } else if (strcasecmp($ext, "php")==0){
            $filetype = 1; 
        } else if (strcasecmp($ext, "ts4s")==0){
            $filetype = 101;
        } else if (strcasecmp($ext, "rdf")==0){
            $filetype = 102;
        } else if (strcasecmp($ext, "eps")==0){
            $filetype = 103;
        } else if (strcasecmp($ext, "tar")==0){
            $filetype = 104;
        } else if (strcasecmp($ext, "gz")==0){
            $filetype = 104;
        } else if (strcasecmp($ext, "zip")==0){
            $filetype = 105;
        }
    }

/*
    if (!empty($reload)) if ($reload>0){
        echo ('<script>
    const filename = "'.$dirname.'/'.$filename.'";
    var lastmtime = -1;
    const Http = new XMLHttpRequest();
    function auto_reload(){
        Http.open("GET", "filemtime.php?file="+filename);
        Http.onreadystatechange=(e)=>{ if (Http.readyState === XMLHttpRequest.DONE){
            var thismtime = Http.responseText;
            if (lastmtime>0 && lastmtime!=thismtime){
                location.reload();
                '.(empty($scroll)?"//":"").'document.body.scrollTop='.($scroll=="bottom"?99999999999:$scroll).';
            }
            lastmtime = thismtime;
        }}
        Http.send();
    }
    setInterval(auto_reload, '.($reload*1000).');
</script>'."\n");
    }
*/
    include ("page_head.php");

    $url_allow_edit = false;
    if (assess_access_in($url, getcwd().'/run')) $url_allow_edit = true;
    if (assess_access_in($url, getcwd().'/solute')) $url_allow_edit = true;

    /*echo ("dirname ".$dirname."<br>\n");
    echo ("filename ".$filename."<br>\n");
    echo ("extension ".$ext."<br>\n");
    echo ("url_no_ext ".$url_noext."<br>\n");
    echo ("fn_no_ext ".$fn_noext."<br>\n");*/

    if ($url_allow_edit){
        if ($edit == "delete"){
            shell_exec('rm -rf '.$url);
            if (file_exists($url)){
                echo ('<div class="alert"><strong>Error</strong>: cannot delete '.$filename.'<span class="closebtn" onclick="this.parentElement.style.display=\'none\';">&times;</span></div>');
            } else {
                echo ('<div class="notify">'.$filename.' deleted<span class="closebtn" onclick="this.parentElement.style.display=\'none\';">&times;</span></div>');
                $url = $dirname;
                $filename = basename($url);
                $ext = pathinfo($url,PATHINFO_EXTENSION);
                $url_noext = substr($url, 0, strlen($url) - strlen($ext) - (substr($url,strlen($url)-strlen($ext)-1,1)=='.'?1:0));
                $fn_noext = substr($filename, 0, strlen($filename) - strlen($ext) - (substr($filename,strlen($filename)-strlen($ext)-1,1)=='.'?1:0));
            }
        } else if ($edit == "duplicate"){
            $newname = ""; for ($i=2; $i<=10000; $i++){
                $this_newname = empty($ext)? $url_noext."_copy_".$i : $url_noext."_copy_".$i.'.'.$ext;
                if (!file_exists($this_newname)){ $newname = $this_newname; break; }
            }
            shell_exec ('cp -r '.$url." ".$newname);
            if (!file_exists($newname)){
                echo ('<div class="alert"><strong>Error</strong>: cannot duplicate '.$filename.'<span class="closebtn" onclick="this.parentElement.style.display=\'none\';">&times;</span></div>');
            } else {
                shell_exec ('chmod -R +w '.$newname);
                echo ('<div class="notify">'.$filename.' duplicated to '.$newname.'<span class="closebtn" onclick="this.parentElement.style.display=\'none\';">&times;</span></div>');
            }
        } else if ($edit == "rename"){
            $newname = empty($ext)? $dirname.'/'.$dest : $dirname.'/'.$dest.'.'.$ext;
            shell_exec ('mv '.$dirname.'/'.$filename.' '.$newname);
            if (file_exists($url)){
                echo ('<div class="alert"><strong>Error</strong>: cannot rename '.$filename.' to '.$dest.'.'.$ext.'<span class="closebtn" onclick="this.parentElement.style.display=\'none\';">&times;</span></div>');
            } else {
                echo ('<div class="notify">'.$filename.' renamed to '.$dest.'.'.$ext.'<span class="closebtn" onclick="this.parentElement.style.display=\'none\';">&times;</span></div>');
                $url = empty($ext)? $dirname.'/'.$dest : $dirname.'/'.$dest.'.'.$ext;
                $filename = basename($url);
                $url_noext = substr($url, 0, strlen($url) - strlen($ext) - (substr($url,strlen($url)-strlen($ext)-1,1)=='.'?1:0));
                $fn_noext = substr($filename, 0, strlen($filename) - strlen($ext) - (substr($filename,strlen($filename)-strlen($ext)-1,1)=='.'?1:0));
            }
        } else if ($edit == "move"){
            $newdir = $dest; $newfile = $newdir.'/'.$filename;
            $url_allow_edit = false;
            if (assess_access_in($newfile, getcwd().'/run')) $url_allow_edit = true;
            if (assess_access_in($newfile, getcwd().'/solute')) $url_allow_edit = true;
            if (!$url_allow_edit){
                echo ('<div class="alert"><strong>Error</strong>: access denied : cannot move '.$filename.' to '.$newdir.'<span class="closebtn" onclick="this.parentElement.style.display=\'none\';">&times;</span></div>');
            } else {
                shell_exec ('mv '.$url.' '.$newdir.'/'.$filename);
                if (file_exists($url)){
                    echo ('<div class="alert"><strong>Error</strong>: cannot move '.$filename.' to '.$newdir.'<span class="closebtn" onclick="this.parentElement.style.display=\'none\';">&times;</span></div>');
                } else {
                    echo ('<div class="notify">'.$filename.' moved to '.$dest.'<span class="closebtn" onclick="this.parentElement.style.display=\'none\';">&times;</span></div>');
                    $url = $newfile;
                    $dirname = dirname($url);
                    $filename = basename($url);
                    $ext = is_dir($url)? "" : pathinfo($url,PATHINFO_EXTENSION);
                    $url_noext = substr($url, 0, strlen($url) - strlen($ext) - (substr($url,strlen($url)-strlen($ext)-1,1)=='.'?1:0));
                    $fn_noext = substr($filename, 0, strlen($filename) - strlen($ext) - (substr($filename,strlen($filename)-strlen($ext)-1,1)=='.'?1:0));
                }
            }
        } else if ($edit == "lock"){
            shell_exec ('chmod -R -w '.$url);
        } else if ($edit == "unlock"){
            shell_exec ('chmod -R +w '.$url);
        }
    }

    if (assess_access_in($url, getcwd().'/run')) $url_allow_edit = true;
    if (assess_access_in($url, getcwd().'/solute')) $url_allow_edit = true;

    menu_bar("menubar", 0.2, $phpurl, $url, $scroll, $url_allow_edit, $filename, $filetype, $fn_noext, $dirname, $original, $reload);
    $path_in_http = menu_bar("topbar", 1, $phpurl, $url, $scroll, $url_allow_edit, $filename, $filetype, $fn_noext, $dirname, $original, $reload);

    if (!$url_allow_edit) $rebuild = "";

    if ($url_allow_edit && ($filetype==102||$filetype==103)){  // check whether it's necessary to rebuild PNG for RDF and EPS files
        $ver_gnuplot = shell_exec("gnuplot --version|head -n1|awk '{print $2}'");
        $ver_convert = shell_exec("convert --version|head -n1|awk '{print $3}'");
        if (empty($ver_gnuplot) || empty($ver_convert)){
            $rebuild = "";
        } else {
            $fn_noext = substr($filename, 0, strlen($filename) - strlen($ext) - (substr($filename,strlen($filename)-strlen($ext)-1,1)=='.'?1:0));
            $plotfilename = $fn_noext.".png";
            if (file_exists($dirname.'/'.$plotfilename)){
                $rebuild = "";
            } else {
                $rebuild = "plot";
            }
        }
    }

    if (empty($url)){
        echo ("<body>\n");
        echo ("<center><h4>Nothing to view</h4></center>\n");
    } else if (!file_exists($url)){
        echo ("<body>\n");
        echo ("<center><h4>".$url." doesn't exist</h4></center>\n");
    } else {
        if (is_dir($url)){
            echo ("<body>\n");
            echo ('<div class="contentcontainer"><div class="container">'."\n"); //echo ('<div class="container" style="width:100%;height:100%;word-wrap:break-word;overflow-y:scroll">'."\n");
            $access_readable = is_readable($url);
            $access_writeable = is_writable($url);
            $access_editable = $url_allow_edit;
            $access_wwwvisit = assess_access_in($url, getcwd());
            echo ('<pre>
  Folder:   <b>'.$filename.'</b>
  Location: <b>'.$url.'</b>
  Modified: '.(date("Y-m-d, D, H:i:s", filemtime($url) + $timezonesecond)).'
  Items:    '. count(glob($url."/*")).'
  Access:  '. ($access_readable?($access_writeable?' writable':' readonly'):' <font color=red>not-accessible</font>'). ($access_editable?", editable":", not editable").'
  URL:      '. (empty($path_in_http)?(getcwd()==$url?'/':'<font color=#AA0000>(not available)</a>') : ('/'.$path_in_http)) .'
            </pre>'."\n");
            //echo ('<iframe name="dir" id="dir" src="/dir.php?path='.$url.'" width=100% height=100% frameborder=0></iframe>'."\n");
            echo ("\n".'</div></div>'."\n");
        } else {

            /*$filetype = 0;

            if ($filename=="stdout" || $filename=="stderr" || $filename==".settings.php"){
                $filetype = 1;
            } else if (empty($ext) && !is_executable($url)){
                $filetype = 1;
            } else if (strcasecmp($ext, "txt")==0){
                $filetype = 1;
            } else if (strcasecmp($ext, "log")==0){
                $filetype = 1;
            } else if (strcasecmp($ext, "sh")==0){
                $filetype = 1;
            } else if (strcasecmp($ext, "h")==0){
                $filetype = 1;
            } else if (strcasecmp($ext, "c")==0){
                $filetype = 1;
            } else if (strcasecmp($ext, "cc")==0){
                $filetype = 1;
            } else if (strcasecmp($ext, "cpp")==0){
                $filetype = 1;
            } else if (strcasecmp($ext, "cxx")==0){
                $filetype = 1;
            } else if (strcasecmp($ext, "f")==0){
                $filetype = 1;
            } else if (strcasecmp($ext, "for")==0){
                $filetype = 1;
            } else if (strcasecmp($ext, "f77")==0){
                $filetype = 1;
            } else if (strcasecmp($ext, "f90")==0){
                $filetype = 1;
            } else if (strcasecmp($ext, "f03")==0){
                $filetype = 1;
            } else if (strcasecmp($ext, "f15")==0){
                $filetype = 1;
            } else if (strcasecmp($ext, "f22")==0){
                $filetype = 1;
            } else if (strcasecmp($ext, "py")==0){
                $filetype = 1;
            } else if (strcasecmp($ext, "tex")==0){
                $filetype = 1;
            } else if (strcasecmp($ext, "bib")==0){
                $filetype = 1;
            } else if (strcasecmp($ext, "bbl")==0){
                $filetype = 1;
            } else if (strcasecmp($ext, "gnuplot")==0){
                $filetype = 1;
            } else if (strcasecmp($ext, "pdb")==0){
                $filetype = 1;
            } else if (strcasecmp($ext, "gro")==0){
                $filetype = 1;
            } else if (strcasecmp($ext, "top")==0){
                $filetype = 1;
            } else if (strcasecmp($ext, "prmtop")==0){
                $filetype = 1;
            } else if (strcasecmp($ext, "itp")==0){
                $filetype = 1;
            } else if (strcasecmp($ext, "solute")==0){
                $filetype = 1;
            } else if (strcasecmp($ext, "gaff")==0){
                $filetype = 1;
            } else if (strcasecmp($ext, "amber03")==0){
                $filetype = 1;
            } else if (strcasecmp($ext, "css")==0){
                $filetype = 1;
            } else if (strcasecmp($ext, "htm")==0){
                $filetype = 2;
            } else if (strcasecmp($ext, "html")==0){
                $filetype = 2;
            } else if (strcasecmp($ext, "php")==0){
                $filetype = -2;
            } else if (strcasecmp($ext, "jpg")==0){
                $filetype = 3;
            } else if (strcasecmp($ext, "jpeg")==0){
                $filetype = 3;
            } else if (strcasecmp($ext, "gif")==0){
                $filetype = 3;
            } else if (strcasecmp($ext, "png")==0){
                $filetype = 3;
            } else if (strcasecmp($ext, "bmp")==0){
                $filetype = 3;
            } else if (strcasecmp($ext, "webp")==0){
                $filetype = 3;
            } else if (strcasecmp($ext, "ts4s")==0){
                $filetype = 101;
            } else if (strcasecmp($ext, "rdf")==0){
                $filetype = 102;
            } else if (strcasecmp($ext, "eps")==0){
                $filetype = 103;
            } else if (strcasecmp($ext, "tar")==0){
                $filetype = 104;
            } else if (strcasecmp($ext, "gz")==0){
                $filetype = 104;
            } else if (strcasecmp($ext, "zip")==0){
                $filetype = 105;
            }
            */

    //echo ("server document root:".$_SERVER['DOCUMENT_ROOT']."<br>\n");
    //echo ("current path:".getcwd()."<br>\n");
    //echo ("server root: ".$rooturl."<br>\n");
    //echo ("file type: ".$filetype."<br>\n");

            if ($filetype==1 || $filetype==-1){  // text files
                echo ('<body>'."\n");
                echo ('<div class="container" style="width:100%;word-break:break-all;word-wrap:break-word">'); echo ('<pre style="overflow-x:auto;white-space:pre-wrap;word-wrap:break-word">');
                echo (shell_exec("cat ".$url));
                echo ("</pre></div>");
            } else if ($filetype==2){   // html files
                echo (shell_exec("cat ".$url));
                return ;
            } else if ($filetype==-2){  // php files
                if (basename($url) != basename($phpurl)){
                    include ($url);
                } else {
                    echo ('<div class="alert"><strong>Error</strong>: cannot display due to infinite recursion</div>');
                }
                return ;
            } else if ($filetype==3){   // images
                echo ("<body>\n");
                 echo ('<div style="overflow-y:scroll;overflow-x:scroll">'."\n");
                $pwd = getcwd();
                if (substr($url, 0, strlen($pwd)) == $pwd){
                    echo ('<center><img src="'.$rooturl.substr($url, strlen($pwd), strlen($url)-strlen($pwd)).'" id="image" name="image" style="max-width:100%;max-height:100%" /></center>'."\n");
                    //echo ('<center><div class="container" style="position:absolute;bottom:0;">'.$filename.'</div></center>'."\n");
                } else {
                    echo ('<div class="alert"><strong>Error</strong>: access denied : cannot display '.$filename.' (outside the server root folder)</div>');
                }
                echo ('</div>'."\n");
            } else if ($filetype==101){     // ts4s file
                $extract_path = $url.".frame_".$frame;
                $short_extract_path = basename($extract_path);
                $colormap = $extract_path.'/'.$filename.".colormap.txt";

                //include ("header.php");
                //include ("page_head.php");
                echo ('<div style="word-break:break-all;word-wrap:break-word;overflow-y:scroll">'."\n");
                echo ('<pre style="overflow-x:auto;white-space:pre-wrap;word-wrap:break-word">');

                $nframes_ts4s = (int)shell_exec($ts4sdump." -n -f ".$url);
                if (!empty($nframes_ts4s) && $nframes_ts4s>0){
                    echo (' <label><input type="radio" name="option" value="0" '.($frame<1||$frame>$nframes_ts4s?"checked":"").' onClick=reload_page("'.$phpurl.'?u='.$url.'&frame=0") > '.$filename.' </label>'."\n");
                    for ($i=1; $i<=$nframes_ts4s; $i++) echo (' <label><input type="radio" name="option" value="'.$i.'" '.($frame==$i?"checked":"").' onClick=reload_page("'.$phpurl.'?u='.$url.'&frame='.$i.'") > Frame '.(shell_exec($ts4sdump.' -l '.$i.' -f '.$url.'|head -n1|tr "\n" " "')).'</label>'."\n");
                    echo ('<br>'."\n");
                    if ($frame>=1 && $frame<=$nframes_ts4s){
                        echo (shell_exec($ts4sdump.' -list '.$frame.' -f '.$url));
                        $dimensions =  preg_split('/\s+/',  shell_exec($ts4sdump.' -dim '.$frame.' -f '.$url));
                        $dimension_x = (int)$dimensions[2]; $dimension_y = (int)$dimensions[3]; $dimension_z = (int)$dimensions[4]; $dimension_v = (int)$dimensions[5];
                        if ($dimension_x*$dimension_y*$dimension_z*$dimension_v > 1){
                            echo ('<br><center>'."\n");
                            //echo ('<br><center><input type="submit" value="extract" onclick=reload_page("'.$phpurl.'?u='.$url.'&frame='.$frame.'&rebuild=on")  /></center><br>');
                            echo ('<input type="submit" value="extract data" onclick="confirm_reload(\'Extract frame '.$frame.' of '.$filename.' to '.$short_extract_path.'?\', \''.$phpurl.'?u='.$url.'&frame='.$frame.'&rebuild=on\')"  />');
                            echo ('<input type="submit" value="render images" onclick="confirm_reload(\'Plot frame '.$frame.' of '.$filename.' from extracted data in '.$short_extract_path.'?\', \''.$phpurl.'?u='.$url.'&frame='.$frame.'&rebuild=plot\')"  />');
                            echo ('</center><br>'."\n");
                        }
                    } else {
                        echo ($filename." has ".$nframes_ts4s." frames"."\n");
                        echo (shell_exec($ts4sdump.' -list -f '.$url));
                    }
                }
                echo (''."\n");

                $extracting_in_progress = false;
                if (!empty($heatmap) && file_exists($colormap)) $extracting_in_progress = true;

                if (!empty($rebuild) && $frame>=1 && $frame<=$nframes_ts4s){
                    $dimensions =  preg_split('/\s+/',  shell_exec($ts4sdump.' -dim '.$frame.' -f '.$url));
                    $dimension_x = (int)$dimensions[2]; (int)$dimension_y = $dimensions[3]; (int)$dimension_z = $dimensions[4]; $dimension_v = (int)$dimensions[5];
                    if ($dimension_x*$dimension_y*$dimension_z*$dimension_v <= 1){
                        echo ("<font color=green> Nothing to extract: <b>".$filename."</b> (frame ".$frame.") </font><br>");
                    } else if ($extracting_in_progress){
                        echo ('<font color=red> Error : another extraction/rendering in progress. <br>  If this is message is incorrect, then please manually delete '.basename($colormap).' </font><br><br>');
                    } else {
                        if (!file_exists($extract_path)) shell_exec('mkdir '.$extract_path);
                        if (file_exists($extract_path) && is_dir($extract_path)){
                            if ($rebuild=="plot"){
                                shell_exec('nohup bash tools/extract-ts4s-and-render-image.sh '.$ts4sdump.' '.$heatmap.' '.$url.' '.$extract_path.' '.$frame.' '.$colormap.' > '.$extract_path.'/stdout.txt 2>&1 &');
                            } else {
                                shell_exec('nohup bash tools/extract-ts4s-data.sh '.$ts4sdump.' '.$url.' '.$extract_path.' '.$frame.' '.$colormap.' > '.$extract_path.'/stdout.txt 2>&1 &');
                            }
                            $extracting_in_progress = true;
                        } else echo ("<font color=red> Error: cannot create folder <b>".$short_extract_path."</b></font><br>");
                    }
                }

                if (file_exists($extract_path)){
                    if ($extracting_in_progress){
                        echo ("<font color=#ff8000> Extracting in progress : <b>".$filename."</b> (frame ".$frame.") </font><br>");
                    } else {
                        echo ("<font color=green> <b>".$filename."</b> (frame ".$frame.") has been extracted to <b>".$short_extract_path."</b> </font><br>");
                    }
                }

                echo ('</pre>');
                echo ('</div>'."\n");
            } else if ($filetype==102 || $filetype==103){       // rdf or eps file
                echo ("<body>\n");
                echo ('<div style="word-break:break-all;word-wrap:break-word;overflow-y:scroll">'."\n");
                if ($original){
                    echo ('<pre style="overflow-x:auto;white-space:pre-wrap;word-wrap:break-word">'.shell_exec("cat ".$url)."</pre>");
                } else {
                    $url_gnuplot = $url_noext.".gnuplot"; $url_eps = $url_noext.".eps"; $url_png = $url_noext.".png";
                    if (!empty($rebuild)){
                        if ($filetype==102) shell_exec("bash tools/build-png-from-rdf.sh ".$url_noext);
                        if ($filetype==103) shell_exec("bash tools/build-png-from-eps.sh ".$url_noext);
                    }
                    if (file_exists($url_png) && filemtime($url_png) >= filemtime($url)){
                        $pwd = getcwd();
                        echo ('<center><img src="'.$rooturl.substr($url_png, strlen($pwd), strlen($url_png)-strlen($pwd)).'" style="max-width:100%;max-height:100%"/></center>'."\n");
                        //echo ('<center><div class="container" style="position:absolute;bottom:0;">'.$filename.' -> <b>'.$fn_noext.'.png</b></div></center>'."\n");
                    } else echo ('<pre style="overflow-x:auto;white-space:pre-wrap;word-wrap:break-word">'.shell_exec("cat ".$url)."</pre>");
                }
                echo ('</div>'."\n");
            } else if ($filetype==104 || $filetype==105){
                echo ("<body>\n");
                echo ('<div style="word-break:break-all;word-wrap:break-word;overflow-y:scroll">'."\n");
                echo ('<pre>');
                if ($filetype==104){
                    echo (shell_exec('tar -tvf '.$url));
                } else if ($filetype==105){
                    echo (shell_exec('unzip -l '.$url));
                }
                echo ('</pre>');
                echo ('</div>'."\n");
            } else {
                echo ("<body>\n");
                echo ('<div style="word-break:break-all;word-wrap:break-word;overflow-y:scroll">'."\n");
                $extinfo = $ext.' file';
                if (empty($ext)) $extinfo = is_executable($url)? " executable file" : "";
                echo ("<center><h4>Cannot open".$extinfo.":<br><br>".$filename."</h4></center>\n");
                echo ('</div>'."\n");
            }

        }
    }

    //echo ('<div style="width:100%;height:50" />'."\n");

    echo ("</body></html>\n");

?>
