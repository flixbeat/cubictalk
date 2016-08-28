<?php echo $javascript->link('jquery-1.4.2.min');?>
<h2>Specify Website Page</h2>

<?php echo $form->create()?>
<table>
	<tr><td colspan = "2"><b>Insert<b><hr/></td></tr>
	<tr>
		<td>Controller</td>
		<td><?php echo $form->input('controller',array('label'=>''))?></td>
	</tr>
	<tr>
		<td>Action</td>
		<td><?php echo $form->input('action',array('label'=>''))?></td>
	</tr>
	<tr>
		<td><?php echo $form->end('Next >')?></td>
		<td><?php echo $html->link('Go to language management',array('controller'=>'language','action'=>'index'));?></td>
	</tr>
</table>

<br>
<?php echo $form->create(null,array('action'=>'edit'))?>
<table>
	<tr><td colspan = "2"><b>Update</b><hr/></td></tr>
	<tr>
		<td>Controllers</td>
		<td>
			<select name="data[LanguageRoute][controller_update]" id="LanguageRouteController">
				<?php
					for($i=0;$i<count($controller);$i+=1){
						$id = $controller[$i]['language_routes']['id'];
						$ctrName = $controller[$i]['language_routes']['controller'];
						echo "<option value = '$id'>$ctrName</option>";
					}
				?>
			</select>
			<input type = "hidden" name = "data[LanguageRoute][id_update]" value = "">
		</td>
	</tr>
	<tr>
		<td>New Controller</td>
		<td><?php echo $form->input('controller',array('label'=>''))?></td>
	</tr>
	<tr>
		<td>New Action</td>
		<td><?php echo $form->input('action',array('label'=>''))?></td>
	</tr>
</table>
<?php echo $form->end('Save')?>
<br>
<?php echo $form->create(null,array('action'=>'delete'))?>
<table>
	<tr>
		<td colspan = "2"><b>Delete<b><hr></td>
	</tr>
	<tr>
		<td>
			Select Controller
		</td>
		<td>
			<select name="data[LanguageRoute][controller_delete]" id="LanguageRouteController">
				<?php
					for($i=0;$i<count($controller);$i+=1){
						$id = $controller[$i]['language_routes']['id'];
						$ctrName = $controller[$i]['language_routes']['controller'];
						echo "<option>$ctrName</option>";
					}
				?>
			</select>
		</td>
	</tr>
	<tr>
		<td>
			Select Action
		</td>
		<td>
			<select id = "cbActions">
			</select>
			<input type = "hidden" name = "data[LanguageRoute][controller_delete]" value = "">
			<input type = "hidden" name = "data[LanguageRoute][action_delete]" value = "">
		</td>
	</tr>
</table>

<?php echo $form->end('Delete')?>

<button id = "asdf">do ajax</button>
<script>
	// update
	$('input[name="data[LanguageRoute][id_update]"]').val($('select[name="data[LanguageRoute][controller_update]"]').val());
	$('select[name="data[LanguageRoute][controller_update]"]').change(function(){
		$('input[name="data[LanguageRoute][id_update]"]').val($(this).val());
	});
	// delete
	$('input[name="data[LanguageRoute][id_delete]"]').val($('select[name="data[LanguageRoute][controller_delete]"]').val());
	$('select[name="data[LanguageRoute][controller_delete]"]').change(function(){
		$('input[name="data[LanguageRoute][id_delete]"]').val($(this).val());
	});

	$('#asdf').click(function(){
		$.post('language_route/ajax',{test:'test123'},function(data){
			alert(data);
		});
	});


	// ajax
	$('select[name="data[LanguageRoute][controller_delete]"]').change(function(){
		//alert(0);
		$.post('language_route/ajax',{
			action:'getActionOptions',
			controller:$(this).val()
		},
		function(data){
			$('#cbActions').html(data);
		});
	});

	$('select[name="data[LanguageRoute][controller_delete]"]').change();
	
	$('#cbActions').change(function(){
		$('input[name="data[LanguageRoute][action_delete]"]').val($(this).val());
	});

	$('input[name="data[LanguageRoute][controller_delete]"]').val($('select[name="data[LanguageRoute][controller_delete]"]').val());
	$('input[name="data[LanguageRoute][action_delete]"]').val($('#cbActions').val());

</script>