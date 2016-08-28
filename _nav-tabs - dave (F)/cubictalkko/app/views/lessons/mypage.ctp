<?php include("views/common/top.php");?>

<!-- Levy Aviles 2012-10-26 -->
<?=$javascript->link('Utils.js');?>
<?php
$today = date('Y-m-d');

$totalLesson = count($lessons);
if(isset($lessons) && $totalLesson > 0){
$paginator_session = $paginator->params;
$rCount=0; // row counting or data counting initialize;
if(isset($paginator_session['paging']['Lesson']) && count($paginator_session['paging']['Lesson']) > 0){
$paginator_session=$paginator_session['paging']['Lesson'];
if($paginator_session['count'] > $paginator_session['current']){
$rCount=(($paginator_session['page']-1)*$paginator_session['defaults']['limit']);
} // if($totalLesson > $paginator_session['current']){}
} // if(isset($paginator_session['paging']['Lesson']) && count($paginator_session['paging']['Lesson']) > 0){}
} // if(isset($lessons) && $totalLesson > 0){}
$cubictalkko_path=c_path();
						
$icon_message=array(
		'Cancel'=>array_merge(array('Dictionary'=>array('name'=>'icon-link-cancel','value'=>''))
				,array('DictionaryDetail'=>array('name'=>'Cancel','express_name_en'=>'Cancel'
						,'express_name_ko'=>'수업취소 및 연기'))
				,array(array('icon_img_name'=>'icon-link-cancel-ko.png')))
		,'Complain'=>array_merge(
				array('Dictionary'=>array('name'=>'icon-link-complain','value'=>''))
				,array('DictionaryDetail'=>array('name'=>'Complain','express_name_en'=>'Complain'
						,'express_name_ko'=>'수업불만'))
				,array(array('icon_img_name'=>'icon-link-complain-ko.png')))
		,'Comment'=>array_merge(array('Dictionary'=>array('name'=>'icon-link-comment','value'=>''))
				,array('DictionaryDetail'=>array('name'=>'Comment','express_name_en'=>'Comment'
						,'express_name_ko'=>'코멘트보기'))
				,array(array('icon_img_name'=>'icon-link-comment-ko.png')))
		,'fixKDays'=>array_merge(array('Dictionary'=>array('name'=>'icon-image-fixKDays','value'=>''))
				,array('DictionaryDetail'=>array('name'=>'fixKDays','express_name_en'=>'Cubic'
						,'express_name_ko'=>'큐빅예약'))
				,array(array('icon_img_name'=>'fixKDays.png')))
		,'fixMDays'=>array_merge(array('Dictionary'=>array('name'=>'icon-image-fixMDays','value'=>''))
				,array('DictionaryDetail'=>array('name'=>'fixMDays','express_name_en'=>'5 Days a week (Mon-Fri)'
						,'express_name_ko'=>'주 5회고정 (월-금)'))
				,array(array('icon_img_name'=>'fixMDays.png')))
		,'fixM2Days'=>array_merge(array('Dictionary'=>array('name'=>'icon-image-fixM2Days','value'=>''))
				,array('DictionaryDetail'=>array('name'=>'fixM2Days','express_name_en'=>'2 Days a week (Tue,Thu)'
						,'express_name_ko'=>'주 2회고정 (화,목)'))
				,array(array('icon_img_name'=>'fixM2Days.png')))
		,'fixM3Days'=>array_merge(array('Dictionary'=>array('name'=>'icon-image-fixM3Days','value'=>''))
				,array('DictionaryDetail'=>array('name'=>'fixM3Days','express_name_en'=>'3 Days a week (Mon,Wed,Fri)'
						,'express_name_ko'=>'주 3회고정 (월,수,금)'))
				,array(array('icon_img_name'=>'fixM3Days.png')))
		,'fixMDDays'=>array_merge(array('Dictionary'=>array('name'=>'icon-image-fixMDDays','value'=>''))
				,array('DictionaryDetail'=>array('name'=>'fixMDDays','express_name_en'=>'Daytime / 5 Days a week (Mon-Fri)'
						,'express_name_ko'=>'낮고정 (월-금)'))
				,array(array('icon_img_name'=>'fixMDDays.png')))
		,'fixPDays'=>array_merge(array('Dictionary'=>array('name'=>'icon-image-fixPDays','value'=>''))
				,array('DictionaryDetail'=>array('name'=>'fixPDays','express_name_en'=>'Point'
						,'express_name_ko'=>'포인트'))
				,array(array('icon_img_name'=>'fixPDays.png')))
		,'fixTDays'=>array_merge(array('Dictionary'=>array('name'=>'icon-image-fixTDays','value'=>''))
				,array('DictionaryDetail'=>array('name'=>'fixTDays','express_name_en'=>'Test'
						,'express_name_ko'=>'체험수업'))
				,array(array('icon_img_name'=>'fixTDays.png')))
		,'fixTMDays'=>array_merge(array('Dictionary'=>array('name'=>'icon-image-fixTMDays','value'=>''))
				,array('DictionaryDetail'=>array('name'=>'fixTMDays','express_name_en'=>'Test Mobile'
						,'express_name_ko'=>'T.Mobile'))
				,array(array('icon_img_name'=>'fixTMDays.png')))
		,'SkypeCall'=>array_merge(array('Dictionary'=>array('name'=>'icon-image-skype_call','value'=>''))
				,array('DictionaryDetail'=>array('name'=>'SkypeCall','express_name_en'=>'Skype Call'
						,'express_name_ko'=>'스카이프로 전화걸기'))
				,array(array('icon_img_name'=>'skype_call.png')))
		,'SkypeAdd'=>array_merge(array('Dictionary'=>array('name'=>'icon-image-skype_add','value'=>''))
				,array('DictionaryDetail'=>array('name'=>'SkypeAdd','express_name_en'=>'Skype Add'
						,'express_name_ko'=>'강사님 연락처추가'))
				,array(array('icon_img_name'=>'skype_add.png')))
		,'fixBONUSDays'=>array_merge(array('Dictionary'=>array('name'=>'icon-image-fixBonus','value'=>''))
				,array('DictionaryDetail'=>array('name'=>'fixBONUSDays','express_name_en'=>'Bonus'
						,'express_name_ko'=>'보너스 수업'))
				,array(array('icon_img_name'=>'fixBONUSDays.png'))));

$icons_html = '';
foreach($icon_message as $keyicon_message => $kim){
	$icon_title_en = $kim['DictionaryDetail']['express_name_en'];
	$icon_title_ko = $kim['DictionaryDetail']['express_name_ko'];
	$icon_img = $kim[0]['icon_img_name'];
$icons_html .= <<<HTML
		<tr><td>
		<img src="$cubictalkko_path/image/mypage/$icon_img" border="0" width="35"
			height="35" title="$icon_title_en \n $icon_title_ko" alt="$icon_title_en $icon_title_ko">
		</td><td>$icon_title_en</td>
		<td>$icon_title_ko</td></tr>
HTML;
}
?>

