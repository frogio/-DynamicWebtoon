<?php

include_once "db_manager.php";
include_once "session.php";
include_once "/dynamic_webtoon_editor/editor_tmp_const.php";
include_once "webtoon_board.php";
include_once "illust_board.php";

session_start();

$GLOBALS['cur_page'] = 1;

if(isset($_GET["cur_page"]))						// GET 방식으로 page 번호가 주어지면
	$GLOBALS['cur_page'] = $_GET["cur_page"];		// 페이지 번호를 받는다.

else												// GET 메시지가 없을 경우 기본 페이지는 1페이지.
	$GLOBALS['cur_page'] = 1;
													// $cur_page는 현재 페이지를 나타내는 변수
$GLOBALS['webtoon_post_count'] = 0;
		
$GLOBALS['cur_block'] = 0;							// 현재 띄워져 있는 페이지가 속해있는 블럭 번호
$GLOBALS['block_start_no'] = 0;						// 현재 보여지는 블록의 시작 번호, 1, 6, 11 ...
$GLOBALS['block_end_no'] = 0;						// 현재 보여지는 블록의 마지막 번호, 5, 10, 15 ...
$GLOBALS['total_page_count'] = 0;					// 전체 페이지 개수
$GLOBALS['total_block_cnt'] = 0;					// 전체 블럭 개수
			

?>

<!DOCTYPE html>


