#!C:\Users\frog\anaconda3\python.exe

import sys
import cv2
import copy
import json

sys.stdout.write('Content-Type: application/json\n\n');

markerJSON = json.load(sys.stdin)

canvas_width = int(markerJSON['canvas_info']['width'])
canvas_height = int(markerJSON['canvas_info']['height'])

base_img_w = markerJSON['base_img_info']['width']
base_img_h = markerJSON['base_img_info']['height']

marker_list = []
chromakey_set = set([])
#cap_dict = {}

for i in markerJSON['Markers']:
	marker_dict = {
		'left' : int(i['left']),
		'top' : int(i['top']),
		'width' : int(i['width']),
		'height' : int(i['height']),
		'label' : str(i['label'])
	}
	
	chromakey_set.add(str(i['label']))
	
	if(base_img_w > base_img_h):
		marker_dict['top'] -= (canvas_height - base_img_h) // 2
	
	else:
		marker_dict['left'] -= (canvas_width - base_img_w) // 2
	marker_list.append(marker_dict.copy())


#for effect in chromakey_set:					# VideoCapture 딕셔너리를 만든다.
#	if(effect == 'jojo'):
#		cap = cv2.VideoCapture('jojo.mp4')
#		cap_dict['jojo'] = copy.deepcopy(cap)
#		
#	elif(effect == 'ko')
#		cap = cv2.VideoCapture('ko.mp4')
#		cap_dict['ko'] = copy.deepcopy(cap)
#		
#	elif(effect == 'focus_line')
#		cap = cv2.VideoCapture('focus_line.mp4')
#		cap_dict['focus_line'] = copy.deepcopy(cap)


cap = cv2.VideoCapture('jojo.mp4')
background = cv2.imread('wall su the man in bald.png')
resize_background = cv2.resize(background, (base_img_w, base_img_h), interpolation=cv2.INTER_CUBIC)		 # 백그라운드 이미지를 사이즈 재조정

original = copy.deepcopy(resize_background)

fps = cap.get(cv2.CAP_PROP_FPS)

fourcc = cv2.VideoWriter_fourcc(*'DIVX') # *'DIVX' == 'D', 'I', 'V', 'X'
            # C++의 fourcc 코덱정보 입력
outputVideo = cv2.VideoWriter('final_output.mp4', fourcc, fps, (base_img_w, base_img_h))
                # VideoWriter클래스를 이용해 동영상 파일 저장

#cur_chromakey = marker_list[0]['label']

while True:

	ret, frame = cap.read()
		
	if not ret: break
	
	resize_background = copy.deepcopy(original)
	
	for marker in marker_list:
		resize_frame = cv2.resize(frame, (marker['width'], marker['height']), interpolation=cv2.INTER_CUBIC)
	
		hsv = cv2.cvtColor(resize_frame, cv2.COLOR_BGR2HSV)
		mask = cv2.inRange(hsv, (50, 150, 0), (70, 255, 255))
		mask_inv = cv2.bitwise_not(mask)
	
		resize_background_cut = resize_background[marker['top'] : marker['height'] + marker['top'], marker['left'] : marker['width'] + marker['left']]
	
		img1 = cv2.bitwise_and(resize_frame, resize_frame, mask=mask_inv)
		img2 = cv2.bitwise_and(resize_background_cut, resize_background_cut, mask=mask)
	
		tmp = cv2.add(img1,img2)
		resize_background[marker['top'] : marker['height'] + marker['top'], marker['left'] : marker['width'] + marker['left']] = tmp

	outputVideo.write(resize_background)   
	
cap.release() 
outputVideo.release()

result = {
	'complete' : 'complete'
}

sys.stdout.write(json.dumps(result))
sys.stdout.write("\n")