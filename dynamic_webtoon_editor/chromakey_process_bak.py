#!C:\Users\frog\anaconda3\python.exe

import sys
import cv2
import copy
import json
import fileinput			# debug용 파일 입출력
import shutil
import chromakey_algorithm

sys.stdout.write('Content-Type: application/json\n\n');

markerJSONList = json.load(sys.stdin)

#f = open("inspector.txt","w")

task_dir = str(markerJSONList['task_dir'])
chromakey_set = markerJSONList['used_effect_list']
cap_dict = {}								# VideoCapture 딕셔너리, chromakey_set을 참조하여 영상 합성에 필요한 크로마키 소스들을 가져온다.

for effect in chromakey_set:													# VideoCapture 딕셔너리를 만든다. (사용된 크로마키 영상 핸들을 딕셔너리로 모아놓는다.)
	if(effect == 'jojo'):
		cap_jojo = cv2.VideoCapture('jojo.mp4')
		cap_dict['jojo'] = cap_jojo
		
	elif(effect == 'ko'):
		cap_ko = cv2.VideoCapture('KO.mp4')
		cap_dict['ko'] = cap_ko
		
	elif(effect == 'focus_line'):
		cap_focus_line = cv2.VideoCapture('focus_line.mp4')
		cap_dict['focus_line'] = cap_focus_line

	elif(effect == "tropicana"):
		cap_tropicana = cv2.VideoCapture('tropicana.mp4')
		cap_dict['tropicana'] = cap_tropicana
	
	elif(effect == "falling_money"):
		cap_falling_money = cv2.VideoCapture('falling_money.mp4')
		cap_dict['falling_money'] = cap_falling_money
		
	elif(effect == "question_mark"):
		cap_question_mark = cv2.VideoCapture('question_mark.mp4')
		cap_dict['question_mark'] = cap_question_mark
		
	elif(effect == "to_be_continued"):
		cap_to_be_continued = cv2.VideoCapture('to_be_continued.mp4')
		cap_dict['to_be_continued'] = cap_to_be_continued
	
	elif(effect == "angry"):
		cap_angry = cv2.VideoCapture('angry.mp4')
		cap_dict['angry'] = cap_angry
		
	elif(effect == "passion"):
		cap_passion = cv2.VideoCapture('passion.mp4')
		cap_dict['passion'] = cap_passion
		
	elif(effect == "flame"):
		cap_flame = cv2.VideoCapture('flame.mp4')
		cap_dict['flame'] = cap_flame
		
	elif(effect == "stressed_out"):
		cap_stressed_out = cv2.VideoCapture('stressed_out.mp4')
		cap_dict['stressed_out'] = cap_stressed_out
	
	elif(effect == "pow"):
		cap_pow = cv2.VideoCapture('pow.mp4')
		cap_dict['pow'] = cap_pow
	
	elif(effect == "laugh1"):
		cap_laugh1 = cv2.VideoCapture('laugh1.mp4')
		cap_dict['laugh1'] = cap_laugh1
	
	elif(effect == "laugh2"):
		cap_laugh2 = cv2.VideoCapture('laugh2.mp4')
		cap_dict['laugh2'] = cap_laugh2

	elif(effect == 'laugh3'):
		cap_laugh3 = cv2.VideoCapture('laugh3.mp4')
		cap_dict['laugh3'] = cap_laugh3
	
	elif(effect == 'chat'):
		cap_chat = cv2.VideoCapture('chat.mp4')
		cap_dict['chat'] = cap_chat
	
	elif(effect == 'math_bg'):
		cap_math_bg = cv2.VideoCapture('math_bg.mp4')
		cap_dict['math_bg'] = cap_math_bg
		
	elif(effect == 'snow_falling'):
		cap_snow_falling = cv2.VideoCapture('snow_falling.mp4')
		cap_dict['snow_falling'] = cap_snow_falling
	
	elif(effect == 'flower_scatter'):
		cap_flower_scatter = cv2.VideoCapture('flower_scatter.mp4')
		cap_dict['flower_scatter'] = cap_flower_scatter
	
	elif(effect == 'jojo_onomatopoeia1'):
		cap_jojo_onomatopoeia1 = cv2.VideoCapture('jojo_onomatopoeia1.mp4')
		cap_dict['jojo_onomatopoeia1'] = cap_jojo_onomatopoeia1
	
	elif(effect == 'jojo_onomatopoeia2'):
		cap_jojo_onomatopoeia2 = cv2.VideoCapture('jojo_onomatopoeia2.mp4')
		cap_dict['jojo_onomatopoeia2'] = cap_jojo_onomatopoeia2
	
	elif(effect == 'jojo_onomatopoeia3'):
		cap_jojo_onomatopoeia3 = cv2.VideoCapture('jojo_onomatopoeia3.mp4')
		cap_dict['jojo_onomatopoeia3'] = cap_jojo_onomatopoeia3

