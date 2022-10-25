#!/bin/bash

fftw_package=fftw-3.3.8

if [ ! -e $fftw_package ]; then
    echo Extract the FFTW package
    tar -zxf $fftw_package.tar.gz
fi

echo Config FFTW 3
cd $fftw_package
make clean
b_fftw_thread=0; if [ ! -z "`cat ../../rismhi3d/header.h|grep _LOCALPARALLEL_FFTW_`" ]; then b_fftw_thread=1; fi
if [ $b_fftw_thread -eq 0 ]; then
    ./configure --prefix=`pwd`/../fftw3
else
    ./configure --prefix=`pwd`/../fftw3 --enable-threads
fi

make -j8 install

if [ -e `pwd`/../fftw3 ]; then
    echo success!
    ls -l `pwd`/../fftw3
else
    echo failed!
fi

