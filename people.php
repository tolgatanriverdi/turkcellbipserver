<?php

require_once 'config.php';

$userInput = urldecode(file_get_contents('php://input'));
if (isset($userInput)) {
	$jsonInput = json_decode($userInput,true);
	if (isset($jsonInput)) {
		
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

			foreach ($contacts as $key => $value) {
				
				if (strlen($value) > 10) {
					$result = @mysql_query("SELECT contactPhone FROM contacts WHERE contactPhone='$value' AND id='$userID'") or die("Query Error1: ".mysql_error());
					$num = mysql_num_rows($result);
					mysql_free_result($result);
					
					$isBip = false;
					$contactUserName = $value."@".$xmppDomain;
					$result = @mysql_query("SELECT * FROM users WHERE username ='$contactUserName'") or die("Query Error2: ".mysql_error());
					$num = mysql_num_rows($result);
					if ($num > 0) {
						$isBip = true;
					}
					
					if ($num == 0) {
						mysql_free_result($result);
						@mysql_query("INSERT INTO contacts (id,contactPhone,isBip) VALUES('$userID','$value','$isBip')") or die("Insertion Error1: ".mysql_error());
					} else {
						@mysql_query(@"UPDATE contacts SET isBip='$isBip' WHERE id='$userID'") or die("Update Error1:".mysql_error());
					}	
								
				}
	
			}
		}
		
		

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