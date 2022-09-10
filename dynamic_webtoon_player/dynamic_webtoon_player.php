<!DOCTYPE html>

<?php

/*
header("Cache-Control: no-store, must-revalidate");
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Content-Type: application/xml; charset=utf-8");
*/

include_once "../db_manager.php";
include_once "../session.php";
include_once "../board.php";
session_start();							// 세션변수를 사용하려면 무조건 호출되어야 한다.

define("UNAVAILABLE_VALUE", -1 , FALSE);

if(isset($_GET["post_no"]))					// 입력된 글의 번호를 받는다.
	$post_no = $_GET["post_no"];

else $post_no = UNAVAILABLE_VALUE;

if(isset($_SESSION["nickname"]))
	$cur_user = $_SESSION["nickname"];

else $cur_user = UNAVAILABLE_VALUE;

if(isset($_SESSION["id_no"]))
	$id_no = $_SESSION["id_no"];

else $id_no = UNAVAILABLE_VALUE;


function clearBrowserCache() {
	header("Pragma: no-cache");
	header("Cache: no-cache");
	header("Cache-Control: no-cache, must-revalidate");
	header("Expires:Mon, 26 Jul 1997 05:00:00 GMT");
}
clearBrowserCache();

increase_hits("dynamic_webtoon", $post_no);

?>

<html>

<head>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <!-- Bootstrap core JS-->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js"></script>
        <!-- Core theme JS-->
		<script src="../js/scripts.js"></script>
		<!-- 네비게이션바의 리스트 기능을 위해 필요-->
		
		<!--
		<meta http-equiv="Expires" content="Mon, 06 Jan 1990 00:00:01 GMT" />
		<meta http-equiv="Expires" content="-1" />
		<meta http-equiv="Pragma" content="no-store" />	
		<meta http-equiv="Cache-Control" content="no-store" />-->
		
		
		<meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>Open Webtoon</title>
        <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
        <!-- Bootstrap icons-->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css" rel="stylesheet" />
        <!-- Google fonts-->
        <link rel="preconnect" href="https://fonts.gstatic.com" />
        <link href="https://fonts.googleapis.com/css2?family=Newsreader:ital,wght@0,600;1,600&amp;display=swap" rel="stylesheet" />
        <link href="https://fonts.googleapis.com/css2?family=Mulish:ital,wght@0,300;0,500;0,600;0,700;1,300;1,500;1,600;1,700&amp;display=swap" rel="stylesheet" />
        <link href="https://fonts.googleapis.com/css2?family=Kanit:ital,wght@0,400;1,400&amp;display=swap" rel="stylesheet" />
        <!-- Core theme CSS (includes Bootstrap)-->
        <link href="../css/styles.css" rel="stylesheet" />
		
		<style type="text/css">
			.text-border{
				border-width: 0px 0px 1px 0px;
				border-style: solid;
				padding-top:1rem;
				padding-bottom:1rem;
				text-align:center;
				
			}
			
			.descript-area{
				background:#ffffff;
			}
			
			.div-height{
				height:700px;
			}
			
			.center {
				height:700px;
				position: relative;
				background:#efefef;
			}
			
			.center_p {
				margin: 0;
				position: absolute;
				top: 50%;
				left: 50%;
				transform: translate(-50%, -50%);
				height:100%;
				width:100%;
				object-fit:contain;
				padding:10px;
			}
			
			.comment-area{
				text-align:right;
				margin-top:50px;
			}
			
			.cmt_element{
				border-width: 0px 0px 1px 0px;
				border-style: solid;
				text-align:left!important;
			}
			.comment_button{ 
				border:1px;
				border-style: solid;	
			}
			.comment_register_btn{
				margin-bottom:75px;
				padding:25px;
			}
			.comment_modify_btn{
				margin-bottom:75px;
				padding:15px;
			}
			.comment_cancel_btn{
				margin-bottom:75px;
				padding:15px;
			}
			
		</style>
</head>


