<?php
(!defined('RUN_INIT')) && die('No Init');
include_once(DIR_VENDOR.'/Spyc/Spyc.cls_php'); 


class extYaml extends Spyc{
    // Dumps array to YAML.
    static function adump($array, $indent = false, $wordwrap = false, $no_opening_dashes = false){
        return self::YAMLDump($array, false, false, false);
    }
    // Parses YAML file to array.
    static function fload($file){
        return self::YAMLLoad($file);
    }
    // Parses YAML str to array.
    static function sload($str){
        return self::YAMLLoadString($str);
    }
}


/* Demo *************************************************************************************

$fp_array = DIR_VENDOR.'/Spyc/cfg_array.php';
$fp_yaml = DIR_VENDOR.'/Spyc/cfg_yaml.yaml';

require($fp_array);
$str_yaml = file_get_contents($fp_yaml);

$yaml = extYaml::adump($cfg_array,4,60);
echo "\n[array dump to yaml:]"; basDebug::varShow($yaml);

echo "\n<hr>\n";

$Data = extYaml::fload($fp_yaml);
echo "\n[load yaml_file to array]<pre>"; basDebug::varShow($Data);

echo "\n<hr>\n";

$Data = extYaml::sload($str_yaml);
echo "\n[load yaml_string to array]"; basDebug::varShow($Data);

//************************************************************************************* ******/

