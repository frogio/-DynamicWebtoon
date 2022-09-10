#!C:\Users\frog\anaconda3\python.exe

import sys
import cv2
import copy
import json
import fileinput
import chromakey_algorithm


sys.stdout.write('Content-Type: application/json\n\n');

markerJSON = json.load(sys.stdin)

canvas_width = int(markerJSON['canvas_info']['width'])
canvas_height = int(markerJSON['canvas_info']['height'])

base_img_w = markerJSON['base_img_info']['width']
base_img_h = markerJSON['base_img_info']['height']
base_img_path = markerJSON['base_img_info']['path']
task_dir = markerJSON['task_dir']			# 작업 경로

marker_list = []
chromakey_set = set([])						# 사용된 크로마키 정보 (집합 자료형이라 중복을 걸러냄)
cap_dict = {}								# VideoCapture 딕셔너리, chromakey_set을 참조하여 영상 합성에 필요한 크로마키 소스들을 가져온다.

for i in markerJSON['Markers']:
	marker_dict = {
		'left' : int(i['left']),
		'top' : int(i['top']),
		'width' : int(i['width']),
		'height' : int(i['height']),
		'label' : str(i['label']),
		'proc_method' : str(i['proc_method'])
	}
	
	chromakey_set.add(str(i['label']))			# 한 컷에 사용된 이펙트 종류의 집합을 만든다. (중복 제거)
	
	if(base_img_w > base_img_h):
		marker_dict['top'] -= (canvas_height - base_img_h) // 2
	
	else:
		marker_dict['left'] -= (canvas_width - base_img_w) // 2
	marker_list.append(marker_dict.copy())



for effect in chromakey_set:					# VideoCapture 딕셔너리를 만든다.
	cap_dict[effect] = cv2.VideoCapture(effect + '.mp4')

background = cv2.imread(base_img_path)
resize_background = cv2.resize(background, (base_img_w, base_img_h), interpolation=cv2.INTER_CUBIC)		 # 백그라운드 이미지를 사이즈 재조정
original = copy.deepcopy(resize_background)

fps = 20
fourcc = cv2.VideoWriter_fourcc(*"H264") # *'DIVX' == 'D', 'I', 'V', 'X'
            # C++의 fourcc 코덱정보 입력
outputVideo = cv2.VideoWriter(task_dir + 'preview.mp4', fourcc, fps, (base_img_w, base_img_h))
                # VideoWriter클래스를 이용해 동영상 파일 저장
				# 작업 경로에서 preview.mp4를 만들어낸다

frame_info = {}
end_frame_cnt = 0
while True:
	
	resize_background = copy.deepcopy(original)				
	
	end_frame_cnt = 0
	
	for i in chromakey_set: 				# chromakey_set 집합 자료형이 갖는 값들은 모두 string
		ret, frame = cap_dict[i].read()		# frame 정보 딕셔너리를 만든다.
		
		if not ret: 						# 어떤 영상이 재생 종료된다면
			end_frame_cnt += 1				# 재생 종료된 영상의 개수를 카운팅한다.
			continue
			
		frame_info[i + '_frame'] = copy.deepcopy(frame)		# 키 이름은 'label name'_frame  e.g.) jojo_frame, ko_frame ...
	
	if(end_frame_cnt == len(chromakey_set)):	# 만약 chromakey_set의 원소 개수와 재생영상 종료 갯수가 같을경우 종료 처리된다.
		break

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

	
for effect in chromakey_set:					# VideoCapture 딕셔너리를 해제한다.
	cap_dict[effect].release()

outputVideo.release()

result = {
	'complete' : 'complete'
}

sys.stdout.write(json.dumps(result))
sys.stdout.write("\n")