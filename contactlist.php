<?php
require_once 'config.php';
//$userInput = urldecode(file_get_contents('php://input'));
$userInput = $_POST["userInput"];


if (isset($userInput)) {
	$jsonInput = json_decode($userInput,true);	
	$resultArr = array();
	$resultArr["result"] = 7;
	
	if (isset($jsonInput["id"])) {
		@mysql_connect($databaseAddr,$databaseUser,$databaseUserPass) or die("Database Connection Error");
		@mysql_select_db($databaseName) or die("Database Selection Error");
		
		$userID = $jsonInput["id"];
		$listType = $jsonInput["allList"];
		
		$result = @mysql_query("SELECT users.profileImage,contacts.contactPhone FROM users,contacts WHERE contacts.id='$userID' AND users.id='$userID' AND contacts.isBip=1") or die("Query Error1:".mysql_error());
		$num = mysql_num_rows($result);
		$row;
		if ($num > 0) {
			$contactIndex = 0;
			$resultArr["contacts"] = array();
			while ($row = mysql_fetch_array($result)) {
				$bipArr = array();
				$bipArr["msisdn"] = $row["contactPhone"];
				if ($row["profileImage"]) {
					$bipArr["profileUrl"] = $row["profileImage"];
				}
				echo "<br> Contact Phone: ".$row["contactPhone"]." ProfileImage: ".$row["profileImage"]."<br>";
				$row["contacts"][$contactIndex] = $bipArr;
				$contactIndex++;
			}
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