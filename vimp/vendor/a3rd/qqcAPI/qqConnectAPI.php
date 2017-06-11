<?php
@session_start();
/* PHP SDK
 * @version 2.0.0
 * @author connect@qq.com
 * @copyright © 2013, Tencent Corporation. All rights reserved.
 */

define("QQC_ROOT",dirname(__FILE__)."/");
define("CLASS_PATH",QQC_ROOT."class/");

require_once(CLASS_PATH."ErrorCase.class.php");
require_once(CLASS_PATH."Oauth.class.php");
require_once(CLASS_PATH."QC.class.php");
require_once(CLASS_PATH."Recorder.class.php");
require_once(CLASS_PATH."URL.class.php");