<html lang="en">
    <head>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <!-- Bootstrap core JS-->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js"></script>
        <!-- Core theme JS-->
		<script src="js/scripts.js"></script>
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
        <link href="css/styles.css" rel="stylesheet" />
    </head>
    <body>
        <!-- Navigation-->
		
		<input type="hidden" id="logout" value="<?php echo REQUEST_LOGOUT; ?>"/>
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
							url: "session.php",
							contentType :'charset=UTF-8',
							data: "LogOut",
							success:function(result){
							
							if(result == $('#logout').val())
								location.reload();	// main homepage로 돌아감
							},error: function(xhr, status, error){
								alert(error);
							}
							});
						
						}
						
						function chk_login(){
							var result = confirm("로그인이 필요한 서비스입니다. 로그인 하시겠습니까?");
							
							if(result)
								location.href = "/Login.html";
						
						}

						function open_editor(){													// 에디터 오픈 함수

							jQuery.ajax({
								type: "POST",
								url: "/dynamic_webtoon_editor/editor_svr_side.php",
								contentType :'application/json; charset=UTF-8',
								data: "make_user_tmp_dir",
								success:function(result){
											//alert(result);		
											localStorage.setItem("task_folder", result);		// 임시 이미지파일과 json 객체를 저장할 작업 디렉토리, editor_tmp 디렉토리에 저장된다.
											location.href = "./dynamic_webtoon_editor/webtoon_editor.php";
								},error: function(xhr, status, error){
									alert(error);
									
								}
							});
							
						}
						
						
						</script>
					
						<?php
							if(isset($_SESSION["isActivate"]))
								echo("
									<li class=\"nav-item\"><a class=\"nav-link me-lg-3\" href = \"javascript:void(0)\">".$_SESSION["nickname"]." 님</a></li>
									<li class=\"nav-item\"><a class=\"nav-link me-lg-3\" href = \"javascript:void(0)\" onclick=\"logout()\">로그아웃</a></li>");
							
							else
								echo("
									<li class=\"nav-item\"><a class=\"nav-link me-lg-3\" href=\"Login.html\">로그인</a></li>		
									<li class=\"nav-item\"><a class=\"nav-link me-lg-3\" href=\"Register.html\">회원가입</a></li>");
									
						?>
						<div class="dropdown">
							<button class="btn dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
								작품 열람
							</button>
							<ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
								<li><a class="dropdown-item" href="illust_list.php">일러스트 게시판</a></li>
								<li><a class="dropdown-item" href="dynamic_webtoon_list.php">다이나믹 웹툰 게시판</a></li>
							</ul>
						</div>

					</ul>
                </div>
            </div>
        </nav>

        <!-- Header-->
        <header class="bg-dark py-5">
            <div class="container px-4 px-lg-5 my-5">
                <div class="text-center text-white">
                    <h1 class="display-4 fw-bolder">다이나믹 웹툰 게시판</h1>
                    <!--<p class="lead fw-normal text-white-50 mb-0">With this shop hompeage template</p>-->
                </div>
            </div>
        </header>
        <!-- Section-->
        <section class="py-5">
		<?php
		
			function set_block_var(){
				
				$GLOBALS['cur_block'] = ceil($GLOBALS['cur_page'] / BLOCK_COUNT);
				$GLOBALS['block_start_no'] = (($GLOBALS['cur_block'] - 1) * BLOCK_COUNT) + 1;
				$GLOBALS['block_end_no'] = $GLOBALS['block_start_no'] + BLOCK_COUNT - 1;
				$GLOBALS['total_page_count'] = ceil($GLOBALS['webtoon_post_count'] / ITEM_COUNT_BY_PAGE);
				
				if($GLOBALS['block_end_no'] > $GLOBALS['total_page_count'])
					$GLOBALS['block_end_no'] = $GLOBALS['total_page_count'];
				
			}
			function show_page_block(){
			
				if($GLOBALS['cur_page'] > 1){
					echo '<a class="btn btn-outline-dark mt-auto" href="dynamic_webtoon_list.php?cur_page=1">처음 </a>';
					$pre = $GLOBALS['cur_page'] - 1;
					echo '<a class="btn btn-outline-dark mt-auto" href = "dynamic_webtoon_list.php?cur_page='.$pre.'">이전 <a>';
				}
				
				for($i = $GLOBALS['block_start_no']; $i <= $GLOBALS['block_end_no']; $i++){
					if($GLOBALS['cur_page'] == $i)
						echo '<b>'.$i." ".'</b>';
					else 
						echo '<a class="btn btn-outline-dark mt-auto" href="dynamic_webtoon_list.php?cur_page='.$i.'">'.$i." ".'</a>';
				}
				
				if($GLOBALS['cur_page'] < $GLOBALS['total_page_count']){
					$next = $GLOBALS['cur_page'] + 1;
					echo '<a class="btn btn-outline-dark mt-auto" href="dynamic_webtoon_list.php?cur_page='.$next.'">다음 </a>';
					echo '<a class="btn btn-outline-dark mt-auto" href="dynamic_webtoon_list.php?cur_page='.$GLOBALS['total_page_count'].'">마지막 <a>';
				}
			}
			
		
		?>
            <div class="container px-4 px-lg-5 mt-5" >
				
				<script>											// 검색 스크립트
					var type = "nick_name";
					
					$("select[name=searchType]").change(function(){
							type = $(this).val(); // 셀렉트 박스 value값 가져오기
					});
					
					function search(){
					
						var JSONdata = {
								"searchType" : type,
								"keyword": $('#keyword').val()
						};
						
						JSONdata = JSON.stringify(JSONdata);
						
						$('#webtoon_list').children().remove();
				
						jQuery.ajax({
								type: "POST",
								url: "dynamic_webtoon_list.php",
								contentType :'application/json; charset=UTF-8',
								data: JSONdata,
								success:function(result){
									//alert("isWorking?");
									console.log(result);
									
								},error: function(xhr, status, error){
									alert(error);
									
								}
							});
					
					}
				</script>
				
			<div class="row gx-4 gx-lg-5 row-cols-2 row-cols-md-5" id = "webtoon_list">
			<!-- 여기까지가 1개-->		
				<?php			//검색이 실행될경우
					
				////if($_SERVER['REQUEST_METHOD'] == 'POST'){										// POST Message가 올때만 작동
				//
				//	header('Content-Type: text/html; charset=utf-8');
				//	$searchMeta = file_get_contents('php://input');
				//	
				//	if(gettype($searchMeta) == 'string'){
				//		$search_meta = json_decode(stripcslashes($searchMeta), true);			// JSON 데이터인지 확인 JSON 객체가 아니면 NULL 반환
				//		
				//		if($search_meta['searchType'] == "nick_name"){
				//
				//		}
				//		else if($search_meta['searchType'] == "title"){
				//			
				//			
				//		}
				//		$result = "search_type : ".$search_meta['searchType']." "."keyword : ".$search_meta['keyword'];
				//		
				//		echo $result;
				//		
				//	}
				//}
				//
				//else{									//게시판 블록, 페이징 구현 부분
					
					$conn = connect_db();
					$GLOBALS['webtoon_post_count'] = w_getPostCount($conn);
					set_block_var();
					show_webtoon_list($GLOBALS['cur_page'], $conn);
					release_db($conn);
					
				//}
				
			?>	
			</div>
			
			<div style="text-align:center;margin-bottom:1rem;">
					<?php		
						show_page_block();
					?>
					
			</div>
			
			<div style="text-align:center">
			
				<select name="searchType">
					<option value="nick_name">작가명</option>
					<option value="title">작품명</option>
				</select>
			
				<input placeholder="검색어를 입력하세요" id="keyword"/>
				<input type="button" value ="검색" onclick="search()">
				
			</div>			
				<div>
				
				<?php
					
					if(isset($_SESSION["isActivate"]))
						echo("<div class=\"text-end div-upload-button\"><a class=\"btn btn-outline-dark mt-auto\" href=\"javascript:void(0)\" onclick=\"open_editor()\" >작품 올리기</a></div>");	
					else
						echo("<div class=\"text-end div-upload-button\"><a class=\"btn btn-outline-dark mt-auto\" href = \"javascript:void(0)\" onclick=\"chk_login()\">작품 올리기</a></div>");
					
				?>
				</div>				
			</div>
    </section>
        <!-- Footer-->
		
    <footer class="py-5 bg-dark">
        <div class="container"><p class="m-0 text-center text-white">Copyright &copy; Open Webtoon 2022</p></div>
    </footer>

    </body>
</html>
