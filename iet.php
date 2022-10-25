<?php

    $title = "iet";
    $phpurl = "iet.php";
    //$runurl = "/runiet.php";
    $workspace_root = getcwd()."/run";

    include ("ietsettings.php");

    $set = new IETSettings;

    include ("header.php");
    include ("page_head.php");

?>
<script>
    function set_text_from_iframe(id_text, id_iframe){
        var iframe = document.getElementById(id_iframe);
        var elmnt = iframe.contentWindow.document.getElementById("address");
        document.getElementById(id_text).value = elmnt.innerHTML;
    }
    function view_file_by_id(id_control){ window.open("viewfile.php?u="+document.getElementById(id_control).value); }
    function generate_run_parameters(work, workspace_substr=""){
        if (workspace_substr==""){
            workspace_substr = document.getElementById("workspace").value;
        }
        var buf = "?"; if (work!="") buf += "do="+work;
        if (workspace_substr!="") buf += (work==""?"" : "&") + "workspace=" + workspace_substr;
        var plist = [ "solute", "solvent", "traj", "np", "rc", "nr", "box", "log", "out", "verbo", "debug", "debugxc", "closure", "steprism", "stephi", "cr", "temperature", "save", "report", "display", "rdfbins", "rdfgrps", "fmt", "lsa", "xvvextend", "ndiis", "delvv", "errtol", "sd", "enlv", "coulomb", "coul_p", "ignoreram" ];
        for (i=0; i<plist.length; i++){
            var value = document.getElementById(plist[i]).value;
            if (value != "") buf += "&" + plist[i] + "=" + document.getElementById(plist[i]).value;
        }
        return buf;
    }
    function submit_background(work, workspace=""){
        buf = generate_run_parameters(work, workspace);
        <?php echo('window.location.replace("'.$phpurl.'"+buf);') ?>
    }
    function submit(work, hint){
        if (work=="" || hint=="" || confirm(hint)){
            submit_background(work);
        }
    }
    function show_run_log(){
        buf = generate_run_parameters("view_log");
        <?php echo('window.open("'.$phpurl.'"+buf);') ?>
    }
    function submit_run(work, hint){
        if (work=="" || hint=="" || confirm(hint)){
            submit_background(work);
        }
    }
    function checkbox_xor(cbox, ctrl, bitmask){
        state = document.getElementById(cbox).checked;
        if (state){
            document.getElementById(ctrl).value |= bitmask;
        } else {
            document.getElementById(ctrl).value &= ~bitmask;
        }
    }
    function checkbox_yesno(cbox, ctrl){
        state = document.getElementById(cbox).checked;
        if (state){
            document.getElementById(ctrl).value = "yes";
        } else {
            document.getElementById(ctrl).value = "no";
        }
    }
    function create_workspace(default_name){
        name = prompt("Pick a name for the new workspace:", default_name); if (name!=null && name!="null" && name!="") {
            submit_background("create_workspace", name);
        }
    }
    function refresh_to_workspace(){
        workspace_name = document.getElementById("workspace").value;
        submit_background("", workspace_name);
    }
</script>