canvas_width = int(markerJSONList['cuts'][0]['canvas_info']['width'])			# 캔버스 정보를 가져온다.
canvas_height = int(markerJSONList['cuts'][0]['canvas_info']['height'])

#f.write("canvas_width : " + str(canvas_width) + '\n');
#f.write("canvas_height : " + str(canvas_height) + '\n');

fps = 20
fourcc = cv2.VideoWriter_fourcc(*"H264") # *'DIVX' == 'D', 'I', 'V', 'X'
			# C++의 fourcc 코덱정보 입력
cut_no = 0;

#n = 0

#f.write('\n' + str(markerJSONList['cuts']));

for cut in markerJSONList['cuts']:			# 한 컷씩 정보를 가져온다.
#	n += 1
#	f.write("반복횟수 : " + str(n));

	base_img_w = cut['base_img_info']['width']
	base_img_h = cut['base_img_info']['height']
	base_img_path = cut['base_img_info']['path']
	
	marker_list = []
	marker_list.clear()
	used_chromakey_set = set()				# 현재 컷에서 사용된 크로마키 영상 집합
	used_chromakey_set.clear()
	
	
	# ajax로 받아온 JSON의 속성을 수정할 때에 반드시 marker_dict에도 추가된 요소를 추가해야 함 #
	# ajax로 받아온 JSON의 속성을 수정할 때에 반드시 marker_dict에도 추가된 요소를 추가해야 함 #
	# ajax로 받아온 JSON의 속성을 수정할 때에 반드시 marker_dict에도 추가된 요소를 추가해야 함 #	
	for i in cut['Markers']:				# 한 컷에 사용된 마커 정보들을 가져온다.
	
#		f.write('\n' + "isWorkingWell?")
		
		marker_dict = {
			'left' : int(i['proc_coord_x']),
			'top' : int(i['proc_coord_y']),
			'width' : int(i['width']),
			'height' : int(i['height']),
			'label' : str(i['label']),
			'proc_method' : str(i['proc_method'])
		}
		
		used_chromakey_set.add(str(i['label']))			# 한 컷에 사용된 이펙트 종류의 집합을 만든다. (중복 제거)
		marker_list.append(marker_dict.copy())			# 딕셔너리를 원소로 갖는 리스트.
	
	if marker_list:																# 마커 리스트가 비어있지 않을경우 (마커가 최소 1개 이상 있을경우)
		
		background = cv2.imread(base_img_path)
		resize_background = cv2.resize(background, (base_img_w, base_img_h), interpolation=cv2.INTER_CUBIC)		 # 백그라운드 이미지를 사이즈 재조정
		
		original = copy.deepcopy(resize_background)
		save_dir = "../dynamic_webtoon_post/" + task_dir + str(cut_no) + ".mp4"
		outputVideo = cv2.VideoWriter(save_dir, fourcc, fps, (base_img_w, base_img_h))							  # VideoWriter클래스를 이용해 동영상 파일 저장
		
		frame_info = {}
		frame_info.clear()
		end_frame_cnt = 0
		
		while True:
	
			resize_background = copy.deepcopy(original)				
			for i in used_chromakey_set: 				# chromakey_set 집합 자료형이 갖는 값들은 모두 string		// 합성할 영상 참조테이블을 만든다
				ret, frame = cap_dict[i].read()			# frame 정보 딕셔너리를 만든다.
				if not ret: 							# 어떤 영상이 재생 종료된다면
					end_frame_cnt += 1					# 재생 종료된 영상의 개수를 카운팅한다.
					continue
					
				frame_info[i + '_frame'] = copy.deepcopy(frame)		# 키 이름은 'label name'_frame  e.g.) jojo_frame, ko_frame ... 현재 크로마키 영상의 프레임들을 가져온다.
			
			if(end_frame_cnt >= len(used_chromakey_set)):	# 만약 chromakey_set의 원소 개수와 재생영상 종료 갯수가 같을경우 종료 처리된다.
				end_frame_cnt = 0
				break
			# 현재 컷에 사용된 크로마키 프레임들을 딕셔너리로 만들어 참조하게 한다.
			
			# 영상을 최종적으로 합성
			# marker_list가 비어있지 않을 경우 (마커가 1개라도 존재할 시 마커를 처리한다.)
			for marker in marker_list:					# 영상 참조 테이블인 frame_info를 참조한다.
				
				cur_frame = frame_info[marker['label'] + '_frame']	# 딕셔너리로부터 frame을 가져온다 키 이름은 'label name'_frame e.g.) jojo_frame, ko_frame ...
				resize_frame = cv2.resize(cur_frame, (marker['width'], marker['height']), interpolation=cv2.INTER_CUBIC)
				# 동영상으로부터 리사이징된 프레임을 가져온다.
				
				
				if(marker['proc_method'] == 'mask'):
					tmp = chromakey_algorithm.mask_algorithm(resize_frame, resize_background, marker)
					resize_background[marker['top'] : marker['height'] + marker['top'], marker['left'] : marker['width'] + marker['left']] = tmp
				# 일반적인 mask algorithm을 사용하는 경우

				elif(marker['proc_method'] == 'vlahos'):
					tmp = chromakey_algorithm.vlahos_algorithm(resize_frame, resize_background, marker)
					resize_background[marker['top'] : marker['height'] + marker['top'], marker['left'] : marker['width'] + marker['left']] = tmp	
				# vlahos 알고리즘을 이용하는 경우
				
			outputVideo.write(resize_background)						

			
		for i in used_chromakey_set:									# 현재 컷에 사용된 모든 크로마키 영상을 다시 되감는다.
			cap_dict[i].set(cv2.CAP_PROP_POS_FRAMES, 0)
	
	else:																# 마커가 존재하지 않을 경우
		if ".jpg" in base_img_path:
			save_dir = "../dynamic_webtoon_post/" + task_dir + str(cut_no) + ".jpg"
			
		elif ".png" in base_img_path:
			save_dir = "../dynamic_webtoon_post/" + task_dir + str(cut_no) + ".png"
		
		elif ".bmp" in base_img_path:
			save_dir = "../dynamic_webtoon_post/" + task_dir + str(cut_no) + ".bmp"


		shutil.copy(base_img_path, save_dir)
	cut_no += 1

