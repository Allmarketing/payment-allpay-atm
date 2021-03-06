<?php
require_once "returncode/atm.php";
require_once "class/mcrypt/aes.php";
require_once "class/Curl.class.php";
require_once "libs/libs-curl-extension.php";
class Model_Order_Payment_Allpay_Atm {
    //put your code here
    protected $config;
    protected $hash = array();
    protected $mode;
    protected $fields = array();
    protected $codedata = array();
    protected $url = array(
        'testing' => "http://pay-stage.allpay.com.tw/payment/Srv/gateway",
        'running' => "https://pay.allpay.com.tw/payment/Srv/gateway",
    );
    protected $xml_template = "templates/allpay-vaccount-xmldata.xml";
    function __construct($config) {
        $this->config = $config;
        $this->mode = $config['exe_mode'];
        $this->hash = $config['Hash'];
        $this->fields['MerchantID'] = $this->config['MerchantID'];
        $this->fields['PaymentType'] = $this->config['PaymentType'];
        $this->codedata['MerchantID'] = $this->config['MerchantID'];
    }
    //結帳
    function checkout($o_id,$total_price,$extra_info=array()){
        $this->codedata['MerchantTradeNo'] = $o_id;
        $this->codedata['MerchantTradeDate'] = date("Y/m/d H:i:s");
        $this->codedata['TradeAmount'] = $total_price;
        $this->codedata = array_merge($this->codedata,$this->config['params']);
        if(!empty($extra_info)){
            foreach($extra_info as $k => $v){
                if(isset($this->codedata[$k])){
                    $this->codedata[$k] = $v;
                }
            }
        }
        $this->fields['XMLData'] = $this->make_xml();
        $GET = http_build_query($this->fields);
        $ch  = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->url[$this->mode].'?'.$GET); //GET
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        $redirect=0;
        $response = curl_redirect_exec($ch,$redirect);
        $returnXML = simplexml_load_string($response);
        $data = array("VAReturn"=>1);
        foreach($returnXML->Data->children() as $elm){
            $key = trim($elm->getName());
            $data[$key] = sprintf("%s",$elm);
        }
        $GET = http_build_query($data);
        header("location:".$this->config['params']['ReplyURL']."?".$GET);
        die();
    }
    //更新訂單
    function update_order(DB $db,SimpleXMLElement $result){
        $oid = $result->Data->MerchantTradeNo;
        if($result->Data->RtnCode=='1'){ //交易成功
            $sql = "update ".$db->prefix("order")." set "
                    . "o_status='1', "
                    . "TradeNo = '".$result->Data->TradeNo."', "
                    . "RtnCode = '".$result->Data->RtnCode."', "
                    . "PayDate = '".$result->Data->PayDate."', "
                    . "AccBank = '".$result->Data->AccBank."', "
                    . "AccNo = '".$result->Data->AccNo."' "
                    . "where o_id='".$oid."'";
        }else{
            //更新訂單狀態
            if(!in_array($result->Data->RtnCode,'10100054','10100089')){ //錯誤原因非訂單編號重複
                $sql = "update ".$db->prefix("order")." set "
                        . "o_status='10', "
                        . "TradeNo = '".$result->Data->TradeNo."', "
                        . "RtnCode = '".$result->Data->RtnCode."', "
                        . "PayDate = '".$result->Data->PayDate."', "
                        . "AccBank = '".$result->Data->AccBank."', "
                        . "AccNo = '".$result->Data->AccNo."' "
                        . "where o_id='".$oid."'";
            }
        }
        //$db->query($sql);
        //$sql = "select * from ".$db->prefix("order")." where o_id='".$oid."'";
        //return $db->query_firstRow($sql,true);
        return $sql;
    }
    /*製作xml*/
    function make_xml(){
        $tpl = new TemplatePower($this->xml_template);
        $tpl->prepare();
        foreach($this->codedata as $k => $v){
            if(!in_array($k,array("MerchantTradeDate"))){
                $tpl->assignGlobal($k,urlencode($v));
            }else{
                $tpl->assignGlobal($k,$v);
            }
        }
        $XMLdata =  $tpl->getOutputContent();
        return Mcrypt_Aes::aes128cbcEncrypt($XMLdata , $this->hash['IV'] , $this->hash['Key']);
    }
    //解析授權回轉結果
    function parse_xmldata($postdata){
        if($postdata){
            $postdata = str_replace(" ", "+", $postdata);
            $xmldata = Mcrypt_Aes::aes128cbcDecrypt($postdata, $this->hash['IV'], $this->hash['Key']);
            $returnXML = new SimpleXMLElement($xmldata);        
        }
        return $returnXML;
    }
    
}
