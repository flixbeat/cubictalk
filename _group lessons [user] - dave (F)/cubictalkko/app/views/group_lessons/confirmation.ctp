<?php include('views/common/top.php');?>
<style>
	#wrapper{
		margin-left: 15px;
	}
</style>
<div id = "wrapper"  class = "div-container">
	<h2><?=$language[$session->read('lang')]['heading_confirm_reservation']?></h2>
	<div class = "row">
		<div class = "col-sm-3">
			<img src = "<?=c_path().$groupLessons[0][0]['img_file']?>" class = "img img-thumbnail img-responsive"/>
			<?=$language[$session->read('lang')]['text_teacher']?>: <strong><?=$groupLessons[0][0]['mb_id'];?></strong>
			<br><br>
			<button type = "button" class = "btn btn-info btn-sm" onclick = "location.href='<?=c_path()?>/group_lessons'">
				<span class = "glyphicon glyphicon-arrow-left"></span>&nbsp;
				<?=$language[$session->read('lang')]['btn_back']?>
			</button>
		</div>
		<div class = "col-sm-7">
			<table class = "table">
				<tr>
					<td><strong><?=$language[$session->read('lang')]['table_head_topic_title']?>:</strong></td>
					<td><?=$groupLessons[0]['gl']['topic_title'];?></td>
				</tr>
				<tr>
					<td><strong><?=$language[$session->read('lang')]['table_head_topic_details']?>:</strong></td>
					<td><?=$groupLessons[0]['gl']['topic_details'];?></td>
				</tr>
				<tr>
					<td><strong><?=$language[$session->read('lang')]['table_head_kind_of_lesson']?>:</strong></td>
					<td>
						<?php 
							if($groupLessons[0]['gl']['kinds_of_lesson'] == 1)
								echo "Common";
							else if($groupLessons[0]['gl']['kinds_of_lesson'] == 2)
								echo "Adult";
							else if($groupLessons[0]['gl']['kinds_of_lesson'] == 3)
								echo "Child";
						?>
					</td>
				</tr>
				<tr>
					<td><strong><?=$language[$session->read('lang')]['table_head_student_level']?>:</strong></td>
					<td><?=$groupLessons[0]['gl']['student_level'];?></td>
				</tr>
				<tr>
					<td><strong><?=$language[$session->read('lang')]['table_head_lesson_time']?>:</strong></td>
					<td><?=$groupLessons[0]['gl']['lesson_time'];?></td>
				</tr>
				<tr>
					<td><strong><?=$language[$session->read('lang')]['table_head_max_students']?>:</strong></td>
					<td><?=$groupLessons[0][0]['students_reserved']?> / <?=$groupLessons[0]['gl']['max_student']?></td>
				</tr>
				<tr>
					<td><strong><?=$language[$session->read('lang')]['table_head_how_long']?>:</strong></td>
					<td>
						<?php 
							if($groupLessons[0]['gl']['how_long'] == 1)
								echo "25 minutes";
							else if($groupLessons[0]['gl']['how_long'] == 2)
								echo "50 minutes";
							else if($groupLessons[0]['gl']['how_long'] == 3)
								echo "75 minutes";
						?>
					</td>
				</tr>
				<tr>
					<td><strong><?=$language[$session->read('lang')]['table_head_spend_point']?>:</strong></td>
					<td><?=$groupLessons[0]['gl']['point'];?></td>
				</tr>
				<tr>
					<td colspan = "2">
						<ul class = "list-group">
							<li class = "list-group-item list-group-item-warning">
								<div class = "row">
									<div class = "col-sm-5">
										<?=$language[$session->read('lang')]['text_your_current_points'].": "?>
										<?php
											$userSession = $session->read('loginInfo');
											echo $userSession['User']['points'];
										?>
									</div>
									<div class = "col-sm-3">
										<?=$language[$session->read('lang')]['text_spend_points'].": ".$groupLessons[0]['gl']['point']?>
									</div>
									<div class = "col-sm-4">
										<?=$language[$session->read('lang')]['text_your_points_after_reserving'].": "?>
										<?php
											$userSession = $session->read('loginInfo');
											$nextPoint = $userSession['User']['points'] - $groupLessons[0]['gl']['point'];
											echo $nextPoint < 0 ? "Not enough points!": $nextPoint;
										?>
									</div>
								</div>
							</li>
							<li class = "list-group-item text-center">
								<button onclick = "confirm(<?=$groupLessons[0]['gl']['id'];?>)" class = "btn btn-success">
									<?=$language[$session->read('lang')]['btn_confirm']?>
								</button>
							</li>
						</ul>
					</td>
				</tr>
			</table>


		</div>
	</div>
</div>
<?php include ('views/common/bottom.php');?>
<script>
	function confirm(groupLessonId){
		location.href = "<?=c_path()?>/group_lessons/submit_confirm/"+groupLessonId;

	}
</script>