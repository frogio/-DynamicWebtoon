<!DOCTYPE html>
<?php

	include_once "../db_manager.php";
	
	define("REQUEST_TASK_DIR","SELECT editor_task_dir FROM dynamic_webtoon_post WHERE post_no = ");
	
	echo '<script>var isModify = "none"</script>';	// 웹툰 수정요쳥 여부를 판단할 자바스크립트 변수를 선언한다, 만약 새로 업로드한다면 none 값을 갖는다.
	$editor_task_dir = "none";
	
	if(isset($_POST['operate'])){					// 웹툰 수정 요청을 받을 때
		$operate = $_POST['operate'];
		$post_no = $_POST['post_no'];
		
		$conn = connect_db();
	
		$query = REQUEST_TASK_DIR.$post_no.";";
		mysqli_autocommit($conn, FALSE);
		
		if($request_result = mysqli_query($conn,$query)){
			if($request_result->num_rows > 0){
				$row = $request_result->fetch_assoc();
					$editor_task_dir = $row['editor_task_dir'];
					mysqli_free_result($request_result);
			}										
		}
		
		mysqli_commit($conn);
		release_db($conn);
		//echo '<script>alert("'.$editor_task_dir.'");</script>';
		echo '<script>isModify = "'.$operate.'"</script>';		// 수정 요쳥을 받을경우 modify란 값을 갖게된다.
		
	}
	
?>
<html>

<head>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<script src="https://unpkg.com/fabric@4.6.0/dist/fabric.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/lodash@4.17.21/lodash.min.js"></script>
    <link href="../css/styles.css" rel="stylesheet" />
</head>

