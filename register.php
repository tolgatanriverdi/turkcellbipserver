<?php

require_once 'config.php';


$userInput = $_POST["userInput"];

if (isset($userInput))  {
	
	$inputArr = json_decode($userInput,true);
	$userPhone = $inputArr["msisdn"];
	$token = $inputArr["token"];
	$phoneType = $inputArr["phoneType"];
	$clientVersion = $inputArr["clientVersion"];
	$clientOs = $inputArr["clientOs"];
	$apnToken = $inputArr["apnToken"];
	//echo "Input Arr:".$inputArr." UserPhone:".$userPhone."User Input:".$userInput."<br>";
	$resultArr = array();
	$resultArr["msisdn"] = $userPhone;
	
	@mysql_connect($databaseAddr,$databaseUser,$databaseUserPass) or die("Database Connection Error");
	@mysql_select_db($databaseName) or die("Database Selection Error");
	
	$result = mysql_query("SELECT * FROM msisdn WHERE serverToken='$token' AND userPhone='$userPhone'") or die("Query Error");
	$num = mysql_num_rows($result);
	mysql_free_result($result);
	
	if ($num > 0) {
		$num = 0;
		$userName = $userPhone."@".$xmppDomain;
		$result = @mysql_query("SELECT * FROM users WHERE username='$username'") or die("Query Error");
		$num = mysql_num_rows($result);
		
		$currentDate = date("Y-m-d H:i:s",time());
		$resultArr["result"] = 7; //Default database hatasi set edilir bir sorun cikmassa successe (0) cevrilir;
		if ($num == 0) {	
			//Ejabberd register request
			$request = "register ".$userPhone." ".$xmppDomain." ".$token;
			$opts =  array('http' =>array('method' => "POST",'header' => "Host: $xmppRestHost\nContent-Type: text/html; charset=utf-8",'content' => $request));
				
			$context = stream_context_create($opts);
			$fp = fopen($xmppUrl, 'r', false, $context);
				
			if ($fp) {
				
				$response_str = stream_get_contents($fp);
				fclose($fp);
				
				//XMPP Serverdan ok donmezse kullaniciyi database de aktive etmez
				if (stripos($response_str,"successfully registered") === false) {
					//echo "XMPP Server Connection Error";
					$resultArr["result"] = 7;
				} else {
					@mysql_query("INSERT INTO users (username,password,phoneType,clientVersion,clientOs,apnToken,dateCreated) VALUES ('$userName','$token','$phoneType','$clientVersion','$clientOs','$apnToken','$currentDate')") or die(json_encode($resultArr));		
					$resultArr["id"] = mysql_insert_id();
					$resultArr["result"] = 0;
				}
					
			}
			
			
		} else {
			@mysql_query("UPDATE users SET phoneType='$phoneType',clientVersion='$clientVersion,clientOs='$clientOs',apnToken='$apnToken',dateCreated='$currentDate' WHERE username='$username'") or die(json_encode($resultArr));
			$row = mysql_fetch_array($result);
			$resultArr["id"] = $row["id"];
			$resultArr["result"] = 0;
		}
		
		
		echo json_encode($resultArr);
	} else {
		$resultArr["result"] = 5;
		echo json_encode($resultArr);
	}
	mysql_close();
}


?>