<?php include('views/common/top.php');?>

<?=$form->create(null,array('action'=>'confirmation','id'=>'formConfirm'));?>
<?=$form->input('groupLessonId',array('type'=>'hidden'))?>
<?=$form->end()?>

<div id = "wrapper"  class = "div-container">
	<?php $session->flash(); ?>
	<h2>Group Class</h2>
	<!-- Dave Von David 2016-07-26 -->
	
	<ul class = "nav nav-tabs">
		<li><a href="#" onclick = "goMyPage()"><h5><strong>수업일정</strong></h5></a></li>
		<li><a href="#" onclick = "goMyReservations()"><h5><strong>수업예약</strong></h5></a></li>
		<li class="active" id = "active"><a href="#" onclick = "goGroupLessons()"><h5><strong>그룹수업</strong></h5></a></li>
		<li><a href="#" onclick = "goLessonHistory()"><h5><strong>지난수업</strong></h5></a></li>
	</ul>
	<br>

	<ul class = "nav nav-tabs">
		<li><a href="#" onclick = "goToMyGroupReservations()"><strong>My Reservation</strong></a></li>
		<li class="active" id = "active"><a href="#"><strong>New Reservation</strong></a></li>
		<li><a href="#" onclick = "goToClassHistory()"><strong>Class History</strong></a></li>
		<span class = "pull-right text-success"><h4><strong>New Reservation</h4></strong></span>
	</ul>

	<table class="table table-hover">
		<tr>
			<th>#</th>
			<th><?=$language[$session->read('lang')]['table_head_select']?></th>
			<th><?=$language[$session->read('lang')]['table_head_teacher']?></th>
			<th><?=$language[$session->read('lang')]['table_head_lesson_time']?></th>
			<th><?=$language[$session->read('lang')]['table_head_kind_of_lesson']?></th>
			<th><?=$language[$session->read('lang')]['table_head_how_long']?></th>
			<th><?=$language[$session->read('lang')]['table_head_topic']?></th>
			<th><?=$language[$session->read('lang')]['table_head_max_students']?></th>
			<th><?=$language[$session->read('lang')]['table_head_level']?></th>
			<th><?=$language[$session->read('lang')]['table_head_spend_point']?></th>
		</tr>
		
		<?php foreach ($groupLessons as $key=>$groupLesson):?>
			<tr>
				<td>
					<?php
						# numbering
						if($pageNumber>1){
							echo (($pageNumber*10) - 10) + ($key+1);
						}
						else if($pageNumber == 1){
							echo $key+1;
						}
					?>
				</td>
				<td>
					<?php
						$min = 0;

						if($groupLesson['gl']['how_long'] == 1)
							$min = 30;
						else if($groupLesson['gl']['how_long'] == 2)
							$min = 60;
						else if($groupLesson['gl']['how_long'] == 3)
							$min = 90;

						$lessonTime = strtotime($groupLesson['gl']['lesson_time']);
						$currentTime = strtotime(date('Y-m-d h:i:sa'));

						# if class is finished
						if(round( ($lessonTime - $currentTime)/60 , 2) <= -$min){
							echo 'Class Finished';
						}
						else{
							echo "
								<button value = \" ".$groupLesson['gl']['id']." \"
								onclick = \" confirm(". $groupLesson['gl']['id'] .") \"
								class = \"btn btn-success\"
								>".$language[$session->read('lang')]['btn_reserve']."</button>
							";
						}
					?>
				</td>
				<td>
					<img src = "<?=c_path().$groupLesson[0]['teacher_img']?>" class = "img img-thumbnail" width = "150" height = "150"
						onclick = "goToTeacherProfile(<?=$groupLesson['gl']['teacher_id']?>)" 
						id = "teacherImg"
					>
				</td>
				<td><?=$groupLesson['gl']['lesson_time'];?></td>	
				
				<td>
					<?php 
						if($groupLesson['gl']['kinds_of_lesson'] == 1)
							echo "Common";
						else if($groupLesson['gl']['kinds_of_lesson'] == 2)
							echo "Adult";
						else if($groupLesson['gl']['kinds_of_lesson'] == 3)
							echo "Child";
					?>
				</td>
				<td>
					<?php 
						if($groupLesson['gl']['how_long'] == 1)
							echo "25 minutes";
						else if($groupLesson['gl']['how_long'] == 2)
							echo "50 minutes";
						else if($groupLesson['gl']['how_long'] == 3)
							echo "75 minutes";
					?>
				</td>
				<td>	
					<button onclick = "popup_details(<?=$groupLesson['gl']['id']?>)" class = "btn btn-success btn-sm"><?=$language[$session->read('lang')]['btn_details']?></button>
					<br><?=$groupLesson['gl']['topic_title']?>
				</td>
				<td>
					<button onclick = "popup_show_students(<?=$groupLesson['gl']['id']?>)" class = "btn btn-success btn-sm"><?=$language[$session->read('lang')]['btn_show_students']?></button>
					<br><?=$groupLesson[0]['students_reserved']?> / <?=$groupLesson['gl']['max_student']?>
				</td>
				<td><?=$groupLesson['gl']['student_level']?></td>
				<td><?=$groupLesson['gl']['point']?></td>
			</tr>
		<?php endforeach; ?>
	</table>
	<?=$paging?>
</div>
<?php include ('views/common/bottom.php');?>

<script>
	function popup_details(groupLessonId){
		window.open("<?=c_path()?>/group_lessons/window_popup_details/"+groupLessonId, "", "width=350,height=200");
	}

	function confirm(groupLessonId){
		//location.href = "<?=c_path()?>/group_lessons/confirmation/"+groupLessonId;
		$('#GroupLessonGroupLessonId').val(groupLessonId);
		$('#formConfirm').submit();
	}

	function goToMyGroupReservations(){
		location.href = "<?=c_path()?>"+"/group_lessons/my_reservations";
	}

	function goToClassHistory(){
		location.href = "<?=c_path()?>"+"/group_lessons/class_history";
	}

	function goToTeacherProfile(teacherId){
		location.href = "<?=c_path()?>"+"/teachers/reserve/"+teacherId;
	}

	function popup_show_students(groupLessonId){
		window.open("<?=c_path()?>/group_lessons/window_popup_show_students/"+groupLessonId, "", "width=360,height=660");
	}

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

<style>
	#wrapper{
		width: 700px;
		min-height: 300px;
		margin: auto;
	}
	#btnMyReservations{
		margin-bottom: 0.5em;
	}
	#teacherImg{
		cursor: pointer;
	}
	li#active.active > a{
		background-color: #F0A75D;
	}
	#tabs{
		margin: auto;
		width: 88.075%;
	}
</style>