#!/bin/bash

if [ ! `command -v gnuplot` ]; then exit; fi
if [ ! `command -v convert` ]; then exit; fi

fn=$1;

if [ -e $fn.rdf ]; then
    #tools/prepare-iet-gnuplot.sh $fn.rdf $fn.rdf -o $fn -color red > $fn.gnuplot
    bash tools/prepare-gnuplot-script.sh -f $fn.rdf -o $fn > $fn.gnuplot
    gnuplot $fn.gnuplot
fi
if [ -e $fn.eps ]; then
    convert -units PixelsPerInch -density 600 $fn.eps $fn.png
fi



