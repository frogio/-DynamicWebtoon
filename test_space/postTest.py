#!C:\Users\frog\anaconda3\python.exe


import sys
import json

sys.stdout.write('Content-Type: application/json\n\n');

myjson = json.load(sys.stdin)

sys.stdout.write(json.dumps(myjson,indent=1))
sys.stdout.write("\n")