<h2>Website Language Management</h2>
<?php echo $form->create(null,array('controller'=>'language','action'=>'manage'));?>
<table>
	<tr>
		<td>Variable</td>
		<td><?php echo $form->input('variable',array('label'=>''));?></td>
	</tr>
	<tr>
		<td>English</td>
		<td><?php echo $form->input('en_text',array('label'=>''));?></td>
	</tr>
	<tr>
		<td>Korean</td>
		<td><?php echo $form->input('ko_text',array('label'=>''));?></td>
	</tr>
	<?php echo $form->input('route_id',
		array('type'=>'hidden',
			'value'=> isset($route_id) ? $route_id : $this->data['Language']['route_id'] ));
	?>
</table>
<?php echo $form->end('Submit')?>