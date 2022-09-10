
<!--<style type="text/css">

 a:link {color: black; text-decoration: none;}
 a:visited {color: black; text-decoration: none;}
 a:hover {color: black; text-decoration: none;}
</style>-->

<?php

include_once "db_manager.php";

define("ITEM_COUNT_BY_PAGE", 15, FALSE);
define("BLOCK_COUNT", 5, FALSE);

define("REQUEST_WEBTOON_COUNT", "SELECT COUNT(post_no) AS webtoon_post_count FROM dynamic_webtoon_post WHERE post_status = 0;", FALSE);
define("REQUEST_POSTS", 
"SELECT d.post_title, 
			d.contents_dir, 
			d.post_no, 
			d.hits, 
			d.time, 
			u.nick_name 
			from dynamic_webtoon_post d, _user u 
			WHERE u.id_no = d.user_no AND
			d.post_status = 0
			ORDER BY d.post_no DESC
			LIMIT "
			, FALSE);

define("REQUEST_TOP_5_WEBTOON", 
"SELECT d.post_title, 
			d.contents_dir, 
			d.post_no, 
			d.hits, 
			d.time, 
			u.nick_name 
			from dynamic_webtoon_post d, _user u 
			WHERE u.id_no = d.user_no AND
			d.post_status = 0
			ORDER BY d.hits DESC
			LIMIT "
			, FALSE);


define("REQUEST_SEARCH_BY_NICKNAME", 
"SELECT d.post_title, 
			d.contents_dir, 
			d.post_no, 
			d.hits, 
			d.time, 
			u.nick_name 
			from dynamic_webtoon_post d, _user u 
			WHERE u.id_no = d.user_no AND
			u.nick_name = $ AND
			d.post_status = 0
			ORDER BY d.post_no DESC
			LIMIT "
			, FALSE);




function search_posts_by_nick_name($nick_name, $cur_page, $conn){

	$query = REQUEST_SEARCH_BY_NICKNAME.ITEM_COUNT_BY_PAGE;
	$query = str_replace("$", "'".$nick_name."'", $query);
	
	echo $query;
	
}


function show_top_5_webtoon($conn){
	
	$query = REQUEST_TOP_5_WEBTOON."5";

	if($request_result = mysqli_query($conn, $query)){
			// request_result엔 쿼리 결과인 전체 행을 받는다.
			
			if($request_result -> num_rows > 0){
				for($i = 0; $i < 5; $i++){
					$row = $request_result->fetch_assoc();
					// 쿼리 결과로 나온 전체 행을 행 한개씩 읽는다.
					
					$title = $row["post_title"];
					$contents_dir = $row["contents_dir"];
					$post_no = $row["post_no"];
					$hits = $row["hits"];
					$time = $row["time"];
					$nick_name = $row["nick_name"];
					$hits = $row["hits"];
		
					$dir = str_replace('../','./',$contents_dir);

					$isThumbExist = file_exists($dir."thumbnail.jpg");
					
					if($isThumbExist)
						$thumb_dir = $dir."thumbnail.jpg";

					$isThumbExist = file_exists($dir."thumbnail.png");
					
					if($isThumbExist)
						$thumb_dir = $dir."thumbnail.png";

					$isThumbExist = file_exists($dir."thumbnail.gif");
					
					if($isThumbExist)
						$thumb_dir = $dir."thumbnail.gif";
		
					echo '<div class="col mb-5">
									<div class="card">
										<a href = "./dynamic_webtoon_player/dynamic_webtoon_player.php?post_no='.$post_no.'">
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
					$thumb_dir = "./img/default_img.jpg";
			}
		}
	}
}


function show_webtoon_list($cur_page, $conn){

	$page_start_no = ($cur_page - 1) * ITEM_COUNT_BY_PAGE;					// 페이지의 시작 번호
	$query = REQUEST_POSTS.$page_start_no.", ".ITEM_COUNT_BY_PAGE;

	if($request_result = mysqli_query($conn, $query)){
			// request_result엔 쿼리 결과인 전체 행을 받는다.
			
			if($request_result -> num_rows > 0){
				
				$illust_count = w_getPostCountInPage($conn, $query);
				for($i = 0; $i < $illust_count; $i++){
					$row = $request_result->fetch_assoc();
					// 쿼리 결과로 나온 전체 행을 행 한개씩 읽는다.
					
					$title = $row["post_title"];
					$contents_dir = $row["contents_dir"];
					$post_no = $row["post_no"];
					$hits = $row["hits"];
					$time = $row["time"];
					$nick_name = $row["nick_name"];
					#$hits = $row["hits"];
					
					$dir = str_replace('../','./',$contents_dir);

					$isThumbExist = file_exists($dir."thumbnail.jpg");
					
					if($isThumbExist)
						$thumb_dir = $dir."thumbnail.jpg";

					$isThumbExist = file_exists($dir."thumbnail.png");
					
					if($isThumbExist)
						$thumb_dir = $dir."thumbnail.png";

					$isThumbExist = file_exists($dir."thumbnail.gif");
					
					if($isThumbExist)
						$thumb_dir = $dir."thumbnail.gif";


					// 임시로 디폴트 이미지를 썸네일로 두었다.
		
					echo '<div class="col mb-5">
									<div class="card">
										<a href = "./dynamic_webtoon_player/dynamic_webtoon_player.php?post_no='.$post_no.'">
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
					$thumb_dir = "./img/default_img.jpg";

			}
		}
		mysqli_free_result($request_result);		
		
	}
	

	
}

function w_getPostCount($conn){
	
	if($request_result = mysqli_query($conn, REQUEST_WEBTOON_COUNT)){
		if($request_result -> num_rows > 0){
			$row = $request_result->fetch_assoc();
			$illust_post_count = $row["webtoon_post_count"];
			mysqli_free_result($request_result);
		}
	}
	
	return $illust_post_count;
	
}

function w_getPostCountInPage($conn, $query){
	
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