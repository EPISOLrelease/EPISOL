#!/bin/bash

renice  19 $$

if [ -z "$5" ]; then echo "extract-ts4sdump-and-plot.sh : too few parameters"; exit; fi

ts4sdump=$1;
ts4s=$2;
folder=$3;
frame=$4;
color=$5;

if [ ! -e $folder ]; then
    echo "extract-ts4sdump-and-plot.sh : error : folder not found : " $folder;
elif [ ! -e $ts4sdump ]; then
    echo "extract-ts4sdump-and-plot.sh : error : ts4sdump not found : " $ts4sdump;
elif [ ! -e $ts4s ]; then
    echo "extract-ts4sdump-and-plot.sh : error : ts4s file not found : " $ts4s;
else
    dims=`$ts4sdump -dim $frame -f $ts4s`;
    dim_x=`echo $dims|awk '{print $3}'`
    dim_y=`echo $dims|awk '{print $4}'`
    dim_z=`echo $dims|awk '{print $5}'`
    dim_v=`echo $dims|awk '{print $6}'`
    if [ -e tools/color_table_guv ]; then
        cp tools/color_table_guv $color
    else
        echo "0      0   0 160" > $color
        echo "1e-8  85 107 255" >> $color
        echo "1    255 255 255" >> $color
        echo "4    255   0   0" >> $color
    fi

    $ts4sdump -ez $frame -f $ts4s -pwd $folder

    if [ -e $color ]; then rm $color; fi
fi
