<?php

require_once 'config.php';

$userInput = urldecode(file_get_contents('php://input'));
//echo "User Input Is: ".$userInput."<br>";

if (isset($userInput)) {
	$inputArr = json_decode($userInput,true);
		
	$userPhone = $inputArr["msisdn"];
	//echo "Input Arr:".$inputArr." UserPhone:".$userPhone."User Input:".$userInput."<br>";
	$resultArr = array();
	$resultArr["msisdn"] = $userPhone;
	
	@mysql_connect($databaseAddr,$databaseUser,$databaseUserPass) or die("Database Connection Error");
	@mysql_select_db($databaseName) or die("Database Selection Error");
	
	if (!isset($inputArr["smsCode"])) {  //Telefon numaras� ile registration kismi ise buraya gelir
		$num = 0;
		
		$result = @mysql_query("SELECT * FROM msisdn WHERE userPhone='$userPhone'") or die("Query Error");
		$num = mysql_num_rows($result);
		
		
		
		$activationCode = rand(10000, 99999);
		$currentDate = date("Y-m-d H:i:s",time());
		$resultArr["resultCode"] = 2;
		if ($num == 0) {
			@mysql_query("INSERT INTO msisdn (userPhone,activationCode,date,isActivated) VALUES ('$userPhone','$activationCode','$currentDate','0')") or die("Insert Query Error");
			echo json_encode($resultArr);

		}  else if ($num > 0) {
			$row = mysql_fetch_array($result,MYSQL_BOTH);
			$dateNow = new DateTime($currentDate);
			$dateStored = new DateTime($row["date"]);
			
			@mysql_query("UPDATE msisdn SET activationCode='$activationCode',date='$currentDate' WHERE userPhone='$userPhone'") or die("Update Error");
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
				
				@mysql_query("UPDATE msisdn SET serverToken='$serverPassword', isActivated=1 WHERE userPhone='$userPhone'") or die("Update Activation Error");
				$resultArr["token"] = $serverPassword;	
				echo json_encode($resultArr);
		
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