<body>
			<div class="row gx-4 gx-lg-5" >
					<input type="hidden" id="request_task_folder" value="<?php echo $editor_task_dir; ?>"/>			<!-- 웹툰 수정요청을 받을 때 해당 웹툰의 작업 경로를 가져온다. -->
					<input type="hidden" id="request_post_no" value="<?php echo $post_no; ?>"/>			<!-- 웹툰 수정요청을 받을 때 해당 웹툰의 작업 경로를 가져온다. -->
					<input type="file" id="input-image" name='input-image' accept=".jpg,.jpeg,.png,.gif,.bmp"/>
					<div class="col-md-6" style="margin-top:5px">
						<input type="button" class = "show_idx btn btn-outline-dark flex-shrink-0" value = "지우기" onclick="clear_canvas()"/>
						<input type="button" class = "btn btn-outline-dark flex-shrink-0"value = "미리보기" onclick="preview_effect()"/> 
						<input type="button" class = "show_idx btn btn-outline-dark flex-shrink-0" value = "현재 컷 삭제" onclick="delete_cut()"/>
						<input type="button" class = "show_idx btn btn-outline-dark flex-shrink-0" value = "앞에 컷 삽입" onclick="insert_cut_forward()"/>
						<input type="button" class = "show_idx btn btn-outline-dark flex-shrink-0" value = "뒤에 컷 삽입" onclick="insert_cut_back()"/>
						
						<input type="button" class = "show_idx btn btn-outline-dark flex-shrink-0" value = "컷 앞으로" onclick="move_cut_forward()"/>
						<input type="button" class = "show_idx btn btn-outline-dark flex-shrink-0" value = "컷 뒤로" onclick="move_cut_back()"/>
						
						
						<div style="text-align:center;margin:5px">
							<input type="button" class = "show_idx btn btn-outline-dark flex-shrink-0" value = "이전 컷" onclick="get_prev_cut()"/>			
							<input type="button" class = "show_idx btn btn-outline-dark flex-shrink-0" value = "다음 컷" onclick="get_next_cut()"/>
						</div>
						<div style="text-align:left;margin:7px">
							<p id = "idx" style="font-size:25px;text-align:center">1 / 1</p>
						</div>
					</div>

					<div class="canvas_container">
						<canvas id="c" style="border:1px solid" width="700" height="700"></canvas>
						
						<div class="menu_container">						
							<div class="d-flex" style= "padding-bottom:1rem;">
								썸네일 이미지<input style="margin-left:1rem;"type="file" value="썸네일 업로드" id="thumb-image" name="thumb" accept=".jpg,.jpeg,.png,.gif,.bmp"/>
							</div>
							
							<div style= "padding-bottom:3rem;text-align:right;" >
								<input class="form-control me-3 display-5 fw-bolder" id="title" placeholder = "제목"/>
								<textarea style="display:none; margin-top:1rem;" class="form-control me-3" id="brief_comment" placeholder = "짧은 코멘트" cols = "45" rows = "10"></textarea>
								<input style="margin-top:1rem;" class="btn btn-outline-dark flex-shrink-0" id="comment" type="button" value = "코멘트 작성" onclick="toggle_comment()">
							</div>
							
							<div style="display:block;" class="editor_window">
								<p id="current_mode">선택 모드</p>
								<input type="button" class="btn btn-outline-dark flex-shrink-0" value = "선택" onclick="set_select_mode()">
								<input type="button" class="btn btn-outline-dark flex-shrink-0" id="select_part_cover" value = "부분 효과 삽입">						
								<input type="button" class="btn btn-outline-dark flex-shrink-0" id="select_all_cover" value = "전체 효과 삽입">
							</div>
							
							<div style="display:block; margin-top:1rem;" class="editor_window" id="part_cover">
								<p>부분 효과 삽입</p>
								<input type="button" class="btn btn-outline-dark flex-shrink-0" value = "죠죠" onclick="select_jojo()">
								<input type="button" class="btn btn-outline-dark flex-shrink-0" value = "ko" onclick="select_ko()">
								<input type="button" class="btn btn-outline-dark flex-shrink-0" value = "집중선" onclick="select_focus_line()">
								<input type="button" class="btn btn-outline-dark flex-shrink-0" value = "트로피카나" onclick="select_tropicana()">
								<input type="button" class="btn btn-outline-dark flex-shrink-0" value = "돈벼락" onclick="select_falling_money()">
								<input type="button" class="btn btn-outline-dark flex-shrink-0" value = "물음표" onclick="select_question_mark()">
								<input type="button" class="btn btn-outline-dark flex-shrink-0" value = "빠직" onclick="select_angry_mark()">
								<input type="button" class="btn btn-outline-dark flex-shrink-0" value = "스트레스" onclick="select_stressed_out()">
								<input type="button" class="btn btn-outline-dark flex-shrink-0" value = "POW" onclick="select_pow()">
								<input type="button" class="btn btn-outline-dark flex-shrink-0" value = "ㅋㅋㅋ1" onclick="select_laugh1()">
								<input type="button" class="btn btn-outline-dark flex-shrink-0" value = "ㅋㅋㅋ2" onclick="select_laugh2()">
								<input type="button" class="btn btn-outline-dark flex-shrink-0" value = "ㅋㅋㅋ3" onclick="select_laugh3()">
								<input type="button" class="btn btn-outline-dark flex-shrink-0" value = "재잘재잘" onclick="select_chat()">
							</div>

							<!-- 전체효과는 id값으로 어떤 효과를 삽입할지 식별함. (id 뒤의 _all_cover는 지워지고 markerJSONList에 삽입됨)
								선택되어있는지를 식별하는 방법은 class에 selected라는 클래스가 있는 여부에 따라 식별-->

							<div style="display:none; margin-top:1rem;" class="editor_window" id="all_cover">
								<p>전체 효과 삽입</p>	
								<input type="button" class="btn btn-outline-dark flex-shrink-0" value = "죠죠" id="jojo_all_cover">
								<input type="button" class="btn btn-outline-dark flex-shrink-0" value = "ko" id="ko_all_cover">
								<input type="button" class="btn btn-outline-dark flex-shrink-0" value = "집중선" id="focus_line_all_cover" >
								<input type="button" class="btn btn-outline-dark flex-shrink-0" value = "돈벼락" id="falling_money_all_cover">
								<input type="button" class="btn btn-outline-dark flex-shrink-0" value = "To Be Continued" id="to_be_continued_all_cover">
								<input type="button" class="btn btn-outline-dark flex-shrink-0" value = "관객 환호" id="passion_all_cover">
								<input type="button" class="btn btn-outline-dark flex-shrink-0" value = "열받음" id="flame_all_cover">
								<input type="button" class="btn btn-outline-dark flex-shrink-0" value = "두뇌 풀가동" id="math_bg_all_cover">
								<input type="button" class="btn btn-outline-dark flex-shrink-0" value = "눈내림" id="snow_falling_all_cover">
								<input type="button" class="btn btn-outline-dark flex-shrink-0" value = "흩날리는 꽃잎" id="flower_scatter_all_cover">
								<input type="button" class="btn btn-outline-dark flex-shrink-0" value = "죠죠 의성어1" id="jojo_onomatopoeia1_all_cover">
								<input type="button" class="btn btn-outline-dark flex-shrink-0" value = "죠죠 의성어2" id="jojo_onomatopoeia2_all_cover">
								<input type="button" class="btn btn-outline-dark flex-shrink-0" value = "죠죠 의성어3" id="jojo_onomatopoeia3_all_cover">
							</div>
							
							<div style="margin-top:3rem; text-align:right;">
								<input type="button" class = "btn btn-outline-dark flex-shrink-0" value = "업로드" onclick="upload_webtoon()"/>
							</div>
						</div>

					</div>

					
					<style type="text/css">

						.canvas_container{ 
							display: flex; 
							float:left;				// div 내부에 있는 원소를 왼쪽부터 추가한다.(수평방향으로 추가)
						} 
						
						.menu_container{
							margin-top:30px;
							margin-left:30px;
							width:40rem;
							
						}
						
						.selected{
							background:#ff0000;
						}

						
					</style>
					
			</div>

		<script>
		
		
			var img_width = 0;
			var img_height = 0;
			// 부모 이미지 파일의 사이즈
			
			var canvas_width = $('#c').attr("width");
			var canvas_height = $('#c').attr("height");
			
			var img_left;
			var img_top;
			// 이미지의 좌상단 좌표값
			
			var rect_left = 0;
			var rect_top = 0;
			var rect_right = 0;
			var rect_bottom = 0;
			
			var selected_rect_scaleX = 0;
			var selected_rect_scaleY = 0;
			var selected_rect_width = 0;
			var selected_rect_height = 0;
			
			var selected_group_scaleX = 0;
			var selected_group_scaleY = 0;
			
			var isClick = false;
			var isMove = false;
			var isSelected = false;
			
			var canvas = new fabric.Canvas(c);
			var markerJSONList = new Array();		// 만화에 쓰인 모든 JSON을 list로 저장한다.
			var JSONListIdx = 0;					// 현재 위치한 컷 인덱스
			var used_effect_list = [];				// markerJSONList의 헤더, 사용된 마커들의 종류를 기록한다. (중복되지 않음)
	
			var isDeleted = false;
			
			var mode = 'select_mode';				// 기본 모드는 select_mode로 객체를 선택하는 상태임
			var selected_effect = 'none';
			var marker_label = 'none';
			
			var task_folder;						// 현재 작업 디렉토리 폴더
			var modified_folder;					// 수정을 위한 디렉토리 경로명
			
			var show_cmt = false;
			
			var is_all_cover = false;
			
			var proc_method = 'mask';				// 영상 합성시 사용할 알고리즘 선택, 값 : mask : 단순 마스크를 이용한 알고리즘 vlahos : vlahos 알고리즘을 이용
			

			if(localStorage.getItem("task_folder") && isModify == 'none')			// 새로운 웹툰을 업로드할 경우
				task_folder = localStorage.getItem("task_folder") + '/';

			else if(isModify == "modify") {											// 기존에 업로드돼있는 웹툰을 수정할 경우, /isModify는 php 내에서 생성한 변수. (Line : 8 참조)
				var dir_tmp = $('#request_task_folder').val();
				dir_tmp = dir_tmp.substring(dir_tmp.indexOf('/', 3) + 1, dir_tmp.length);
				task_folder =  dir_tmp;
				

				
				//console.log("task_folder : " + task_folder);						// 작업 경로를 가져온다.
				
				modified_folder = $('#request_task_folder').val();						// 수정하길 원하는 웹툰에 해당하는 작업 디렉토리를 가져온다.
				var json_dir = modified_folder + "editor_cut_info.json";
				
				jQuery.ajax({														// 업로드 당시 작업 내역을 기록한 json파일을 가져온다.
					type: "POST",
					url: json_dir,
					contentType :'charset=UTF-8',
					success:function(result){
						

						//console.log(result);
						$('#title').val(result.title);
						$('#brief_comment').val(result.brief_comment);
						markerJSONList = result.cuts;								// 읽어온 json파일의 cuts정보를 가져와 컷 정보들을 복구한다.
						
						$('#idx').text((JSONListIdx + 1) + " / " + markerJSONList.length);				// 컷 페이지표기를 복구한다.
						restore_cut(markerJSONList[0]);
			
						for(var i = 0; i < markerJSONList.length; i++){
							for(var j = 0; j < markerJSONList[i].Markers.length; j++)
								used_effect_list.push(markerJSONList[i].Markers[j].label);				// used_effect_list의 원형을 복구한다. 
																										// (기록되어있는 used_effect_list는 이미 가공되어 중복이 제거되어있기 때문에
																										// 사용할 수 없다.
						}
						//console.log(used_effect_list);
						
					},error: function(xhr, status, error){
						alert(error);
					}
				});
				

			
			}
			
			show_current_mode();
			add_markerJSONList();
			
			$("#select_part_cover").click(function(){
				$("#part_cover").css({"display":"block"});
				$("#all_cover").css({"display":"none"});
				is_all_cover = false;
			});
			
			$("#select_all_cover").click(function(){
				$("#part_cover").css({"display":"none"});
				$("#all_cover").css({"display":"block"});
				is_all_cover = true;
			});
			
			function toggle_comment(){
				
				if(show_cmt == false){
					show_cmt = true;
					$('#brief_comment').css({"display" : "block"});
					$('.editor_window').css({"display" : "none"});
					$("#comment").val("코멘트 닫기");
					
				}
				else{
					show_cmt = false;
					$('#brief_comment').css({"display" : "none"});
					$('.editor_window').css({"display" : "block"});
					$("#comment").val("코멘트 작성");
					
					if(is_all_cover == true){
						$("#part_cover").css({"display": "none"});
						$("#all_cover").css({"display": "block"});
					}
					else{
						$("#part_cover").css({"display": "block"});
						$("#all_cover").css({"display": "none"});
					}
				}
				
			}
			
			
			$('.show_idx').click(function(){			// 인덱스 표기 동시에 marker를 수정한다.
				$('#idx').text((JSONListIdx + 1) + " / " + markerJSONList.length);
				$("#input-image").val("");
				
				//console.log(markerJSONList);
			});
			
			function add_markerJSONList(){				// 새로운 markerObj을 초기화하여 markerJSONList에 넣는다.				
				var markerObj= new Object();
			
				markerObj.canvas_info = new Object();
				markerObj.canvas_info.width = canvas_width;
				markerObj.canvas_info.height = canvas_height;
	
				markerObj.base_img_info = new Object();
				
				markerObj.base_img_info.path = "none";
				markerObj.base_img_info.width = 0;
				markerObj.base_img_info.height = 0;

				markerObj.Markers = new Array();	
				markerJSONList.push(markerObj);
				// 임시로 1개의 오브젝트를 리스트에 넣는다.
				
				//console.log(markerJSONList);
			}


			function esc_key(){						// esc 키 기능, 누르면 모든 객체의 선택이 해제되고 셀렉트 모드로 강제로 변경한다.
				set_select_mode();
				canvas.discardActiveObject().renderAll();
			}
			
			function show_current_mode(){
			
				if(mode == 'select_mode')
					$('#current_mode').text("선택 모드");
				
				else if(mode == 'insert_mode')
					$('#current_mode').text(selected_effect + "효과 삽입");
				
			}
		
			function set_select_mode(){
				mode = 'select_mode';
				show_current_mode();
			}
	
			/////////////////////////////////////////////////////////// 새로운 이펙트를 추가할 때 반드시 proc_method를 지정해주어야 한다 ///////////////////////////////////////////////////
			function select_jojo(){
				mode = 'insert_mode';
				marker_label = 'jojo';
				selected_effect = '죠죠 '
				show_current_mode();
				proc_method = 'mask';
			}
			function select_ko(){
				mode = 'insert_mode';
				marker_label = 'ko';
				selected_effect = 'KO '
				show_current_mode();
				proc_method = 'mask';				
			}
			function select_focus_line(){
				mode = 'insert_mode';
				marker_label = 'focus_line';
				selected_effect = '집중선 ';
				show_current_mode();
				proc_method = 'mask';
			}
			
			function select_tropicana(){
				mode = 'insert_mode';
				marker_label = 'tropicana';
				selected_effect = '트로피카나 ';
				show_current_mode();
				proc_method = 'mask';
			}
			
			function select_falling_money(){
				mode = 'insert_mode';
				marker_label = 'falling_money';
				selected_effect = '돈벼락 ';
				show_current_mode();
				proc_method = 'mask';
			}
			function select_question_mark(){
				mode = 'insert_mode';
				marker_label = 'question_mark';
				selected_effect = '물음표 ';
				show_current_mode();
				proc_method = 'mask';
			}
			
			function select_angry_mark(){
				mode = 'insert_mode';
				marker_label = 'angry';
				selected_effect = '물음표 ';
				show_current_mode();				
				proc_method = 'mask';
			}
			function select_stressed_out(){
				mode = 'insert_mode';
				marker_label = 'stressed_out';
				selected_effect = '스트레스 ';
				show_current_mode();
				proc_method = 'vlahos';
			}
			
			function select_pow(){
				mode = 'insert_mode';
				marker_label = 'pow';
				selected_effect = 'POW ';
				show_current_mode();
				proc_method = 'mask';
			}
			function select_laugh1(){
				mode = 'insert_mode';
				marker_label = 'laugh1';
				selected_effect = 'ㅋㅋㅋ1 ';
				show_current_mode();
				proc_method = 'vlahos';
			}
			
			function select_laugh2(){
				mode = 'insert_mode';
				marker_label = 'laugh2';
				selected_effect = 'ㅋㅋㅋ2 ';
				show_current_mode();
				proc_method = 'mask';				
			}
			
			function select_laugh3(){
				mode = 'insert_mode';
				marker_label = 'laugh3';
				selected_effect = 'ㅋㅋㅋ3 ';
				show_current_mode();
				proc_method = 'mask';				
			}
			
			function select_chat(){
				mode = 'insert_mode';
				marker_label = 'chat';
				selected_effect = '재잘재잘 ';
				show_current_mode();
				proc_method = 'vlahos';				
			}
			
			
			function isImgExist(){
				
				return !(markerJSONList[JSONListIdx].base_img_info.path == "none" ||
						markerJSONList[JSONListIdx].base_img_info.path == "inserted" ||
						markerJSONList[JSONListIdx].base_img_info.path == "deleted")
			}
			
			function restore_selected_all_cover_marker(markerObj){
				
				var aCoords;
				if(img_width > img_height){
				
					aCoords = {
						tl: new fabric.Point(0, img_top),
						tr: new fabric.Point(img_width, img_top),
						br: new fabric.Point(img_width , img_top + img_height),
						bl: new fabric.Point(0, img_top + img_height)
					}				// 좌표 성분에 label성분을 추가한다.
				}
				else {
					aCoords = {
						tl: new fabric.Point(img_left, 0),
						tr: new fabric.Point(img_left + img_width, 0),
						br: new fabric.Point(img_left + img_width , img_height),
						bl: new fabric.Point(img_left, img_height)
					}
					
				}
						
				fabric.Image.fromURL('./marker_img/' + markerObj.label + '_marker_img.png', function(img) {
					const width = img.width;
					const height = img.height;	
					
					//console.log("is Valied? : " + i);
					
					img.set({
						left: aCoords.tl.lerp(aCoords.br).x,
						top: aCoords.tl.lerp(aCoords.br).y,
						scaleX:(aCoords.tl.distanceFrom(aCoords.tr)) / width,
						scaleY:(aCoords.tl.distanceFrom(aCoords.bl)) / height,
						originY: 'center',
						originX: 'center',
						selectable:false,
						all_cover_label: markerObj.label,
						proc_method : markerObj.proc_method
					});
					
					canvas.add(img).renderAll();
					
					//console.log(canvas);
				});
				
			}
			
			
			function show_selected_all_cover_marker(marker_name){
				
				var aCoords;
				if(img_width > img_height){
				
					aCoords = {
						tl: new fabric.Point(0, img_top),
						tr: new fabric.Point(img_width, img_top),
						br: new fabric.Point(img_width , img_top + img_height),
						bl: new fabric.Point(0, img_top + img_height)
					}				// 좌표 성분에 label성분을 추가한다.
				}
				else {
					aCoords = {
						tl: new fabric.Point(img_left, 0),
						tr: new fabric.Point(img_left + img_width, 0),
						br: new fabric.Point(img_left + img_width , img_height),
						bl: new fabric.Point(img_left, img_height)
					}
					
				}
						
				fabric.Image.fromURL('./marker_img/' + marker_name + '_marker_img.png', function(img) {
					const width = img.width;
					const height = img.height;	
					
					//console.log("is Valied? : " + i);
					
					img.set({
						left: aCoords.tl.lerp(aCoords.br).x,
						top: aCoords.tl.lerp(aCoords.br).y,
						scaleX:(aCoords.tl.distanceFrom(aCoords.tr)) / width,
						scaleY:(aCoords.tl.distanceFrom(aCoords.bl)) / height,
						originY: 'center',
						originX: 'center',
						selectable:false,
						all_cover_label: marker_name,
						proc_method : proc_method
					});
					
					canvas.add(img).renderAll();
					
					//console.log(canvas);
				});
				
			}
			
			function deselete_all_cover_marker(marker_name){
				
					del_used_effect_list_elem(marker_name);
					//obj = markerJSONList[JSONListIdx].Markers;
					obj = canvas._objects;
					
					for(let i = 0; i < obj.length; i++) {
						if(obj[i].all_cover_label == marker_name)  {
							canvas.remove(obj[i]); 
							break;
						}
					}
			
			}
			
	/////////////////////////////////////////////////////////// 새로운 이펙트를 추가할 때 반드시 proc_method를 지정해주어야 한다 ///////////////////////////////////////////////////

			
			
			function del_used_effect_list_elem(label){
				var index = used_effect_list.indexOf(label); // 5를 제거해야 하는 경우
				if (index > -1) 
					used_effect_list.splice(index, 1);
			}
			
			$("#jojo_all_cover").click(function(){
				if(isImgExist() == false){
					alert("먼저 이미지를 업로드해주세요");
					return;
				}
				
				if($("#jojo_all_cover").hasClass("selected") === false) {
					$("#jojo_all_cover").addClass("selected");
					used_effect_list.push("jojo");
					show_selected_all_cover_marker("jojo");
				}
				else{
					deselete_all_cover_marker("jojo");
					$("#jojo_all_cover").removeClass("selected"); 
				}
				proc_method = 'mask';					
				//console.log(canvas);
				
			});
			
			$("#ko_all_cover").click(function(){
				if(isImgExist() == false){
					alert("먼저 이미지를 업로드해주세요");
					return;
				}
				
				if($("#ko_all_cover").hasClass("selected") === false){
					$("#ko_all_cover").addClass("selected");
					used_effect_list.push("ko");
					show_selected_all_cover_marker("ko");
				}
				else {
					deselete_all_cover_marker("ko");
					$("#ko_all_cover").removeClass("selected"); 
				}
				proc_method = 'mask';
			});

			$("#focus_line_all_cover").click(function(){
				
				if(isImgExist() == false){
					alert("먼저 이미지를 업로드해주세요");
					return;
				}
				
				if($("#focus_line_all_cover").hasClass("selected") === false) {
					$("#focus_line_all_cover").addClass("selected");
					used_effect_list.push("focus_line");
					show_selected_all_cover_marker("focus_line");
				}
				else {
					deselete_all_cover_marker("focus_line");
					$("#focus_line_all_cover").removeClass("selected"); 
				}
				proc_method = 'mask';
			});
			
			$("#falling_money_all_cover").click(function(){
				if(isImgExist() == false){
					alert("먼저 이미지를 업로드해주세요");
					return;
				}
				
				if($("#falling_money_all_cover").hasClass("selected") === false) {
					$("#falling_money_all_cover").addClass("selected");
					used_effect_list.push("falling_money");
					show_selected_all_cover_marker("falling_money");
				}
				else {
					deselete_all_cover_marker("falling_money");
					$("#falling_money_all_cover").removeClass("selected"); 
				}
				proc_method = 'mask';
			});
			
			$("#to_be_continued_all_cover").click(function(){
				if(isImgExist() == false){
					alert("먼저 이미지를 업로드해주세요");
					return;
				}
				
				if($("#to_be_continued_all_cover").hasClass("selected") === false) {
					$("#to_be_continued_all_cover").addClass("selected");
					used_effect_list.push("to_be_continued");
					show_selected_all_cover_marker("to_be_continued");
				}
				else {
					deselete_all_cover_marker("to_be_continued");
					$("#to_be_continued_all_cover").removeClass("selected"); 
				}
				proc_method = 'mask';
			});

			$("#passion_all_cover").click(function(){
				if(isImgExist() == false){
					alert("먼저 이미지를 업로드해주세요");
					return;
				}
				
				if($("#passion_all_cover").hasClass("selected") === false) {
					$("#passion_all_cover").addClass("selected");
					used_effect_list.push("passion");
					show_selected_all_cover_marker("passion");
				}
				else {
					deselete_all_cover_marker("passion");
					$("#passion_all_cover").removeClass("selected"); 
				}
				proc_method = 'vlahos';
			});

			
			
			$("#flame_all_cover").click(function(){
				if(isImgExist() == false){
					alert("먼저 이미지를 업로드해주세요");
					return;
				}
				
				if($("#flame_all_cover").hasClass("selected") === false) {
					$("#flame_all_cover").addClass("selected");
					used_effect_list.push("flame");
					show_selected_all_cover_marker("flame");
				}
				else {
					deselete_all_cover_marker("flame");
					$("#flame_all_cover").removeClass("selected"); 
				}
				proc_method = 'mask';
			});
			
			
			$("#snow_falling_all_cover").click(function(){
				if(isImgExist() == false){
					alert("먼저 이미지를 업로드해주세요");
					return;
				}
				
				if($("#snow_falling_all_cover").hasClass("selected") === false) {
					$("#snow_falling_all_cover").addClass("selected");
					used_effect_list.push("snow_falling");
					show_selected_all_cover_marker("snow_falling");
				}
				else {
					deselete_all_cover_marker("snow_falling");
					$("#snow_falling_all_cover").removeClass("selected"); 
				}
				proc_method = 'vlahos';
			});
			
			$("#math_bg_all_cover").click(function(){
				if(isImgExist() == false){
					alert("먼저 이미지를 업로드해주세요");
					return;
				}
				
				if($("#math_bg_all_cover").hasClass("selected") === false) {
					$("#math_bg_all_cover").addClass("selected");
					used_effect_list.push("math_bg");
					show_selected_all_cover_marker("math_bg");
				}
				else {
					deselete_all_cover_marker("math_bg");
					$("#math_bg_all_cover").removeClass("selected"); 
				}
				proc_method = 'vlahos';
			});
			
			$("#flower_scatter_all_cover").click(function(){
				if(isImgExist() == false){
					alert("먼저 이미지를 업로드해주세요");
					return;
				}
				
				if($("#flower_scatter_all_cover").hasClass("selected") === false) {
					$("#flower_scatter_all_cover").addClass("selected");
					used_effect_list.push("flower_scatter");
					show_selected_all_cover_marker("flower_scatter");
				}
				else {
					deselete_all_cover_marker("flower_scatter");
					$("#flower_scatter_all_cover").removeClass("selected"); 
				}
				proc_method = 'mask';
			});
			$("#jojo_onomatopoeia1_all_cover").click(function(){
				if(isImgExist() == false){
					alert("먼저 이미지를 업로드해주세요");
					return;
				}
				
				if($("#jojo_onomatopoeia1_all_cover").hasClass("selected") === false) {
					$("#jojo_onomatopoeia1_all_cover").addClass("selected");
					used_effect_list.push("jojo_onomatopoeia1");
					show_selected_all_cover_marker("jojo_onomatopoeia1");
				}
				else {
					deselete_all_cover_marker("jojo_onomatopoeia1");
					$("#jojo_onomatopoeia1_all_cover").removeClass("selected"); 
				}
				proc_method = 'mask';
			});
			
			$("#jojo_onomatopoeia2_all_cover").click(function(){
				if(isImgExist() == false){
					alert("먼저 이미지를 업로드해주세요");
					return;
				}
				
				if($("#jojo_onomatopoeia2_all_cover").hasClass("selected") === false) {
					$("#jojo_onomatopoeia2_all_cover").addClass("selected");
					used_effect_list.push("jojo_onomatopoeia2");
					show_selected_all_cover_marker("jojo_onomatopoeia2");
				}
				else {
					deselete_all_cover_marker("jojo_onomatopoeia2");
					$("#jojo_onomatopoeia2_all_cover").removeClass("selected"); 
				}
				proc_method = 'mask';
			});
			
			$("#jojo_onomatopoeia3_all_cover").click(function(){
				if(isImgExist() == false){
					alert("먼저 이미지를 업로드해주세요");
					return;
				}
				
				if($("#jojo_onomatopoeia3_all_cover").hasClass("selected") === false) {
					$("#jojo_onomatopoeia3_all_cover").addClass("selected");
					used_effect_list.push("jojo_onomatopoeia3");
					show_selected_all_cover_marker("jojo_onomatopoeia3");
				}
				else {
					deselete_all_cover_marker("jojo_onomatopoeia3");
					$("#jojo_onomatopoeia3_all_cover").removeClass("selected"); 
				}
				proc_method = 'mask';
			});
			
			
			
			
			///////////////////////////////////////////////////
			//
			// 마킹용 사각형 생성코드
			//
			// canvas Width, canvas Height, image Width, image Height, left, top를 json화 하여 python 코드에 넘겨준다.
			//
			///////////////////////////////////////////////////

			canvas.on('mouse:down', function(options){						// 마우스 왼쪽 버튼을 클릭할 경우
				// 이벤트 핸들러는 두가지 속성(e:이벤트 원본, target: 대상)을 
				// 가진 옵션(options)을 수신하게 됩니다.
				if(mode =='insert_mode' && isClick == false && options.target && options.target.type == 'image'){
					rect_left = options.e.layerX;
					rect_top = options.e.layerY;
					isClick = true;
					
				}
			});

			canvas.on("mouse:move", function(options){
				if(mode =='insert_mode' && isClick && options.target && options.target.type == 'image'){
					rect_right = options.e.layerX;
					rect_bottom = options.e.layerY;
					isMove = true;
					
				}
			});
			
			
			canvas.on("mouse:up", function(options){
			
				if(mode =='insert_mode' && isMove && isClick && options.target && options.target.type == 'image'){
					isClick = false;
					isMove = false;
					
					var_right = options.e.layerX;
					var_bottom = options.e.layerY;
					
					var tmp;
					
					if(rect_left > rect_right){
						tmp = rect_left;
						rect_left = rect_right;
						rect_right = tmp;
					}
					
					if(rect_top > rect_bottom){
						tmp = rect_top;
						rect_top = rect_bottom;
						rect_bottom = tmp;
					}
						
					
					//console.log("imgPosX : " + imgPosX  + "imgPosY : " + imgPosY);
					
					// 사각형이 뒤집혀질 경우 좌상단과 우하단 좌표를 바꾼다.
					
					const aCoords = {
						tl: new fabric.Point(rect_left, rect_top),
						tr: new fabric.Point(rect_right, rect_top),
						br: new fabric.Point(rect_right, rect_bottom),
						bl: new fabric.Point(rect_left, rect_bottom)
					}
					if(isSelected == false){						// 선택되어있지 않을 경우
							fabric.Image.fromURL('./marker_img/' + marker_label + '_marker_img.png', img => {
								const width = img.width;
								const height = img.height;
								
								img.set({
									left: aCoords.tl.lerp(aCoords.br).x,
									top: aCoords.tl.lerp(aCoords.br).y,
									scaleX:(aCoords.tl.distanceFrom(aCoords.tr)) / width,
									scaleY:(aCoords.tl.distanceFrom(aCoords.bl)) / height,
									originY: 'center',
									originX: 'center',
									label : marker_label,
									proc_method : proc_method
								});
													/////////////////////////////////////////////////////////////////
													//
													// 만들어진 마커에 마우스 콜백 함수를 등록하여 주요 마커 정보를 저장하게 한다. (648라인 오브젝트 scaled 이벤트에서 사용될 변수)
													//
													//
													/////////////////////////////////////////////////////////////////
													
								img.on('mousedown',function(options){
										var obj = options.target;
										var boundingRect = obj.getBoundingRect(true);
										
										selected_rect_scaleX = obj.scaleX;
										selected_rect_scaleY = obj.scaleY;
										
										selected_rect_width = boundingRect.width / 2;
										selected_rect_height = boundingRect.height / 2;});
								canvas.add(img);
							});
					}

				
					
					used_effect_list.push(marker_label);					// 사용한 마커들을 기록한다.
										
					rect_left = 0;
					rect_right = 0;
					rect_top = 0;
					rect_bottom = 0;
					
					//set_select_mode();
							

					
					//canvas.add(rect);
					//console.log(markerJSONList);
					
				}
				
			else{		// 만약 이미지의 영역을 넘겨서 사각형을 생성할 경우
			
					isClick = false;
					isMove = false;
			
					rect_left = 0;
					rect_right = 0;
					rect_top = 0;
					rect_bottom = 0;
					return;
						// 모든것을 기본값으로 초기화하고 종료한다.
			}
				
				
			}); 
		
			
			
			document.onkeydown = function(event){							// delete키 기능
				if(event.keyCode == 46){									// delete key			
					canvas.getActiveObjects().forEach((obj) => {
					for(let i = 0; i < used_effect_list.length; i++) {
						if(used_effect_list[i] == obj.label)  {
							used_effect_list.splice(i, 1);
							break;
						}
					}
					
					canvas.remove(obj);
					//console.log(markerJSONList);
					});
					canvas.discardActiveObject().renderAll()

				}
				
				if(event.keyCode == 27)										// esc키 기능
					esc_key();
				
			}
			
			///////////////////////////////////////////////////
			//
			// Canvas에 이미지 표시
			//
			//
			///////////////////////////////////////////////////
			
			function img_preprocess(image){
					var img_ratio;
					
					if(image.width > image.height){
						img_ratio = image.height / image.width;		// 이미지 원본 비율을 계산						
						img_width = Math.floor(canvas_width);
						img_height = Math.floor(canvas_height * img_ratio);
					}
					
					else {
						img_ratio = image.width / image.height;		// 이미지 원본 비율을 계산
						img_height = Math.floor(canvas_height);
						img_width = Math.floor(canvas_width * img_ratio);
					}
			// 바뀐 이미지의 가로 세로 길이를 계산하여 가져온다.
			
					img_left = (canvas_width - img_width) / 2;
					img_top = (canvas_height - img_height) / 2;
			// 이미지의 좌상단 좌표를 계산한다.
			}

			
			
			document.getElementById('input-image').addEventListener("change", 		// 이미지가 변경되면
			function (e) {			
				canvas.clear();						// 이미지가 바뀌면 캔버스도 초기화한다

				
				var image_data = $('#input-image').prop('files')[0];
				var imageData = new FormData();
				imageData.append("image", image_data);
				imageData.append("task_folder", task_folder);						// ajax로 이미지를 임시 저장할 폴더 이름을 보낸다.
					
				jQuery.ajax({														// editor_tmp_const에 ajax를 보내 서버에 이미지를 저장한다.
					type : "POST",
					url : "./editor_svr_side.php",
					processData : false,
					contentType : false,
					data : imageData,
					success : function(_path){										// 이미지가 서버에 완전히 업로드 된 경우 이미지의 임시 경로를 _path에 받는다.
						//alert("isWorking?");
						var file = e.target.files[0];
						var reader = new FileReader();
						reader.onload = function (f) {
							var data = f.target.result;	
							var image = new Image();
							image.src = reader.result;
							
							image.onload=function(){	

									img_preprocess(image);
									markerJSONList[JSONListIdx].base_img_info.width = img_width;
									markerJSONList[JSONListIdx].base_img_info.height = img_height
									markerJSONList[JSONListIdx].base_img_info.path = _path;																	
									
									// 이미지를 로딩한 순간부터 다음 인덱스를 가리킨다.
							}
																		
							fabric.Image.fromURL(data, function (img) {
								var oImg = img.set({left: 0, top: 0, angle: 0, selectable:false});
								
								oImg.scaleToWidth(img_width);
								oImg.scaleToHeight(img_height);
								
								canvas.centerObject(img);
								canvas.add(oImg).renderAll();
							});
						// 이미지를 canvas객체에 표시한다.
							
						};
						reader.readAsDataURL(file);
					},
					error : function(err){
						console.log("error : " + err);
					}
				
				});
			
			});
			
			canvas.on('object:moved', function (options) {						// 마킹용 사각형이 이미지 영역을 빠져나가는 것을 방지함.
				var obj = options.target;
				var boundingRect = obj.getBoundingRect(true);
				
				if(typeof obj._objects != "undefined" && obj._objects.length >= 2){	// 객체를 그룹으로 선택할 때
				
					if(img_width > img_height){										// 이미지 비율이 width가 height보다 클 경우
						
						if(boundingRect.top < img_top)
							obj.top = img_top;
						
						if(boundingRect.left < 0)
							obj.left = 0;
							
						if(boundingRect.left + boundingRect.width > canvas_width)
							obj.left -= boundingRect.left + boundingRect.width - canvas_width + 1;			// 객체가 벽에 붙을 때 여백 1을 주어 버그가 발생함을 방지한다.
							
						if(boundingRect.top + boundingRect.height > img_height + img_top)
							obj.top -= boundingRect.top + boundingRect.height - (img_height + img_top) + 1;			// 객체가 벽에 붙을 때 여백 1을 주어 버그가 발생함을 방지한다.
						
					}
					
					else{
										
						if(boundingRect.top < 0)
							obj.top = 0;
						
						if(boundingRect.left < img_left)
						obj.left = img_left;
							
						if(boundingRect.left + boundingRect.width > img_left + img_width)
							obj.left -= boundingRect.left + boundingRect.width - (img_left + img_width) + 1;			// 객체가 벽에 붙을 때 여백 1을 주어 버그가 발생함을 방지한다.
							
						if(boundingRect.top + boundingRect.height > canvas_height)
							obj.top -= boundingRect.top + boundingRect.height - canvas_height + 1;			// 객체가 벽에 붙을 때 여백 1을 주어 버그가 발생함을 방지한다.
						
					}
				
				}
				else if(typeof obj._objects == "undefined"){						// 단일 객체가 선택되었을 때
					if(img_width > img_height){										// 이미지 비율이 width가 height보다 클 경우
					
						if(boundingRect.top < img_top)
							obj.top = img_top + boundingRect.height / 2;
						
						if(boundingRect.left < 0)
							obj.left = boundingRect.width / 2 ;
							
						if(boundingRect.left + boundingRect.width > canvas_width)
							obj.left -= boundingRect.left + boundingRect.width - canvas_width + 1;			// 객체가 벽에 붙을 때 여백 1을 주어 버그가 발생함을 방지한다.
							
						if(boundingRect.top + boundingRect.height > img_height + img_top)
							obj.top -= boundingRect.top + boundingRect.height - (img_height + img_top) + 1;			// 객체가 벽에 붙을 때 여백 1을 주어 버그가 발생함을 방지한다.
						
					}
					
					else{															// 이미지 비율이 height이 width보다 클 경우
					
						if(boundingRect.top < 0)
							obj.top = boundingRect.height / 2;
						
						if(boundingRect.left < img_left)
							obj.left = img_left + boundingRect.width / 2;
							
						if(boundingRect.left + boundingRect.width > img_left + img_width)
							obj.left -= boundingRect.left + boundingRect.width - (img_left + img_width) + 1;			// 객체가 벽에 붙을 때 여백 1을 주어 버그가 발생함을 방지한다.
							
						if(boundingRect.top + boundingRect.height > canvas_height)
							obj.top -= boundingRect.top + boundingRect.height - canvas_height + 1;			// 객체가 벽에 붙을 때 여백 1을 주어 버그가 발생함을 방지한다.
					
					}
				}
			});

			canvas.on('object:scaled', function (options) {									// 마킹용 사각형의 사이즈를 조절할 때 이미지 영역을 빠져나가는것을 방지함.
				var obj = options.target;
				var boundingRect = obj.getBoundingRect(true);
				
				if(typeof obj._objects != "undefined" && obj._objects.length >= 2){			// 객체를 그룹으로 선택할 때
				
					if (img_width > img_height && boundingRect.left < 0
						|| boundingRect.top < img_top
						|| boundingRect.left + boundingRect.width > canvas_width
						|| boundingRect.top + boundingRect.height > img_height + img_top) {
						
						obj.left = img_left;
						obj.top = img_top;
						
						obj.scaleX = selected_group_scaleX;
						obj.scaleY = selected_group_scaleY;
	
					
					}
					
					else if(img_width <= img_height && boundingRect.left < 0
						|| boundingRect.top < img_top
						|| boundingRect.left + boundingRect.width > img_left + img_width
						|| boundingRect.top + boundingRect.height > canvas_height){
					
						obj.left = img_left;
						obj.top = img_top;
						
						obj.scaleX = selected_group_scaleX;
						obj.scaleY = selected_group_scaleY;
					
					}
					
				}
				
				else if(typeof obj._objects == "undefined"){									// 단일 객체가 선택되었을 때
						
						if (img_width > img_height && boundingRect.left < 0
							|| boundingRect.top < img_top
							|| boundingRect.left + boundingRect.width > canvas_width
							|| boundingRect.top + boundingRect.height > img_height + img_top) {
							
							obj.left = img_left + selected_rect_width;
							obj.top = img_top + selected_rect_height;
							
							obj.scaleX = selected_rect_scaleX;
							obj.scaleY = selected_rect_scaleY;
		
						
						}
						
						else if(img_width <= img_height && boundingRect.left < 0
							|| boundingRect.top < img_top
							|| boundingRect.left + boundingRect.width > img_left + img_width
							|| boundingRect.top + boundingRect.height > canvas_height){
						
							obj.left = img_left + selected_rect_width;
							obj.top = img_top + selected_rect_height;
							
							obj.scaleX = selected_rect_scaleX;
							obj.scaleY = selected_rect_scaleY;
						
						}
				}
				
			});

			canvas.on('selection:created', function (e) {			// 마킹용 사각형의 회전을 막아놓음, 또한 오브젝트 선택 이벤트를 발생시킴
				const activeSelection = e.target;
				activeSelection.set({lockRotation : true});
				isSelected = true;
				
				if(typeof activeSelection._objects != "undefined"){		// 그룹으로 선택되었을 때 그룹의 스케일 X,Y값을 가져온다.
					selected_group_scaleX = activeSelection.scaleX;
					selected_group_scaleY = activeSelection.scaleY;
				}
				
			});
			
			canvas.on('selection:cleared', function(e){				// 오브젝트 선택이 취소될 경우 isSelected를 false로 둬 새로운 마커를 생성할 수 있게 함.
				isSelected = false;
				
			});
			
			// fired e.g. when you select one object first,
			// then add another via shift+click
			canvas.on('selection:updated', function (e) {			// 마킹용 사각형의 회전을 막아놓음
				const activeSelection = e.target;
				if (activeSelection.hasRotatingPoint) {
					activeSelection.set({lockRotation : true});
				}
			});
			
			function clear_selected_all_cover(){
				for(var i = 1; i < $("#all_cover").children().length; i++)		// 선택된 전체 이펙트를 전부 초기화한다
					$("#all_cover").children().eq(i).removeClass("selected");
			}
			
			function restore_selected_all_cover(markerObj){			// 이미 기록한 컷을 불러올 때, 선택된 전체 적용 효과를 복구한다.
			
				var effect_id = "#" + markerObj.label + "_all_cover";
				$(effect_id).addClass('selected');
				//show_selected_all_cover_marker(markerObj.label);	// 선택된 전체 효과 마커를 보여준다.
				restore_selected_all_cover_marker(markerObj);
			}
			
				
			function clear_canvas(){								// 현재 컷을 지운다.
				canvas.clear();
				markerJSONList[JSONListIdx].Markers = [];
				markerJSONList[JSONListIdx].base_img_info.path = "deleted";			
				clear_selected_all_cover();
				//console.log(markerJSONList);
			}
			
			function preview_effect(){
					get_cur_markers();
					var preview_json = $.extend({}, markerJSONList[JSONListIdx]);			// JSON Object를 복사한다.
					preview_json.task_dir = "../editor_task_dir/" + task_folder;			// 사본에 task_dir값을 넣는다.
					
					var json = JSON.stringify(preview_json);
					console.log(json);
					alert("영상 합성을 시작합니다");
	
					jQuery.ajax({
							type: "POST",
							url: "preview_cut.py",
							contentType :'charset=UTF-8',
							data: json,
							success:function(result){
								
								alert("영상 합성 완료");
								localStorage.setItem("task_folder", preview_json.task_dir);
								window.open("preview_page.php");
								// for debug
								//show_marker_json(result);
								
							},error: function(xhr, status, error){
								alert(error); 
							}
			
				});
				
				canvas.renderAll();
				////////////////////////////////////////////////////////////////////////
				//
				// 영상 합성이 완료된 후 markerJSON을 초기화한다.											
				// 합성완료 후 베이스 이미지에 대한 정보가 모두 초기화되므로
				// 같은 이미지를 두번 합성할 때 오류가 발생하게 된다. (미리보기 구현 때 해결 필요...)
				//
				////////////////////////////////////////////////////////////////////////

				
			}




	function show_marker_json(result){
			var resultString = "canvas width : " + result.canvas_info.width + "\n" +
							"canvas height : " + result.canvas_info.height + "\n" +
							"base img width : " + result.base_img_info.width + "\n" +
							"base img height : " + result.base_img_info.height + "\n";
			for(var i = 0; i < result.Markers.length; i++){
				resultString +=  i + " markers : left " + result.Markers[i].left +
								" top " + result.Markers[i].top + 
								" width " + result.Markers[i].width +
								" height "+ result.Markers[i].height +
								" label " + result.Markers[i].label + '\n';
			
			}			
			alert(resultString);
	}

	//////////////////////////////////////////////////
	//
	// 마커 배열을 모두 초기화하여 새로 만들어 리턴한다.
	//
	// 에디터의 모든 버튼을 눌렀을 때 호출된다.
	//
	// 마커 리스트를 갱신한다.
	//
	//////////////////////////////////////////////////
	function getMarkers(){
		canvas.discardActiveObject();						// 모든 선택을 해제한다. (오브젝트들이 여러개 선택되어 있으면 좌표가 바뀌기 때문)

		var markers = new Array();	
		
		for(var i = 1; i < canvas._objects.length; i++){	// 첫번째 요소부터가 마커이므로 i는 1부터 시작한다.
		
			if (canvas._objects[i].label){					// label 속성을 가지고 있는 오브젝트는 marker로 취급한다.

				var markerObj = new Object();	
				
				// 편집기 상에서의 좌상단 좌표
				markerObj.left = canvas._objects[i].aCoords.tl.x | 0;
				markerObj.top = canvas._objects[i].aCoords.tl.y | 0;
				markerObj.width = canvas._objects[i].width * canvas._objects[i].scaleX | 0;
				markerObj.height = canvas._objects[i].height * canvas._objects[i].scaleY | 0;
				markerObj.label = canvas._objects[i].label;
				markerObj.is_all_cover = false;
				
				if(canvas._objects[i].scaleX)					// 마커의 가로길이가 변경되어 스케일값X을 가질 경우
					markerObj.width = canvas._objects[i].width * canvas._objects[i].scaleX;
	
				if(canvas._objects[i].scaleY)					// 마커의 세로길이가 변경되어 스케일값Y을 가질 경우
					markerObj.height = canvas._objects[i].height * canvas._objects[i].scaleY;				
				
				
				// 영상 합성과정에서 사용될 좌상단 좌표
				if(img_width > img_height){
					markerObj.proc_coord_x = Math.floor(markerObj.left) + 1;
					markerObj.proc_coord_y = Math.floor(markerObj.top - (canvas_height - img_height) / 2) + 1;
				}
				
				else{
					markerObj.proc_coord_x = Math.floor(markerObj.left - (canvas_width - img_width) / 2) + 1;
					markerObj.proc_coord_y = Math.floor(markerObj.top) + 1;
				}
				
				markerObj.proc_method = canvas._objects[i].proc_method;
				//markerObj.proc_method = proc_method;
				markers.push(markerObj);
				console.log(markerObj);
			}			
		}		// marker 오브젝트를 추출해 JSON으로 만든다.
		
		
		/////////////////////////////////// 전체 효과 마커들을 기록한다 ///////////////////////////////////////

		
		for(var i = 1; i < $("#all_cover").children().length; i++){		// 선택된 전체 이펙트들을 html요소에서 가져온다.
			var selected = $("#all_cover").children().eq(i);			// 0번째는 p 태그이므로 1번째부터 시작한다.
			//var marker_idx = 1;
			
			if(selected.hasClass("selected") == true){
				//alert("isWorking?");
				var markerObj = new Object();

				// 편집기 상에서의 좌상단 좌표
				markerObj.left = (canvas._objects[0].aCoords.tl.x | 0) + 1;
				markerObj.top = (canvas._objects[0].aCoords.tl.y | 0) + 1;
				markerObj.width = img_width - 1;
				markerObj.height = img_height - 1;
				// 마진을 주어 좌표가 어긋나는것을 방지한다.

				var label = selected.attr('id');
				label = label.replace("_all_cover","");					// _all_cover문자열을 지워 label을 추출한다.
				markerObj.label = label;
				markerObj.is_all_cover = true;
				
				// 영상 합성과정에서 사용될 좌상단 좌표
				markerObj.proc_coord_x = 1;
				markerObj.proc_coord_y = 1;
				
				
				//console.log("idx value : " + i);				
				for(var idx = 1; idx < canvas._objects.length; idx++){
					
					if(markerObj.is_all_cover == true && markerObj.label == canvas._objects[idx].all_cover_label)
						markerObj.proc_method = canvas._objects[idx].proc_method;

				}
				// html로 나열된 효과 적용 순서와 canvas object에 나열된 효과 적용 순서가 일치하지 않으므로,
				// 마커 이름이 일치할 때만 proc_method를 기록한다.
				
				markers.push(markerObj);
				//console.log(markerObj);

			}
		}
		//console.log(markers)

		return markers;
		
	}
	function restore_cut(markerObj){

		if(markerObj.base_img_info.path != 'none' 
				&& markerObj.base_img_info.path != 'inserted'
				&& markerObj.base_img_info.path != 'deleted'){ 
			fabric.Image.fromURL(markerObj.base_img_info.path, function (img) {
				img_preprocess(img);
				var oImg = img.set({left: 0, top: 0, angle: 0, selectable:false});
				
				oImg.scaleToWidth(img_width);
				oImg.scaleToHeight(img_height);
				
				canvas.centerObject(img);
				canvas.add(oImg).renderAll();
				
				for(var i = 0; i < markerObj.Markers.length; i++){
					
					if(markerObj.Markers[i].is_all_cover == false){						// 전체 효과 적용 옵션이 아닐 경우만 마커를 복구한다.
						
						const aCoords = {
							tl: new fabric.Point(markerObj.Markers[i].left, markerObj.Markers[i].top),
							tr: new fabric.Point(markerObj.Markers[i].width + markerObj.Markers[i].left, markerObj.Markers[i].top),
							br: new fabric.Point(markerObj.Markers[i].width + markerObj.Markers[i].left, markerObj.Markers[i].height + markerObj.Markers[i].top),
							bl: new fabric.Point(markerObj.Markers[i].left, markerObj.Markers[i].height + markerObj.Markers[i].top),
							label : markerObj.Markers[i].label,
							proc_method : markerObj.Markers[i].proc_method
						}				// 좌표 성분에 label 성분과 proc_method 성분을 추가한다.
						
						
						fabric.Image.fromURL('./marker_img/' + markerObj.Markers[i].label + '_marker_img.png', function(img) {
							const width = img.width;
							const height = img.height;	
							
							//console.log("is Valied? : " + i);
							
							img.set({
								left: aCoords.tl.lerp(aCoords.br).x,
								top: aCoords.tl.lerp(aCoords.br).y,
								scaleX:(aCoords.tl.distanceFrom(aCoords.tr)) / width,
								scaleY:(aCoords.tl.distanceFrom(aCoords.bl)) / height,
								originY: 'center',
								originX: 'center',
								label: aCoords.label,
								proc_method : aCoords.proc_method
							});			// 컷을 복구할 때 label과 proc_method를 복구한다.
							
							img.on('mousedown',function(options){
										var obj = options.target;
										var boundingRect = obj.getBoundingRect(true);
										
										selected_rect_scaleX = obj.scaleX;
										selected_rect_scaleY = obj.scaleY;
										
										selected_rect_width = boundingRect.width / 2;
										selected_rect_height = boundingRect.height / 2;});
							
							canvas.add(img);
						});	

						//console.log(markerObj);	
						canvas.renderAll();
						

					}
					///////////////////////////////////////////////////// 기록되어있는 전체 적용 마커 정보들을 복구하여 체크된 것을 복구한다./////////////////////////////////////////////////////
					else
						restore_selected_all_cover(markerObj.Markers[i]);
					
				}
			});	
		}
		//else load_default_img();		
	}
	
	
	function get_prev_cut(){											// 이전 컷 버튼을 누를 때 발생

		if(JSONListIdx > 0){
			if(isDeleted == false){											// 삭제된 상태가 아닐 경우에만 마커를 저장한다.
				get_cur_markers();
				clear_selected_all_cover();
				canvas.clear();												// 캔버스를 지운다.
			}
			JSONListIdx--;
			restore_cut(markerJSONList[JSONListIdx]);					// 이전 컷을 복원한다.
			
		}
		else alert("현재 첫번째 페이지입니다.");
		
		//console.log(markerJSONList);		
		//console.log("idx : " + JSONListIdx);	
	}
	function get_next_cut(){											// 다음 키를 누르는 순간, JSON객체를 리스트에 삽입한다.

		//console.log(markerJSONList);

		if(markerJSONList[JSONListIdx].base_img_info.path == "none"){
																		// 다음 버튼을 누를 때 이미지를 업로드 하지 않았을 경우
			alert("현재 마지막 페이지입니다.");					// 이미지 업로드 요청을 한다.
			return;
		}
	
		// 이미지를 업로드한다. (363 Line에 이미지 업로드 코드가 존재)
		
		if(markerJSONList[JSONListIdx].base_img_info.path != "none"){	// 다음키를 눌렀을 때 none이 아니라면, 
			
			get_cur_markers();											// 마커객체와 이미지를 먼저 저장한 뒤, 
			canvas.clear();												// 캔버스를 지운다.
			clear_selected_all_cover();									// 전체 적용 마커 표기를 초기화한다.		
			//console.log(markerJSONList[JSONListIdx].Markers);
			
			
			if(JSONListIdx < markerJSONList.length){					// JSONListIdx를 1 증가시켜 다음 컷으로 이동한다.
				JSONListIdx++;
				
				if(typeof markerJSONList[JSONListIdx] == "undefined")	// 다음 컷이 정의되어 있지 않다면	
					add_markerJSONList();								// JSONList에 새로운 원소를 추가한다.
				
				else restore_cut(markerJSONList[JSONListIdx]);			// 그렇지 않을 경우 다음 컷을 복원한다.
		
				return;
			}
		}
		// 이미지를 업로드한다. (363 Line에 이미지 업로드 코드가 존재)
		
	}
	
	function delete_cut(){
		if(confirm("현재 컷을 지우시겠습니까?")){									// 지울 때 캔버스를 먼저 다 지운 뒤 리스트를 조작한다.	
			canvas.clear();													// 캔버스를 clear해버리면 삭제 대상의 마커가 모두 다음컷에 영향을 주므로 플래그를 준다.
			isDeleted = true;												// 캔버스를 완전히 비운 상태임을 표시함.
			if(JSONListIdx > 0 && JSONListIdx < markerJSONList.length){		// 중간에 삽입되어 있는 컷을 지울 때
				markerJSONList.splice(JSONListIdx, 1);
				get_prev_cut();

			}
			else if(JSONListIdx == 0){										// 가장 맨 앞의 컷(idx : 0)을 지울 때
				markerJSONList.splice(JSONListIdx, 1);
				if(typeof markerJSONList[0] != "undefined")
					restore_cut(markerJSONList[0]);
			}
			//console.log(markerJSONList);
			isDeleted = false;

		}

		
	}
	
	function make_inserted_cut(){
		var emptyObj = new Object();
		
		emptyObj.canvas_info = new Object();
		emptyObj.canvas_info.width = canvas_width;
		emptyObj.canvas_info.height = canvas_height;
	
		emptyObj.base_img_info = new Object();
		
		emptyObj.base_img_info.path = "inserted";
		emptyObj.base_img_info.width = 0;
		emptyObj.base_img_info.height = 0;

		emptyObj.Markers = new Array();	
		
		return emptyObj;
		
	}
	
	function load_default_img(){
		fabric.Image.fromURL("../img/default_img2.jpg", function (img) {
			img_preprocess(img);
			var oImg = img.set({left: 0, top: 0, angle: 0, selectable:false});
			
			oImg.scaleToWidth(canvas_width);
			oImg.scaleToHeight(canvas_height);
			
			canvas.centerObject(img);
			canvas.add(oImg).renderAll();
		});	
	}
	
	function insert_cut_forward(){
		get_cur_markers();
		clear_selected_all_cover();
		// 컷을 추가해 인덱스를 증가시키기 전에 마커들을 저장한다.

		canvas.clear();
		var emptyObj = make_inserted_cut();
		markerJSONList.splice(JSONListIdx, 0, emptyObj);
		//load_default_img();
	}

	function insert_cut_back(){
		
		if(markerJSONList[JSONListIdx].base_img_info.path == "none"){
			alert("현재 컷이 마지막 컷입니다.");
			return;
		}
		get_cur_markers();
		clear_selected_all_cover();
		canvas.clear();
		var emptyObj = make_inserted_cut();
		JSONListIdx++;
		markerJSONList.splice(JSONListIdx, 0, emptyObj);
		//load_default_img();
		
	}


	//////////////////////////////////////////////////////////////////
	// !!!현재 수정된 컷의 모든 마커들을 저장한다.
	//
	//////////////////////////////////////////////////////////////////

	function get_cur_markers(){											
		var markerObj = getMarkers();									// 현재 컷의 마커 정보들을 전부 가져와 저장한다.
		markerJSONList[JSONListIdx].Markers = markerObj;
	}

	function chk_empty_cut(){
		
		for(var i = 0; i < markerJSONList.length; i++){				// 업로드 중 비워져있는 컷이 있을 경우 
			if(markerJSONList[i].base_img_info.path == "none" ||
				markerJSONList[i].base_img_info.path == "inserted" ||
				markerJSONList[i].base_img_info.path == "deleted"){
			alert((i + 1) + "번째 컷이 비워져 있습니다. 최소 1개 이상의 이미지를 올려주세요");		// 이미지 업로드를 요청
			return true;
			}
		}
		
		return false;
		
	}
	
	function get_cut_info(){
		
		const used_effect_set = new Set(used_effect_list);			// used_effect_list를 Set으로 만들어 중복을 제거하여 각 원소가 unique하도록 만든다.
		used_effect_list = [...used_effect_set];					// 만들어진 집합 객체를 다시 배열로 변환한다.
		get_cur_markers();											// 업로드 버튼을 눌렀을 때의 보여지고 있는 컷의 정보를 가져온다.
		//console.log(markerJSONList);
		
		var cut_info = new Object();
		cut_info.title = $('#title').val();
		cut_info.brief_comment = $('#brief_comment').val();
		cut_info.task_dir = task_folder;
		cut_info.used_effect_list = used_effect_list;
		cut_info.cuts = markerJSONList;								// 사용된 크로마키 영상과 컷의 정보들을 하나의 JSON Object로 묶는다.
				
		return cut_info;
	}

	function upload_webtoon(){
		
		if($('#title').val() == ""){								// 제목 확인
			alert("제목을 입력해주세요");
			$('#title').focus();
			return;
		}	

	
		if(chk_empty_cut() == true)
			return;
		
		var cut_info = get_cut_info();								// 모든 컷의 정보들을 가져와 json 형식으로 만든다.
		
		//LoadingWithMask("LoadingSpinner.gif");
		console.log(cut_info);
		
		if(isModify == 'none'){		
			if(confirm("웹툰을 업로드하겠습니까?")){
				
				cut_info.request_new_dir = true;				
				var webtoon_data = JSON.stringify(cut_info);
				
				jQuery.ajax({											// 먼저 새로운 디렉토리를 생성한다.
					type: "POST",
					url: "editor_svr_side.php",
					contentType :'application/json; charset=UTF-8',
					data: webtoon_data,
					//async: false,
					success:function(result){
					},error: function(xhr, status, error){
						alert(error);
					}
				});	
				
				cut_info.request_new_dir = false;				
				webtoon_data = JSON.stringify(cut_info);
				//console.log(cut_info);	

				//LoadingWithMask("LoadingSpinner.gif");			
				jQuery.ajax({											// 영상을 합성한다.
					type: "POST",
					url: "chromakey_process.py",
					contentType :'application/json; charset=UTF-8',
					data: webtoon_data,
					async: false,
					success:function(result){
						
					},error: function(xhr, status, error){
						alert(error);
						alert("isError?3");
					}
				});
				

				var thumb_data = new FormData();
				
				thumb_data.append("task_dir", task_folder);
				thumb_data.append("thumbnail", $('#thumb-image')[0].files[0]);
				thumb_data.append("default_thumbnail", cut_info.cuts[0].base_img_info.path);
				
				jQuery.ajax({											// 썸네일을 업로드한다.
					url: "upload_thumb.php",
					type: "POST",
					data: thumb_data,
					contentType: false,
					processData:false,
					//async: false,
					success:function(result){


					},error: function(xhr, status, error){
						alert(error);
					}
				});
				
				jQuery.ajax({											// db에 기록한다
					type: "POST",
					url: "editor_svr_side.php",
					contentType :'application/json; charset=UTF-8',
					data: webtoon_data,
					success:function(result){
						//closeLoadingWithMask();
						alert("업로드 완료");
						window.history.back();
						
					},error: function(xhr, status, error){
						alert(error);
					}
				});	

							
			}		// if confirm
		}		// if isModify == none
		else if(isModify == 'modify'){
			
			if(confirm("수정된 내용을 업로드하시겠습니까?")){

				cut_info.isModified = isModify;						// editor_svr_side에 수정된 cut_info임을 알리기 위해 플래그값을 넣는다.				
				//console.log(cut_info);
				
				cut_info.post_no = $('#request_post_no').val();
				var webtoon_data = JSON.stringify(cut_info);

				//console.log(webtoon_data);
	
				//LoadingWithMask("LoadingSpinner.gif");
				jQuery.ajax({										// 현재 post경로에서 파일들을 모두 지우고 작업 디렉토리(editor_task_dir)에 수정된 json파일을 기록한다.
					type: "POST",
					url: "editor_svr_side.php",
					contentType :'application/json; charset=UTF-8',
					data: webtoon_data,
					//async: false,
					success:function(result){
						//alert(result);
					},error: function(xhr, status, error){
						alert(error);
					}
				});
				
				jQuery.ajax({										// 최종적으로 영상 합성을 시작한다.
					type: "POST",
					url: "chromakey_process.py",
					contentType :'application/json; charset=UTF-8',
					data: webtoon_data,
					async: false,
					success:function(result){
						//alert(result);
					},error: function(xhr, status, error){
						alert(error);
					}
				});

				var thumb_data = new FormData();					
				
				thumb_data.append("task_dir", task_folder);
				thumb_data.append("thumbnail", $('#thumb-image')[0].files[0]);
				thumb_data.append("default_thumbnail", cut_info.cuts[0].base_img_info.path);
				
				jQuery.ajax({										// 썸네일을 변경한다.
					url: "upload_thumb.php",
					type: "POST",
					data: thumb_data,
					contentType: false,
					processData:false,
					success:function(result){

						alert("업로드 완료");
						//window.history.back();
						
					},error: function(xhr, status, error){
						alert(error);
					}
				});

						
			}	// if confirm
			
		}	// if isModify == modify
		
		
	}
	
	function readImage(input) {
	// 인풋 태그에 파일이 있는 경우
		var fileForm = /(.*?)\.(jpg|jpeg|png|gif|bmp|pdf)$/;
		var imgFile = $("#thumb-image").val();							
		// 정규식
	
		if(input.files && input.files[0]) {
			
			if(!imgFile.match(fileForm)){
				alert("이미지 파일만 업로드 가능합니다.");
				location.reload();
			}
		}
	}
	
	
	$(document).ready(function(){	
		const inputImage = document.getElementById("thumb-image");
		
		if(inputImage != null){
			inputImage.addEventListener("change", e =>{ 
				readImage(e.target) });
		}
	});

	
	function LoadingWithMask() {
		//화면의 높이와 너비를 구합니다.
		var maskHeight = $(document).height();
		var maskWidth  = window.document.body.clientWidth;
		
		//화면에 출력할 마스크를 설정해줍니다.
		var mask       ="<div id='mask' style='position:absolute; z-index:9000; background-color:#000000; display:none; left:0; top:0;'></div>";
		var loadingImg ='';
		
		loadingImg +="<div id='loadingImg'>";
		loadingImg +=" <img src='LoadingSpinner.gif' style='position: relative; display: block; margin: 0px auto;'/>";
		loadingImg +="</div>"; 
	
		//화면에 레이어 추가
		$('body')
			.append(mask)
			.append(loadingImg)
			
		//마스크의 높이와 너비를 화면 것으로 만들어 전체 화면을 채웁니다.
		$('#mask').css({
				'width' : maskWidth
				,'height': maskHeight
				,'opacity' :'0.3'
		});
	
		//마스크 표시
		$('#mask').show();  
	
		//로딩중 이미지 표시
		$('#loadingImg').show();
	}


	
	function closeLoadingWithMask() {
		$('#mask, #loadingImg').hide();
		$('#mask, #loadingImg').empty(); 
	}
	
	function move_cut_forward(){
		
		if(JSONListIdx <= 0){							// 컷이 가장 앞에 있을 경우
			alert("현재 컷이 가장 앞에 있습니다.");
			return;
		}
		else {											// 앞으로 이동할 수 있을 경우
			var forward_cut = markerJSONList[--JSONListIdx];
			markerJSONList[JSONListIdx] = markerJSONList[JSONListIdx + 1];
			markerJSONList[JSONListIdx + 1] = forward_cut;
			console.log(markerJSONList);
		}
		
	}
	
	function move_cut_back(){
		
		if(markerJSONList[JSONListIdx].base_img_info.path == "none"){			// 컷이 가장 뒤에 있을 경우
			alert("현재 컷이 가장 뒤에 있습니다.");
			return;
		}
		else {																	// 뒤로 이동할 수 있을 경우 
			var back_cut = markerJSONList[++JSONListIdx];
			markerJSONList[JSONListIdx] = markerJSONList[JSONListIdx - 1];
			markerJSONList[JSONListIdx - 1] = back_cut;
			console.log(markerJSONList);
			
		}
		
	}


	</script>

	
</body>

</html>
