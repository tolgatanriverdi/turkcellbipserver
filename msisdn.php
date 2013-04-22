<?php

require_once 'config.php';


$userInput = $_POST["userInput"];

if (isset($userInput)) {
	$inputArr = json_decode($userInput);
	$userPhone = $inputArr["msisdn"];
	$resultArr[];
	
	@mysql_connect($databaseAddr,$databaseUser,$databaseUserPass) or die("Database Connection Error");
	@mysql_select_db($databaseName) or die("Database Selection Error");
	
	if (!isset($inputArr["smsCode"])) {  //Telefon numarasõ ile registration kismi ise buraya gelir
		$num = 0;
		
		$result = @mysql_query("SELECT * FROM msisdn WHERE userPhone='$userPhone'") or die("Query Error");
		$num = mysql_num_rows($result);
		
		
		
		$activationCode = rand(10000, 99999);
		$currentDate = date("Y-m-d H:i:s",time());
		$resultArr["resultCode"] = 2;
		if ($num == 0) {
			@mysql_query("INSERT INTO msisdn (userPhone,activationCode,date,isActivated) VALUES ('$userPhone','$activationCode','$currentDate','0')") or die("Insert Query Error");
			$resultArr["msisdn"] = $userPhone;
			echo json_encode($resultArr);

		}  else if ($num > 0) {
			$row = mysql_fetch_array($result,MYSQL_BOTH);
			$dateNow = new DateTime($currentDate);
			$dateStored = new DateTime($row["date"]);
			
			@mysql_query("UPDATE users SET activationCode='$activationCode',date='$currentDate' WHERE userPhone='$userPhone'") or die("Update Error");
			
			$resultArr["msisdn"] = $userPhone;
			echo json_encode($resultArr);
		}
		else {
			echo "Mysql Error";
		}	
		
		mysql_free_result($result);
		
	} else {		//Sms code ile aktivasyon kismi ise buraya gelir
		
		$activationKey = $inputArr["smsCode"];
		$result = mysql_query("SELECT * FROM msisdn WHERE userPhone='$userPhone' AND activationCode='$activationKey'");
		$num = mysql_numrows($result);
		
		if ($num > 0) {
			$row = mysql_fetch_array($result,MYSQL_BOTH);
			$resultArr["resultCode"] = 0;
			if ($row["isActivated"] == 0) {
				
				$letters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
				$serverPassword = str_shuffle($letters);
				
				//Ejabberd register request
				$request = "register ".$userPhone." ".$xmppDomain." ".$serverPassword;
				$opts =  array('http' =>array('method' => "POST",'header' => "Host: ".$xmppRestHost."\nContent-Type: text/html; charset=utf-8",'content' => $request));
				
				$context = stream_context_create($opts);
				$fp = fopen($xmppUrl, 'r', false, $context);
				
				if ($fp) {
				
					$response_str = stream_get_contents($fp);
					fclose($fp);
				
					//XMPP Serverdan ok donmezse kullaniciyi database de aktive etmez
					if (stripos($response_str,"successfully registered") === false) {
						echo "XMPP Server Connection Error";
					} else {
						@mysql_query("UPDATE msisdn SET serverToken='$serverPassword', isActivated=1 WHERE userPhone='$userPhone'") or die("Update Activation Error");
						$resultArr["token"] = $serverPassword;	
						echo json_encode($resultArr);
					}
					
				}
		
			}
			else {
				mysql_free_result($result);
				$result = @mysql_query("SELECT * FROM msisdn WHERE userPhone='$userPhone'") or die("Query Error");
				$row = mysql_fetch_array($result,MYSQL_BOTH);
				$resultArr["token"] =  $row["serverToken"];
				echo json_encode($resultArr);
				mysql_free_result($result);
			}
		}
		else {
			$resultArr["resultCode"] = 3;
			echo json_encode($resultArr);
		}
		
		
	}
		
mysql_close();	
	
}


?>