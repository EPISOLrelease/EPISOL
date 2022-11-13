<?php
    $title = "analysis";
    $phpurl = "analysis.php";

    $url = $_POST["filename"]?? "";
    if (empty($url)) $url = $_GET["filename"]?? getcwd().'/run';

    include ("header.php");
    include ("page_head.php");
    echo ('<script>
    function set_text_from_iframe(id_text, id_iframe){
        var iframe = document.getElementById(id_iframe);
        var elmnt = iframe.contentWindow.document.getElementById("address");
        document.getElementById(id_text).value = elmnt.innerHTML;
    }
    function view_file_from_iframe(id_text, id_iframe, urlex=""){
        set_text_from_iframe(id_text, id_iframe);
        view_file_by_id("filename", urlex);
    }
    function view_file_by_id(id_control, urlex=""){
        document.getElementById("filetext").src = "viewfile.php?u="+document.getElementById(id_control).value+urlex+"&analysis=on";
    }
</script>'."\n");
    include ("page_header_element.php");

    echo ('<div class="contentcontainer"><div class="container">'."\n");

    echo ('<br><center>'."\n");

    echo ('<div class="itembox" style="width:480;height:480">'."\n");
      echo ('<div class="container" style="width:480;height:30">'."\n");
        //echo ('<div class="navbc" id="viewbutton" onclick="view_file_by_id(\'filename\',\'&rebuild=on\')"><small>view</small></div>'."\n");
        //echo ('<form action="'.$phpurl.'" method="post"><input type="text" id="filename" name="filename" value="" style="width:430" /></form>');
        echo ('<form action="'.$phpurl.'" method="post"><input type="text" id="filename" name="filename" value="" style="width:100%" /></form>');
      echo ('</div>'."\n");



      //echo (' <div type="navbd" style="width:40" onclick="view_file_by_id(\'filename\',\'&rebuild=on\')">"-->"</div>');
      echo ('<div class="container" style="border-style:solid;border-color:#CCCCCC;border-width:1px;width:480;height:450">'."\n");
        echo ('<iframe name="filelist" id="filelist" src="dir.php?path='.$url.'" width=100% height=100% frameborder=0 onLoad="view_file_from_iframe(\'filename\', \'filelist\')"></iframe>'."\n");
      echo ('</div>'."\n");
    echo ('</div>'."\n");
    echo ('<div class="itembox" style="min-width:480px;width:calc(100% - 530px);height:480">'."\n");
        echo ('<iframe name="filetext" id="filetext" src="viewfile.php" width=100% height=100% frameborder=0"></iframe>'."\n");
    echo ('</div>'."\n");



    echo ('</center>');

    echo ('</div><div><br>');

    include ("page_footer.php");

?>
