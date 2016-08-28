<h2>Manage Languages</h2>
<table border = "1">
	<tr>
		<th>Variable</th>
		<th>English Text</th>
		<th>Korean Text</th>
		<th>Edit</th>
		<th>Delete</th>
	</tr>
	<?php
		$id = null;
		if(isset($languages)){
			foreach($languages as $language){
				$route_id = $language['languages']['route_id'];
				echo '<tr>';
				echo '<td>'.$language['languages']['variable'].'</td>';
				echo '<td>'.$language['languages']['en_text'].'</td>';
				echo '<td>'.$language['languages']['ko_text'].'</td>';
				echo '<td>'.$html->link("Edit Translation",array('controller'=>'language','action'=>'edit_translation_ui',$language['languages']['id'])).'</td>';
				echo '<td>'.$html->link("Delete",array('controller'=>'language','action'=>'delete',$language['languages']['id']), array(), "Are you sure you wish to delete this translation?").'</td>';
				echo '</tr>';
			}
		}
		else{
			echo '<tr><td colspan = "5">No records.</td></tr>';
		}
		
	?>
</table>
<br>
<?php echo $html->link('Add variable >',array('controller'=>'language','action'=>'add_variable_ui',$route_id)); ?>