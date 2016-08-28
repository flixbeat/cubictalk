<?php $userSession = $session->read('loginInfo')?>
<?php $userId = $userSession['User']['id']?>

<?php include('views/common/top.php');?>
<div id = "wrapper"  class = "div-container">
	<?php $session->flash(); ?>
	<h2>Group Class</h2>

	<ul class = "nav nav-tabs">
		<li><a href="#" onclick = "goMyPage()"><h5><strong>수업일정</strong></h5></a></li>
		<li><a href="#" onclick = "goMyReservations()"><h5><strong>수업예약</strong></h5></a></li>
		<li class="active" id = "active"><a href="#" onclick = "goGroupLessons()"><h5><strong>그룹수업</strong></h5></a></li>
		<li><a href="#" onclick = "goLessonHistory()"><h5><strong>지난수업</strong></h5></a></li>
	</ul>
	<br>
	<ul class = "nav nav-tabs">
		<li class="active" id = "active"><a href="#" onclick = ""><strong>My Reservation</strong></a></li>
		<li><a href="#" onclick = "goToGroupClasses()"><strong>New Reservation</strong></a></li>
		<li><a href="#" onclick = "goToClassHistory()"><strong>Class History</strong></a></li>
		<span class = "pull-right text-success"><h4><strong>My Reservation</h4></strong></span>
	</ul>
	<table class = "table">
		<tr>
			<th>#</th>
			<th><?=$language[$session->read('lang')]['table_head_teacher']?></th>
			<th><?=$language[$session->read('lang')]['table_head_topic_title']?></th>
			<th><?=$language[$session->read('lang')]['table_head_lesson_time']?></th>
			<th><?=$language[$session->read('lang')]['table_head_student_level']?></th>
			<th><?=$language[$session->read('lang')]['table_head_kind_of_lesson']?>n</th>
			<th><?=$language[$session->read('lang')]['table_head_max_students']?></th>
			<th><?=$language[$session->read('lang')]['table_head_how_long']?></th>
			<th><?=$language[$session->read('lang')]['table_head_point']?></th>
			<th><?=$language[$session->read('lang')]['table_head_action']?></th>
		</tr>
		<?php foreach($groupLessons as $key=>$groupLesson):?>
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
				<img src = "<?=c_path().$groupLesson['teachers']['img_file']?>"  width = "150" height = "150" class = "img img-thumbnail"
				onclick = "goToTeacherProfile(<?=$groupLesson['group_lessons']['teacher_id']?>)" 
				id = "teacherImg"
				>
			</td>
			<td>
				<?=$groupLesson['group_lessons']['topic_title'];?><br>
				<button onclick = "popup_details(<?=$groupLesson['group_lessons']['id']?>)" class = "btn btn-success btn-sm"><?=$language[$session->read('lang')]['btn_details']?></button>
			</td>
			<td><?=$groupLesson['group_lessons']['lesson_time'];?></td>
			<td><?=$groupLesson['group_lessons']['student_level'];?></td>
			<td>
				<?php 
					if($groupLesson['group_lessons']['kinds_of_lesson'] == 1)
						echo "Common";
					else if($groupLesson['group_lessons']['kinds_of_lesson'] == 2)
						echo "Adult";
					else if($groupLesson['group_lessons']['kinds_of_lesson'] == 3)
						echo "Child";
				?>
			</td>
			<td><?=$groupLesson['group_lessons']['max_student'];?></td>
			<td>
				<?php 
					if($groupLesson['group_lessons']['how_long'] == 1)
						echo "25 minutes";
					else if($groupLesson['group_lessons']['how_long'] == 2)
						echo "50 minutes";
					else if($groupLesson['group_lessons']['how_long'] == 3)
						echo "75 minutes";
				?>
			</td>
			<td><?=$groupLesson['group_lessons']['point'];?></td>
			<td>
				<button class = "btn btn-success btn-sm" onclick="location.href = '<?=$groupLesson['group_lessons']['skype_link'];?>'">Join Skype</button><br>
				<button class = "btn btn-warning btn-sm" onclick = "cancelClass(<?=$userId?>,<?=$groupLesson['group_lessons']['id'];?>,<?=$groupLesson['lessons']['id'];?>)"><?=$language[$session->read('lang')]['btn_cancel']?></button>
			</td>
		</tr>
		<?php endforeach;?>
		<?php
			if(sizeof($groupLessons)==0){
				echo '<tr>';
				echo '<td colspan = "10">You don\'t have any group class reservation.</td>';
				echo '</tr>';
			}
		?>
	</table>
	<?=$paging;?>
</div>
<style>
	#wrapper{
		margin-left: 15px;
	}
	#btnGroupClasses{
		margin-bottom: 0.5em;
	}
	#teacherImg{
		cursor: pointer;
	}
	li#active.active > a{
		background-color: #F0A75D;
	}
</style>
<script>
	function goToGroupClasses(){
		location.href = "<?=c_path()?>"+"/group_lessons";
	}

	function goToClassHistory(){
		location.href = "<?=c_path()?>"+"/group_lessons/class_history";
	}

	function cancelClass(userId,groupLessonId,lessonId){
		var r = confirm("Are you sure you want to cancel the class?");
		if (r == true) {
		    location.href = "<?=c_path()?>"+"/group_lessons/cancel/"+userId+"/"+groupLessonId+"/"+lessonId;
		}
	}
	function popup_details(groupLessonId){
		window.open("<?=c_path()?>/group_lessons/window_popup_details/"+groupLessonId, "", "width=850,height=600,menubar=yes,location=yes,resizable=yes,scrollbars=yes,status=yes");
	}

	function goToMyGroupReservations(){
		location.href = "<?=c_path()?>"+"/group_lessons/my_reservations";
	}
	
	function goToTeacherProfile(teacherId){
		location.href = "<?=c_path()?>"+"/teachers/reserve/"+teacherId;
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
<?php include ('views/common/bottom.php');?>
