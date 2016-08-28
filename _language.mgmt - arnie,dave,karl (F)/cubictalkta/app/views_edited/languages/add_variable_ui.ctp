<h2>Add Variable</h2>

<?php echo $form->create();?>
<table>
	<tr>
		<td>Variable</td>
		<td><?php echo $form->input('variable',array('label'=>'')) ?></td>
	</tr>
	<tr>
		<td>English Text</td>
		<td><?php echo $form->input('en_text',array('label'=>'')) ?></td>
	</tr>
	<tr>
		<td>Korean Text</td>
		<td><?php echo $form->input('ko_text',array('label'=>'')) ?></td>
	</tr>
	<input type = "hidden" name = "data[Language][route_id]" value = "<?php echo $texts[0]['languages']['route_id']; ?>">
</table>
<?php echo $form->end('Add');?>
<h3>Variables</h3>
<table border = "1">
	<tr>
		<th>Variable</th>
		<th>English Text</th>
		<th>Korean Text</th>
	</tr>
	<?php
		if(isset($texts[0]['languages']['variable'])){
			foreach ($texts as $text) {
				echo '<tr>';
				echo '<td>'.$text['languages']['variable'].'</td>';
				echo '<td>'.$text['languages']['en_text'].'</td>';
				echo '<td>'.$text['languages']['ko_text'].'</td>';
				echo '</tr>';
			}
		}
		else{
			echo '<tr><td colspan = "3">No records.</td></tr>';
		}
	?>
</table>
<br>
<?php echo $html->link('Go to Manage Page Languages >',array('controller'=>'language','action'=>'index')) ?><br>
