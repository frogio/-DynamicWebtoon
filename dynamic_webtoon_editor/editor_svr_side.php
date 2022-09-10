<?php
	include_once "../thumbnail.php";			// genCode 함수를 실행하기위해 필요
	include_once "../db_manager.php";
	
	session_start();							// 세션변수를 사용하려면 무조건 호출되어야 한다.

	define("UNAVAILABLE_VALUE", -1 , FALSE);

	if(isset($_SESSION["nickname"]))
		$cur_user = $_SESSION["nickname"];
	
	else $cur_user = UNAVAILABLE_VALUE;
	
	if(isset($_SESSION["id_no"]))
		$id_no = $_SESSION["id_no"];
	
	else $id_no = UNAVAILABLE_VALUE;
	
	
	define("TASK_DIRECTORY", "../editor_task_dir/", false);
	define("UPLOAD_DIRECTORY", "../dynamic_webtoon_post/",false);
	
	define("EDITOR_CUT_INFO","editor_cut_info.json", false);
	define("REQUEST_UPLOAD_D_WEBTOON","INSERT INTO dynamic_webtoon_post VALUES(NULL, ",false);
	define("REQUEST_MODIFY_D_WEBTOON", "UPDATE dynamic_webtoon_post SET ",false);
	
	header('Content-Type: text/html; charset=utf-8');
	$operate = file_get_contents('php://input');
	
	if(gettype($operate) == 'string')
		$editor_data = json_decode(stripcslashes($operate), true);			// JSON 데이터인지 확인 JSON 객체가 아니면 NULL 반환

	function make_user_tmp_dir(){

		$rand_folder_name = genCode(20);
		$file_path = TASK_DIRECTORY.$rand_folder_name; // 중복되지 않도록 다시 이름을 설정한다.

		while(is_dir($file_path) == true){		// 디렉토리 이름이 중복되었을 경우
			$rand_folder_name = genCode(20);	
			$file_path = TASK_DIRECTORY.$rand_folder_name; // 중복되지 않도록 다시 이름을 설정한다.
		}
		
		mkdir($file_path);
		return $rand_folder_name;								// 폴더 이름을 리턴한다.
	}
	
	function make_new_dir($dir){
		
		$upload_dir = rtrim(UPLOAD_DIRECTORY.$dir, '/');
		mkdir($upload_dir);			// Upload할 시 저장할 디렉토리를 만들어놓는다.
		
		return $upload_dir;
	}
	
	function upload_d_webtoon($dir, $json, $id_no){
		$task_dir = TASK_DIRECTORY.$dir;
		file_put_contents($task_dir.EDITOR_CUT_INFO, $json);	// marker정보를 갖는 JSON 파일을 저장한다 (후의 게시글 수정을 위한)
		
		$data =  json_decode(stripcslashes($json), true);
		$title = $data['title'];
		$brief_comment = $data['brief_comment'];
		
		$conn = connect_db();
		
		$query = REQUEST_UPLOAD_D_WEBTOON."'".$title."', "
										."'".$brief_comment."', "
										."'".UPLOAD_DIRECTORY.$dir."', "
										."'".$task_dir."', "
										."0 ,"
										."0 ,"
										."now(), "
										.$id_no.", "			// user_no
										."0, "					// category_no
										."1)";					// genre_no
										
										

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
	
	function modify_d_webtoon($dir, $json){						// dynamic_webtoon의 게시글을 수정하는 함수
		$post_dir = rtrim(UPLOAD_DIRECTORY.$dir, '/');

		$dir_handle = opendir($post_dir);						// 업데이트할 웹툰의 디렉토리 내부 파일들을 모두 지운다.
		while ($itemName = readdir($dir_handle)) {
			
			if($itemName == '.' || $itemName == '..')
				continue;
			unlink($post_dir.'/'.$itemName);
			
		}
		closedir($dir_handle);
		// 해당 post 경로의 파일들을 모두 지운다.
		
		$task_dir = TASK_DIRECTORY.$dir;
		file_put_contents($task_dir.EDITOR_CUT_INFO, $json);	// marker정보를 갖는 JSON 파일을 업데이트한다 (후의 게시글 수정을 위한)

		$conn = connect_db();
		$data =  json_decode(stripcslashes($json), true);
		$title = $data['title'];
		$brief_comment = $data['brief_comment'];
		$post_no = $data['post_no'];
		
		$query = REQUEST_MODIFY_D_WEBTOON.'post_title = "'.$title.'", '.
												'brief_comment = "'.$brief_comment.'", '.
												'time = NOW() '.
												'WHERE post_no = '.$post_no.';';		// post_no?
	
		
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
		
		return $query;

	}
	
	if($operate == 'make_user_tmp_dir')							// 에디터의 임시 작업 디렉토리를 만든다.
		echo make_user_tmp_dir();

	else if($editor_data == NULL){								// editor의 이미지 한컷을 저장한다.
	
		$task_folder = $_POST['task_folder'];
		$image_name = $_FILES["image"]["name"];
		$frame = $_FILES["image"]["tmp_name"];
		$rand_name = genCode(20);								// 50문자 길이의 파일이름을 만든다.
		$ext = substr(strrchr($image_name, '.'), 1);									// 확장자 추출
		$file_path = TASK_DIRECTORY.$task_folder.$rand_name.".".$ext;
		
		while(file_exists($file_path) == true){					// 파일이 중복되었을 경우
		
			$rand_name = genCode(20);
			$ext = substr(strrchr($image_name, '.'), 1);		
			$file_path = TASK_DIRECTORY.$task_folder.$rand_name.".".$ext;
																// 중복되지 않도록 다시 이름을 설정한다.
		}
		
		move_uploaded_file($frame, $file_path);					// 현재 경로에 입력받은 이미지 파일을 저장한다.
		echo $file_path;
	
	}
	
	else if($editor_data != NULL && $editor_data['request_new_dir'] == TRUE){		// json 객체(markerJSONList)이면서 새로운 글을 업로드 할 경우
		echo make_new_dir($editor_data['task_dir']);
	}
	
	else if($editor_data != NULL && $editor_data['isModified'] != "modify"){		// json 객체(markerJSONList)이면서 새로운 글을 업로드 할 경우
		echo upload_d_webtoon($editor_data['task_dir'], $operate, $id_no);
	}
	
	else if($editor_data != NULL && $editor_data['isModified'] == "modify"){		// json 객체(markerJSONList)이면서 기존 글을 변경할 경우
		echo modify_d_webtoon($editor_data['task_dir'], $operate);
	}
	
	
?>