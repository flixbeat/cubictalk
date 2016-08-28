<?php
echo $language[$session->read('lang')]['fluency']; 
include('views/common/top.php');

$loginInfo = $session->read('loginInfo');
$cubictalkko_path = c_path();
?>
<link rel="stylesheet" href="<?php echo $cubictalkko_path;?>/css/alertui/themes/alertify.core.css" type="text/css">
<link rel="stylesheet" href="<?php echo $cubictalkko_path;?>/css/alertui/themes/alertify.default.css" type="text/css">
<style type="text/css">
.container *{font-family:"Helvetica Neue",Helvetica,Arial,sans-serif;font-size:9pt}
#major:hover { padding:2px 6px 3px 6px; background: darkgray; border-radius: 5px; color: white; }

.tds:hover{
	background: #FCE0D3;
	/*background: #99FF99;*/
	box-shadow: 0px 0px 20px #F19367;
	
}
.text{
	background: darkgray;
	border-radius: 5px;
	color: white;
}

.divback {
	padding:20px 5px 20px 5px;
	display:none; position:fixed; z-index:999; width:100%; border:1px solid #FFF; top:0; left:0;
	
	filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#000000', endColorstr='#fffff'); /* for IE */
	-ms-filter: "progid:DXImageTransform.Microsoft.Alpha(Opacity=70)";
	
	background: -webkit-gradient(linear, left top, left bottom, from(#000), to(#fff)); /* for webkit browsers */
	background: -moz-linear-gradient(top,  #000,  #fff); /* for firefox 3.6+ */  opacity: .2; 
}
.divfront { display:none; }
.divfront { border:1px solid #666; -webkit-border-radius: 5px; -moz-border-radius: 5px; border-radius: 5px; padding:10px; background:#FFF; }

div.popVid { display:none; position:fixed; z-index:9999; }

div.df { background: rgb(233, 233, 233); border:1px solid rgb(204,204,204); } 

.float-left { float:left; padding:5px; }
.border { border:1px solid #808080; margin-left:2px; margin-top:2px; }
div[id*=customize-float]:after {
	content: " ";
	display: block; 
	height: 0; 
	clear: both;
}
.top-space-bottom { height:10px; }

.schedSearch {
  background: #F9F7F2;
  border: 1px solid;
  border-color: #CCCCCC;
  margin: 0;
  padding: 0;
  padding: 10px 17.3px;
}
.schedSearch img {
  vertical-align: middle;
}
.schedSearch table {
  width: 100%;
}
.schedSearch  .YrMon {
  font-size: 16px;
  font-weight: 900px;
  line-height: 18px;
  margin: 0;
  padding: 0;
  margin-bottom: 8px;
}
.dateList
,.dateList ul
,.teacherList
,.teacherList ul {
  margin: 0;
  padding: 0;
}
.dateList ul li
,.teacherList ul li {
  list-style: none;
  margin: 0;
  padding: 0;
}
.dateList ul .date-list
,.teacherList ul .teacher-list{
  float: left;
  min-height: 50px;
}
.dateList ul li a {
  border: 1px solid;
  border-bottom: 2px solid;
  border-top: 2px solid;
  display: block;
  font-size: 18px;
  font-weight: 900;
  padding: 12px 22px;
  
  text-decoration: none;
}
.dateList ul li:first-child a {
  border-color: #0099CC;
  border-left: 2px solid #0099CC;
}
.dateList ul li:last-child a {
  border-color: #0099CC;
  border-right: 2px solid #0099CC;
}
.dateList ul li a:hover {
  background: #E6E6E6;
  border-color: #0099CC;
  color: #585858;
}
.dateList ul li .a-on {
  background: #0099CC;
  color: #ffffff;
  border-color: #0099CC;
}

.teacherschedSearch {
  margin-bottom: 10px;
  visibility: hidden;
}
.teacherschedSearch .teacherList ul {
}
.teacherList ul .teacher-list{
  background-color: #FFFFFF;
  border: 1px solid #cccccc;
  padding: 5px;
}
.teacherList .teacher-info
,.teacherList .teacher-info .teacher-panel {
  margin: 0;
  padding: 0;
  position: relative;
}
.teacherList .teacher-info .teacher-panel .float-left {
  float: left;
}
.teacher-panel .panel-img
,.teacher-panel .panel-name
,.teacher-panel .panel-time {
  padding: 2px;
}
.teacher-panel .panel-img img {
  width: 100%;
}
.teacher-panel .panel-name a {
  cursor: pointer;
  text-decoration: none;
}
.teacher-panel .panel-time a {
  background-color: #428bca;
  border: 1px solid;
  border-color: #357ebd;
  color: #fff;
  
  float: left;
  margin: 0;
  margin-left: 2px;
  margin-top: 2px;
  padding: 1px;
  text-decoration: none;
  text-align: center;
  width: 37px;
}
.teacher-panel .panel-time .off-link {
  background-color: #BFBFBF;
  border-color: #A7A7A7;
  color: #fff;
  cursor: not-allowed;
}
.teacher-panel .panel-time .off-link:hover {
  background-color: #838383;
  border-color: #6E6E6E;
  color: #fff;
}
.teacher-panel .panel-time .on-link:hover {
  background-color: #3071a9;
  border-color: #285e8e;
  color: #fff;
}
.teacher-panel .panel-img
,.teacher-panel .panel-name
,.teacher-panel .panel-info
,.teacher-panel .panel-time {
  margin: 0;
  margin-bottom: 5px;
  text-align: center;
}
.teacherList .teacher-info .teacher-panel .onePanel {
  min-width: 94px;
  width: 94px;
}
.teacherList .teacher-info .teacher-panel .twoPanel {
  min-width: 90px;
  width: 90px;
}
.dateList
,.dateList:after
,.dateList ul:after
,.teacherList
,.teacherList:after
,.teacherList ul:after
,.teacherList .teacher-info .teacher-panel
,.teacherList .teacher-info .teacher-panel:after
,.teacher-panel .panel-time
,.teacher-panel .panel-time:after {
  content: "";
  clear: both;
  display: block;
}
</style>
<style type="text/css">
.oF-group {
  font-size: 14px;
  line-height: 24px;
  margin: 0;
  min-height: 16px;
  padding: 0;
/*   padding-top: 4px; */
}

.oF-group {
  display: block;
  position: relative;
}
.oF-group #row4 {
  margin: 0 auto;
  text-align: center;
}
.oF-group #cell4{
  display: inline-block;
/*   float: left; // For IE < IE8 */
  height: auto;
  margin: 0;
  margin-right: -4px;
  padding: 0;
  position: relative;
}
.oF-group .skyTelOption {
  background: #ffffff;
  border: 1px solid #D1D1D1;
  cursor: pointer;
  padding: 0 3px;
}
.skyTelOption label {
  -moz-user-select: none;
  background-image: none;
  cursor: pointer;
  font-size: 14px;
  font-weight: 400;
  line-height: 1.42857;
  margin-bottom: 0;
  padding: 6px 12px;
  text-align: center;
  vertical-align: middle;
  white-space: nowrap;
}
.oF-group .skyTelOption:hover {
  background: #E6E6E6;
  border-color: #D1D1D1;
  color: #585858;
}
.oF-group .active
,.oF-group .active:hover {
  background: #0099CC;
  border-color: #0099CC;
  color: #ffffff;
}
.skyTelOption label {
  margin-bottom: 0;
}
.oF-group
,.oF-group:after
,.oF-group #row4:after {
 clear: both;
 content: "";
 display: block;
}
</style>
<?php
function calendar($year, $month, $click_day = null) {
  //月末
  $l_day = date("j", mktime(0, 0, 0, $month + 1, 0, $year));
  $fomatted_month = sprintf('%02d',$month);
  //月末分繰り返す
  $formatted_ym = $year.$fomatted_month;
  
  $formatted_ym_b = date("Y", mktime(0, 0, 0, $month , 0, $year)).  sprintf('%02d',date("n", mktime(0, 0, 0, $month , 0, $year)))."01";
  $formatted_ym_a = date("Y", mktime(0, 0, 0, $month+2 , 0, $year)).  sprintf('%02d',date("n", mktime(0, 0, 0, $month+2 , 0, $year)))."01";
  

  
  //change local and server
  
  //$cubictalkko_path = 'http://127.0.0.1/cubictalkko';
  //$cubictalkko_path = 'http://www.cubictalk.com/cubictalkko';
  $cubictalkko_path = c_path();
    
  //初期出力
  $tmp = <<<EOM
 <TABLE border=0 cellSpacing=1 cellPadding=1 width="100%" bgColor=#cccccc>
 	<COLGROUP>
    <COL span=7 width=40>
    <TBODY>
    	<TR>
 			<TD bgColor=#f9f9f9 colSpan=7>
            	<TABLE border=0 cellSpacing=0 cellPadding=2 width="100%">
                <TBODY>
                <TR>
                	<TD>
                    	<DIV align=left>
  						<A href="{$cubictalkko_path}/teachers/index/{$formatted_ym_b} ">
  						<IMG border=0 alt=지난달 src="/cubictalk/img/arrow03_l.gif"></A>                               
                        </DIV>
                    </TD>
                    <TD>
                    	<DIV align=center><SPAN style="FONT-SIZE: 15px"><STRONG>
  							{$year}년{$fomatted_month}월
							</STRONG></SPAN>
						</DIV>
					</TD>
                     <TD>
                     	<DIV align=right> 
							<A href="{$cubictalkko_path}/teachers/index/{$formatted_ym_a}">
							<IMG border=0 alt=다음달 src="{$cubictalkko_path}/img/arrow03_r.gif"></A>                                
                         </DIV>
                      </TD>
                </TR></TBODY></TABLE>
          </TD>
          </TR>
                                
           <TR>
           	<TD bgColor=#f5f5f5><DIV align=center>일</DIV></TD>
            <TD bgColor=#f5f5f5><DIV align=center>월</DIV></TD>
            <TD bgColor=#f5f5f5><DIV align=center>화</DIV></TD>
            <TD bgColor=#f5f5f5><DIV align=center>수</DIV></TD>
            <TD bgColor=#f5f5f5><DIV align=center>목</DIV></TD>
            <TD bgColor=#f5f5f5><DIV align=center>금</DIV></TD>
            <TD bgColor=#f5f5f5><DIV align=center>토</DIV></TD>
          </TR>
EOM;
  
  
  for ($i = 1; $i < $l_day + 1;$i++) {
  	$formatted_day = sprintf('%02d',$i);
  	$formatted_ymd = $formatted_ym.$formatted_day;
	
  	//色判定
  	$bgcolor[0] ="#FFFF99";
  	$bgcolor[1] ="#fff4f9";
  	$bgcolor[2] ="#f5fafa";
  	$bgcolor[3] ="#ffffff";
  	
  	if($click_day!=null && array_key_exists($formatted_ymd,$click_day)){
  		$bgcolor[0] ="#FFCC00";
  	 	$bgcolor[1] ="#FFCC00";
  	 	$bgcolor[2] ="#FFCC00";
  	 	$bgcolor[3] ="#FFCC00";
  	}
  	
    //曜日の取得
    $week = date("w", mktime(0, 0, 0, $month, $i, $year));
    //曜日が日曜日の場合
    if ($week == 0) {
      $tmp .= "  <TR>\n";
    }
    //1日の場合
    if ($i == 1) {
      $tmp .= str_repeat("    <td  bgColor=#fff4f9>&nbsp;</td>\n", $week);
    }
    if ($i == date("j") && $year == date("Y") && $month == date("n")) {
      //現在の日付の場合
      $tmp .= "    <td bgColor={$bgcolor[0]}><DIV align=\"center\"><STRONG><A href=\"javascript:day_click({$formatted_ymd});\">{$i}</A></STRONG></DIV></td>\n";
    } else {
      //現在の日付ではない場合
      if($week == 0){
      	 $tmp .= "    <td  bgColor={$bgcolor[1]}><DIV align=\"center\"><STRONG><A href=\"javascript:day_click({$formatted_ymd});\">{$i}</A></STRONG></DIV></td>\n";
      }elseif($week == 6){
      	$tmp .= "    <td  bgColor={$bgcolor[2]}><DIV align=\"center\"><STRONG><A href=\"javascript:day_click({$formatted_ymd});\">{$i}</A></STRONG></DIV></td>\n";
      }
      else{
      	$tmp .= "    <td bgColor={$bgcolor[3]}><DIV align=\"center\"><STRONG><A href=\"javascript:day_click({$formatted_ymd});\">{$i}</A></STRONG></DIV></td>\n";
      }
    }
    
    
    //月末の場合
    if ($i == $l_day) {
      $tmp .= str_repeat("    <td bgColor=#fff4f9>&nbsp;</td>\n", 6 - $week);
    }
    //土曜日の場合
    if($week == 6) {
      $tmp .= "  </TR>\n";
    }
  }
  $tmp .= "</TBODY></TABLE>\n";
  return $tmp;
}
?>
<div class="container">
<div class="div-container">
	<div class="text-left"><img src="<?php echo $cubictalkko_path;?>/image/title/teachers-title-ko.png" border="0"></div>
</div>

<!-- 교육과정 -->
<table border="0" cellspacing="0" cellpadding="0" width="670">
             	
             	<?php
             	if($checkPayment=='OK'){ ?> 
                 <?php }else if($checkPayment=='TEST'){ ?> 
                   <DIV><STRONG>현재 테스트유저로 로그인하셨읍니다.
                   		실제 예약시에는 고객님의 아이디로 로그인을 하시고 [내강의실]에 들어가셔야 합니다.</STRONG></DIV>
                  <?php }else{?>
                   <DIV class=mypage_title><STRONG><?=$loginInfo['User']['name']?>님 수업기기간이 끝나셨습니다.^^</STRONG></DIV>
                  <?php } ?> 
                  <TABLE cellSpacing=0 cellPadding=0 border=0> 
                    <TBODY>
                    <!-- 
                    <TR> 
                      <TD><IMG height=25 alt=강사검색 src="<?php echo $cubictalkko_path;?>/img/tbl-teacher-search-ko.png" width=670></TD></TR>
                       -->
                    <TR> 
                      <TD>
<div class="schedSearch" style="margin: 0; margin-bottom: 10px;">
	<?php
	// please make it Global Variables
	$date = array('(일)','(월)','(화)','(수)','(목)','(금)','(토)');
	
	$date_today = date('Ymd');
	$old_year = array();
	if(isset($express_day)){
	}
	else $express_day = $date_today;
	$date_arr = array();
	
	$year = date('Y',strtotime($express_day));
	$month = date('m',strtotime($express_day));
	$Year_Month = "{$year} 년 {$month} 월";
	echo <<<html
	<p class="YrMon">{$Year_Month}</p>
html;
	?>
	
	<div class="dateList" style="margin-bottom: 8px;">
	<ul>
	<?php
	$ninethdays = date('Ymd',strtotime('+9 days'));
	for($i = $date_today; $i < $ninethdays;){
		$indx = date('w',strtotime($i));
		$style = '';
		if($indx == 0) {
			$style = ' color: #ff0000;';
		}
		elseif($indx == 6) {
			$style = ' color: #F781F3;';
		}
		$day_name = '<span style="display: block; margin-bottom: 4px; ' . $style . '"> ' . $date[$indx] . ' </span>' . date('d',strtotime($i));
		$class = '';
		if(isset($express_day) && $i == $express_day){
			$class = " a-on ";
		}
		$date_arr[] = "$i";
		echo <<<html
		<li class="date-list"><a class=" {$class} " id="" href="javascript:void(0)" onclick="teaSearch(this,{$i})">{$day_name}</a></li>
html;
		$i = date('Ymd',strtotime($i . '+1 day'));
	}
	?>
	</ul>
	</div>
	<div class="" style="margin-bottom: 8px;">
		<?php echo $form->create('Teacher', array('id'=>'formTeacher','name'=>'formTeacher','action'=>'')); ?>
			<div class="" style="margin-bottom: 8px;">
			<div class="" style="text-align: center;">
				<label style="margin-right: 10px;">시간</label>
				<?php
				$options_time = array();
				$options_time[' '] = "전부표시";
				for($i=0;$i<25;$i++){
					$i = sprintf('%02d',$i);
					$j = sprintf('%02d',$i+1);
					$formatted_time = sprintf('%s：00～%s：00',$i,$j);
					$options_time[$i.$j] = $formatted_time;
				}
				//$selected = 0;
				echo $form->select('cal_time',$options_time, $selected['time']);
				?>
				<a href="javascript:void(0)"onclick="teaSearch(this)">
				<img src="<?php echo $cubictalkko_path;?>/img/btn_serch.gif" border="0" width="" height="" alt="이조건으로검색"></a>
				<a href="javascript:void(0)" onclick="popVid()">
                <img src="<?=b_path();?>/image/manual/step_video.png" border="0" width="" height="" alt="Step Video"></a>
			</div>
			<input type="hidden" id="searchDate" name="searchDate" value="">
			<input type="hidden" id="searchParams" name="searchParams" value="">
		<?php
		echo $form->input('cal_date', array('type'=>'hidden','value'=>$express_day));
		echo $form->input('express_day', array('type'=>'hidden','value'=>$express_day)); 
		echo $form->end();
		?> 
	</div>
</div>
                      </TD></TR></TBODY></TABLE> 

                  <TABLE cellSpacing=1 cellPadding=10 width=670 bgColor=#cccccc border=0> 
                    <TBODY> 
                    <TR> 
                      <TD bgColor=#f9f7f2> 
                        <DIV class=cont_title>
<?php
echo <<<html
<form class="" id="" method="post">
<div class="div-container">
<div>【자유예약가능한 강사일람】  ~ 사진클릭->전체 스케줄 확인 및 예약 :  시간클릭->클릭시간 예약</div>
<div class="oF-group">
<div id="row4">
html;
$kinds = array();
if(isset($loginInfo['User'])
		&& $loginInfo['User']['pay_plan'] === 'M'){
echo <<<html
<div id="cell4">
<div class="skyTelOption active"><input type="radio" id="c_skype" onclick="skyTelephone(this)" name="class_in_use" value="skype" checked>
<label for="c_skype"> Skype </label></div></div>

<div id="cell4"><div style="margin-right: 10px;">
<div class="skyTelOption "><input type="radio" id="c_telephone" onclick="skyTelephone(this)" name="class_in_use" value="telephone">
<label for="c_telephone"> Telephone </label></div></div></div>
html;
}
else{

echo <<<html
<div id="cell4"><div style="margin-right: 10px;"><label>결재플랜</label></div></div>
html;

$checked = '';
if(isset($getPaymentAllNotFix['userPayment'])
		&& count($getPaymentAllNotFix['userPayment']) > 0){
	$checked = ' checked ';
echo <<<html
<div id="cell4"><div style="margin-right: 10px;"><input type="radio" id="payplan1" name="my_Plan" value="1" {$checked}>
<select class="" id="change_payplan">
html;
foreach($getPaymentAllNotFix['userPayment'] as $key_getPaymentAllNotFix => $kgpanf){
	$p_name_script = $kgpanf['Payment']['goods_name'];
	if(isset($kgpanf['DictionaryDetail']['express_name_ko'])
			&& strlen(str_replace(' ','',$kgpanf['DictionaryDetail']['express_name_ko'])) > 0){
		$p_name_script = $kgpanf['DictionaryDetail']['express_name_ko'];
	}
	elseif(isset($kgpanf['DictionaryDetail']['express_name_en'])
			&& strlen(str_replace(' ','',$kgpanf['DictionaryDetail']['express_name_en'])) > 0){
		$p_name_script = $kgpanf['DictionaryDetail']['express_name_en'];
	}
	
	if($kgpanf['Payment']['goods_name'] == 'P'){
		$go_subs2 = $const['point_reservation'];
	}
	else{
		$go_subs2 = $const['koma_reservation'];
	}
	$kinds[$kgpanf['Payment']['id']] = $go_subs2;
	echo <<<html
<option value="{$kgpanf['Payment']['id']}">{$p_name_script}</option>
html;
}
echo <<<html
</select></div></div>
html;
$checked = '';
}
else {
	$checked = ' checked ';
}
echo <<<html
<div id="cell4"><div style="margin-right: 10px;"><input type="radio" id="payplan2" name="my_Plan" value="0" {$checked}>
<label for="payplan2"> 포인트 사용하기 </label></div></div>
html;
}

$checked = '';
if($loginInfo['User']['teacher_schedule_setting'] == 1){
	$checked = ' checked ';
}
echo <<<html
<div id="cell4">
<div style="margin-right: 10px;">
<input type="hidden" name="modify" value="1">
<input type="checkbox" name="teacher_schedule_setting" value="1" {$checked}>
<button type="submit" class="btn btn-primary btn-md"> 가능한 시간만 보기 세팅 </button></div>
</div>
html;

echo <<<html
</div>
</div>
</div>
</form>
html;
$checked = '';
?>
                        </DIV>
                        <TABLE height=30 cellSpacing=0 cellPadding=0 border=0> 
                          <TBODY> 
                          <TR>
                          <?php
                          	$search_conditons = $session->read('search_coditions'); 
                            if($checkPayment=='OK'){ 	
                          ?>
                           
                            <TD><STRONG>검색내용　： </STRONG>
                            	<?php if($search_conditons != null){ ?>
                            		 <span id="search" style="font-size:9px"> <?=$search_conditons['start_day']?></span> ~ 
                            		 <span id="search2" style="font-size:9px"><?=$search_conditons['end_day']?></span>
                            	<?php } else { ?>
                            		 <span id="search" style="font-size:9px">강사님 사진을 클릭하셔서 강사님 동영상  스케줄확인 및 자유수업예약이 가능합니다. 고정수업은 [<a href="<?php echo c_path();?>/fixedschedules/index1/">고정수업</a>]메뉴를 이용해주세요</span> 
                            		 <span id="search2" style="font-size:9px"></span>
                            	<?php }?>
                            		
                            	
                            </TD>
                           <?php }else {?>
                           <TD style="font-size:14px"><STRONG>강사님 사진을 클릭한 후 스케줄 및 소개동영상을 보실 수 있습니다.</STRONG></TD>
                           <?php }?>
                            </TR>
                            
                           <TR>  
                            <TD align='left'>
<?php
if(isset($getAllTeacherAvailableLesson['teaAvailable']) && count($getAllTeacherAvailableLesson['teaAvailable']) > 0){
?>
<div id="teacherschedSearch" class="teacherschedSearch">
	<div class="teacherList">
	<?php
	$date_today = date('YmdH00');
	$group_tea_id = array();
	$teacher_name = '';
	$teacher_time = '';
	$prev_tea = '';
	$nxt_tea = '';
	$time_c = '';
	$echo = false;
	echo <<<html
<ul id="teacherListWrap">
html;
	foreach($getAllTeacherAvailableLesson['teaAvailable'] as $key_getAllTeacherAvailableLesson => $kgatal){
		$str_dtatime = date('YmdHi',strtotime($kgatal['Available_lesson']['available_time']));
		$str_time = date('H:i',strtotime($kgatal['Available_lesson']['available_time']));
		if(in_array($kgatal['Available_lesson']['teacher_id'],$group_tea_id)){
			$time_ready = true;
		}
		else $time_ready = false;
		
		
		
		if(!$time_ready){
			if($echo){
				echo <<<html
<li id="test{$teacher_id}" class="teacher-list">
					<div class="teacher-info">
						<div class="teacher-panel">
							<div class="float-left onePanel">
								<div class="panel-img">{$teacher_img}</div>
								<div class=" text-center"><a href="{$cubictalkko_path}/teachers/reserve/{$teacher_id}/" style="text-decoration: none;">{$teacher_name}</a></div>
								<div class="text text-center" style="margin-top: -2px;">{$teacher_time}</div>
								<div class="panel-info"></div>
							</div>
							<div class="float-left twoPanel">
								<div class="panel-time">{$time_c}</div>
							</div>
						</div>
					</div>
				</li>
html;
			}
			$teacher_id = $kgatal['Available_lesson']['teacher_id'];
			$group_tea_id[] = $kgatal['Available_lesson']['teacher_id'];
			$teacher_name = $kgatal['Teacher']['tea_name'];
			$teacher_time = $kgatal['Teacher']['tea_time'];
			$teacher_img = <<<html
			<a href="{$cubictalkko_path}/teachers/reserve/{$teacher_id}/"><img src="{$cubictalkko_path}{$kgatal['Teacher']['img_file']}" onerror="goTeacherImgDefault(this)" width="70" height="80"></a>
html;
			$time_c = '';
			$attrib = " class=\"off-link\" ";
			if($date_today < $str_dtatime){
				$attrib = " class=\"on-link\" href=\"javascript: void(0)\" onclick=\"goReserveConfirm($teacher_id,{$str_dtatime})\" ";
			}
			$time_c .= <<<html
		<a id="" {$attrib}>{$str_time}</a>
html;
			$echo = true;
		}
		else {
			$attrib = " class=\"off-link\" ";
			if($date_today < $str_dtatime){
				$attrib = " class=\"on-link\" href=\"javascript: void(0)\" onclick=\"goReserveConfirm($teacher_id,{$str_dtatime})\" ";
			}
			$time_c .= <<<html
		<a id="" {$attrib}>{$str_time}</a>
html;
		}
	}
	echo <<<html
<li id="test{$teacher_id}" class="teacher-list">
					<div class="teacher-info">
						<div class="teacher-panel">
							<div class="float-left onePanel">
								<div class="panel-img">{$teacher_img}</div>
								<div class=" text-center"><a href="{$cubictalkko_path}/teachers/reserve/{$teacher_id}/" style="text-decoration: none;">{$teacher_name}</a></div>
								<div class="text text-center" style="margin-top: -2px;">{$teacher_time}</div>
								<div class="panel-info"></div>
							</div>
							<div class="float-left twoPanel">
								<div class="panel-time">{$time_c}</div>
							</div>
						</div>
					</div>
				</li>
html;
	$num_li = (count($group_tea_id) % 3);
	while($num_li != 0
			&& $num_li < 3) {
		$box = "box{$num_li}";
		echo <<<html
<li id="test{$box}" class="teacher-list">
					<div class="teacher-info">
						<div class="teacher-panel">
							<div class="float-left onePanel">
								<div class="panel-img"></div>
								<div class=" text-center"></div>
								<div class="text text-center" style="margin-top: -2px;"></div>
								<div class="panel-info"></div>
							</div>
							<div class="float-left twoPanel">
								<div class="panel-time"></div>
							</div>
						</div>
					</div>
				</li>
html;
		$group_tea_id[] = $box;
		$num_li++;
	}
	echo <<<html
</ul>
html;
	?>
	<form class="" id="TeacherReserveConfirm" name="TeacherReserveConfirm" method="post">
	<input type="hidden" id="t_id" name="id" value="0">
	<input type="hidden" id="t_reservation_time" name="reservation_time" value="">
	<input type="hidden" id="t_reservation_kinds" name="reservation_kinds" value="">
	<input type="hidden" id="t_payment" name="payment" value="">
	<?php
	if(isset($loginInfo['User']) && $loginInfo['User']['pay_plan'] === 'M'){
	?>
	<input type="hidden" id="t_class_in_use" name="class_in_use" value="skype">
	<?php
	}
	?>
	</form>
	</div>
</div>
<script type="text/javascript">
var teaSS = document.getElementById('teacherListWrap');
var li_id = '';
var liId = <?php echo json_encode($group_tea_id);?>;
var liHeight = 0;
var t_payment = 0
,kinds = <?php echo json_encode($kinds);?>
,path = {0:'reserve_confirm',1:'reserve_confirm2'};
for(i=0;i<liId.length;i++){
	li_id = document.getElementById('test' + liId[i]);
	if(li_id.clientHeight > liHeight){
		liHeight = li_id.clientHeight;
	}
}
for(i=0;i<liId.length;i++){
	li_id = document.getElementById('test' + liId[i]);
	li_id.style.height = liHeight + 'px';
}
document.getElementById('teacherschedSearch').style.visibility = 'visible';
function goTeacherImgDefault(e){
	e.src = "<?php echo $cubictalkko_path;?>/img/teacher/test.gif";
	return false;
}

<?php
if(isset($loginInfo['User']) && $loginInfo['User']['pay_plan'] === 'M'){
}
else{
?>
document.getElementById('payplan2').onclick = function(){
	t_payment = 0;
	t_reservation_kinds = "<?php echo $const['point_reservation'];?>";
	this.checked = true;
	if(document.getElementById('change_payplan')){
		document.getElementById('change_payplan').selectedIndex = 0;
	}
};
<?php
if(isset($getPaymentAllNotFix['userPayment'])
		&& count($getPaymentAllNotFix['userPayment']) > 0){
?>
document.getElementById('payplan1').onclick = function(){
	document.getElementById('change_payplan').selectedIndex = 0;
	t_payment = 0;
	this.checked = true;
};
document.getElementById('change_payplan').onclick = function(e){
	t_payment = this.value;
	document.getElementById('payplan1').checked = true;
};
document.getElementById('change_payplan').onchange = function(e){
	t_payment = this.value;
};
if(document.getElementById('change_payplan')){
	t_payment = document.getElementById('change_payplan').value;
}
<?php
}
}
?>
function goReserveConfirm(t_id,t_reservation_time){
	var oF = document.getElementById('TeacherReserveConfirm');
	document.getElementById('t_id').value = t_id;
	document.getElementById('t_reservation_time').value = t_reservation_time;
	document.getElementById('t_payment').value = t_payment;

	l_path = path[0];
	<?php
	if(isset($loginInfo['User']) && $loginInfo['User']['pay_plan'] === 'M'){
	?>
	var class_in_use = 'skype';
	if(document.getElementById('c_skype') && document.getElementById('c_skype').checked){
		class_in_use = 'skype';
	}
	else if(document.getElementById('c_telephone') && document.getElementById('c_telephone').checked){
		class_in_use = 'telephone';
	}
	document.getElementById('t_class_in_use').value = class_in_use;
	<?php
	}
	else{
	?>
	if(document.getElementById('payplan1')
			&& document.getElementById('payplan1').checked){
		l_path = path[1];
		doNotRunt = true;
	}
	else if(document.getElementById('payplan2')
			&& document.getElementById('payplan2').checked){
		l_path = path[0];
		doNotRunt = true;
	}
	else doNotRunt = false;
	<?php
	}
	?>
	t_reservation_kinds = "<?php echo $const['point_reservation'];?>";
	if(kinds[t_payment]){
		t_reservation_kinds = kinds[t_payment];
	}

	document.getElementById('t_reservation_kinds').value = t_reservation_kinds;

	oF.action = "<?php echo $cubictalkko_path;?>/teachers/" + l_path + "/";
	oF.target = "";
	
	<?php
	if(isset($loginInfo['User']) && $loginInfo['User']['pay_plan'] === 'M'){
	?>
	class_in_use = class_in_use.charAt(0).toUpperCase() + class_in_use.substr(1);
	
	alertify.confirm("<p>Do you want to continue reservation with the use of "+class_in_use+"?</p>", function(e){
		if(e){
			oF.submit();
			alertify.success("You've clicked OK");
			return false;
		}
		else{
			class_in_use = '';
			alertify.error("You've clicked Cancel");
			return false;
		}
	});
	<?php
	}
	else{
	?>
	oF.submit();
	<?php
	}
	?>
	return false;
}
</script>
<?php
}
?>
                            </TD>
                            </TBODY></TABLE>
<?php
if(isset($getAllTeacherAvailableLesson['teaAvailable']) && count($getAllTeacherAvailableLesson['teaAvailable']) > 0){
}
else{
?>
							<div id = "customize-float1">
                            <?	$teaChar = array();
                            	if ( isset($teacherChar) ) {
                            		$ii = 1;
                            		$path = get_basic_path()."/teacher";
                            		$image_path = "";
	                            	foreach ( $teacherChar as $keyTCs => $tCs) {
	                            		
	                            		$teaChar[$tCs["DictionaryDetail"]["express_name_en"]]["name"] = $tCs["DictionaryDetail"]["name"];
										$teaChar[$tCs["DictionaryDetail"]["express_name_en"]]["express_name_en"] = $tCs["DictionaryDetail"]["express_name_en"];
										$teaChar[$tCs["DictionaryDetail"]["express_name_en"]]["express_name_ko"] = $tCs["DictionaryDetail"]["express_name_ko"];
	                            									  		
								  		$img_name = $tCs["DictionaryDetail"]["express_name_en"];
								  		$tag_title = "KO: ".$tCs["DictionaryDetail"]["express_name_ko"]."\nEN: ".$tCs["DictionaryDetail"]["express_name_en"];
								  		if ( strlen(trim($image_path)) < 1 ) {
	                              			$image_path = "<div class = \"border float-left\"><img src = \"{$path}/image/teacher/tea_char/{$img_name}.png\" onerror = \"\" border = \"0\" width = \"18\" height = \"18\" title = \"{$tag_title}\"> (". $tCs["DictionaryDetail"]["express_name_ko"].")</div>";
	                              		} else {
	                              			$image_path = $image_path . "<div class = \"border float-left\"><img src = \"{$path}/image/teacher/tea_char/{$img_name}.png\" onerror = \"\" border = \"0\" width = \"18\" height = \"18\" title = \"{$tag_title}\"> (". $tCs["DictionaryDetail"]["express_name_ko"].")</div>";
	                              		}
	                              		$ii++;
	                            	}
                            	}
                            	
                            	if ( strlen(trim($image_path)) < 1 ) {
                            		$image_path = "약어　：무(무료체험),어(어린이),토(토익),오(오픽)";
                            	}
                            	echo $image_path;
                            ?>
                            </div>
                            <div class = "top-space-bottom"></div>
                        <TABLE class=thumb_table cellSpacing=1 cellPadding=8 

                        width="100%" bgColor=#ffffff border=0> 
                          <TBODY> 
                          <?php
                          	$i=0;
                          	$size = sizeof($teachers_info); 
                          	foreach($teachers_info as $teacher){
                          	if($teacher['Available_lesson'] != null ){
                          	if($i%5==0){
                              echo("<TR>");
                          	 } 
                              echo("<TD class = 'tds' background=/cubictalk/img/bg_gra_gray.jpg><DIV align=center class = 'tds' style = 'border-radius: 5px;'>");
                         
                              //echo $html->link( $html->image(change_path($teacher['Teacher']['img_file'],"/" ),
                              echo $html->link( $html->image($teacher['Teacher']['img_file'],
                              array("alt" => $teacher['Teacher']['tea_name'],"style"=>"border-radius: 5px; width: 80px; height: 70px;"/*<- style was added to make the images look more natural "height"=>"60", "width"=>"80","border"=>"1" <- old*/)), 
                              array('controller'=>'teachers','action' => 'reserve', 'id' =>$teacher['Teacher']['id']),
                              array('escape'=>false));
                              echo("<BR>");
                              echo $html->link ($teacher['Teacher']['tea_name'], array('controller'=>'teachers','action' => 'reserve','id' =>$teacher['Teacher']['id']));
                              $teaChars = array();
                              $teaChars = explode(",", $teacher['Teacher']['tea_char']);
                              $path = get_basic_path()."/teacher";
                              $image_path = "";
                              $img_name = "";
                              $tag_title = "";
                              $ii = 1;
                              foreach ( $teaChars as $keyTCs => $tCs) {
                              	$br = "";
							  	if ( isset($teaChar[$tCs]) ) {
							  		if ( ($ii%5) == 0 ) {
							  			$br = "<br>";
							  		}
							  		
							  		$img_name = $teaChar[$tCs]["express_name_en"];
							  		$tag_title = "KO: ".$teaChar[$tCs]["express_name_ko"]."\nEN: ".$teaChar[$tCs]["express_name_en"];

                              		if ( strlen(trim($image_path)) < 1 ) {
                              			$image_path = "<img src = \"{$path}/image/teacher/tea_char/{$img_name}.png\" onerror = \"\" border = \"0\" width = \"18\" height = \"18\" title = \"{$tag_title}\">";
                              		} else {
                              			$image_path = $image_path . "<img src = \"{$path}/image/teacher/tea_char/{$img_name}.png\" onerror = \"\" border = \"0\" width = \"18\" height = \"18\" title = \"{$tag_title}\">" . $br;
                              		}
                              		$ii++;
                              	}
                              }
                              if ( strlen(trim($image_path)) < 1 ) {
                              	$image_path = $teacher['Teacher']['tea_char'];
                              }
                              echo("<BR><div>".$image_path."</div>");
                              echo "<br /><div class = 'text'>".$teacher['Teacher']['tea_time']."</div>";
                              echo("</DIV></TD>"); 
                          	 	$i++;
                              if($i%5==0){
                              	echo("</TR>");
                          	  } 	
                               
                          	 }
                          	 
                          	}//if exist availelesson or not 
                         	
                          ?>
                         </TBODY></TABLE>
<?php
} // End if(isset($getAllTeacherAvailableLesson['teaAvailable']) && count($getAllTeacherAvailableLesson['teaAvailable']) > 0){}
// else{}
?>
                         </TD></TR></TBODY></TABLE> 
                  <P>&nbsp;</P>
                  
                     
                 <TABLE cellSpacing=1 cellPadding=10 width=670 bgColor=#cccccc 
                  border=0> 
                    <TBODY> 
                    <TR> 
                      <TD bgColor=#f9f7f2> 
                        <DIV class=cont_title>【검색일자에 스케줄이 없는 강사일람】</DIV> 
                        <TABLE height=30 cellSpacing=0 cellPadding=0 border=0> 
                          <TBODY> 
                          <TR>
                          <?php if($checkPayment=='OK'){ ?>   
                            <TD><STRONG>검색내용　：</STRONG>강사님 사진을 클릭하셔서 강사님 동영상  스케줄확인 및 자유수업예약이 가능합니다. 고정수업은 [<a href="<?php echo c_path();?>/fixedschedules/index1/">고정수업</a>]메뉴를 이용해주세요</TD>
                            <?php }else {?>
                           <TD><STRONG>유료회원으로 전환하셔서 예약을 하실 수 있습니다.</STRONG> </TD>
                           <?php }?> 
                            </TR></TBODY></TABLE> 
                        <TABLE class=thumb_table cellSpacing=1 cellPadding=8 

                        width="100%" bgColor=#ffffff border=0> 
                          <TBODY> 
                          <?php
                          	$i=0;
                          	$size = sizeof($teachers_all_info); 
                          	foreach($teachers_all_info as $teacher){
                          		$is_scheduled_teacher = false;
                          		foreach($teachers_info as $sheduled_teacher){
                          			if($sheduled_teacher['Available_lesson'] != null ){
	                          			if($sheduled_teacher['Teacher']['id']==$teacher['Teacher']['id']){
	                          				$is_scheduled_teacher = true;
	                          				break;
	                          			}
                          			}
                          		}
                          		
                          		if($is_scheduled_teacher == true){
                          			continue;
                          		}
                          		
                          	 if($i%5==0){
                              echo("<TR>");
                          	 } 
                              echo("<TD class = 'tds' background=/cubictalk/img/bg_gra_gray.jpg><DIV align=center class = 'tds' style = 'border-radius: 5px;'>");
                         
                              //echo $html->link( $html->image(change_path($teacher['Teacher']['img_file'],"/" ),
                              echo $html->link( $html->image($teacher['Teacher']['img_file'],
                              array("alt" => $teacher['Teacher']['tea_name'] , "style"=>"border-radius: 5px; width: 80px; height: 70px;"/*<- style was added to make the images look more natural "height"=>"60", "width"=>"80","border"=>"1" <- old*/)), 
                              array('controller'=>'teachers','action' => 'reserve', 'id' =>$teacher['Teacher']['id']),
                              array('escape'=>false));
                              echo("<BR>");
                              echo $html->link ($teacher['Teacher']['tea_name'], array('controller'=>'teachers','action' => 'reserve','id' =>$teacher['Teacher']['id']));
                              // <!-- Begin Comment Teacher's Character
//                               $teaChars = array();
//                               $teaChars = explode(",", $teacher['Teacher']['tea_char']);
//                               $path = get_basic_path()."/teacher/";
//                               $image_path = "";
//                               $img_name = "";
//                               $tag_title = "";
//                               $ii = 1;
//                               foreach ( $teaChars as $keyTCs => $tCs) {
//                               	$br = "";
// 							  	if ( isset($teaChar[$tCs]) ) {
// 							  		if ( ($ii%5) == 0 ) {
// 							  			$br = "<br>";
// 							  		}
							  		
// 							  		$img_name = $teaChar[$tCs]["express_name_en"];
// 							  		$tag_title = "KO: ".$teaChar[$tCs]["express_name_ko"]."\nEN: ".$teaChar[$tCs]["express_name_en"];

//                               		if ( strlen(trim($image_path)) < 1 ) {
//                               			$image_path = "<img src = \"{$path}/image/teacher/tea_char/{$img_name}.png\" onerror = \"\" border = \"0\" width = \"18\" height = \"18\" title = \"{$tag_title}\">";
//                               		} else {
//                               			$image_path = $image_path . "<img src = \"{$path}/image/teacher/tea_char/{$img_name}.png\" onerror = \"\" border = \"0\" width = \"18\" height = \"18\" title = \"{$tag_title}\">" . $br;
//                               		}
//                               		$ii++;
//                               	}
//                               }
//                               if ( strlen(trim($image_path)) < 1 ) {
//                               	$image_path = $teacher['Teacher']['tea_char'];
//                               }
//                               echo("<BR><div>".$image_path."</div>");
								// --> End Comment Teacher's Character
                              echo "<br /><div class = 'text'>".$teacher['Teacher']['tea_time']."</div>";
                              echo("</DIV></TD>"); 
                          	 	$i++;
                              if($i%5==0){
                              	echo("</TR>");
                          	  } 	
                               
                          	 }
                         	
                          ?>
                         </TBODY></TABLE></TD></TR></TBODY></TABLE> 
                  <P>&nbsp;</P>
</table>
<!-- /어학과정 -->

<!-- 배너 -->
<table border="0" cellspacing="0" cellpadding="0" width="652">

</table>
<!-- /배너 -->

<div class="popVid" id="popVid">
	<div id="db" class="divback" onclick="divClose()" align="center"></div>
	<div id="df" class="divfront" style="position:fixed; z-index:9999;"></div>
</div>
</div>

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
<script type="text/javascript">
	function popVid(){
		if (document.getElementById('popVid').style.display!="block")
			document.getElementById('popVid').style.display="block";
		
		var closeAre = '<div class="close" align="right"><span><a href="javascript:void(0)" onclick="divClose()" style="padding:5px;font-size:18px; font-family:arial; font-weight:bold; letter-spacing:3px;"><b>close</b></a></span></div>';
		
		$('div#df').empty().html(closeAre+'<div><iframe width="560" height="315" src="//www.youtube.com/embed/RS3u6-FqBL4" frameborder="0" allowfullscreen></iframe></div>');

		document.getElementById('db').style.display = 'block';
		document.getElementById('df').style.display = 'block';

		if (document.getElementById('df')) {
			var getPosition="df";
			var dfStyle={
					"df":{ top:parseInt(((window.innerHeight-$("#df").height())/2))+"px", left:parseInt(((window.innerWidth-$("#df").width())/2))+"px"},
					"img-result":{top:parseInt(((window.innerHeight-328)/2))+"px", left:parseInt(((window.innerWidth-541)/2))+"px"}};
			if (document.getElementById('img-result'))
				getPosition="img-result";
					
				$('#df').css(dfStyle[getPosition]);
		}

		document.getElementById('db').style.height=window.innerHeight+"px";
		document.getElementById('db').style.position="fixed";
		return false; }
	
	function divClose() {
		$('div[id="df"]').empty(); document.getElementById('db').style.display = 'none'; document.getElementById('df').style.display = 'none'; 
		return false;
	}

var oF = ''
,date_arr = <?php echo json_encode($date_arr);?>;
function dateSearch(e,d){
	if(date_arr.indexOf(d)){
		oF = document.getElementById('formTeacher');
		oF.reset();
		document.getElementById('searchDate'). value = d;
		oF.action = "<?php echo $cubictalkko_path;?>/teachers/";
		oF.submit();
	}
	else window.reload();
	return false;
}
function teaSearch(e,d){
	if(!d){
		d = "<?php echo $express_day;?>";
		document.getElementById('searchParams').value = 1;
	}
	oF = document.getElementById('formTeacher');
	document.getElementById('TeacherCalDate').value = d;
	document.getElementById('TeacherExpressDay').value = d;
	oF.action = "<?php echo $cubictalkko_path;?>/teachers/";
	oF.submit();
	return false;
}

function skyTelephone(element){
	var p, attrClass;

	p = document.getElementById('c_skype').parentElement;
	p.className = p.className.replace(/(^|\s)active(\s|$)/g,'');
	
	p = document.getElementById('c_telephone').parentElement;
	p.className = p.className.replace(/(^|\s)active(\s|$)/g,'');
	
	p = element.parentElement;
	attrClass = p.getAttribute('class');
	
	if(!attrClass.match(/(^|\s)active(\s|$)/g)){
		p.className = p.className + ' active ';
	}
	return false;
}
</script>

<?php include ('bottom.php')?>
                  