<body>
		<input type="hidden" id="logout" value="<?php echo REQUEST_LOGOUT; ?>"/>
        <!-- Navigation-->
        <nav class="navbar navbar-expand-lg navbar-light fixed-top shadow-sm" id="mainNav">
            <div class="container px-5">
                <a class="navbar-brand fw-bold" href="/">Open Webtoon</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
                    Menu
                    <i class="bi-list"></i>
                </button>
                <div class="collapse navbar-collapse" id="navbarResponsive">
                    <ul class="navbar-nav ms-auto me-4 my-3 my-lg-0">						
						
						<script>
					
							function logout(){
							
								jQuery.ajax({
								type: "POST",
								url: "../session.php",
								contentType :'charset=UTF-8',
								data: "LogOut",
								success:function(result){
									if(result == $('#logout').val())
										location.href = "/";	// main homepage로 돌아감
											
									},error: function(xhr, status, error){
										alert(error);
									}
								});
							
							}
								
								
								
						//////////////////////////////////////////
						//
						//	show_comment() 덧글 보여주기 기능
						//
						//////////////////////////////////////////			
								
								
								
							var is_cmt_show = false;
							var is_load_cmt_once = false;
							var swap_count = 0;
							
							function show_comment(){
								
								var post_no = <?php echo $post_no;?>;
								var cur_user = <?php echo ("'".$cur_user."'");?>;
								
								jQuery.ajax({
									type: "GET",
									url: "dynamic_webtoon_comment.php",
									contentType :'charset=UTF-8',
									data: {'no' : post_no, 'type' : 'illust'},
									success:function(result){
										
										if(is_cmt_show == false){							// 댓글 보기 (댓글이 가려져 있을 경우)
											is_cmt_show = true;
											$('#cmt_button').attr("value","댓글 접기");

											if(is_load_cmt_once == false){					// 댓글을 최초로 불러올 경우 
												is_load_cmt_once = true						// DB에서 데이터를 최초로 가져옴
												
												cmts = JSON.parse(result);
												// 리턴 결과는 String 타입, JSON 객체가 아님.
												// 따라서 string 타입을 json 객체로 변경해주는 작업이 필요
												var numberOfElements = cmts.length;
												
												for(var i = 0; i < numberOfElements; i++){
													
													var cmt_frame = '<div class="cmt_element" value=' + cmts[i].comment_no + '>' + 
																		'<div>' + 
																		cmts[i].nick_name + "<p>" +
																		cmts[i].comment + "</p>" +
																		cmts[i].time;
																		
													if(cmts[i].nick_name == cur_user)
														cmt_frame += " <input type='button' value='수정' class = 'get-cmt-no-modify btn btn-outline-dark mt-auto' id='" + cmts[i].comment_no + "'/> " +
													    "<input type='button'  value='삭제' class = 'get-cmt-no-delete btn btn-outline-dark mt-auto' id='" + cmts[i].comment_no + "'/> ";
														
													cmt_frame += "</div></div>";
																		
													$('#total_element').append(cmt_frame);
												}		// for
											}		// if is_load_cmt_once											
											$('#total_element').show();						// 댓글창을 보여준다.
											$('#register_cmt').css({"display":"block"});
										
										}		// if is_cmt_show		
										
										else if(is_cmt_show == true){						// 댓글 가리기 (댓글이 보여진 상태일 경우)
											is_cmt_show = false;
											$('#cmt_button').attr("value","댓글 보기");
											$('#total_element').hide();						/// 댓글창을 가린다.
											$('#register_cmt').css({"display":"none"});
										}		// else if is_cmt_show							
									
									},error: function(xhr, status, error){
			
									}	// error func
								});		// ajax
								
							}
							
							
						//////////////////////////////////////////
						//
						//	register_comment() 덧글 등록함수
						//
						//////////////////////////////////////////
							
							
							
						function register_comment(){
							
							var post_no = <?php echo $post_no;?>;				
							var cur_user = <?php echo ("'".$cur_user."'");?>;
							var id_no = <?php echo $id_no;?>;
							var comment = $('#comment_container').val();
							var now = new Date(+new Date() + 3240 * 10000).toISOString().replace("T", " ").replace(/\..*/, '');
							
							console.log("post_no : " + post_no
									+ " cur_user : " + cur_user
									+ " id_no : " + id_no
									+ " comment : " + comment
									+ " now : " + now);
							
							if(cur_user == -1){
								alert("로그인이 필요한 서비스입니다.");
								return;
							}
							
							if(comment == ""){
								alert("내용을 입력해 주세요");
								return;
							}
							

							jQuery.ajax({
									type: "GET",
									url: "dynamic_webtoon_comment.php",
									contentType :'charset=UTF-8',
									data: {'no' : post_no, 'cur_user' : cur_user, "id_no" : id_no,"comment" : comment , 'type' : 'illust_comment'},
									success:function(result){
										
											// 댓글을 올리면 가장 최대값의 comment_no(db의 primary key)가 가장 최신이 되므로 최대값을 result로 받아온다.
											
											$('#comment_container').val('');
											alert("등록 완료");
											var cmt_frame = '<div class="cmt_element" id="swap' + swap_count + '" value=' + result + '><div>' + 
																		cur_user + 
																		'<p>' + comment + '</p>' +
																		now +  
																		" <input type='button' value='수정' class = 'get-cmt-no-modify btn btn-outline-dark mt-auto' id='" + result + "'/> " +
																		"<input type='button' value='삭제' class = 'get-cmt-no-delete btn btn-outline-dark mt-auto' id='" + result + "'/> " + "</div></div>";
											$('#total_element').append(cmt_frame);
											$('#swap' + swap_count).insertAfter('#register_cmt');
											swap_count++;
											// 가장 최근에 올린 댓글을 맨 위로 올려놓는다.
									},
									error: function(xhr, status, error){
										alert(error);
										
										
									}	// error func
								});		// ajax
						}





						//////////////////////////////////////////////////
						//
						// 덧글 수정버튼 이벤트
						//
						// 덧글 수정창을 보여주는 버튼 구현
						//
						// 덧글을 표현하는 요소는 한 div에 두가지 div가 있는데,
						// 기본적으로 보여지는 덧글창 div,
						// 수정을 위해 존재하는 div가 있음
						// 수정 과정에서 서로 토글되어 보여짐.
						// 
						//
						//////////////////////////////////////////////////

						$(document).on("click",".get-cmt-no-modify", function(){
								var comment_no = $(this).attr("id");
								var parent_div = $(this).parent("div").parent("div");
								var comment = $(this).prev().text();		// this는 현재 덧글 기본창의 수정버튼이며, 수정버튼 바로 이전 요소에 코멘트를 감싸는 p 태그가 있으므로 prev로 가져온다.
								
								if(parent_div.children().length < 2)		// 자식수(div tag)가 2개 미만일 경우 덧글 수정창이 없다는 의미이므로 덧글 수정창을 추가한다.
									parent_div.append(
											'<div><textarea rows="3" cols="120" placeholder="댓글 입력" style="resize:none;">' + comment + '</textarea>' + " " +  
											'<input type = "button" class="comment_modify_btn btn btn-outline-dark mt-auto" value="수정" id ="' + comment_no + '"/>'+ " " +
											'<input type = "button" class="comment_cancel_btn btn btn-outline-dark mt-auto" value="취소"/></div>');
							
								else 										// 이미 덧글창에 덧글 수정창이 추가되었을 경우 단순히 보여주기만 한다.
									$(this).closest("div").next().css({"display" : "block"});
							
								$(this).closest("div").css({"display" : "none"});
						});
						
						
						
						// 덧글 업데이트 토글 버튼 구현 -- 수정 취소기능
						$(document).on("click", ".comment_cancel_btn", function(){
							parent_div = $(this).closest("div");
							parent_div.css({"display" : "none"});
							// 현재 덧글 수정창을 가리고
							
							parent_sibling = parent_div.prev();
							parent_sibling.css({"display" : "block"});
							// 다시 기본 덧글창을 보여준다.
							
						});
						// 동적으로 생성된 엘리먼트는 다음과 같이 속성을 접근해야 가능하다.


						//////////////////////////////////////////////////
						//
						// 덧글 수정 최종 결정 버튼 (기본 덧글창에서 수정버튼 클릭시 나오는 수정 버튼)
						//
						// 덧글 내용을 수정한 뒤 최종적으로 수정을 결정하는 버튼 이벤트 
						// 
						//////////////////////////////////////////////////
						
						
						$(document).on("click",".comment_modify_btn", function(){
							
							var comment_no = $(this).attr("id");
							var comment_text = $(this).prev();			// 코멘트 작성공간(textarea)은 수정버튼 이전에 있으므로 prev함수로 가져온다.
							var comment = comment_text.val();
							
							if(comment == ""){							// 코멘트 내용이 없을 경우
								alert("내용을 입력해 주세요");
								return;
							}
							
							else {										// 코멘트 내용이 있을 경우
								jQuery.ajax({
										type: "GET",
										url: "dynamic_webtoon_comment.php",
										contentType :'charset=UTF-8',
										data: {"comment_no" : comment_no, "comment" : comment, "operate" : "update"},
										success:function(result){
											alert("수정되었습니다.");
										},
										error: function(xhr, status, error){}	// error func
									});		// ajax
									
								var parent_div = $(this).closest("div");
								parent_div.css({"display" : "none"});
								// 현재 덧글 수정창을 가리고
								
								var parent_sibling = parent_div.prev();
								parent_sibling.css({"display" : "block"});
								parent_sibling.children().text(comment);
								// 다시 기본 덧글창에 수정된 내용을 담아 보여준다.
								// 덧글 기본창과 수정창의 toggle
							}
						});
						
						//////////////////////////////////////////////////
						//
						// 덧글 삭제버튼 이벤트
						//
						//////////////////////////////////////////////////

						$(document).on("click",".get-cmt-no-delete",function(){
								var comment_no = $(this).attr("id");
								
								if(confirm("댓글을 삭제하시겠습니까?") == true){
									$(this).parent("div").parent("div").remove();
									// 댓글 전체를 래핑하고있는 부모를 찾아 지운다. (브라우저에 표기된 댓글을 지운다.)
									
									jQuery.ajax({
										type: "GET",
										url: "dynamic_webtoon_comment.php",
										contentType :'charset=UTF-8',
										data: {'comment_no' : comment_no, 'operate' : 'delete'},
										success:function(result){
											
											if(result == true)
													alert("삭제되었습니다.");
											
											
										},
										error: function(xhr, status, error){}	// error func
									});		// ajax
								}		// 서버 DB에 지우기를 요청한다.
								
								else return;		
								
						});


						//////////////////////////////////////////////////
						//
						// 일러스트 게시물 수정, 삭제 버튼
						//
						// 
						//
						//////////////////////////////////////////////////


						function modify_dynamic_webtoon(){

							if(confirm("웹툰을 수정하시겠습니까?") == true)
								$('#modify_form').submit();
							
						}
						
						
                        function delete_webtoon(){
							if(confirm("웹툰을 삭제하시겠습니까?") == true){
								var post_no = <?php echo $post_no;?>;
								
								var jObj = new Object();
								jObj.post_no = post_no;
								jObj.operate = 'delete';
								jObj.type = "dynamic_webtoon";
								
								console.log(jObj);
								
								var json_data = JSON.stringify(jObj);
								
								jQuery.ajax({
										type: "POST",
										url: "../post_operate.php",
										contentType :'application/json; charset=UTF-8',
										data: json_data,
										success:function(result){
											
											if(result == true){
												alert("삭제되었습니다.");
												window.history.back();
											}
											
										},
										error: function(xhr, status, error){
											alert(error);
										}	// error func
									
									
									});		// ajax
							}
						}



						// 컷 목록 가져오기


						</script>
					
						<?php

							if(isset($_SESSION["isActivate"]))
								echo("
									<li class=\"nav-item\"><a class=\"nav-link me-lg-3\" href = \"javascript:void(0)\">".$_SESSION["nickname"]." 님</a></li>
									<li class=\"nav-item\"><a class=\"nav-link me-lg-3\" href = \"javascript:void(0)\" onclick=\"logout()\">로그아웃</a></li>");
							
							else
								echo("
									<li class=\"nav-item\"><a class=\"nav-link me-lg-3\" href=\"../Login.html\">로그인</a></li>		
									<li class=\"nav-item\"><a class=\"nav-link me-lg-3\" href=\"../Register.html\">회원가입</a></li>");
							?>
							<div class="dropdown">
								<button class="btn dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
									작품 열람
								</button>
								<ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
									<li><a class="dropdown-item" href="../illust_list.php">일러스트 게시판</a></li>
									<li><a class="dropdown-item" href="../dynamic_webtoon_list.php">다이나믹 웹툰 게시판</a></li>
								</ul>
							</div>
						</ul>
                </div>
            </div>
        </nav>

	
        <section class="py-5">	   
				<div class="container px-4 px-lg-5 my-5">
				
					<div>
						<input type="button" value= "처음으로" onclick = "first_cut()" class = "btn btn-outline-dark mt-auto"/>
						<input type="button" value= "뒤로가기" onclick = "prev_cut()" class = "btn btn-outline-dark mt-auto"/>
						<input type="button" value= "다음 컷" onclick= "next_cut()" class = "btn btn-outline-dark mt-auto"/>
						<input type="button" value= "마지막 컷" onclick = "last_cut()" class = "btn btn-outline-dark mt-auto"/>
					</div>
					
					<div class="row gx-4 gx-lg-5 div-height" >
	
							<!---------------------다이내믹 웹툰 플레이어------------------------->
							<!---------------------다이내믹 웹툰 플레이어------------------------->
							<!---------------------다이내믹 웹툰 플레이어------------------------->			
							<div class="col-md-6 center">
								<!--
								<img class="card-img-top mb-5 mb-md-0 center_p" 
								id="planeImg" src="<?php echo $contents_dir;?>" alt="..." />
								-->
								<a href="javascript: next_cut();">
									<video class = "card-img-top mb-5 mb-md-0 center_p" id="dynamicImg" alt="..." muted="muted">
										<source src="" type="video/mp4" id="dynamicImgSrc">
									</video>
								</a>
								
								<a href="javascript: next_cut()">
									<img class="card-img-top mb-5 mb-md-0 center_p" id="planeImg"
										src="" style="display:none;" alt="..." />
								</a>

								
								<!--Firefox에선 지원하지 않음.-->
							</div>

							
							<?php
									///////////////////////////
									//
									// 작품을 db로부터 불러온다
									//
									///////////////////////////
									
									$conn = connect_db();
									$query = 'SELECT d.post_title,
													d.brief_comment,
													d.contents_dir, 
													u.nick_name
													FROM dynamic_webtoon_post d, _user u 
													WHERE d.post_no='.$post_no.' and u.id_no=d.user_no';	
										
									if($request_result = mysqli_query($conn,$query)){
										if($request_result->num_rows > 0){
											$row = $request_result->fetch_assoc();
												$title = $row['post_title'];
												$brief_cmt = $row['brief_comment'];
												$contents_dir = $row['contents_dir'];
												$nick_name = $row['nick_name'];
												mysqli_free_result($request_result);
										}										
									}
									
									$list = '';
									if($cut_list = opendir($contents_dir)){				// 컷 파일의 목록을 읽어온다
										while(($file = readdir($cut_list)) != false){
											
											if($file == '.' || $file == '..' || strpos($file, "thumbnail") !== false)
												continue;
											
											$list = $list.$file.'|';
										
										}
										
										//echo '<script>console.log("'.$list.'")</script>';
									}
									closedir($cut_list);
									
															
									release_db($conn);	
							?>

							<script>
							
								var current_cut_no = 0;				// 컷 번호를 가리키는 인덱스
								var planeImg = $("#planeImg");
								
								// 다이나믹 웹툰 플레이어 스크립트
								
								<?php
									echo 'var cut_list_str ='.'"'.$list.'";';
									echo 'var post_dir ='.'"'.$contents_dir.'";';
									// 파일목록을 저장한 php변수 $list를 가져온다.
								?>
								
								var cut_list = cut_list_str.split('|');
								cut_list.pop();
								//console.log("cut_list : " + cut_list[0]);
								/*
								for(var i = 0; i < cut_list.length; i++){
									var int_a = cut_list[i].substring(0, cut_list[i].indexOf('.'));
									console.log(int_a);
								}*/

								cut_list.sort(function(a, b)  {
									var int_a = parseInt(a.substring(0, a.indexOf('.')));
									var int_b = parseInt(b.substring(0, b.indexOf('.')));
									
									if(int_a > int_b) return 1;
									if(int_a === int_b) return 0;
									if(int_a < int_b) return -1;
								});
								// 문자열 배열을 숫자로 변환하여 크키순으로 정렬한다.
								
								//console.log(cut_list);
								

								// cut_list의 마지막 요소는 공백이므로 미리 제거해놓는다.
								
								function get_cut(currernt_cut){					// cut_no 확장자가 mp4이면 true 그렇지 않으면 false
								
									if(currernt_cut.search("mp4") == -1){		// planeImg일 경우
										$("dynamicImg").css({"display":"none"});
										planeImg.css({"display":"block"});
										planeImg.attr("src", post_dir + currernt_cut);
										$('#dynamicImgSrc').attr("src", "");
										$("#dynamicImg")[0].load();				// 위 함수를 호출해야 영상 소스를 바꿔서 재생 가능

									}
									else{										// DynamicImg일 경우
										$("dynamicImg").css({"display":"block"});
										planeImg.css({"display":"none"});
										$('#dynamicImgSrc').attr("src", post_dir + currernt_cut);	
										$("#dynamicImg")[0].load();				// 위 함수를 호출해야 영상 소스를 바꿔서 재생 가능
										document.getElementById("dynamicImg").play();
									}
									
								}			
								
								function next_cut(){
									if(current_cut_no < cut_list.length - 1)
										current_cut_no++;
									
									else	
										alert("마지막 컷입니다.");
									
									get_cut(cut_list[current_cut_no]);
								}
								
								function prev_cut(){
									if(current_cut_no > 0)
										current_cut_no--;
									
									else	
										alert("첫번째 컷입니다.");
									
									get_cut(cut_list[current_cut_no]);
								}
								
								function first_cut(){
									current_cut_no = 0
									get_cut(cut_list[current_cut_no]);
								}
								
								function last_cut(){
									current_cut_no = cut_list.length - 1;
									get_cut(cut_list[current_cut_no]);
								}
								
								
								// 가장 먼저 처음컷을 보여준다.
								get_cut(cut_list[current_cut_no]);


							</script>
	
	

							<!---------------------다이내믹 웹툰 플레이어------------------------->
							<!---------------------다이내믹 웹툰 플레이어------------------------->
							<!---------------------다이내믹 웹툰 플레이어------------------------->			



							
							<div class="col-md-6 descript-area">
								<div class="text-border">
										<b><p style="font-size:30px;"><?php echo $title?></p></b>
								</div>
								
								<div style="padding-bottom:1rem; margin-bottom:20rem" >
										<b><p class="text-border" style="font-size:15px;"><?php echo $nick_name." ";?>작가의 코멘트</p></b>
										
										<p style="font-size:15px;">
											<?php 
											if($brief_cmt == "")
												echo "코멘트 없음";
											else 
												echo $brief_cmt;?></p>
										</div>
								<div style="border-width: 1px 0px 0px 0px; border-style: solid; text-align:right; padding-top:1rem;">
									<form id='modify_form' action='../dynamic_webtoon_editor/webtoon_editor.php' method='POST'>
										<?php 
										
											if($cur_user == $nick_name){
												// 현재 세션 변수의 nick_name과 일러스트 업로더의 nick_name이 서로 같으면 수정/삭제 기능을 보여줌
												
												echo '<input type = "hidden" name="operate" value="modify"/>
													<input type = "hidden" name="post_no" value="'.$post_no.'"/>'.
													'<input type = "button" class="btn btn-outline-dark mt-auto" value="게시글 수정" onclick="modify_dynamic_webtoon()"/>
													<input type = "button" class="btn btn-outline-dark mt-auto" value="게시글 삭제" onclick="delete_webtoon()"/>';
											}
										
										?>
									</form>
									
							
								</div>			
							</div>
								
							<div class="comment-area" id="cmt_area">
								<div class="text-end div-upload-button">
									<input type="button" value="댓글 보기" class ="comment_button btn btn-outline-dark mt-auto" id="cmt_button"onclick="show_comment()"/>
								</div>
								
								<div style="border-width: 0px 0px 2px 0px; border-style: solid; margin-bottom:30px;"></div>					
								
								<!--댓글 목록 표기-->
								<div id="total_element">
								
									<!-- 댓글 입력란 -->
									<div id="register_cmt" style="display:none; text-align:left;">	
									
										<textarea rows="3" cols="120" placeholder="댓글 입력" style="resize:none;" id="comment_container"></textarea>
										<input type = "button" class="comment_register_btn btn btn-outline-dark mt-auto" value="댓글 등록" onclick="register_comment()"/>
										<div style="border-width: 0px 0px 2px 0px; border-style: solid; margin-bottom:10px;"></div>		
									
									</div>
									
								</div>	
							</div>
							
							
					</div>
        </section>
		

</body>

</html>