<?php

    error_reporting(0);

    function human_filesize($bytes, $decimals = 3) {
      $size = array('B','KB','MB','GB','TB','PB','EB','ZB','YB');
      $factor = floor((strlen($bytes) - 1) / 3);
      return sprintf("%.{$decimals}g", $bytes / pow(1024, $factor)) . @$size[$factor];
    }
    function assess_access($pwd, $accessroot) {
        if (empty($pwd) || empty($accessroot)) return true;
        if ($accessroot == '/') return true;
        if (strlen($pwd) == strlen($accessroot)){
            return $pwd==$accessroot? true : false;
        } else if (strlen($pwd) < strlen($accessroot)){
            return false;
        } else {
            if (substr($pwd, 0, strlen($accessroot)+1) == $accessroot."/") return true;
            else return false;
        }
    }

    include ("header.php");
    include ("page_head.php");

    $rootfolder = "/";
    $phpurl = "dir.php";
    if (empty($urlex)) $urlex = "";

  // get folder

    if (empty($url)){
        $keys = array( "d", "l", "p", "i", "path", "folder" );
        foreach($keys as $keyi){ $key = $keyi; $url = $_GET[$key]??""; if (!empty($url)) break; }
        if (empty($url)) $url = getenv('HOME');
    } else {
        $key = "folder";
    }
    if ($url == "/") $url = "";
    $key = "folder";

    $sortfile = ((int)($_GET["sort"]?? "3")-1)%6+1;  // 1/2: name; 3/4: type; 5/6: time (file modified time); positive: ascending, negative: descending
    $sortfile_key = array( "X", "N", "n", "Y", "y", "T", "t" );

    $filter_list = $_GET["c"]??"";
    $filters = empty($filter_list)? "" : explode(',', $filter_list);
    $nfilters = empty($filters)? 0 : count($filters);