for effect in chromakey_set:					# VideoCapture 딕셔너리를 해제한다.
	if(effect == 'jojo'):
		cap_dict['jojo'].release() 
		
	elif(effect == 'ko'):
		cap_dict['ko'].release() 
		
	elif(effect == 'focus_line'):
		cap_dict['focus_line'].release()
		
	elif(effect == 'tropicana'):
		cap_dict['tropicana'].release() 
		
	elif(effect == 'falling_money'):
		cap_dict['falling_money'].release()
		
	elif(effect == 'question_mark'):
		cap_dict['question_mark'].release()
	
	elif(effect == 'to_be_continued'):
		cap_dict['to_be_continued'].release()
	
	elif(effect == 'angry'):
		cap_dict['angry'].release()
		
	elif(effect == 'passion'):
		cap_dict['passion'].release()
	
	elif(effect == 'flame'):
		cap_dict['flame'].release()
	
	elif(effect == 'stressed_out'):
		cap_dict['stressed_out'].release()
		
	elif(effect == 'pow'):
		cap_dict['pow'].release()

	elif(effect == 'laugh1'):
		cap_dict['laugh1'].release()
		
	elif(effect == 'laugh2'):
		cap_dict['laugh2'].release()
	
	elif(effect == 'laugh3'):
		cap_dict['laugh3'].release()
		
	elif(effect == 'chat'):
		cap_dict['chat'].release()
	
	elif(effect == 'math_bg'):
		cap_dict['math_bg'].release()

	elif(effect == 'snow_falling'):
		cap_dict['snow_falling'].release()
		
	elif(effect == 'flower_scatter'):
		cap_dict['flower_scatter'].release()
		
	elif(effect == 'jojo_onomatopoeia1'):
		cap_dict['jojo_onomatopoeia1'].release()
	
	elif(effect == 'jojo_onomatopoeia2'):
		cap_dict['jojo_onomatopoeia2'].release()
	
	elif(effect == 'jojo_onomatopoeia3'):
		cap_dict['jojo_onomatopoeia3'].release()		

if ('outputVideo' in locals() or 'outputVideo' in globals()):				# 마커가 1개 이상이라도 있어서 outputVideo가 정의되어 있을 경우
	outputVideo.release()													# outputVideo를 릴리즈한다.

#f.close()

result = {
	'complete' : 'complete'
}

sys.stdout.write(json.dumps(result))
sys.stdout.write("\n")