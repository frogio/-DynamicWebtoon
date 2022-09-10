<?php

include_once 'db_manager.php';
include_once 'thumbnail.php';

header('Content-Type: text/html; charset=utf-8');
if($delete_operate = file_get_contents('php://input')){				// JSON을 ajax post로 받는다.
	
	$post_info = json_decode(stripcslashes($delete_operate), true);
	
	$post_no = $post_info['post_no'];
	$operate = $post_info['operate'];
	$post_type = $post_info['type'];
	
}

else if(isset($_POST['modify_operate'])){							// 수정 연산
	
	$operate = $_POST['modify_operate'];
	$title = $_POST['title'];
	$brief_comment = $_POST['brief_comment'];
	$post_no = $_POST['post_no'];
	$post_type = $_POST['type'];
	
}


define("REQUEST_ILLUST_POST_DELETE", "UPDATE illust_post SET post_status = -1 WHERE post_no =  ", FALSE);
define("REQUEST_ILLUST_POST_DELETE_ALL_CMT", "UPDATE illust_comment SET state = -1 WHERE post_no = ",FALSE);
define("REQUEST_ILLUST_POST_UPDATE", "UPDATE illust_post SET ",FALSE);

define("REQUEST_D_WEBTOON_POST_DELETE", "UPDATE dynamic_webtoon_post SET post_status = -1 WHERE post_no =  ", FALSE);
define("REQUEST_D_WEBTOON_POST_DELETE_ALL_CMT", "UPDATE dynamic_webtoon_comment SET state = -1 WHERE post_no = ",FALSE);

function delete_dynamic_webtoon_post($post_no){
	$conn = connect_db();
	
	$commnet_query = REQUEST_D_WEBTOON_POST_DELETE_ALL_CMT.$post_no.";";
	mysqli_autocommit($conn, FALSE);
	
	if ($request_result = mysqli_query($conn, $commnet_query)){
	
		if($request_result == false){
			mysqli_rollback($conn);
			mysqli_autocommit($conn, TRUE);
			return FALSE;
		}
		
	}
	mysqli_commit($conn);

	
	$post_query = REQUEST_D_WEBTOON_POST_DELETE.$post_no.";";
	if ($request_result = mysqli_query($conn, $post_query)){
	
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


function delete_illust_post($post_no){
	$conn = connect_db();
	
	$commnet_query = REQUEST_ILLUST_POST_DELETE_ALL_CMT.$post_no.";";
	mysqli_autocommit($conn, FALSE);
	
	if ($request_result = mysqli_query($conn, $commnet_query)){
	
		if($request_result == false){
			mysqli_rollback($conn);
			mysqli_autocommit($conn, TRUE);
			return FALSE;
		}
		
	}
	mysqli_commit($conn);

	
	$post_query = REQUEST_ILLUST_POST_DELETE.$post_no.";";
	if ($request_result = mysqli_query($conn, $post_query)){
	
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

function update_illust($title, $brief_comment, $post_no){
	
	$query = "";
	
	if($_FILES["illust"]["error"] != 4){		// POST로 받은 파일이 비어있지 않을 경우 (일러스트 이미지를 변경했을 경우)
		
		$illust_file_name= $_FILES["illust"]["name"];
		$illust_file = $_FILES["illust"]["tmp_name"];
		// 새로운 일러스트의 정보를 가져온다.
		
		if($title == ""){
			echo '<script>alert("제목을 입력해주세요");</script>';
			return FALSE;
		}
		
		else {	
			$rand_name = genCode(50);					// 50문자 길이의 파일이름을 만든다.
			$ext = substr(strrchr($illust_file_name, '.'), 1);									// 확장자 추출
			$file_path = "./illust/".$rand_name.".".$ext;
			
			while(file_exists($file_path) == true){		// 파일명이 중복되었을 경우
			
				$rand_name = genCode(50);
				$ext = substr(strrchr($illust_file_name, '.'), 1);		
				$file_path = "./illust/".$rand_name.".".$ext;
														// 중복되지 않도록 다시 이름을 설정한다.
			}
			// 현재 경로를 나타내는 .을 붙여야 제대로 저장이 된다.
			
	
			$thumb_path = "./illust_thumb/".$rand_name.".".$ext;
			make_thumb($illust_file, $thumb_path, 100);						// 썸네일을 생성한다.
			move_uploaded_file($illust_file, $file_path);					// 현재 경로에 입력받은 이미지 파일을 저장한다.		
			
			
			$query = REQUEST_ILLUST_POST_UPDATE.'post_title = "'.$title.'", '.
						'brief_comment = "'.$brief_comment.'", '.
						'contents_dir = "'.$file_path.'", '.
						'time = NOW()'.', '.
						'hits = 0 '.
						'WHERE post_no = '.$post_no.';';
			
		}
		
	}
	else if($_FILES["illust"]["error"] == 4){								// 일러스트 이미지를 변경하지 않았을 경우 일러스트 경로 컬럼 업데이트 제외
			
		$query = REQUEST_ILLUST_POST_UPDATE.'post_title = "'.$title.'", '.
									'brief_comment = "'.$brief_comment.'", '.
									'time = NOW()'.', '.
									'hits = 0 '.
									'WHERE post_no = '.$post_no.';';
									
									
	}
	$conn = connect_db();

									
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
	echo '<script>alert("수정 완료");document.location.href="/illust_list.php";</script>';
	return TRUE;
}

if($operate == 'delete' && $post_type == 'illust')
	echo delete_illust_post($post_no);

if($operate == 'modify' && $post_type == 'illust')
	echo update_illust($title, $brief_comment, $post_no);

if($operate == 'delete' && $post_type == 'dynamic_webtoon')
	echo delete_dynamic_webtoon_post($post_no);


?>