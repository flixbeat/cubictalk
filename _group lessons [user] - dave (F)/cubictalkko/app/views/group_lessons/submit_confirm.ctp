<?php include('views/common/top.php');?>
<style>
	#wrapper{
		margin-left: 15px;
	}
</style>
<div id = "wrapper"  class = "div-container">
	<h2>Reservation Success</h2>
	<button type = "button" class = "btn btn-info" onclick = "location.href='<?=c_path()?>/group_lessons'">Show Group Classes</button>
	<button type = "button" class = "btn btn-success" onclick = "location.href='<?=c_path()?>/group_lessons/my_reservations'">Show My Reservations</button>
</div>
<?php include ('views/common/bottom.php');?> 