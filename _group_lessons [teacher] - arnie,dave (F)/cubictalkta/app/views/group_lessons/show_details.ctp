<?php
	//pr($showDetails);
	//pr($id);
?>
<?php echo $javascript->link('jquery-1.4.2.min');?>
<table border = "1" cellspacing="0" cellpadding="3" width="10px" height = "10px">
	<tr>
		<th>Question</th>
	</tr>
	<tr>
		<?php
			foreach($showDetails as $row) {
				echo '<tr>';	
					echo '<td>'. $row['composition_questions']['question']  . '</td>';	
				echo '</tr>';	
			}
		?>
	</tr>	
</table>
	