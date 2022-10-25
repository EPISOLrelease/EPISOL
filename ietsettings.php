<?php

    $closures_MSAHNC = array( "HNC", "PLHNC", "MSA", "KGK", "KH", "PSE2", "PSE3", "PSE4", "PSE5", "PSE6", "PSE7", "PSE8", "PSE9", "PSE10" );
    $closures = array( "HNC", "PLHNC", "MSA", "KGK", "KH", "PSE2", "PSE3", "PSE4", "PSE5", "PSE6", "PSE7", "PSE8", "PSE9", "PSE10", "PY", "HNCB", "D2", "MS", "BPGG", "Marucho-Pettitt", "MHNC", "Modified-Verlet" );

    class IETSettings {
        public $workspace;
        public $np,$ntb,$rc,$nr,$box;
        public $log,$out,$verbo,$debug,$debugxc;
        public $closure,$steprism,$stephi,$cr,$temperature;
        public $save,$report,$display,$rdfbins,$rdfgrps;
        public $fmt,$lsa,$xvvextend,$ndiis,$delvv,$errtol,$sd,$enlv,$coulomb,$coul_p;
        public $ignoreram;
        public $solute,$solvent,$traj;
    }
    function init_settings($set){
      // load settings from URL
       // files
        if (empty($set->workspace  )) $set->workspace   = $_GET["workspace"]?? "";
        if (empty($set->solute     )) $set->solute      = $_GET["solute"]?? "";
        if (empty($set->solvent    )) $set->solvent     = $_GET["solvent"]?? "";
        if (empty($set->traj       )) $set->traj        = $_GET["traj"]?? "";
       // basic options
        if (empty($set->np         )) $set->np          = $_GET["np"   ]?? "";
        if (empty($set->ntb        )) $set->ntb         = $_GET["ntb"  ]?? "";
        if (empty($set->rc         )) $set->rc          = $_GET["rc"   ]?? 1;
        if (empty($set->nr         )) $set->nr          = $_GET["nr"   ]?? "100x100x100";
        if (empty($set->box        )) $set->box         = $_GET["box"  ]?? "";
        if (empty($set->log        )) $set->log         = $_GET["log"  ]?? "";
        if (empty($set->out        )) $set->out         = $_GET["out"  ]?? "";
        if (empty($set->verbo      )) $set->verbo       = $_GET["verbo"]?? 1;
        if (empty($set->debug      )) $set->debug       = $_GET["debug"]?? 0;
        if (empty($set->debugxc    )) $set->debugxc     = $_GET["debugxc"]?? 16;
        if (empty($set->closure    )) $set->closure     = $_GET["closure" ]?? "KH";
        if (empty($set->steprism   )) $set->steprism    = $_GET["steprism"]?? 500;
        if (empty($set->stephi     )) $set->stephi      = $_GET["stephi"  ]?? 0;
        if (empty($set->cr         )) $set->cr          = $_GET["cr"      ]?? 0;
        if (empty($set->temperature)) $set->temperature = $_GET["temperature"  ]?? 298;
       // output
        if (empty($set->save       )) $set->save        = $_GET["save"    ]?? 2051;
        if (empty($set->report     )) $set->report      = $_GET["report"  ]?? 7;
        if (empty($set->display    )) $set->display     = $_GET["display" ]?? "table";
        if (empty($set->rdfbins    )) $set->rdfbins     = $_GET["rdfbins" ]?? 50;
        if (empty($set->rdfgrps    )) $set->rdfgrps     = $_GET["rdfgrps" ]?? "";
       // advanced options
        if (empty($set->fmt        )) $set->fmt         = $_GET["fmt"     ]?? "14.7g";
        if (empty($set->lsa        )) $set->lsa         = $_GET["lsa"     ]?? 0.3;
        if (empty($set->xvvextend  )) $set->xvvextend   = $_GET["xvvextend"]?? 0;
        if (empty($set->ndiis      )) $set->ndiis       = $_GET["ndiis"   ]?? 5;
        if (empty($set->delvv      )) $set->delvv       = $_GET["delvv"   ]?? 1;
        if (empty($set->errtol     )) $set->errtol      = $_GET["errtol"  ]?? "0.0000001";
        if (empty($set->sd         )) $set->sd          = $_GET["sd"      ]?? 15;
        if (empty($set->enlv       )) $set->enlv        = $_GET["enlv"    ]?? 1;
        if (empty($set->coulomb    )) $set->coulomb     = $_GET["coulomb" ]?? "Coulomb";
        if (empty($set->coul_p     )) $set->coul_p      = $_GET["coul_p"  ]?? "";
        if (empty($set->ignoreram  )) $set->ignoreram   = $_GET["ignoreram"]?? "no";
    }
    function save_settings($set, $file){
        $buf = "<?php\n";
        if (!empty($set->solute     )) $buf .= "    \$set->solute      = \"".$set->solute     ."\";\n";
        if (!empty($set->solvent    )) $buf .= "    \$set->solvent     = \"".$set->solvent    ."\";\n";
        if (!empty($set->traj       )) $buf .= "    \$set->traj        = \"".$set->traj       ."\";\n";
        if (!empty($set->np         )) $buf .= "    \$set->np          = \"".$set->np         ."\";\n";
        if (!empty($set->ntb        )) $buf .= "    \$set->ntb         = \"".$set->ntb        ."\";\n";
        if (!empty($set->rc         )) $buf .= "    \$set->rc          = \"".$set->rc         ."\";\n";
        if (!empty($set->nr         )) $buf .= "    \$set->nr          = \"".$set->nr         ."\";\n";
        if (!empty($set->box        )) $buf .= "    \$set->box         = \"".$set->box        ."\";\n";
        if (!empty($set->log        )) $buf .= "    \$set->log         = \"".$set->log        ."\";\n";
        if (!empty($set->out        )) $buf .= "    \$set->out         = \"".$set->out        ."\";\n";
        if (!empty($set->verbo      )) $buf .= "    \$set->verbo       = \"".$set->verbo      ."\";\n";
        if (!empty($set->debug      )) $buf .= "    \$set->debug       = \"".$set->debug      ."\";\n";
        if (!empty($set->debugxc    )) $buf .= "    \$set->debugxc     = \"".$set->debugxc    ."\";\n";
        if (!empty($set->closure    )) $buf .= "    \$set->closure     = \"".$set->closure    ."\";\n";
        if (!empty($set->steprism   )) $buf .= "    \$set->steprism    = \"".$set->steprism   ."\";\n";
        if (!empty($set->stephi     )) $buf .= "    \$set->stephi      = \"".$set->stephi     ."\";\n";
        if (!empty($set->cr         )) $buf .= "    \$set->cr          = \"".$set->cr         ."\";\n";
        if (!empty($set->temperature)) $buf .= "    \$set->temperature = \"".$set->temperature."\";\n";
        if (!empty($set->save       )) $buf .= "    \$set->save        = \"".$set->save       ."\";\n";
        if (!empty($set->report     )) $buf .= "    \$set->report      = \"".$set->report     ."\";\n";
        if (!empty($set->display    )) $buf .= "    \$set->display     = \"".$set->display    ."\";\n";
        if (!empty($set->rdfbins    )) $buf .= "    \$set->rdfbins     = \"".$set->rdfbins    ."\";\n";
        if (!empty($set->rdfgrps    )) $buf .= "    \$set->rdfgrps     = \"".$set->rdfgrps    ."\";\n";
        if (!empty($set->fmt        )) $buf .= "    \$set->fmt         = \"".$set->fmt        ."\";\n";
        if (!empty($set->lsa        )) $buf .= "    \$set->lsa         = \"".$set->lsa        ."\";\n";
        if (!empty($set->xvvextend  )) $buf .= "    \$set->xvvextend   = \"".$set->xvvextend  ."\";\n";
        if (!empty($set->ndiis      )) $buf .= "    \$set->ndiis       = \"".$set->ndiis      ."\";\n";
        if (!empty($set->delvv      )) $buf .= "    \$set->delvv       = \"".$set->delvv      ."\";\n";
        if (!empty($set->errtol     )) $buf .= "    \$set->errtol      = \"".$set->errtol     ."\";\n";
        if (!empty($set->sd         )) $buf .= "    \$set->sd          = \"".$set->sd         ."\";\n";
        if (!empty($set->enlv       )) $buf .= "    \$set->enlv        = \"".$set->enlv       ."\";\n";
        if (!empty($set->coulomb    )) $buf .= "    \$set->coulomb     = \"".$set->coulomb    ."\";\n";
        if (!empty($set->coul_p     )) $buf .= "    \$set->coul_p      = \"".$set->coul_p     ."\";\n";
        if (!empty($set->ignoreram  )) $buf .= "    \$set->ignoreram   = \"".$set->ignoreram  ."\";\n";
        $buf .= "?>\n";
        $fp = fopen($file, "w"); fwrite($fp, $buf); fclose($fp);
    }
    function generate_calc_command($set, $eprism3d){
        $exec = $eprism3d;
        $pwd_relocated = false; if (is_dir(getcwd().'/run/'.$set->workspace)){
            $exec .= " -pwd run/".$set->workspace  ; $pwd_relocated = true;
        } else {
            $exec .= " -pwd run";
        }
        if (!empty($set->solute     )) $exec .= " -s "     .$set->solute     ;
        if (!empty($set->solvent    )) $exec .= " -p "     .$set->solvent    ;
        if (!empty($set->traj       )) $exec .= " -f "     .$set->traj       ;
        if (!empty($set->np         )) $exec .= " -nt "    .$set->np         ;
        if (!empty($set->ntb        )) $exec .= " -ntb "   .$set->ntb        ;
        if (!empty($set->rc         )) $exec .= " -rc "    .$set->rc         ;
        if (!empty($set->nr         )) $exec .= " -nr "    .$set->nr         ;
        if (!empty($set->box        )) $exec .= " -box "   .$set->box        ;
        if (!empty($set->log)) $set->log = str_replace("/", "_", $set->log);
        if (!empty($set->log)) $exec .= " -log ".$set->log; else if ($pwd_relocated) $exec .= " -log stdout.txt";
        if (!empty($set->out)) $set->out = str_replace("/", "_", $set->out);
        if (!empty($set->out        )) $exec .= ($set->save&1024?" -ov ":" -o ").$set->out        ;
        $exec .= " -v "     .$set->verbo      ;
        if (!empty($set->debug      )) $exec .= " -debug " .$set->debug      ;
        if (!empty($set->debugxc    )) $exec .= ($set->debugxc&16?" -debug-xvv":"") . ($set->debugxc&32?" -debug-crc":"");
        if (!empty($set->temperature)) $exec .= " -T "      .$set->temperature;
        if (!empty($set->rdfbins    )) $exec .= " -rdf-bins ".$set->rdfbins    ;
        if (!empty($set->rdfgrps    )) $exec .= " -rdf-grps ".$set->rdfgrps    ;
        if (!empty($set->fmt        )) $exec .= " -%".$set->fmt        ;
        if (!empty($set->lsa        )) $exec .= " -lsa "     .$set->lsa        ;
        if (!empty($set->xvvextend  )) $exec .= " -xvv-extend ".$set->xvvextend  ;
        if (!empty($set->ndiis      )) $exec .= " -ndiis "   .$set->ndiis      ;
        if (!empty($set->delvv      )) $exec .= " -delvv "   .$set->delvv      ;
        if (!empty($set->errtol     )) $exec .= " -errtolrism "  .$set->errtol     ;
        if (!empty($set->sd         )) $exec .= " -sd "      .$set->sd         ;
        if (!empty($set->enlv)) if ($set->enlv>0) $exec .= " -dynamic-delvv ".$set->enlv; else $exec .= " -no-dynamic-delvv ";
        if (!empty($set->coulomb    )) $exec .= " -coulomb ".$set->coulomb    ;
            if (!empty($set->coul_p )) $exec .= " ".$set->coul_p;
        if (!empty($set->ignoreram)) if ($set->ignoreram=="yes") $exec .= " -ignore-ram";
        if (!empty($set->cr)) $exec .= " -cr";

        $exec .= " -cmd ";
        if (!empty($set->closure    )) $exec .= " closure=".$set->closure    ;
        if (!empty($set->stephi) && $set->stephi>0) $exec .= " hi,step=".$set->stephi     ;
        if (!empty($set->steprism) && $set->steprism>0) $exec .= " rism,step=".$set->steprism   ;
        if (!empty($set->report) && $set->report != 0){
            $exec .= $set->display=="table"?" report":" display";
            $exec .= ":mass".($set->report&1?",volume":"").($set->report&2?",TS":"").($set->report&4?",lj":"").($set->report&8?",coul":"").($set->report&16?",Hef0":"");
            $exec .= ($set->report&32?",excess":"").($set->report&64?",excessGF":"").($set->report&128?",cuv":"");
        }
        /*if (!empty($set->display) && $set->display != 0){
            $exec .= " display:mass".($set->display&1?",volume":"").($set->display&2?",TS":"").($set->display&4?",lj":"").($set->display&8?",coul":"").($set->display&16?",Hef0":"");
            $exec .= ($set->display&32?",excess":"").($set->display&64?",excessGF":"").($set->display&128?",cuv":"");
        }*/
        if (!empty($set->report) && $set->report != 0 && $set->report&256){
            $exec .= $set->display=="table"?" report@end,rdf":" display@end,rdf";
        }
        if (!empty($set->save) && $set->save!=0 && $set->save&512){
            $exec .= " save@end,rdf";
        }
        if (!empty($set->save) && ($set->save&~(1024|2048))!=0){
            $exec .= " save".($set->save&1?",cmd":"").($set->save&2?",guv":"").($set->save&4?",huv":"").($set->save&8?",cuv":"");
            $exec .= ($set->save&16?",csr":"").($set->save&32?",dd":"").($set->save&64?",lj":"").($set->save&128?",coul":"").($set->save&256?",ef":"");
        }

        return $exec;

    }

    function generate_param_url($set){
        $url = "?";
        $plist = array("workspace", "solute", "solvent", "traj", "np", "ntb", "rc", "nr", "box", "log", "out", "verbo", "debug", "debugxc", "closure", "steprism", "stephi", "cr", "temperature", "save", "report", "display", "rdfbins", "rdfgrps", "fmt", "lsa", "xvvextend", "ndiis", "delvv", "errtol", "sd", "enlv", "coulomb", "coul_p", "ignoreram");
        foreach($plist as $key){
            $url .= (strlen($url)==1?"":"&").$key."=".$set->$key;
        }
        return $url;
    }


?>
