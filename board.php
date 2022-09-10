<?php

include_once "db_manager.php";

function increase_hits($board_name, $post_no){

	$conn = connect_db();
	
	$is_count = false;
	if (isset($_COOKIE[$board_name."_no_".$post_no]) == false) {    				// 쿠키가 존재하지 않을 때 (해당 일러스트를 처음 볼 때)
		setcookie($board_name."_no_".$post_no, $post_no, time() + 60 * 60 * 24);	// 쿠키를 생성한다.
		$is_count = true;
	}
	
	if ($is_count) {														// 처음보는게 맞다면
		
		$hits_query = "UPDATE ".$board_name."_post set hits = hits + 1 where post_no = ".$post_no;
		
		mysqli_autocommit($conn, FALSE);
	
		if ($request_result = mysqli_query($conn, $hits_query)){			// 조회수를 1 증가시킨다.
		
			if($request_result == false){
				mysqli_rollback($conn);
				mysqli_autocommit($conn, TRUE);
				return FALSE;
			}
		
		}
		
		mysqli_commit($conn);
		mysqli_autocommit($conn, TRUE);
		
	}
	
	release_db($conn);
}
?>