<?php
/**
	B"H
	Crown Heights Cycles Amazon UPC Info App
	Developed by GorinSystems (www.gorinsystems.com)

	Page: Config (config.php)
	Description: PHP config, Amazon API config (access keys, timeout time, other params),

**/

ini_set('max_execution_time',18000); // in seconds
ini_set('memory_limit','256M');
ini_set('max_input_time',18000); // in seconds

// Amazon Product API Public Key for CHCycles account
define('PUBLIC_KEY','AKIAIYICN5TUJRQWFUOQ');  

// Amazon Product API Private Key for CHCycles account
define('PRIVATE_KEY','gJAp16jOVCs4Yxn/HObCva53A00++ESG5g4T/WCn'); 

// Amazon Associate Tag for CHCycles Accont
define('ASSOCIATE_TAG','crowheigcycl-20'); 

// Amazon Region ("com" = commercial)
define('REGION','com'); 

// Amazon Associate Tag for CHCycles Accont
define('SEARCH_INDEX','SportingGoods'); 

// Num seconds to pause between each API query; by default Amazon only lets 1 query/second 
define('TIMEOUT',1);
 
?>
