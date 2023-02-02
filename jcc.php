<?php

    function echo_freesolv_link($workspace, $title, $logfile, $grids, $closure, $ndiis, $delvv, $enlv, $errtol, $verbose){
        if ($closure == "plhnc")  $cf = 3; else $cf = "";
        echo('<a href="iet.php?workspace='.$workspace.'&solute='.getcwd().'/run/episol_demon/FreeSolv-0.51/solute&solvent='.getcwd().'/solvent/tip3p-amber14.01A.gaff&traj='.getcwd().'/run/episol_demon/FreeSolv-0.51/pdb&np=&rc=1&nr='.$grids.'&box=4x4x4&log='.$logfile.'&out=&verbo='.$verbose.'&debug=0&debugxc=0&closure='.$closure.'&cf='.$cf.'&steprism=3000&stephi=0&cr=0&temperature=298&save=0&report=111&display=table&ndiis='.$ndiis.'&delvv='.$delvv.'&errtol='.$errtol.'&enlv='.$enlv.'&coulomb=Coulomb&ignoreram=no" style="color:blue">'.$title.'</a>'."\n");
    }
    function echo_hi_link($workspace, $solute_file, $traj_file, $title, $outfile, $grids, $closure, $stephi, $rdfbins, $rdfgrps){
        if ($closure == "plhnc")  $cf = 3; else $cf = "";
        echo('<a href="iet.php?workspace='.$workspace.'&solute='.getcwd().'/run/episol_demon/solutes/'.$solute_file.'&solvent='.getcwd().'/solvent/tip3p-amber14.01A.gaff&traj='.getcwd().'/run/episol_demon/solutes/'.$traj_file.'&np=&rc=1&nr='.$grids.'&box=4x4x4&log='.$outfile.'.log&out='.$outfile.'&verbo=0&debug=0&debugxc=0&closure='.$closure.'&cf='.$cf.'&steprism=500&stephi='.$stephi.'&cr=0&temperature=298&save=1536&report=111&display=table&rdfbins='.$rdfbins.'&rdfgrps='.$rdfgrps.'&ndiis=5&delvv=1&errtol=1e-12&enlv=1&coulomb=Coulomb&ignoreram=no" style="color:blue">'.$title.'</a>'."\n");
    }
    function echo_3drism_link($workspace, $solute_file, $traj_file, $title, $outfile, $grids, $closure, $nt, $rism_steps, $rdfbins, $rdfgrps, $additional, $warning){
        if ($closure == "plhnc")  $cf = 3; else $cf = "";
        echo('<a '.(empty($warning)?'':'onclick="return confirm(\''.$warning.'\')"').' href="iet.php?workspace='.$workspace.'&np='.$nt.'&solute='.getcwd().'/run/episol_demon/solutes/'.$solute_file.'&solvent='.getcwd().'/solvent/tip3p-amber14.01A.gaff&traj='.getcwd().'/run/episol_demon/solutes/'.$traj_file.'&rc=1&nr='.$grids.'&log='.$outfile.'.log&out='.$outfile.'&verbo=2&debug=0&debugxc=0&closure='.$closure.'&cf='.$cf.'&steprism='.$rism_steps.'&stephi=0&cr=0&temperature=298&save=1538&report=111&display=table&rdfbins='.$rdfbins.'&rdfgrps='.$rdfgrps.'&ndiis=5&delvv=1&errtol=1e-6&enlv=1&coulomb=Coulomb&ignoreram=no'.(empty($additional)?"":'&additional='.$additional).'" style="color:blue">'.$title.'</a>'."\n");
    }

    echo ('<h4>&#9678 Generate the results in the JCC paper</h4>'."\n");
    echo ('<p>'."\n");

  # =============================================
  # Convergence test
  # =============================================

    $workspace = "episol_demon";
    $errtol = 1e-12;
    $closure_list = array("HNC", "PLHNC", "PSE3", "PSE2", "KH", "KGK");
    $delvv_list = array(1, 0.7, 0.3);

    echo("<p><b>3DRISM without DIIS:</b>\n");
    $ndiis=0; $delvv=1; $enlv=0; $verbose=2; $grids="40x40x40";
    foreach ($delvv_list as $delvv){
        echo("<p>static_mixing=".$delvv.": ");
        foreach ($closure_list as $closure){
            $logfile = $closure.".diis_".$ndiis.".s_".$delvv.".d_".$enlv.".log";
            $title =  $closure;
            echo_freesolv_link($workspace, $title, $logfile, $grids, $closure, $ndiis, $delvv, $enlv, $errtol, $verbose);
        }
    }

    echo("<p><b>3DRISM with DIIS:</b>\n");
    $ndiis=5; $delvv=1; $enlv=0; $verbose=2; $grids="40x40x40";
    foreach ($delvv_list as $delvv){
        echo("<p>static_mixing=".$delvv.": ");
        foreach ($closure_list as $closure){
            $logfile = $closure.".diis_".$ndiis.".s_".$delvv.".d_".$enlv.".log";
            $title =  $closure;
            echo_freesolv_link($workspace, $title, $logfile, $grids, $closure, $ndiis, $delvv, $enlv, $errtol, $verbose);
        }
    }

    echo("<p><b>3DRISM with DIIS and dynamic mixing factor:</b>\n");
    $ndiis=5; $delvv=1; $enlv=1; $verbose=2; $grids="40x40x40";
    foreach ($delvv_list as $delvv){
        echo("<p>static_mixing=".$delvv.": ");
        foreach ($closure_list as $closure){
            $logfile = $closure.".diis_".$ndiis.".s_".$delvv.".d_".$enlv.".log";
            $title =  $closure;
            echo_freesolv_link($workspace, $title, $logfile, $grids, $closure, $ndiis, $delvv, $enlv, $errtol, $verbose);
        }
    }

  # =============================================
  # Solvation free energy calculation
  # =============================================

    $workspace = "episol_demon";
    $errtol = 1e-12;
    $closure_list = array("PLHNC", "PSE3", "PSE2", "KH", "KGK");
    $delvv_list = array(1, 0.7, 0.3);

    echo("<p><b>Solvation free energy calculation of FreeSolv-0.51</b>\n");
    $ndiis=5; $delvv=1; $enlv=1; $verbose=0; $grids="100x100x100";
    echo("<p>performa calculations ");
    foreach ($closure_list as $closure){
        $logfile = '3drism_uc.'.$closure.".log";
        $title =  $closure;
        echo_freesolv_link($workspace, $title, $logfile, $grids, $closure, $ndiis, $delvv, $enlv, $errtol, $verbose);
    }

  # =============================================
  # 3DRISM-IDC for chaperone
  # =============================================

    echo("<p><b>3DRISM-IDC for huge biomolecules</b>\n");
    $closure = "PSE3";
    $rdfgrps = "58441-1=1,66276-1=1,74111-1=1,81946-1=1,89781-1=1,97616-1=1,105451-1=1,58831-1=2,66666-1=2,74501-1=2,82336-1=2,90171-1=2,98006-1=2,105841-1=2,58834-1=2,66669-1=2,74504-1=2,82339-1=2,90174-1=2,98009-1=2,105844-1=2,58266-1=3,66101-1=3,73936-1=3,81771-1=3,89606-1=3,97441-1=3,105276-1=3,58269-1=3,66104-1=3,73939-1=3,81774-1=3,89609-1=3,97444-1=3,105279-1=3,58244-1=4,66079-1=4,73914-1=4,81749-1=4,89584-1=4,97419-1=4,105254-1=4,55795-1=5,63630-1=5,71465-1=5,79300-1=5,87135-1=5,94970-1=5,102805-1=5,55796-1=5,63631-1=5,71466-1=5,79301-1=5,87136-1=5,94971-1=5,102806-1=5,55827-1=6,63662-1=6,71497-1=6,79332-1=6,87167-1=6,95002-1=6,102837-1=6,55512-1=7,63347-1=7,71182-1=7,79017-1=7,86852-1=7,94687-1=7,102522-1=7,54852-1=8,62687-1=8,70522-1=8,78357-1=8,86192-1=8,94027-1=8,101862-1=8,11630-1=9,19465-1=9,27300-1=9,35135-1=9,42970-1=9,50805-1=9,11631-1=9,19466-1=9,27301-1=9,35136-1=9,42971-1=9,50806-1=9,11171-1=10,19006-1=10,26841-1=10,34676-1=10,42511-1=10,50346-1=10,8785-1=11,16620-1=11,24455-1=11,32290-1=11,40125-1=11,47960-1=11,8786-1=11,16621-1=11,24456-1=11,32291-1=11,40126-1=11,47961-1=11,8817-1=12,16652-1=12,24487-1=12,32322-1=12,40157-1=12,47992-1=12,8502-1=13,16337-1=13,24172-1=13,32007-1=13,39842-1=13,47677-1=13,7842-1=14,15677-1=14,23512-1=14,31347-1=14,39183-1=14,47017-1=14";

    $title = "3DRISM-IDC-".$closure. " (218x164x164)";
    $outfile = "groel.3drism-idc-".$closure;
    $warning = "";
    echo("<p>Chaprone (GroEL): ");
    echo_3drism_link($workspace, "groel.idc.amber03.solute", "groel.fixed_grps.pdb", $title, $outfile, "218x164x164", $closure, 5, 1500, 50, $rdfgrps, "", $warning);

    $title = "3DRISM-IDC-".$closure." (436x330x326)";
    $outfile = "groel.better.3drism-idc-".$closure;
    $warning = "Calculation may take more than 8 hours, and require 30.5GB physical memory; output file can be 300~700MB. Sure to run?";
    echo("<p>Chaprone (GroEL): ");
    echo_3drism_link($workspace, "groel.idc.amber03.solute", "groel.fixed_grps.pdb", $title, $outfile, "436x330x326", $closure, 4, 1500, 50, $rdfgrps, "", $warning);

    #$title = "3DRISM-IDC-".$closure." (single thread)";
    #$warning = "This requires 22GB physical memory. Sure to run?";
    #echo("<p>Chaprone (GroEL): ");
    #echo_3drism_link($workspace, "groel.idc.amber03.solute", "groel.fixed_grps.pdb", $title, $outfile, "436x330x326", $closure, 1, 1500, 50, $rdfgrps, "", $warning);
    #echo(' <font style="color:red">(requires 22GB physical memory)</font>'."\n");

    #$title = "3DRISM-IDC-".$closure." (4 threads)";
    #$warning = "This requires 30.5GB physical memory. Sure to run?";
    #echo("<p>Chaprone (GroEL): ");
    #echo_3drism_link($workspace, "groel.idc.amber03.solute", "groel.fixed_grps.pdb", $title, $outfile, "436x330x326", $closure, 4, 1500, 50, $rdfgrps, "", $warning);
    #echo(' <font style="color:red">(requires 30.5GB physical memory)</font>'."\n");

  # =============================================
  # HI test for hydrophobic particles
  # =============================================

    $workspace = "episol_demon";
    $errtol = 1e-12;
    $closure_list = array("PSE3", "KH");

    echo("<p><b>3DRISM-HI for hydrophobic particles</b>\n");
    echo("<p>Modified-C60 without HI: ");
    foreach ($closure_list as $closure){
        $outfile = "C61.3drism-".$closure;
        $title = "3DRISM-".$closure;
        echo_hi_link($workspace, "C61.1.solute", "C61.pdb", $title, $outfile, "80x80x80", $closure, 0, 100, "1-1");
    }
    echo("<p>Modified-C60 with HI: ");
    foreach ($closure_list as $closure){
        $outfile = "C61.3drismhi-".$closure;
        $title = "3DRISM-HI-".$closure;
        echo_hi_link($workspace, "C61.1.solute", "C61.pdb", $title, $outfile, "80x80x80", $closure, 50, 100, "1-1");
    }

    echo("<p>Benzene without HI: ");
    foreach ($closure_list as $closure){
        $outfile = "benzene.3drism-".$closure;
        $title = "3DRISM-".$closure;
        echo_hi_link($workspace, "benzene.opls.solute", "benzene.gro", $title, $outfile, "80x80x80", $closure, 0, 100, "1-1=1,2-1=1,3-1=1,4-1=1,5-1=1,6-1=1");
    }
    echo("<p>Benzene with HI: ");
    foreach ($closure_list as $closure){
        $outfile = "benzene.3drismhi-".$closure;
        $title = "3DRISM-HI-".$closure;
        echo_hi_link($workspace, "benzene.opls.solute", "benzene.gro", $title, $outfile, "80x80x80", $closure, 50, 100, "1-1=1,2-1=1,3-1=1,4-1=1,5-1=1,6-1=1");
    }


?>
