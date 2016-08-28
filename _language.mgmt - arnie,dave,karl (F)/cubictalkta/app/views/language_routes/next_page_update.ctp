<h2>Language Routes</h2>
<?php echo $javascript->link('jquery-1.4.2.min');?>

<?php
	echo $form->create(null,array('action'=>'update_action'));
?>
<table>
	<tr>
		<td>Action</td>
		<td>
			<select id="sel">
				<?php
					$a;
					foreach($actions as $action){
						$a = $action['language_routes']['action'];
						echo "<option>$a</option>";
					}
				?>
			</select>
			<input type='hidden' name = "data[LanguageRoute][controller]" id ='hid1' value = "<?php echo $controller;?>">
			<input type='hidden' name = "data[LanguageRoute][old_action]" id ='tbAction' value = "">
		</td>
	</tr>
	<tr>
		<td>New Action </td>
		<td>
			<input name="data[LanguageRoute][action]" value="">
		</td>
	</tr>
</table>
<?php
	echo $form->end('Edit');
?>
<?php
	echo $form->create(null,array('action'=>'index'));
?>
<?php
	echo $form->end('Back');

?>
<script>
	$(function(){
		$('#sel').change(function(){
			var x = $(this).val();
			$('#tbAction').val(x);
		});

		$('#sel').change();
	});
</script>