<?php
  // load specified or default settings
    init_settings($set);
    $do = $_GET["do"]?? "";

  // do commands before loading the page

    //$settingfile = getcwd()."settings_iet.php";

    if (!file_exists(getcwd()."run")) shell_exec("mkdir ".getcwd()."/run");
    if (!file_exists(getcwd()."run/default")) shell_exec("mkdir ".getcwd()."/run/default");

    $settingfile = "";
    if (empty($set->workspace)){
        $settingfile = getcwd()."run/settings_iet.php";
    } else if (!empty($set->workspace) && is_dir(getcwd().'/run/'.$set->workspace)){
        $settingfile = getcwd().'/run/'.$set->workspace.'/.settings.php';
    }

    if (!empty($do)){
        if (strcasecmp($do, "save") == 0){          // save settings
            if (!empty($settingfile)) save_settings($set, $settingfile);
            if (!empty($settingfile) && file_exists($settingfile)){
                echo ('<script>window.onload = window.location.replace("'.($phpurl.generate_param_url($set)).'");</script>'."\n");
            } else {
                echo ('<div class="alert"><strong>Error</strong>: can\'t save settings'.(!empty($set->workspace)?(is_dir(getcwd().'/run/'.$set->workspace)?' to workspace ':' to non-existing workspace ').$set->workspace:'').' <span class="closebtn" onclick="this.parentElement.style.display=\'none\';">&times;</span></div>');
            }
        } else if (strcasecmp($do, "load") == 0){   // load settings
            if (!empty($settingfile) && file_exists($settingfile)){
                include ($settingfile);
                echo ('<script>window.onload = window.location.replace("'.($phpurl.generate_param_url($set)).'");</script>'."\n");
            } else {
                echo ('<div class="alert"><strong>Error</strong>: can\'t load settings as it hasn\'t been saved before <span class="closebtn" onclick="this.parentElement.style.display=\'none\';">&times;</span></div>');
            }
        } else if (strcasecmp($do, "calc") == 0){   // do calculation
            if (!empty($set->workspace) && !is_dir(getcwd().'/run/'.$set->workspace)){
                echo ('<div class="alert"><strong>Error</strong>: can\'t do calculations : please create workspace '.$set->workspace.' first <span class="closebtn" onclick="this.parentElement.style.display=\'none\';">&times;</span></div>');
            } else {
                if (!empty($settingfile)) save_settings($set, $settingfile);

                $filename = basename($set->solute);
                $ext = is_dir($set->solute)? "" : pathinfo($filename,PATHINFO_EXTENSION);
                if (!is_dir($set->solute) && strcasecmp($ext, "top")==0){
                  // solute is a top file: convert it to solute first
                    $fn_noext = substr($filename, 0, strlen($filename) - strlen($ext) - (substr($filename,strlen($filename)-strlen($ext)-1,1)=='.'?1:0));
                    $topfile = $set->solute;
                    $solutefile = getcwd().'solute/'.$fn_noext.'.solute';
                    $set->solute = $solutefile;
                    $exec = $gmxtop2solute.' -ab -top '.$topfile.' -o '.$solutefile.';';
                    $exec .= generate_calc_command($set, $rismhi3d);
                    //echo ('<p>command: '.$exec." >> ".$run_stdout.'</p><br>');
                } else {
                    $exec = generate_calc_command($set, $rismhi3d);
                    //echo ('<p>command: '.$exec." >> ".$run_stdout.'</p><br>');
                }
                shell_exec("echo \# ".$software_name.": ".$exec." > ".$run_stdout);
                shell_exec("nohup ".$exec." >> ".$run_stdout." 2>&1 &");
                echo ('<script>window.onload = window.location.replace("'.($phpurl.generate_param_url($set)).'");</script>'."\n");

                /*
                $exec = generate_calc_command($set, $rismhi3d);
                shell_exec("echo \# ".software_name..": ".$exec." > ".$run_stdout);
                shell_exec("nohup ".$exec." >> ".$run_stdout." 2>&1 &");
                echo ('<script>window.onload = window.location.replace("'.($phpurl.generate_param_url($set)).'");</script>'."\n");
                */
            }
        } else if (strcasecmp($do, "term") == 0){   // terminate all calculations
            shell_exec("killall -9 eprism3d");
            echo ('<script>window.onload = window.location.replace("'.($phpurl.generate_param_url($set)).'");</script>'."\n");
        } else if (strcasecmp($do, "create_workspace") == 0){   // terminate all calculations
            if (!file_exists($workspace_root.'/'.$set->workspace)) shell_exec('mkdir '.$workspace_root.'/'.$set->workspace);
            echo ('<script>window.onload = window.location.replace("'.($phpurl.generate_param_url($set)).'");</script>'."\n");
        } else if (strcasecmp($do, "view_log") == 0){
            $logfile = empty($set->log)? "stdout.txt" : $set->log;
            if (!empty($set->workspace)) $logfile = $set->workspace.'/'.$logfile;
            echo ('<script>window.onload = window.location.replace("'.('viewfile.php?scroll=bottom&u='.getcwd().'/run/'.$logfile).'");</script>'."\n");
        }
    }



  // print the html body

    include ("page_header_element.php");

    echo ('<div class="contentcontainer"><div class="container">'."\n");
    echo ('<center>'."\n");

    echo ('<br>Workspace ');
    $first_workspace_name = "";
    echo ('<select id="workspace" name="workspace" style="min-width:300;font-size:18;" onChange="refresh_to_workspace(\'wname\',\'workspace\')">');
        $workspace_count = 0;
        $files_scan_a = array();
        foreach (scandir($workspace_root) as $file) {
            $files_scan_a[$file] = filemtime($workspace_root.'/'. $file);
        }
        arsort($files_scan_a);
        foreach (array_keys($files_scan_a) as $file) {
            if (substr($file,0,1)==".") {
            } else if (is_dir($workspace_root.'/'.$file)){
                echo ('  <option value="'.$file.'"'.(($file==$set->workspace||empty($first_workspace_name))?" selected":"").'> '.$file.(file_exists($workspace_root.'/'. $file.'/.settings.php')?' *':'').' </option>'); $workspace_count ++;
                if (empty($first_workspace_name)) $first_workspace_name = $file;
            }
        }
        if ($workspace_count<1) echo ('  <option value="" disabled> please create a workspace </option>');
    echo ('</select>'."\n");

    $default_workspace_name = 'new_'.date('Ymd_Hi', time()+$timezonesecond);
    echo ('<input type="submit" id="newworkspace" name="newworkspace" value="create new" onclick=create_workspace("'.$default_workspace_name.'") />');

    echo ('<br>'."\n");
    //document.getElementsByName('stuff')[0].options[0].innerHTML = "Water";

    echo ('<br>'."\n");

    echo ('</center>'."\n");
    echo ('</div></div>'."\n");
    echo ('<div class="contentcontainer" id="main" name="main"><div class="container">'."\n");
    echo ('<center>'."\n");

  // -p, -s and -f picker

    echo ('<div class="container">'."\n");
      echo ('<table style="width:100%;table-layout:fixed">'."\n");
      echo ('<tr>'."\n");
        echo ('<th style="width:33%"><center><a href="help.php#iet_solvent">Solvent</a> <input type="submit" value="view" onclick="view_file_by_id(\'solvent\')"/>   </th>'."\n");
        echo ('<th style="width:33%">');
        echo ('<center><a href="help.php#iet_solute">Solute</a> <input type="submit" value="view" onclick="view_file_by_id(\'solute\')"/>');
        echo (' <input type="submit" value="top2solute" onclick="window.open(\'gmxtop2solute.php?top=\'+document.getElementById(\'solute\').value)" />   ');
        echo ('</th>'."\n");
        echo ('<th style="width:33%"><center><a href="help.php#iet_traj">Conformation or trajecory</a> <input type="submit" value="view" onclick="view_file_by_id(\'traj\')"/>   </th>'."\n");
      echo ('</tr>'."\n");
      echo ('<tr><td>'."\n");
        echo ('<input type="text" id="solvent" name="solvent" value="" style="width:100%" readonly />');
        echo ('</td><td>'."\n");
        echo ('<input type="text" id="solute" name="solute" value="" style="width:100%" readonly />');
        echo ('</td><td>'."\n");
        echo ('<input type="text" id="traj" name="traj" value="" style="width:100%" readonly />');
      echo ('</td></tr>'."\n");
      echo ('<tr><td>'."\n");
        echo ('<iframe name="solvent_list" id="solvent_list" src="dir.php?path='.(!empty($set->solvent)?$set->solvent:getcwd().'/solvent').'" width=100% frameborder=0 onLoad="set_text_from_iframe(\'solvent\', \'solvent_list\')"></iframe>'."\n");
        echo ('</td><td>'."\n");
        echo ('<iframe name="solute_list" id="solute_list" src="dir.php?c=.solute,.top,.prmtop&path='.(!empty($set->solute)?$set->solute:getcwd().'/solute').'" width=100% frameborder=0 onLoad="set_text_from_iframe(\'solute\', \'solute_list\')"></iframe>'."\n");
        echo ('</td><td>'."\n");
        echo ('<iframe name="traj_list" id="traj_list" src="dir.php?c=.pdb,.gro,.xtc&path='.(!empty($set->traj)?$set->traj:getcwd().'/solute').'" width=100% frameborder=0 onLoad="set_text_from_iframe(\'traj\', \'traj_list\')"></iframe>'."\n");
      echo ('</td></tr>'."\n");
      echo ("</table>\n");
    echo ('</div>'."\n");

    echo ("<hr width=95%/>\n");

  // basic options

    echo ('<center>');
    echo ('<div class="itembox"><table>'."\n");
        echo ('<tr><td><a href="help.php#iet_threads">threads</a></td><td> <input type="text" id="np" name="np" value="'.$set->np.'" placeholder="-nt" style="width:40"/> threads </td></tr>'."\n");
        //echo ('<tr><td><a href="help.php#iet_threads">threads</a></td><td> <input type="text" id="np" name="np" value="'.$set->np.'" placeholder="-nt" style="width:40"/> threads, <input type="text" id="ntb" name="ntb" value="'.$set->ntb.'" placeholder="1" style="width:40"/> <a href="help.php#iet_batch">batch</a></td></tr>'."\n");
        echo ('<tr><td><a href="help.php#iet_grids">grid number</a></td><td> <input type="text" id="nr" name="nr" value="'.$set->nr.'" /></td></tr>'."\n");
        echo ('<tr><td><a href="help.php#iet_box">Default box</a></td><td> <input type="text" id="box" name="box" value="'.$set->box.'" /></td></tr>'."\n");
        echo ('<tr><td><a href="help.php#iet_rc">r cutoff</a></td><td> <input type="text" id="rc" name="rc" value="'.$set->rc.'" style="width:50"/> nm</td></tr>'."\n");
        echo ('<tr><td>temperature</td><td> <input type="text" id="temperature" name="temperature" value="'.$set->temperature.'" style="width:50"/> K</td></tr>'."\n");
        echo ('<tr><td>Log file</td><td> <input type="text" id="log" name="log" value="'.$set->log.'" placeholder="stdout.txt" /></td></tr>'."\n");
        echo ('<tr><td>Output file</td><td> <input type="text" id="out" name="out" value="'.$set->out.'" placeholder="autogenerated" /></td></tr>'."\n");
        echo ('<tr><td>Perform RISM</td><td>');
          echo ('<select id="closure">');
          foreach($closures as $val) echo ('  <option value="'.$val.'" '.($set->closure==$val?"selected=\"selected\"":"").'> '.$val.' </option>');
          echo ('</select>'."\n");
          echo ('<input type="text" id="steprism" name="steprism" value="'.$set->steprism.'" style="width:40" /> steps'."\n");
        echo ("</td></tr>\n");
        echo ('<tr><td>HI</td><td>');
          echo ('<input type="text" id="stephi" name="stephi" value="'.$set->stephi.'" style="width:40" /> steps'."\n");
        echo ("</td></tr>\n");
        echo ('<tr><td><a href="help.php#cavity_removal">Cavity</a></td><td>');
          //echo ('<input type="text" id="cr" name="cr" value='.$set->cr.' hidden />'."\n");
          echo ('<input type="text" id="cr" name="cr" value='.$set->cr.' readonly hidden/>'."\n");
          echo ('<input type="checkbox" name="crcheck" id="crcheck" '.($set->cr&1?"checked":"").' onClick="checkbox_xor(\'crcheck\',\'cr\',1)"> remove unrealistic solvents</input>');
        echo ("</td></tr>\n");
    echo ('</table></div>'."\n");

  // output

    echo ('<div class="itembox"><table>'."\n");
        echo ('<tr><td>save</td><td>'); echo ('<input type="text" id="save" name="save" value='.$set->save.' hidden />'."\n");
          echo ('<input type="checkbox" name="save_cmd" id="save_cmd" '.($set->save&1?"checked":"").' onClick="checkbox_xor(\'save_cmd\',\'save\',1)">command </input>');
          echo ('<input type="checkbox" name="save_LJ" id="save_LJ" '.($set->save&64?"checked":"").' onClick="checkbox_xor(\'save_LJ\',\'save\',64)">LJ </input>');
          echo ('<input type="checkbox" name="save_Coul" id="save_Coul" '.($set->save&128?"checked":"").' onClick="checkbox_xor(\'save_Coul\',\'save\',128)">Coul </input>');
          //echo ('<input type="checkbox" name="save_Ef" id="save_Ef" '.($set->save&256?"checked":"").' onClick="checkbox_xor(\'save_Ef\',\'save\',256)">Electric_Field </input>');
          //echo ('<br>');
          echo ('<br>');

          echo ('<input type="checkbox" name="save_guv" id="save_guv" '.($set->save&2?"checked":"").' onClick="checkbox_xor(\'save_guv\',\'save\',2)">guv </input>');
          echo ('<input type="checkbox" name="save_huv" id="save_huv" '.($set->save&4?"checked":"").' onClick="checkbox_xor(\'save_huv\',\'save\',4)">huv </input>');
          echo ('<input type="checkbox" name="save_cuv" id="save_cuv" '.($set->save&8?"checked":"").' onClick="checkbox_xor(\'save_cuv\',\'save\',8)">cuv </input>');
          //echo ('<input type="checkbox" name="save_csr" id="save_csr" '.($set->save&16?"checked":"").' onClick="checkbox_xor(\'save_csr\',\'save\',16)">cuv_SR </input>');
          echo ('<input type="checkbox" name="save_rdf" id="save_rdf" '.($set->save&512?"checked":"").' onClick="checkbox_xor(\'save_rdf\',\'save\',512)">rdf </input>');
          echo ('<br>');
          //echo ('<input type="checkbox" name="save_dd" id="save_dd" '.($set->save&32?"checked":"").' onClick="checkbox_xor(\'save_dd\',\'save\',32)">density_inhomogeneity </input>');
          //echo ('<br>');
        echo ("</td></tr>\n");
        echo ('<tr><td>save options</td><td>');
          echo ('<input type="checkbox" name="overwrite" id="overwrite" '.($set->save&1024?"checked":"").' onClick="checkbox_xor(\'overwrite\',\'save\',1024)">overwrite </input>');
          echo ('<input type="checkbox" name="compress" id="compress" '.($set->save&2048?"checked":"").' onClick="checkbox_xor(\'compress\',\'save\',2048)">compress </input>');
        echo ("</td></tr>\n");
        echo ('<tr><td>report</td><td>'); echo ('<input type="text" id="report" name="report" value='.$set->report.' hidden />'."\n");
          echo ('<input type="checkbox" name="report_N" id="report_N" '.($set->report&1?"checked":"").' onClick="checkbox_xor(\'report_N\',\'report\',1)">Volume </input>');
          //echo ('<input type="checkbox" name="report_TS" id="report_TS" '.($set->report&2?"checked":"").' onClick="checkbox_xor(\'report_TS\',\'report\',2)">entropy </input>');
          echo ('<input type="checkbox" name="report_LJ" id="report_LJ" '.($set->report&4?"checked":"").' onClick="checkbox_xor(\'report_LJ\',\'report\',4)">LJ </input>');
          echo ('<input type="checkbox" name="report_Coul" id="report_Coul" '.($set->report&8?"checked":"").' onClick="checkbox_xor(\'report_Coul\',\'report\',8)">Coulomb </input>');
          echo ('<br>');
          //echo ('<input type="checkbox" name="report_Ef" id="report_Ef" '.($set->report&16?"checked":"").' onClick="checkbox_xor(\'report_Ef\',\'report\',16)">EF_Energy </input>');
          //echo ('<br>');
          echo ('<input type="checkbox" name="report_ex" id="report_ex" '.($set->report&32?"checked":"").' onClick="checkbox_xor(\'report_ex\',\'report\',32)">excess </input>');
          echo ('<input type="checkbox" name="report_GF" id="report_GF" '.($set->report&64?"checked":"").' onClick="checkbox_xor(\'report_GF\',\'report\',64)">excess_GF </input>');
          //echo ('<br>');
          //echo ('<input type="checkbox" name="report_cuv" id="report_cuv" '.($set->report&128?"checked":"").' onClick="checkbox_xor(\'report_cuv\',\'report\',128)">cuv </input>');
          echo ('<input type="checkbox" name="report_rdf" id="report_rdf" '.($set->report&256?"checked":"").' onClick="checkbox_xor(\'report_rdf\',\'report\',256)">rdf </input>');
        echo ("</td></tr>\n");
        echo ('<tr><td>report format</td><td>');
          echo ('<select id="display">');
          echo ('  <option value="table" '.($set->display=="table"?"selected=\"selected\"":"").'> Table </option>');
          echo ('  <option value="list" '.($set->display=="list"?"selected=\"selected\"":"").'> List </option>');
          echo ('</select><br>'."\n");
           //echo ('<input type="checkbox" name="report" id="report" '.($set->report!=0?"checked":"").'>table </input>');
           //echo ('<input type="checkbox" name="display" id="display" '.($set->display!=0?"checked":"").'>list </input>');
        echo ("</td></tr>\n");
        echo ('<tr><td><a href="help.php#iet_rdf_bins">rdf-bins</a></td><td> <input type="text" id="rdfbins" name="rdfbins" style="width:50" value="'.$set->rdfbins.'" /> <small>(number of RDF bins)</small></td></tr>'."\n");
        echo ('<tr><td><a href="help.php#iet_rdf_grps">rdf-grps</a></td><td> <input type="textarea" id="rdfgrps" name="rdfgrps" value="'.$set->rdfgrps.'" style="width:200" placeholder="#atom1-#site1,#atom2-#site2,..."/></td></tr>'."\n");
        echo ('<tr><td>Verbose mode</td><td>');
          echo ('<select id="verbo">');
          echo ('  <option value=0 '.($set->verbo==0?"selected=\"selected\"":"").'> level 0 (silent) </option>');
          echo ('  <option value=1 '.($set->verbo==1?"selected=\"selected\"":"").'> level 1 (default) </option>');
          echo ('  <option value=2 '.($set->verbo==2?"selected=\"selected\"":"").'> level 2 (detailed)</option>');
          echo ('</select>'."\n");
        echo ("</td></tr>\n");
        echo ('<tr><td><a href="help.php#iet_debug_level">debug level</a></td><td>');
          echo ('<select id="debug">');
          echo ('  <option value=0 '.($set->debug%16==0?"selected=\"selected\"":"").'> 0 </option>');
          echo ('  <option value=1 '.($set->debug%16==1?"selected=\"selected\"":"").'> 1 </option>');
          echo ('  <option value=2 '.($set->debug%16==2?"selected=\"selected\"":"").'> 2 </option>');
          echo ('  <option value=3 '.($set->debug%16==3?"selected=\"selected\"":"").'> 3 </option>');
          echo ('</select>'."\n");
          echo ('<input type="text" id="debugxc" name="debugxc" value='.$set->debugxc.' hidden />'."\n");
          echo ('<input type="checkbox" name="debugx" id="debugx" '.($set->debugxc&16?"checked":"").' onClick="checkbox_xor(\'debugx\',\'debugxc\',16)"> xvv </input>');
          echo ('<input type="checkbox" name="debugc" id="debugc" '.($set->debugxc&32?"checked":"").' onClick="checkbox_xor(\'debugc\',\'debugxc\',32)"> crc </input>');
        echo ("</td></tr>\n");
    echo ('</table></div>'."\n");

  // advanced options

    echo ('<div class="itembox"><center><b>Advanced Options</b><br></center><table>'."\n");
        echo ('<tr><td><a href="help.php#iet_lsa">A of LES</a></td><td> <input type="text" id="lsa" name="lsa" value="'.$set->lsa.'" style="width:60"/></td>');
        echo ('<tr><td><a href="help.php#iet_Coulomb">Electric interaction</a></td><td>');
          echo ('<select id="coulomb">');
          echo ('  <option value="Coulomb" '.(strcasecmp($set->coulomb,"Coulomb")==0?"selected=\"selected\"":"").'> Coulomb </option>');
          echo ('  <option value="Dielect" '.(strcasecmp($set->coulomb,"Dielect")==0?"selected=\"selected\"":"").'> Dielect </option>');
          echo ('  <option value="YukawaFFT" '.(strcasecmp($set->coulomb,"YukawaFFT")==0?"selected=\"selected\"":"").'> Yukawa </option>');
          echo ('</select>'."\n");
          echo ('<input type="text" id="coul_p" name="coul_p" value="'.$set->coul_p.'" style="width:40" placeholder="param"/>'."\n");
        echo ("</td><td></td></tr>\n");
        echo ('<tr><td><a href="help.php#iet_data_format">Data format</a></td><td> <input type="text" id="fmt" name="fmt" value="'.$set->fmt.'" style="width:60" /></td><td></td></tr>'."\n");
        echo ('<tr><td><a href="help.php#iet_extend_xvv">Extend xvv</a></td><td> <input type="text" id="xvvextend" name="xvvextend" value="'.$set->xvvextend.'" style="width:60" /></td>');
        echo ('<tr><td><a href="help.php#iet_extend_xvv">DIIS depth</a></td><td> <input type="text" id="ndiis" name="ndiis" value="'.$set->ndiis.'" style="width:60" /></td><td></td></tr>'."\n");
        echo ('<tr><td><a href="help.php#iet_delvv">SCF stepin factor</a></td><td> <input type="text" id="delvv" name="delvv" value="'.$set->delvv.'" style="width:60" /></td><td></td></tr>'."\n");
        echo ('<tr><td><a href="help.php#iet_dynamic_delvv">Dynamic stepin factor</a></td><td> <input type="text" id="enlv" name="enlv" value="'.$set->enlv.'" style="width:60" /></td></tr>'."\n");
        echo ('<tr><td><a href="help.php#iet_errtol">Error tolerance</a> </td><td> <input type="text" id="errtol" name="errtol" value="'.$set->errtol.'" style="width:120" /></td></tr>'."\n");
        echo ('<tr><td><a href="help.php#iet_sigdig">Significant digits</a> </td><td> <input type="text" id="sd" name="sd" value="'.$set->sd.'" style="width:60" /></td></tr>'."\n");
        echo ('<tr><td><a href="help.php#iet_igram">Physical memory</a></td><td>'); echo ('<input type="text" id="ignoreram" name="ignoreram" value='.$set->ignoreram.' hidden />'."\n");
          echo ('<input type="checkbox" name="ignoreramc" id="ignoreramc" '.($set->ignoreram=="yes"?"checked":"").' onClick="checkbox_yesno(\'ignoreramc\',\'ignoreram\')"> <font color=red> allow to exceed</font> </input>');
        echo ("</tr>\n");
    echo ('</table></div>'."\n");

    echo ('</center>'."\n");
    echo ('</div>');

  // perform calculation

    echo ('<div class="container">'."\n");
    echo ('<center>'."\n");
    echo ("<hr width=95%/>\n");

    $n_running_eprism3d = shell_exec("pgrep -u `whoami` eprism3d | wc -l");

    echo ("<div class=\"container\">\n");
        echo ('<input type="submit" value="load saved settings" onclick="submit(\'load\', \'Do you want to load saved settings?\')" />'."\n");
        /*if (!empty($settingfile) && file_exists($settingfile)){
            echo ('<input type="submit" value="load saved settings" onclick="submit(\'load\', \'Do you want to load saved settings?\')" />'."\n");
        } else {
            echo ('<input type="submit" value="load saved settings" onclick="submit(\'load\', \'Do you want to load saved settings?\')" disabled />'."\n");
        }*/
        echo ('<input type="submit" value="save settings" onclick="submit(\'save\', \'Do you want to save current settings?\')" '.((empty($settingfile))?"disabled":"").'/>'."\n");
        echo ('<input type="submit" value="perform calculation" onclick="submit_run(\'calc\', \'Do you want to perform calculation?\n\nSettings will be saved as well.\')" '.($n_running_eprism3d>0?'disabled':'').'/>'."\n");
        echo ('<input type="submit" value="view screen output" onclick=show_run_log() />'."\n");
        echo ('<input type="submit" '.($n_running_eprism3d>0?'style="color:red"':'').' value="terminate all calculations" onclick="submit(\'term\', \'Do you want to terminate all calculations?\')" '.($n_running_eprism3d>0?'':'disabled').'/>'."\n");
        echo ('<input type="submit" id="safeload" name="safeload" class="safeload" value="safe refresh" onclick="submit(\'\', \'\')" />'."\n");
    echo ("</div>\n");

    echo ("<hr width=95%/>\n");

    echo ('</center>'."\n");
    echo ('</div></div>'."\n");

    include ("page_footer.php");

?>
