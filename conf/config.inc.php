<?php
$cms_cfg['debug'] = false;
/*
server name
*/
$cms_cfg['server_name']	= "localhost";
$cms_cfg['index_page'] = "index.html";
$cms_cfg["manage_page"] = "index.php";

$cms_cfg['base_root']	= "/payment_allpay_atm/";
$cms_cfg['base_url']	= "http://".$cms_cfg['server_name'].$cms_cfg['base_root'];
$cms_cfg['base_css']	= $cms_cfg['base_root']."css/";
$cms_cfg['base_images']	= $cms_cfg['base_root']."images/";
$cms_cfg['base_templates']	= "templates/";

//共用檔案路徑
$cms_cfg['file_root']="/payment_allpay_atm/";
$cms_cfg['file_url']	= "http://".$cms_cfg['server_name'].$cms_cfg['file_root'];

$cms_cfg['tb_prefix'] = "cht";
session_start();
?>