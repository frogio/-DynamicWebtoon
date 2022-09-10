<?php

// php에서 ajax에게 응답을 할 경우 echo를 사용하여 응답한다.
// 따라서 echo를 이용해 자바스크립트 문장을 실행시킬 수 없다. 자바스크립트 코드 자체가 텍스트 데이터로 리턴되기 떄문이다.

include_once 'db_manager.php';

$post_no = $_GET['no'];
$post_type = $_GET['type'];

if(isset($_GET['cur_user']))
	$cur_user = $_GET['cur_user'];

if(isset($_GET['id_no']))
	$id_no = $_GET['id_no'];

if(isset($_GET['comment']))
	$comment = $_GET['comment'];

if(isset($_GET['operate']))
	$operate = $_GET['operate'];

if(isset($_GET['comment_no']))
	$comment_no = $_GET['comment_no'];



define("REQUEST_COMMENT",'SELECT	c.comment_no,
									c.comment_, 
									c.time,
									u.nick_name
									FROM illust_comment c, _user u 
									WHERE c.post_no = '.$post_no.' AND
									c.user_no = u.id_no AND
									c.state = 0
									ORDER BY c.comment_no DESC;', FALSE);

define("REQUSET_REGISTER_COMMENT","INSERT INTO illust_comment VALUES(NULL, ",FALSE);									
define("REQUEST_LATEST_COMMENT_NO", "SELECT max(comment_no) AS cmt_cnt from illust_comment;" ,FALSE); 
define("REQUEST_DELETE_COMMENT", "UPDATE illust_comment SET state = -1 WHERE comment_no = ",FALSE);
define("REQUEST_UPDATE_COMMENT", "UPDATE illust_comment SET comment_ = ",FALSE);


define("DELETE_STATE", -1, FALSE);

function get_illust_comment(){
	$conn = connect_db();
	$comment_array = array();		// 배열을 생성한다.
	
	$query = REQUEST_COMMENT;
	if($request_result = mysqli_query($conn, $query)){
		if($request_result->num_rows > 0){
			for($i = 0; $i < $request_result->num_rows; $i++){
				$row = $request_result->fetch_assoc();
				$json = array("comment_no" => $row['comment_no'], "comment" => $row['comment_'], "time" => $row['time'], "nick_name" => $row['nick_name']);
				array_push($comment_array, $json);
			}
		mysqli_free_result($request_result);
		}
	}
	
	$result = json_encode($comment_array);
	release_db($conn);	
	return $result;
}


function register_comment($post_no, $cur_user, $id_no, $comment){
	$conn = connect_db();
	$query = REQUSET_REGISTER_COMMENT."'".$comment."'".', NOW(), '.'0, '.$post_no.", ".$id_no.');';
	
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
	
	$request_result = mysqli_query($conn, REQUEST_LATEST_COMMENT_NO);
	if($request_result->num_rows > 0){
		$row = $request_result->fetch_assoc();
		$latest_comment_no = $row['cmt_cnt'];
	}
	
	
	release_db($conn);
	return 	$latest_comment_no;
	// 등록할 때마다 계속 번호의 최대값을 리턴하여 가장 최신의 댓글 번호를 가져온다
}

function delete_comment($comment_no){
	$conn = connect_db();
	$query = REQUEST_DELETE_COMMENT.$comment_no.';';
	
	// 삭제된 코멘트의 state 컬럼값을 -1로 둔다.
	
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

function update_comment($comment_no, $comment){
	$conn = connect_db();
	$query = REQUEST_UPDATE_COMMENT."'".$comment."'"." WHERE comment_no = ".$comment_no.";";
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


if($operate == 'delete')
	echo delete_comment($comment_no);

else if($operate == 'update')
	echo update_comment($comment_no, $comment);

if($post_type == 'illust')
	echo get_illust_comment();
// JSON 객체가 아니라 String 타입으로 리턴된다.

else if($post_type == 'illust_comment')
	echo register_comment($post_no, $cur_user, $id_no, $comment);
	
else if($post_type == 'd_webtoon')
	return;

?>