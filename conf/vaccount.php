<?php
$cms_cfg['vaccount']['MerchantID'] = "2000132";//特店代碼
$cms_cfg['vaccount']['PaymentType'] = "vAccount";//虛擬帳號固定vAccount
$cms_cfg['vaccount']['params']['ExpireDate'] = '10';     //允許繳費有效天數，最長 60 天
$cms_cfg['vaccount']['params']['BankName'] = 'TAISHIN';  //銀行代碼請參考附件，請參考附件documents/虛擬帳號介接規格說明.pdf
$cms_cfg['vaccount']['params']['ReplyURL'] = $cms_cfg['base_url']."card-test3.php"; //接收授權結果通知網址(以client post方式返回)
$cms_cfg['vaccount']['Hash']['Key'] = "ejCk326UnaZWKisg";
$cms_cfg['vaccount']['Hash']['IV'] = "q9jcZX8Ib9LM8wYk";
$cms_cfg['vaccount']['exe_mode'] = "testing";  //執行模式: testing or running
?>