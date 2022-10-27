<img src="https://github.com/seechin/EPISOL/blob/main/images/logo_512.png" height=100></img>

# The Expanded Package for IET-based Solvation

The graphic interface to do IET calculations

v 1.1.321 (c) 2022 Cao Siqin

All rights reserved. Academic use is licensed with [GNU Lesser General Public License (LGPL)](https://www.gnu.org/licenses/lgpl-3.0.en.html).

# Run EPISOL

The EPISOL uses a server-client architecture for the GUI. The server side requires PHP (>=5), and the client side requires a web browser (Chrome, FireFox, Safari, Edge, etc. Internet Explorer not supported)

## Start EPISOL server with Apache 2

Extract the EPISOL package to a sub-folder of your website.

## Start EPISOL server with the build-in server of PHP

php -S YOUR_IP_ADDRESS:A_PORT -t CWBSOL_ROOT_FOLDER

e.g.: php -S 127.0.0.1:8080 -t $HOME/Downloads/CWBSol-master

# Install the necessary components

The install page (install.php) provides the installation interface of two components: FFTW and the kernel of EPISOL. Install FFTW first, then install the kernel.

Other optional tools required for data analysis: gnuplot and ImageMagick. System settings of ImageMagick is seen in the help page (help.php).


