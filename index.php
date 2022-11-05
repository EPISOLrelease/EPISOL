<?php
    $title = "Expanded Package for IET-based Solvation";

    include ("ietsettings.php");

    include ("header.php");
    include ("page_head.php");
    echo ('<style> p { text-indent: 1em; } </style>'."\n");
    include ("page_header_element.php");

    echo ('<div class="container" style="margin:5 auto;">'."\n");

    echo ('<center>'."\n");

    echo ('<br><center><div class="container"><h3>EPISol: the Expanded Package for IET-based Solvation</h3></div></center>'."\n");

    if (empty($kernel_version)){
        echo('<br>Kernel not installed. <a href="install.php" style="color:red"><b>Please install the kernel of '.$software_name.'</b></a>'."\n");
    } else {
        echo('<br>'.$software_name.' '.$software_version.' (kernel '.$kernel_version.")\n");
    }

    echo ('<div class="container" style="width:90%;min-width:400;text-align:left;padding:3;">'."\n");

    echo ('<h4>&#9678 What is '.$software_name.'?</h4>
  <p>'.$software_name.' is the graphic interface to do IET (integration equation theory of liquid) calculations. '.$software_name.' can produce site-based (i.e. atom-based) thermodynamical distributions of simple liquids around a given conformation of solute molecule; and HI can calculate molecule-based liquid density depletion of polar solvents or density segregation of solvent mixtures at solid-liquid interfaces.</p>
  <p> '.$software_name.' currently contains the implementations of <a style="color:blue" href="http://doi.org/10.1021/cr5000283">3D Reference Interaction Site Model</a> (3DRISM), <a style="color:blue" href="http://doi.org/10.1063/1.4928051">Hydrophobicity induced density Inhomogeneity theory</a> (HI), <a style="color:blue" href="https://doi.org/10.1021/acs.jpcb.2c04431">Ion-Dipole Correction</a> (IDC). EPISol-1.1.321 can be seen in <a style="color:blue" href="">JCC ...</a>.
'."\n");
    echo ('<p><b>Security concern</b>: please <a href="help.php#about_security" style="color:red"><b>click me</b></a> to see the security concerns to have a copy of EPISol on your server.'."\n");

// A step by step guide

    $gmx_source_lib = getenv('GMXDATA');
    echo ('<h4>&#9678 A step by step guide</h4>'."\n");
    if (empty($kernel_version)) echo('<p>First of all, install the kernel. Please nevigate to the <a href="install.php"><u style="color:#0000A0">install page</u></a>, press <input type="submit" value="install fftw3" /> to install FFTW3, and then press <input type="submit" value="install kernel" /> to install the kernel. If installed successfully, the <a href="install.php"><u style="color:#0000A0">install page</u></a> will show the versions of FFTW and kernel instead of "FFTW3 not installed" or "Please install kernel".'."\n");
    echo ('<p>1. Before running the IET calculations, three input files need to be prepared: the solvent file, the solute file, and the conformation/trajectory file. These files are specified in the three file explorers in the <a href="iet.php"><u style="color:#0000A0">IET page</u></a>.
<p>Several solvent files have been generated in the <a href="analysis.php?filename='.getcwd().'/solvent"><u style="color:#0000A0">solvent</u></a> folder. The conformation or trajectory can be a PDF/GRO/XTC file.  A multiframe PDB/GRO/XTC is recognized as a trajectory.
<p>The solute file: If you have <a href="viewfile.php?u='.getcwd().'/solute/methane.prmtop"><u style="color:#0000A0">AMBER\'s PRMTOP</u></a> then you can skip this step. If you want to use <a href="viewfile.php?u='.getcwd().'/solute/methane.top"><u style="color:#0000A0">GROMACS\'s TOP</u></a>, you need to use <a href="gmxtop2solute.php?top='.getcwd().'/solute/methane.top&sname=methane"><u style="color:#0000A0">gmxtop2solute</u></a> to translate it into <a href="viewfile.php?u='.getcwd().'/solute/methane.solute"><u style="color:#0000A0">the solute format</u></a>. ');
if (empty($gmx_source_lib)){
    echo ('When doing gmxtop2solute, normally you will need the GROMACS library defined by environmental string $GMXDATA before starting the '.$software_name.' server. This is normally set by sourcing GROMACS\'s GMXRC.');
} else {
    echo ('($GMXDATA already set to '.$gmx_source_lib.')');
};
    echo ('
<p>Note: gmxtop2solute will generate two solute files: the original solute file (directly translated from TOP file), and the IDC corrected solute fil (generated from original solute file). This is the only way to generate IDC related parameters. Therefore, you need to use the solute format if you want to do IDC.
<p>2. Run the IET calculations. You can either <a href="iet.php?workspace=default&solute='.getcwd().'/solute/methane.prmtop&solvent='.getcwd().'/solvent/tip3p-amber14.01A.gaff&traj='.getcwd().'/solute/methane.pdb&rc=1.2&nr=60x60x60&log=tutorial_methane&out=tutorial_methane&verbo=2&debug=0&debugxc=16&closure=KH&steprism=500&stephi=0&temperature=298&save=515&report=45&display=table&rdfbins=60&rdfgrps=1-1,1-2&fmt=14.7g&lsa=0.3&xvvextend=0&ndiis=5&delvv=1&errtol=0.0000001&sd=5&enlv=1&coulomb=Coulomb&ignoreram=no"><u style="color:#0000A0">run with PRMTOP</u></a> or <a href="iet.php?workspace=default&solute='.getcwd().'/solute/methane.solute&solvent='.getcwd().'/solvent/tip3p-amber14.01A.gaff&traj='.getcwd().'/solute/methane.gro&rc=1.2&nr=60x60x60&log=tutorial_methane&out=tutorial_methane&verbo=2&debug=0&debugxc=16&closure=KH&steprism=500&stephi=0&temperature=298&save=515&report=45&display=table&rdfbins=60&rdfgrps=1-1,1-2&fmt=14.7g&lsa=0.3&xvvextend=0&ndiis=5&delvv=1&errtol=0.0000001&sd=5&enlv=1&coulomb=Coulomb&ignoreram=no"><u style="color:#0000A0">run with SOLUTE</u></a>. Press the <input type="submit" value="perform calculation" /> button there to start calculations. After starting IET, please press the <input type="submit" value="view screen output" /> button to check <a href="viewfile.php?scroll=bottom&u='.getcwd().'/run/default/tutorial_methane"><u style="color:#0000A0">the screen output (or log)</u></a>.
<p>3. Analyse the outputs.
After successfully performing the previous step: (a) From <a href="analysis.php?filename='.getcwd().'/run/default/tutorial_methane"><u style="color:#0000A0">the screen out put</u></a>, the hydration free energy can be calculated with the excessive chemical potential (columne “excess”) and partial molar volume (columne “volume”) following the Universal Correction scheme.
(b) Click <a href="analysis.php?filename='.getcwd().'/run/default/tutorial_methane.rdf"><u style="color:#0000A0">tutorial_methane.rdf</u></a> and you can see the RDF. 
(c) Click <a href="analysis.php?filename='.getcwd().'/run/default/tutorial_methane.ts4s"><u>tutorial_methane.ts4s</u></a> to view the TS4S file. This TS4S file should have two frames, where the second frame contain the spatial distribution of density. Choose the second frame <span style="background-color:#EEEEEE"><small><input type="radio" value="2" checked> Frame 2  guv@1, real8:60x60x60x2</input></small></span> and click the <input type="submit" value="render image" /> to generate cutviews in <a href="analysis.php?filename='.getcwd().'/run/default/tutorial_methane.ts4s.frame_2"><u>methane.ts4s.frame_2</u></a> folder.
<p>4. If you have any question, or find options difficult to understand, please click the link on the options or directly nevigate to the <a href="help.php"><u style="color:#0000A0">help page</u></a>.
</p>
'."\n");

// Some demos

echo ('<h4>&#9678 Some demos</h4>
<p> <small>(Press the <input type="submit" value="perform calculation" /> button after clicking the following links)</small>
<p> Water around methane: 3DRISM-KH: <a href="iet.php?workspace=default&solute='.getcwd().'/solute/methane.prmtop&solvent='.getcwd().'/solvent/tip3p-amber14.01A.gaff&traj='.getcwd().'/solute/methane.pdb&rc=1.2&nr=60x60x60&log=tutorial_methane&out=tutorial_methane&verbo=0&debug=0&debugxc=0&closure=KH&steprism=500&stephi=0&temperature=298&save=512&report=45&display=table&rdfbins=60&rdfgrps=1-1,1-2&fmt=14.7g&lsa=0.3&xvvextend=0&ndiis=5&delvv=1&errtol=0.0000001&sd=5&enlv=1&coulomb=Coulomb&ignoreram=no"><u style="color:#0000A0"> click to continue </u></a>
<p> Water around methane: 3DRISM-HI-HNC, considering dewetting at hydrophobic solutes: <a href="iet.php?workspace=default&solute='.getcwd().'/solute/methane.prmtop&solvent='.getcwd().'/solvent/tip3p-amber14.01A.gaff&traj='.getcwd().'/solute/methane.pdb&rc=1.2&nr=60x60x60&log=tutorial_methane&out=tutorial_methane&verbo=0&debug=0&debugxc=0&closure=HNC&steprism=500&stephi=100&temperature=298&save=512&report=45&display=table&rdfbins=60&rdfgrps=1-1,1-2&fmt=14.7g&lsa=0.3&xvvextend=0&ndiis=5&delvv=1&errtol=0.0000001&sd=5&enlv=1&coulomb=Coulomb&ignoreram=no"><u style="color:#0000A0"> click to continue </u></a>
<p> Water around sodium and chloride: 3DRISM-HNC: <a href="iet.php?workspace=default&solute='.getcwd().'/solute/NaCl.solute&solvent='.getcwd().'/solvent/tip3p-amber14.01A.gaff&traj='.getcwd().'/solute/NaCl.pdb&rc=1.2&nr=80x80x80&log=tutorial_NaCl&out=tutorial_NaCl&verbo=0&debug=0&debugxc=0&closure=HNC&steprism=500&stephi=0&temperature=298&save=512&report=45&display=table&rdfbins=60&rdfgrps=1-1,2-1&fmt=14.7g&lsa=0.3&xvvextend=0&ndiis=5&delvv=1&errtol=1e-12&sd=15&enlv=1&coulomb=Coulomb&ignoreram=no"><u style="color:#0000A0"> click to continue </u></a>
<p> Water around sodium and chloride: 3DRISM-IDC-HNC, consider the ion-dipole-correction to the water orientation effect: <a href="iet.php?workspace=default&solute='.getcwd().'/solute/NaCl.idc.solute&solvent='.getcwd().'/solvent/tip3p-amber14.01A.gaff&traj='.getcwd().'/solute/NaCl.pdb&rc=1.2&nr=80x80x80&log=tutorial_NaCl&out=tutorial_NaCl&verbo=0&debug=0&debugxc=0&closure=HNC&steprism=500&stephi=0&temperature=298&save=512&report=45&display=table&rdfbins=60&rdfgrps=1-1,2-1&fmt=14.7g&lsa=0.3&xvvextend=0&ndiis=5&delvv=1&errtol=1e-12&sd=15&enlv=1&coulomb=Coulomb&ignoreram=no"><u style="color:#0000A0"> click to continue </u></a>
<p> Water around a short peptide 5TJ1: 3DRISM-IDC-PSE3, compute RDF from multiple frames: <a href="iet.php?workspace=default&solute='.getcwd().'/solute/5tj1.idc.solute&solvent='.getcwd().'/solvent/tip3p-amber14.01A.gaff&traj='.getcwd().'/solute/5tj1-only-traj.gro&rc=1.2&nr=80x80x80&log=tutorial_5tj1&out=tutorial_5tj1&verbo=0&debug=0&debugxc=0&closure=PSE3&steprism=500&stephi=0&temperature=298&save=512&report=45&display=table&rdfbins=60&rdfgrps=16-1,131-1,88-1,106-1,117-1,217-1,285-1,278-1,79-1,248-1,201-1,216-1,16-2,131-2,88-2,106-2,117-2,217-2,285-2,278-2,79-2,248-2,201-2,216-2&fmt=14.7g&lsa=0.3&xvvextend=0&ndiis=5&delvv=1&errtol=1e-7&sd=15&enlv=1&coulomb=Coulomb&ignoreram=no"><u style="color:#0000A0"> click to continue </u></a>
<p>
'."\n");


    echo ('<h4>&#9678 Licence</h4>
  <p>All rights reserved. You can use and modify the software under the <a href="https://www.gnu.org/licenses/lgpl-3.0.en.htm" style="color:blue"><u>GNU Lesser General Public License v3</u></a>. </p>
'."\n");

    echo ('</div>'."\n");

    echo ('<div class="container">'."\n");

    echo ('<table style="width:95%;min-width:480;max-width:750;table-layout:fixed">'."\n");
        echo ('<tr>'."\n");
        echo ('<td width=25% height=100><center><a href="install.php"><img src="images/Install.png" width=100 height=80 /></a></center></td>'."\n");
        echo ('<td width=25% height=100><center>'.(empty($iet_bin)?"":'<a href="iet.php">').'<img src="images/Solvate.png" width=100 height=80 />'.(empty($iet_bin)?"":'</a>').'</center></td>'."\n");
        echo ('<td width=25% height=100><center><a href="analysis.php"><img src="images/Analysis.png" width=100 height=80 />'.(empty($iet_bin)?"":'</a>').'</center></td>'."\n");
        echo ('<td width=25% height=100><center>'.(empty($iet_bin)?"":'<a href="help.php">').'<img src="images/TT.png" width=100 height=80 />'.(empty($iet_bin)?"":'</a>').'</center></td>'."\n");
        echo ('</tr>'."\n");
        echo ('<tr>'."\n");
        echo ('<td><center><a href="install.php">'.(empty($iet_bin)?'Install':'Reinstall').'</a></center></td>'."\n");
        echo ('<td><center>'.(empty($iet_bin)?"":'<a href="iet.php">').'IET calculations'.(empty($iet_bin)?"":'</a>').'</center></td>'."\n");
        echo ('<td><center><a href="analysis.php">Data analysis</center></a></td>'."\n");
        echo ('<td><center><a href="help.php">Help</a></center></td>'."\n");
        echo ('</tr>'."\n");
    echo ("</table>\n");

    echo ('</div>'."\n");

    echo ('<br>'."\n");

    echo ('</center>'."\n");

    echo ('</div>'."\n");

    include ("page_footer.php");

?>
