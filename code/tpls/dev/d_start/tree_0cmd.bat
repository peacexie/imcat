
@echo off

%~d0
cd %~dp0

cd ../../../../

cd root
tree >../code/tpls/dev/d_start/tree_droot.txt
cd ../code
tree >../code/tpls/dev/d_start/tree_dcode.txt

pause

cd ../
tree >code/tpls/dev/d_start/tree_full.txt /F
tree >code/tpls/dev/d_start/tree_fdir.txt

pause
cmd
