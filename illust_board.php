
<style type="text/css">

 a:link {color: black; text-decoration: none;}
 a:visited {color: black; text-decoration: none;}
 a:hover {color: black; text-decoration: none;}
</style>

<?php

include_once "db_manager.php";

define("ITEM_COUNT_BY_PAGE", 15, FALSE);
define("BLOCK_COUNT", 5, FALSE);

define("REQUEST_ILLUST_COUNT", "SELECT COUNT(post_no) as illust_post_count FROM illust_post;", FALSE);
define("REQUEST_POSTS", 
"SELECT i.post_title, 
			i.contents_dir, 
			i.post_no, 
			i.hits, 
			i.time, 
			u.nick_name 
			from illust_post i, _user u 
			WHERE u.id_no = i.user_no AND
			i.post_status = 0
			ORDER BY i.post_no DESC
			LIMIT "
			, FALSE);
			
define("REQUEST_TOP_5_POSTS", 
"SELECT i.post_title, 
			i.contents_dir, 
			i.post_no, 
			i.hits, 
			i.time, 
			u.nick_name 
			from illust_post i, _user u 
			WHERE u.id_no = i.user_no AND
			i.post_status = 0
			ORDER BY i.hits DESC
			LIMIT "
			, FALSE);
			

function show_top_5_illust($conn){
	
	$query = REQUEST_TOP_5_POSTS."5";

	if($request_result = mysqli_query($conn, $query)){
			// request_result엔 쿼리 결과인 전체 행을 받는다.
			
			if($request_result -> num_rows > 0){
				
				//$illust_count = getPostCountInPage($conn, $query);
				for($i = 0; $i < 5; $i++){
					$row = $request_result->fetch_assoc();
					// 쿼리 결과로 나온 전체 행을 행 한개씩 읽는다.
					
					$title = $row["post_title"];
					$contents_dir = $row["contents_dir"];
					$post_no = $row["post_no"];
					$hits = $row["hits"];
					$time = $row["time"];
					$nick_name = $row["nick_name"];
					$hits = $row['hits'];
					
					$thumb_dir = str_replace("illust","illust_thumb",$contents_dir);
		
					echo '<div class="col mb-5">
									<div class="card">
										<a href = "./view_illust.php?post_no='.$post_no.'">
											<img class="card-img-top" src="'.$thumb_dir.'" alt="..." />
											<div class="card-body p-4">
												<div class="text-center">
													<h6>'.$title.'</h6>
													<h6>작가 '.$nick_name.'</h6>
													<h6>조회수 '.$hits.'</h6>
												</div>
											</div>
										</a>
									</div>
								</div>';
			}
		}
	}
}

function show_illust_list($cur_page, $conn){

	$page_start_no = ($cur_page - 1) * ITEM_COUNT_BY_PAGE;					// 페이지의 시작 번호
	$query = REQUEST_POSTS.$page_start_no.", ".ITEM_COUNT_BY_PAGE;

	if($request_result = mysqli_query($conn, $query)){
			// request_result엔 쿼리 결과인 전체 행을 받는다.
			
			if($request_result -> num_rows > 0){
				
				$illust_count = getPostCountInPage($conn, $query);
				for($i = 0; $i < $illust_count; $i++){
				$row = $request_result->fetch_assoc();
				// 쿼리 결과로 나온 전체 행을 행 한개씩 읽는다.
				
				$title = $row["post_title"];
				$contents_dir = $row["contents_dir"];
				$post_no = $row["post_no"];
				$hits = $row["hits"];
				$time = $row["time"];
				$nick_name = $row["nick_name"];
	
				$thumb_dir = str_replace("illust","illust_thumb",$contents_dir);
	
				echo '<div class="col mb-5">
								<div class="card">
									<a href = "./view_illust.php?post_no='.$post_no.'">
										<img class="card-img-top" src="'.$thumb_dir.'" alt="..." />
										<div class="card-body p-4">
											<div class="text-center">
												<h6>'.$title.'</h6>
												<h6>작가 '.$nick_name.'</h6>
												<h6>조회수 '.$hits.'</h6>
											</div>
										</div>
									</a>
								</div>
							</div>';


			}
		}
		mysqli_free_result($request_result);		
		
	}
	

	
}

function getPostCount($conn){
	
	if($request_result = mysqli_query($conn, REQUEST_ILLUST_COUNT)){
		if($request_result -> num_rows > 0){
			$row = $request_result->fetch_assoc();
			$illust_post_count = $row["illust_post_count"];
			mysqli_free_result($request_result);
		}
	}
	
	return $illust_post_count;
	
}

function getPostCountInPage($conn, $query){
	
	$query_count= "select COUNT(post.post_no) AS illust_count FROM (".$query.") post;";
	
	
	if($request_result = mysqli_query($conn, $query_count)){
		if($request_result -> num_rows > 0){
			$row = $request_result->fetch_assoc();
			$illust_count = $row["illust_count"];
			mysqli_free_result($request_result);
		}
	}
	
	return $illust_count;
}
?>