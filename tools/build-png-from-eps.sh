#!/bin/bash

if [ ! `command -v convert` ]; then exit; fi

fn=$1;

if [ -e $fn.eps ]; then
    convert -units PixelsPerInch -density 600 $fn.eps $fn.png
fi



