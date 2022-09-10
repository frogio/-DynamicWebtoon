<?php

define("MAX_THUMB_HEIGHT", 450, FALSE);

function make_thumb($source, $destination, $quality) {

    $info = getimagesize($source);

    if ($info['mime'] == 'image/jpeg')
        $image = imagecreatefromjpeg($source);

    elseif ($info['mime'] == 'image/gif')
        $image = imagecreatefromgif($source);

    elseif ($info['mime'] == 'image/png')
        $image = imagecreatefrompng($source);
		
	$image_width = $info[0];
	$image_height = $info[1];

	$thumb_width = 300;
	
	$ratio = $image_height / $image_width;								// 1 width 당 몇 height 인가?	
	$thumb_height = $thumb_width * $ratio; 	
	$dst_height = 0;													// 이미지 높이가 최대 높이보다 작을 때 사용될 중앙정렬 좌표
	$original_height = $thumb_width * $ratio;							// 이미지의 비율을 유지한 높이값
	$crop_size = 0;														// 최대 높이가 더 클 경우 자를 사이즈
	
	if($thumb_height > MAX_THUMB_HEIGHT)								// 만약 일러스트 이미지의 높이값이 최대 높이보다 클 경우
		$crop_size = $thumb_height - MAX_THUMB_HEIGHT;					// 자를 크기를 계산한다.
		
	else if($thumb_height < MAX_THUMB_HEIGHT){							// 만약 일러스트가 최대 높이보다 작을경우
		$thumb_height = MAX_THUMB_HEIGHT;								// 빈 이미지크기를 최대 높이로 설정한다.	
		$dst_height = (MAX_THUMB_HEIGHT - $original_height) / 2;		// 이미지 중앙정렬을 위한 좌표
	} 

    $dst = imagecreatetruecolor($thumb_width, $thumb_height - $crop_size);		// 빈 이미지를 만든다.
	$back_ground = imagecolorallocate($dst, 240, 240, 240);						// 백그라운드 이미지 객체를 생성한다.
	imagefill($dst, 0, 0, $back_ground);										// 백그라운드를 적용한다.
	
	imagecopyresampled($dst, $image, 0, $dst_height, 0, 0, $thumb_width, $original_height, $image_width, $image_height);
	// 빈 이미지에 사이즈 축소한 이미지를 저장한다.

	imagejpeg($dst, $destination, $quality);
    // 최종 결과물을 저장한다.
	
	return $destination;

}

function make_dynamic_default_thumb($info, $image, $destination, $quality) {
	
	$image_width = $info[0];
	$image_height = $info[1];

	$thumb_width = 300;
	
	$ratio = $image_height / $image_width;								// 1 width 당 몇 height 인가?	
	$thumb_height = $thumb_width * $ratio; 	
	$dst_height = 0;													// 이미지 높이가 최대 높이보다 작을 때 사용될 중앙정렬 좌표
	$original_height = $thumb_width * $ratio;							// 이미지의 비율을 유지한 높이값
	$crop_size = 0;														// 최대 높이가 더 클 경우 자를 사이즈
	
	if($thumb_height > MAX_THUMB_HEIGHT)								// 만약 일러스트 이미지의 높이값이 최대 높이보다 클 경우
		$crop_size = $thumb_height - MAX_THUMB_HEIGHT;					// 자를 크기를 계산한다.
		
	else if($thumb_height < MAX_THUMB_HEIGHT){							// 만약 일러스트가 최대 높이보다 작을경우
		$thumb_height = MAX_THUMB_HEIGHT;								// 빈 이미지크기를 최대 높이로 설정한다.	
		$dst_height = (MAX_THUMB_HEIGHT - $original_height) / 2;		// 이미지 중앙정렬을 위한 좌표
	} 

    $dst = imagecreatetruecolor($thumb_width, $thumb_height - $crop_size);		// 빈 이미지를 만든다.
	$back_ground = imagecolorallocate($dst, 240, 240, 240);						// 백그라운드 이미지 객체를 생성한다.
	imagefill($dst, 0, 0, $back_ground);										// 백그라운드를 적용한다.
	
	imagecopyresampled($dst, $image, 0, $dst_height, 0, 0, $thumb_width, $original_height, $image_width, $image_height);
	// 빈 이미지에 사이즈 축소한 이미지를 저장한다.

	imagejpeg($dst, $destination, $quality);
    // 최종 결과물을 저장한다.
	
	return $destination;
	
}

function genCode($s){			// 랜덤으로 파일 이름을 정해주는 함수 $s는 이름 길이

	$arx = "ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890abcdefghijklmnopqrstuvwxyz";
	$rt = "";
	for($i=0; $i < $s; $i++){
		$rn = rand(0, strlen($arx));
		$rt .= substr($arx, $rn, 1);
	}
	return $rt;
}


?>