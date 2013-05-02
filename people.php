<?php

require_once 'config.php';

//$userInput = urldecode(file_get_contents('php://input'));
$userInput = $_POST["userInput"];

if (isset($userInput)) {
	$jsonInput = json_decode($userInput,true);
	//echo "JSON INPUT ID:".$jsonInput["id"]." PhoneType:".$jsonInput["phoneType"];
	if (isset($jsonInput["id"])) {
		
		@mysql_connect($databaseAddr,$databaseUser,$databaseUserPass) or die("Database Connection Error");
		@mysql_select_db($databaseName) or die("Database Selection Error");
		
		$userID = $jsonInput["id"];
		$phoneType;
		$clientOs;
		$apnToken;
		$nickName;
		$contacts;
		
		$resultArr = array();
		$resultArr["id"] = $userID;
		
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
			$resultArr["contacts"] = array();
			$resultArr["result"] = 9;
			$contactsIndex = 0;

			foreach ($contacts as $msisdnArr) {

				$value = $msisdnArr["msisdn"];
				if (strlen($value) > 10) {
					
					$isBip = 0;
					$contactUserName = $value."@".$xmppDomain;
					$result = @mysql_query("SELECT * FROM users WHERE username ='$contactUserName'") or die(json_encode($resultArr));
					$num_bip = mysql_num_rows($result);
					if ($num_bip > 0) {
						$isBip = 1;
						
						//Bip userin profil resim url ini almak icindir
						$row = mysql_fetch_array($result);
						$profileURL = $uploadsUrl.$row["profileImage"];
						
						$bipArr = array();
						$bipArr["msisdn"] = $value;
						$bipArr["profileUrl"] = $profileURL;
						$resultArr["contacts"][$contactsIndex] = $bipArr;
						$contactsIndex++;
					}
					echo "<br>".$value." ISBIP:".$isBip."<br>";
					mysql_free_result($result);
					
					
					$result = @mysql_query("SELECT contactPhone FROM contacts WHERE contactPhone='$value' AND id='$userID'") or die(json_encode($resultArr));
					$num = mysql_num_rows($result);
					mysql_free_result($result);
					
					if ($num == 0) {
						@mysql_query("INSERT INTO contacts (id,contactPhone,isBip) VALUES('$userID','$value','$isBip')") or die(json_encode($resultArr));
					} else {
						@mysql_query(@"UPDATE contacts SET isBip='$isBip' WHERE id='$userID' AND contactPhone='$value'") or die(json_encode($resultArr));
					}	
								
				}
	
			}
		}
		
		
		//echo "UPDATE users SET ".$updateStr." WHERE id='$userID' <br>";
		@mysql_query(@"UPDATE users SET ".$updateStr." WHERE id='$userID'") or die(json_encode($resultArr));
		mysql_close();
		
		$resultArr["result"] = 0;
		echo json_encode($resultArr,JSON_UNESCAPED_SLASHES);
		
	} else {
		$resultArr["result"] = 8;
		echo json_encode($resultArr);
	}
	
} else {
	$resultArr["result"] = 8;
	echo json_encode($resultArr);
}

?>