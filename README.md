<img src="https://github.com/EPISOLrelease/EPISOL/blob/main/images/logo_512.png" height=100></img>

# The Expanded Package for IET-based Solvation

The graphic interface to do IET calculations

v 1.1.322 (c) 2022 Cao Siqin

All rights reserved. You can use and modify the software under the [GNU Lesser General Public License v3](https://www.gnu.org/licenses/lgpl-3.0.en.html)

# Run EPISOL

The EPISOL uses a server-client architecture for the GUI. The server side requires PHP (>=5), and the client side requires a web browser (Chrome, FireFox, Safari, Edge, etc. Internet Explorer not supported)

## Start EPISOL server with Apache 2

Extract the EPISOL package to a sub-folder of your website.

## Start EPISOL server with the build-in server of PHP

php -S YOUR_IP_ADDRESS:A_PORT -t CWBSOL_ROOT_FOLDER

e.g.: php -S 127.0.0.1:8080 -t $HOME/Downloads/CWBSol-master

# Please install the kernel first

The install page (install.php) provides the installation interface of two components: FFTW and the kernel of EPISOL. Install FFTW first, then install the kernel.

Other optional tools required for data analysis: gnuplot and ImageMagick. System settings of ImageMagick is seen in the help page (help.php).

# References

[1] Siqin Cao, Michael L. Kalin, Xuhui Huang, EPISOL: A Software Package with Expanded Functions to Perform 3D-RISM Calculations for the Solvation of Chemical and Biological Molecules,  Journal of Computational Chemistry (submitted)

[2] Siqin Cao, Yunrui Qiu, Ilona C. Unarta, Eshani C. Goonetilleke, and Xuhui Huang, The Ion-Dipole Correction of the 3DRISM Solvation Model to Accurately Compute Water Distbutions around Negatively Charged Biomolecules, [Journal of Physical Chemistry B (2022)](https://doi.org/10.1021/acs.jpcb.2c04431)

[3] Siqin Cao, Kirill Konovalov, Ilona Christy Unarta, and Xuhui Huang, Recent Developments in Integral Equation Theory for Solvation to Treat Density Inhomogeneity at Solute-Solvent Interface, [Advanced Theory and Simulations 2, 1900049 (2019)](https://doi.org/10.1002/adts.201900049)

[4] Siqin Cao, Lizhe Zhu, and Xuhui Huang, 3DRISM-HI-D2MSA: an improved analytic theory to compute solvent structure around hydrophobic solutes with proper treatment of solute–solvent electrostatic interactions, [Molecular Physics 116, 1003 (2017)](https://doi.org/10.1080/00268976.2017.1416195)

[5] Siqin Cao, Fu Kit Sheong, and Xuhui Huang, Reference interaction site model with hydrophobicity induced density inhomogeneity: An analytical theory to compute solvation properties of large hydrophobic solutes in the mixture of polyatomic solvent molecules, [Journal of Chemical Physics 143, 054110 (2015)](https://doi.org/10.1063/1.4928051)