//if (!empty($filters)){ echo ($nfilters." : "); print_r($filters); }
    if (!empty($filter_list)) $urlex = "&c=".$filter_list;

    $scroll = $_GET["scroll"]??"";

  // security control : only files in home folders will be shwon

    if (!assess_access($url, $accessroot)){
        $url = $accessroot;
    }

  // get file

    $keysf = array( "f", "file" );
    foreach($keysf as $keyfi){ $keyf = $keyfi; $urlf = $_GET[$keyf]??""; if (!empty($urlf)) break; }
    if ($urlf == "/") $urlf = "";
    $keyf = "file";

    if (!is_dir($url)){
        $urlf = basename($url);
        $url = dirname($url);
    }

  // scan folder

    $dir = $rootfolder.$url; $n_files = 0; $n_true_files = 0;

    if ($sortfile==3 || $sortfile==4){  // sort by type
        $files_scan_a = array();
        foreach (scandir($dir) as $file) {
            if (!empty($ignored) && in_array($file, $ignored)) continue;
            $files_scan_a[$file] = (is_dir($dir.'/'.$file)? "~" : ".".pathinfo($file,PATHINFO_EXTENSION)) . '/' . $file;
        }
        asort($files_scan_a);
        $files_scan = array_keys($files_scan_a);
    } else if ($sortfile==5 || $sortfile==6){   // sort by time
        $files_scan_a = array();
        foreach (scandir($dir) as $file) {
            if (!empty($ignored) && in_array($file, $ignored)) continue;
            $files_scan_a[$file] = filemtime($dir.'/'. $file) . '.' . $file;
        }
        arsort($files_scan_a);
        $files_scan = array_keys($files_scan_a);
    } else {    // sort by name
        $files_scan = scandir($dir);
    }

    //if (!empty($filters)){ echo ($nfilters." : "); print_r($filters); }

    if (count($files_scan)>0){
        for ($i=0; $i<count($files_scan); $i++){
            $files_attr[$i] = 0; // normal files
            if ($sortfile==1||$sortfile==2){
                if ($files_scan[$i]=="." || $files_scan[$i]=="..") $files_attr[$i] += 32; // hidden files
            } else {
                if (substr($files_scan[$i],0,1)==".") $files_attr[$i] += 32; // hidden files
            }
            if (is_dir($rootfolder.$url."/".$files_scan[$i])) $files_attr[$i] += 1; // folder
            if ($nfilters>0 && !($files_attr[$i]&1)){ $match = false;
                for ($j=0; $j<$nfilters; $j++) if (!empty($filters[$j])){
                    if (strpos($files_scan[$i], $filters[$j])) $match = true;;


                    /*
                    $preg_match_ret = preg_match('/('.$filters[$j].')/i', $files_scan[$i]);
                    //echo ('<small><small> filename '.$files_scan[$i].' filter '.$filters[$j].' : '.$preg_match_ret.'<br></small></small>');
                    if ($preg_match_ret==1) $match = true;
                    */
                }
                if (!$match) $files_attr[$i] += 64; // nonmatch files
            }
            if ($files_scan[$i]!="." && $files_scan[$i]!="..") $n_true_files++;
        }
        $j = 0;
        if ($sortfile%2==1){
            for ($i=0; $i<count($files_scan); $i++) if ($files_attr[$i]==1) { $files[$j] = $files_scan[$i]; $file_attrs[$j] = $files_attr[$i]; $j ++; }
            for ($i=0; $i<count($files_scan); $i++) if ($files_attr[$i]==0) { $files[$j] = $files_scan[$i]; $file_attrs[$j] = $files_attr[$i]; $j ++; }
        } else {
            for ($i=0; $i<count($files_scan); $i++) if ($files_attr[$i]==1 || $files_attr[$i]==0) { $files[$j] = $files_scan[$i]; $file_attrs[$j] = $files_attr[$i]; $j ++; }
        }
        $n_files = $j;
    }


    $parentname=(empty($url)?"/":$url);

  // script
    echo ('<script>
    function change_sort(url){
        window.location.replace(url+"&scroll="+window.scrollY+"&sort='.(($sortfile)%6+2).'");
    }
    function reload_page(url){
        window.location.replace(url+"&scroll="+window.scrollY+"&sort='.$sortfile.'");
    }
</script>'."\n");

  // display page

    echo ('<body'.(empty($scroll)?'':' onload="document.body.scrollTop='.$scroll.'"').'>'."\n");

    echo ('<div class="container"><div class="content" style="padding:6">'."\n");

  // display name of this folder or file

    //echo ('<div class="content">'."\n");

    echo ('<div class="navbd" style="border-style:solid;border-width:0.5px;width:3%" onclick="change_sort(\''.$phpurl.'?'.$key.'='.$url.$urlex.'\')"><small>'.$sortfile_key[$sortfile].'</small></div>'."\n");

    echo ('<div class="content" style="width:95%">'."\n");

    echo ('<div id="address" hidden>'.$url.(empty($urlf)?"":"/".$urlf).'</div>');

    echo ('<div class="container" style="width:100%;word-wrap:break-word">'."\n");
    $path_array = explode('/', $parentname); $current = "";
    //echo ('<a href="'.$phpurl.'?'.$key.'='."/".$urlex.'"><b>/</b></a>');
    echo ('<div class="linkunit" onClick=reload_page("'.$phpurl.'?'.$key.'='."/".$urlex.'")><b>/</b></div>');
    foreach($path_array as $sec) if (!empty($sec)){
        $current .= "/".$sec;
        //if (file_exists($rootfolder.$current)) echo ('<a href="'.$phpurl.'?'.$key.'='.$current.$urlex.'"><b>'.$sec.'/</b></a>');
        if (file_exists($rootfolder.$current)) echo ('<div class="linkunit" onClick=reload_page(\''.$phpurl.'?'.$key.'='.$current.$urlex.'\')><b>'.$sec.'/</b></div>');
        else echo ('<font color=#cccccc>/'.$sec.'</font>');
    }
    if (!empty($urlf)){
        //if (file_exists($parentname.$urlfile)) echo ('<a href="'.$phpurl.'?'.$key.'='.$parentname.'&'.$keyf.'='.$urlf.$urlex.'">'.$urlf.'</a>');
        if (file_exists($parentname.$urlfile)) echo ('<div class="linkunit" onClick=reload_page(\''.$phpurl.'?'.$key.'='.$parentname.'&'.$keyf.'='.$urlf.$urlex.'\')>'.$urlf.'</div>');
    }
    echo ('</div>'."\n");

    echo ('</div>'."\n");

    //echo ('</div>'."\n");

  // display name of all items in this folder

    echo ("<hr width=100%/>\n");

    echo ('<div id="list" name="list" style="word-wrap:break-word;width:100%">'."\n");

    $lastindicator = "-1";
    $currenttime = time()+$timezonesecond;

    if (file_exists($dir) && $n_files<1){
        if ($n_true_files<1){
            echo ('This folder is empty');
        } else {
            echo ('No item to display in this folder');
        }
    }

    if (file_exists($dir)) for ($i=0; $i<$n_files; $i++){
        $file = $url."/".$files[$i];
      // classified header
        if ($sortfile==3||$sortfile==4){
            $thisindicator = is_dir($rootfolder.$file)? "/" : pathinfo($rootfolder.$file,PATHINFO_EXTENSION);
            if (strcasecmp($thisindicator,$lastindicator)!=0){
                echo ('</div>');
                echo ('<div class="infoheader" style="width:100%;">'.($thisindicator=="/"?"Folder":(strtoupper($thisindicator))." file")."</div>\n");
                echo ('<div id="list" name="list" style="word-wrap:break-word;width:100%">'."\n");
            }
            $lastindicator = $thisindicator;
        } else if ($sortfile==5||$sortfile==6){
            $filetime = filemtime($rootfolder.$file) + $timezonesecond;
            if (date("Y-m-d", $currenttime) == date("Y-m-d", $filetime)) $thisindicator = "Today";
            else if (date("Y-m-d", $currenttime-86400) == date("Y-m-d", $filetime)) $thisindicator = "Yesterday";
            else if (date("Y-m", $currenttime) == date("Y-m", $filetime)) $thisindicator = "Early this month";
            else if (date("Y", $currenttime) == date("Y", $filetime)) $thisindicator = "Early this year";
            else if (date("Y", $currenttime)-1 == date("Y", $filetime)) $thisindicator = "Last year";
            else $thisindicator = "Before last year";
            if ($thisindicator != $lastindicator){
                echo ('</div>');
                echo ('<div class="infoheader" style="width:100%;"> '.$thisindicator." </div>\n");
                echo ('<div id="list" name="list" style="word-wrap:break-word;width:100%">'."\n");
                $lastindicator = $thisindicator;
            }
        } else if ($sortfile==1) {
            $thisindicator = $file_attrs[$i];
            if ($thisindicator != $lastindicator){
                echo ('</div>');
                echo ('<div class="infoheader" style="width:100%;">'.($thisindicator==1?"Folder":"File")."</div>\n");
                echo ('<div id="list" name="list" style="word-wrap:break-word;width:100%">'."\n");
                $lastindicator = $thisindicator;
            }
        }

      // file list
        if (is_dir($rootfolder.$file)){
            $filetime = filemtime($rootfolder.$file) + $timezonesecond;
            $filetime_string = date("Y-m-d, H:i", $filetime);
                if (date("Y-m-d", $currenttime) == date("Y-m-d", $filetime)) $filetime_string = date("H:i", $filetime);
                else if (date("Y-m-d", $currenttime-86400) == date("Y-m-d", $filetime)) $filetime_string = "Yest-".date("H:i", $filetime);
            echo('<div class="linkitem" onClick=reload_page(href="'.$phpurl.'?'.$key.'='.$file.$urlex.'")><b>'.$files[$i].(is_writable($file)?"":" &reg")."</b>");
            if ($dir_show_detail) echo ('<font color=#cccccc><small> ('.$filetime_string.')</small></font>');
            echo ('</div>');
            //echo('<a href="'.$phpurl.'?'.$key.'='.$file.$urlex.'"><div class="item"><b>'.$files[$i]."</b>");
            //echo ('<span class="detailfont"> ('.date ("Y-m-d,H:i", filemtime($rootfolder.$file)).')</span>');
            //echo ('</div></a>');
        } else {
            $filesize = human_filesize(filesize($rootfolder.$file));
            $filetime = filemtime($rootfolder.$file) + $timezonesecond;
            $filetime_string = date("Y-m-d, H:i", $filetime);
                if (date("Y-m-d", $currenttime) == date("Y-m-d", $filetime)) $filetime_string = date("H:i", $filetime);
                else if (date("Y-m-d", $currenttime-86400) == date("Y-m-d", $filetime)) $filetime_string = "Yest-".date("H:i", $filetime);
            //echo('<a href="'.$phpurl.'?'.$key.'='.$parentname.'&'.$keyf.'='.$files[$i].$urlex.'"><div class="item">'.$files[$i]);
            echo('<div '.($files[$i]==$urlf?'class="linkitema"':'class="linkitem"').' onClick=reload_page("'.$phpurl.'?'.$key.'='.$parentname.'&'.$keyf.'='.$files[$i].$urlex.'")>'.$files[$i].(is_writable($file)?"":" &reg"));
            if ($dir_show_detail) echo ('<font color=#cccccc><small> ('.$filesize.', '.$filetime_string.')</small></font>');
            echo ('</div>');
            //echo ('</div></a>');
        }
        echo ("\n");
    }

    echo ('</div>'."\n");

  // done

    echo ('</div></div>'."\n");

    echo ("</body></html>\n");

?>
