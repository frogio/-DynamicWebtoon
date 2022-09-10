<!DOCTYPE html>

<html>
	<head>	
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	</head>
	
	<body>
	
	<style type="text/css">
	
	.center {text-align: center; height: 100vh; line-height: 100vh;} 
	
	.parent {
		display: table-cell;
		vertical-align: middle;
	}
	</style>

	<div class="center">
			<video id="dynamicImg" alt="..." muted="muted">
				<source src="" type="video/mp4" id="dynamicImgSrc">
			</video>
	</div>	
		<script>
			var preview_dir;
			
			if(localStorage.getItem("task_folder"))
				preview_dir = localStorage.getItem("task_folder") + "preview.mp4";
			
			$("#dynamicImgSrc").attr("src",preview_dir);
			$("#dynamicImg")[0].load();				// 위 함수를 호출해야 영상 소스를 바꿔서 재생 가능
			document.getElementById("dynamicImg").play();
			
		</script>

	
	</body>
</html>
