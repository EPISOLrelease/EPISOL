<?php

    $server = $_SERVER['HTTP_HOST'];

    echo ('<div class="container"><div class="footercontainer">');
    echo ('<font size=2>'.$software_name.' '.$software_version.(empty($server)?'':' on <a href="/" style="color:blue">'.$server.'</a> ').'(c) 2022 Siqin Cao</font>');
    echo ('</div></div>');
    echo ("</body></html>")


?>
