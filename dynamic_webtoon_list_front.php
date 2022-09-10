<div class="row gx-4 gx-lg-5 row-cols-2 row-cols-md-5" id = "webtoon_list">
<!-- 여기까지가 1개-->		
	<?php			// 게시판 블록, 페이징 구현 부분
		$conn = connect_db();
		$GLOBALS['webtoon_post_count'] = w_getPostCount($conn);
		set_block_var();
		show_webtoon_list($GLOBALS['cur_page'], $conn);
		release_db($conn);
		
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