<div id = "wrapper" class = "div-container text-center">
	<h3>Reserved Students</h3>
	<button class = "btn btn-success" onclick = "closeWin()">Close Window</button>
	<hr>
	<?php foreach($studentInfo as $student):?>
		<?php 
			if($student['users']['sex']=='M')
				echo "<img src = '".c_path()."/image/show_students/boy-avatar.png' class = 'img-thumbnail' width = 150 height = 150>";
			else
				echo "<img src = '".c_path()."/image/show_students/girl-avatar.png' class = 'img-thumbnail' width = 150 height = 150>";
		?>
		<ul class = "list-group">
			<li class = "list-group-item list-group-item-info"><?=$student['users']['nick_name']?></li>
			<li class = "list-group-item"><?="Level: ".$student['users']['level']?></li>
			<li class = "list-group-item">
				Age:
				<?php 
					if($student['users']['age'] < 11)
						echo "<img style = 'display:inline' src = '".c_path()."/image/show_students/age1.png' class = 'img-responsive' width = 25 height = 25>";
					else if($student['users']['age'] >= 11 && $student['users']['age'] < 21)
						echo "<img style = 'display:inline' src = '".c_path()."/image/show_students/age11.png' class = 'img-responsive' width = 25 height = 25>";
					else if($student['users']['age'] >= 21 && $student['users']['age'] < 31)
						echo "<img style = 'display:inline' src = '".c_path()."/image/show_students/age21.png' class = 'img-responsive' width = 25 height = 25>";
					else if($student['users']['age'] >= 31)
						echo "<img style = 'display:inline' src = '".c_path()."/image/show_students/age31.png' class = 'img-responsive' width = 25 height = 25>";
				?>
			</li>
		</ul>		
		<hr>
	<?php endforeach;?>
</div>
<style>
	#wrapper{
		margin: auto;
		width: 300px;
		min-height: 200px;
	}
</style>
<script type="text/javascript">
	function closeWin(){
		window.close();
	}
</script>