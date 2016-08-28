<h2>Edit Translation</h2>

<?php echo $form->create(null,array('action'=>'edit')); ?>
<?php $id = null;?>
<table>
	<tr>
		<th>Old Values</th>
		<th>New Values</th>
	</tr>
	<tr>
		<?php
			foreach ($texts as $text) {
				$id = $text['languages']['id'];
				$variable = $text['languages']['variable'];
				$en = $text['languages']['en_text'];
				$ko = $text['languages']['ko_text'];

				echo '<tr>';
				echo '<td>'.$variable.'</td>';
				echo '<td>'.$form->input('variable',array('label'=>'','value'=>$variable)).'</td>';
				echo '</tr>';
				echo '<tr>';
				echo '<td>'.$en.'</td>';
				echo '<td>'.$form->input('en_text',array('label'=>'','value'=>$en)).'</td>';
				echo '</tr>';
				echo '<tr>';
				echo '<td>'.$ko.'</td>';
				echo '<td>'.$form->input('ko_text',array('label'=>'','value'=>$ko)).'</td>';
				echo '</tr>';
			}
		?>
		<input type = "hidden" name = "data[Language][id]" value = "<?php echo $id;?>">
	</tr>
</table>
<?php echo $form->end('Save')?>
<br>