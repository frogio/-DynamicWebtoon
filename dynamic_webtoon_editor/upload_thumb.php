<?php

include_once("../thumbnail.php");

if($_SERVER['REQUEST_METHOD'] == 'POST'){
	
	$task_dir = $_POST["task_dir"];
	$default_thumbnail = $_POST["default_thumbnail"];
	$img_name = $_FILES["thumbnail"]["name"];
	$img_file = $_FILES["thumbnail"]["tmp_name"];

	if($img_name == ""){												// 썸네일 이미지가 올려져 있지 않을 경우
	
		$ext = pathinfo($default_thumbnail, PATHINFO_EXTENSION);
		$thumbnail_dir = "../dynamic_webtoon_post/".$task_dir."thumbnail".'.'.$ext;

		$info = getimagesize($default_thumbnail);
		
		if ($info['mime'] == 'image/jpeg')
			$image = imagecreatefromjpeg($default_thumbnail);
		
		else if ($info['mime'] == 'image/gif')
			$image = imagecreatefromgif($default_thumbnail);
		
		else if ($info['mime'] == 'image/png')
			$image = imagecreatefrompng($default_thumbnail);
		
		make_dynamic_default_thumb($info, $image, $thumbnail_dir, 100);			// 디폴트 썸네일을 생성한다.
	
		echo $thumbnail_dir;
	
	}
	
	else {
		
		$ext = substr(strrchr($img_name, '.'), 1);
		$thumbnail_dir = "../dynamic_webtoon_post/".$task_dir."thumbnail".'.'.$ext;
		make_thumb($img_file, $thumbnail_dir, 100);						// 썸네일을 생성한다.
	
	}

}



?>