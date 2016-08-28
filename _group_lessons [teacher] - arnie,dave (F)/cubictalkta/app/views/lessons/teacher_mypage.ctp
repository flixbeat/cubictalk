<?php include("teacher_top.php");
$cubictalkko_path = cubictalkko_path();
$cubictalkta_path = c_path();
?> 
<link rel="stylesheet" href="<?php echo $cubictalkko_path;?>/css/alertui/themes/alertify.core.css" type="text/css">
<link rel="stylesheet" href="<?php echo $cubictalkko_path;?>/css/alertui/themes/alertify.default.css" type="text/css">

<style type="text/css">
.div-container {
	font-family: "Helvetica Neue",Helvetica,Arial,sans-serif;
}
.div-container .alert {
	margin-bottom: 0;
}
.div-container .notice {
	font-weight: bold;
	font-size: 1.3em;
}
table.div-extension { border:1px solid #888; background:#BDBDBD; font-size:18px; font-weight:bold; letter-spacing:4px; }
table.div-extension tr th { padding:5px; }
table.div-extension tr th:hover { cursor:pointer; background:#848484; }
div.div-fixSched { border:2px solid #CCC; padding:5px; font-family:"Times New Roman", Times, serif; font-size:14px; font-weight:bold; color:#ff6600; }
table.div-fixSched th.div-fixSched { border:2px solid #CCC; padding:4px; }
th.div-fixSched:focus, th.div-fixSched:hover { background:#C7E1BA; color:#397249; }

td.composition div#numComposition { position:relative; }
div.composition  div#numComposition { padding-right:5px; font-family:"Times New Roman", Times, serif; font-size:25px; font-weight:bold; text-align:right; height:100%; }
div.composition { background: url('<?=c_path();?>/image/teacher_mypage/composition.png') no-repeat center; width: 100%; height: 25px; }
td.composition a { text-decoration:none; color:#FFFFFF; }
td.composition a:hover { color:#FF0000; }
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
			-moz-border-radius:2px;
			-webkit-border-radius:2px;
			border-radius:2px;
			border:1px solid #1f2f47;
			display:inline-block;
			cursor:pointer;
			color:#ffffff;
			font-family:arial;
			font-size:12px;
			padding:2px 5px;
			text-decoration:none;
			text-shadow:0px 1px 0px #263666;
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

#mid-container { display:block; position:relative; }
#mid-container {
 font-family:Arial, Helvetica, sans-serif;
 text-align:left;
 margin:0;
 left:0;
}
.font-sz-12px { font-size:12px; }
.font-sz-20px { font-size:20px; }
div.proceedNotice a { display:block; color:#09C; font-size:16px; }
div.proceedNotice a:hover { text-decoration:underline; color:#09C; }
div.proceedNotice span { color:#808080; }
div.proceedNotice span.effectFadeInOut { color:#808080; }
div.proceedNotice span.effect { color:#ff0000; }
</style>
<!-- 타이틀 -->
<table border="0" cellspacing="0" cellpadding="0">
<tr>
	<td><img src="<?=c_path()?>/image/lesson/tit_edge01.gif" border="0"></td>
	<td background="<?=c_path()?>/image/tit_bg.gif"><img src="<?=c_path()?>/image/teacher/tit.gif" border="0"></td>
	<td width="269" background="<?=c_path()?>/image/tit_bg.gif" style="font-size:11px;color:#7b7b7b;line-height:75px" align="right"></td>
	<td><img src="<?=c_path()?>/image/lesson/tit_edge02.gif" border="0"></td></tr>
</table>
<!-- /타이틀 -->

<!-- 교육과정 -->
<table border="0" cellspacing="0" cellpadding="0" width="670">
<tr><td width="100%">

<script type="text/javascript" src="http://download.skype.com/share/skypebuttons/js/skypeCheck.js"></script>
 <SCRIPT type=text/JavaScript>

  //comment入力
  function go_comment(lesson_id,user_id,update)
  {
	 location.href =  "<?=c_path();?>/comments/teacher_comment/" + lesson_id + "/"+ user_id+ "/"+ 'teacher_mypage';
  }
  //レッスンステータスをＣｈａｎｇｅする
  function change_status(lesson_id,update)
  {
	     search_type = '<?=$search_type?>';
		 location.href = "<?=c_path();?>/lessons/teacher_change_lesson_status/" + lesson_id + "/"+ update + "/" + search_type ;
   }

  //go leveltest
  function go_leveltest(user_id,lesson_id)
  {
	location.href = "<?=c_path();?>/comments/teacher_leveltest/" + user_id + "/" + lesson_id ;
  }

  //Lesson　Historyをみる
  function go_lesson_history(user_id)
  {
	 //location.href =  "<?=c_path();?>/comments/teacher_comment_history/"+ user_id+ "/" + 'teacher_mypage';
	 url =  "<?=c_path();?>/comments/teacher_comment_history/"+ user_id+ "/" + 'teacher_mypage';
	 popup_window(url);
  }

  function go_before_management(url,page)
  {

	  //alert(page);
	  go_teacher_management('today_lesson',page)
  }
  	
  //go to teacher management
  function go_teacher_management(search_type,page)
  {
	//alert(page);
	if(page != null){
		document.form1.page.value = page;
    }
	
	document.form1.search_type.value = search_type;
	document.form1.action = "<?=c_path()?>/lessons/teacher_mypage";
	document.form1.skype_id.value = null;
	document.form1.nick_name.value = null;
	document.form1.search_from.value = null;
	document.form1.search_to.value = null;
	//alert(search_type);
	document.form1.submit();
   }

  //go to teacher management
  function go_teacher_salary(search_type)
  {
	document.form1.search_type.value = search_type;
	document.form1.action = "<?=c_path()?>/lessons/teacher_salary";
	document.form1.submit();
   }

  //go to findrecords
  function go_find_records(search_type)
  {
		document.form1.action = "<?=c_path()?>/lessons/teacher_mypage_search";
		document.form1.search_type.value = search_type;
		document.form1.submit();
   }

  //go to teacher management
  function go_teacher_admin(search_type)
  {
	document.form1.search_type.value = search_type;
	document.form1.action = "<?=c_path()?>/lessons/teacher_admin";
	document.form1.submit();
   }

	//Added by Dennis
	//March 05, 2011
	//redirect page leveltests/leveltest_insert
	function leveltest_insert(user_id)
	{
		url = "<?=c_path();?>/leveltests/leveltest_insert/" + user_id  ;
		popup_window(url);
	} 
	
	function teacher_leveltest_show(user_id){
		url = "<?=c_path();?>/leveltests/teacher_leveltest_show/" + user_id  ;
		popup_window(url);
	}
	//end march 05, 2011
    
    // Added March 20, 2011
	// by Dennis 	
	function teacher_fixed_student(){
		location.href = "<?=c_path();?>/fixedstudents/teacher_fixed_student";
	}
	//end march 20, 2011
	
	//Added April 2, 2011
	//by Dennis
	function teacher_salarydynamic(){
		document.form1.action = "<?=c_path()?>/lessons/salarydynamic";
		document.form1.submit();
	
	}
	function teacher_bookmark(user_id) {
		location.href = "<?=c_path();?>/bookmarks/teacher_bookmark/" + user_id  ;
		}
	
	//end April 2, 2011
	function go_fix_schedule(){
		document.form1.action = "<?=c_path()?>/fixedschedules/fix_teacher";
		document.form1.submit();
	
	}
	
	function teacher_booksetting(user_id) {
		document.form1.action = "<?=c_path();?>/ftpbooks/index/" + user_id;
		document.form1.submit();
	}
	function go_view_user_complain() {
		location.href = "<?=c_path();?>/complains/complain" ;
	}
	function go_view_user_evaluates() {
		location.href = "<?=c_path();?>/evaluates/" ;
	}

  // 새 창
  function popup_window(url)
  {
      window.open(url);
  }

  function re()
  {
      location.reload(); //reload page
  }

  function popfield (url){
		var _top = (screen.height * 0.27);
		var _left = (screen.width * 0.35);
		params = 'width=405, height=300, top='+_top+', left='+_left+', scrollbars=yes';
		params += ', fullscreen=yes';

		newwin=window.open(url,'MyWindow', params);
		if (window.focus) {newwin.focus();}
		return false;
	}
  
  tid = setTimeout(re,3600000);
  
  var screenH = screen.height;
	var screenW = screen.width;

	function sms_Message(id){
		if ( id == 'alert' ) {
			alert("The student doesn't have any HP or a contact number.");
			return false;
		}
		
		document.forms['form1'].userId.value = id;
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
		document.forms['form1'].submit();

		return false;
	}

	function update_user_info(id,mb_id,age,gender){
	    var age2, isNumeric = /^[-+]?(\d+|\d+\.\d*|\d*\.\d+)$/;
	    gender_male = '';
	    gender_female = '';
		if(gender){
			if(gender === 'M'){
				gender_male = 'checked';
			}else if(gender === 'F'){
				gender_female = 'checked';
			}
		}
		msg = "<div class=\"div-container\"><div class=\"text-left\">"
		+ "<div><label>" + mb_id + "'s age : </label>"
		+ " <input type=\"text\" id=\"user_age\" onfocus=\"this.value = '';\""
		+ " value=\"Input number only!\"></div><div><label>Gender : </label>"
		+ " <input type=\"radio\" id=\"gender_male\" name=\"gender\" value=\"M\""+gender_male+">"
		+ " <label for=\"gender_male\">M</label> <input type=\"radio\" id=\"gender_female\""
		+ " name=\"gender\" value=\"F\""+gender_female+"> <label for=\"gender_female\">F</label></div></div></div>";

		alertify.confirm(msg,
			function(e){
				if(e){
					if(document.getElementById('gender_male').checked === true){
						gender = 'M';
					}else if(document.getElementById('gender_female').checked === true){
						gender = 'F';
					}
					if(document.getElementById('user_age')){
						age2 = document.getElementById('user_age').value;
					}
					if(isNumeric.test(age2)){
						age = age2;
					}
					$.ajax({
						type: 'POST',
						url: '<?php echo $cubictalkta_path;?>/fixedstudents/userInfo/',
						dataType: 'json',
						data:{id:id, age:age, sex:gender},
						success:function(data){
							location.reload();
							alertify.success('Ok');
						},
						error:function(message){
							alert(message);
						}
					});
				}else{
					alertify.error('Cancel');
				}
			});
		return false;
	}
</SCRIPT>
<DIV class=page_title>Reserved Classes</DIV>
	<div align="left">
		<A href="<?=get_basic_path();?>/cubicboardko/document/rules/rules.htm" target=_blank><font size='3' color='#FF0000'><b>RULES AND REGULATIONS</b></font></A><label style="font-weight:bold; color:#CCC"> | </label> 
		<A href="<?=get_basic_path();?>/cubicboardko/document/booklists/BOOKLISTS.htm" target=_blank><font size='3' color='#FF0000'><b>BOOKLISTS</b></font></A><label style="font-weight:bold; color:#CCC"> | </label>
		<A href="<?=get_basic_path();?>/cubicboardko/document/booklists/adults.htm" target=_blank><font size='3' color='#FF0000'><b>ADULTS</b></font></A><label style="font-weight:bold; color:#CCC"> | </label>
		<A href="<?=get_basic_path();?>/cubicboardko/document/booklists/junior.htm" target=_blank><font size='3' color='#FF0000'><b>JUNIORS</b></font></A><label style="font-weight:bold; color:#CCC"> | </label>
		<A href="<?=get_basic_path();?>/cubicboardko/document/teacher_guide/TEACHER'S PAGE GUIDE.htm" target=_blank><font size='3' color='#FF0000'><b>Teacher Guide</b></font></A><label style="font-weight:bold; color:#CCC"> | </label>
		<A href="<?=get_basic_path();?>/cubicboardko/document/initial-leveltest/LEVEL TEST-Initial Test.htm" target=_blank><font size='3' color='#FF0000'><b>initial-leveltest</b></font></A><label style="font-weight:bold; color:#CCC"> | </label>
		<A href="<?=get_basic_path();?>/cubicboardko/document/leveltest-report/HOW TO INPUT TEST RESULT A.htm" target=_blank><font size='3' color='#FF0000'><b>leveltest-report</b></font></A>
	</div>
	<div>
		<TABLE  border="0" cellspacing="0" cellpadding="5" style="">
			<TR>
				<TD>
					<a href="javascript:void(0);" onclick="go_teacher_management('today_lesson')"><img src="<?=c_path();?>/image/teacher_mypage/today.png" border="0" height="25" alt="Today" title="Today's Lesson"></a></TD>
				<TD>
					<a href="javascript:void(0);" onclick="go_teacher_management('tommorrow_lesson')"><img src="<?=c_path();?>/image/teacher_mypage/tomorrow.png" border="0" height="25" alt="Tomorrow Lesson" title="Tomorrow Lesson"></a></TD>
				<TD>
					<a href="javascript:void(0);" onclick="go_teacher_management('after_today_lesson')"><img src="<?=c_path();?>/image/teacher_mypage/afterToday.png" border="0" height="25" alt="After Today Lesson" title="After Today Lesson"></a></TD>
				<TD>
					<a href="javascript:void(0);" onclick="go_teacher_management('last_lesson')"><img src="<?=c_path();?>/image/teacher_mypage/lastLesson.png" border="0" height="25" alt="Last Lesson" title="Last Lesson"></a></TD>
				<TD>
					<a href="javascript:void(0);" onclick="go_fix_schedule()"><img src="<?=c_path();?>/image/teacher_mypage/fixSchedule.png" border="0" height="25" alt="Fixed Schedule" title="Fixed Schedule"></a></TD>
				<TD>
					<a href="javascript:void(0);" onclick="go_view_user_complain()"><img src="<?=c_path();?>/image/teacher_mypage/studentComplain.png" border="0" height="25" alt="Student Complain" title="Student Complain"></a></TD>
			</TR>
			<TR>
				<TD>
					<a href="javascript:void(0);" onclick="location.href='<?=c_path()?>/comments/teacher'"><img src="<?=c_path();?>/image/teacher_mypage/comments.png" border="0" height="25" alt="Comment" title="Comment"></a></TD>
			    <TD>
			    	<a href="javascript:void(0);" onclick="go_view_user_evaluates()"><img src="<?=c_path();?>/image/teacher_mypage/studentEvaluation.png" border="0" height="25" alt="Student Evaluation" title="Student Evaluation"></a></TD>
				<TD>
					<a href="javascript:void(0);" onclick="location.href='/cubictalkta/salaries/teacherSalary'"><img src="<?=c_path();?>/image/teacher_mypage/salary.png" border="0" height="25" alt="Salary" title="Salary"></a></TD>
				<TD>
					<div><a href="javascript:void(0);" onclick="location.href='<?=c_path();?>/compositions/teacherComposition/'"><img src="<?=c_path();?>/image/teacher_mypage/composition.png" border="0" height="25" alt="Composition" title="Composition"></a></div></TD>
					<?php
					if(isset($teacherProceed) && !empty($teacherProceed) && intval($teacherProceed) > 0){
					?>
					<TD class="composition">
						<div id="mid-container" class="font-sz-20px proceedNotice">
							<a href="<?php echo c_path();?>/compositions/teacherProceed/">Composition <span id="effectFadeInOut" class="effectFadeInOut effect">New</span></a></div>
					</TD>
					<?php
					}
					?>
				<TD>
				 <a href="<?php echo c_path();?>/teachers/teachersVidFile/"><img src="<?=c_path();?>/image/teacher_mypage/teachers_video_file.png" border="0" height="25" alt="Video File" title="Video File"></a>
				</TD>
				<TD>
				 <a href="<?php echo c_path();?>/lessons/testUser/"><img src="<?=c_path();?>/image/teacher_mypage/test_students.png" border="0" height="25" alt="Test Students" title="A test students class."></a>
				</TD>
			</TR>
			<TR>
			<?php
				if(isset($loginInfo) && $loginInfo['management_level'] > 1){
				?>
				
				<td colspan="2"><button type="button" class="btn btn-default btn-primary btn-md" onclick="location.href='<?php echo c_path();?>/curriculum_managements/'">Curriculum & Student Level</button></td>
				
				<?php
				}
				?>
				<td colspan="2"><button onclick = "location.href = '<?=c_path()?>/group_lessons/insert' "> Group Class Management</button></td>
			</TR>
		</TABLE>
	</div>
				
				<!-- search method add 2010-10-20 start -->
				<!-- change the 2010-11-16 to find condition -->
				<FORM id = "form1" name=form1 action=lesson_cancel_check.php method=post> 
				<input type = "hidden" name = "sendsms" value = "0">
                <input type = "hidden" name = "userId" value = "0">
				<?php 
					//pr($search_conditions);
					if(empty($search_conditions['skype_id'])){
						$skype_id = '';
					}else{
						$skype_id = $search_conditions['skype_id'];
						
					}
					
					if(empty($search_conditions['nick_name'])){
						$nick_name = '';
					}else{
						$nick_name = $search_conditions['nick_name'];
						
					}
					
					if(empty($search_conditions['search_from'])){
						$search_from = null;
					}else{
						$search_from = $search_conditions['search_from'];
					}
					
					if(empty($search_conditions['search_to'])){
						$search_to = null;
					}else{
						$search_to = $search_conditions['search_to'];
					}
				?>
				
				<!-- 2012-07-14 Levy Aviles Edit type(s){ each input type include placeholder and the border of table} -->
				
				<TABLE  border="1" cellspacing="0" cellpadding="4" style="margin-left:50px">
				<TR>
					<TD colspan=5 align=center>SELECT CONDITIONS</TD>
				</TR>
				<TR>
					<TD>  <input type='text' name ='skype_id'  placeholder="SKYPE ID.." > </TD>
					
					<TD>  <input type='text' name ='nick_name' placeholder="NICK NAME.." > </TD>
					
					<TD>  <input type='text' name='search_from' class='date-pick' placeholder="SEARCH FROM.." > </TD>
					
					<TD>  <input type='text' name='search_to' class='date-pick' placeholder="SEARCH TO.." > </TD>
					
					<TD>  <input type='button' onClick=javascript:go_find_records() value ='FIND'> </TD>
				</TR>
				</TABLE>
				
				
				<!-- search method add 2010-10-20 start --> 
				<?	if (isset($fixSched) && count($fixSched) > 0) { ?>
				<br>
				<div class="div-fixSched">
					<div><font size="5">Fixed Schedule</font></div>
					<div style="padding:5px 0 3px 0;">
						<table class="div-fixSched" border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
							<thead>
								<tr>
									<?	$a=1;
										foreach ($fixSched as $key => $sched) { ?>
									<th class="div-fixSched"><?=date("H:i A", strtotime(change_ph_time($sched["FixscheduleTeacher"]["time"],"BEHIND")));?></th>
									<?	if (($a%13) == 0)
											echo "</tr><tr>";
										$a++;
										} ?>
								</tr>
							</thead>
						</table>
					</div>
					<div class="notice" style="color:#666;">
						<font color="#ff0000">Note:</font> Even if we did not <font color="#ff0000">Open our Schedule</font> in <font color="#ff0000">MANAGE SCHEDULE</font>, <font style="text-decoration:underline;">students can reserve/booked a class in our Fixed Schedule</font>.
						<a href="javascript:void(0);" onclick="go_fix_schedule()">Fixed Schedule</a> is our opened schedule. Contact the Management regards to Fixed Schedule.</div>
				</div>
				<?	} ?>
				<br>
				<div class="div-table">
				<table class="div-extension">
					<tr>
						<th <? if (isset($_TotalExtension)) { ?> onclick="location.href='<?=c_path()?>/extendeds/';" <? } ?> > TOTAL EXTENSION[S] </th>
						<th <? if (isset($_TotalExtension)) { ?> onclick="location.href='<?=c_path()?>/extendeds/';" <? } ?>  style="background:#F2F2F2;" align="center"><?=$_TotalExtension?></th>

						<th <? if (isset($_TotalExtension)) { ?> onclick="location.href='<?=c_path()?>/homeworks/';" <? } ?> > TOTAL HOMEWORK[S] </th>
						<th <? if (isset($_TotalExtension)) { ?> onclick="location.href='<?=c_path()?>/homeworks/';" <? } ?> style="background:#F2F2F2;" align="center"><?=$_TotalHomework?></th>

						<th colspan="2" onclick="popfield('<?=c_path()?>/fields/myfield/');"  > FIELDS </th>
					</tr>
				</table>
				</div>
<br>
<div class="div-container">
	<div class="alert alert-danger" role="alert">
  		<div class="notice" style="color:#666;">
		<font color="#ff0000">Note:</font> Daily change the student's information
		(age or gender) by clicking button 'U.I'.</div>
	</div>
</div>
				<TABLE  border="0" cellspacing="0" cellpadding="0">
				<TR><TD>                 
					<!--                                                                             width="670"         -->
                  <IMG align="left" height=25 alt=レッスン予約状況 src="<?=c_path();?>/img/comment02.jpg"  >
                </TD></TR>
                
                <TR><TD>
  <div class="row">
  <table class="" border="0" cellpadding="2" cellspacing="1" width="100%" bgColor="#CCCCCC">
  <thead background="<?php echo c_path();?>/img/bg_gra_green.jpg" bgcolor="#FFFFFF">
  <tr>
   <td align="center">No.</td>
   <td align="center">Class Time</td>
   <td align="center">SkypeId</td>
   <td align="center">Nickname<br>Last Day</td>
   <td align="center">Gender<hr>Age</td>
   <td align="center" width="80">New</td>
   <td align="center">Book(s)</td>
   <td align="center">Level</td>
   <td align="center">S-Comment</td>
   <td align="center">T-Comment</td>
   <td align="center">Status</td>
   <td align="center">Change2</td>
   <td align="center">Change1</td>
   <td align="center">From</td>
  </tr>
  </thead>
  <tbody>
  <?php
  if ($lessons==null || $lessons=="") {
  ?>
  <tr>
   <td colspan="14" background="<?php echo c_path();?>/img/bg_gra_gray.jpg" bgColor="#f5f5f5"><strong>Sorry!</strong> No Reservation found.</td>
  </tr>
  <?php
  } else {
  $i = 0;
  $tag = "";
  foreach ($lessons as $lesson) { 
   $lesson_status = $lesson['Lesson']['lesson_status'];
   if(!array_key_exists($lesson_status,$show_lesson_status)){
    continue;
   }
   $tag = "";
   $user_id = $lesson['User']['id'];
   if (strlen(trim($lesson['User']['hp'])) > 0) {
    $tag = "onclick = \"sms_Message($user_id)\" ";
  ?>
  <input type="hidden" name="userMbId<?php echo $lesson['User']['id'];?>" value="<?php echo $lesson['User']['mb_id'];?>">
  <input type="hidden" name="userHp<?php echo $lesson['User']['id'];?>" value="<?php echo $lesson['User']['hp'];?>">
  <input type="hidden" name="userName<?php echo $lesson['User']['id'];?>" value="<?php echo $lesson['User']['name'];?>">
  <input type="hidden" name="userNickName<?php echo $lesson['User']['id'];?>" value="<?php echo $lesson['User']['nick_name'];?>">
  <?php
   } else {
   	$tag = "onclick = \"sms_Message('alert')\" ";
   }
  ?>
  <tr>
   <td background="<?php echo c_path();?>/img/bg_gra_gray.jpg" bgColor="#f5f5f5" align="center"><?php echo $i+=1;?></td>
   <td background="<?php echo c_path();?>/img/bg_gra_gray.jpg" bgColor="#f5f5f5" align="center">
    <?php echo date('Y-m-d', strtotime(change_ph_time($lesson['Lesson']['reserve_time'],"BEHIND")));?><br>
    <?php echo date('H:i', strtotime(change_ph_time($lesson['Lesson']['reserve_time'],"BEHIND")));?>
   </td>
   <?php 
//  2010-10-14 substring check 
   if($lesson['User']['tel'] != null && strlen($lesson['User']['tel'])> 0){
    $first_num = substr($lesson['User']['tel'],0,1);
    if($first_num == '0'){
     $lesson['User']['tel'] = substr($lesson['User']['tel'],1);
    }
   }
   $contact_value = "";
   if($lesson['User']['how_class']=='S'){
    $contact_value = $lesson['User']['skype_id']."<br>".'+82'.$lesson['User']['hp'];
   }else if($lesson['User']['how_class']=='T'){
    if($lesson['User']['country']=='K'){
     $contact_value = $lesson['User']['skype_id']."<br>".'+82'.$lesson['User']['tel'];
    }else if($lesson['User']['country']=='J'){
     $contact_value = $lesson['User']['skype_id']."<br>".'+82'.$lesson['User']['tel'];
    }
   }else if($lesson['User']['how_class']=='H'){
   	$contact_value = $lesson['User']['skype_id']."<br>".'+82'.$lesson['User']['hp'];
   }else if( isset($allTeachersLesson['User']['how_class']) && $allTeachersLesson['User']['how_class']=='H'){
    $contact_value = $allTeachersLesson['User']['hp'];
   } else if ( $lesson['User']['how_class']=='V' ) {
    $contact_value = $lesson['User']['skype_id']."<br>".'+82'.$lesson['User']['tel'];
   }
   ?>             
   <td background="<?php echo c_path();?>/img/bg_gra_gray.jpg" bgColor="#f5f5f5" align="center">
   <?php
   if($lesson['Lesson']['reservation_kinds'] == 'TM'){
   }
   else{
   if($lesson['User']['how_class'] == 'S'){
   	echo <<<html
   	<a href="skype:{$lesson['User']['skype_id']}?call">
    <img src="{$cubictalkko_path}/image/mypage/skype_call.png" border="0" width="25" height="25" alt="Skype Me™!" /></a>
    <a href="skype:{$lesson['User']['skype_id']}?add">
    <img src="{$cubictalkko_path}/image/mypage/skype_add.png" border="0" width="25" height="25" alt="Add me to Skype" /></a>
html;
   }
   elseif($lesson['User']['how_class'] == 'V'){
   	echo <<<html
   	<a href="javascript:void(0)" onclick="iframego()">
    <img src="{$cubictalkko_path}/image/buttons/v_class.png" border="0" width="25" height="25" alt="Video Class" /></a>
html;
   }// else if ($lesson['User']['how_class'] == 'T') {
   //	echo "<a href=\"callTo://+82{$lesson['User']['tel']}\">
   //	<img src=\"{$cubictalkta_path}/image/buttons/t_class.png\" border=\"0\" width=\"25\" height=\"25\" alt=\"Telephone Class via Skype\" /></a>
   //	";
   //}
   else {
   	echo <<<html
   	<a href="skype:{$lesson['User']['skype_id']}?call">
   	<img src="{$cubictalkta_path}/image/mypage/skype_call.png" border="0" width="25" height="25" alt="Skype Me™!" /></a>
   	<a href="skype:{$lesson['User']['skype_id']}?add">
   	<img src="{$cubictalkta_path}/image/mypage/skype_add.png" border="0" width="25" height="25" alt="Add me to Skype" /></a>
html;
   }
   }
   echo "<br>$contact_value";
   ?>
   </td>
   <?php
   $type_string = $lesson['Lesson']['reservation_kinds'];
   if($lesson['Lesson']['reservation_kinds'] == 'M'){
    $type_string = '(F)';
   }
   $last_day = date("Y-m-d",strtotime('-1 day', strtotime($lesson['User']['last_day'])));
   ?>
   <td background="<?php echo c_path();?>/img/bg_gra_gray.jpg" bgColor="#f5f5f5" align="center">
    <a href="javascript:go_lesson_history(<?php echo $lesson['Lesson']['user_id'];?>)"><?php echo $lesson['User']['nick_name'];?></a>
    <?php echo "<br>$type_string";?>
    <?php echo "<br>$last_day";?>
   </td>
   <td background="<?php echo c_path();?>/img/bg_gra_gray.jpg" bgColor="#f5f5f5" align="center">
		<?php echo $lesson['User']['sex'];
			  echo '<hr><div class="">'.$lesson['User']['age'].'</div>';
			  echo '<button type="button" class="btn btn-xs btn-primary" onclick="update_user_info('.$lesson['User']['id'].',\''.$lesson['User']['mb_id'].'\','.$lesson['User']['age'].',\''.$lesson['User']['sex'].'\')">U.I</button>';?>
   </td>
   <td background="<?php echo c_path();?>/img/bg_gra_gray.jpg" bgColor="#f5f5f5" align="center" width="80">
   <?php
   $strEquipment = "";
   $title="Unknown Class cause by reservation_kinds";
   $color="#666666";
//   this are the Symbol from Database LANDLINE10,MOBILE10,LANDLINE20,MOBILE20
   $telephoneMobile = array('LANDLINE10','MOBILE10','LANDLINE20','MOBILE20','TTEM3','TMEM3');
   $reservation_kinds = $lesson['Lesson']['reservation_kinds'];
   if (in_array($reservation_kinds, $telephoneMobile)) {
   	$pregLandline = preg_match('/^LANDLINE/', $reservation_kinds, $matchLandline);
   	$pregMobile = preg_match('/^MOBILE/', $reservation_kinds, $matchMobile);
   	$minutes = ""; // Minutes is either 10 or 20 maybe unknown it found to ex. LANDLINE10 -> 10, LANDLINE20 -> 20
   	if (isset($matchLandline[0]) && $matchLandline[0]=='LANDLINE') {
   	 $minutes = str_replace($matchLandline[0],"",$reservation_kinds);
   	 if (strlen($minutes) < 1) {
   	  $minutes = "00";
   	 }
   	 $strEquipment = strtolower($matchLandline[0]);
   	 $color="#666666";
   	 if ($minutes==20) {
   	  $color="#FF0000";
   	 }
   	 $minutes = "<br><span style=\"color:$color\">$minutes min.</span>";
   	 $title="Landline Class";
   	}elseif (isset($matchMobile[0]) && $matchMobile[0]=='MOBILE') {
   	 $minutes = str_replace($matchMobile[0],"",$reservation_kinds);
   	 if (strlen($minutes) < 1) {
   	  $minutes = "00";
   	 }
   	 $strEquipment = strtolower($matchMobile[0]);
   	 $color="#666666";
   	 if ($minutes==20) {
   	  $color="#FF0000";
   	 }
   	 $minutes = "<br><span style=\"color:$color\">$minutes min.</span>";
   	 $title="Mobile Class";
   	}else{
   		preg_match('(TTE|TME)', $reservation_kinds, $pregmatch);
   		$strEquipment='';
   		if(isset($pregmatch[0])){
   			$strEquipment = strtolower($pregmatch[0]);
   			if(in_array($strEquipment,array('tte','TTE'))){
   				$title="Telephone Class 3 Days a week";
   			}elseif(in_array($strEquipment,array('tme','TME'))){
   				$title="Mobile Class 3 Days a week";
   			}
   			$strEquipment=strtolower($reservation_kinds);
   		}
   	}
   ?>
   <img src="<?php echo cubictalkko_path();?>/image/icon/<?php echo $strEquipment;?>_class_icon.png" border="0" width="40" height="40" title="<?php echo $title;?>" alt="<?php echo $title;?>">
   <?php
    echo $minutes;
   }
   // User for TEST MOBILE
   elseif(in_array($reservation_kinds,array('T','TM'))){
   	// Test Skype with 20 Minutes class
   	if($reservation_kinds === 'T'){
   		$testScript = '<div>25 min.</div>';
   	}
   	// Test Telephone with 10 Minutes class
   	elseif($reservation_kinds === 'TM'){
   		$testScript = '<div>10 min.</div>';
   	}
   	echo "<img src=\"{$cubictalkko_path}/image/mypage/fix{$reservation_kinds}Days.png\" width=\"40\" height=\"40\">{$testScript}";
   }
   if($reservation_kinds === 'BONUS'){
	echo "<img src=\"{$cubictalkko_path}/image/mypage/fix{$reservation_kinds}Days.png\" width=\"40\" height=\"40\">";
   }
   ?>
   </td>
   <td background="<?php echo c_path();?>/img/bg_gra_gray.jpg" bgColor="#f5f5f5" align="center">
    <input type="hidden" name="userid<?php echo $lesson['User']['id'];?>" value="<?php echo $lesson['User']['id'];?>">

    <?php
    if (isset($UserBookmark)) {
     if (in_array($lesson['User']['id'],$UserBookmark)) {
    ?>
    <button onclick="teacher_bookmark(<?php echo $lesson['User']['id'];?>)">B</button><br>
    <?php
     } else {
      echo "No<br>";
     }
    } else {
   	 echo "No<br>";
    }
    ?>
    <button type="button" class="btn" onclick="return teacher_booksetting(<?php echo $lesson['User']['id'];?>);">S</button>
    <!-- 07-12-2012 Levy Aviles --> 
    <!-- 07-12-2012 Levy Aviles -->
   </td>
   <td background="<?php echo c_path();?>/img/bg_gra_gray.jpg" bgColor="#f5f5f5" align="center">
    <?php
    $leveltest = "";
    if (isset($leveltests[$i-1])) {
     $leveltest = $leveltests[$i-1];
    }
    ?>
    <a href="javascript:teacher_leveltest_show(<?php echo $lesson['User']['id'];?>)"><b><?php echo $leveltest;?><b></a><br>
    <a href="javascript:leveltest_insert(<?php echo $lesson['Lesson']['id'];?>)"><b><font color="#000000">INPUT</font></b></a>
   </td>
   <td  background="<?php echo c_path();?>/img/bg_gra_gray.jpg" bgColor="#f5f5f5" align="center" style="max-height:50px;y-overflow:scroll"><?php echo $lesson['Comment']['u_comment'];?></td>
   <td background="<?php echo c_path();?>/img/bg_gra_gray.jpg" bgColor="#f5f5f5" align="center">
   <?php
   $TComment = ""; 
   if($lesson['Comment']['t_comment'] == null || trim($lesson['Comment']['t_comment']) == '' || $lesson['Comment']['t_matrial'] == null || trim($lesson['Comment']['t_matrial']) == '') {
   	$TComment = "INPUT PLEASE";
   } else {
   	$TComment = "Thank You ^^";
   }
   ?>
   <a href="javascript:javascript:go_comment(<?php echo $lesson['Lesson']['id'];?>,<?php echo $lesson['Lesson']['user_id'];?>,'I')"><b><font color="#FF0000"><?php echo $TComment;?></font></b></a>                     		
   </td>
   <td background="<?php echo c_path();?>/img/bg_gra_gray.jpg" bgColor="#f5f5f5" align="center"><?php echo $lesson['Lesson']['lesson_status'];?></td>
   <?php 
//   2010-10-02 START for salary computting start
   $button_show = 'Y';
   if (strtotime('2011-08-16 02:00:00')- strtotime($lesson['Lesson']['reserve_time']) > 0){
    $button_show = 'N';
   }
//   2010-10-02 end
   $lesson_status = $lesson['Lesson']['lesson_status'];
   if($lesson_status == 'S' || $lesson_status == 'E' || $lesson_status == 'P' || $lesson_status == 'M' || $lesson_status == 'L'){
    $button_show = 'N';
   }
   ?> 
   <td background="<?php echo c_path();?>/img/bg_gra_gray.jpg" bgColor="#f5f5f5" align="center">
   <div title = "Send SMS Message if the student are absent."><button class = "button" <?php echo $tag;?> >SMS</button></div>
   <?php
   if($button_show == 'Y'){
   ?>
   <button type="button" class="btn" onclick="return change_status(<?php echo $lesson['Lesson']['id']?>,'F')">F</button><br>
   <button type="button" class="btn" onclick="return change_status(<?php echo $lesson['Lesson']['id']?>,'A')">A</button>
   <?php
   }
   ?>
   </td>
   <td background="<?php echo c_path();?>/img/bg_gra_gray.jpg" bgColor="#f5f5f5" align="center">
   <?php
   if($button_show == 'Y'){
   ?>
   <button type="button" class="btn" onclick="return change_status(<?php echo $lesson['Lesson']['id']?>,'R')">R</button><br>
   <button type="button" class="btn" onclick="return change_status(<?php echo $lesson['Lesson']['id']?>,'T')">T</button><br>
   <button type="button" class="btn" onclick="return change_status(<?php echo $lesson['Lesson']['id']?>,'L')">L</button>
   <?php
   }
   ?>
   </td>
   <td background="<?php echo c_path();?>/img/bg_gra_gray.jpg" bgColor="#f5f5f5" align="center">
   <?php
   if (isset($OriginalTeacher[$lesson['Lesson']['original_teacher_id']])) {
   ?>
   <span style="color:#000;font-size:18px;font-weight:bold;" align="center"><?php echo $OriginalTeacher[$lesson['Lesson']['original_teacher_id']];?></span>
   <?php
   }
   ?>
   </td>
  </tr>
  <?php
  }
  } // end condition of if ($lessons==null || $lessons=="") {
  ?>
  </tbody>
  </table>
  </div>
                  <!-- END conTent -->
                  <!-- 2010-02-22 hidden value add start -->
                  <input type='hidden' name='search_type' value='<?=$search_type?>'>
                  <input type='hidden' name='page' value='0'>
                  <!-- 2010-02-22 hidden value add end -->
                  
              </TD></TR>

            </TABLE></FORM>

</td></tr></table>
<script type="text/javascript">
 $(document).ready(function(){
  setInterval(function() {
   if($("#effectFadeInOut").hasClass("effectFadeInOut")){
    $("#effectFadeInOut").toggleClass("effect");
   }
  }, 1000);
 });
</script>
<script type="text/javascript" src="<?php echo $cubictalkko_path;?>/js/alertify.min.js"></script>
<script type="text/javascript">
function reset () {
	$("#toggleCSS").attr("href", "<?php echo $cubictalkko_path;?>/css/alertui/themes/alertify.default.css");
	alertify.set({
		labels : {
			ok     : "OK",
			cancel : "Cancel"
		},
		delay : 5000,
		buttonReverse : false,
		buttonFocus   : "ok"
	});
}
</script>
<?php include ('bottom.php')?>
