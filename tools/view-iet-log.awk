#!/usr/bin/awk -f
#   analysis eprism3d log
#   parameters: -v verbos=1/0 -v a=... -v b=... -v fit=fep/exp

BEGIN {
    ninst = 0;
    if (verbose=="") verbose=2;
    if (verbose=="" && verb!="") verbose=verb;

    fit_all_cates[1] = "FEP";
    fit_all_cates[2] = "EXP";

    nuc = 0;

    #ss[++nuc]="FEP      KH  -624.168469765581    0";
    #ss[++nuc]="EXP      KH  -627.012426149722    0";
    #ss[++nuc]="FEP     KGK  -425.248535087388    0";
    #ss[++nuc]="EXP     KGK  -428.022379386743    0";
    #ss[++nuc]="FEP    PSE2  -668.747116955235    0";
    #ss[++nuc]="EXP    PSE2   -671.96073432949    0";
    #ss[++nuc]="FEP    PSE3  -681.838110075808    0";
    #ss[++nuc]="EXP    PSE3  -685.195150013335    0";
    #ss[++nuc]="FEP    PSE4  -686.450160715357    0";
    #ss[++nuc]="EXP    PSE4  -691.669231883881    0";
    #ss[++nuc]="FEP    PSE5  -688.525197069011    0";
    #ss[++nuc]="EXP    PSE5  -695.178346803915    0";
    #ss[++nuc]="FEP   PLHNC  -686.828594825657    0";  # fitted at alpha=3
    #ss[++nuc]="EXP   PLHNC    -690.2696652787    0";  # fitted at alpha=3
    #ss[++nuc]="FEP     HNC  -686.828594825657    0";  # HNC is simply copied from PLHNC
    #ss[++nuc]="EXP     HNC    -690.2696652787    0";  # HNC is simply copied from PLHNC

    ss[++nuc]=" FEP     KH  -647.348453608648    4.8547480416612";
    ss[++nuc]=" EXP     KH  -619.952559990907  -1.47859772642391";
    ss[++nuc]=" FEP    KGK   -462.53443482818   8.04130525293397";
    ss[++nuc]=" EXP    KGK  -435.780764679989   1.67322083808028";
    ss[++nuc]=" FEP   PSE2  -687.908213741747    3.5610101387673";
    ss[++nuc]=" EXP   PSE2  -657.653298424423  -2.65897745235407";
    ss[++nuc]=" FEP   PSE3  -702.709116407626   3.75516272446305";
    ss[++nuc]=" EXP   PSE3  -671.598836677146  -2.44628209193456";
    ss[++nuc]=" FEP   PSE4  -710.201646298877   4.17184421269326";
    ss[++nuc]=" EXP   PSE4  -684.990022127642  -1.17317388291133";
    ss[++nuc]=" FEP   PSE5  -714.134748976177   4.40755095859481";
    ss[++nuc]=" EXP   PSE5  -692.678698950017 -0.430203751105637";
    ss[++nuc]=" FEP  PLHNC  -710.548315106323   4.20537805720096";  # fitted at alpha=3
    ss[++nuc]=" EXP  PLHNC  -678.964891475467   -2.0042752246319";  # fitted at alpha=3
    ss[++nuc]=" FEP    HNC  -710.548315106323   4.20537805720096";  # HNC is simply copied from PLHNC
    ss[++nuc]=" EXP    HNC  -678.964891475467   -2.0042752246319";  # HNC is simply copied from PLHNC



    for (i=1;i<=nuc;i++){
        split(ss[i], ssa, " "); for(j=1;j<=4;j++) uc[i,j] = ssa[j];
    }

} NF>0 {
    if (substr($1,length($1)-7,8)=="eprism3d"){
        if (NF==2){
            current_version = $2;
        } else if ($2=="begins"){
            ninst ++; software[ninst]="eprism3d"; version[ninst] = current_version; begin_time[ninst] = $4; frames[ninst] = 0;
        } else if ($2=="ends"){
            end_time[ninst] = $4;
        } else if ($3=="process"){
            nthreads[ninst] = $7;
        } else if (substr($2,1,1)=="-"){
          for (i=1;i<=NF;i++){
            if ($i=="-p" || $i=="--p" || $i=="-solvent" || $i=="--solvent"){
                #solvent_file[ninst] = $(i+1)=="--"? $(i+2) : $(i+1);
                filename = $(i+1)=="--"? $(i+2) : $(i+1);
                n = split(filename, fa, "/");
                solvent_file[ninst] = fa[n]; 
            } else if ($i=="-s" || $i=="--s" || $i=="-solute" || $i=="--solute"){
                #solute_file[ninst] = $(i+1)=="--"? $(i+2) : $(i+1);
                filename = $(i+1)=="--"? $(i+2) : $(i+1);
                n = split(filename, fa, "/");
                solute_file[ninst] = fa[n]; 
            } else if ($i=="-f" || $i=="--f" || $i=="-traj" || $i=="--traj" || $i=="-conf" || $i=="--conf"){
                #traj_file[ninst] = $(i+1)=="--"? $(i+2) : $(i+1);
                filename = $(i+1)=="--"? $(i+2) : $(i+1);
                n = split(filename, fa, "/");
                traj_file[ninst] = fa[n]; 
            }
          }
        }
    } else if ($1=="Memory" && substr($2,1,5)=="usage"){
        total_memory = $(NF-1)" "$NF;
    } else if (substr($1,1,6)=="solute" && substr($1,length($1))==":"){
        gsub(/^[ \t]+/, "", $0);
        solute_info[ninst] = $0;
    } else if (substr($1,1,7)=="solvent" && substr($1,length($1))==":"){
        gsub(/^[ \t]+/, "", $0);
        solvent_info[ninst] = $0;
    } else if ($1==">" && ($2=="Frame" || substr($3,1,2)=="ps")){
        frames[ninst] ++;
        frame_box[ninst,frames[ninst]] = $4;
        frame_grid[ninst,frames[ninst]] = $(NF-1);
    } else if (substr($1,1,2)=="HI" || substr($1,1,4)=="HSHI"){
        hi_method[ninst,frames[ninst]] = $1; hi_steps[ninst,frames[ninst]] = $3; hi_err[ninst,frames[ninst]] = $5;
    } else if (substr($1,1,4)=="RISM" || substr($1,1,4)=="SSOZ"){
        rism_method[ninst,frames[ninst]] = $1; rism_steps[ninst,frames[ninst]] = $3; rism_err[ninst,frames[ninst]] = $5;
        closure[ninst,frames[ninst]] = substr($1,6);
    } else if ($1=="Atom"){
        last_Atom_line_NF = NF;
        for (j=1;j<=NF;j++){
            if ($j=="Volume") col_vol[ninst,frames[ninst]] = j;
            else if ($j=="excess") col_excess[ninst,frames[ninst]] = j;
            else if ($j=="exGF") col_GF[ninst,frames[ninst]] = j;
            else if ($j=="excessGF") col_GF[ninst,frames[ninst]] = j;
            else if ($j=="LJSR") col_lj[ninst,frames[ninst]] = j;
            else if ($j=="Coul") col_coul[ninst,frames[ninst]] = j;
        }
    } else if ($1=="total" && $NF!="s" && NF>=last_Atom_line_NF){
        if (col_vol[ninst,frames[ninst]]>0) volume[ninst,frames[ninst]] = $(col_vol[ninst,frames[ninst]]);
        if (col_excess[ninst,frames[ninst]]>0) excess[ninst,frames[ninst]] = $(col_excess[ninst,frames[ninst]]);
        if (col_GF[ninst,frames[ninst]]>0) excess_GF[ninst,frames[ninst]] = $(col_GF[ninst,frames[ninst]]);
        if (col_lj[ninst,frames[ninst]]>0) energy_lj[ninst,frames[ninst]] = $(col_lj[ninst,frames[ninst]]);
        if (col_coul[ninst,frames[ninst]]>0) energy_coul[ninst,frames[ninst]] = $(col_coul[ninst,frames[ninst]]);
    }

} END {
    default_param_ever_used = 0;
  for (iinst=1; iinst<=ninst; iinst++){
    if (frames[iinst]<=0) continue;
    printf("===============================================================================\n");
    if (verbose>=1 || ninst>1){
        printf("Log %d : %s %s\n  # begin at %s\n  # end at %s\n", iinst, software[iinst], version[iinst], begin_time[iinst], end_time[iinst]);
    }
    if (verbose>=1){
        if (length(nthreads)>0) printf("  # %d thread%s, %s\n", nthreads[iinst], nthreads[iinst]>1?"s":"", total_memory);
        if (length(solvent_file)>0) printf("  # solvent: %s (%s)\n", solvent_file[iinst], solvent_info[iinst]);
        if (length(solute_file)>0) printf("  # solute: %s (%s)\n", solute_file[iinst], solute_info[iinst]);
        if (length(traj_file)>0) printf("  # traj: %s : %d frame%s\n", traj_file[iinst], frames[iinst], frames[iinst]>1?"s":"");
    } else {
        printf("# solvent: %s\n", solvent_file[iinst]);
        printf("# solute: %s\n", solute_file[iinst]);
        printf("# traj: %s : %d frames\n", traj_file[iinst], frames[iinst]);
    }
    for (j=1; j<=frames[iinst]; j++){
        # printf("> Frame %d %s %s\n", j, frame_box[iinst,j], frame_grid[iinst,j]);
        if (verbose>=1 || frames[iinst]>1){
          printf("Frame %d : ", j);
            if (hi_steps[iinst,j]>0) printf(" %s steps=%d,err=%s", hi_method[iinst,j], hi_steps[iinst,j], hi_err[iinst,j]);
            if (rism_steps[iinst,j]>0) printf(" %s steps=%d,err=%s", rism_method[iinst,j], rism_steps[iinst,j], rism_err[iinst,j]);
            printf ("\n");
        }
        if (length(closure[iinst,j])<=0) continue ;
        # printf("closure: %s\n", closure[iinst,j]);
        pmv = volume[iinst,j]; ex = excess[iinst,j]; gf = excess_GF[iinst,j];
        if (col_lj[iinst,j]>0) printf("  %-12s %15s kJ/mol\n", "LJSR", energy_lj[iinst,j]);
        if (col_coul[iinst,j]>0) printf("  %-12s %15s kJ/mol\n", "Coulomb", energy_coul[iinst,j]);
        if (col_vol[iinst,j]>0) printf("  %-12s %15s nm3\n", "PMV", pmv);
        if (col_excess[iinst,j]>0) printf("  %-12s %15s kcal/mol\n", "excess", ex/4.184);
        #if (col_excess[iinst,j]>0) printf("  %-12s %15s kcal/mol\n", "excess_GF", gf/4.184);
        if (col_excess[iinst,j]>0 && col_vol[iinst,j]>0){
            printf("  %-12s %15s\n", "closure", closure[iinst,j]);
            if (a=="" || b==""){
              for (icat=1; icat<=2; icat++){
                fit_cate = fit_all_cates[icat];
                i_found_uc = -1;
                for (iuc=1;iuc<=nuc;iuc++) if (uc[iuc,1]==fit_cate && uc[iuc,2]==substr(closure[iinst,j],1,length(uc[iuc,2]))) i_found_uc = iuc;
                if (i_found_uc<0){ printf("# error: cannot find UC parameters\n"); continue; }
                uc_a = uc[i_found_uc,3]; uc_b = uc[i_found_uc,4];
                default_param_ever_used ++;

                sfe = ex + uc_a * pmv + uc_b;
                if (closure[iinst,j]!=uc[i_found_uc,2]){
                    printf("  %-15s %12g kcal/mol as %s\n", "SFE.for."fit_cate, sfe/4.184, uc[i_found_uc,2]);
                } else {
                    printf("  %-15s %12g kcal/mol\n", "SFE.for."fit_cate, sfe/4.184);
                }
              }
            } else {
                sfe = ex + a * pmv + b;
                printf("  %-12s %15g kcal/mol\n", "SFE", sfe/4.184);
            }
        }
    }
  }
  if (verbose>=1 && default_param_ever_used>0) printf("# [1] DOI: 10.26434/chemrxiv-2022-52cn3\n");
  if (ninst>0) printf("===============================================================================\n");
}

