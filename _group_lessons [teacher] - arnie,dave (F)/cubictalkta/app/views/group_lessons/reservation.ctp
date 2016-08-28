<?php echo $javascript->link('jquery-1.4.2.min');?>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.0/jquery-ui.js"></script>
<div id = "wrapper"  class = "div-container">
	
	<h2>Reservations</h2>
	
	<?php
		$loginInfo = $session->read('loginInfo');
		$managementLevel = $loginInfo['management_level'];
	?>
	<?php if($managementLevel < 2):?>
		<button class = "btn btn-primary" onclick = "location.href = '<?=c_path()?>/lessons/teacher_mypage'">Back to My Lessons</button>
	<?php endif;?>

	<?php if($managementLevel >= 2):?>
		<button class = "btn btn-primary" onclick = "location.href = '<?=c_path()?>/lessons/teacher_management'">Back to My Lessons</button>
	<?php endif; ?>

	<?php $session->flash(); ?>
	<hr>

	<table>
		<tr>
			<?=$form->create(null,array('action'=>'reservation','id'=>'formConfirm'));?>
			<td>
				<label>Teacher Name: </label>
				<input list = "tea_name" name="data[GroupLesson][tea_name]" >
				<datalist id = "tea_name">
					<?php foreach($teacherNames as $teacherName):?>
						<option value = "<?=$teacherName['teachers']['tea_name']?>">
					<?php endforeach;?>
				</datalist>
			</td>
			<td><?=$form->input('start_date',array('label'=>'Start Date: ','id'=>'start_date'))?></td>
			<td><?=$form->input('end_date',array('label'=>'End Date: ','id'=>'end_date'))?></td>
			<td><?=$form->end('Search')?></td>
		</tr>
	</table>
	
	<br>

	<ul class = "nav nav-tabs">
		<li><a href="#" onclick = "goToCGL()"><h5><strong>Create Group Lesson</strong></h5></a></li>
		<li class="active" id = "active" onclick="goToReservations()"><a href="#"><h5><strong>Reservations</strong></h5></a></li>
		<li><a href="#" onclick = "goToClassHistory()"><h5><strong>Class History</strong></h5></a></li>
	</ul>

	<table class="table table-hover">
		<tr>
			<th>#</th>
			<th>Status</th>
			<th>Teacher</th>
			<th>Lesson Time</th>
			<th>Kind of Lesson</th>
			<th>How Long</th>
			<th>Head Topic</th>
			<th>Max Student</th>
			<th>Level</th>
			<th>Spend Point</th>
			<th>Action</th>
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
							/*
							echo "
								<button value = \" ".$groupLesson['gl']['id']." \"
								onclick = \" confirm(". $groupLesson['gl']['id'] .") \"
								class = \"btn btn-success\"
								>Reserve</button>
							";
							*/
							echo "Available";
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
					<button onclick = "popup_details(<?=$groupLesson['gl']['id']?>)" class = "btn btn-success btn-sm">Details</button><br>
					<?=$groupLesson['gl']['topic_title']?>
				</td>
				<td>
					<button onclick = "popup_show_students(<?=$groupLesson['gl']['id']?>)" class = "btn btn-success btn-sm">Show Students</button><br>
					<?=$groupLesson[0]['students_reserved']?> / <?=$groupLesson['gl']['max_student']?>
				</td>
				<td><?=$groupLesson['gl']['student_level']?></td>
				<td><?=$groupLesson['gl']['point']?></td>
				<td>
					<?php
						$loginInfo = $session->read('loginInfo');
						$managementLevel = $loginInfo['management_level'];
					?>
					<?php if( !(round( ($lessonTime - $currentTime)/60 , 2) <= -$min) && $managementLevel >= 2):?>
						<button class = "btn btn-warning" onclick = "cancelClass(<?=$groupLesson['gl']['id'];?>)">Cancel</button>
					<?php endif;?>
				</td>
			</tr>
		<?php endforeach; ?>
	</table>
	<?=$paging?>
</div>

<script>
	function popup_details(groupLessonId){
		window.open("<?=c_path()?>/group_lessons/window_popup_details/"+groupLessonId, "", "width=350,height=200,menubar=yes,location=yes,resizable=yes,scrollbars=yes,status=yes");
	}

	function confirmation(groupLessonId){
		location.href = "<?=c_path()?>/group_lessons/confirmation/"+groupLessonId;
	}

	function goToCGL(){
		location.href = "<?=c_path()?>"+"/group_lessons/insert";
	}

	function goToReservations(){
		location.href = "<?=c_path()?>"+"/group_lessons/reservation";
	}

	function goToClassHistory(){
		location.href = "<?=c_path()?>"+"/group_lessons/class_history";
	}

	function goToTeacherProfile(teacherId){
		location.href = "<?=c_path()?>"+"/teachers/reserve/"+teacherId;
	}

	function popup_show_students(groupLessonId){
		window.open("<?=c_path()?>/group_lessons/window_popup_show_students/"+groupLessonId, "", "width=360,height=660,menubar=yes,location=yes,resizable=yes,scrollbars=yes,status=yes");
	}

	function popup_details(groupLessonId){
		window.open("<?=c_path()?>/group_lessons/window_popup_details_2/"+groupLessonId, "", "width=350,height=200,menubar=yes,location=yes,resizable=yes,scrollbars=yes,status=yes");
	}
	
	function popup_show_students(groupLessonId){
		window.open("<?=c_path()?>/group_lessons/window_popup_show_students/"+groupLessonId, "", "width=360,height=660,menubar=yes,location=yes,resizable=yes,scrollbars=yes,status=yes");
	}

	function cancelClass(groupLessonId){
		var r = confirm("Are you sure you want to cancel the class?");
		if (r == true) {
		    location.href = "<?=c_path()?>"+"/group_lessons/cancel/"+groupLessonId;
		}
	}

	$("#start_date").datepicker({dateFormat: "yy-mm-dd"});
	$("#end_date").datepicker({dateFormat: "yy-mm-dd"});
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
</style>