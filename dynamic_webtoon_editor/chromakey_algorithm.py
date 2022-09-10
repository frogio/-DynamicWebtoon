import cv2
import json
import fileinput

def mask_algorithm(resize_frame, resize_background, marker):
	
	#f = open("inspector.txt","w")

	hsv = cv2.cvtColor(resize_frame, cv2.COLOR_BGR2HSV)
	mask = cv2.inRange(hsv, (50, 150, 0), (70, 255, 255))			# 초록색 성분만 검출, 즉 초록색 부분이 1 그 외 부분은 0
	mask_inv = cv2.bitwise_not(mask)
	#mask_inv = cv2.morphologyEx(mask_inv, cv2.MORPH_CLOSE, None)	# 모폴로지 닫기 연산을 통해 내부에 생긴 구멍을 메꾼다.
	#mask = cv2.bitwise_not(mask_inv)								# 만들어진 깨끗한 마스크(mask_inv)로 반전된 새로운 마스크를 생성한다.
	resize_background_cut = resize_background[marker['top'] : marker['height'] + marker['top'], marker['left'] : marker['width'] + marker['left']]
	# roi (region of interest)영역을 설정한다.
	
	img2 = cv2.bitwise_and(resize_background_cut, resize_background_cut, mask=mask)
	
	if(marker['label'] != 'focus_line'):
		img1 = cv2.bitwise_and(resize_frame, resize_frame, mask=mask_inv)
		tmp = cv2.add(img1, img2)
		return tmp
	
	else:																					# 이펙트 효과가 집중선일 경우 특별한 처리를 한다.
		dark_focus_line = cv2.subtract(mask_inv,30)
		dark_focus_line = cv2.cvtColor(dark_focus_line, cv2.COLOR_GRAY2BGR)		
		merged_img = cv2.add(dark_focus_line, img2)											# 먼저 병합된 이미지를 만들어놓는다
		alpha = 0.7
		tmp = cv2.addWeighted(merged_img, alpha, resize_background_cut, (1-alpha), 0) 		# 병합된 이미지와 이미지 원본을 합쳐 투명한 효과를 얻어낸다.
		return tmp


vlahos_alpha_intensity = {
	'passion' : (1.0, 0),
	'chat' : (1.1, 0),
	'stressed_out' : (1.35, 0),
	'laugh1' : (1.0, 0),
	'snow_falling':(2.0, 1.0),
	'math_bg' : (2.0, 1.0),
	'flower_scatter' : (2.0, 1.0)
}

def vlahos_algorithm(resize_frame, resize_background, marker):

	intensity = vlahos_alpha_intensity[marker['label']]
	
	a1 = intensity[0]
	a2 = intensity[1]
	
	#f.write("part 1\n");
	
	resize_background_cut = resize_background[marker['top'] : marker['height'] + marker['top'], marker['left'] : marker['width'] + marker['left']]

	#f.write("part 2\n");


	# roi (region of interest)영역을 설정한다.
	normalized_frame = resize_frame.astype(float) / 255
	normalized_bg = resize_background_cut.astype(float) / 255

	#f.write("part 3\n");

	
	bg_blue_channel = normalized_bg[:,:,0]
	bg_green_channel = normalized_bg[:,:,1]
	bg_red_channel = normalized_bg[:,:,2]
		
	src_blue_channel = normalized_frame[:,:,0]
	src_green_channel = normalized_frame[:,:,1]
	src_red_channel = normalized_frame[:,:,2]
	
	#f.write("part 4\n");

	
	# 브로드 캐스팅을 이용하여 각 채널을 0 ~ 1사이로 둔다.
	
	alpha = 1.0 - a1 * (src_green_channel - a2 * src_blue_channel)
	alpha_int = alpha[:,:] * 255
	_, alpha_int = cv2.threshold(alpha_int, 255, 255, cv2.THRESH_TRUNC)
	_, alpha_int = cv2.threshold(-1 * alpha_int, 0, 0, cv2.THRESH_TRUNC)
	alpha = -1 * alpha_int.astype(float) / 255
	
	#f.write("part 5\n");

	# alpha의 Edge를 검출하여 인페인팅을 수행한다.
	
	src_blue_channel = cv2.multiply(alpha, src_blue_channel)
	src_green_channel = cv2.multiply(alpha, src_green_channel)
	src_red_channel = cv2.multiply(alpha, src_red_channel)
	# src는 alpha = 1로 설정하여 전경으로 둠
	
	bg_blue_channel = cv2.multiply(1.0 -alpha, bg_blue_channel)
	bg_green_channel = cv2.multiply(1.0 -alpha, bg_green_channel)
	bg_red_channel = cv2.multiply(1.0 -alpha, bg_red_channel)
	# background는 alpha = 0으로 설정하여 배경으로 둠
	
	#f.write("part 6\n");

	
	resize_frame[:,:,0] = src_blue_channel[:,:] * 255
	resize_frame[:,:,1] = src_green_channel[:,:] * 255
	resize_frame[:,:,2] = src_red_channel[:,:] * 255
	
	resize_background_cut[:,:, 0] = bg_blue_channel * 255
	resize_background_cut[:,:, 1] = bg_green_channel * 255
	resize_background_cut[:,:, 2] = bg_red_channel * 255
	
	
	#f.write("part 7\n");

	
	tmp = cv2.add(resize_frame, resize_background_cut)
	
	#f.write("part 8\n");

	
	return tmp