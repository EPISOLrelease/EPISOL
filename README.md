<img src="https://github.com/EPISOLrelease/EPISOL/blob/main/images/logo_512.png" height=100></img>

# The Expanded Package for IET-based Solvation

The graphic interface to do IET calculations

v 1.1.326 (c) 2022 Cao Siqin

# Installation

### System requirement:

The server side requires Linux/MacOS/WindowsWSL/Cygwin with the following software installed: php (>=5), bash, gcc (>=4.6), make, tar.

The client side requires a browser, e.g.: Chrome, Safari, Edge, Firefox, Opera. Internet Explorer is not supported. Since everything is stored in the server, the client side only needs HTML4 and JavaScript.

### Install the PHP

In most Linux distributions, php can be easily installed from apt (Debian/Ubuntu), yum (Fedora/RedHat/CentOS), zypper (SuSE), etc. In Linux and MacOS, php can also be installed via homebrew or anaconda.

If you want to compile PHP, please refer to the [php installation guide on Unix](https://www.php.net/manual/en/install.unix.php). EPISOL only uses the basic functions of PHP, so you don't need to enable any other packages when compiling the PHP. For example:
```
./configure --prefix=$HOME/php-8.1.12 --without-sqlite3 --without-pdo-sqlite --without-libxml --disable-xml --disable-dom --disable-simplexml --disable-xmlreader --disable-xmlwriter
make
make install
```

### Start the EPISOL server

**Start EPISOL server with Apache 2**: extract the EPISOL package to a sub-folder of your website.

**Start EPISOL server with the build-in server of PHP**:

`php -S YOUR_IP_ADDRESS:A_PORT -t EPISOL_ROOT_FOLDER`

e.g.: `php -S 127.0.0.1:8080 -t $HOME/Downloads/EPISOL-main`

### Install the kernel 

Nevigate to install.php (the second tab “Install” or “Reinstall” on the front page), which provides buttons to install two components: FFTW and the kernel of EPISOL. Install FFTW first, then install the kernel.

Other optional tools required for data analysis: gnuplot and ImageMagick. System settings of ImageMagick is seen in the help page (help.php).


# License

You can use, modify or redistribute under the terms of the [GNU Lesser General Public License v3](https://www.gnu.org/licenses/lgpl-3.0.en.html)


# References

[1] Siqin Cao, Michael L. Kalin, Xuhui Huang, EPISOL: A Software Package with Expanded Functions to Perform 3D-RISM Calculations for the Solvation of Chemical and Biological Molecules,  Journal of Computational Chemistry (submitted)

[2] Siqin Cao, Yunrui Qiu, Ilona C. Unarta, Eshani C. Goonetilleke, and Xuhui Huang, The Ion-Dipole Correction of the 3DRISM Solvation Model to Accurately Compute Water Distbutions around Negatively Charged Biomolecules, [Journal of Physical Chemistry B (2022)](https://doi.org/10.1021/acs.jpcb.2c04431)

[3] Siqin Cao, Kirill Konovalov, Ilona Christy Unarta, and Xuhui Huang, Recent Developments in Integral Equation Theory for Solvation to Treat Density Inhomogeneity at Solute-Solvent Interface, [Advanced Theory and Simulations 2, 1900049 (2019)](https://doi.org/10.1002/adts.201900049)

[4] Siqin Cao, Lizhe Zhu, and Xuhui Huang, 3DRISM-HI-D2MSA: an improved analytic theory to compute solvent structure around hydrophobic solutes with proper treatment of solute–solvent electrostatic interactions, [Molecular Physics 116, 1003 (2017)](https://doi.org/10.1080/00268976.2017.1416195)

[5] Siqin Cao, Fu Kit Sheong, and Xuhui Huang, Reference interaction site model with hydrophobicity induced density inhomogeneity: An analytical theory to compute solvation properties of large hydrophobic solutes in the mixture of polyatomic solvent molecules, [Journal of Chemical Physics 143, 054110 (2015)](https://doi.org/10.1063/1.4928051)
