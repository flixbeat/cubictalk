<!-- Dave Von David 2016-07-26 -->
<?php 

	$url = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

	$controller = str_replace(c_path(),"",$url);
	$r = explode('/', $controller);
	$controller = $r[1];
	$action = $r[2]!=null ? $r[2]:'index'; 

	$myPage = null;
	$reservation = null;
	$groupLessons = null;
	$lessonHistory = null;

	$uri = $controller .'/'. $action;
	if($uri == 'lessons/mypage')
		$myPage = 'class="active" id = "active"';
	else if($uri == 'teachers/index')
		$reservation = 'class="active" id = "active"';
	else if($uri == 'group_lessons/index')
		$groupLessons = 'class="active" id = "active"';
	else if($uri == 'lessons/myhistory')
		$lessonHistory = 'class="active" id = "active"';

?>

<div class = "div-container" id = "tabs">
	<ul class = "nav nav-tabs">
		<li <?=$myPage?> ><a href="#" onclick = "goMyPage()"><h5><strong>수업일정</strong></h5></a></li>
		<li <?=$reservation?> ><a href="#" onclick = "goMyReservations()"><h5><strong>수업예약</strong></h5></a></li>
		<li <?=$groupLessons?> ><a href="#" onclick = "goGroupLessons()"><h5><strong>그룹수업</strong></h5></a></li>
		<li <?=$lessonHistory?> ><a href="#" onclick = "goLessonHistory()"><h5><strong>지난수업</strong></h5></a></li>
	</ul>
</div>

<style>
	#tabs{
		margin: auto;
		width: 88.075%;
	}
	li#active.active > a{
		background-color: #F0A75D;
	}
</style>

<script type="text/javascript">

	function goMyPage(){
		location.href = "<?=c_path()?>/lessons/mypage";
	}

	function goMyReservations(){
		location.href = "<?=c_path()?>/teachers/";
	}

	function goGroupLessons(){
		location.href = "<?=c_path()?>/group_lessons/";
	}

	function goLessonHistory(){
		location.href = "<?=c_path()?>/lessons/myhistory/";
	}

</script>
<!-- code end ~Dave -->