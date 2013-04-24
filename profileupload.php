<?php
require_once 'config.php';

function makeThumbnails($origFile,$thumbFile)
{
    $thumbnail_width = 80;
    $thumbnail_height = 80;
    $arr_image_details = getimagesize($origFile); // pass id to thumb name
    $original_width = $arr_image_details[0];
    $original_height = $arr_image_details[1];
    if ($original_width > $original_height) {
        $new_width = $thumbnail_width;
        $new_height = intval($original_height * $new_width / $original_width);
    } else {
        $new_height = $thumbnail_height;
        $new_width = intval($original_width * $new_height / $original_height);
    }
    $dest_x = intval(($thumbnail_width - $new_width) / 2);
    $dest_y = intval(($thumbnail_height - $new_height) / 2);
    if ($arr_image_details[2] == 1) {
        $imgt = "ImageGIF";
        $imgcreatefrom = "ImageCreateFromGIF";
    }
    if ($arr_image_details[2] == 2) {
        $imgt = "ImageJPEG";
        $imgcreatefrom = "ImageCreateFromJPEG";
    }
    if ($arr_image_details[2] == 3) {
        $imgt = "ImagePNG";
        $imgcreatefrom = "ImageCreateFromPNG";
    }
    if ($imgt) {
        $old_image = $imgcreatefrom($origFile);
        $new_image = imagecreatetruecolor($thumbnail_width, $thumbnail_height);
        imagecopyresized($new_image, $old_image, $dest_x, $dest_y, 0, 0, $new_width, $new_height, $original_width, $original_height);
        $imgt($thumbFile);
        return true;
    }
    
    return false;
}

$userInput = $_POST["json"];
$profileImage = $_FILES["file"];
$resultArr = array();

if (isset($userInput) && isset($profileImage)) {
	$jsonInput = json_decode($userInput,true);
	$id = $jsonInput["id"];
	$nickName = $jsonInput["nickname"];
	
	$letters = 'abcdefghi1234567890';
	$filePrefix = str_shuffle($letters);
	
	$fileTmpName = $profileImage["tmp_name"];
	$fileExt = pathinfo($profileImage["name"],PATHINFO_EXTENSION);
	$thumbFileName = $filePrefix.".".$fileExt;
	$originalFileName = "orig_".$thumbFileName;
	
	$originalFilePath = $uploadsDir."/".$originalFileName;
	$thumbnailFilePath = $uploadsDir."/".$thumbFileName;
	
	
	if (move_uploaded_file($fileTmpName, $originalFilePath)) {
		if (makeThumbnails($originalFilePath, $thumbnailFilePath)) {
			@mysql_query("UPDATE users set nickname=$nickName,profileImage=$thumbnailFilePath WHERE id=$id") or die("Update Query Error:".mysql_error());
			$resultArr["resultCode"] = 0;
			$resultArr["fileID"] = $thumbnailFilePath;			
		} else {
			echo "Creating Thumbnail Failed";
		}

	} else {
		$resultArr["resultCode"] = 9;
	}
	
	echo json_encode($resultArr);
	
} else {
	echo "Missing Input Error";
}



?>