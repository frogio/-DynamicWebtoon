<!DOCTYPE html>

<?php

include_once "db_manager.php";
include_once "session.php";
include_once "post_operate.php";
include_once "thumbnail.php";
session_start();							// 세션변수를 사용하려면 무조건 호출되어야 한다.

define("REQUEST_POST_DATA", "SELECT * FROM illust_post WHERE post_no = ", FALSE);

if(isset($_POST["post_no"])){
	$post_no = $_POST["post_no"];
	$conn = connect_db();
	$query = REQUEST_POST_DATA.$post_no.';';

	if($request_result = mysqli_query($conn, $query)){
		if($request_result->num_rows > 0){
			$row = $request_result->fetch_assoc();
			
			$data["title"] = $row["post_title"];
			$data["comment"] = $row["brief_comment"];
			$data["contents_dir"] = $row["contents_dir"];
			$data["user_no"] = $row["user_no"];
		}
	}
	
	release_db($conn);
}

else return;
?>


<html>

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
								url: "session.php",
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
							


							function readImage(input) {
							// 인풋 태그에 파일이 있는 경우
								var fileForm = /(.*?)\.(jpg|jpeg|png|gif|bmp|pdf)$/;
								var imgFile = $("#input-image").val();							
								// 정규식
	
								if(input.files && input.files[0]) {
									
									if(!imgFile.match(fileForm)){
										alert("이미지 파일만 업로드 가능합니다.");
										location.reload();
									}
									// 이미지 파일인지 검사 (생략)
									// FileReader 인스턴스 생성
									const reader = new FileReader();
									// 이미지가 로드가 된 경우
									reader.onload = e => {
										const previewImage = document.getElementById("preview-image");
										previewImage.src = e.target.result;
									}
									// reader가 이미지 읽도록 하기
										reader.readAsDataURL(input.files[0]);
									}
							}
							$(document).ready(function(){			// 수정하기 전의 게시글을 보여준다.
								function show_prev_post(){
									var illust_title = <?php echo "'".$data["title"]."'";?>;
									var comment = <?php echo "'".$data["comment"]."'";?>;
									var contents_dir = <?php echo "'".$data["contents_dir"]."'";?>;
									var user_no = <?php echo "'".$data["user_no"]."'";?>;
									
									$('#title').val(illust_title);
									$('#brief_comment').val(comment);
									$('#preview-image').attr("src", contents_dir);
									
								}
								
								show_prev_post();
								
							});
							
							
							function cancel_modify(){
								
								if(confirm("수정을 취소하시겠습니까?") == true)
									window.history.back();
							
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
		
        <section class="py-5">
			<form enctype="multipart/form-data" method="post" action="post_operate.php">		   
				<div class="container px-4 px-lg-5 my-5">
					<div class="row gx-4 gx-lg-5 align-items-center">
	
							<div class="col-md-6">
								<img class="card-img-top mb-5 mb-md-0" id="preview-image" src="./img/default_img.jpg" alt="..."/>
							</div>
							
							<div class="col-md-6">
							
								<input type="hidden" name="modify_operate" value="modify"/>
								<input type="hidden" name="post_no" value = "<?php echo $post_no;?>" />
								<input type="hidden" name="type" value="illust"/>
								
								<div style= "padding-bottom:3rem;" >
									<input class="form-control me-3 display-5 fw-bolder" id="title" placeholder = "제목" name="title"/>
								</div>
								
								<div style= "padding-bottom:1rem;" >
									<textarea class="form-control me-3 " id="brief_comment" placeholder = "짧은 코멘트" cols = "40" rows = "10" name="brief_comment"></textarea>
								</div>
								
								<div class="d-flex" style= "padding-bottom:1rem;" >
									<input type="file" name="illust" id="input-image" accept=".jpg,.jpeg,.png,.gif,.bmp"/>
								</div>
								
								<div class="d-flex" style= "padding-bottom:1rem; float:right" >
								
									<input class="btn btn-outline-dark flex-shrink-0" style="margin-right:1rem;" onclick="cancel_modify()" type="button" value="취소"/>
									<input class="btn btn-outline-dark flex-shrink-0" type="submit" value="수정"/>

								</div>
								
							</div>
						
					</div>
				</div>
			</form>
		
        </section>
		
		<script>
			$(document).ready(function(){	
				const inputImage = document.getElementById("input-image");
				
				if(inputImage != null){
					inputImage.addEventListener("change", e =>{ 
						readImage(e.target) });
				}
			});
		</script>
		

</body>

</html>