ALTER TABLE  `cht_order` 
ADD  `BankCode` CHAR( 3 ) NOT NULL 
ADD  `vAccount` VARCHAR( 255 ) NOT NULL 
ADD  `ExpireDate` DATE NOT NULL 
ADD  `TradeNo` VARCHAR( 20 ) NOT NULL ,
ADD  `RtnCode` INT NOT NULL ,
ADD  `PayDate` DATETIME NOT NULL ,
ADD  `AccBank` CHAR(3) NOT NULL ,
ADD  `AccNo` CHAR( 5 ) NOT NULL ;
