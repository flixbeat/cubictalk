<?php
include("top.php");
$paginator->options(array('url'=>$this->passedArgs));
if(isset($myhistory) && count($myhistory) > 0){
$paginator_session = $paginator->params;
$rCount=0; // row counting or data counting initialize;
if(isset($paginator_session['paging']['Lesson']) && count($paginator_session['paging']['Lesson'])){
$paginator_session=$paginator_session['paging']['Lesson'];
$rCount=(($paginator_session['page']-1)*$paginator_session['defaults']['limit']);
}
}
$cubictalkko_path=c_path();
$teacher_comment = <<<HTML
<link rel="stylesheet" type="text/css" href="{$cubictalkko_path}/css/cubictalk-modal.css">
<link rel="stylesheet" type="text/css" href="{$cubictalkko_path}/css/cubictalk-styled.css">
HTML;
echo $teacher_comment;
?>
<style type="text/css">
*{-webkit-box-sizing:border-box;-moz-box-sizing:border-box;box-sizing:border-box}
:before,:after{-webkit-box-sizing:border-box;-moz-box-sizing:border-box;box-sizing:border-box}
html{font-size:10px;-webkit-tap-highlight-color:rgba(0,0,0,0)}
body{font-family:"Helvetica Neue",Helvetica,Arial,sans-serif;font-size:14px;line-height:1.42857143;color:#333;background-color:#fff}
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
.tdincomments:hover { cursor:pointer; background:#F3E2A9; }
div.lesson_comment {
	font-family:arial; font-size:11px; background:#f18103; color:#FFF; letter-spacing:3px;
	-webkit-border-top-left-radius: 5px; -webkit-border-top-right-radius: 5px; -moz-border-radius-topleft: 5px; -moz-border-radius-topright: 5px;
	border-top-left-radius: 5px; border-top-right-radius: 5px; border:1px solid #f18103; padding:2px 5px 2px 5px; width:529px; max-width:529px; min-width:529px;
}
textarea.txtarea_comment { width:535px; max-width:535px; min-width:535px; height:134px; max-height:134px; min-height:134px; }

._main ._pages,._pages>a:link{font-size:110%;font-weight:bold}
._main,._main:after,_main:before{content: " ";}
._main ._content{position:relative;}
._content,._pages{display:inline-block;text-align:center;margin-top:5px;margin-left:5px;}
._content{padding:10px 11px;}
._pages{padding:5px 10px;border:1px solid #ccc;color:#ff0000}
._pages>._disabled{color:#ccc}
._prev1,._number1,._number1>._pages,._next1{float:left}
._counter1{float:right}
.text-center{text-align:center}
.paging>.clearfix,._main>._clearfix{content:" ";display:block;clear:both}
._close{opacity:1;color:#888;font-size:21px;}
</style>
<script type="text/javascript">
var oForm;
function go_myhistory2(kinds)
{
	oForm=document.form1;
	oForm['reservation_kinds'].value = kinds;
	oForm.action = "<?php echo c_path();?>/lessons/myhistory";
	oForm.submit();
	oForm="";
	return false;
}
function go_myhistory(kinds)
{
	oForm=document.form1;
	oForm['lesson_status'].value = kinds;
	oForm.action = "<?php echo c_path();?>/lessons/myhistory";
	oForm.submit();
	oForm="";
	return false;
}
function evaluate_submit(lesson_id,teacher_id)
{
	oForm=document.evaluate;
	oForm['lesson_id'].value = lesson_id;
	oForm['teacher_id'].value = teacher_id;
	oForm.action = '<?php echo c_path();?>/evaluates/evaluate';
	oForm.submit();
	oForm="";
	return false;
}
</script>
<div class="div-container">
	<div class="text-left"><img src="<?php echo $cubictalkko_path;?>/image/title/lessons-myhistory-title-ko.png" border="0"></div>
</div>

<?php include('views/common/nav_tabs.php');?>

<div class="container-fluid">
	<div class="rows">
		<img height="25" alt="지난수업" src="<?php echo c_path();?>/img/tbl-lessons-myhistory-ko.png" width="670">
		<TABLE cellSpacing="1" cellPadding="2" width="670" bgColor="#cccccc" border="0">
		<TBODY>
		<TR>
			<TD bgColor=#f9f7f2>
				<P>지금까지의 수업내용을 조회하실 수 있습니다.</P>
				<P>수업내용은 6개월간 보존되여 있습니다.<BR>【수업평가】에서「<SPAN class="sky_b">
				평가하기</SPAN>」링크를 클릭하시면  강사의 강의 내용을 평가 하실 수 있 습니다.
				<BR>수업평가는 임의이며 평가내용은 강사에게는 공개되지 않습니다.</P>
				<P><FONT color="#ff0000">※</FONT>수업시간으로 부터<FONT  color="#ff0000">
				3일 이내에</FONT>에 평가 해주시않으면 [평가하기]는 자동 사라지게 됩니다.</P>
				<P><FONT color="#ff0000">※</FONT>수업종류를 클릭하시면 수업종류별로 보실수 있습니다.</P>
				</TD></TR>
		</TBODY></TABLE>
	</div>
	<br>
	<div class="rows">
		<TABLE cellSpacing="1" cellPadding="2" width="670" bgColor="#cccccc" border="1">
		<TBODY>
		<TR>
			<TD align='center'>
				<b><a href="javascript:go_myhistory('ALL')"><font color="#0000CC" size="+1">총수업</font></a></b></TD>
			<TD align='center'>
				<b><a href="javascript:go_myhistory2('K')"><font color="#0000CC" size="+1">큐빅수업</font></a></b></TD>
			<TD align='center'>
				<b><a href="javascript:go_myhistory2('P')"><font color="#0000CC" size="+1">포인트수업</font></a></b></TD>
			<TD align='center'>
				<b><a href="javascript:go_myhistory2('M')"><font color="#0000CC" size="+1">고정수업</font></a></b></TD></TR>
		</TBODY>
		</TABLE>
	</div>
	<div class="rows">
		<TABLE cellSpacing="1" cellPadding="2" width="670" bgColor="#cccccc" border="1">
		<TBODY>
		<TR>
			<TD align='center'>
				<b><a href="javascript:go_myhistory('F')"><font color="#0000CC" size="+1">완료수업</font></a></b></TD>
			<TD align='center'>
				<b><a href="javascript:go_myhistory('A')"><font color="#0000CC" size="+1">결석수업</font></a></b></TD>
			<TD align='center'>
				<b><a href="javascript:go_myhistory('C')"><font color="#0000CC" size="+1">취소수업</font></a></b></TD>
			<TD align='center'>
				<b><a href="javascript:go_myhistory('E')"><font color="#0000CC" size="+1">연장수업</font></a></b></TD>
			<TD align='center'>
				<b><a href="javascript:go_myhistory('R')"><font color="#0000CC" size="+1">미진행수업</font></a></b></TD></TR>
		</TBODY>
		</TABLE>
	</div>
	<br>
	<div class="rows">
		<table cellSpacing="0" cellPadding="0" width="670" border="0">
			<tr>
				<td><img src="<?php echo $cubictalkko_path;?>/img/tbl-lessons-myhistory-ko.png" width="670"></td>
			</tr>
			<tr>
				<td>
					<table cellSpacing="1" cellPadding="2" width="670" bgColor="#cccccc" border="0">
					<TBODY>
						<TR>
							<TD background="<?=c_path()?>/img/bg_gra_green.jpg" height=22>
								<DIV align="center">NO.</DIV></TD>
							<TD background="<?=c_path()?>/img/bg_gra_green.jpg" height="22">
								<DIV align="center">수업날짜/시간<br>결제일</DIV></TD>
							<TD background="<?=c_path()?>/img/bg_gra_green.jpg" height="22">
								<DIV align="center">선생님</DIV></TD>
							<TD background="<?=c_path()?>/img/bg_gra_green.jpg" height="22">
								<DIV align="center">결제일</DIV></TD>
							<TD background="<?=c_path()?>/img/bg_gra_green.jpg" height="22">
								<DIV align="center">사용포인트</DIV></TD>
							<TD background="<?=c_path()?>/img/bg_gra_green.jpg" height="22">
								<DIV align="center">평가하기 </DIV></TD>
							<TD background="<?=c_path();?>/img/bg_gra_green.jpg" height="22">
								<DIV class="style7" align=center>코멘트</DIV></TD></TR>
						<?php
						if((isset($UserInfo['User']['lessons_myhistory']) && $UserInfo['User']['lessons_myhistory'] > 0)
								&& isset($myhistory) && count($myhistory) > 0){
							$three_days = array();
							$today = date('Y-m-d');
							$one_days = (24 * 60 * 60);
							foreach($myhistory as $keymyhistory => $kmh){ // $kmh means $keymyhistory
						?>
						<TR>
							<TD bgColor="#f9f7f2">
								<DIV align="center"><?php echo $rCount+=1;?></DIV></TD>
							<TD bgColor="#f9f7f2">
								<DIV align="center"><?php echo change_date_format($kmh['Lesson']['reserve_time'],1);?></DIV></TD>
							<TD bgColor="#ffffff">
								<DIV align="center"><A href="<?php echo c_path();?>/teachers/profile/<?echo $kmh['Teacher']['id'];?>">
								<IMG height="45" alt="" src="<?php echo c_path().$kmh['Teacher']['img_file'];?>" width="60" border="0">
								</A>
								<DIV><A href="<?php echo c_path();?>/teachers/profile/<?php echo $kmh['Teacher']['id'];?>"><?php echo $kmh['Teacher']['tea_name'];?>
			                    </A></DIV></DIV></TD>
							<TD bgColor="#ffffff">
								<DIV align="center">
								<?php
								if(isset($kmh['Payment']['id'])){
								?>
								<div class="row text-center" align="center"><?php echo date("Y-m-d", strtotime($kmh['Payment']['created']));?></DIV>
								<?php
								}
								?>
								</DIV></TD>
							<?php 
							$lesson_kinds = $kmh['Lesson']['reservation_kinds'];
							$lesson_kinds_text = null;
							if($lesson_kinds == 'P'){
								$lesson_kinds_text = $kmh['Lesson']['spend_point'].'포인트';
							}elseif($lesson_kinds == 'K'){
								$lesson_kinds_text = '큐빅';
							}elseif($lesson_kinds == 'M'){
								$lesson_kinds_text = '일괄예약';
							}elseif($lesson_kinds == 'MD'){
								$lesson_kinds_text = '낮시간 할인';
							}elseif($lesson_kinds == 'M2'){
								$lesson_kinds_text = '주2회 고정';
							}elseif($lesson_kinds == 'M3'){
								$lesson_kinds_text = '주3회 고정';
							}elseif($lesson_kinds == 'T'){
								$lesson_kinds_text = '체험수업';
							}elseif(in_array($lesson_kinds,array('LANDLINE10','MOBILE10','LANDLINE20','MOBILE20'))){
								$lesson_kinds_text = '일괄예약';
							}elseif(in_array($lesson_kinds,array('TTEM3','TMEM3'))){
								$lesson_kinds_text = '주3회 고정';
							}elseif(in_array($lesson_kinds,array('TTEM2','TMEM2'))){
								$lesson_kinds_text = '주2회 고정';
							}
							?> 
							<TD bgColor="#ffffff" width="100">
								<DIV align="center" title='<?php echo $lesson_kinds_text?>'>
								<?php
								if(!empty($lesson_kinds) && in_array($lesson_kinds, array('M','MD','M2','M3','P','K','T'))){
								?>
								<img src="<?php echo c_path();?>/image/mypage/fix<?php echo $lesson_kinds;?>Days.png" width="35" border="0" height="35" title="<?php echo $lesson_kinds_text?>" alt="<?php echo $lesson_kinds_text?>">
								<?
								}elseif(!empty($lesson_kinds) && in_array($lesson_kinds, array('LANDLINE10','MOBILE10','LANDLINE20','MOBILE20','TTEM3','TMEM3','TTEM2','TMEM2'))){
								?>
								<img src="<?php echo c_path();?>/image/mypage/fix<?php echo $lesson_kinds;?>Days.png" width="35" border="0" height="35" border="0" title="<?php echo $lesson_kinds_text?>" alt="<?php echo $lesson_kinds_text?>">
								<?php
								} else echo $lesson_kinds_text;
								?>
								</DIV></TD>
							<TD bgColor="#ffffff">
								<?php
								$onday_ahead_time = change_time($kmh['Lesson']['reserve_time'],"ONEDAY_AHEAD");
								$evel_status = "T";
								$count_days = ((strtotime($today) - strtotime(date('Y-m-d', strtotime($kmh['Lesson']['reserve_time'])))) / $one_days);
								if($count_days < 4){
									$evel_status = "T";
								}elseif(strtotime($onday_ahead_time)-time() < 0){
									$evel_status = "F";
								}elseif($loginInfo['User']['pay_plan'] == 'M'){
									$evel_status = "T";
								}
								?>
								<DIV align="center">
								<?php
								$status= $kmh['Lesson']['lesson_status'];
								if(isset($lesson_status[$status])){
									echo($lesson_status[$status]);
								}
								if($kmh['Lesson']['evaluate_status'] == 'N' && $evel_status == 'T' && ($kmh['Lesson']['lesson_status'] == 'F' || $kmh['Lesson']['lesson_status'] == 'A')){
								?>
								<A href="javascript:evaluate_submit(<?php echo $kmh['Lesson']['id'];?>,<?php echo $kmh['Teacher']['id'];?>);"><font size='5'><b>평가하기</b></font></A>
								<?php
								}elseif($kmh['Lesson']['evaluate_status'] == 'Y'){
								?>
								평가완료
								<?php
								}
								?>
								</DIV></TD>
							<TD bgColor="#f9f7f2">
								<div style="text-align: center;"><a id="class_comment" href="javascript:void(0)" data-description="Class Comment" data-required-id="<?php echo $kmh['Lesson']['id'];?>">
								<img src="<?php echo $cubictalkko_path;?>/image/icon/icon-link/icon-link-comment-ko.png" border="0" width="35" height="35" title="코" alt="코"></a></div>
								</TD></TR>
						<?php
							} // End foreach($myhistory as $keymyhistory => $kmh){}
						}else{
						?>
						<TR>
							<TD bgcolor="#FFFFFF" colspan="7">수강하신 수업이 없습니다.</TD></TR>
						<?php
						} // End if((isset($UserInfo['User']['lessons_myhistory']) && $UserInfo['User']['lessons_myhistory'] == 0)
						//		&& isset($myhistory) && count($myhistory) > 0){
						?>
					</TBODY>
					</table>
				</td>
			</tr>
		</table>
	</div>
	<br>
	<?php
	if((isset($UserInfo['User']['lessons_myhistory']) && $UserInfo['User']['lessons_myhistory'] > 0)
			&&
			isset($myhistory) && count($myhistory) > 0){
	?>
	<div class="rows">
	<div class="_main text-center">
		<div class="_main_content">
			<div class="_content _prev1"><div class="_pages"><?php echo $paginator->prev('<< '.__('previous', true), array(), null,array('class'=>'_disabled'));?></div></div>
			<div class="_content _number1">
			<?php echo $paginator->numbers(array('tag' => 'div class="_pages"','separator' => '','class' => '_active'));?>
			<div class="clearfix"></div>
			</div>
			<div class="_content _next1"><div class="_pages"><?php echo $paginator->next(__('next', true).' >>', array(), array(),array('class'=>'_disabled'));?></div></div>
			<div class="_content _counter1"><div class="_pages"><?php echo $paginator->counter();?></div></div>
			<div class="clearfix"></div>
		</div>
		<div class="clearfix"></div>
	</div>
	</div>
	<?php
	} // 
	?>
</div>
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
							<div class="current-pages-hide teacher-lesson-comment">
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


<div class="formCubictalkMaterial">
<form id="formCubictalkMaterial" name="" action="" method="post" target="" enctype=""><input type="hidden" id="book_page" name="page" value=""></form>
</div>

<script type="text/JavaScript">
var modal_title = {"Class Comment":"teacher-lesson-comment"};
var no_script = true;
var data_description = 'Icons Description';
var add_script = '<script type="text/javascript" src="<?php echo $cubictalkko_path;?>/js/jquery-1.11.1.min.js"><\/script><script type="text/javascript" src="<?php echo $cubictalkko_path;?>/js/bootstrap.min.js"><\/script>';

function closemod(){
	$('#iconsmodal').modal('hide');
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
<form name="form1" method="post" >
	<input type="hidden" name="reservation_kinds">
	<input type="hidden" name="lesson_status">
</form>
<form name="evaluate" method="post" action="javascript:evaluate_submit(document.evaluate);" enctype="multipart/form-data" autocomplete="off">
	<INPUT type="hidden" name="lesson_id" value="">
	<INPUT type="hidden" name="teacher_id" value="">
</form>
<?php
include ('bottom.php');
?>