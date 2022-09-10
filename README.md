# DynamicWebtoon
웹툰에 애니메이션을 첨가할 수 있는 에디터를 제공하는 특수 웹툰 사이트

### 개요

Open Dynamic Webtoon은 기존 웹툰 서비스에 웹 기반으로 특수한 애니메이션을 첨가해주는 편집기를 만들고 
이를 공개하여 누구나 쉽게 편집기를 통해 애니메이션 효과가 첨가된 특수 웹툰을 만드는 것을 목표로 둔 프로젝트입니다.
</br>
</br>

![웹사이트 UML](https://user-images.githubusercontent.com/12217092/189481792-503e5f7c-a6fd-407b-ae99-6480b3abf16e.png)

</br>
</br>

### 사용 기술 및 환경
php, apache, MySQL, jQuery, fabric.js, OpenCV

</br>
</br>

### 데모 영상
-웹 기반 편집기를 통해 애니메이션 효과를 적용한 웹툰 영상

</br>
</br>

<img src="https://user-images.githubusercontent.com/12217092/189482411-cc51d18e-047e-447f-aa8a-010fce9b462c.gif" width="20%" height="20%"></img>

</br>
</br>

-편집기 시연 영상

<img src="https://user-images.githubusercontent.com/12217092/189485654-57375b05-51b5-4198-bfd5-aaff9cd38460.gif"></img>

</br>
</br>

### 파일 구성
Login.html, register.php, Register.html, session.php</br>
로그인 및 가입, 세션관련 php 프로그램</br>

uploadIllust.php, viewIllust.php, modify_illust.php, illust_list.php, illust_board, registerIllust</br>
일러스트 게시판 / 수정 / 삭제와 관련된 php 프로그램</br>

dynamic_webtoon_list.php, webtoon_board.php, dynamic_webtoon_player.php</br>
웹툰을 게시 / 재생과 관련된 php 프로그램</br>

dynamic_webtoon_post</br>
편집기를 이용해 만든 웹툰 파일들을 저장해놓은 폴더</br>

dynamic_webtoon_editor</br>
핵심이 되는 Webtoon Editor 프로그램이 존재하는곳</br>
	- webtoon_editor.php : 웹툰 편집기 프로그램</br>
	- chromakey_process.py : 애니메이션 웹툰을 만들기 위해 apache와 cgi로 연동이 되어 실행되는 처리 프로그램, webtoon_editor.php에서 처리를 위해 JSON을 넘김.</br>
	- chromakey_algorithm.py : 애니메이션 첨가를 위해 chromakey_process가 호출하는 핵심 알고리즘 프로그램. mask 알고리즘과 Vlahos 알고리즘이 작성되어 있음.