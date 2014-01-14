<?php
require_once "TP/class.TemplatePower.inc.php";
require_once "conf/vaccount.php";
$tpl = new TemplatePower("test1.html");
$tpl->prepare();
foreach($cms_cfg['vaccount']['Bank'] as $code=>$name){
    $tpl->newBlock("BANK_LIST");
    $tpl->assign(array(
        "BANK_CODE" => $code,
        "BANK_NAME" => $name,
    ));
}
$tpl->printToScreen();
