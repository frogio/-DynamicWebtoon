<?php 

include_once "db_manager.php";

define("REQUEST_LOGOUT", 1, FALSE);


header('Content-Type: text/html; charset=utf-8');
$request_login = file_get_contents('php://input');


define("REQUSET_REGISTER","INSERT INTO _user VALUES(NULL ,",FALSE);
define("REQUEST_REGISTER_ID_OVERLAPPED", "SELECT id FROM _user WHERE id = ",FALSE);
define("REQUEST_REGISTER_NICKNAME_OVERLAPPED", "SELECT nick_name FROM _user WHERE nick_name = ",FALSE);
define("REQUEST_LOGIN","SELECT id, pw FROM _user WHERE id = ", FALSE);
define("REQUEST_USER_INFO","SELECT id_no, id, nick_name, e_mail FROM _user WHERE id = ", FALSE);
// query

define("REGISTER_FLAG_NO_ERROR", 0 , FALSE);
define("REGISTER_FLAG_ACCEPT", 1 , FALSE);
define("REGISTER_FLAG_ID_OVERLAPPED", -1, FALSE);
define("REGISTER_FLAG_NICKNAME_OVERLAPPED", -2, FALSE);


define("LOGIN_FLAG_NO_ERROR", 0, FALSE);
define("LOGIN_FLAG_ACCEPT", 1, FALSE);
define("LOGIN_FLAG_UNAVAILABLE", -1, FALSE);

function register_request($user_id, $user_pw, $nick_name, $email, $permission){
	
	$result = REGISTER_FLAG_NO_ERROR;
	$conn = connect_db();

	$regiseter_query = REQUSET_REGISTER."\"".$user_id."\" ,"
								."\"".$user_pw."\" ,"
								."\"".$nick_name."\" ,"
								."\"".$email."\" ,"
								.$permission.")";
	
	$check_id_overlapped = REQUEST_REGISTER_ID_OVERLAPPED."\"".$user_id."\"";	
	$check_nick_name_overlapped = REQUEST_REGISTER_NICKNAME_OVERLAPPED."\"".$nick_name."\"";
	
	
	if($request_result = mysqli_query($conn, $check_id_overlapped)){
		
		if($request_result -> num_rows > 0){
			$result = REGISTER_FLAG_ID_OVERLAPPED;
			mysqli_free_result($request_result);
		}
	}

	
	if($request_result = mysqli_query($conn, $check_nick_name_overlapped)){
		
		if($request_result -> num_rows > 0){
			$result = REGISTER_FLAG_NICKNAME_OVERLAPPED;	
			mysqli_free_result($request_result);					// BOOL형 값을 mysqli_free_result에 넣지 말것!...
		}
	}
	
	if ($result == REGISTER_FLAG_NO_ERROR && $request_result = mysqli_query($conn, $regiseter_query))
		$result = REGISTER_FLAG_ACCEPT;	
	
	release_db($conn);
	return $result;	
	
}


function login_request($id, $pw){
	
	$result = LOGIN_FLAG_NO_ERROR;
	
	$conn = connect_db();
	$query = REQUEST_LOGIN."\"".$id."\"";

	if($request_result = mysqli_query($conn, $query)){
		
		if($request_result -> num_rows == 0){				// ID가 존재하지 않을 때
			$result = LOGIN_FLAG_UNAVAILABLE;	
			mysqli_free_result($request_result);
		}

		else{												// ID가 존재할 때
			$row = $request_result->fetch_assoc();
			$c_pw = $row["pw"];
			mysqli_free_result($request_result);
			
			if (password_verify($pw ,$c_pw))				// PW까지 일치하면 승인
				$result = LOGIN_FLAG_ACCEPT;
				
			else 											// 그렇지 못할 경우 무효처리
				$result = LOGIN_FLAG_UNAVAILABLE;		
		}
		
	}
	
	release_db($conn);
	return $result;
}

function get_user_info($id){
	
	$conn = connect_db();
	$query = REQUEST_USER_INFO."\"".$id."\"";

	if($request_result = mysqli_query($conn, $query)){
		
		$row = $request_result->fetch_assoc();
		
		$info["id"] = $row["id"];
		$info["nickname"] = $row["nick_name"];
		$info["email"] = $row["e_mail"];
		$info["id_no"] = $row["id_no"];

		mysqli_free_result($request_result);
		release_db($conn);	
		return $info;
	}
	
	else return -1;
	
}

if($request_login != "LogOut"){


	$login_info = json_decode(stripcslashes($request_login), true);
	
	//echo "isWorking?";
	// echo는 Ajax의 응답으로 작동한다. 즉, 모든 처리를 끝낸 뒤에야 echo를 마지막으로 써야한다.
	
	$id = $login_info["id"];
	$pw = $login_info["pw"];
	
	$result = login_request($id, $pw);
	
	if($result == LOGIN_FLAG_ACCEPT){
		
		$user_info = get_user_info($id);
	
		session_start();
	
		$_SESSION["isActivate"] = 1;
		$_SESSION["user_id"] = $user_info["id"];
		$_SESSION["nickname"] = $user_info["nickname"];
		$_SESSION["email"] = $user_info["email"];
		$_SESSION["id_no"] = $user_info["id_no"];
		
		echo $result;	
	}
	

}
else{
	
	session_start();							// 세션변수를 사용하려면 무조건 호출되어야 한다.
	session_unset();
	session_destroy();
	echo REQUEST_LOGOUT;
	
}

//echo $result;

?>