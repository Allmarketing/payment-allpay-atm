<?php
require_once "TP/class.TemplatePower.inc.php";
require_once "class/model/order/payment/allpay/atm.php"; 
require_once "conf/vaccount.php";
require_once "conf/config.inc.php";
if($_POST){
    $card = new Model_Order_Payment_Allpay_Atm($cms_cfg['vaccount']);
    $card->checkout($_POST['orderid'], $_POST['price']);
}