<style type="text/css">
.container *,.div-container *{font-family:"Helvetica Neue",Helvetica,Arial,sans-serif}
.container>img{vertical-align:middle}
.container>img{border:0}
.bordered{border:1px solid #606060}
.divback {
	padding:20px 5px 20px 5px;
	display:none; position:fixed; z-index:999; width:100%; border:1px solid #FFF; top:0; left:0;
	
	filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#000000', endColorstr='#fffff'); /* for IE */
	-ms-filter: "progid:DXImageTransform.Microsoft.Alpha(Opacity=70)";
	
	background: -webkit-gradient(linear, left top, left bottom, from(#000), to(#fff)); /* for webkit browsers */
	background: -moz-linear-gradient(top,  #000,  #fff); /* for firefox 3.6+ */  opacity: .2; 
}
.divfront { display:none; }
.divfront { border:1px solid #666; -webkit-border-radius: 2px; -moz-border-radius: 2px; border-radius: 2px; padding:10px; background:#FFF; }
.tdincomments:hover { cursor:pointer; background:#F3E2A9; }

div.div-other-info {
 padding-top:5px; padding-bottom:5px; padding-left:6px; padding-right:6px;  background: rgb(204, 204, 204);
 -webkit-border-radius: 2px; -moz-border-radius: 2px; border-radius: 2px;
}

div.div-other-info table.tbl-other-info, div.div-pay-plan-info table.tbl-pay-plan-info {
	border:1px solid rgb(57, 114, 73); border-collapse:collapse; background:rgb(255,255,255); width:100%;
	-webkit-border-radius: 2px; -moz-border-radius: 2px; border-radius: 2px; }

div.div-other-info table.tbl-other-info th, div.div-pay-plan-info table.tbl-pay-plan-info th { border:1px solid rgb(57, 114, 73); }

div.div-pay-plan-info table.tbl-pay-plan-info thead th {
	padding-top:5px; padding-bottom:3px; padding-left:10px; padding-right:10px;
	font-size:12px; font-family:arial; font-weight:bold; letter-spacing:3px;
	background: rgb(98, 139, 97); color:rgb(255, 255, 255); }

div.div-other-info table.tbl-other-info thead th, div.div-pay-plan-info table.tbl-pay-plan-info tbody th {
	padding-top:5px; padding-bottom:3px; padding-left:10px; padding-right:10px;
	font-size:12px; font-family:arial; font-weight:bold; letter-spacing:2px; }
	
div.div-pay-plan-info table.tbl-pay-plan-info tbody th:first-child {
	padding-top:5px; padding-bottom:3px; padding-left:10px; padding-right:10px;
	font-size:12px; font-family:arial; font-weight:bold; letter-spacing:2px; }

span.span-info {
	padding:2px; cursor:pointer; background:rgb(199, 225, 186); color:rgb(0, 0, 0); border:1px solid rgb(107, 171, 120);
	font-size:8px; font-family:arial; font-weight:bold; letter-spacing:2px; }

span.span-info:hover { background:rgb(107, 171, 120); color:rgb(255, 0, 0); }

div.div-info, div.info_comments { display:none; position:fixed; z-index:9999; }
	
div.info_details {
	width:695px; max-width:695px; min-width:695px;
	background: rgb(233, 233, 233); border:1px solid rgb(204,204,204); padding:5px;
	-webkit-border-radius: 5px; -moz-border-radius: 5px; border-radius: 5px; }

div.df { background: rgb(233, 233, 233); border:1px solid rgb(204,204,204); } 

div#infos {
	text-align:justify; background: rgb(209, 247, 217); color: rgb(0,0,0); border:1px solid rgb(204,204,204); padding:5px;
	-webkit-border-radius: 2px; -moz-border-radius: 2px; border-radius: 2px; }

span.update,span.cancel,button.btn.update,button.btn.cancel { display:none; }

div.records {
	border:1px solid #888; width:670px; height:390px; padding:5px;
	-webkit-border-radius: 2px; -moz-border-radius: 2px; border-radius: 2px; }

iframe.listoffiles { width:404px; border:none; border-top:1px solid #888; }

div.lesson_comment {
	font-family:arial; font-size:11px; background:#f18103; color:#FFF; letter-spacing:3px;
	-webkit-border-top-left-radius: 2px; -webkit-border-top-right-radius: 2px; -moz-border-radius-topleft: 2px; -moz-border-radius-topright: 2px;
	border-top-left-radius: 2px; border-top-right-radius: 2px; border:1px solid #f18103; padding:2px 5px 2px 5px; width:529px; max-width:529px; min-width:529px; }

textarea.txtarea_comment { width:535px; max-width:535px; min-width:535px; height:134px; max-height:134px; min-height:134px; }

div.table-infos { font-family:Arial, Helvetica, sans-serif; font-size:0.8em;}
<?php
if ( isset( $getPaymentHistory ) && count($getPaymentHistory) > 1 ) {
?>
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
<?php
}
?>
<?php
if ( isset($getEvaerCodeThruPayment) && count($getEvaerCodeThruPayment) > 0 && isset($getEvaerCodeThruPayment["codeDisplay"]) && $getEvaerCodeThruPayment["codeDisplay"] == true ) {?>
.evaerCode {
 padding:5px;
 background:rgb(204, 204, 204);
 position:absolute;
 border:1px solid #808080;
 z-index:1000;
 text-align:left;
 font-size:10px;
 display:none;
}
.color-link { color:#0099CC; }
.evaerClose {
 border:1px solid #808080;
 padding:5px;
 background:#F5F5F5;
}
<?php
}
?>
div.proceedNotice span { color:#808080; }
div.proceedNotice span.effectFadeInOut { color:#808080; }
div.proceedNotice span.effect { color:#ff0000; }
img{vertical-align:middle}
form div#paging>[type='button']{
	font-size:18px;
}
div.popVid { display:none; position:fixed; z-index:9999; }
div.df { background: rgb(233, 233, 233); border:1px solid rgb(204,204,204); } 
._close{opacity:1;color:#888;font-size:21px;}
<?php //paging ?>
._main ._pages,._pages>a:link{font-size:16px;font-weight:bold}
._main,._main:after,_main:before{content: " ";}
._main ._content{position:relative;}
._content,._pages{display:inline-block;text-align:center;margin-top:5px;margin-left:5px;}
._content{padding:10px 5px;}
._pages{padding:5px 6px;border:1px solid #ccc;color:#ff0000}
._pages>._disabled{color:#ccc}
._prev1,._number1,._number1>._pages,._next1{float:left}
._counter1{float:right}
.text-center{text-align:center}
.paging>.clearfix,._main>._clearfix{content:" ";display:block;clear:both}

.div-container .custome1-table-header tr th,.div-container .custome1-table-header tr td{
	border:1px solid #397249;
}
.div-container .custome1-table-header thead>tr{
	background:#628B61;
}
.div-container .custome1-table-header thead td{
	color:#FFFFFF;
}
.div-container .current-pages-hide{display:none}
.div-container .current-pages-show{display:block}
.div-skype .tbl-skype > thead > tr > td
,.div-skype .tbl-skype > tbody > tr > td {
  border-color: #2F7549;
}

.noBreak {
  white-space: nowrap;
}

.table.table-bordered {
  border-collapse: collapse;
}
.table.table-bordered tr td {
  border: 1px solid #cccccc;
}
.table.table-bordered tr.scheduleToday td {
  background: #DBFBD9;
}
</style>
<!-- Levy Aviles 2012-10-26 -->
<!-- <DIV class="this-title"></DIV> -->
<div class="div-container">
	<div class="text-left"><img src="<?php echo $cubictalkko_path;?>/image/title/lessons-title-ko.png" border="0"></div>

</div>


<?php include('views/common/nav_tabs.php');?>

<!-- 교육과정 -->
<table border="0" cellspacing="0" cellpadding="0" width="670">
<tr><td width="100%">

 <SCRIPT type=text/JavaScript>

  	function go_sub(lesson_id){
	  	//alert(lesson_id);
		//location.href = "<?=c_path();?>/lessons/lesson_cancel/" + lesson_id;
		document.form1.cancel_point.value = lesson_id;
		document.form1.action = "<?=c_path();?>/lessons/lesson_cancel/" + lesson_id;
		document.form1.submit();
	}

	  //booklists
	  function go_ftpbooks()
	  {
		 location.href =  "<?=c_path();?>/ftpbooks/index/";
	  }

	function to_complains(id) {
		document.getElementById("form"+id).action = "<?=c_path()?>/complains/";
		document.getElementById("form"+id).submit();
	}

	function comment (id) {
		$.ajax({
			type: 'POST',
			url: '<?=c_path()?>/lessons/view',
			dateType:'html',
			data: {id : id},
			success: function(data){
				$('#_pages').empty().html(data);
				$('body').first().addClass('modal-open').css({'padding-right':'17px'});
				$('#myModal').prepend($('<div class="modal-backdrop fade in" style="z-index: 0;"></div>'));
				$('#myModal').find('.modal-dialog').css({'width':'98%'});
				$('#myModal').addClass('fade in');
				$('#myModal').show();
			},
			error: function(message){alert(message);}
		});
		return false;
	}

	function popDiv (id) {
		
		var info = {1 : '<font size="2"><b>수업 1시간전</b>이면 수업취소가 가능하며(매일수업,횟수수업) 다른 강사님으로 예약할 수 있습니다. 고객님이 획득하신 포인트가 10포인트가 넘을 경우 예약화면에 [포인트예약버튼]이 표시됩니다. 한수업당 단가를 국내 최저가로 낮추었기 때문에 수업은 꼭 매일 2번씩 받으셔야 합니다. 횟수플랜 매일플랜을 받으시는 분은 수업이 끝나시면 바로 다음수업을 예약하실수 있습니다. 수업예약방법은 구체적인 방법은 <a id="link1" href="<?=b_path();?>/bbs/board.php?bo_table=newsKO&wr_id=20&page=4" target="_blank">수업예약방법보기</a> 를 클릭해 주세요</font>', 2 : '<font size="2"><p><b>수업완료 :</b> 수업을 진행한 후 강사님이 [예약]을 [완료]로 변경합니다.실제로 수업이 정상 진행된 경우만 표시됩니다.</p> <p><b>수업연장 :</b> 큐빅토크 또는 강사님 사정으로 예약되어있는 수업이 진행이 되지 못할때 관리자에 의해 [연장]으로 표시됩니다.</p> <p><b>즉 고객님 총 받으신 수업은 :</b> [완료]+[결석]=[총 받으신 횟수]이며 [취소]나 [연장]등은 받으신 횟수에 포함되지 않습니다.</p> <p>한개의 수업은 한개의 수업상태만 가집니다.한개의 수업이 예을 들어 [완료]이면서 [연장]일수가 없습니다.</p></font>', 3: '<div id="records" class="records"><div><iframe id="iframe" src="<?=c_path();?>/lessons/rfolders/" style="border:none; width:664px; height:512px;"></iframe></div></div>'};
		var top = 0.10;
		if (id==1 || id==2) 
			top=0.25;

		
		document.getElementById('info_details').style.top = (window.innerHeight*top)+'px';
		document.getElementById('info_details').style.left = (window.innerWidth*0.25)+'px';
		$('.div-info').fadeIn();
		$('#infos').empty().html(info[id]).fadeIn();
		if (document.getElementById("records") && document.getElementById("iframe")) {
			document.getElementById("iframe").style.height=$("#records").height()+"px";
		}
		return false;
	}
	
	function divClose(id) {
		if(!id){
			$('div[id="df"]').empty(); document.getElementById('db').style.display = 'none'; document.getElementById('df').style.display = 'none'; 
		} else if(id=="info_details"){
			$('.div-info').fadeOut(); $('#infos').empty().fadeOut();
		} else if(id="info_comments"){
			$('#myModal').hide();
			$('body').first().removeClass('modal-open');
			$('#myModal').find('.modal-backdrop').detach();
			$('#myModal').removeClass('fade in');
		}
		return false;
	}
	
	function popurl () {
		var url = 'http://blog.naver.com/buko33/187146636';
		var params = 'scrollbars=yes, height=800, width=800, top=0, left=0 resizable=yes';
		window.open(url,'evaerManual',params);
		return false; }
	
	function getPaymentHistory () {
		var iframe = '<iframe id = "iframe" style = "border:none; width:664px; height:512px;"></iframe>';
		$('#infos').empty().html(iframe).fadeIn();
		if ( document.getElementById('iframe') ) {
			document.getElementById('iframe').src = '<?=c_path();?>/lessons/myPaymentHistory/';
		}
		return false;
	}
  </SCRIPT>

<div class="container">
<?php // Modal ?>
<div class="div-container">
<div id="myModal" class="modal no-border-radius fade in" role="dialog" aria-hidden="false">
<div class="modal-dialog no-border-radius">
<div class="modal-content no-border-radius">
<div class="modal-header"><button type="button" class="close _close" onclick="divClose('info_comments')" aria-hidden="true">Close</button>
<h4 class="modal-title example-comment text-left"></h4></div>
<div id="modalBody" class="modal-body text-center"><div id="_pages"></div>
</div></div></div></div></div>

<div class="popVid" id="popVid">
	<div id="db" class="divback" onclick="divClose()" align="center"></div>
	<div id="df" class="divfront" style="position:fixed; z-index:9999;"></div>
</div>

<div class="">

<div class="div-info info_details" id="info_details">
 <div class="close" align="right"><span><a href="javascript:void(0)" onclick="divClose('info_details')" style="padding:5px;font-size:18px; font-family:arial; font-weight:bold; letter-spacing:3px;"><b>close</b></a></span></div>
 <div id="infos"></div>
</div>
 
<div class="info_comments" id="info_comments">
 <div id="db" class="divback" onclick="divClose('info_comments')" align="center"></div>
 <div id="df" class="divfront" style="position:fixed; z-index:9999;"></div>
</div>

<?php
// OLD
//if ($loginInfo['User']['pay_plan'] == 'E1' || $loginInfo['User']['pay_plan'] == 'E2') {
// IF the user pay_plan is NOT M(test) they already paid
if ($loginInfo['User']['pay_plan'] != 'M') {
 $class_count = 0;
 if ($loginInfo['User']['pay_plan'] == 'E1') {
  $class_count = 1;
 } else if ($loginInfo['User']['pay_plan'] == 'E2') {
  $class_count = 2;
 } // End if ($loginInfo['User']['pay_plan'] == 'E1');

// echo <<<html
// <div align="left">
// <UL>
// <LI>  <font color="#000000" >{$loginInfo['User']['name']}님 유료회원으로 가입해주셔서 진심으로 감사 드립니다.</font></LI>
// </UL>
// </div>
// <UL><LI>
// 또한 회원님의 추천으로 다른분이 유료회원이 되었을 경우 회원님과 신규회원님 모두 20포인트 (2수업분)을 보너스로 받을수
// 있습니다.다만 다 두분다 유료회원이셔야하셔야 하며 일주일 이내에 신청을 해주셔야 합니다. 꼭 이용해 보세요.
// </LI></UL>
// html;
//<!-- 2010-06-09 spart s2 s3 s5  -->
} else if ($loginInfo['User']['pay_plan'] == 'S2' || $loginInfo['User']['pay_plan'] == 'S3' || $loginInfo['User']['pay_plan'] == 'S5') { 
 $class_count = 0;
 if ($loginInfo['User']['pay_plan'] == 'S2') {
  $class_count = 2;
 } else if ($loginInfo['User']['pay_plan'] == 'S3') {
  $class_count = 3;
 } else if ($loginInfo['User']['pay_plan'] == 'S5') {
  $class_count = 5;
 } // End if ($loginInfo['User']['pay_plan'] == 'S2');

$start_day_s = change_date_format($loginInfo['User']['start_day'],2);
$last_day_s = change_date_format(change_time($loginInfo['User']['last_day'],"ONEDAY_BEHIND"),2);
echo <<<html
<div align="left">
<UL>
<LI>{$loginInfo['User']['name']}님 유료회원으로 가입해주셔서 진심으로 감사 드립니다.<br>
{$loginInfo['User']['name']}님은<font color="#FF0000"> 주({$class_count}회)</font>플랜으로 기간은 
<font color="#FF0000">{$start_day_s} ~ {$last_day_s}</font>
입니다.
</LI>
</UL>
<UL>
<LI>수업기간중 3번에 한해서  수업3시간전에  수업일정메뉴의 취소버튼을 눌러 취소하시면 수업기간을 연장해 드립니다.</LI>
</UL>
<UL>
<LI>또한 회원님의 추천으로 다른분이 유료회원이 되었을 경우 회원님과 신규회원님 모두 20포인트 (2수업분씩)을 보너스로 받을수
있습니다.두분다 유료회원이셔야하며 결제금액은 60,000원이상이셔야 합니다.꼭 이용해 보세요.
</LI>
</UL>  
</div> 
html;
}
else {
?>
 <div align="left">
  <UL>
  <LI><?=$loginInfo['User']['name'];?>님의 무료체험 기간은
      <font color="#FF0000"><?= change_date_format($loginInfo['User']['start_day'],2)?> ~ <?= change_date_format(change_time($loginInfo['User']['last_day'],"ONEDAY_BEHIND"),2)?></font>
        입니다.
  </LI>
  </UL>
  <?php if ($checkPayment == 'OK') { ?> 
  <UL>
  <LI>무료수강신청을 희망하시는 분은 <a href="<?=c_path();?>/teachers">강사검색</a>을 하시고 “무료포인트10포인트”를 
        사용하세요.수업을 마치시면 수업받으신 날짜의 자정이 지나지 않았으면 이 화면에 [수업평가]링크에서 자정이 지나셨으면 [지난수업]메뉴의[수업평가]를 누루시고 간단한 강사평가를 해주시면 다시 10포인트가 부여됩니다.<br>

      <BR>（수업시간은 약 25분간으로 진행됩니다.）수업시간 10 분전에 스카이프에 로그인하신상태에서 기다려주세요 수업시간이 되면 강사 선생님께서 전화를 걸어옵니다.  
  </LI>
  </UL>
  
  <UL>
  <LI>스카이프아이디가 정확이 기재되어 있는지 확인해보세요. <a href='http://cubictalk.com/cubicboardko/document/skype/skype_detail.htm' target=_blank><b>스카이프아이디란? 클릭</b></a>
      <a href="javascript:void(0)" onclick="return goToElmnt('table.tbl-other-info thead');">변경</a>
      <br>현재 큐빅토크에 등록한 스카이프 아이디 : <?=$loginInfo['User']['skype_id'];?> 정확하지 않은 스카이프아이디는 수업취소가 될수 있습니다.<br>
	  정보가 틀리시면 「<STRONG><a href="<?=b_path()?>/bbs/member_confirm.php?url=register_form.php">내정보수정</a></STRONG>」을 클릭해서 올바른 정보로 변경해 주세요 .
  </LI>
  </UL>
  
  <UL>
  <LI>
   <font color="#000000"><b>주의사항</b><br>
   <b>1.다른분의(가족분이나 친구분등)의 명의로 동일인이 반복해서 무료수업을 받는 경우에는 법적 책임을 물을수도 있습니다. <br> 
      2.연락가능한 전화번호가 불분명할 때는 임으로 수업이 취소될 수 있습니다.<br> 
      3.수업시간 한시간전까지 스카이프아이디등이 명확지 않을 경우에도 수업이 취소될수 있습니다.<br>
   </b>                        
   </font>
  </LI>
  </UL>
  <?php
  } else {
  ?>
  <UL>
  <LI><?=$loginInfo['User']['name'];?>님 무료체험이 끝나셨습니다. 
        무료체험 어떻하셨나요? 물론 부족한 점이 많이 있었을 것이라고  생각합니다 .
        큐빅토크는 국내 최저의 가격으로 가능한 많은 서비스를 제공하려고 언제나 노력하고 있습니다.
        현재 무료체험기간 중 사용하지 않은 나머지 포인트는 유료회원이 되시면 그대로 사용할 수 있습니다.
     <br>
  <?php
  } // End if ($checkPayment == 'OK');
  ?>
  </LI>
  </UL>
 </div>
<?php
} // End if ($loginInfo['User']['pay_plan'] == 'E1' || $loginInfo['User']['pay_plan'] == 'E2')
?>

<!-- 2010-10-10 START COMMON
<div align="left">
 <UL style="color:#FF0000;">
 <LI>아래의 큐빅토크 관리자들을  스카이프에 꼭 추가해주세요. 변동사항이 있을 경우 스카이프로 고객님께 메세</LI>
 </UL>
</div>
 -->

<div class="div-container">
<div class="div-skype">
<table class="table table-bordered tbl-skype">
<tr><td style="background: rgb(98, 139, 97); color:rgb(255, 255, 255);">스카이프</td>
<td><a href="http://www.cubictalk.com/cubicmatrials/softwares/evaer.zip">evaer다운로드 클릭</a><br>
    <a href="http://www.evaer.com/download.htm">evaer다운로드페이지가기
</td>
<td><a href="javascript:void(0)" onclick="popurl()">사용방법보기</a></td>
<?php
if (isset($getEvaerCodeThruPayment) && count($getEvaerCodeThruPayment) > 0 && isset($getEvaerCodeThruPayment["codeDisplay"]) && $getEvaerCodeThruPayment["codeDisplay"] == true) {
	$getEvaerCodeThruPaymentStr = (count($getEvaerCodeThruPayment["evaerCode"]) > 0 && isset($getEvaerCodeThruPayment["evaerCode"]["license"]) && strlen($getEvaerCodeThruPayment["evaerCode"]["license"]) > 0) ? $getEvaerCodeThruPayment["evaerCode"]["license"] : "";
	echo <<<html
	<td><div id = "evaerCode"><a href="javascript:void(0)" onclick="return eCde()">Evaer License</a>{$getEvaerCodeThruPaymentStr}</div></td>
html;
}
?>
<td><a href="#" class="icons-info" data-description="How to use skype"><img
src="<?php echo $cubictalkko_path;?>/image/mypage/icons-how-to-use-skype-ko.png"
border="0" width="" height="" alt="How to use skype" title="How to use skype"></a></td>
</tr>
</table>
</div>
</div>

	                 <!--
	                 <UL>
	                 <LI>
	                  <b><A href="<?=get_basic_path();?>/cubicboardko/document/howtoseebook/howtoseebook.htm" target=_blank><font color="#FF0000" size='2'>아래 교재 샘플 보는 방법 클릭 |</font></A> </b>
	                  <b><A href="<?=get_basic_path();?>/cubicboardko/document/booklists/student-booklists.htm" target=_blank><font color="#FF0000" size='2'>교재찿기 클릭 |</font></A> </b>
	                  <b><A href="<?=get_basic_path();?>/cubicboardko/document/booklists/adults.htm" target=_blank><font color="#FF0000" size='2'>성인추천교재 |</font></A> </b>
	                  <b><A href="<?=get_basic_path();?>/cubicboardko/document/booklists/junior.htm" target=_blank><font color="#FF0000" size='2'>주니어추천교재 </font></A> </b>
	                 </LI>

	                 <LI>
	                 	<b>현재 아래의 자료는 [교재를 선택]메뉴와 통합 중입니다. <a href="javascript:javascript:go_ftpbooks()" >[교재를 선택]가기</a></b>  
	                 </LI>
	                 
	                 <LI>
	                  <font color="#FF0000" size='2'> <b><A href="http://cubicmatrials1.zxq.net/textbook/main.php" target=_blank>1.초급 주니어용 교재샘플 1</A> :파닉스 시리즈 , SIDE BY SIDE PLUS등 </b></font>
	                 </LI>
	                 
	                 <LI>
	                  <font color="#FF0000" size='2'> <b><A href="<?=get_basic_path();?>/teacher/bbs/main.php" target=_blank>2.초급 주니어용 교재샘플 2</A> :Let's GO ,English Time,Side by Side등 </b></font>
	                 </LI>
	                 
	                  
	                 <LI>
	                  <font color="#FF0000" size='2'> <b><A href="http://cubictalkbook2.vacau.com/textbook/j_main.php" target=_blank>3.초급 주니어용 교재샘플 3</A> :주니어 그래머 시리즈(grammar time등),미국교과서외.</b></font>
	                 </LI>
	                 
	                  <LI>
	                  <font color="#FF0000" size='2'> <b><A href="http://cubicmatrials3.zxq.net/textbook/main.php" target=_blank>3.1 주니어동화</A> : 주니어 동화 </b></font>
	                 </LI>
	                 
	                 <LI>
	                  <font color="#FF0000" size='2'> <b><A href="http://cubictalkbook3.herobo.com/textbook/main.php" target=_blank>4.일반시중교재샘플 1</A> : 성인 (주니어 중급) 회화교재 1 </b></font>
	                 </LI>
	                 
	                 <LI>
	                  <font color="#FF0000" size='2'> <b><A href="http://newbook6.herobo.com/textbook/main.php" target=_blank>4.일반시중교재샘플 1-1</A> : 1-1(연속) </b></font>
	                 </LI>
	                 
	                 <LI>
	                  <font color="#FF0000" size='2'> <b><A href="http://newbook7.netne.net/textbook/main.php" target=_blank>5.일반시중교재샘플 2</A> : 성인 (주니어 중급) 회화교재 2 </b></font>
	                 </LI>
	                 
	                 <LI>
	                  <font color="#FF0000" size='2'> <b><A href="<?=get_basic_path();?>/cubicmatrials/textbook/main.php" target=_blank>6.일반시중교재샘플 3</A> :일반 시중교재 샘플입니다. </b></font>
	                 </LI>
	                 
	                 <LI>
	                  <font color="#FF0000" size='2'> <b><A href="http://cubictalkbook5.site50.net/textbook/main.php" target=_blank>7.성인 문법 비지니스 교재</A> : Grammar In Use , Business etc. </b></font>
	                 </LI>
	                 
	                 <LI>
	                  <font color="#FF0000" size='2'> <b><A href="http://cubictalkbook8.herobo.com/textbook/main.php" target=_blank>8.시험영어샘플</A> :토익스피킹,오픽,토플등 </b></font>
	                 </LI>         
	                 </UL>
	                 -->

</div>

</div>

<div align="left">
<div class="div-other-info" style="padding-bottom:10px;color:#FF0000;">
 <table class="tbl-other-info">
 <thead>
 <!--
 <tr>
  <th class="th-other-info" style="width:115px;" valign="top" align="left">수업기간</th>
  <th class="th-other-info" valign="top" align="left">
   <?php
   $start_day = $loginInfo['User']['start_day'];
   $last_day = $loginInfo['User']['last_day'];
   $paymentInfo = false;
   if (isset( $getPaymentHistory ) && (count($getPaymentHistory) > 1 || count($getPaymentHistory) == 1)) {
    $id = $getPaymentHistory[0]['Payment']['id'];
    $start_day = $getPaymentHistory[0]['Payment']['start_day'];
    $last_day = $getPaymentHistory[0]['Payment']['last_day'];
    $goods_name_num = substr($getPaymentHistory[0]['Payment']['goods_name'], 1, 1);
    $goods_months_num = $getPaymentHistory[0]['Payment']['goods_months'];
    $lesson_num = $getPaymentHistory[0]['Payment']['lesson_num'];
    $total = $goods_name_num * $goods_months_num * $lesson_num;
    $pay_plan_info['total_count'] = $total;
    $pay_plan_info['name'] = $getPaymentHistory[0]['DictionaryDetail']['express_name_ko'];
    if (count($getPaymentHistory) > 1) {
	 $paymentInfo = true;
    }
   }
   ?>
   <?=change_date_format($start_day,2)?> ~ <?=change_date_format($last_day,2)?>
   &nbsp;<?=$mypage_info['last_day'];?>
   <?php
   if ($paymentInfo == true) {
   ?>
   <a class = "preview" href = "javascript:void(0);" >중복결제 클릭</a>
   <?php
   }
   ?>
   </th>
   <th rowspan="3">
    <div><font color="#274257"><b>MY LEVEL</b></font></div>
    <div>
    <?php
    if (isset($leveltest)) {
    ?>
    <a href="<?=c_path();?>/leveltests/leveltest/"><img src="<?=c_path()?>/image/leveltests/levels/<?=((int)$leveltest)?>.png" height="90" width="100" alt="<?=((int)$leveltest)?> Level - Leveltests" /></a>
    <?php
    }
    ?>
    </div>
   </th>
  </tr>
  <tr>
   <th class="th-other-info" valign="top" align="left"><b>총 수업 횟수</b></th>
   <th class="th-other-info" valign="top" align="left">(25분기준)<?= $pay_plan_info['total_count']; ?>회</th>
  </tr>
  <tr>
   <th class="th-other-info" valign="top" align="left"><b>수업플랜</b></th>
   <th class="th-other-info" valign="top" align="left"><?= $pay_plan_info['name']; ?></th>
  </tr>
  -->
  <tr>
   <th class="th-other-info skype_id" colspan="3" valign="top" align="left" style="color:#FF0000;">
    <script type="text/javascript">
    <?php
    $checkedYes = '';
    $checkedNo = 'checked';
    $setSubstituteValue = 0;
    if(isset($loginInfo['User']['classes_status']) && $loginInfo['User']['classes_status'] == 1){
    	$checked = 'checked';
    	$checkedYes = 'checked';
    	$checkedNo = '';
    	$setSubstituteValue = 1;
    }
    ?>
    var setSubstituteClassChange = {'old':<?php echo $setSubstituteValue;?>,'new':0};
    function setSubstituteClass(e){
    	var SubstituteClassValue = 0;
    	setSubstituteClassChange['new'] = <?php echo $setSubstituteValue;?>;
    	if(document.getElementById('SubstituteYes').checked){
    		SubstituteClassValue = 1;
    		setSubstituteClassChange['new'] = 1;
    	}else if(document.getElementById('SubstituteNo').checked){
    		SubstituteClassValue = 0;
    		setSubstituteClassChange['new'] = 0;
        }else{
    		setSubstituteClassChange['new'] = 0;
        }
    	if(setSubstituteClassChange['old'] != setSubstituteClassChange['new']){
    		$.ajax({
    			type: 'POST',
    			url: '<?php echo $cubictalkko_path;?>/lessons/setSubstituteClass/',
    			dataType: 'json',
    			data: {action:'post',SubstituteClass:parseInt(SubstituteClassValue),'id':<?php echo $loginInfo['User']['id'];?>},
    			success: function(data){
    				// alert(data.setSubstituteClass);
    				setSubstituteClassChange['old'] = parseInt(SubstituteClassValue);
    				location.reload();
    			},
    			error: function(message){alert(message)}
    		});
    	}else{
    		alert('Already update!.');
    	}
    	return false;
   	}
    //Added by: Edmark
   	//Date: June 21 2016
   	//Show video user function
   	function show_video_user($user_id){
    	 window.open("<?=c_path()?>/teachers_video_manage/showtable/"+ $user_id, "mywindow", "location=1,status=1,scrollbars=1,width=600,height=600");
    	moveTo(3, 3);
    	return true;
    }
    </script>
	<div class="div-container">
	<table class="" cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td>
		<div class="">
		<font color="#274257"><b>SKYPE ID </b></font><font size="" color="#FF0000"><b><span id="skype_id"><font><?=$loginInfo['User']['skype_id'];?></font></span></b></font>&nbsp;
		<button type="button" class="span-info update update_skype_id btn btn-default btn-xs btn-primary" onclick="skypeid('skype_id','update')">Update</button>
		<button type="button" class="span-info cancel cancel_skype_id btn btn-default btn-xs btn-primary" onclick="skypeid('skype_id','cancel')">Cancel</button>
		<button type="button" class="span-info edit edit_skype_id btn btn-default btn-xs btn-primary" onclick="skypeid('skype_id','edit')">Edit</button>
		<a href="skype:<?=$loginInfo['User']['skype_id'];?>?call" style="padding:0;">
		<img src="<?=c_path();?>/image/mypage/skype_call.png" border="0" width="25" height="25" alt="Skype Me™!" title="Skype Me™!"/></a>
		<a href="#" class="icons-info btn btn-default btn-xs btn-info" data-description="Skype Id Profile"><?php echo $ChangeLanguageTo['Substitutewhatisthis'];?></a></div>
		<div class="">
		<?php echo $ChangeLanguageTo['Substitute?'];?>
		<span class="group-input-option">
			<input type="radio" name="SubstituteClass" id="SubstituteNo" value="0" <?php echo $checkedNo;?>>
			<label for="SubstituteNo"><?php echo $ChangeLanguageTo['No'];?></label>
		</span>
		<span class="group-input-option">
			<input type="radio" name="SubstituteClass" id="SubstituteYes" value="1" <?php echo $checkedYes;?>>
			<label for="SubstituteYes"><?php echo $ChangeLanguageTo['Ok'];?></label>
		</span>
		<button type="button" class="btn btn-default btn-xs btn-primary" onclick="setSubstituteClass()"><?php echo $ChangeLanguageTo['Change'];?></button>
		<a href="#" class="icons-info btn btn-default btn-xs btn-info" data-description="Substitute Message"><?php echo $ChangeLanguageTo['Substitutewhatisthis'];?></a>
	    </div></td>
        <!-- 
		Date: June 20 2016
		Added by: Edmark Isla
		Play video button
		-->
	    <th rowspan = "3" width = "105">
			<div>
				<?php 
				     $login_info = $session->read('loginInfo');
				     $user_id = $login_info['User']['id'];
				?>	
				<p>Play Video</p>
				<a href="#" onclick = "return show_video_user(<?php echo $user_id; ?>)" style="padding:0;">
				
				<img id = "vImg" src="<?=c_path();?>/image/users_video_images/users_play_video.png" border="0" width="60" height="50" alt="Play video" title="Play video"/></a>
			</div>
		</th>
	</tr>
	</table><form name="changeskype" method="post">
    <input type="hidden" name="id" value="<?=$loginInfo['User']['id'];?>"><input type="hidden" name="mb_id" value="<?=$loginInfo['User']['mb_id'];?>">
    <input type="hidden" name="user_id"><input type="hidden" name="field"></form></div>
   </th>
   <td rowspan="2" width="100">
	<div class="div-container">
	<?php
	$book_img = <<<html
	<a href="{$cubictalkko_path}/ftpbooks/" target="_blank"><img src="{$cubictalkko_path}/ftpbook/book_choose_a_book-ko.png" onerror="bookErrorImg(this)" width="100" height="113"></a>
html;
	if(isset($getMyCurrentBook['getMyCurrentBook']['Bookmark'])){
		$book_img = <<<html
	<a href="{$cubictalkko_path}/ftpbooks/show_book/{$getMyCurrentBook['getMyCurrentBook']['Ftpbook']['id']}/" target="_blank">
	<img src="{$cubictalkko_path}/ftpbook/category/{$getMyCurrentBook['getMyCurrentBook']['Ftpbook']['ftpsite_id']}/{$getMyCurrentBook['getMyCurrentBook']['Ftpbook']['symbol']}.jpg" onerror="bookErrorImg(this)" width="100" height="113"></a>
html;
	}
	echo $book_img;
	?>
	</div>
   </td>
   <th rowspan="2" width="100">
		<div class="text-center"><font color="#274257"><b>MY LEVEL</b></font></div>
		<div>
			<?php
			if(isset($leveltest)){
			?>
			<a href="<?=c_path();?>/leveltests/leveltest/"><img src="<?=c_path()?>/image/leveltests/levels/<?=((int)$leveltest)?>.png" height="90" width="100" alt="<?=((int)$leveltest)?> Level - Leveltests" /></a>
			<?php
			}
			?>
    </div>
   </th>
  </tr>
  <tr>
   <th class="th-other-info" valign="top" align="left" height="10"><font color="#274257">영작문: </font></th>
   <th class="th-other-info skype_id" colspan="2" valign="top" align="left" style="color:#FF0000;">
    <div id="mid-container" class="proceedNotice font-sz-12px">
     <a href="<?php echo c_path();?>/compositions/proceed/">Composition
      <?php
      $tagClass="";
      if (isset($proceedNotice) && !empty($proceedNotice) && intval($proceedNotice) > 0){
       $tagClass="effectFadeInOut effect";
      }
      ?>
      <span id="effectFadeInOut" class="<?php echo $tagClass;?>">New (<?php echo intval($proceedNotice);?>)</span>
     </a>
    </div>
    <div style="height:5px;"></div>
   </th>
  </tr>
  </thead>
  </table>
 </div>

 <form name="skype_id" method="post"> <input type="hidden" name="id" value="<?=$loginInfo['User']['id'];?>"><input type="hidden" name="user_id"></form>
<div class="div-container">
	<div class="container-table">
		<?php
		$lesson_status_header = $lesson_status_value['lesson_status_header'];
		$lesson_status_condition = array_merge(array('TotalE'=>0),$lesson_status_value['lesson_status_value']);
		$lesson_status_each = $lesson_status_condition;
		?>
		<table class="table table-bordered custome1-table-header">
		<thead>
			<tr><td width="210">
				<div class="" width="210">
					<span class="span-info" onclick="popDiv(1)"><font size="1"><b>수업예약방법</b></font></span>
					<span class="span-info" onclick="popDiv(2)"><font size="1"><b>수업상태설명</b></font></span>
				</div>
				</td>
			<?php
			foreach($lesson_status_header as $key_lesson_status_header => $k_lsh){
				$lesson_status_name = $key_lesson_status_header;
				if(is_array($k_lsh)){
					if(isset($k_lsh['DictionaryDetail']['express_name_ko'])
							&& strlen(str_replace(' ','',$k_lsh['DictionaryDetail']['express_name_ko'])) > 0) {
								$lesson_status_name = $k_lsh['DictionaryDetail']['express_name_ko'];
					}
					elseif(isset($k_lsh['DictionaryDetail']['express_name_en'])
							&& strlen(str_replace(' ','',$k_lsh['DictionaryDetail']['express_name_en'])) > 0) {
								$lesson_status_name = $k_lsh['DictionaryDetail']['express_name_en'];
					}
					elseif(isset($k_lsh['DictionaryDetail']['name'])
							&& strlen(str_replace(' ','',$k_lsh['DictionaryDetail']['name'])) > 0) {
								$lesson_status_name = $k_lsh['DictionaryDetail']['name'];
					}else{
								$lesson_status_name = $keycustom_lesson_status;
					}
				}
				echo "<td>{$lesson_status_name}</td>";
			}
			// End foreach($custom_lesson_status as $keycustom_lesson_status => $k_lsh){}
			?>
			</tr>
		</thead>
		<?php
		$next_payment_id = TRUE;
		// Student has Payment
		if(isset($payment_list) && count($payment_list) > 0)
		{
			$start_day_msg = '';
			foreach ($payment_list as $key_payment_list => $k_pl)
			{
				$payment_id = $k_pl['Payment']['id'];
				$start_day = date('Y-m-d',strtotime($k_pl['Payment']['start_day']));
				$start_day_ko = date('Y년m월d일',strtotime($start_day));
				$start_day_msg = "결제일 : {$start_day_ko}";
				$last_day = date('Y-m-d',strtotime($k_pl['Payment']['last_day']));
				if(isset($k_pl['DictionaryDetail']['express_name_en'])
						&& in_array($k_pl['DictionaryDetail']['express_name_en'], $RenewGetAllReservationKinds)){
					$last_day = '';
				}
					
				$months = 1;
				$months_num = 1;
				if(isset($k_pl['Payment']['goods_months'])
						&& is_numeric($k_pl['Payment']['goods_months'])
						&& $k_pl['Payment']['goods_months'] > 0){
					$months = $k_pl['Payment']['goods_months'];
					$months_num = $k_pl['Payment']['goods_months'];
				}
					
				$months = ' <span class=" noBreak " style="color:#FF6600;">' . $months . ' 개월</span>';
					
				$payment_goods_name_description2 = '';
				if(isset($k_pl['DictionaryDetail']['express_name_ko'])
						&& strlen(str_replace(' ','',$k_pl['DictionaryDetail']['express_name_ko'])) > 0) {
							$payment_goods_name_description2 = $k_pl['DictionaryDetail']['express_name_ko'];
							$payment_goods_name_description2 = $payment_goods_name_description2 . $months;
						}elseif(isset($k_pl['DictionaryDetail']['express_name_en'])
								&& strlen(str_replace(' ','',$k_pl['DictionaryDetail']['express_name_en'])) > 0) {
							$payment_goods_name_description2 = $k_pl['DictionaryDetail']['express_name_en'];
							$payment_goods_name_description2 = $payment_goods_name_description2 . $months;
						}elseif(isset($k_pl['DictionaryDetail']['name'])
								&& strlen(str_replace(' ','',$k_pl['DictionaryDetail']['name'])) > 0) {
							$payment_goods_name_description2 = $k_pl['DictionaryDetail']['name'];
							$payment_goods_name_description2 = $payment_goods_name_description2 . $months;
						}else{
							$payment_goods_name_description2 = 'Etc.,';
							if(isset($k_pl['Payment']['goods_name'])){
								$payment_goods_name_description2 = $k_pl['Payment']['goods_name'] .'/'. $payment_id;
								$payment_goods_name_description2 = $payment_goods_name_description2;
							}
						}
							
						$lesson_num = 0;
						if(isset($k_pl['Payment']['lesson_num'])
								&& is_numeric($k_pl['Payment']['lesson_num'])
								&& $k_pl['Payment']['lesson_num'] > 0){
							$lesson_num = $k_pl['Payment']['lesson_num'];
						}
							
						// 					$payment_status_num = 1;
						// 					if(isset($k_pl['Payment']['status']) && $k_pl['Payment']['status'] != 'U'
						// 							&& isset($k_pl['Payment']['status_num']) && $k_pl['Payment']['status_num'] > 0){
						// 						$payment_status_num = $k_pl['Payment']['status_num'];
						// 					}
						$fixedschedule_btn = '';
							
						$reserve = '';
						$extend = '';
						$change = '';
							
						if(isset($k_pl['Payment']['goods_name'])
								&& in_array($k_pl['Payment']['goods_name'],array('E1F','E2F'))){
							$reserve = "{$cubictalkko_path}/fixedschedules/reserve/";
							$extend = "{$cubictalkko_path}/fixedschedules/extend/";
							$change = "{$cubictalkko_path}/fixedschedules/delete/";
						}
						elseif(isset($k_pl['Payment']['goods_name'])
								&& in_array($k_pl['Payment']['goods_name'],array('E1F2'))){
							$reserve = "{$cubictalkko_path}/fixedschedules/reserve4/";
							$extend = "{$cubictalkko_path}/fixedschedules/extend4/";
							$change = "{$cubictalkko_path}/fixedschedules/delete4/";
						}
						elseif(isset($k_pl['Payment']['goods_name'])
								&& in_array($k_pl['Payment']['goods_name'],array('E1F3'))){
							$reserve = "{$cubictalkko_path}/fixedschedules/reserve3/";
							$extend = "{$cubictalkko_path}/fixedschedules/extend3/";
							$change = "{$cubictalkko_path}/fixedschedules/delete3/";
						}
						elseif(isset($k_pl['Payment']['goods_name'])
								&& in_array($k_pl['Payment']['goods_name'],array('E1FD','E2FD'))){
							$reserve = "{$cubictalkko_path}/fixedschedules/ReserveDayTime/";
							$extend = "{$cubictalkko_path}/fixedschedules/ExtendDayTime/";
							$change = "{$cubictalkko_path}/fixedschedules/DeleteDayTime/";
						}
						elseif(isset($k_pl['Payment']['goods_name'])
								&& in_array($k_pl['Payment']['goods_name'],array('TTE','TME'))){
							$reserve = "{$cubictalkko_path}/fixedschedules/reserveTelClass/";
							$extend = "{$cubictalkko_path}/fixedschedules/extendTelClass/";
							$change = "{$cubictalkko_path}/fixedschedules/DeleteTelClass/";
						}
						elseif(isset($k_pl['Payment']['goods_name'])
								&& in_array($k_pl['Payment']['goods_name'],array('TT3E','TM3E'))){
							$reserve = "{$cubictalkko_path}/fixedschedules/reserveTelClass3Days/";
							$extend = "{$cubictalkko_path}/fixedschedules/extendTelClass3Days/";
							$change = "{$cubictalkko_path}/fixedschedules/DeleteTelClass3Days/";
						}
						elseif(isset($k_pl['Payment']['goods_name'])
								&& in_array($k_pl['Payment']['goods_name'],array('TT2E','TM2E'))){
							$reserve = "{$cubictalkko_path}/fixedschedules/reserveTelClass2Days/";
							$extend = "{$cubictalkko_path}/fixedschedules/extendTelClass2Days/";
							$change = "{$cubictalkko_path}/fixedschedules/DeleteTelClass2Days/";
						}
							
						if(isset($k_pl['Payment']['status'])
								&& $k_pl['Payment']['status'] != 'U')
						{
							if(strlen($reserve) > 0){
								$fixedschedule_btn = <<<html
	<a class="btn btn-xs btn-primary" href="{$reserve}">신규</a>
html;
							}
							if(strlen($extend) > 0){
								$fixedschedule_btn .= <<<html
	<a class="btn btn-xs btn-success" href="{$extend}">연장</a>
html;
							}
						}
						else
						{
							if(strlen($change) > 0){
								$fixedschedule_btn .= <<<html
		<a class="btn btn-xs btn-info" href="{$change}">수업변경</a>
html;
							}
						}
							
						// lesson_num * goods_months = count the lesson number can reserve
						$total_classes = (($lesson_num * $months_num));
						// 					$total_classes2 = (($lesson_num * $months_num) * $payment_status_num);
							
						$lesson_status_each = $lesson_status_condition;
							
						// But the Payment didn't use sometimes already used
						if(isset($lesson_with_payment_id) && count($lesson_with_payment_id) > 0)
						{
							foreach($lesson_with_payment_id as $key_lesson_with_payment_id => $k_lwpi)
							{
								$lesson_status = $k_lwpi['Lesson']['lesson_status'];
									
								// Payment Id > 0
								if($k_lwpi['Lesson']['payment_id'] == $payment_id)
								{
									$next_payment_id = FALSE;
								}
								else
								{
									$next_payment_id = TRUE;
								}
									
								if(!$next_payment_id)
								{
									if($lesson_status == 'R'){
										$lesson_status_each['R'] = $k_lwpi[0]['total_lesson_status'];
									}
									if($lesson_status == 'F'){
										$lesson_status_each['F'] = $k_lwpi[0]['total_lesson_status'];
									}
									if($lesson_status == 'A'){
										$lesson_status_each['A'] = $k_lwpi[0]['total_lesson_status'];
									}
									if($lesson_status == 'E'){
										$lesson_status_each['E'] = $k_lwpi[0]['total_lesson_status'];
									}
									if($lesson_status == 'EM'){
										$lesson_status_each['EM'] = $k_lwpi[0]['total_lesson_status'];
									}
									if($lesson_status == 'CP'){
										$lesson_status_each['CP'] = $k_lwpi[0]['total_lesson_status'];
									}
									if($lesson_status == 'C'){
										$lesson_status_each['C'] = $k_lwpi[0]['total_lesson_status'];
									}
		
									unset($lesson_with_payment_id[$key_lesson_with_payment_id]);
								}
								else break;
							}
							// End foreach($lesson_with_payment_id as $key_lesson_with_payment_id => $k_lwpi)
						}
						// End if(isset($lesson_with_payment_id) && count($lesson_with_payment_id) > 0)
		
						$lesson_status_each['Total'] = $lesson_status_each['R'] + $lesson_status_each['A'] + $lesson_status_each['F'] + $lesson_status_each['CP'];
						$lesson_status_each['TotalE'] = $lesson_status_each['E'] + $lesson_status_each['EM'];
						if($total_classes > 0){
							$total_classes = "({$total_classes}회)";
						}
						else $total_classes = '';
						?>
				<tr>
					<td>
					<span class=" noBreak " data-payment-id="<?php echo $payment_id;?>">
					<?php echo "{$payment_goods_name_description2} {$total_classes}";?></span>
					<div class="" style="border-top: 1px solid #cccccc;">
					<span class=" noBreak " data-payment-id="<?php echo $payment_id;?>" style="display:block;"><?php echo $start_day_msg;?></span>
					<?php echo $fixedschedule_btn;?></div>
					</td>
					<td>
					
					<?php echo $lesson_status_each['R'];?></td>
					<td>
					
					<?php echo $lesson_status_each['F'];?></td>
					<td>
					
					<?php echo $lesson_status_each['A'];?></td>
					<td>

					<?php echo $lesson_status_each['CP'];?></td>
					<td><?php echo $lesson_status_each['Total'];?></td>
					<td><?php echo $lesson_status_each['TotalE'];?></td>
					<td><?php echo $lesson_status_each['C'];?></td>
					<td><?php echo $last_day;?></td>
				</tr>
				<?php
							unset($payment_list[$key_payment_list]);
						}
						// End foreach ($payment_id as $key_payment_id => $k_pi)
					}
					// End if(isset($payment_id) && count($payment_id) > 0)
					
						// For Test Class 
						$lesson_status_each = $lesson_status_condition;
						if(isset($lesson_no_payment_id) && count($lesson_no_payment_id) > 0)
						{
							foreach($lesson_no_payment_id as $key_lesson_no_payment_id => $k_lnpi)
							{
								$lesson_status = $k_lnpi['Lesson']['lesson_status'];
								$last_day = date('Y-m-d',strtotime($k_lnpi[0]['max_reserve_time']));
									
								if($lesson_status == 'R'){
									$lesson_status_each['R'] = $k_lnpi[0]['total_lesson_status'];
								}
								if($lesson_status == 'F'){
									$lesson_status_each['F'] = $k_lnpi[0]['total_lesson_status'];
								}
								if($lesson_status == 'A'){
									$lesson_status_each['A'] = $k_lnpi[0]['total_lesson_status'];
								}
								if($lesson_status == 'E'){
									$lesson_status_each['E'] = $k_lnpi[0]['total_lesson_status'];
								}
								if($lesson_status == 'EM'){
									$lesson_status_each['EM'] = $k_lnpi[0]['total_lesson_status'];
								}
								if($lesson_status == 'CP'){
									$lesson_status_each['CP'] = $k_lnpi[0]['total_lesson_status'];
								}
								if($lesson_status == 'C'){
									$lesson_status_each['C'] = $k_lnpi[0]['total_lesson_status'];
								}
							}
							// End foreach($lesson_no_payment_id as $key_lesson_no_payment_id => $k_lnpi)
						$lesson_status_each['Total'] = $lesson_status_each['R'] + $lesson_status_each['A'] + $lesson_status_each['F'] + $lesson_status_each['CP'];
						$lesson_status_each['TotalE'] = $lesson_status_each['E'] + $lesson_status_each['EM'];
				?>
				<tr>
					<td><span class=" noBreak ">포인트 예약</span></td>
					<td>

					<?php echo $lesson_status_each['R'];?></td>
					<td>

					<?php echo $lesson_status_each['F'];?></td>
					<td>

					<?php echo $lesson_status_each['A'];?></td>
					<td>

					<?php echo $lesson_status_each['CP'];?></td>
					<td><?php echo $lesson_status_each['Total'];?></td>
					<td><?php echo $lesson_status_each['C'];?></td>
					<td><?php echo $lesson_status_each['TotalE'];?></td>
					<td><?php echo $last_day;?></td>
				</tr>
				<?php
							}
							// End if(isset($lesson_no_payment_id) && count($lesson_no_payment_id) > 0)
echo <<<HTML
	<tr>
		<td>현재 고정수업 마지막 수업</td>
		<td colspan="8"><div style="color:#FF0000;text-align:justify;">{$fix_schedule_message}</div></td>
	</tr>
HTML;
					if(isset($checkStatusHOLD) && intval($checkStatusHOLD) > 0){
echo <<<HTML
	<tr><td colspan="9">
	<div align="left" style="letter-spacing:2px;font-size:12px;color:#003300"><b>장기휴강수업 :&nbsp;
	(<a class="hold-status" href="javascript:void(0)" onclick="return checkStatusHold(this)">
	$checkStatusHOLD  상세보기</a>)</b></div></td>
	</tr>
HTML;
					}
				?>
		</table>
	</div>
</div>
</div>
<!--  -->

	              <!-- change the 2010-11-16 to find condition -->
				
				<FORM name=form1 action=lesson_cancel_check.php method=post> 
				<input type = 'hidden' id = 'cancel_point' name = 'cancel_point'>
				<?php 
					//2012-05-28 START
					if(empty($search_conditions['skype_id'])){
						$skype_id = '';
					}else{
						$skype_id = $search_conditions['skype_id']; }
					
					if(empty($search_conditions['nick_name'])){
						$nick_name = '';
					}else{
						$nick_name = $search_conditions['nick_name']; }
					
					if(empty($search_conditions['search_from'])){
						$search_from = null;
					}else{
						$search_from = $search_conditions['search_from']; }
					
					if(empty($search_conditions['search_to'])){
						$search_to = null;
					}else{
						$search_to = $search_conditions['search_to']; }
					
					//2012-05-28 END
				?>
				</FORM>
				
				<FORM name='form11' id = 'form11' method='post'>
				<!-- start -->
				<div align="center" style="border:1px solid #888; -webkit-border-radius: 5px; -moz-border-radius: 5px; border-radius: 5px;">
				<TABLE class = 'c'>
				<thead>
					<TR>
						<td align="center" style="border-bottom:1px solid;"> <?php echo __('결제일');?>: </td>
						<td align="center" style="border-bottom:1px solid;">
							<select name="payment_id" id="payment_id" style="width:115px;">
							<?php
							if(isset($getMyListOfPayment)){
								foreach($getMyListOfPayment as $key_getMyListOfPayment => $k_gMLOP){
								
									$payment_goods_name_description2 = '';
									if(isset($k_gMLOP['DictionaryDetail']['express_name_ko'])
											&& strlen(str_replace(' ','',$k_gMLOP['DictionaryDetail']['express_name_ko'])) > 0) {
										$payment_goods_name_description2 = $k_gMLOP['DictionaryDetail']['express_name_ko'];
									}
									elseif(isset($k_gMLOP['DictionaryDetail']['express_name_en'])
											&& strlen(str_replace(' ','',$k_gMLOP['DictionaryDetail']['express_name_en'])) > 0) {
										$payment_goods_name_description2 = $k_gMLOP['DictionaryDetail']['express_name_en'];
									}
									elseif(isset($k_gMLOP['DictionaryDetail']['name'])
											&& strlen(str_replace(' ','',$k_gMLOP['DictionaryDetail']['name'])) > 0) {
										$payment_goods_name_description2 = $k_gMLOP['DictionaryDetail']['name'];
									}
								
									$payment_start_day = date('Y-m-d',strtotime($k_gMLOP['Payment']['start_day']));
								
									$selected = '';
								
									echo <<<HTML
									<option value= "{$k_gMLOP['Payment']['id']}" {$selected}>{$payment_goods_name_description2}\r\n<br>{$payment_start_day}</option>
HTML;
								}
							}
							?>
							<option value="all">ALL</option>
							</select>
						</td>
						<TD align="center" style="border-bottom:1px solid;"> <div>수업종류</div> </TD>
						<TD align="center" style="border-bottom:1px solid;"> 
							 <select name="reservation_kinds" id = 'sel1'>
							    <option value='all' selected >전부</option>
								<?php
								foreach($reservationKinds as $reservationKind){
									if(isset($r) && $reservationKind['DictionaryDetail']['express_name_en'] == $r && strlen($r) > 0)
										echo '<option value = "'.$reservationKind['DictionaryDetail']['name'].'" selected>'.$reservationKind['DictionaryDetail']['express_name_ko'].'</option>';
									else
										echo '<option value = "'.$reservationKind['DictionaryDetail']['name'].'">'.$reservationKind['DictionaryDetail']['express_name_ko'].'</option>';
								}
								?>
							 </select>
						</TD>
						<TD align="center" style="border-bottom:1px solid;"> 시작날짜 </TD>
						<TD align="center" style="border-bottom:1px solid;"> <input type='text' readonly='readonly' id='search_from' name='search_from' class='date-pick' value='<?php if(isset($from)) echo $from;?>' style="width: 75px;"> </TD>
						<TD rowspan="2" align="center" style="padding:5px;">
							<div style="padding:5px;"><input type='image' onClick="return checkInput()" name = 'search_submit' src="<?=c_path();?>/image/button/search-ko.png" alt="Submit"></div>
							<div style="padding:5px;"><input type='image' onClick="return resetValues()" name = 'search_submit' src="<?=c_path();?>/image/button/reset-ko.png" alt="Submit"></div>
						</TD>	
					</TR>
					<TR>
						<TD align="center"> <div>수업상태</div></TD>
						<TD align="center">
							<select name="lesson_status" id = "sel">
							   <option value='all' selected >전부</option>
							   <?php
								foreach($lessonStatus as $lessonStatu){
									if(isset($l) && $lessonStatu['DictionaryDetail']['express_name_en'] == $l)
										echo '<option value = "'.$lessonStatu['DictionaryDetail']['name'].'" selected>'.$lessonStatu['DictionaryDetail']['express_name_ko'].'</option>';
									else
										echo '<option value = "'.$lessonStatu['DictionaryDetail']['name'].'">'.$lessonStatu['DictionaryDetail']['express_name_ko'].'</option>';
								}
								?>
							</select>
						</TD>
						<TD align="center"> 종료날짜 </TD>
						<TD align="center"> <input type='text' readonly='readonly' id='search_to' name='search_to' class='date-pick' value='<?php if(isset($to)) echo $to;?>' style="width: 75px;"> </TD>
					</TR>
				</thead>
				</TABLE>
				</div>
				<div style="height:10px;"></div>
				<div style="font-family:arial;letter-spacing:2px;font-size:1em;"><font><b>강사님을 고객님의 스카이프 연락처에 추가를 해 주셔야 안정적으로 수업을 받으실 수 있습니다.</b></font></div>
				
				<!-- 2014-04-10 -->
				<br>
				<div class="div-container">
					<div class="">
						<table width="100%">
							<tr>
								<td><a href="#" class="icons-info" data-description="Manager Contacts"><img
										src="<?php echo $cubictalkko_path;?>/image/mypage/managers-contact-ko.png"
										border="0" width="" height="" alt="Manager Contacts" title="Manager Contacts"></a></td>
								<td><a href="#" class="icons-info" data-description="Icons Description"><img
										src="<?php echo $cubictalkko_path;?>/image/mypage/icons-description-ko.png"
										border="0" width="" height="" alt="Icons Description" title="Explained other icons"></a></td>
								<td><a href="javascript:void(0)" onclick="popVid()"><img
										src="<?php echo $cubictalkko_path;?>/image/mypage/cancel_schedules_video_tutorial.png"
										border="0" width="" height="" alt="Step Video"></a></td>
							</tr>
						</table>
					</div>
				</div>
				<!-- end -->
				<br>
				</FORM>
				  
	              <!-- 2010-10-10 END COMMON --> 
	              <div class='table-infos'> 
				<TABLE  border="0" cellspacing="0" cellpadding="0">
				<TR><TD>                 
                  <IMG align="left" height=25 alt=수업일정 src="<?=c_path();?>/img/mypage.png" >
                </TD></TR>
                
                <TR><TD> 

                  <TABLE class="table table-bordered" cellSpacing=0 cellPadding=2 width="670" 
                  bgColor=#cccccc border=0>
                    <TBODY>
                    <TR>
                     <TD background=<?=c_path();?>/img/bg_gra_green.jpg 
                      bgColor=#ffffff height=20>
                        <DIV class=style7 align=center>번호</DIV></TD>
                      <TD background=<?=c_path();?>/img/bg_gra_green.jpg 
                      bgColor=#ffffff height=20>
                        <DIV class=style7 align=center>수업일시</DIV></TD>
                      <TD background=<?=c_path();?>/img/bg_gra_green.jpg 
                      bgColor=#ffffff height=20>
                        <DIV class=style7 align=center>강사</DIV></TD>
                      <TD background=<?=c_path();?>/img/bg_gra_green.jpg 
                      bgColor=#ffffff height=20>
                        <DIV class=style7 align=center>코멘트</DIV></TD>
                     
                      <TD background=<?=c_path();?>/img/bg_gra_green.jpg 
                      bgColor=#ffffff height=20>
                        <DIV class=style7 align=center>예약방법</DIV></TD>
                      
                       <TD background=<?=c_path();?>/img/bg_gra_green.jpg 
                      bgColor=#ffffff height=20 width="100">
                        <DIV class=style7 align=center>전화걸기/추가하기<span style="display:block;">skype 아이디</span>결제일</DIV></TD>
                        
                          <TD background=<?=c_path();?>/img/bg_gra_green.jpg 
                      bgColor=#ffffff height=20>
                        <DIV class=style7 align=center>수업상태</DIV></TD>
                    
                  
                      <TD background=<?=c_path();?>/img/bg_gra_green.jpg 
                      bgColor=#ffffff>
                        <DIV class=style7 align=center>수업취소</DIV></TD>
                        <TD colspan="1" background=<?=c_path();?>/img/bg_gra_green.jpg 
                      bgColor=#ffffff>
                        <DIV class=style7 align=center>불만</DIV></TD>
                        <TD colspan="1" background=<?=c_path();?>/img/bg_gra_green.jpg 
                      bgColor=#ffffff>
                        <DIV class=style7 align=center>코</DIV></TD>
                        </TR>
                    
                      <?php if($lessons==null || $lessons==""){ ?>
                      <TR> <TD  bgcolor="#FFFFFF" colspan="10">현재 예약하신 수업이 없습니다.</TD> </TR>
                      <?php } else {
   $strEquipment = "";
   $title="Unknown Class cause by reservation_kinds";
   $color="#666666";
//   this are the Symbol from Database LANDLINE10,MOBILE10,LANDLINE20,MOBILE20
   $telephoneMobile = array('LANDLINE10','MOBILE10','LANDLINE20','MOBILE20','TTEM3','TMEM3','TTEM2','TMEM2');
   $telMobile = "";                   	 
                      	$i = 0;
                      	foreach ($lessons as $lesson) {

                      		// Highlight the schedule today
                      		$scheduleToday = '';
                      		if(date('Y-m-d',strtotime($lesson['Lesson']['reserve_time'])) == $today){
                      			$scheduleToday = ' scheduleToday ';
                      		}
                      		
   $reservation_kinds = $lesson['Lesson']['reservation_kinds'];
   $telMobile = "";
   if (in_array($reservation_kinds, $telephoneMobile)) {
   	$pregLandline = preg_match('/^LANDLINE/', $reservation_kinds, $matchLandline);
   	$pregMobile = preg_match('/^MOBILE/', $reservation_kinds, $matchMobile);
   	$minutes = ""; // Minutes is either 10 or 20 maybe unknown it found to ex. LANDLINE10 -> 10, LANDLINE20 -> 20
   	if(isset($matchLandline[0]) && $matchLandline[0]=='LANDLINE'){
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
   	}elseif(isset($matchMobile[0]) && $matchMobile[0]=='MOBILE'){
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
   	 
   	// For new Reservation that belongs to Telephone or Mobile or Others
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
    $telMobile = "<div class=\"row text-center\" align=\"center\"><img src='".c_path()."/image/icon/".$strEquipment."_class_icon.png' width='40%' border='0' title='$title' alt='$title'>$minutes</div>";
   }
                     ?>
                      <TR class="<?php echo $scheduleToday;?>">
                      <TD background=<?=c_path();?>/img/bg_gra_gray.jpg 
                      bgColor=#f5f5f5>
                        <DIV align=center><?php echo $rCount+=1;?></DIV></TD>
                      <TD background=<?=c_path();?>/img/bg_gra_gray.jpg 
                      bgColor=#f5f5f5 width="100">
                        <DIV align=center><b><?php echo $fomattedDays[$i];?></b></DIV></TD>
                      <TD bgColor=#ffffff>
                        <DIV align=center><A 
	                        href="<?=c_path()?>/teachers/profile/<?= $lesson['Teacher']['id'];?>"><IMG 
	                        height=45 alt="" src=<?=c_path().$lesson['Teacher']['img_file'];?> 
	                        width=60 border=0></A></DIV>
	                        <DIV align=center><A href="<?=c_path();?>/comments/index/<?php echo $loginInfo['User']['id'];?>">
	                        <?php echo $lesson['Teacher']['tea_name'];?> 
	                        </A></DIV></TD>
                      <TD bgColor=#ffffff>
                        <DIV align=center>
                        <!-- LESSONに該当するコメントをみる -->
                        <A href="<?=c_path();?>/comments/index/<?php echo $lesson['Lesson']['id'];?>" 
                        target=_blank><IMG height=31 alt=코멘트보기 
                        src="<?php echo $cubictalkko_path;?>/image/icon/icon-link/icon-link-users-comment-ko.png" width=54 
                        border=0></A></DIV></TD>
                      <?php 
                      	$lesson_kinds = $lesson['Lesson']['reservation_kinds'];
                      	$lesson_kinds_text = null;
                      	if($lesson_kinds == 'P'){
                      		$lesson_kinds_text = $lesson['Lesson']['spend_point'].'포인트';
                      	}else if($lesson_kinds == 'K'){
                      		$lesson_kinds_text = '큐빅';
                      	}else if($lesson_kinds == 'M'){
                      		$lesson_kinds_text = '일괄예약';
                      	}else if($lesson_kinds == 'MD'){
                      		$lesson_kinds_text = '낮고정';
                      	}else if($lesson_kinds == 'M2'){
                      		$lesson_kinds_text = '주2회 고정';
                      	}else if($lesson_kinds == 'M3'){
                      		$lesson_kinds_text = '주3회 고정';
                      	}else if($lesson_kinds == 'T'){
                      		$lesson_kinds_text = '체험수업';
                      	}
                      	
                      	
                      	// reservation_kinds = T
                      	//if($lesson_kinds == 'T'){
                      	//	$lesson_kinds = 'P';
                      	//}
                      ?> 
                      
                      <TD bgColor=#ffffff>
						<DIV align=center title='<?php echo $lesson_kinds_text?>'>
						<?php
						$icon_name = 'fix'.$lesson_kinds.'Days';
						if(isset($icon_message[$icon_name])){
							$icon_img = $icon_message[$icon_name][0]['icon_img_name'];
							
							echo '<img src="';
							echo $cubictalkko_path;
							echo '/image/mypage/';
							echo $icon_img;
							echo '" border="0" title="';
							echo $lesson_kinds_text;
							echo '" width="40" height="40" alt="';
							echo $lesson_kinds_text;
							echo '">';
						}else{ echo $lesson_kinds_text; }
						?></DIV>		
						</TD>
                      
                       <TD bgColor=#ffffff>
   <?php
   echo $telMobile;
   if (strlen(str_replace(' ', '', $telMobile)) < 1) {
   ?>
   <div class="row text-center" align="center">
    <a href="skype:<?php echo $lesson['Teacher']['tea_skype_id'];?>?call">
    <img src="<?php echo c_path();?>/image/mypage/skype_call.png" border="0" width="20" height="20" alt="Skype Me™!" /></a>
    <a href="skype:<?php echo $lesson['Teacher']['tea_skype_id'];?>?add">
    <img src="<?php echo c_path();?>/image/mypage/skype_add.png" border="0" width="20" height="20" alt="Add me to Skype" /></a> 
   </div>
   <div class="row text-center" align="center"><b><?php echo $lesson['Teacher']['tea_skype_id'];?></b></div>
   <?php
   } // end condition for if (strlen(str_replace(' ', '', $telMobile)) < 1) {;
   if (isset($lesson['Payment']['id'])) {
    echo "<div class=\"row text-center\" align=\"center\">".date("Y-m-d", strtotime($lesson['Payment']['created']))."</div>";
   }
   ?>
   
                       </TD>
                    
                      <TD bgColor=#ffffff>
                      <?php if($lesson['Lesson']['lesson_status'] == 'F' && $lesson['Lesson']['evaluate_status'] == 'N' ){?>
                        <DIV align=center><A href="javascript:evaluate_submit(<?= $lesson['Lesson']['id'];?>,<?=$lesson['Teacher']['id'];?>);"><b>평가하기</b></A> </DIV>
                       <?php }elseif($lesson['Lesson']['lesson_status'] == 'F' 
                      	&& $lesson['Lesson']['evaluate_status'] == 'Y'){?>
                      		<DIV align=center><b>평가완료</b></DIV>						
                       <?php }elseif($lesson['Lesson']['lesson_status'] == 'A'){?>	
                      		<DIV align=center><b>결석</b></DIV>
                       <?php }elseif($lesson['Lesson']['lesson_status'] == 'S'){?>   
                      		<DIV align=center><b>강사변경</b></DIV>					
                       <?php }elseif($lesson['Lesson']['lesson_status'] == 'C'){?>  
                      		<DIV align=center><b>고객님의 수업취소</b></DIV>
                       <?php }elseif($lesson['Lesson']['lesson_status'] == 'E' || $lesson['Lesson']['lesson_status'] == 'EM'){?> 
                      		<DIV align=center><FONT color=#ff0000><b>수업기간연장</b></FONT></DIV>
                      <?php }elseif($lesson['Lesson']['lesson_status'] == 'M'){?> 
                      		<DIV align=center><FONT color=#ff0000><b>수업기간 연장(강사벌칙)</b></FONT></DIV>	
                       <?php }elseif($lesson['Lesson']['lesson_status'] == 'P'){?>  
                      		<DIV align=center><FONT color=#ff0000><b>포인트대체</b></FONT></DIV>									
                       <?php }else {?>
                       		<DIV align=center><b>-</b></DIV>
                       <?php }?>
                       </TD>
                      <TD bgColor=#ffffff>
                       <?php
                       // All lesson_status is not R or (reserve) cannot cancel the button will not show up
                       if($lesson['Lesson']['lesson_status'] != 'R'){
                       }else {
                       		$diff= check_time_diff(strtotime($lesson['Lesson']['reserve_time']),1);
                       		if($diff == 'Y'){
                       ?> 
                        <DIV align=center>
                        <a href="javascript:void(0)" onclick="go_sub(<?=$lesson['Lesson']['id'];?>)">
                        <img src="<?=c_path();?>/image/mypage/icon-link-cancel-ko.png" border="0" title="취소" alt="취소" width="40" height="40"></a>
                        </DIV></TD>
                       <?php
                       		}
                       	}
                       ?>
                       <!-- button comments -->
                       <TD bgColor=#ffffff align=center>
                       		<? if ($lesson['Lesson']['reserve_time'] >= $today && $lesson['Lesson']['reserve_time'] <= date("Y-m-d 02:00", strtotime($today)+(((60*60)*24))) ) {?>
                       		<?php $strRemove = array("-",":"," ");?>
                       		<?php $_holder = str_replace($strRemove, '_', $lesson['Lesson']['reserve_time']);?>
                       		<form id="form<?=$_holder.$lesson['Teacher']['id'];?>" name="form3" method="post">
	                            <INPUT type=hidden id="lesson_id" name="lesson_id" value="<?=$lesson['Lesson']['id'];?>">
	                        	<INPUT type=hidden id="tea_id" name="tea_id" value="<?=$lesson['Teacher']['id'];?>">
	                        	<INPUT type=hidden name="lesson_time" value="<?=$lesson['Lesson']['reserve_time'];?>">
	                        	<INPUT type=hidden name="lesson_status" value="<?=$lesson['Lesson']['lesson_status'];?>">
	                        	<a href="javascript:void(0)" onclick="to_complains('<?=$_holder.$lesson['Teacher']['id'];?>')">
	                        	<img src="<?php echo $cubictalkko_path;?>/image/mypage/icon-link-complain-ko.png" border="0" title="불만" alt="불만" width="40" height="40"></a>
                       		</form>
                       		<? } ?>
                       </TD>
                       <!-- end -->
                       
                       <!-- Levy Aviles 2012-10-26 -->
                       <TD bgColor=#ffffff align=center>
                       <? if ($lesson['Lesson']['reserve_time'] >= $today && $lesson['Lesson']['reserve_time'] <= date("Y-m-d 02:00", strtotime($today)+(((60*60)*24))) ) {?>
                       	<a id="class_comment" href="javascript:void(0)" data-description="Class Comment" data-required-id="<?php echo $lesson['Lesson']['id'];?>">
                       	<img src="<?php echo $cubictalkko_path;?>/image/mypage/icon-link-comment-ko.png" border="0" title="코" alt="C" width="40" height="40"></a>
                       <? } ?>
                       </TD>
                       <!-- end -->
                       <!-- Levy Aviles 2012-10-26 -->
                       </TR>
                       
                    <?php 
                      	$i++;
                      	} ?>
                      <?php }  ?>
                      </TBODY></TABLE>
<?php
//2012-11-04 Paging Start
if(isset($lessons) && $totalLesson > 0 && $paginator_session['count'] > $paginator_session['current']){
$page=$paginator->prev('<< '.__('previous', true), array(), null,array('class'=>'_disabled'));
$paging_html = <<<HTML
<div class="">
<div class="_main_content">
<div class="_content _prev1"><div class="_pages">{$page}</div></div>
HTML;
$page=$paginator->numbers(array('tag' => 'div class="_pages"','separator' => '','class' => '_active'));
$paging_html .= <<<HTML
<div class="_content _number1">{$page}<div class="clearfix"></div></div>
HTML;
$page=$paginator->next(__('next', true).' >>', array(), array(),array('class'=>'_disabled'));
$paging_html .= <<<HTML
<div class="_content _next1"><div class="_pages">{$page}</div></div>
HTML;
$page=$paginator->counter();
$paging_html .= <<<HTML
<div class="_content _counter1"><div class="_pages">{$page}</div></div>
<div class="clearfix"></div>
</div>
<div class="clearfix"></div>
</div>
HTML;
echo $paging_html;
}
?>
            
            <!--2012-11-04 Paging En  -->
            
                  <P>&nbsp;</P>
              </TD></TR>
            </TABLE>
            
</div> <!-- // -->

<form name="evaluate" method="post" action="javascript:evaluate_submit(document.evaluate);" enctype="multipart/form-data" autocomplete="off">
<INPUT type="hidden" name="lesson_id" value="">
<INPUT type="hidden" name="teacher_id" value="">
</form>

<script type="text/javascript">
function skypeid(id,e) {
 var skype_id;
 if (e == 'edit') {
  skype_id = $('span#'+id).find("font").html();
  $('#'+id).find("font").empty().html('<input type="text" id="'+id+'" name="'+id+'" value="'+skype_id+'" maxlength="32" size="18">');
  $('.'+e+"_"+id).hide(); $('.update_'+id).show(); $('.cancel_'+id).show();
 }

 if (e == 'cancel') {
  skype_id = $('input#'+id).val();
  $('span#'+id).find("font").empty().html(skype_id);
  $('.'+e+"_"+id).hide(); $('.update_'+id).hide(); $('.edit_'+id).show();
 }
	
 if (e == 'update') {
  skype_id = $('input#'+id).val();
  var iChars = "!@#$%^&*()+=[]\\\';/{}|\":<>?";
  var specialChars=false;
  if (skype_id.length > 5) {
   for (var i = 0; i < skype_id.length; i++) {
    if (iChars.indexOf(skype_id.charAt(i)) != -1) {
     specialChars=true;
    }
   }
   if (specialChars==false) {
    document.changeskype.user_id.value = skype_id;
    document.changeskype.field.value = id;
    document.changeskype.action = '<?php echo c_path();?>/lessons/changeskypeid/'+id+"/";
    document.changeskype.submit();
   } else {
    alert ("Your SKYPE ID has special characters. \nThese are not allowed.\n Please remove them and try again.");
   }
  } else {
   alert ("Your Skype ID must have between 6 and 32 characters");
  }
 }
 return false;
}

function evaluate_submit(lesson_id,teacher_id)
{
	document.evaluate.lesson_id.value = lesson_id;
	document.evaluate.teacher_id.value = teacher_id;
	document.evaluate.action = '<?=c_path()?>/evaluates/evaluate';
	document.evaluate.submit();
}
</script>


</td></tr></table>
<!-- /어학과정 -->

<!-- Modal -->
<?php
if(strlen($icons_html) > 0){
?>
<div class="div-container">
	<div class="">
		<div id="iconsmodal" class="modal fade no-border-radius" role="dialog">
			<div class="modal-dialog no-border-radius modal-lg">
				<div class="modal-content no-border-radius">
					<!-- 
					<div class="modal-header no-border-radius">
						<button type="button" class="close" onclick="closemod()"
							aria-hidden="true">&times;</button>
						<h4 class="modal-title text-left"></h4>
					</div>
					 -->
					<div id="modalBody" class="modal-body">
						<div id="_pages">
							<div class="current-pages-show icons-description"><table class="table table-bordered custome1-table-header">
								<thead><tr><td>Icons</td><td>Description (En)</td><td>Description (Ko)</td></tr></thead>
								<?php
								echo $icons_html;
								?>
								</table></div>
							<div class="current-pages-hide manager-contracts"><table class="table table-bordered custome1-table-header">
								<thead><tr><td colspan="2">##</td><td>Skype Contact</td><td>Action</td></tr></thead>
								<tr><td>한국인 관리자</td>
									<td width="50"></td>
									<td>cubictalk.japanoffice</td>
									<td><a href="skype:cubictalk.japanoffice?call">
									<img src="<?php echo $cubictalkko_path;?>/image/mypage/skype_call.png" width="25" height="25" alt="Skype Me™!"/></a>
	   								<a href="skype:cubictalk.japanoffice?add">
	   								<img src="<?php echo $cubictalkko_path;?>/image/mypage/skype_add.png" width="25" height="25" alt="Add me to Skype"/></a></td>
								</tr>
								<tr><td>필리핀인 매니저1</td>
									<td width="50"><img src="<?php echo $cubictalkko_path;?>/img/teacher/myjie.png" border="0" width="60" height="57"
									alt="T. Myjie" title="T. Myjie"></td>
									<td>cubictalk.myjie</td>
									<td><a href="skype:cubictalk.myjie?call">
									<img src="<?php echo $cubictalkko_path;?>/image/mypage/skype_call.png" width="25" height="25" alt="Skype Me™!"/></a>
	   								<a href="skype:cubictalk.myjie?add">
	   								<img src="<?php echo $cubictalkko_path;?>/image/mypage/skype_add.png" width="25" height="25" alt="Add me to Skype"/></a></td>
								</tr>
								<tr><td>필리핀인 매니저2</td>
									<td width="50"><img src="<?php echo $cubictalkko_path;?>/img/teacher/disma.jpg" border="0" width="60" height="57"
									alt="T. Disma" title="T. Disma"></td>
									<td>cubictalk.disma</td>
									<td><a href="skype:cubictalk.disma?call">
									<img src="<?php echo $cubictalkko_path;?>/image/mypage/skype_call.png" width="25" height="25" alt="Skype Me™!"/></a>
	   								<a href="skype:cubictalk.disma?add">
	   								<img src="<?php echo $cubictalkko_path;?>/image/mypage/skype_add.png" width="25" height="25" alt="Add me to Skype"/></a></td>
								</tr>
								</table></div>
							<div class="current-pages-hide substitute-whatisthis-message">
								<div class="text-left"><div id="infos"><?php echo $ChangeLanguageTo['SubstitutewhatisthisMessage'];?></div></div></div>
							<div class="current-pages-hide change-skype-id-profile">
								<div class="text-left"><div id="infos"><?php echo $ChangeLanguageTo['SkypeIdProfile'];?></div></div></div>
							<div class="current-pages-hide teacher-lesson-comment">
								</div>
							<div class="current-pages-hide how-to-use-skype">
								<div>
									<div style="margin-bottom: 10px;"><a class="btn btn-default btn-primary" href='http://blog.naver.com/buko33/189386084' target='_blank'>스카이프에 연락처 추가 방법</a></div>
									<div style="margin-bottom: 10px;"><a class="btn btn-default btn-primary" href='http://blog.naver.com/buko33/189386677' target='_blank'>스카이프 일반적인 사용방법</a></div>
									<div style="margin-bottom: 10px;"><a class="btn btn-default btn-primary" href='http://blog.naver.com/buko33/189396670' target='_blank'>스마트폰에서 스카이프 사용방법</a></div>
									<div style="margin-bottom: 10px;"><a class="btn btn-default btn-primary" href='http://blog.naver.com/buko33/203853276' target='_blank'>스카이프에서 전자칠판 사용 샘플보기</a></div>
								</div>
								</div>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="close" onclick="closemod()"aria-hidden="true">&times;</button>
						<h4 class="modal-title text-left"></h4>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php
}
?>
<!-- 배너 -->
<table border="0" cellspacing="0" cellpadding="0" width="652">

</table>
<!-- /배너 -->
<?	if ( $paymentInfo == true ) { ?>
<div id="prev-x-content" class="prev-x-content"></div>
<div id="bg-x-content" class="bg-x-content"></div>
<?	} ?>

<div class="formCubictalkMaterial">
<form id="formCubictalkMaterial" name="" action="" method="post" target="" enctype=""><input type="hidden" id="book_page" name="page" value=""></form>
</div>

<script type="text/javascript">
	var screenH = screen.height;
	var screenW = screen.width;
	var icons_messages = <?php echo json_encode($icon_message);?>;
	var modal_title = {"Icons Description":"icons-description","Manager Contacts":"manager-contracts","Substitute Message":"substitute-whatisthis-message"
		,"Skype Id Profile":"change-skype-id-profile","Class Comment":"teacher-lesson-comment","How to use skype":"how-to-use-skype"};
	var no_script = true;
	var data_description = 'Icons Description';
	var add_script = '<script type="text/javascript" src="<?php echo $cubictalkko_path;?>/js/jquery-1.11.1.min.js"><\/script><script type="text/javascript" src="<?php echo $cubictalkko_path;?>/js/bootstrap.min.js"><\/script>';
<?php
if (isset($checkStatusHOLD) && intval($checkStatusHOLD) > 0) {
?>
function checkStatusHold(id) {
 var screenW = screen.width;
 var screenH = screen.height;
 var url = "<?php echo c_path();?>/lessons/checkStatusHold/";
 var named = "Lesson";
 var rplce = false;
 windowW = screenW;
 if (screenW > 768) {
  windowW = screenW * 0.40;
  lLeft = (screenW - windowW) * 0.15;
 }
 windowH = screenH;
 if (screenH > 736) {
  windowH = screenH * 0.75;
  lTop = (screenH - windowH) / 4;
 }
 var specs = 'width='+windowW+',height='+windowH+',top=0,left=0,scrollbars=yes';
 Lesson = window.open(url,named,specs);
 return false;
}
<?php
}
?>

<?	if ( $paymentInfo == true ) { ?>
$("a.preview").click(function( event ){
	event.preventDefault();
	var id = $(this).attr('id');
	var xTopCon = '<div id="top-x-content"><div class="top-x-content title" align="left"></div> <div class="top-x-content close"><input type="button" onclick="xCloseCon()" value="x"></div></div>';
	var xBottomCon = '<div id="bottom-x-content" align="right"> <div class="bottom-x-content close" ><input type="button" onclick="xCloseCon()" value="close"></div> </div>';
	var xIframeCon = '<div id="iframe-x-content" class="iframe-x-content" style="width:570px; height:515px;" align="left"></div>';
	if ( document.getElementById('prev-x-content') ) {
		$("div#prev-x-content").empty().append( '<div class="view-x-content">' + xIframeCon + xBottomCon + '</div>');
				
		if ( document.getElementById('iframe-x-content') ) {
			var xIframeCon = '<iframe id="iframeOn" frameborder="0" width="560" height="510" ></iframe>';
			$("div#iframe-x-content").empty().append( xIframeCon );

			if ( document.getElementById('iframeOn') )
				document.getElementById('iframeOn').src = '<?=c_path();?>/lessons/myPaymentHistory/';
		}
		document.getElementById('bg-x-content').style.display = 'block';
		document.getElementById('bg-x-content').style.height=window.innerHeight+"px";
			
		if ( document.getElementById('bg-x-content') )
			$("div.bg-x-content").css({ opacity: "0.2" });
		
		$("div#iframeOn").css({ top:parseInt(((window.innerHeight-$("div#prev-x-content").height())/2))+"px", left:parseInt(((window.innerWidth-$("div#prev-x-content").width())/2))+"px" });
		$("div#prev-x-content").css({ display:'block', width:'578px', height:'548px', border:'1px solid', top:parseInt(((window.innerHeight-$("div#prev-x-content").height())/2))+"px", left:parseInt(((window.innerWidth-$("div#prev-x-content").width())/2))+"px" });
		$("div#prev-x-content").draggable();
	}
});

function xCloseCon () {
	if ( document.getElementById('prev-x-content') ) {
		$("div#prev-x-content").empty().css({ display:'none' });
		document.getElementById('bg-x-content').style.display = 'none';
	}
}
<?	} ?>
<?	if ( isset($getEvaerCodeThruPayment) && count($getEvaerCodeThruPayment) > 0 && isset($getEvaerCodeThruPayment["codeDisplay"]) && $getEvaerCodeThruPayment["codeDisplay"] == true ) {?>
function eCde(){
	if ( document.getElementById("evaerCode") ) {
		var eCdeW = document.getElementById("evaerCode").offsetWidth;
		var lCdeW = $(".evaerCode").width();
		var lCdeH = $(".evaerCode").height();
		
		var topBottom = ((screenH - lCdeH) - 10) / 2;
		var leftRight = ((screenW - lCdeW) - 10) / 2;

		$(".evaerCode").css({top:topBottom+"px", left:leftRight+"px", position:"fixed"}).slideToggle();	
	}
	return false;
}
<?	} ?>

function closemod(){
	$('#iconsmodal').modal('hide');
	return false;
}

$(document).ready(function(){
<?php
if(strlen($icons_html) > 0){
?>
	$('.icons-info').click(function(){
		height = $(window).height() - 130;
		if(no_script){
			$('#iconsmodal').prepend(add_script);
			no_script = false;
		}
		data_description = $(this).attr('data-description');
		if(modal_title[data_description] !== undefined){
			$('#iconsmodal').find('.modal-body').attr('style','');
			
			$('#iconsmodal').find('.current-pages-show').each(function(key,value){
				$(this).removeClass('current-pages-show').addClass('current-pages-hide').hide();
			});
			
			$('#iconsmodal').find('.modal-title').empty().prepend(data_description);
			$('.' + modal_title[data_description]).removeClass('current-pages-hide').addClass('current-pages-show').show();
			$('#iconsmodal').modal('show');
			$("#iconsmodal .modal-backdrop").css({'z-index':0});
			$('#iconsmodal').find('.modal-body').css({'max-height':height,'overflow-y':'scroll'});
		}
		return false;
	});
<?php
}
?>
	setInterval(function() {
		if($("#effectFadeInOut").hasClass("effectFadeInOut")){
			$("#effectFadeInOut").toggleClass("effect");
		}
	}, 1000);
});
 
 function goToElmnt (id) {
  var p = $(id).position();
  $(window).scrollTop(p.top);
 }

 function popVid(){
	if (document.getElementById('popVid').style.display!="block"){document.getElementById('popVid').style.display="block";}
	var closeAre = '<div class="close" align="right"><span><a href="javascript:void(0)" onclick="divClose()" style="padding:5px;font-size:18px; font-family:arial; font-weight:bold; letter-spacing:3px;"><b>close</b></a></span></div>';
	$('div#df').empty().html(closeAre+'<div><iframe width="560" height="315" src="//www.youtube.com/embed/kDRhzPIZN9o" frameborder="0" allowfullscreen></iframe></div>');
	document.getElementById('db').style.display = 'block';
	document.getElementById('df').style.display = 'block';
	if (document.getElementById('df')) {
		var getPosition="df";
		var dfStyle={
				"df":{ top:parseInt(((window.innerHeight-$("#df").height())/2))+"px", left:parseInt(((window.innerWidth-$("#df").width())/2))+"px"},
				"img-result":{top:parseInt(((window.innerHeight-328)/2))+"px", left:parseInt(((window.innerWidth-541)/2))+"px"}};
		if (document.getElementById('img-result')){getPosition="img-result";}
		$('#df').css(dfStyle[getPosition]);
	}
	document.getElementById('db').style.height=window.innerHeight+"px";
	document.getElementById('db').style.position="fixed";
	return false;
}

 var once_class_comment = new Array;
 $('a#class_comment').click(function(){
	height = $(window).height() - 130;
	if(no_script){
		$('#iconsmodal').prepend(add_script);
		no_script = false;
	}
	data_description = $(this).attr('data-description');
	data_required_id = $(this).attr('data-required-id');
	if(modal_title[data_description] !== undefined){
		if(once_class_comment['cc_' + data_required_id] === undefined){
			$.ajax({
				type: 'POST',
				url: '<?php echo $cubictalkko_path;?>/comments/getTeacherComment/',
				dataType: 'json',
				data: {id: data_required_id},
				async: false,
				success: function(data){
					once_class_comment['cc_' + data_required_id] = data;
				},
				error: function(msg){
					alert(msg + '\nThis page will reload;');
					// location.reload();
				}
			});
		}
		if(once_class_comment['cc_' + data_required_id] !== undefined){
			$('#iconsmodal').find('.modal-body').css({'max-height':height,'overflow-y':'scroll'});
			$('.' + modal_title[data_description]).empty().prepend(once_class_comment['cc_' + data_required_id].getTeacherComment);
			$('#iconsmodal').find('.current-pages-show').each(function(key,value){
				$(this).removeClass('current-pages-show').addClass('current-pages-hide').hide();
			});
			$('#iconsmodal').find('.modal-title').empty().prepend(once_class_comment['cc_' + data_required_id].class_comment);
			$('.' + modal_title[data_description]).removeClass('current-pages-hide').addClass('current-pages-show').show();
			$('#iconsmodal').modal('show');
			$("#iconsmodal .modal-backdrop").css({'z-index':0});
		}
	}
	return false;
});

function bookMaterialLink(e,i,id,page){
	if(id && page){
		<?php // <!-- Begin submit by Form ?>
		var oF = document.getElementById('formCubictalkMaterial');
		document.getElementById('book_page').value = page;
		oF.target = "_blank";
		oF.action = "<?php echo $cubictalkko_path;?>/ftpbooks/show_book/" + id;
		oF.submit();
		oF.target = "";
		<?php // --> End submit by Form ?>
	}
	else {
		alert('Sorry! There is a problem in retrieving location.');
	}
	return false;
}

function bookErrorImg(e){
	e.src="<?php echo $cubictalkko_path;?>/ftpbook/book_sorry_no_available_image.png";
	return false;
}
</script>
<?php include ('bottom.php');?>
<?php
	function check_time_diff($time ,$diff) {
		
		$result = 'Y';
		
		$time_diff = (time()+ 60*60*$diff) -($time);
		
		//
		if($time_diff < 0 ){
			$result = 'Y';
		}else{
			$result = 'N';
		}
		
		return $result;
	}

?>
