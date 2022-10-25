#!/bin/bash

renice  19 $$

if [ -z "$6" ]; then echo "extract-ts4sdump-and-plot.sh : too few parameters"; exit; fi
echo extract-ts4s-and-plot.sh $1 $2 $3 $4 $5 $6

sizescale=5;
ts4sdump=$1;
heatmap=$2;
ts4s=$3;
folder=$4;
frame=$5;
color=$6;

if [ ! -e $folder ]; then
    echo "extract-ts4sdump-and-plot.sh : error : folder not found : " $folder;
elif [ ! -e $ts4sdump ]; then
    echo "extract-ts4sdump-and-plot.sh : error : ts4sdump not found : " $ts4sdump;
elif [ ! -e $heatmap ]; then
    echo "extract-ts4sdump-and-plot.sh : error : heatmap not found : " $heatmap;
elif [ ! -e $ts4s ]; then
    echo "extract-ts4sdump-and-plot.sh : error : ts4s file not found : " $ts4s;
else
    ts4s_base=`basename -- $ts4s`
    ts4s_base_no_ext=`echo $ts4s_base | sed s/.ts4s//g`
    dims=`$ts4sdump -dim $frame -f $ts4s`;
    dim_x=`echo $dims|awk '{print $3}'`
    dim_y=`echo $dims|awk '{print $4}'`
    dim_z=`echo $dims|awk '{print $5}'`
    dim_v=`echo $dims|awk '{print $6}'`
    #printf  "%dx%dx%dx%d\n" $dim_x $dim_y $dim_z $dim_v
    if [ -e tools/color_table_guv ]; then
        cp tools/color_table_guv $color
    else
        echo "0      0   0 160" > $color
        echo "1e-8  85 107 255" >> $color
        echo "1    255 255 255" >> $color
        echo "4    255   0   0" >> $color
    fi

    txt_count=`ls $folder/${ts4s_base_no_ext}_z*.txt | wc -l`;
    if [ $txt_count -ne $dim_z ]; then
        echo $ts4sdump -ez $frame -f $ts4s -pwd $folder
        $ts4sdump -ez $frame -f $ts4s -pwd $folder
    fi

    for fn in `ls $folder/${ts4s_base_no_ext}_z*.txt|sed s/.txt//g`; do
        datafile=$fn.txt;
        bmpfile=$fn.bmp;
        jpgfile=$fn.jpg;
        echo $heatmap -c $color -f $datafile -col 4 -nr ${dim_x}x${dim_y} -size $((dim_x*sizescale))x$((dim_y*sizescale)) -o $bmpfile
        $heatmap -c $color -f $datafile -col 4 -nr ${dim_x}x${dim_y} -size $((dim_x*sizescale))x$((dim_y*sizescale)) -o $bmpfile
        if [ -e $bmpfile ]; then
            echo convert $bmpfile -quality 9 $jpgfile
            convert $bmpfile $jpgfile
            rm $bmpfile
        fi
    done
    convert -delay 10 -loop 0 $folder/${ts4s_base_no_ext}_z*.jpg $folder/${ts4s_base_no_ext}_frame_${frame}.gif


    if [ -e $color ]; then rm $color; fi
fi
