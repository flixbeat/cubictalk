<?php echo $javascript->link('jquery-1.4.2.min');?>
<?	if ( empty($loginInfo) ) { ?>
<script type="text/javascript">
	window.parent.location.href = "<?=b_path();?>/";
</script>
<?	} else {?>
<style type="text/css">
	.button {

		-moz-box-shadow:inset 0px -3px 7px 0px #23395e;
		-webkit-box-shadow:inset 0px -3px 7px 0px #23395e;
		box-shadow:inset 0px -3px 7px 0px #23395e;
		background:-webkit-gradient(linear, left top, left bottom, color-stop(0.05, #2e466e), color-stop(1, #415989));
		background:-moz-linear-gradient(top, #2e466e 5%, #415989 100%);
		background:-webkit-linear-gradient(top, #2e466e 5%, #415989 100%);
		background:-o-linear-gradient(top, #2e466e 5%, #415989 100%);
		background:-ms-linear-gradient(top, #2e466e 5%, #415989 100%);
		background:linear-gradient(to bottom, #2e466e 5%, #415989 100%);
		filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#2e466e', endColorstr='#415989',GradientType=0);
		background-color:#2e466e;
		-moz-border-radius:1px;
		-webkit-border-radius:1px;
		border-radius:1px;
		border:1px solid #1f2f47;
		display:inline-block;
		cursor:pointer;
		color:#ffffff;
		font-family:arial;
		font-size:10px;
		padding:2px 5px;
		text-decoration:none;
		text-shadow:0px 1px 0px #263666;
		margin-bottom:2px;
	}
	.button:hover {
		background:-webkit-gradient(linear, left top, left bottom, color-stop(0.05, #415989), color-stop(1, #2e466e));
		background:-moz-linear-gradient(top, #415989 5%, #2e466e 100%);
		background:-webkit-linear-gradient(top, #415989 5%, #2e466e 100%);
		background:-o-linear-gradient(top, #415989 5%, #2e466e 100%);
		background:-ms-linear-gradient(top, #415989 5%, #2e466e 100%);
		background:linear-gradient(to bottom, #415989 5%, #2e466e 100%);
		filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#415989', endColorstr='#2e466e',GradientType=0);
		background-color:#415989;
	}
	.button:active {
		position:relative;
		top:1px;
	}
	div#ex-description {
		padding:5px;
		font-weight:bold;
		font-family:Arial, Helvetica, sans-serif;
	}
	div#ex-description span { padding:5px; }
	
	div.content .table-striped thead {
		font-size:14px;
		background-color: #243743;
		color: #FFFFFF;
	}
	div.content th, div.content td {
		text-align:left;
		border-bottom:1px #c0c0c0;
		padding:5px;
		vertical-align: top;
	}
	
	div.content .table-striped tbody tr:nth-child(odd) {
	   background-color: #EBF4FA;
	}
	
	div.content {
		padding:0 20px 0 0;
		font-family:"Trebuchet MS", Helvetica, sans-serif;
		font-size:14px;
	}
	
	div#ex-description {
		padding:5px;
		font-size:14px;
		font-weight:bold;
	}
</style>
<script type="text/javascript">
	var screenH = screen.height;
	var screenW = screen.width;

	function change_status(lesson_id,update) {
//		url = "<?=c_path();?>/lessons/teacher_change_lesson_status/" + lesson_id + "/"+ update;
//		popup_window(url);
		window.open("<?=c_path();?>/lessons/teacher_change_lesson_status/" + lesson_id + "/"+ update);
	}
	
	function go_change_status_common(search_type,lesson_id) {
		document.form1.search_type.value = search_type;
		document.form1.lesson_id.value = lesson_id;
		document.form1.action = "<?=c_path()?>/lessons/teacher_management";
		document.form1.submit();
	}
   
	function go_lesson_history(id) {
		var calcuHeight = 540;
		var calcuWidth = 870;
		
		if ( screenH < calcuHeight) { calcuHeight = screenH; }
		if ( screenW < calcuWidth) { calcuWidth = screenW; }
		
		var topBottom = ((screenH - calcuHeight) - 10) / 2;
		var leftRight = ((screenW - calcuWidth) - 10) / 2;

		params = 'width='+calcuWidth+', height='+calcuHeight+', top='+topBottom+', left='+leftRight+', scrollbars=yes, fullscreen=yes, location=0';
		
		newwin=window.open("<?=c_path();?>/comments/teacher_comment_history/"+ id+ "/" + 'teacher_mypage','PopUpWindow', params);
		if (window.focus) {newwin.focus();}
		return false;
	}
// 	function iframego(){
//		var sUserName = "<?=$loginInfo['tea_name'];?>";
//		var sWebid = "<?=$loginInfo['mb_id'];?>";
// 		var sPosition = "강사";
// 		var sDept = "";
// 		var sUserlevel = "1";
// 		var sRoomtitle = ""; 
// 		var sRunmethod = "Voffice";
// 		var sAlertEndMinutes = "";
// 		var sEndTime = "";
// 		var sStrLang = "eng"; // 국문 : kor , 영문 : eng

// 		if (sPosition.length > 0) {
// 			document.voform.Position.value = encode64Han(sPosition);
// 		}
// 		if (sDept.length > 0) {
// 			document.voform.Dept.value = encode64Han(sDept);
// 		}
// 		if (sRoomtitle.length > 0) {
// 			document.voform.Roomtitle.value = encode64Han(sRoomtitle);
// 		}
// 		document.voform.Webid.value = encode64Han(sWebid);
// 		document.voform.UserName.value = encode64Han(sUserName);
// 		document.voform.Runmethod.value = sRunmethod;
// 		document.voform.AlertEndMinutes.value = sAlertEndMinutes;
// 		document.voform.EndTime.value = sEndTime;
// 		document.voform.Userlevel.value = sUserlevel;
// 		document.voform.StrLang.value = sStrLang;
// 		document.voform.submit();
// 	}
	
	function sms_Message(id){
		if ( id == 'alert' ) {
			alert("The student doesn't have any HP or a contact number.");
			return false;
		}
		
		document.forms['form1'].sendsms.value = 1;
		document.forms['form1'].action = "<?=c_path();?>/lessons/smsOn/";

		var calcuHeight = 540;
		var calcuWidth = 870;
		
		if ( screenH < calcuHeight) { calcuHeight = screenH; }
		if ( screenW < calcuWidth) { calcuWidth = screenW; }
		
		var topBottom = ((screenH - calcuHeight) - 10) / 2;
		var leftRight = ((screenW - calcuWidth) - 10) / 2;

		params = 'width='+calcuWidth+', height='+calcuHeight+', top='+topBottom+', left='+leftRight+', scrollbars=yes, fullscreen=yes, location=0';
		
		newwin=window.open("",'PopUpWindow', params);
		if (window.focus) {newwin.focus();}
		document.forms['form1'].target='PopUpWindow';

		document.forms['form1'].userId.value = id;
		
		document.forms['form1'].submit();

		return false;
	}
	
	function popdetail(id) {
		var url = "<?=c_path();?>/students/detail/"+id+"/";
		
		var calcuHeight = 540;
		var calcuWidth = 870;
		
		if ( screenH < calcuHeight) { calcuHeight = screenH; }
		if ( screenW < calcuWidth) { calcuWidth = screenW; }
		
		var topBottom = ((screenH - calcuHeight) - 10) / 2;
		var leftRight = ((screenW - calcuWidth) - 10) / 2;

		params = 'width='+calcuWidth+', height='+calcuHeight+', top='+topBottom+', left='+leftRight+', scrollbars=yes, fullscreen=yes, location=0';

		newwin=window.open(url,'StudentDetail', params);
		if (window.focus) {newwin.focus();}
		return false;
	}
</script>
<form name= "voform" method="post" action="http://asp.4nb.co.kr/cubictalk/login2.asp" target="_blank">
	<input type="hidden" name="UserName" value="<?=$loginInfo['tea_name'];?>"> 
	<input type="hidden" name="Webid" value="<?=$loginInfo['mb_id'];?>"> 
	<input type="hidden" name="Position"> 
	<input type="hidden" name="StrLang">
	<input type="hidden" name="Dept"> 
	<input type="hidden" name="Userlevel"> 
	<input type="hidden" name="Roomtitle"> 
	<input type="hidden" name="Runmethod">
	<input type="hidden" name="AlertEndMinutes"> 
	<input type="hidden" name="EndTime">
</form>
<div class = "content">
<form name = "form1" id = "form1" method = "post">
	<input type = "hidden" name = "sendsms" value = "0">
	<input type = "hidden" name = "userId" value = "0">
	<div id = "ex-description">
		<?	$teacher_name = '';
			if ( isset($TeacherInfo) && count($TeacherInfo) > 0 ) {
				$TeacherInfo = $TeacherInfo['Teacher']['tea_name'];
			}
		?>
		<span class = "ex-description">TEACHER : <font color = "#FF6600"><?=$TeacherInfo;?></font></span>
		<?	$teacherSched = 0;
			if ( isset($TeacherSched[0][0]['TeacherSched']) && $TeacherSched[0][0]['TeacherSched'] > 0 ) {
				$teacherSched = $TeacherSched[0][0]['TeacherSched'];
			};
			$teacherSched = $teacherSched;
			$teacherClass = count($TeacherClass);
			$rate = round ((($teacherClass / $teacherSched) * 100)) . '%';
		?>
		<span class = "ex-description">SCHEDULE : <font color = "#FF6600"><?=$teacherSched;?></font></span>
		<span class = "ex-description">CLASS : <font color = "#FF6600"><?=$teacherClass;?></font></span>
		<span class = "ex-description">RATE : <font color = "#FF6600"><?=$rate;?></font></span>
	</div>
	<table class = "info_tbl table-striped" border = "0" cellpadding = "5" cellspacing = "0" width = "620" style = "border-collapse:collapse;">
		<thead>
			<tr>
				<th colspan = "2">SCHEDULE</th>
				<th colspan = "2">INFO</th>
				<th>PAYPLAN</th>
				<th colspan = "3">SETTINGS</th>
			</tr>
		</thead>
		<tbody>
			<?	$i = 1;
				$tag = "";
				foreach($TeacherClass as $keyTC => $tc) {
					$user_id = $tc['User']['id'];
					if ( strlen(trim($tc['User']['hp'])) > 0 ) {
						$tag = "onclick = \"sms_Message($user_id)\" ";
			?>
			<input type = "hidden" name = "userMbId<?=$tc['User']['id'];?>" value = "<?=$tc['User']['mb_id'];?>">
			<input type = "hidden" name = "userHp<?=$tc['User']['id'];?>" value = "<?=$tc['User']['hp'];?>">
			<input type = "hidden" name = "userName<?=$tc['User']['id'];?>" value = "<?=$tc['User']['name'];?>">
			<input type = "hidden" name = "userNickName<?=$tc['User']['id'];?>" value = "<?=$tc['User']['nick_name'];?>">
			<?		} else {
						$tag = "onclick = \"sms_Message('alert')\" ";
					}
			?>
			<tr>
				<th><span style = "color:#FF0000;"><?=sprintf('%02d',($i++));?></span></th>
				<th>
					<span><?=date('Y-m-d', strtotime($tc['Lesson']['reserve_time']));?></span><br />
					<span style = "font-size:11px;">KO :</span> <font color = "#FF6600"><?=date('H:i', strtotime($tc['Lesson']['reserve_time']));?></font><br />
					<span style = "font-size:11px;">PH :</span> <font color = "#FF6600"><?=date('H:i', strtotime($tc['Lesson']['reserve_time'].'-1 hour'));?></font>
				</th>
				<th>
					<span style = "font-size:11px;">NAME :</span> <a href="javascript:go_lesson_history(<?=$tc['Lesson']['user_id']?>)"><?=$tc['User']['nick_name'];?></a><br />
					<?	$contact_value = "";
						if($tc['User']['country']=='K'){
                      		$contact_value = '+82';
                      	}else if($tc['User']['country']=='J'){
                      		$contact_value = '+81';
                      	}
                    ?>
					<span style = "font-size:11px;">SKYPE :</span> <?=$tc['User']['skype_id'];?><br />
					<span style = "font-size:11px;">TEL :</span> <?=( isset($tc['User']['tel']) && $tc['User']['tel'] != 'null' ) ? $contact_value.$tc['User']['tel'] : $contact_value ;?><br />
					<span style = "font-size:11px;">HP :</span> <?=( isset($tc['User']['hp']) && $tc['User']['hp'] != 'null' ) ? $contact_value.$tc['User']['hp'] : $contact_value ;?><br />
					<span style = "font-size:11px;">AGE :</span> <?=$tc['User']['age'];?> &emsp;
					<span style = "font-size:11px;">SEX :</span> <?=$tc['User']['sex'];?><br />
					
					<?	$type_class = '';
						if ( $tc['User']['how_class'] == 'S' || $tc['User']['how_class'] == 'H' ) {
							$type_class = 'SKYPE CLASS';
						} else if( $tc['User']['how_class'] == 'V' ){
                      		$type_class = 'VIDEO CLASS';
                      	}else if( $tc['User']['how_class'] == 'T' ){
                      		$type_class = 'TELEPHONE CLASS';
                      	}
                    ?>
                    <span class = "type-class" style = "font-size:11px; color:#FF6600;"><?=$type_class;?></span>
				</th>
				<th>
					<a class = "button" href="javascript:void(0)" onclick = "return popdetail(<?=$tc['User']['id'];?>)">INFO</a><br />
					<a class = "button" href="javascript:void(0)" <?=$tag;?> >SMS</a><br />
					<a href="skype:<?=$contact_value?>call"><img src="<?=c_path();?>/image/teacher_mypage/skype_call.png" style="border: none;" width="25" height="25" alt="Skype Me™!" /></a><br />
					<a href="skype:<?=$tc['User']['skype_id']?>?add"><img src="<?=c_path();?>/image/teacher_mypage/skype_add.png" style="border: none;" width="25" height="25" alt="Add me to Skype" /></a>
					<?	if ($tc['User']['how_class'] == 'V') { ?>
					<!--
					<br />
					<a href="javascript:void(0)" onclick="iframego()"><img src="<?=c_path();?>/image/buttons/v_class.png" style="border: none;" width="25" height="25" alt="Video Class" /></a>
					-->
	                <?	} else if ($tc['User']['how_class'] == 'T') { ?>
	                <br />
	                <a href="callTo://+82<?=$tc['User']['tel'];?>"><img src="<?=c_path();?>/image/buttons/t_class.png" style="border: none;" width="25" height="25" alt="Telephone Class" /></a>
	                <?	} ?>
				</th>
				<th>
					<?=$tc['User']['pay_plan2'];?>
					<br>
					<!--dave-->
					<input type = "button" onclick = "edit(<?=$tc['User']['id']?>,<?=$tc['User']['age']?>,'<?=$tc['User']['sex']?>')" value = "Videos" id = "btnEdit">
				</th>
				<th><span class = "" style = "color:#FF6600;"><?=$tc['Lesson']['lesson_status'];?></span>
				<button>Videos</button>
				</th>
				<th>
					<input type = "button" onClick = "javascript:change_status(<?=$tc['Lesson']['id']?>,'F')" value = "F">
                    <input type = "button" onClick = "javascript:change_status(<?=$tc['Lesson']['id']?>,'A')" value = "A"><br />
                    <input type = "button" onClick = "javascript:change_status(<?=$tc['Lesson']['id']?>,'E')" value = "E">
                    <input type = "button" onClick = "javascript:change_status(<?=$tc['Lesson']['id']?>,'P')" value = "P">
				</th>
				<th>
					<input type = "button" onClick = "javascript:go_change_status_common('cancel_lesson',<?=$tc['Lesson']['id']?>)" value = "C"><br />
					<input type = "button" onClick = "javascript:go_change_status_common('miss_lesson',<?=$tc['Lesson']['id']?>)" value = "M">
					
				</th>
			</tr>
			<?	} ?>
		</tbody>
	</table>
	<input type = 'hidden' name = 'search_type' value = '<?=$search_type;?>'>
    <input type = 'hidden' name = 'lesson_id' value = ''>
    <?php #pr($TeacherClass); ?>s
</form>
</div>
<?	} ?>

<script>
	// added by dave
	// 6-21-2016
	// enable edit age and sex form pop-up
	function edit(userId,age,sex){
		var route = '../../../../lessons/edit_age_sex_form/'+userId+'/'+age+'/'+sex;
		window.open(route, "", "width=300,height=180");
	}
</script>