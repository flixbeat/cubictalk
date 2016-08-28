<?php include('views/common/top.php');?>
<div id = "wrapper"  class = "div-container">
	<table class="table table-hover">
		<tr>
			<th colspan = "7"><h2>Group Class<h2></th>
		</tr>
		<tr>
			<th>Select</th>
			<th>Teacher</th>
			<th>Lesson</th>
			<th>How Long</th>
			<th>Topic</th>
			<th>Max Student</th>
			<th>Level</th>
		</tr>
		
		<?php foreach ($groupLessons as $groupLesson):?>
		<tr>
			<td><button value = "<?=$groupLesson['gl']['id']?>" class = "btn btn-primary"onclick="window.location.href='confirm'">Reserve</button></td>
				<td><img src = "<?=c_path().$groupLesson[0]['teacher_img']?>" width = "60%" height = "60%" class = "img img-thumbnail"></td>
				<td><?=$groupLesson['gl']['lesson_time']?></td>
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
					<?=$groupLesson['gl']['topic_title']?><br>
					<button onclick = "popup_details(<?=$groupLesson['gl']['id']?>)" class = "btn btn-info btn-sm">Details</button>
				</td>
				<td><?=$groupLesson['gl']['max_student']?></td>
				<td><?=$groupLesson['gl']['student_level']?></td>
			</tr>
		<?php endforeach; ?>
	</table>
</div>
<?php include ('views/common/bottom.php');?>

<?#=pr($groupLessons);?>

<script>
	function popup_details(groupLessonId){
		window.open("window_popup_details/"+groupLessonId, "", "width=350,height=200");
	}
</script>

<style>
	#wrapper{
		width: 700px;
		min-height: 300px;
		margin: auto;
	}
</style>