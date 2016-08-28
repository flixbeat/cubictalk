<?php echo $javascript->link('jquery-1.4.2.min');?>
<h2>Manage Page Languages</h2>

<?php echo $form->create(null,array('action'=>'edit_language_ui'))?>
<table>
	<tr>
		<td colspan = "2"><b>Update</b><hr></td>
	</tr>
	<tr>
		<td>Select controller and action: </td>
		<td>
			<select name="data[LanguageRoute][controller]" id="LanguageRouteController">
				<?php
					for($i=0;$i<count($controller);$i+=1){
						$id = $controller[$i]['language_routes']['id'];
						$ctrName = $controller[$i]['language_routes']['controller'];
						$action = $controller[$i]['language_routes']['action'];
						echo "<option value = '$id'>$ctrName/$action</option>";
					}
				?>
			</select>
			<input type = "hidden" name = "data[LanguageRoute][id]" value = "">
		</td>
	</tr>
</table>
<?php echo $form->end('Next >');?>
<br>
<?php echo $html->link('Go to language routes >',array('controller'=>'language_route','action'=>'index'));?>

<script>	
	$('input[name="data[LanguageRoute][id]"]').val($('select[name="data[LanguageRoute][controller]"]').val());
	$('select[name="data[LanguageRoute][controller]"]').change(function(){
		$('input[name="data[LanguageRoute][id]"]').val($(this).val());
	});
</script>