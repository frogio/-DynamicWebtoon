<?php

include_once "db_manager.php";

define("POST_STATUS_NORMAL", 0, FALSE);
define("REQUSET_UPLOAD_ILLUST","INSERT INTO illust_post VALUES(NULL ,",FALSE);
define("REQUEST_USER_ID_NO","SELECT id_no FROM _USER WHERE nick_name = ",FALSE);


function registerIllust($title, $brief_comment, $img_path, $nick_name){
	$conn = connect_db();
	$hits = 0;
	
	$user_no = getIdNo($nick_name, $conn);
	$query = REQUSET_UPLOAD_ILLUST."\"".$title."\" ,"
								."\"".$brief_comment."\" ,"
								."\"".$img_path."\" ,"
								.POST_STATUS_NORMAL." ,"
								.$hits." ,"
								."now() ,"
								.$user_no.")";
								
	mysqli_autocommit($conn, FALSE);
	

	if ($request_result = mysqli_query($conn, $query)){
	
		if($request_result == false){
			mysqli_rollback($conn);
			mysqli_autocommit($conn, TRUE);
			return FALSE;
		}
		
	}

	mysqli_commit($conn);
	mysqli_autocommit($conn, TRUE);
	release_db($conn);
	
	return TRUE;
}

function getIdNo($nick_name, $conn){
	
	$query = REQUEST_USER_ID_NO.'"'.$nick_name.'"';

	if($request_result = mysqli_query($conn, $query)){
		$row = $request_result->fetch_assoc();
		$no = $row["id_no"];
		mysqli_free_result($request_result);
	}
	
	return $no;
}



?>
