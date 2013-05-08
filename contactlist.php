<?php
require_once 'config.php';
$userInput = urldecode(file_get_contents('php://input'));
//$userInput = $_POST["userInput"];

if (isset($userInput)) {
	$jsonInput = json_decode($userInput,true);	
	$resultArr = array();
	$resultArr["result"] = 7;
	
	if (isset($jsonInput["id"])) {
		@mysql_connect($databaseAddr,$databaseUser,$databaseUserPass) or die("Database Connection Error");
		@mysql_select_db($databaseName) or die("Database Selection Error");
		
		$userID = $jsonInput["id"];
		$listType = $jsonInput["allList"];
		$resultArr["id"] = $userID;
		
		$result  = mysql_query("SELECT username FROM users WHERE id='$userID'");
		$row = mysql_fetch_row($result);
		$userPhone = strstr($row[0],"@",true);
		mysql_free_result($result);
		
		$result = @mysql_query("SELECT * FROM contacts WHERE id='$userID' AND isBip=1") or die("Query Error1:".mysql_error());
		$num = mysql_num_rows($result);
		$row;
		if ($num > 0) {
			$contactIndex = 0;
			$resultArr["contacts"] = array();
			while ($row = mysql_fetch_array($result)) {
				$bipArr = array();
				$bipArr["msisdn"] = $row["contactPhone"];
				$bipArr["abID"] = intval($row["abID"]);
				
				$contactUsername = $row["contactPhone"]."@".$xmppDomain;
				$userResult = @mysql_query("SELECT * FROM users WHERE username='$contactUsername'");
				$userRow = mysql_fetch_array($userResult);
				if (strlen($userRow["profileImage"]) > 3) {
					$bipArr["profileUrl"] = $userRow["profileImage"];
				}
				mysql_free_result($userResult);
				//echo "<br> Contact Phone: ".$row["contactPhone"]." ProfileImage: ".$userRow["profileImage"]."<br>";
				$resultArr["contacts"][$contactIndex] = $bipArr;
				$contactIndex++;
				
				
				//Ejabberda rosteritemlari ekler
				$request = "add_rosteritem ".$userPhone." ".$xmppDomain." ".$bipArr["msisdn"]." ".$xmppDomain." Osman Friends both";		
				$opts =  array('http' =>array('method' => "POST",'header' => "Host: localhost\nContent-Type: text/html; charset=utf-8",'content' => $request));
				$context = stream_context_create($opts);
				$fp = fopen($xmppUrl, 'r', false, $context);
					
				if ($fp) {
			
					$response_str = stream_get_contents($fp);
					fclose($fp);
				}
							
			}
			mysql_free_result($result);
			$resultArr["result"] = 0;
		} else {
			$resultArr["result"] = 13;
		}
		
		
		echo json_encode($resultArr,JSON_UNESCAPED_SLASHES);
		
	} else {
		echo "Json Input Error";
	}
} else {
	echo "User Input Error";
}

?>