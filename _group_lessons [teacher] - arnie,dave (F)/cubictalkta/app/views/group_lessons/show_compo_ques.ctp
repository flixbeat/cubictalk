<?php echo $javascript->link('jquery-1.4.2.min');?>
<style type="text/css">
	#search_panel{
		position:fixed;
		width:100%;
		background-color: rgba(128,128,128,0.4);
		padding: 5px;
	}
	.shadowed{
		text-shadow: 2px 2px 5px gray;
	}
</style>


<div class = "div-container">
	

	<div class = "text-center" id = "search_panel">
		<form id="GroupLessonAddForm" method="post" action="/cubictalkta/group_lessons/showCompoQues"><fieldset style="display:none;"><input type="hidden" name="_method" value="POST" /></fieldset>
			<label class = "shadowed">Teacher Name: </label><input list = "tea_name" name = "data[teacher_name]" value = "<?= isset($_POST['data']['teacher_name']) ? $_POST['data']['teacher_name']:null ?>">
			<datalist id = "tea_name">
				<?php foreach($teacherNames as $teacherName):?>
					<option value = "<?=$teacherName['teachers']['tea_name']?>">
				<?php endforeach;?>
			</datalist>
			<button class = "btn btn-primary btn-sm">Search</button>
			<button class = "btn btn-warning btn-sm" onclick="closeWindow()"><span class = "glyphicon glyphicon-remove"></span>Close</button>
		</form>
		
	</div>

	<h3>Composition Questions</h3>
	<hr>

	<table id = "table2" class = "table table-hover table-bordered" >
	 	
		<tr>
			<th>Choose</th>
			<th>Title</th>
			<th>Question</th>
			<th>Group Composition Id</th>
			<th>Submitted By</th>
			<th>Created</th>
			<th>Modified</th>
		</tr>
		<?php
			foreach($questions as $row) {
				$id = $row['cq']['id'];
				$title = $row['cq']['title'];
				$question = $row['cq']['question'];
				$img = $row['t']['img_file'];
					echo '<tr>';
						echo '<td>' . "<button class ='btnSub btn btn-primary' value = '$id'>" . 'Select' . '</td>';	
						#echo '<td>' . "<input type = 'radio' name = 'ques' id = 'ques' value = '$id' onclick='getId()'>" . '</td>'; //
						echo '<td>' . $row['cq']['title'] . '</td>';
						echo '<td>' . "<button class = 'btn btn-default' value = '$id' name ='data[GroupLesson][question]' id = 'btn' onclick = 'showDetails($id)'>". 'Show Details' . "</button>" . '</td>';
						echo '<td>' . $row['cq']['group_composition_id'] . '</td>';
						echo '<td>'. "<img src='".cubictalkko_path()."/$img/' height ='80' width='80'>" . '</td>';
						echo '<td>' . $row['cq']['created'] . '</td>';
						echo '<td>' . $row['cq']['modified'] . '</td>';
					echo '</tr>';
			}
			echo '<input type = "hidden" name = "data[GroupLesson][question]" id = "hidQ">';
			echo '<input type = "hidden" name ="datap[GroupLesson][question]" id ="compoQues">';
			
		 ?>
	</table>
</div>
<script>	
	function showDetails(id) {
		var myWindow = window.open("<?=c_path()?>/group_lessons/showDetails/"+id, "Show Details", "width=600,height=600","menubar=yes,location=yes,resizable=yes,scrollbars=yes,status=yes");
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

	function closeWindow() {
		window.close();	
	}

	/*
	$('input[name="ques"]').click(function(){
		$('#btnSub').val($(this).val());
	});
	*/

	$('.btnSub').click(function(){
		var composition = window.opener.document.getElementById("composition");
		composition.value = $(this).val();
		window.close();
	});

	/*
	function getId() {

		$("input:radio[name=ques]").click(function() {
			var x = document.getElementById("btnSub").value=($(this).val());
			popup.focus();
			alert(x);
		});

	}
	
	

	function setValue() {
		var a = document.getElementById("ques").value;
		document.getElementByid("composition").innerHTML = a;

	}
	function passValue() {
		if(window.opener != null && !window.opener.closed) {
			var composition = window.opener.document.getElementById("composition");
			composition.value = document.getElementById("btnSub").value;

		}

		alert('Selected');
		window.close();
	}
	*/
</script> 
	