<?php echo $javascript->link('jquery-1.4.2.min');?>
<?php echo $form->create(null,array('action'=>'insert_ui')); ?>
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.4/jquery.timepicker.min.css">
<script src="http://code.jquery.com/jquery-1.11.0.min.js"></script>
<script src="//code.jquery.com/jquery-1.10.2.js"></script>
<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>

<style type="text/css">
	#wrapper{
		margin: auto;
		width: 50%;
	}
	li#active.active > a{
		background-color: #F0A75D;
	}
</style>

<div id = "wrapper"  class = "div-container">
	<h2>Create Group Lesson</h2>

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

	<hr>
	<ul class = "nav nav-tabs">
		<li class="active" id = "active"><a href="#" onclick = "goToCGL()"><h5><strong>Create Group Lesson</strong></h5></a></li>
		<li><a href="#" onclick = "goToReservations()"><h5><strong>Reservations</strong></h5></a></li>
		<li><a href="#" onclick = "goToClassHistory()"><h5><strong>Class History</strong></h5></a></li>
	</ul>
	
	<div class = "row">
		<h2>Create Group Lesson</h2>
		<table class = "table" >
		<script src="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.4/jquery.timepicker.min.js"></script>
			<tr>
				<?php
					if($teachLevel >=2 ) {
						echo "<td><lable>Teacher</label></td>";	
						echo "<td>";	
							echo "<select>";
								foreach($teacher as $teacher) {
									echo "<option value = '".$teacher['teachers']['id']."'>" . $teacher['teachers']['mb_id'] .  "</option>";
								}
							echo "</select>";
						echo "</td>";			
					}
				?>
			</tr>	
			<tr>
				<td><label>Topic Title</label></td>
				<td><input type = "text" name = "data[GroupLesson][topic_title]" required="data[GroupLesson][topic_title]"></td>
			</tr>
			<tr>
				<td><label>Topic Details</label></td>
				<td><textarea name = "data[GroupLesson][topic_details]" required = "data[GroupLesson][topic_details]"></textarea></td>
			</tr>
			<tr>
				<td><label>Composition<label></td>
				<td>
					<input type = "text" id = "composition" name = "data[GroupLesson][composition_question_id]" required= "data[GroupLesson][composition_question_id]">
					<button type = "button" id = 'btn' onclick = "showCompoQues()">Choose Composition </button>
				</td>
			</tr>
			<tr>
				<td><label>Max Student</label></td>
				<td>
					<select name = "data[GroupLesson][max_stud]" name = "data[GroupLesson][max_student]" id = "maxStud">
						<option value = "1">1</option>
						<option value = "2">2</option>
						<option value = "3">3</option>
						<option value = "4">4</option>
					<select>
				</td>
				<input type = "hidden" name = "data[GroupLesson][max_student]" value = "1">
			</tr>
			<tr>	
				<td><label>Min Level</label></td>
				<td>
					<select name = "data[GroupLesson][stud_min_level]" required = "data[GroupLesson][student_min_level]" id="minLevel">
						<option value = "1">1</option>
						<option value = "2">2</option>
						<option value = "3">3</option>
						<option value = "4">4</option>	
					</select>
					<button type = "button" onclick = "window.open('<?=c_path()?>/leveltests_comments/leveltests_comments/index', '','width=800,height=600','menubar=yes,location=yes,resizable=yes,scrollbars=yes,status=yes')">Show Grade Description</button>
				</td>
				<input type = "hidden" name = "data[GroupLesson][student_min_level]" value = "1">
			</tr>

			<tr>
				<td><label>Lesson Date</label></td>
				<td><div><input type = "text" id="datepicker" required></div></td>
				<input type = "hidden" name = "data[GroupLesson][less_date]">
				<td><label>Lesson Time</label></td>
				<td>
					<?php
						echo '<select name = "data[GroupLesson][lesson_time]" id="time">';
							for($hours=0; $hours<24; $hours++) {
								for($mins=0; $mins<60; $mins+=30) {
									echo '<option>' . str_pad($hours,2,'0',STR_PAD_LEFT). ':' .str_pad($mins,2,'0',STR_PAD_LEFT) . '</option>';			
								}
							}
						echo '</select>';
					?>
				</td>
				<input type = "hidden" name = "data[GroupLesson][less_time]">
			</tr>
			<tr>	
				<td><label>Point</label></td>
				<td><input type = "text" name = "data[GroupLesson][point]"  required = "data[GroupLesson][point]"></td>
			</tr>
			<tr>
				<td><label>How Long</label></td>
				<td><input type = "radio" value="1" name = "how_long" checked>25 Minutes</td>
				<td><input type = "radio" value="2" name = "how_long">50 Minutes</td>
				<td><input type = "radio" value="3" name = "how_long">75 Minutes</td>
				<input type = "hidden" name = "data[GroupLesson][how_long]" value = "1">
			</tr>
			<tr>
				<td><label>Kinds of Lesson</label></td>
				<td><input type = "radio" value = "1" name = "les_kind" checked>Adult</td>
				<td><input type = "radio" value = "2" name = "les_kind">Children</td>
				<td><input type = "radio" value = "3" name = "les_kind">Common	</td>
				<input type = "hidden" name = "data[GroupLesson][les_kind]" value = "1">
			</tr>	
			<tr>
				<td><label>Skype Link</label></td>
				<td><input type = "text" name = "data[GroupLesson][skype_li]" placeholder = "ex: https://join.skype.com/GNoAHrH0WzXppr" style = "width: 25em"></td>
			</tr>
		</table>
			
		<div class="submit">
			<input value="Insert" type="submit" class = "btn btn-primary btn-lg" style = "width:150px">
		</div>


		
	</div>
</div>
</form>
	
 <script>
	$(document).ready(function() {
		 $("input[name=how_long]").click(function() {
			var long = ($(this).val());
			$("input[type=hidden][name='data[GroupLesson][how_long]']").val(long);
		});

		 $("input[name=les_kind]").click(function() {
		 	var min = ($(this).val());
		 	$("input[type=hidden][name='data[GroupLesson][les_kind]']").val(min);
		 });
		 $( "#datepicker" ).datepicker({
		 	onSelect: function(dateText, inst) {
		 		$("input[type=hidden][name='data[GroupLesson][less_date]']").val(dateText);
		 	}  
		 });	
		 $("#time").change(function(){
				$("input[type=hidden][name='data[GroupLesson][less_time]']").val($(this).val());
		});	
		 $("#maxStud").change(function(){
				$("input[type=hidden][name='data[GroupLesson][max_student]']").val($(this).val());
		});	
		 $("#minLevel").change(function(){
				$("input[type=hidden][name='data[GroupLesson][student_min_level]']").val($(this).val());
		});				 
	});

	function getId() {
		$("input:radio[name=ques]").click(function() {
		 	document.getElementById("composition").value=($(this).val());
		 });
	}

	
	function showDetails(id) {
		var myWindow = window.open("<?=c_path()?>/group_lessons/showDetails/"+id, "Show Details", "width=600,height=600, menubar=yes,location=yes,resizable=yes,scrollbars=yes,status=yes");
		document.write($(this).val());
	}	

	function closeWindow() {
		myWindow.close();
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

	function showCompoQues() {
		var Mywindow = window.open("<?=c_path()?>/group_lessons/showCompoQues/", "Composition Question", "width=800, height=700,menubar=yes,location=yes,resizable=yes,scrollbars=yes,status=yes");
		window.blur();
		
	}
</script>