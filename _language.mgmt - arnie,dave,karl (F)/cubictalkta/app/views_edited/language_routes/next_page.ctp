<?php echo $javascript->link('jquery-1.4.2.min.js')?>
<?php echo $form->create(null,array('action'=>'delete_language')); ?>
<table border = "1" cellpadding = "3" cellspacing = "0">
	<tr>	
		<td colspan="2">
			<h2> Delete</h2>
		</td>
	</tr>		
	<tr>
		<td>
			Action
				<select id = 'sel'>
					<?php 
					//controller delete
						for($i=0; $i<count($actions); $i+=1) {
							$id = $actions[$i]['language_routes']['id'];
							$action = $actions[$i]['language_routes']['action'];
							echo "<option value = '$id'>$action</option>";
						}
					?>
				</select>
				<input type = "hidden"  name = "data[LanguageRoute][Id]" id = 'tbId' value = ''>
				<input type = "hidden"  name = "data[LanguageRoute][action]" id = 'tbAction' value = ''>
		</td>
		<td>			
			<?php echo $form->end('Delete'); ?>
		</td>
	</tr>
</table>

<script type="text/javascript">

$(function(){

	$('#sel').change(function(){
		var id = $(this).val();
		var action = $("#sel option[value='"+id+"']").text();
		
		$('#tbAction').val(action);
		$('#tbId').val(id);
	});

	$('#sel').change();
});


function closeWindow() {
    window.close();
    window.opener.location.reload();
}
</script>

<br>
<button type = "button" onclick = "closeWindow()" id = "btnCloseWin">Close Window</button>


<style>
	#btnCloseWin{
		margin: auto;
		width: 150px;
		height: 50px%;
	}
</style>
