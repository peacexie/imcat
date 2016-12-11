
@echo off

%~d0
cd %~dp0

cd ../../../

cd root
tree >../skin/dev/d_start/tree_droot.txt
cd ../code
tree >../skin/dev/d_start/tree_dcode.txt
cd ../skin
tree >../skin/dev/d_start/tree_dskin.txt

pause

cd ../
tree >skin/dev/d_start/tree_full.txt /F
tree >skin/dev/d_start/tree_fdir.txt

pause
cmd
