<?php
require_once "TP/class.TemplatePower.inc.php";
require_once "class/model/order/payment/allpay/atm.php"; 
require_once "conf/config.inc.php";
require_once "conf/vaccount.php";
if($_POST){
    $card = new Model_Order_Payment_Allpay_Atm($cms_cfg['vaccount']);
    $bank = $_POST['BankName']?$_POST['BankName']:$cms_cfg['vaccount']['params']['BankName'];
    $card->checkout($_POST['orderid'], $_POST['price'],array('BankName'=>$bank));
}

