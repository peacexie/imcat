
@echo off

%~d0
cd %~dp0

cd ../../../

cd root
tree >../views/dev/d_start/tree_droot.txt
cd ../views
tree >../views/dev/d_start/tree_dviews.txt

pause

cd ../
tree >views/dev/d_start/tree_full.txt /F
tree >views/dev/d_start/tree_fdir.txt

pause

cd ../share_imcat
tree >../catmain/views/dev/d_start/tree_dimcat.txt

pause
cmd
