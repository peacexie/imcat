
@echo off

%~d0
cd %~dp0

cd ../../../

cd root
tree >../views/dev/d_start/tree_droot.txt
cd ../imcat
tree >../views/dev/d_start/tree_dimcat.txt
cd ../views
tree >../views/dev/d_start/tree_dviews.txt

pause

cd ../
tree >views/dev/d_start/tree_full.txt /F
tree >views/dev/d_start/tree_fdir.txt

pause
cmd
