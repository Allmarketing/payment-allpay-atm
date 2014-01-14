payment-allpay
============

歐付寶虛擬帳號串接


前置設定
---------------
1.設定conf/vaccount.php裡的 $cms_cfg['vaccount']['MerchantID'] (特店編號)、 $cms_cfg['vaccount']['params']各項目及$cms_cfg['vaccount]['Hash']各項目。<br/>
2.Model_Order_Payment_Allpay_Returncode_Atm裡的項目會持續新增，請不定時至廠商後台->系統開發管理->交易狀態代碼查詢。<br/>
3.$cms_cfg['vaccount']['Hash']的Key及IV在廠商後台->基本資料查詢->廠商基本資料查詢，請依序填入[一般金流介接HashKey]及[一般金流介接HashIV]<br/>
4.歐付寶虛擬帳號串接會使用AES加密，請確認class/mcrypt/aes.php存在，才可以正確執行加、解密的操作。<br/>


測試流程
---------------
1.執行card-test1.php，輸入訂單號碼、訂單價格及可匯款的銀行.<br/>
2.前述訂單號碼、訂單價格銀行代碼由card-test2.php接收後，依documents/虛擬帳號介接規格說明.pdf第四頁開始的說明傳給歐付寶伺服器，伺服器會將結果以xml顯示於網頁上.<br/>
3.Model_Order_Payment_Allpay_Atm::checkout()會以curl的方式進行第2點的操作並取得xml，解析xml內容後將內容以GET的方式傳遞給$cms_cfg['vaccount']['params']['ReplyURL'].<br/>
4.結果頁，輸出獲得的虛擬帳號訊息以及接收付款通知。


api說明
---------------

### Model_Order_Payment_Allpay_Atm::__construct($config)

    1.$config: 即conf/vaccount.php裡的$cms_cfg['vaccount'].


### Model_Order_Payment_Allpay::checkout($o_id,$total_price,$extra_info=array())

    1.$o_id:訂單號碼.
    2.$total_price:訂單價格.
    3.$extra_info:額外的欄位，預設是空陣列，也就是不加新欄位，如果要加新欄位，請以關聯式陣列輸入，例如: array('email'=>'xxxx@some.domain','tel'=>'88881888').
    4.此函數會使用到libs/libs-curl-extension.php裡的函數，請確認此檔案存在。

### Model_Order_Payment_Allpay::parse_xmldatat($postdata)

    1.$postdata:傳入歐付寶授權回傳的$_POST['XMLData']
> 說明:此函數是作為回傳結果的解密之用， 解密之前需先將回傳結果的空白取代為+<br/>
>      解密後得到一份xml資料，將此xml丟給SimpleXMLElement產生SimpleXMLElement物件，<br/>
>      以利進一步利用。


### Model_Order_Payment_Allpay::update_order($db,SimpleXMLElement $result)

    1.$db: 即libs/libs-mysql.php類別的實體物件。請使用本專案的libs/libs-mysql.php，因為有使用到新增的prefix().
    2.$result: 即歐付寶伺服器回傳的結果，經解密後再轉為SimpleXMLElement的物件傳入.
> 說明:原本只傳出sql，現改為直接在函數裡更新訂單內容，並寫入以下回傳結果欄位:<br/>
>      TradeNo,RtnCode,PayDate,AccBank,AccNo
> 　　 除了訂單編號重複的錯誤之外(RtnCode=10100054 or 10100089)，無論授權成功或失敗皆更新訂單.<br/>
> 　　 授權成功將訂單狀態改為處理中，授權失敗將訂單狀態修改為拒絕訂單.
