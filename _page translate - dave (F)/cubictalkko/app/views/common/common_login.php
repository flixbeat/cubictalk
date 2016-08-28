<?php $loginInfo = $session->read('loginInfo');?>
<?php $checkPayment = $session->read('checkPayment');?>
<!-- 가입 -->
<?php if($loginInfo==null){?>
<?php  echo $form->create('User', array('action'=>'/login')); ?>
<table border="0" cellspacing="0" cellpadding="0">
		<tr>
			<td rowspan="4"><img src="/cubictalk/image/login_edge01.gif" border="0"></td>
			<td height="55" valign="top"><img src="/cubictalk/image/login_tit.gif" border="0"></td>
			<td rowspan="4"><img src="/cubictalk/image/login_edge02.gif" border="0"></td></tr>
		<tr>
			<td valign="top">
			
<table border="0" cellspacing="0" cellpadding="0">
<tr>
	<td height="25" valign="top"><img src="/cubictalk/image/login_txt01.gif" border="0"></td>
	<td width="98" valign="top">
	<!-- <input type="text" style="width:94;height:18;border:solid 1px #bbbbbb">-->
	<?php echo $form->text('skype_id', array('class'=>'field1','style'=>'ime-mode:disabled')) ?>
	</td>
	<td rowspan="2" valign="top"><INPUT type="image" src="/cubictalk/image/login_btn01.gif" border="0" alt="ログイン"></td></tr>
<tr>
	<td height="28" valign="top"><img src="/cubictalk/image/login_txt02.gif" border="0"></td>
	<td valign="top"><?php echo $form->text('password', array('class'=>'field1','style'=>'ime-mode:disabled')) ?></td></tr>
</table>


</td></tr>
		<tr>
			<td><img src="/cubictalk/image/login_edge03.gif" border="0"></td></tr>
		<tr>
			<td valign="top">
				<!-- 버튼 -->
				<table border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td><a href="http://127.0.0.1/cubicboard/bbs/member_confirm.php?url=register_form.php"><img src="/cubictalk/image/login_btn02.gif" border="0" alt="회원가입"></a>
					<!--  <td><a href="#"><img src="/cubictalk/image/login_btn02.gif" border="0" alt="회원가입"></a></td>-->
					<td><A href="javascript:win_point();"><img src="/cubictalk/image/login_btn03.gif" border="0" alt="ID/PW찾기"></a></td></tr>
				</table>
				<!-- /버튼 -->
			</td></tr>
		</table>

<?php echo $form->end(); ?>

<?php }else{ ?>
<!-- 로그인 -->
<table border="0" cellspacing="0" cellpadding="0">
		<tr>
			<td rowspan="5"><img src="<?= b_path()?>/skin/outlogin/basic/img/login_edge01.gif" border="0"></td>
			<td height="19" valign="top"><img src="<?= b_path()?>/skin/outlogin/basic/img/login_tit02.gif" border="0"></td>
			<td rowspan="5"><img src="<?= b_path()?>/skin/outlogin/basic/img/login_edge02.gif" border="0"></td></tr>
            
         <tr>
            	<td height="36"><a href="javascript:win_memo();"><FONT color="#ff8871;"><B>message (<?=$loginInfo['User']['notReadMemo']?>)</B></FONT></a></td></tr>
		<tr>
			<td valign="top">
			
<table border="0" cellspacing="0" cellpadding="0">
<tr>
	<td colspan="2" height="25" width="120" valign="top">
	<!-- <input type="text" style="width:94;height:18;border:solid 1px #bbbbbb">-->
	<SPAN class=t12_333><SPAN class=status1><?php echo $loginInfo['User']['nick_name']?></SPAN> 님</SPAN>
	</td>
	<td rowspan="2" valign="top"><a href="<?= c_path()?>/users/logout"><img src="<?= b_path()?>/skin/outlogin/basic/img/logout_btn01.gif" border="0" alt="ログアウト"></a></td></tr>
<tr>
	<td colspan="2" height="28" valign="top" width="120"><SPAN class=t12_333><STRONG><a href="javascript:win_point();"><FONT color="#ff8871;"><?php echo $loginInfo['User']['points']?></FONT></a></STRONG> 포인트</SPAN></td>
</tr>
</table>


</td></tr>
		<tr>
			<td><img src="<?= b_path()?>/skin/outlogin/basic/img/login_edge03.gif" border="0"></td></tr>
		<tr>
			<td valign="top">
				<!-- 버튼 -->
				<table border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td><a href="<?= b_path()?>/bbs/member_confirm.php?url=register_form.php"><img src="<?= b_path()?>/skin/outlogin/basic/img/login_btn04.gif" border="0" alt="登録情報修正"></a></td>
					<td><a href="javascript:win_scrap();"><img src="<?= b_path()?>/skin/outlogin/basic/img/login_btn03.gif" border="0" alt="SCRAP"></a></td></tr>
				</table>
				<!-- /버튼 -->
			</td></tr>
		</table>
<!-- /로그인 -->
<?php }?>
<!-- /가입 -->