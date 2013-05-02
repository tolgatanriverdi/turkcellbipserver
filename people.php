<?php

require_once 'config.php';

//$userInput = urldecode(file_get_contents('php://input'));
$userInput = $_POST["userInput"];

if (isset($userInput)) {
	$jsonInput = json_decode($userInput,true);
	echo "JSON INPUT ID:".$jsonInput["id"]." PhoneType:".$jsonInput["phoneType"];
	if (isset($jsonInput["id"])) {
		
		@mysql_connect($databaseAddr,$databaseUser,$databaseUserPass) or die("Database Connection Error");
		@mysql_select_db($databaseName) or die("Database Selection Error");
		
		$userID = $jsonInput["id"];
		$phoneType;
		$clientOs;
		$apnToken;
		$nickName;
		$contacts;
		
		$updateStr="id='$userID'";
		if (isset($jsonInput["phoneType"])) {
			$phoneType = $jsonInput["phoneType"];		
			$updateStr = $updateStr.",phoneType='$phoneType'";	
		}

		if (isset($jsonInput["clientVersion"])) {
			$clientVersion = $jsonInput["clientVersion"];	
			$updateStr = $updateStr.",clientVersion='$clientVersion'";		
		}

		if (isset($jsonInput["clientOs"])) {
			$clientOs = $jsonInput["clientOs"];			
			$updateStr = $updateStr.",clientOs='$clientOs'";
		}

		if (isset($jsonInput["apnToken"])) {
			$apnToken = $jsonInput["apnToken"];	
			$updateStr = $updateStr.",apnToken='$apnToken'";		
		}

		if (isset($jsonInput["nickname"])) {
			$nickName = $jsonInput["nickname"];		
			$updateStr =$updateStr.",nickname='$nickName'";	
		}

		if (isset($jsonInput["contacts"])) {
			$contacts = $jsonInput["contacts"];	
			
			var_dump($contacts);

			foreach ($contacts as $msisdnArr) {

				$value = $msisdnArr["msisdn"];
				if (strlen($value) > 10) {
					$result = @mysql_query("SELECT contactPhone FROM contacts WHERE contactPhone='$value' AND id='$userID'") or die("Query Error1: ".mysql_error());
					$num = mysql_num_rows($result);
					mysql_free_result($result);
					
					$isBip = false;
					$contactUserName = $value."@".$xmppDomain;
					$result = @mysql_query("SELECT * FROM users WHERE username ='$contactUserName'") or die("Query Error2: ".mysql_error());
					$num_bip = mysql_num_rows($result);
					if ($num_bip > 0) {
						$isBip = true;
					}
					echo $value."<br> ISBIP:".$isBip."<br>";
					mysql_free_result($result);
					
					if ($num == 0) {
						@mysql_query("INSERT INTO contacts (id,contactPhone,isBip) VALUES('$userID','$value','$isBip')") or die("Insertion Error1: ".mysql_error());
					} else {
						@mysql_query(@"UPDATE contacts SET isBip='$isBip' WHERE id='$userID'") or die("Update Error1:".mysql_error());
					}	
								
				}
	
			}
		}
		
		
		echo "UPDATE users SET ".$updateStr." WHERE id='$userID' <br>";
		@mysql_query(@"UPDATE users SET ".$updateStr." WHERE id='$userID'") or die("Update Error2:".mysql_error());
		mysql_close();
	} else {
		echo "Json Input Error :".json_last_error()."<br>";
		echo "Raw Input: ".$userInput;
	}
	
} else {
	echo "Input Error";
}

?>