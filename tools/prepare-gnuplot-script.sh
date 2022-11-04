#!/bin/bash
__array_test=( 1 2 3 4 );
array_shift=0; if [ ${__array_test[1]} -eq 2 ]; then array_shift=-1; fi

argc=0; show_help=0; success=1;
for args in $*; do argc=$((argc+1)); argv[$argc]=$args; done

show_help=0;
output="temp";
data_list="";
ndata=0;
color=( "black" "black" "black" );
ncolor=0;
style=( 2 2 2); 
nstyle=0;

for ((i=1;i<=argc;i++)); do key=${argv[i]}; key_prefix_char=`echo ${argv[i]}|awk '{print substr($1,1,1)}'`;
  if [ "$key" == "-h" ]||[ "$key" == "-help" ]||[ "$key" == "--help" ]; then
    show_help=1;
  elif [ "$key" == "-o" ]||[ "$key" == "-override" ]||[ "$key" == "--override" ]; then
    if [ "`echo ${argv[i+1]}|awk '{print substr($1,1,1)}'`" != "-" ]; then
        i=$((i+1)); output=${argv[i]}
    fi
  elif [ "$key" == "-f" ]||[ "$key" == "-data" ]||[ "$key" == "--data" ]; then
    while [ $i -lt $argc ]&&[ "`echo ${argv[i+1]}|awk '{print substr($1,1,1)}'`" != "-" ]; do
        i=$((i+1)); data_list=$data_list" "${argv[i]}; ndata=$((ndata+1)); data[$ndata]="${argv[i]}";
    done
  elif [ "$key" == "--c" ]||[ "$key" == "-c" ]||[ "$key" == "-color" ]||[ "$key" == "--color" ]; then
    while [ $i -lt $argc ]&&[ "`echo ${argv[i+1]}|awk '{print substr($1,1,1)}'`" != "-" ]; do
        i=$((i+1)); ncolor=$((ncolor+1)); color[$ncolor]="${argv[i]}";
    done
  elif [ "$key" == "--lt" ]||[ "$key" == "-lt" ]||[ "$key" == "-line-type" ]||[ "$key" == "--line-type" ]; then
    while [ $i -lt $argc ]&&[ "`echo ${argv[i+1]}|awk '{print substr($1,1,1)}'`" != "-" ]; do
        i=$((i+1)); nstyle=$((nstyle+1)); style[$nstyle]="${argv[i]}";
    done
  else
    echo "$0 : argv[$i] : unrecognizable parameter ${argv[i]}"
    success=0;
  fi
done

for ((i=1;i<=ndata; i++)); do 
    if [ ! -e ${data[i]} ]; then
        echo prepare-gnuplot-script.sh : error : cannot find ${data[i]};
        success=0;
    fi
done
if [ $success -lt 1 ]; then exit ; fi

if [ $ncolor -le 0 ]; then
    ncolor=1;
fi

  # get data range

    ranges=$(for ((i=1;i<=ndata; i++)); do 
        cat ${data[$i]}
    done | awk  -v minx=0 -v miny=0 -v maxx=0.5 -v maxy=0.1 '{if(substr($1,1,1)=="-"||(substr($1,1,1)>=0&&substr($1,1,1)<=9)){if($1<minx)minx=$1;if($1>maxx)maxx=$1; for(i=2;i<=NF;i++){if($i<miny)miny=$i;if($i>maxy)maxy=$i}}}END{printf("%14.8g %14.8g %14.8g %14.8g\n",minx,maxx,miny,maxy)}' );

    panels=$(for ((i=1;i<=ndata; i++)); do
        cat ${data[$i]}
    done | awk -v pn=0 '{if(NF-1>pn)pn=NF-1}END{print pn}');

    xpanels=$panels; ypanels=1;
    if [ $panels -gt 10 ]; then
        xpanels=10; ypanels=$((panels%10==0?panels/10:panels/10+1))
    fi

    range_array=($ranges);
    minx=${range_array[1+array_shift]};
    maxx=${range_array[2+array_shift]};
    miny=${range_array[3+array_shift]};
    maxy=${range_array[4+array_shift]}
    #echo $minx $miny $maxx $maxy $panels

  # generate header

    WIDTH=$(  echo $maxx | awk '{printf("%d",$1*5+1)}' | awk '{printf("%g",$1/5)}'  );
    HEIGHT=$( echo $maxy | awk '{printf("%d",$1*2+1)}' | awk '{printf("%g",$1/2)}'  );
    OUTPUT=$output;

#    WIDTH=`echo $maxx|awk '{printf("%g",$1*0.7)}`

    echo set term postscript eps color linewidth 1 \"Arial,12\" enhanced size $xpanels,$ypanels 
    echo set output \"$OUTPUT.eps\"
    echo set multiplot layout $ypanels,$xpanels
    echo set xrange [0:$WIDTH]
    echo set yrange [0:$HEIGHT]
    echo set key bottom
    echo set key spacing 0.5
    echo set tmargin 0.1
    echo set bmargin 0.1
    echo set lmargin 0.2
    echo set rmargin 0.2

    echo set xtics format \"\"
    echo set ytics format \"\"

    echo set ytics 1
    echo set xtics 0.5

    echo set key spacing 0

    for ((a=1; a<=panels; a++)); do
        icolor=1; istyle=1;
        echo set xlabel \"`head -n1 ${data[1]}|awk -v a=$a '{print $(a+1)}'`\" offset 0,12.5,0 tc rgb \"grey\"
        printf 'plot \\\n'
        for ((i=1;i<=ndata;i++)); do
          if [ $i -eq $ndata ]; then
            printf '    "%s" u 1:%d w lines lt %s lw 3 lc rgb \"%s\" notitle \n'  ${data[i]} $((a+1)) ${style[istyle]} ${color[icolor]} 
          else
            printf '    "%s" u 1:%d w lines lt %s lw 3 lc rgb \"%s\" notitle, \\\n'  ${data[i]} $((a+1)) ${style[istyle]} ${color[icolor]}
          fi
          icolor=$((icolor+1)); if [ $icolor -gt $ncolor ]; then icolor=1; fi
          istyle=$((istyle+1)); if [ $istyle -gt $nstyle ]; then istyle=1; fi
        done
        printf "\n"
    done

