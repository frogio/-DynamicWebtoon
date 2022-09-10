<?php

include_once "session.php";
include_once "site_address_info.php";

define("USER_PERMISSION_ADMIN", 0, FALSE);
define("USER_PERMISSION_NORMAL", 1, FALSE); 

header('Content-Type: text/html; charset=utf-8');
$request_body = file_get_contents('php://input');
$register_info = json_decode(stripcslashes($request_body), true);

$user_id = $register_info["id"];
$user_pw = $register_info["pw"];
$nick_name = $register_info["nick_name"];
$email = $register_info["e_mail"];
$permission = USER_PERMISSION_NORMAL;

$encrypted_pw = password_hash($user_pw, PASSWORD_DEFAULT);

$register_result = register_request($user_id, $encrypted_pw, $nick_name, $email, $permission);

echo $register_result;		// 실행 결과를 ajax에 전달한다.

?>