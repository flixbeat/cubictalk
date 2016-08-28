<?php echo $javascript->link('jquery-1.4.2.min');?>
<fieldset>
	<legend>Edit Age and Sex Data</legend>
	<?php echo $form->create(null, array('action'=>'edit_age_sex_func')) ?>
	<label>Age:</label>&ensp;<input type = "number" value = "<?=$age?>" name = "data[Lesson][age]"><br>
	

	<label>Sex:</label>
	M <input type = "radio" name = "sex" id = "rbM" <?php echo $sex=="M"? 'checked':'' ?>>
	F <input type = "radio" name = "sex" id = "rbF" <?php echo $sex=="F"? 'checked':'' ?>>	
	<input type = "hidden" value = "<?=$sex?>" name = "data[Lesson][sex]" id = "tbSex">


	<input type = "hidden" value = "<?=$userId?>" name = "data[Lesson][id]">

	<br><br>
	<?php echo $form->end('Save') ?><br>
	
	<button type = "button" onclick = "window.close()" style = "display:inline;">Close</button>
</fieldset>

<script>
	$(document).ready(function(){
		$('#rbM').click(function(){
			$('#tbSex').val('M');
			//alert($('#tbSex').val());
		});

		$('#rbF').click(function(){
			$('#tbSex').val('F');
			//alert($('#tbSex').val());
		});


	});
</script>

