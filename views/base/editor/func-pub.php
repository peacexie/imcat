<?php
#namespace imcat;

use imcat\basJscss;

function edwimp($file){
    echo basJscss::write(basJscss::imp($file,'vendui'))."\n";
}
