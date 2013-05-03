<?php


use com\google\i18n\phonenumbers\PhoneNumberUtil;
use com\google\i18n\phonenumbers\PhoneNumberFormat;
use com\google\i18n\phonenumbers\NumberParseException;
require_once 'libPhoneNumberPHP/PhoneNumberUtil.php';
require_once 'config.php';

//$userInput = urldecode(file_get_contents('php://input'));
$userInput = $_POST["userInput"];
$phoneUtil = PhoneNumberUtil::getInstance();

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
			
			$result = @mysql_query("SELECT username FROM users WHERE id='$userID'") or die(json_encode($resultArr));
			$row = mysql_fetch_row($result);
			$userPhone = strstr($row[0], "@",true);
			//echo "<br> UserPhone: '$userPhone' <br>";
			mysql_free_result($result);

			foreach ($contacts as $msisdnArr) {

				$value = $msisdnArr["msisdn"];
				$abID = $msisdnArr["abID"];
				if (strlen($value) > 10) {
					$countryCode = 'TR';	//Test amacli oldugu icin sadece turkiye eklenmistir
					$isValid=false;
					$number;
					try {
						$number = $phoneUtil->parseAndKeepRawInput($value, $countryCode);
						$isValid = $phoneUtil->isValidNumber($number);				
					} catch(Exception $e) {
						$isValid=false;
					}
					
					if ($isValid) {  //Sadece valid phone numberlar database e eklenir
						
						$value = substr($phoneUtil->format($number, PhoneNumberFormat::E164), 1);
						//echo "<br>Formatted phone: ".$value." <br>";
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
							$bipArr["abID"] = $abID;
							if ($row["profileImage"]) {
								$bipArr["profileUrl"] = $profileURL;								
							}

							$resultArr["contacts"][$contactsIndex] = $bipArr;
							$contactsIndex++;
						}
						//echo "<br>".$value." ISBIP:".$isBip."<br>";
						mysql_free_result($result);
						
						
						$result = @mysql_query("SELECT contactPhone FROM contacts WHERE contactPhone='$value' AND id='$userID'") or die(json_encode($resultArr));
						$num = mysql_num_rows($result);
						mysql_free_result($result);
						
						if ($num == 0) {

							
							//Ejabberda rosteritemlari ekler
							$request = "add_rosteritem ".$userPhone." ".$xmppDomain." ".$value." ".$xmppDomain." Osman Friends both";		
							$opts =  array('http' =>array('method' => "POST",'header' => "Host: localhost\nContent-Type: text/html; charset=utf-8",'content' => $request));
							$context = stream_context_create($opts);
							$fp = fopen($xmppUrl, 'r', false, $context);
				
							if ($fp) {
		
								$response_str = stream_get_contents($fp);
								fclose($fp);
								
								if ($response_str == "0") {
									@mysql_query("INSERT INTO contacts (id,contactPhone,abID,isBip) VALUES('$userID','$value','$abID',$isBip')") or die(json_encode($resultArr));
								}
							}
						} else {
							@mysql_query(@"UPDATE contacts SET isBip='$isBip',abID='$abID' WHERE id='$userID' AND contactPhone='$value'") or die(json_encode($resultArr));
						}						
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
	$resultArr["result"] = 10;
	echo json_encode($resultArr);
}

?>