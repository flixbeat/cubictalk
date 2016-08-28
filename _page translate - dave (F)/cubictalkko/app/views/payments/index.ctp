<?php

echo $language[$session->read('lang')]['fluency']; 
include('views/common/top.php');

$path=c_path();

$totalpayment_info = count($payment_info);
if(isset($payment_info) && $totalpayment_info > 0){
	$paginator_session = $paginator->params;
	$rCount=0; // row counting or data counting initialize;
	if(isset($paginator_session['paging']['Payment']) && count($paginator_session['paging']['Payment']) > 0){
		$paginator_session=$paginator_session['paging']['Payment'];
		if($paginator_session['count'] > $paginator_session['current']){
			$rCount=(($paginator_session['page']-1)*$paginator_session['defaults']['limit']);
		} // if($totalLesson > $paginator_session['current']){}
	} // if(isset($paginator_session['paging']['Lesson']) && count($paginator_session['paging']['Lesson']) > 0){}
} // if(isset($lessons) && $totalLesson > 0){}
?>
<link rel="stylesheet" href="<?php echo $path;?>/css/default.css" type="text/css">
<link rel="stylesheet" href="<?php echo $path;?>/css/cubictalk-style.css" type="text/css">
<script type="text/javascript" src="<?php echo $path;?>/js/jquery-1.11.1.min.js"></script>
<script type="text/javascript" src="<?php echo $path;?>/js/bootstrap.min.js"></script>
<style type="text/css">
	ul li { list-style:none; padding-top: 10px;}
    .tableBordered table, .tableBordered th, .tableBordered td, .tableBordered tr{
      text-align: center;  padding: 10px; border: 1px solid #999999;  }
    
    .tableBordered th{ background: #cccccc; }
    
    .tableBordered tr:hover{ background: #FFFFCC; }
    
    .tableBordered td:hover{ background: #FFFF99; }
	div#lightbox, div#lightbox-shadow, div#tbl-details{ display:none; }
	div#close { background: #FFFFFF url('<?=c_path()?>/image/icon/close.png') no-repeat center; }
	div#close:hover { background: #FAF8CC url('<?=c_path()?>/image/icon/close.png') no-repeat center; }
	div#tbl-infos table thead th.th-payment, div#tbl-details table thead th.th-payment { background: #5FFB17; padding: 5px; color: #000; font-family: Arial Black; }
	div#tbl-infos table th.th-payment { font-family: Arial Black; padding: 5px; }
	div#tbl-infos table tbody th.th-payment:hover { background: #FFF380; }
	div#tbl-details table tr:hover { background: #FFFFCC; }
	div#tbl-details table tbody th.th-payment:hover { background: #cccccc; }
	
	div.div-title {
		-webkit-border-radius: 5px;  -moz-border-radius: 5px;  border-radius: 5px;
		background:rgb(57, 114, 73); border:1px solid rgb(199, 225, 186); color:rgb(255, 255, 255);
		font-size:20px; font-family:arial; font-weight:bold; letter-spacing:5px;
		padding-top:10px;padding-bottom:5px;padding-top:10px;padding-top:10px;
	}
.modal-dialog {width: 90%}
<?php //paging ?>
.paging-new {
	display:block;
	margin:0;
	padding:0;
}
.paging-new > ul {
	margin:0;
	padding:0;
	margin-top:2px;
	margin-bottom:2px;
}
.paging-new a,.paging-new a:link {
	margin:0;
	padding:0;
	display:block;
}
.paging-new a:hover,.paging-new a:focus {
	text-decoration: none;
}
.paging-menu a,.paging-new label {
	width:100%;
	font-size:20px;
}
.paging-new .paging-menu {
	float:left;
	text-align:center;
	padding:2px;
	width:auto;
	line-height:2em;
	margin-left:-1px;
	border-collapse:collapse;
	padding:6px 12px;
}
.paging-new ul, .paging-new ul li, .paging-new ul li a{
	position:relative;
} 
.paging-new:before,.paging-new:after,.clearfix:before,.clearfix:after,.paging-new ul:before,.paging-new ul:after {
	display:block;
	clear:both;
}
@media (min-width:768px) {
	.paging-new .paging-menu {
		float:none;
		display:inline-block;
		border-collapse:collapse;
		/* using margin-left negative make space inbetween remove and make border-collapse */
		margin-left:-5px;
	}
}
@media (min-width:992px) {
	.paging-new .paging-menu {
		float:none;
		display:inline-block;
		border-collapse:collapse;
		/* using margin-left negative make space inbetween remove and make border-collapse */
		margin-left:-5px;
	}
}
@media (min-width:1200px) {
	.paging-new .paging-menu {
		float:none;
		display:inline-block;
		border-collapse:collapse;
		/* using margin-left negative make space inbetween remove and make border-collapse */
		margin-left:-5px;
	}
}
</style>
<script type="text/javascript">
 var $modal=0;
 function viewPdf (id) {
  document.payments.searchSetting.value = 1;
  document.payments.view_pdf.value = id;
  document.payments.target = '_blank';
  document.payments.action = '<?php echo c_path();?>/payments/viewPdf/' + id + '/';
  document.payments.submit();
  return false;
 }
 
	function feeInfo (id, sday, lday) {
		$.ajax({
			type:'POST',
			url: '<?=c_path()?>/payments/info',
			data: {id : id, sday : sday, lday : lday},
			success:function(data){
				$('.td'+id).css('background','red');
				document.getElementById('lightbox').style.top = (window.innerHeight*0.25)+'px';
				document.getElementById('lightbox').style.left = (window.innerWidth*0.20)+'px';
				$('#lightbox').empty();
				//$('#query').html(data);
				$('#lightbox-shadow').slideToggle();
				setTimeout(function(){$('#lightbox').slideToggle();},500);
				$('#lightbox').html(data).delay(300);
			},
			error:function(){
				alert('Error Found!');
			}
		});
		return false;
	}

	function status(id, info, sday, lday, page) {
		document.getElementById('lightbox').style.left = (window.innerWidth*0.20)+'px';
		document.getElementById('lightbox').style.top = (window.innerHeight*0.02)+'px';
		$.ajax({
			type:'POST',
			url: '<?=c_path()?>/payments/status',
			data: {id : id, info : info, sday : sday, lday : lday, page : page},
			success:function(data){
				$('#tbl-details').empty();
				$('#tbl-details').css('display','none');
				setTimeout(function(){ $('#tbl-details').slideToggle(function(){$('#tbl-details').html(data).delay(300);});},500);
			},
			error:function(){
				alert('Error Found!');
			}
		});
		return false;
	}

 function comment(id){
  $.ajax({
   type: 'POST',
   url: '<?php echo c_path();?>/lessons/view',
   data: {id:id},
   dataType:'html',
   success: function(data){
	$('#LessonCommentModal').modal('show');
	$("#LessonCommentModal .modal-backdrop").css({'z-index':0});
	$('#LessonCommentModal').find('.modal-body').html(data);
	$modal=1;
   },
   error: function(message){
    alert('Error found! reload the page.');
   }
  });
  return false;
 }

 function divClose(id) {
  if(!$modal){ 
   $('#lightbox').slideToggle().delay(300);
   setTimeout(function(){$('#lightbox-shadow').slideToggle();},500);
   $('.td'+id).css('background','transparent');
  }else{
   $modal=0;
  }
 }
	
	function popurl(idA, idB, idC, idD, idE) {
		
		var scrnW = screen.width * 0.2;
		var url = '<?=c_path()?>/payments/'+idA+'/'+idB+'/'+idC+'/';
		params = 'width='+idD+', height='+idE+', top=0, left='+scrnW+', scrollbars=yes, resizable=false';
		params += ', fullscreen=yes';
		
		newwin=window.open(url,idA, params);
		if (window.focus) {newwin.focus();}
		return false;
	}
</script>
<!-- 타이틀 -->
<div class="div-container">
	<div class="text-left"><img src="<?php echo $cubictalkko_path;?>/image/title/payments-title-ko.png" border="0"></div>
</div>

<DIV id="query"></DIV>
<div id="lightbox-shadow" style="position: fixed; top:0px; left:0px; width:1366px; height:705px; background: url('<?=c_path()?>/image/bg/bg-msg.png') repeat; "></div>
<div id="lightbox" style=" cursor:pointer; position: fixed; z-index:99;" align="center"></div>
<? if(isset($payment_info)){ ?>
<form name="payments" method="post">
<input type="hidden" name="searchSetting" value="0">
<input type="hidden" name="view_pdf" value="0">

<div>
<table class = "tableBordered" style = "border-collapse: collapse; margin-top: 20px;">
	<tr>
		<th>No.</th>
		<th>결제종류</th>
		<th>수업횟수</th>
		<th>금액</th>
		<th>결제일</th>
		<th>상태</th>
		<th>결제기간별수업</th>
		<th>출석부</th>
		<th>수강증</th>
	</tr>
	<?
	$goods_name = array('E1' => '25 mins', 'E2' => '50 mins', 'P' => 'Point Plan', 'E1F' => '25 mins fix plan', 'E2F' => '50 mins fix plan');
	$goods_ko= array('E1' => '25 mins', 'E2' => '50 mins', 'P' => 'Point Plan', 'E1F' => '25 mins fix plan', 'E2F' => '50 mins fix plan');
	$y = 1;
	if(isset($paginator_session['page'])){
		$y = (($paginator_session['page'] * $paginator_session['defaults']['limit']) - $paginator_session['defaults']['limit']) + 1;
	}
	
	foreach ($payment_info as $info_payment){
		$good_name = $info_payment['Payment']['goods_name'];
		if($info_payment['Payment']['goods_name'] == 'E1'){
			$good_name = $goods_name['E1'];
		}elseif($info_payment['Payment']['goods_name'] == 'E2'){
			$good_name = $goods_name['E2'];
		}elseif($info_payment['Payment']['goods_name'] == 'P'){
			$good_name = $goods_name['P'];
		}elseif($info_payment['Payment']['goods_name'] == 'E1F'){
			$good_name = $goods_name['E1F'];
		}elseif($info_payment['Payment']['goods_name'] == 'E2F'){
			$good_name = $goods_name['E2F'];
		}elseif(substr($payment_info[0]['Payment']['goods_name'],(strlen($payment_info[0]['Payment']['goods_name']) - 1), strlen($payment_info[0]['Payment']['goods_name'])) == 'C') {
			$good_name = $payment_info[0]['Payment']['goods_name'];
		}
		
		if($info_payment['Payment']['goods_months'] > 1)
			$month = 'months';
		else
			$month = 'month';
		
		if($info_payment['Payment']['lesson_num'] > 0)
			$lesson = $info_payment['Payment']['lesson_num'];
		else
			$lesson = 'everyday';
		
		$goods_name = $info_payment['Payment']['goods_name'];
		if(strlen($info_payment['DictionaryDetail']['express_name_ko']) > 0){
			$goods_name = $info_payment['DictionaryDetail']['express_name_ko'];
		}elseif(strlen($info_payment['DictionaryDetail']['express_name_en']) > 0){
			$goods_name = $info_payment['DictionaryDetail']['express_name_en'];
		}
		
		// DictionaryDetail for Payment Status
		$status = $info_payment['Payment']['pay_ok'];
		if(strlen($info_payment['DictionaryDetail2']['express_name_ko']) > 0){
			$status = $info_payment['DictionaryDetail2']['express_name_ko'];
		}elseif(strlen($info_payment['DictionaryDetail2']['express_name_en']) > 0){
			$status = $info_payment['DictionaryDetail2']['express_name_en'];
		}
	?>
	<tr>
		<td class="td<?=$info_payment['Payment']['id']?>" ><?=$y?></td>
		<td class="td<?=$info_payment['Payment']['id']?>" >
		<?php
		// echo $info_payment['Payment']['goods_months']." $month ".$good_name;
		echo  $goods_name . ' <span style="color:#ff6600">' . $info_payment['Payment']['goods_months'] . '개월</span>';
		?>
		</td>
		<td class="td<?=$info_payment['Payment']['id']?>" ><?=$lesson?></td>
		<td class="td<?=$info_payment['Payment']['id']?>" ><?=$info_payment['Payment']['amount']?></td>
		<td class="td<?=$info_payment['Payment']['id']?>" ><?=$info_payment['Payment']['created']?></td>
		<td class="td<?=$info_payment['Payment']['id']?>" ><?php echo $status;?></td>
		<td class="td<?=$info_payment['Payment']['id']?>" ><input type="button" onclick="feeInfo(<?=$info_payment['Payment']['id']?>, '<?=$info_payment['Payment']['start_day']?>',  '<?=$info_payment['Payment']['last_day']?>', '<?=$info_payment['Payment']['last_day']?>')" value="통계보기"></td>
		<td class="td<?=$info_payment['Payment']['id']?>" ><button type="button" onclick="return viewPdf(<?php echo $info_payment['Payment']['id'];?>)">출력</button></td>
		<td class="td<?=$info_payment['Payment']['id']?>" ><input type="button" onclick="popurl('receipt', <?=$info_payment['Payment']['id']?>, '<?=strtotime(date('Y-m-d H:i:s'))?>', 650, 700)" value="출력"></td>
		
	</tr>
	<? $y++; } ?>
</table>
</div>
</form>
<?php
if(isset($payment_info) && $totalpayment_info > 0 && $paginator_session['count'] > $paginator_session['current']){
	echo '<div class="div-container">';
	$max_num_page_display = 9; // from left and right
	$start = 1;
	$end = 10;
	$max_num_page_display_half = ceil(($max_num_page_display / 2));
	$page_display_status = false;
	$page_display = $paginator_session['page'] - $max_num_page_display_half;
	$total_page = ceil($paginator_session['count'] / $paginator_session['defaults']['limit']);
	// pr($paginator_session);
	if($page_display > 0){
		$page_display_status = true;
		$start = ($paginator_session['page'] - $max_num_page_display_half) + 1;
		$end = $paginator_session['page'] + $max_num_page_display_half;
	}
	
	if($end > $total_page){
		$start = ($total_page - $max_num_page_display) + 1;
		if ($start < 1) {
			$start = 1;
		}
		$end = $total_page + 1;
	}
	$controller_name = strtolower($this->name);
	
	$prev_btn = '&#171; Prev';
	if($paginator_session['page'] > 1){
		$page = $paginator_session['page'] - 1;
		$url = $this->webroot . $controller_name . '/' . $paginator->action . '/page:' . $page;
		$prev_btn = '<a href="'.$url.'">'.$prev_btn.'</a>';
	}
	$paging_html1 = <<<HTML
	<div class="paging-menu">{$prev_btn}</div>
HTML;
	for($start;$start < $end;$start++){
		$page = $start;
		if($start != $paginator_session['page']){
			$url = $this->webroot . $controller_name . '/' . $paginator->action . '/page:' . $page;
			$page = '<a href="'.$url.'">'.$page.'</a>';
		}
	$paging_html1 .= <<<HTML
	<div class="paging-menu ">{$page}</div>
HTML;
	}
	$next_btn = 'Next &#187;';
	if($paginator_session['page'] < $total_page){
		$page = $paginator_session['page'] + 1;
		$url = $this->webroot . $controller_name . '/' . $paginator->action . '/page:' . $page;
		$next_btn = '<a href="'.$url.'">'.$next_btn.'</a>';
	}
	$paging_html1 .= <<<HTML
	<div class="paging-menu">{$next_btn}</div>
HTML;
	$page_counter=$paginator->counter();
	$paging_html1 .= <<<HTML
	<div class="paging-menu">{$page_counter}</div>
HTML;
	$paging_html = <<<HTML
<div>
	<div class="paging-new">
		{$paging_html1}
		<div class="clearfix"></div>
	</div>
</div>
HTML;
	echo $paging_html;
	echo '</div>';
}
?>
<? } else { ?>
<h3>No Result</h3>
<? } ?>

<div id="LessonCommentModal" class="modal fade">
 <div class="modal-dialog modal-lg">
  <div class="modal-content no-border-radius">
   <div class="modal-header"></div>
   <div class="modal-body"></div>
   <div class="modal-footer text-center"></div>
  </div>
 </div>
</div>
