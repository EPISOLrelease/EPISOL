#!/bin/bash

fn=$1;

if [ -e $fn.eps ]; then
    convert -units PixelsPerInch -density 600 $fn.eps $fn.png
fi



