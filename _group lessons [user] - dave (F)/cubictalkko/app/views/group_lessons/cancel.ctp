<?php include('views/common/top.php');?>
<div id = "wrapper"  class = "div-container">
	<p>You canceled your group class</p>
	<button class = "btn btn-primary" onclick = "location.href='<?=c_path()?>/group_lessons/my_reservations'">Go to My Reservations</button>
	<button class = "btn btn-success" onclick = "location.href='<?=c_path()?>/group_lessons/'">Group Classes</button>
</div>
<style>
	#wrapper{
		margin-left: 15px;
	}
</style>
<?php include('views/common/bottom.php');?>