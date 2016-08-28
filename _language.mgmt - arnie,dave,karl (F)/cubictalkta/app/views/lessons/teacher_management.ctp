<?php include("teacher_top.php");?> 
<style type="text/css">
	div#ex-description {
		padding:5px;
		font-weight:bold;
		font-family:Arial, Helvetica, sans-serif;
	}
	div#ex-description span { padding:5px; }
	
	div.prev-x-content { display:none; }
	div.prev-x-content { position:fixed; z-index:9999; background:#FFFFFF; }
	
	div.bg-x-content {
		padding:20px 5px 20px 5px; background-color: #000000;
		display:none; position:fixed; z-index:99; width:100%; border:1px solid #FFF; top:0; left:0;
		
		filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#000000', endColorstr='#fffff'); /* for IE */
		-ms-filter: "progid:DXImageTransform.Microsoft.Alpha(Opacity=70)";
		
		background: -webkit-gradient(linear, left top, left bottom, from(#000000), to(#fff)); /* for webkit browsers */
		background: -moz-linear-gradient(top,  #000000,  #fff); /* for firefox 3.6+ */  opacity: .2; 
	}
	
	div.title { font-family:"Times New Roman", Times, serif; font-weight:bold; font-size:2em; color:#354B5E; }
	div.preview { font-family:"Times New Roman", Times, serif; font-weight:bold; font-size:0.8em; color:#2BBBD8; cursor:pointer; }
	
	div#top-x-content { border-bottom: 1px solid #CCCCCC; }
	div#top-x-content div.top-x-content { display:inline-block; }
	div.top-x-content:first-child { width:600px; padding:5px 0 5px 0; }
	div.top-x-content:last-child { width:20px;  padding:5px 0 5px 0; }
	div.bottom-x-content { padding:5px; }
	div#bottom-x-content { border-top: 1px solid #CCCCCC; }

	<?	if ( isset($TeacherClass) && count($TeacherClass) > 0 ) { ?>
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
	
	.float-left { float:left; padding-right:3px; }
	.float-right { float:right; }
	#arrangement .up { padding-bottom:2px; }
	#arrangement .down { padding-top:2px; }
	.arrow-up {
		width: 0; 
		height: 0; 
		border-left: 5px solid transparent;
		border-right: 5px solid transparent;
		border-bottom: 5px solid #E96D63;
		padding-left:2px;
	}

	.arrow-down {
		width: 0; 
		height: 0; 
		border-left: 5px solid transparent;
		border-right: 5px solid transparent;
		border-top: 5px solid #C0C0C0;
		padding-right:2px;
	}
	
	.arrow-up:hover, .arrow-up:focus {
		border-bottom: 5px solid #206BA4;
	} 
	.arrow-down:hover, .arrow-down:focus {
		border-top: 5px solid #206BA4;
	}
	<?	} ?>
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
  <!--
  //comment入力
  function go_comment(lesson_id,user_id,update)
  {
	 location.href =  "<?=c_path();?>/comments/teacher_comment/" + lesson_id + "/"+ user_id+ "/"+ update;
   }

  //レッスンステータスをＣｈａｎｇｅする
  function change_status(lesson_id,update)
  {
	 location.href = "<?=c_path();?>/lessons/teacher_change_lesson_status/" + lesson_id + "/"+ update;
   }

  //go to teacher management
  function go_teacher_salary_all(search_type)
  {
	document.form1.search_type.value = search_type;
	document.form1.action = "<?=c_path()?>/lessons/teacher_salary_all";
	document.form1.submit();
   }

  //go to search lessons 2010-12-14
  function go_search_lessons(search_type)
  {
	document.search_form.action = "<?=c_path()?>/lessons/teacher_search_lessons";
	document.search_form.submit();
   }

  //extend all users
  function go_extend_users()
  {
	document.search_form.action = "<?=c_path()?>/lessons/teacher_extend_users";
	document.search_form.submit();
   }
  
  //change teacher complete
  function go_change_status_common(search_type,lesson_id)
  {
	document.form1.search_type.value = search_type;
	document.form1.lesson_id.value = lesson_id;
	document.form1.action = "<?=c_path()?>/lessons/teacher_management";
	document.form1.submit();
   }

  //change teacher complete
  function go_change_teacher_id(index)
  {
	
	document.form1.new_teacher_id.value = document.form1.elements["changed_teacher[]"][index].value;
	alert(document.form1.elements["teacher_change_button[]"][index].disabled);
	document.form1.elements["teacher_change_button[]"][index].disabled = false;
	
 }

  //make lesson
  function go_teacher_make_lesson()
  {
	document.form1.action = "<?=c_path()?>/lessons/teacher_make_lesson";
	document.form1.submit();
   }

  //make contract
  function go_teacher_make_contract()
  {
	document.form1.action = "<?=c_path()?>/lessons/teacher_make_contract";
	document.form1.submit();
  }	

  //Lesson　Historyをみる
  function go_lesson_history(user_id)
  {
	 //location.href =  "<?=c_path();?>/comments/teacher_comment_history/"+ user_id+ "/" + 'teacher_mypage';
	 url =  "<?=c_path();?>/comments/teacher_comment_history/"+ user_id+ "/" + 'teacher_mypage';
	 popup_window(url);
  }

  //search lessons
  function go_teacher_make_lesson()
  {
	document.form1.action = "<?=c_path()?>/lessons/teacher_make_lesson";
	document.form1.submit();
   }

  //added by Dennis
  //Feb 19, 2010 
  //make statistics
  function go_teacher_statistics_search()
  {
	document.form1.action = "<?=c_path()?>/lessons/teacher_statistics_search";
	document.form1.submit();
   }
   
   //added by Dennis
  //Feb 20, 2010 
   //search teacher schdeules
  function go_teacher_schedules_search(){
  	document.form1.action = "<?=c_path()?>/lessons/teacher_schedules_search";
	document.form1.submit();
  }

  //added by Dennis
  //Feb 27, 2010 
  //make booklist
  function go_booklist()
  {
	document.form1.action = "<?=c_path()?>/booklists/";
	document.form1.submit();
   }


  //added by Dennis
  //March 15, 2011 
  //search teacher schdeules
  
  function go_fixedstudent(){
  	document.form1.action = "<?=c_path()?>/fixedstudents/teacher_created_fixedstudent2";
	document.form1.submit();
  }

  //added by Dennis
  //March 25, 2011 
  //redirect on salaries
  function go_teacher_salaries(){
  	//document.form1.action = "<?=c_path()?>/salaries/";
	//document.form1.submit();
	location.href = "<?=c_path()?>/salaries/";
  }

  //added by Dennis
  //April 14, 2011 
  //redirect on special date
  function go_special(){
  	document.form1.action = "<?=c_path()?>/special_dates/";
	document.form1.submit();
  }
  
  //added by YHLEE
  //April 05, 2011 
  //DBmanagenet
  function go_db_management(){
  	document.form1.action = "<?=c_path()?>/dbmanagements/teacher_insert_fixedtable";
	document.form1.submit();
  }

  //added by YHLEE
  //May 19, 2011 
  //DBmanagenet
  function go_fix_management(){
  	document.form1.action = "<?=c_path()?>/fixedstudents/teacher_fixstudents_manage";
	document.form1.submit();
  }

//Absent
  function go_teacher_absents(){
  	document.form1.action = "<?=c_path()?>/absents/teacher_absents";
	document.form1.submit();
  }

  //Food
  function go_teacher_foods(){
  	document.form1.action = "<?=c_path()?>/foods/teacher_foods";
	document.form1.submit();
  }

  //teacher_foodorders
  function go_teacher_foodorders(){
  	document.form1.action = "<?=c_path()?>/foodorders/index";
	document.form1.submit();
  }

//fixed_schedules
  function go_fixschedule(){
  	document.form1.action = "<?=c_path()?>/fixedschedules/fix";
	document.form1.submit();
  }
  
  //salarys**
  function go_teacher_payments(){
	document.form1.action = "<?=c_path()?>/payments/add";
	document.form1.submit();
  }

	//search fixed student
  function go_search_fixed_student()
  {
	document.form1.action = "<?=c_path()?>/fixedschedules/search_fixed_student";
	document.form1.submit();
	
  }
 
  function go_to_view_complains()
  {
	document.form1.action = "<?=c_path()?>/complains/";
	document.form1.submit();
  }

  // added by Dave
  // June 10 2016
  // redirect to language management
  function go_to_language_management(){
  	
  	document.form1.action = "<?=c_path()?>/language/";
  	document.form1.submit();
  }
  
  // 새 창
  function popup_window(url)
  {
      window.open(url);
  }  
  //-->
  
  // 달력 창
    function win_calendar(fld, cur_date, delimiter, opt)
    {
        if (!opt)
            opt = "left=50, top=50, width=240, height=230, scrollbars=0,status=0,resizable=0";
        win_open("<?=b_path()?>/bbs"  + "/calendar.php?fld="+fld+"&cur_date="+cur_date+"&delimiter="+delimiter, "winCalendar", opt);
    } 
  </SCRIPT>

 <DIV class=page_title>ALL Teacher's Reserved Classes</DIV>
                  <div align="left"> 
                  <UL>
                    <LI>Today's Reserved Time Slot
                    	Contact the student as soon as the Lesson starts.<BR><BR>
                    <LI>When the Lesson is finished, please click on F button<BR>
                    When the student is absent, please click the A button.<BR>
                    When the extends student's contract period, please click the E button.<BR>
                    When the gives student's point, please click the P button.<BR>
                  </UL>
                  </div>
				<?	if ($loginInfo["management_level"] > 1) {	?>
				<TABLE  border="1" cellspacing="0" cellpadding="4">
				<TR>
					<TD><input type='button' onClick=javascript:go_teacher_management('today_lesson') value='Today Lesson'/></TD>
					<TD><input type='button' onClick=javascript:go_teacher_management('after_today_lesson') value='After Today Lesson'/></TD>
					<TD><input type='button' onClick=javascript:go_teacher_management('last_lesson') value='Last Lesson'/></TD>
					<TD><input type='button' onClick=javascript:go_teacher_management('change_teacher') value='Change Teacher'/></TD>
				</TR>
				
				<?php if($loginInfo['management_level'] >= 2 ){  ?>
					<TR>
						<TD><input type='button' onClick=javascript:go_teacher_make_lesson() value='Make Lesson'/></TD>
						<TD><input type='button' onClick=javascript:go_teacher_make_contract() value='Make Contract'/></TD>
						<TD><input type='button' onClick=javascript:go_search_lessons() value='Search Lessons'/></TD>
						<TD><input type='button' onClick=javascript:go_extend_users() value='Extend Class'/></TD>
					</TR>
				<?php } ?>
				
				<TR>
						<!-- 
						Added by Dennis
						Feb 20, 2010 
						-->
						<TD><input type='button' onClick=javascript:go_teacher_schedules_search() value='Search Schedules'/></TD>
						<TD ><input type='button' onClick=javascript:go_teacher_statistics_search() value='Make Statistics'/></TD>
						<td><input type='button' onClick=javascript:go_booklist() value='Make Booklists'/>	</TD>
						<td ><input type='button' onClick=javascript:go_fixedstudent() value='Fixed Student'/>	</TD>
						
						<!-- end links -->
					</TR>
					
					<TR>
						<!-- 
						Added by YHLEE
						DB Management 
						-->
						<TD colspan='1'><input type='button' onClick=javascript:go_teacher_salary_all() value='teacher_salary'/></TD>
						<TD colspan='1'><input type='button' onClick=javascript:go_fix_management() value='FIX Management'/></TD>
						<TD colspan=''><input type='button' onClick='javascript:go_teacher_salaries()' value='Salaries'/></TD>
						<TD colspan='3'><input type='button' onClick='javascript:go_special()' value='Add Special Date'/></TD>
						<!-- end links -->
					</TR>
					
					<!-- 06-28-2012 levy START -->
					<TR>
						<TD><input type='button' onClick=javascript:go_teacher_absents() value='teacher_absents'/></TD>
						<TD><input type='button' onClick=javascript:go_teacher_foods() value='Foods'/></TD>
						<TD><input type='button' onClick=javascript:go_teacher_payments() value='Payments'/></TD>
						<TD><input type='button' onClick=javascript:go_teacher_foodorders() value='Food Orders'/></TD>
					</TR>
					<TR>
						<!--  TD><input type='button' onClick=javascript:go_fixed_schedules() value='Fixed Schedule'/></TD-->
						<!-- Added by Melanie <button for fix schedule> -->
						<TD><input type='button' onClick=javascript:go_fixschedule() value='Fix Schedule'/></TD>
						<!-- Added by Melanie <button for fix schedule> -->
						<TD><input type='button' onClick=javascript:go_search_fixed_student() value='Search Fixed Student'/></TD>
						<!-- Added 2012-07-25 Levy Aviles -->
						<TD><input type='button' onClick='javascript:go_to_view_complains()' value='View Complain(s)'/></TD>
						<td><input type='button' onClick="location.href='<?=c_path()?>/evaluates/search'"  value= 'Search Evaluation'/> </td>
					</TR>
					
					<TR>
						<TD><input type='button' onClick="location.href='<?=c_path()?>/comments/search_comment'" value='Searching For Comments'/></TD>
						<td><input type='button' onClick="location.href='<?=c_path()?>/fixedschedules/endingFixedschedules'"  value= 'Search ending fschedules'/> </td>
						<td><input type='button' onClick="location.href='<?=c_path()?>/evaluations/index'"  value= "teacher's evaluation"/> </td>
						<TD><input type='button' onClick="location.href='<?=c_path()?>/availables/'" value='Teachers Available Lesson'/></TD>
					</TR>
					<TR>
						<TD><input type='button' onClick="location.href='<?=c_path()?>/students/'" value='Student Informantion[s]'/></TD>
						<TD><input type='button' onClick="location.href='<?=c_path()?>/infolessons/'" value='Lesson[s] Information'/></TD>
						<TD><input type='button' onClick="location.href='<?=c_path()?>/dbmanagements/show_cancel_status/'" value='Chang_Status'/></TD>
						<TD><input type='button' onClick="location.href='<?=c_path()?>/payments/userPayment/'" value='Student Payment Info'/></TD>
					</TR>
					<!-- 06-28-2012 levy END -->
					
					<TR>
						<TD><input type='button' onClick="MyWindow=window.open('<?=c_path()?>/holidays/','MyWindow','width=900,height=500,left=30%,top=30%,scrollbars=yes,fullscreen=yes');MyWindow.focus();" value='Holiday[s]'/></TD>
						<TD><input type='button' onClick="MyWindow=window.open('<?=c_path()?>/fields/teacherfield/','MyWindow','width=900,left=30%,top=30%,height=500,scrollbars=yes,fullscreen=yes');MyWindow.focus();" value='Field[s]'/></TD>
						<TD><input type='button' onClick="MyWindow=window.open('<?=c_path()?>/incentives/','MyWindow','width=900,height=500,left=30%,top=30%,scrollbars=yes,fullscreen=yes');MyWindow.focus();" value='Incentive[s]'/></TD>
						<TD><input type='button' onClick="location.href='<?=c_path()?>/distributors/index/'" value='distributor'/></TD>
					</TR>
					
					<TR>
						<TD><input type='button' onClick="MyWindow=window.open('<?=c_path()?>/ftpbooks/newbook/','MyWindow','width=900,height=500,left=30%,top=30%,scrollbars=yes,fullscreen=yes');MyWindow.focus();" value='Insert New Book[s]'/></TD>
						<TD><input type='button' onClick="location.href='<?=c_path()?>/infoteachers/index/'" value='teacherinfo'/></TD>
						<TD><input type='button' onClick="location.href='<?=c_path()?>/benefits/'" value='BENEFIT[S]'/></TD>
						<TD><input type='button' onClick="location.href='<?=c_path()?>/schedules/'" value='Time Schedule[s]'/></TD>
					</TR>
					<TR>
						<TD><input type='button' onClick="MyWindow=window.open('<?=c_path()?>/reservekinds/lessonstatus/','MyWindow','width=900,height=500,left=30%,top=30%,scrollbars=yes,fullscreen=yes');MyWindow.focus();" value='Search Reserve Kinds [M,K,P]'/></TD>
						<TD><input type='button' onClick="location.href='<?=c_path()?>/searchs/fix/'" value='Search [F,A,R,E,S]'/></TD>
						<TD><input type='button' onClick="MyWindow=window.open('<?=c_path()?>/infocancelations/infocancelation/','MyWindow','width=900,height=500,left=30%,top=30%,scrollbars=yes,fullscreen=yes');MyWindow.focus();" value='Cancelation Info[s]'/></TD>
						<TD><input type='button' onClick="location.href='<?=c_path()?>/extendeds/'" value='Extend[s] Class Request'/></TD>
					</TR>
					<TR>
						<TD><input type='button' onClick="location.href='<?=c_path();?>/payments/payment_info/'" value='Payment Info'/></TD>
						<TD><input type='button' onClick="location.href='<?=c_path();?>/compositions/mgmtComposition/'" value='COMPOSITIONS'/></TD>
						<TD><input type='button' onClick="location.href='<?=c_path();?>/fixedschedules/fixedStudentSearch/'" value='CHANGE FIXED SCHED'/></TD>
						<TD><input type='button' onClick="location.href='<?=c_path();?>/friends/'" value='FRIENDS REQUEST'/></TD>
					</TR>
					<TR>
						<TD><input type='button' onClick="location.href='<?=c_path();?>/lessons/testUser/'" value='TEST USERS'/></TD>
						<TD><div class="div-container">
						<a class="btn btn-primary btn-sm" href="<?php echo c_path();?>/sms_messages/getSmsMessages">SMS</a></div></TD>
						<TD><input type='button' onClick="location.href='<?=c_path();?>/fixedstudents/newUser/'" value='NEW USER'/></TD>
						<TD><input type='button' onClick="PopUp_window('<?=c_path();?>/infoteachers/teacherChar/');" value='tea_char'/></TD>
					</TR>
					<TR>
						<TD><button type="button" onClick="return popup_window('<?php echo c_path();?>/courses/coursesIndex/')">Courses Composition</button></TD>
						<TD><button type='button' onClick="location.href='<?php echo c_path();?>/user_active_schedules/getDistributors/'">Distributor Users Schedule</button></TD>
						<TD><a href="<?php echo c_path();?>/leveltests_comments/leveltests_comments/index/">Leveltest Comment</a></TD>
						<td><div class="div-container"><a class="btn btn-primary btn-sm" href="<?php echo c_path();?>/fixedschedules/TeacherAvailableSched/">Search Available Sched.</a></div></td>
					</TR>
					<TR>
						<TD><div class="div-container"><a class="btn btn-primary btn-sm" href="<?php echo c_path();?>/teacher_fixed_schedules/teacher_fixed_schedules/">All Teacher Fixed Sched.</a></div></TD>
						<TD><button type='button' onClick="go_to_language_management()">Website Language Management</button></TD>
						<TD></TD>
						<td></td>
					</TR>
				</TABLE>
				<?	} ?>
				
		    <!-- search method add 2010-10-20 start -->
			 <FORM name=search_form action=lesson_cancel_check.php method=post> 
				<br>SELECT CONDITIONS
				<TABLE  border="0" cellspacing="0" cellpadding="5">
				<TR>
					<TD>
					 <div>Teacher Name:</div>
					 <!-- Select box 표시 -->
					 <div><?=$allTeachers_select?></div>
					</TD>
					
					
					<TD>
					 <div>NICK NAME:</div>
					 <div><input type='text' name ='nick_name'></div>
					</TD>
					
					<TD> 
					 <div>SKYPE ID:</div>
					 <div><input type='text' name ='skype_id'></div>
					</TD>
					
					<TD> 
					 <div>PAY PLAN:</div>
					 <div><input type='text' name ='pay_plan'></div>
					</TD>
				</TR>
				
				<TR>
					<TD>
						<div>SEARCH DATE FROM:</div>
						<div><input type='text' name='start_date' class='date-pick'></div>
					</TD>
					
					<TD>
						<div>SEARCH DATE TO:</div>
						<div><input type='text' name='last_date' class='date-pick'></div>
					</TD>
					
					<TD>
						<div>RESERVATION:</div>
						<?php
						$reservation = $GetAllReservationKinds;
						echo '<div><select style="width:6.5em" name="reservation"><option value="">ALL</option>';
						foreach($GetAllReservationKinds as $keyGetAllReservationKinds => $kGARK){
							echo '<option value="';
							echo $keyGetAllReservationKinds;
							echo '">';
							echo $kGARK;
							echo '</option>';
						}
						echo '</select></div>';
						?>
					</TD>
					
					<TD>
						<input type='button' value ='FIND' onClick=javascript:go_search_lessons() style="font-size:20px;">
					</TD>
				</TR>
				</TABLE>
			</FORM>
				<!-- search method add 2010-10-20 start -->
				<div class = "content">
				<form name = "form11" id = "form11" method = "post">
				<div id = "ex-description" style = "font-size:14px;">
					<span class = "ex-description" title = ""># M : <?=(isset($getPayPlan[0][0]['pay_plan']) && $getPayPlan[0][0]['pay_plan'] > 0 ) ? $getPayPlan[0][0]['pay_plan'] : 0 ;?></span>
					<span class = "ex-description" title = "total class for today"># CLASS : <?=(isset($totalTodayClass[0][0]['TotalClass']) && $totalTodayClass[0][0]['TotalClass'] > 0) ? $totalTodayClass[0][0]['TotalClass'] : 0;?></span>
				</div>
				
				<?	//pr($TeacherClass);
					if ( isset($TeacherClass) && count($TeacherClass) > 0 ) { ?>
				<div id = "ex-description">
					<input type = "hidden" name = "toSort" value = "TotalClass DESC">
					<table class = "info_tbl table-striped" border = "0" cellpadding = "6" cellspacing = "0" style = "border-collapse:collapse;">
						<thead>
							<tr>
							 <th>No.</th>
								<th>
									<div class = "txt-arrangement float-left" > TEACHER NAME </div>
									<div class = "txt-arrangement float-right" id = "arrangement">
										<div class = "up"><a href = "#" class = "toArrange" id = "Teacher.tea_name" title = "asc"><div id = "ASC" class = "arrow-up"></div></a></div>
										<div class = "down"><a href = "#" class = "toArrange" id = "Teacher.tea_name" title = "desc"><div id = "DESC" class = "arrow-down"></div></a></div>
									</div>
								</th>
								<th>
									<div class = "txt-arrangement float-left"> CLASS </div>
									<div class = "txt-arrangement float-right" id = "arrangement">
										<div class = "up"><a href = "javascript:void(0)" class = "toArrange" id = "TotalClass" title = "asc"><div id = "ASC" class = "arrow-up"></div></a></div>
										<div class = "down"><a href = "javascript:void(0)" class = "toArrange" id = "TotalClass" title = "desc"><div id = "DESC" class = "arrow-down"></div></a></div>
									</div>
								</th>
								<th>
									<div class = "txt-arrangement float-left"> SCHEDULE </div>
									<div class = "txt-arrangement float-right" id = "arrangement">
										<div class = "up"><a href = "#" class = "toArrange" id = "TotalSched" title = "asc"><div id = "ASC" class = "arrow-up"></div></a></div>
										<div class = "down"><a href = "#" class = "toArrange" id = "TotalSched" title = "desc"><div id = "DESC" class = "arrow-down"></div></a></div>
									</div>
								</th>
								<th>
									<div class = "txt-arrangement float-left"> RATE  </div>
									<div class = "txt-arrangement float-right" id = "arrangement">
										<div class = "up"><a href = "#" class = "toArrange" id = "TotalRate" title = "asc"><div id = "ASC" class = "arrow-up"></div></a></div>
										<div class = "down"><a href = "#" class = "toArrange" id = "TotalRate" title = "desc"><div id = "DESC" class = "arrow-down"></div></a></div>
									</div>
								</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<th class = "teacher-name " align = "left" colspan = "5">
									<font><i>NOTE: TO DISPLAY THE SCHEDULES, CLICK THE NAME OF TEACHER.</i></font>
								</th>
							</tr>
							<?php
							$i = 0;
							$rCount = 1;
							foreach	($TeacherClass as $keyTC => $tc) {
							?>
							<tr>
							  <th class = "teacher-name " align = "left"><?php echo $rCount;?></th>
								<th class = "teacher-name " align = "left">
									<a href = "javascript:void(0)" onclick = "teacherClass(<?=$tc['Teacher']['id'];?>, <?=$tc[0]['TotalClass'];?>)" alt = "<?=$tc['Teacher']['tea_name'];?>" title = "<?=$tc['Teacher']['tea_name'];?>"><?=$tc['Teacher']['tea_name'];?></a>
								</th>
								<th class = "teacher-name " align = "left"><font color = "#FF6600"><?=$tc[0]['TotalClass'];?></font></th>
								<th class = "teacher-name " align = "left"><font color = "#FF6600"><?=$tc[0]['TotalSched'];?></font></th>
								<th class = "teacher-name " align = "left"><font color = "#FF6600"><?=$tc[0]['TotalRate'].'%';?></font></th>
							</tr>
							<?php
							 $rCount++;
							}
							?>
							<tr>
								<th class = "teacher-name " align = "left" colspan = "4">
									<font><i>NOTE: TO DISPLAY THE SCHEDULES, CLICK THE NAME OF TEACHER.</i></font>
								</th>
							</tr>
						</tbody>
					</table>
				</div>
				<?	} ?>
				</form>
				</div>
				<FORM name=form1 action=lesson_cancel_check.php method=post>
				<?php if($allTeachersLessons==null || $allTeachersLessons==""){ ?>
				<?php } else { ?>
				<TABLE  border="0" cellspacing="0" cellpadding="0">
				<TR><TD>                 
                  <IMG align="left" height=25 alt=All-Teachers-Management-Schedule src="<?=c_path();?>/img/comment02.jpg" width="670" >
                </TD></TR>
                
                <TR><TD>   
                  <TABLE cellSpacing=1 cellPadding=2 width="670" 
                  bgColor=#cccccc border=0>
                    <TBODY>
                    <TR>
                    <TD background=<?=c_path();?>/img/bg_gra_green.jpg 
                      bgColor=#ffffff height=20>
                      <DIV class=style7 align=center>NO</DIV></TD>
                      
                      <TD background=<?=c_path();?>/img/bg_gra_green.jpg 
                      bgColor=#ffffff height=20>
                      <DIV class=style7 align=center>Teacher Name</DIV></TD>
                      <?php if(isset($allTeachers) && $search_type == 'change_teacher') { ?>
                      <TD background=<?=c_path();?>/img/bg_gra_green.jpg 
                      bgColor=#ffffff height=20>
                      <DIV class=style7 align=center>Change Teacher</DIV></TD>
                      <?php }?>
                      <TD background=<?=c_path();?>/img/bg_gra_green.jpg 
                      bgColor=#ffffff >
                        <DIV class=style7 align=center>LESSON-TIME</DIV></TD>
                      <TD background=<?=c_path();?>/img/bg_gra_green.jpg 
                      bgColor=#ffffff >
                        <DIV class=style7 align=center>SKYPE_ID</DIV></TD>
                      <TD background=<?=c_path();?>/img/bg_gra_green.jpg 
                      bgColor=#ffffff height=20 >
                        <DIV class=style7 align=center>NICK-NAME</DIV></TD>
                      <TD background=<?=c_path();?>/img/bg_gra_green.jpg 
                      bgColor=#ffffff height=20 >
                       <DIV class=style7 align=center>PayPlan</DIV></TD>
                      <TD background=<?=c_path();?>/img/bg_gra_green.jpg 
                      bgColor=#ffffff height=20 >
                        <DIV class=style7 align=center>SEX</DIV></TD>
                      <TD background=<?=c_path();?>/img/bg_gra_green.jpg 
                      bgColor=#ffffff height=20 >
                        <DIV class=style7 align=center>S-COMMENT</DIV></TD>
                      <TD background=<?=c_path();?>/img/bg_gra_green.jpg 
                      bgColor=#ffffff height=20>
                        <DIV class=style7 align=center>T-COMMENT</DIV></TD>
                      <TD background=<?=c_path();?>/img/bg_gra_green.jpg 
                      bgColor=#ffffff height=20>
                        <DIV class=style7 align=center>STATUS</DIV></TD>
                      </TR>
  
                      
                      
                      <?php 
                      	$i = 0;
                      	foreach ($allTeachersLessons as $allTeachersLesson) { 
                      	
                      ?>
                      <TR>
                      <!-- reserve_time -->
                      <TD background=<?=c_path();?>/img/bg_gra_gray.jpg bgColor=#f5f5f5>
                        <DIV align=center><?=$i+1?></DIV></TD>
                      <TD background=<?=c_path();?>/img/bg_gra_gray.jpg bgColor=#f5f5f5>
                        <DIV align=center><?=$allTeachersLesson['Teacher']['tea_name']?></DIV></TD>
                      <?php if(isset($allTeachers) && $search_type == 'change_teacher') { ?>
                       <TD background=<?=c_path();?>/img/bg_gra_gray.jpg bgColor=#f5f5f5>
                        <DIV align=center>
                        <select name="changed_teacher[]"  onChange="javascript:go_change_teacher_id(<?=$i?>)">
					       <?
					        foreach($allTeachers as $teacher) {
					        	$teacher_id = $teacher['Teacher']['id'];
					        	$teacher_name = $teacher['Teacher']['tea_name'];
					        	$selected='';
					        	if($teacher_id == $allTeachersLesson['Lesson']['teacher_id']){
					        		$selected='selected';
					        	}
					            echo "<option value='$teacher_id' $selected >$teacher_name</option>\n";
					        }
        					?>
        				</select>
        				<input type='button' name='teacher_change_button[]' onClick=javascript:go_change_status_common('change_teacher_complete',"<?=$allTeachersLesson['Lesson']['id']?>") value='Change Teacher' disabled/>
                      </DIV></TD>
                      <?php }?>
                      <TD background=<?=c_path();?>/img/bg_gra_gray.jpg bgColor=#f5f5f5>
                        <DIV align=center><?=change_ph_time($allTeachersLesson['Lesson']['reserve_time'],"BEHIND")?></DIV></TD>
                      <TD background=<?=c_path();?>/img/bg_gra_gray.jpg bgColor=#f5f5f5>
                      <?php 
                      //2010-07-07 substring check 
                      	if($allTeachersLesson['User']['tel'] != null && 
                      		strlen($allTeachersLesson['User']['tel'])> 0){
                      		$first_num = substr($allTeachersLesson['User']['tel'],0,1);
                      		
                      		if($first_num == '0'){
                      			$allTeachersLesson['User']['tel'] = substr($allTeachersLesson['User']['tel'],1);
                      		}
                      	}
                      ?>
                      <?	$contact_value = "";
							if( isset($allTeachersLesson['User']['how_class']) && $allTeachersLesson['User']['how_class']=='S'){
                      			$contact_value = $allTeachersLesson['User']['skype_id']."<br>".'+82'.$allTeachersLesson['User']['tel'];
                      		} else if($allTeachersLesson['User']['how_class']=='T'){
                      			if($allTeachersLesson['User']['country']=='K'){
                      				$contact_value = '+82'.$allTeachersLesson['User']['tel'];
                      			}else if($allTeachersLesson['User']['country']=='J'){
                      				$contact_value = '+81'.$allTeachersLesson['User']['tel'];
                      			}
                      		} else if( isset($allTeachersLesson['User']['how_class']) && $allTeachersLesson['User']['how_class']=='H'){
                      			$contact_value = $allTeachersLesson['User']['hp'];
                      		} else if ( isset($allTeachersLesson['User']['how_class']) && $allTeachersLesson['User']['how_class'] =='V' ) {
                      			$contact_value = $allTeachersLesson['User']['skype_id']."<br>".'+82'.$allTeachersLesson['User']['tel'];
                      		}
                      ?>
                       <DIV align=center>
                       		<a href="skype:<?=$contact_value?>call"><img src="<?=c_path();?>/image/teacher_mypage/skype_call.png" style="border: none;" width="25" height="25" alt="Skype Me™!" /></a>
                       		<a href="skype:<?=$allTeachersLesson['User']['skype_id']?>?add"><img src="<?=c_path();?>/image/teacher_mypage/skype_add.png" style="border: none;" width="25" height="25" alt="Add me to Skype" /></a>
                       		<?	if ($allTeachersLesson['User']['how_class'] == 'V') {
	                        //	} else { ?>
	                        <a href="javascript:void(0)" onclick="iframego()"><img src="<?=c_path();?>/image/buttons/v_class.png" style="border: none;" width="25" height="25" alt="Video Class" /></a>
	                        <?	} else if ($allTeachersLesson['User']['how_class'] == 'T') { ?>
	                        <a href="callTo://+82<?=$allTeachersLesson['User']['tel'];?>"><img src="<?=c_path();?>/image/buttons/t_class.png" style="border: none;" width="25" height="25" alt="Telephone Class" /></a>
	                        <?	} ?>
	                        <hr>
	                        <?=$contact_value?>
                       </DIV>
                       </TD>
                       
                       <TD background=<?=c_path();?>/img/bg_gra_gray.jpg bgColor=#f5f5f5>
                       <DIV align=center><a href="javascript:go_lesson_history(<?=$allTeachersLesson['Lesson']['user_id']?>)"><?=$allTeachersLesson['User']['nick_name']?></a></DIV></TD>
                      
                      <TD background=<?=c_path();?>/img/bg_gra_gray.jpg bgColor=#f5f5f5>
                      	<!-- pay plan -->
                      	<?php $pay_plan =$allTeachersLesson['User']['pay_plan'];
                      			if($pay_plan == 'M'){
                      				echo("<DIV align=center><font color='#FF0000'>$pay_plan</font></DIV>");
                      			}else{
                      				echo("<DIV align=center><font color='#000000'>$pay_plan</font></DIV>");
                      			}	
                      	?>
                        
                        
                        </TD>
                        
                      <TD background=<?=c_path();?>/img/bg_gra_gray.jpg bgColor=#f5f5f5>
                        <DIV align=center><?=$allTeachersLesson['User']['sex']?></DIV></TD>
                      <TD width=100 background=<?=c_path();?>/img/bg_gra_gray.jpg bgColor=#f5f5f5>
                        <DIV align=center><?=$allTeachersLesson['Comment']['u_comment']?></DIV></TD>
                      <TD background=<?=c_path();?>/img/bg_gra_gray.jpg bgColor=#f5f5f5>
                      <!-- 先生のコメントがない場合 -->
                        <DIV align=center>
                         <?php if($allTeachersLesson['Comment']['t_comment'] == null || $allTeachersLesson['Comment']['t_comment'] == '') { ?>
                        <input type="button" onClick="javascript:go_comment(<?=$allTeachersLesson['Lesson']['id']?>,<?=$allTeachersLesson['Lesson']['user_id']?>,'I')" value="w">
                       <!-- 先生のコメントがある場合 -->   
                         <?php } else{ ?>
                         <input type=button onClick="javascript:go_comment(<?=$allTeachersLesson['Lesson']['id']?>,<?=$allTeachersLesson['Lesson']['user_id']?>,'U')" value="u">
                        <?php } ?>
                        </DIV></TD>
                      <!-- レッスンステータス -->
                       
                      <TD width=100 background=<?=c_path();?>/img/bg_gra_gray.jpg bgColor=#f5f5f5>
                        <DIV align=center><?=$allTeachersLesson['Lesson']['lesson_status']?> 
                        <input type=button onClick="javascript:change_status(<?=$allTeachersLesson['Lesson']['id']?>,'F')" value="F">
                        <input type=button onClick="javascript:change_status(<?=$allTeachersLesson['Lesson']['id']?>,'A')" value="A">
                        <input type=button onClick="javascript:change_status(<?=$allTeachersLesson['Lesson']['id']?>,'E')" value="E">
                        <input type=button onClick="javascript:change_status(<?=$allTeachersLesson['Lesson']['id']?>,'P')" value="P">
                        </DIV>
                        
                        <input type=button onClick="javascript:go_change_status_common('cancel_lesson',<?=$allTeachersLesson['Lesson']['id']?>)" value="C">
                        <input type=button onClick="javascript:go_change_status_common('miss_lesson',<?=$allTeachersLesson['Lesson']['id']?>)" value="M">
                      </TD>
                        
                       </TR>
                      	<?php 
                      	$i++;
                      	} ?>
                      </TBODY></TABLE>
                  <P>&nbsp;</P>
                  
                  
              </TD></TR>
            </TABLE>
            <?php }  ?>
            	  <!-- 2010-02-22 hidden value add start -->
                  <input type='hidden' name='search_type' value='<?=$search_type?>'>
                  <input type='hidden' name='lesson_id' value=''>
                  <input type='hidden' name='new_teacher_id' value=''>
                  <!-- 2010-02-22 hidden value add end -->
            </FORM>

<div id = "prev-x-content" class = "prev-x-content"></div>
<div id = "bg-x-content" class = "bg-x-content"></div>

<script type="text/javascript">
	var screenH = screen.height;
	var screenW = screen.width;
	
	function PopUp_window (url) {
		var calcuHeight = 540;
		var calcuWidth = 870;
		
		if ( screenH < calcuHeight) { calcuHeight = screenH; }
		if ( screenW < calcuWidth) { calcuWidth = screenW; }
		
		var topBottom = ((screenH - calcuHeight) - 10) / 2;
		var leftRight = ((screenW - calcuWidth) - 10) / 2;

		params = 'width='+calcuWidth+', height='+calcuHeight+', top='+topBottom+', left='+leftRight+', scrollbars=yes, fullscreen=yes, location=0';
    	
		newwin=window.open(url,'PopUpWindow', params);
		if (window.focus) {
			newwin.focus();
			this.target = 'PopUpWindow';
		}
		return false;
	}
	
	function teacherClass (id, numClass) {
		var xTopCon = '<div id="top-x-content"><div class="top-x-content title" align="left"></div> <div class="top-x-content close"><input type="button" onclick="xCloseCon()" value="x"></div></div>';
		var xBottomCon = '<div id="bottom-x-content" align="right"> <div class="bottom-x-content close" ><input type="button" onclick="xCloseCon()" value="close"></div> </div>';
		var xIframeCon = '<div id="iframe-x-content" class="iframe-x-content" style="width:650px; height:515px;" align="left"></div>';
		if ( document.getElementById('prev-x-content') ) {
			$("div#prev-x-content").empty().append( '<div class="view-x-content">' + xIframeCon + xBottomCon + '</div>');
					
			if ( document.getElementById('iframe-x-content') ) {
				var xIframeCon = '<iframe id="iframeOn" frameborder="0" width="650" height="510" ></iframe>';
				$("div#iframe-x-content").empty().append( xIframeCon );
	
				if ( document.getElementById('iframeOn') )
					document.getElementById('iframeOn').src = '<?=c_path();?>/lessons/teacherClass/'+id+'/'+numClass+'/';
			}
			document.getElementById('bg-x-content').style.display = 'block';
			document.getElementById('bg-x-content').style.height=window.innerHeight+"px";
				
			if ( document.getElementById('bg-x-content') )
				$("div.bg-x-content").css({ opacity: "0.2" });
			
			$("div#iframeOn").css({ top:parseInt(((window.innerHeight-$("div#prev-x-content").height())/2))+"px", left:parseInt(((window.innerWidth-$("div#prev-x-content").width())/2))+"px" });
			$("div#prev-x-content").css({ display:'block', width:'650px', height:'548px', border:'1px solid', top:parseInt(((window.innerHeight-$("div#prev-x-content").height())/2))+"px", left:parseInt(((window.innerWidth-$("div#prev-x-content").width())/2))+"px" });
			$("div#prev-x-content").draggable();
		}
	}
	
	function xCloseCon () {
		if ( document.getElementById('prev-x-content') ) {
			$("div#prev-x-content").empty().css({ display:'none' });
			document.getElementById('bg-x-content').style.display = 'none';
		}
	}
</script>

</td></tr></table>
<!-- /어학과정 -->

<!-- 배너 -->
<table border="0" cellspacing="0" cellpadding="0" width="652">

</table>
<!-- /배너 -->
<?	if ( isset($TeacherClass) && count($TeacherClass) > 0 ) { ?>
<script type="text/javascript">
	$("a.toArrange").click(function(){
		var tosort = $(this).attr('id') +" "+ $(this).find('div').attr('id');
		document.form11.toSort.value = tosort;
		document.form11.action = "<?=c_path();?>/lessons/teacher_management/";
		document.form11.submit();
		return false;
	});
</script>
<?	} ?>
<?php include ('bottom.php')?>
