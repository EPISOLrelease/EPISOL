#!/bin/bash

eprism_package=release

if [ ! -e $eprism_package ]; then
    echo Extract the $eprism_package package
    echo tar -zxf ${eprism_package}.tar.gz
    tar -zxf ${eprism_package}.tar.gz
fi

echo Config EPRISM
cd $eprism_package
#make clean

if [ ! -z "$GMXBIN" ]; then
    ./configure --with-fftw=$PWD/../../fftw/fftw3 --prefix=$PWD/.. --with-gmx=$GMXBIN/..
else
    ./configure --with-fftw=$PWD/../../fftw/fftw3 --prefix=$PWD/..
fi

make -j8 install

if [ -e $PWD/../bin ]&&[ -e $PWD/../share ]; then
    echo success!
    ls -l $PWD/../bin
    ls -l $PWD/../share
else
    echo failed!
    exit
fi

cd ..
echo " " >  ver.php

if [ -e bin/ts4sdump ]&&[ -e bin/gmxtop2solute ]&&[ -e bin/eprism3d ]; then
    echo success!
    bin/eprism3d
    bin/ts4sdump
    bin/gmxtop2solute
    if [ -e bin/gensolvent ]; then bin/gensolvent; fi
    if [ -e bin/heatmap ]; then bin/heatmap; fi
    if [ -e bin/generate-idc.sh ]; then bash bin/generate-idc.sh; fi
    ver_eprism3d=(`bin/eprism3d|while read bin ver; do echo $ver;done`); printf "<?php\n  \$software_version = \"%s\";\n?>\n" $ver_eprism3d > ver.php 
else
    echo failed!
fi
