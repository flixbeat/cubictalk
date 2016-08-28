<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html  xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo h("국내 최저가격의 온라인 영어회화 CubicTalk"); ?></title>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<meta name="author" content="CubicTalk">
<META name="description" content="스카이프를 이용한 온라인영어회화 업계 최저의 가격（CubicTalk）입니다。 1개월59,000원으로 매일 25분 영어회화 맨투맨 수업을 받을 수 있습니다.">
<META name="keywords" content="온라인 영어회화,영어회화,스카이프,CubicTalk,맨투맨,개인레슨,Skype,CubicTalk,최저가격">
<script src="<?=b_path();?>/swf/js/flashObj.js" type="text/javascript"></script>
<link rel="stylesheet" href="<?=c_path();?>/link.css">
<link rel="stylesheet" href="<?=c_path();?>/tmp1_1.css">
<style>
.c td{
padding: 2px;
border-collapse: collapse;
border: 0px black solid;
}

.fancySubmitButton{
background: #999; /* for non-css3 browsers */

filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#ffffff', endColorstr='#cccccc'); /* for IE */
background: -webkit-gradient(linear, left top, left bottom, from(#fff), to(#ccc)); /* for webkit browsers */
background: -moz-linear-gradient(top,  #fff,  #ccc); /* for firefox 3.6+ */ 

border: 3px solid #FFCC99;
width: 100px;
height: 50px;
border-radius: 5px;
-moz-box-shadow: 0px 3px 8px #000;
-webkit-box-shadow: 0px 3px 8px #000;
box-shadow: 0px 3px 8px #000;
font-weight: bold;
font-size: 1.3em;
text-shadow: 0px 0px 3px #000;
}

.fancySubmitButton:hover{
text-shadow: 1px 1px 3px #fff;
border: 3px solid #FF9900;
background: #999; /* for non-css3 browsers */

filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#cccccc', endColorstr='#888888'); /* for IE */
background: -webkit-gradient(linear, left top, left bottom, from(#ccc), to(#888)); /* for webkit browsers */
background: -moz-linear-gradient(top,  #ccc,  #888); /* for firefox 3.6+ */ 
}
    .tableBordered table, .tableBordered th, .tableBordered td, .tableBordered tr{
      text-align: center;
      padding: 10px;
      border: 1px solid #999999;
     
    }
    
    .tableBordered th{
      background: #cccccc;
    }
    
    .tableBordered tr:hover{
    	background: #FFFFCC;
    }
    
    .tableBordered td:hover{
    	background: #FFFF99;
    }
    
    hr{
    	border: 0;
    	width: 100%;
    	height: 1px;
    	background: #999999;
    }
    
.en{
	background: url('<?=c_path();?>/image/english-button.jpg');
}

.ko{
	background: url('<?=c_path();?>/image/korean-button.jpg');
}

.link{
	display: block;
	width: 150px;
	height: 40px;
	border: 2px solid #cccccc;
	background-position: bottom;
	border-radius: 10px;
	-moz-box-shadow: 0px 3px 8px #000;
	-webkit-box-shadow: 0px 3px 8px #000;
	box-shadow: 0px 3px 8px #000;
}

.link:hover{
	background-position: top;
	border: 2px solid black;
}

.align-left{
text-align: left;
}
</style>

</head>
 <SCRIPT type=text/JavaScript>
function checkInput(){
	document.forms["form11"].action = "<?=c_path()?>/lessons/search/";
	document.forms["form11"].submit();
	return false;
}

	  function resetValues(){
		document.getElementById('search_from').value = '';
		document.getElementById('search_to').value = '';
		document.getElementById('sel').options[0].selected=true;
		document.getElementById('sel1').options[0].selected=true;
	  }
	  
	  
</SCRIPT>
<script language="javascript">
var g4_path = "<?=b_path();?>";
</script>
<script src="<?=c_path();?>/js/cubicboard.js" type="text/javascript"></script>

 <?php echo $javascript->link('jquery-1.4.2.min');?>
    <?php echo $javascript->link('jquery-ui-1.8.4.custom.min');?>
    <?php echo $javascript->link('jquery.autocomplete.min');?>
    <?php echo $javascript->link('jquery.jeditable.mini');?>
    <?php echo $html->css('jquery-ui-1.8.4.custom'); ?>
  <?php
 
    echo $javascript->link('date.js');
    echo $javascript->link('jquery.datePicker.js');
    echo $html->css('datePicker');
    echo $html->css('mycss');
   ?>
   
   <script type="text/javascript" charset="utf-8">
					$(function()
					{
						Date.format = 'yyyy-mm-dd';
						$('.date-pick').datePicker({clickInput:true,startDate:'1990-01-01'});
					
						
					});
</script>


<body topmargin="0" leftmargin="0" marginheight="0" marginwidth="0">

<!-- 로고 및 메뉴부분 -->
<table border="0" cellspacing="0" cellpadding="0" width="100%" background="<?=c_path();?>/image/top_bg.gif">
<tr>
	<td width="980">
		<table border="0" cellspacing="0" cellpadding="0" width="980">
		<tr>
			<td rowspan="2"><a href="<?=b_path()?>/index.php"><img src="<?=b_path()?>/image/logo.png" border="0" alt="CubicTalk"></a></td>
			<td width="762" valign="top" colspan="2">
				<!-- 탑메뉴 -->
				<table border="0" cellspacing="0" cellpadding="0">
				<tr>
					<?php 
						# get current controller and action name
						$controller = $this->params['controller'];
						$action = $this->params['action'];
						# get parameters if there are any
						$urlParams = '';
						if(sizeof($this->passedArgs) != 0){
							foreach($this->passedArgs as $param){
								$urlParams .= "/$param";
							}
						}
						
					?>

					<td width="601" align="right"><a href="<?=b_path()?>/index.php"><img src="<?=b_path()?>/image/topmenu01.gif" border="0" alt="홈"></a></td>
					<td><a href="<?=b_path();?>/bbs/login.php"><img src="<?=b_path()?>/image/topmenu02.gif" border="0" alt="로그인"></a></td>
					<!-- <td><a href="<?=b_path();?>/index.php"><img src="<?=b_path()?>/image/topmenu03.gif" border="0" alt="한국어"></a></td> -->
					<!-- <td><a href="<?=b_ja_path();?>/index.php"><img src="<?=b_path();?>/image/topmenu04.gif" border="0" alt="일본어"></a></td> -->
			
					<td>
						<a href = "<?php echo c_path().'/language_redirect/change_lang/ko/'.$controller.'/'.$action . $urlParams?>">
							<img src="<?=b_path()?>/image/topmenu03.gif" border="0" alt="한국어">
						</a>
					</td>
					<td><a href = "<?php echo c_path().'/language_redirect/change_lang/en/'.$controller.'/'.$action . $urlParams?>"><img src="<?=b_path()?>/image/topmenu04.gif" border="0" alt="일본어"></a></td>
					</tr>
					
				</table>
				<!-- /탑메뉴 -->
			</td></tr>
	<!-- menu start:flashをimageに変換 090909-Amu -->
		<tr>
			<td width="580" valign="top">
				<!-- 메뉴 -->
				<table border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td width="580" valign="top">
                        <!-- 메뉴플래시 적용 부분-->
                        <script type="text/javascript">
                            FlashObject("<?=b_path();?>/swf/index.swf?route=0", 580, 73, "#ffffff", "");
                        </script>
                        <!-- /메뉴플래시 적용 부분-->
					</td>
                  
                  <td valign="top" width="182">
				<!-- 탑이벤트 -->
				<table border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td><a href="#"><img src="<?=b_path();?>/image/top_event01.gif" border="0" alt="커뮤니티"></a></td>
					<td><a href="#"><img src="<?=b_path();?>/image/top_event02.gif" border="0" alt="이벤트"></a></td></tr>
				</table>
				<!-- /탑이벤트 -->
			</td>
            
                  </tr>
				</table>
				<!-- /메뉴 -->
			</td>
	<!-- menu end -->
		</table>
	</td>
	<td width="100%"></td></tr>
</table>
<!-- /로고 및 메뉴부분 -->

<!-- 메인테이블 -->
<table border="0" cellspacing="0" cellpadding="0" width="100%" background="<?=c_path();?>/image/s_main_bg.gif" style="background-repeat:repeat-x">
<tr>
	<td width="980">
		<table border="0" cellspacing="0" cellpadding="0" width="980">
		<tr>
			<td width="218" valign="top">
				<!-- 왼쪽부분 -->
				<table border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td valign="top" width="14"></td>
					<td width="204" valign="top">
						<!-- 로그인 -->
						<?php include('common_login.php');?>
						<!-- /로그인 -->

						<? include("menu.php") ;?>
						
					</td></tr>
				</table>
				<!-- /왼쪽부분 -->
			</td>
			<td width="762" valign="top" align="center">
