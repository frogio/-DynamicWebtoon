#!C:\Users\frog\anaconda3\python.exe


import sys
import json
import fileinput


sys.stdout.write('Content-Type: application/json\n\n');

myjson = json.load(sys.stdin)

canvas_width = int(myjson['canvas_info']['width'])
canvas_height = int(myjson['canvas_info']['height'])

base_img_w = myjson['base_img_info']['width']
base_img_h = myjson['base_img_info']['height']

marker_list = [];

for i in myjson['Markers']:
	marker_dict = {
		'left' : int(i['left']),
		'top' : int(i['top']),
		'width' : int(i['width']),
		'height' : int(i['height']),
		'label' : str(i['label'])
	}

	if(base_img_w > base_img_h):
		marker_dict['top'] -= (canvas_height - base_img_h) // 2

		
		
	else:
		marker_dict['left'] -= (canvas_width - base_img_w) // 2

	marker_list.append(marker_dict.copy())

f = open('ReceiveTest.txt','w')

f.write(str(canvas_width))
f.write('\n')
f.write(str(canvas_height))
f.write('\n')
f.write(str(base_img_w))
f.write('\n')
f.write(str(base_img_h))
f.write('\n')

for j in marker_list:
	f.write(str(j['left']))
	f.write('\n')
	f.write(str(j['top']))
	f.write('\n')
	f.write(str(j['width']))
	f.write('\n')
	f.write(str(j['height']))
	f.write('\n')
	f.write(str(j['label']))
	f.write('\n')
	
f.close()


result = {
	'complete' : 'complete'
}

sys.stdout.write(json.dumps(result))
sys.stdout.write("\n")