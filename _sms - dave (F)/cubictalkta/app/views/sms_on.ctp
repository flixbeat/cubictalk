<?php
$loginInfo = $session->read('loginInfo');
?>
<?=$javascript->link('jquery-1.4.2.min');?>
<style type="text/css">
	.th-title-max-width { width:130px; max-width:130px; }
	.hide { display:none; }
	.content { width:80%;max-width:80%; }
	.border-collapse { border-collapse:collapse; }
	.top-space-bottom { height:10px; }
	.align-left { text-align:left; }
	table { background:#FFFFFF; }
	.table-border thead tr th, .table-border thead tr td,
	.table-border tbody tr th, .table-border tbody tr td {
		border:1px solid #CCC;
		text-align:left;
	}
	.table-border thead { background:#397249; color:#C7E1BA; }
	.search-area { padding:5px; }
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
		-moz-border-radius:3px;
		-webkit-border-radius:3px;
		border-radius:3px;
		border:1px solid #1f2f47;
		display:inline-block;
		cursor:pointer;
		color:#ffffff;
		font-family:arial;
		font-size:15px;
		padding:9px 23px;
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
	tr.checked { background:#C7E1BA; }
</style>
	
<script type="text/javascript">
	<?	Configure::write('debug',1);
		if ( isset($search) && strlen(trim($search)) > 0 && $search == "search" ) {
			Configure::write('debug',0); ?>
	function searchSetting(directId) {
		document.form11.search_setting.value = 1;
		if ( directId == 1 ) {
			document.form11.sendsms.value = 1;
		} else {
			document.form11.sendsms.value = 0;
			document.form11.userId.value = 0;
		}
//			document.form11.action = "<?=c_path();?>/lessons/smsOn/search/";
			document.form11.submit();
			return false;
	}
	<?	} ?>
	function smsMsg(){
		document.getElementById("smsMsg").target = "";
		document.getElementById("smsMsg").action = "<?=b_path();?>/smssend.php";
		document.getElementById("smsMsg").submit();
		return false;
	}
	<?	if ( empty($loginInfo) ) { ?>
	alert("Sorry! The page could not display the SMS Page.");
	window.close();
	<?	} ?>
</script>
	
<script>
	/*
	create by: dave
	date: 7/29/2016
	purpose: used to phone change numbers
	*/
	var phone1a = "070";
	var phone1b = "4897";
	var phone1c = "5487";

	var phone2a = "070";
	var phone2b = "4898";
	var phone2c = "5407";

	function changePhoneNumber(val){
		
		var phone = val;
		//alert(phone);
		if(phone == 'phone1'){
			$("input[name='sphone1']").val(phone1a);
			$("input[name='sphone2']").val(phone1b);
			$("input[name='sphone3']").val(phone1c);
		}
		else if(phone == 'phone2'){
			$("input[name='sphone1']").val(phone2a);
			$("input[name='sphone2']").val(phone2b);
			$("input[name='sphone3']").val(phone2c);
		}

	}
	/*
	var phone2a = 
	<input type="text" name="sphone1" maxlength="4" value="070">-
							<input type="text" name="sphone2" maxlength="4" value="4897">-
							<input type="text" name="sphone3" maxlength="4" value="5487">
	*/
</script>

<div class = "content">
	<?	if ( isset($search) && strlen(trim($search)) > 0 && $search == "search" ) { ?>
	<form id = "form11" name = "form11" method = "post">
		<div class = "top-space-bottom"></div>
		<div class = "search-area">
			<input type = "hidden" name = "search_setting" value = "0">
			<input type = "hidden" name = "sendsms" value = "0">

			<table class = "border-collapse" border = "0" cellpadding = "2" cellspacing = "0">
				<tr>
					<th align = "left">Cubictalk.Id :</th>
					<th><input type = "text" name = "mb_id" value = "<?=(isset($search_condition['mb_id'])) ? $search_condition['mb_id'] : '' ;?>" ></th>
				</tr>
				<tr>
					<th align = "left">Ko.Name :</th>
					<th><input type = "text" name = "name" value = "<?=(isset($search_condition["name"])) ? $search_condition["name"] : '' ;?>"></th>
				</tr>
				<tr>
					<th align = "left">NickName :</th>
					<th><input type = "text" name = "nick_name" value = "<?=(isset($search_condition["nick_name"])) ? $search_condition["nick_name"] : '' ;?>"></th>
				</tr>
				<tr>
					<th align = "left">Skype :</th>
					<th><input type = "text" name = "skype_id" value = "<?=(isset($search_condition["skype_id"])) ? $search_condition["skype_id"] : '' ;?>"></th>
				</tr>
				<tr>
					<th></th>
					<th align = "right"><button onclick = "return searchSetting()">SEARCH</button></th>
				</tr>
			</table>
		</div>
		<?	if ( isset($user_info) && !empty($user_info) ) { ?>
		<div class = "top-space-bottom"></div>
		<div class = "search-area" align = "center">
			<div>
				<table class = "table-border border-collapse" border = "0" cellpadding = "5" cellspacing = "0">
					<thead>
						<tr>
							<th colspan = "2">CubictalkId</th>
							<th>NAME</th>
							<th>NICK_NAME</th>
							<th>SKYPE_ID</th>
							<th>HP</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<th><input type = "radio" id = "searchUserId" name = "userId" onclick = "searchSetting(1)" checked></th>
							<th colspan = "5">return null</th>
						</tr>
						<?	$_selected = "";
							$disable = "";
							foreach ( $user_info as $keyUI => $ui ) {
								$_selected = "";
								$disable = "";
								if ( isset($sms_important_info["userId"]) && $sms_important_info["userId"] == $ui['User']['id'] ) {
									$_selected = "checked";
								}
								
								if ( strlen(trim($ui['User']['hp'])) < 1 ) {
									$disable = "disabled";
								}
						?>
						<input type = "hidden" name = "userMbId<?=$ui['User']['id'];?>" value = "<?=$ui['User']['mb_id'];?>">
						<input type = "hidden" name = "userHp<?=$ui['User']['id'];?>" value = "<?=$ui['User']['hp'];?>">
						<input type = "hidden" name = "userName<?=$ui['User']['id'];?>" value = "<?=$ui['User']['name'];?>">
						<input type = "hidden" name = "userNickName<?=$ui['User']['id'];?>" value = "<?=$ui['User']['nick_name'];?>">
						<tr class = "<?=$_selected;?>">
							<th><input type = "radio" id = "searchUserId" name = "userId" onclick = "searchSetting(1)" value = "<?=$ui["User"]["id"];?>" <?=$_selected;?> <?=$disable;?> ></th>
							<th><?=$ui["User"]["mb_id"];?></th>
							<th><?=$ui["User"]["name"];?></th>
							<th><?=$ui["User"]["nick_name"];?></th>
							<th><?=$ui["User"]["skype_id"];?></th>
							<th><?=$ui["User"]["hp"];?></th>
						</tr>
						<?	} ?>
					</tbody>
				</table>
			</div>
		</div>
	<?	} ?>
	</form>
	<div class = "top-space-bottom"></div>
	<?	} ?>
  	<div class = "top-space-bottom"></div>
  	<div class = "search-area">
		<form id = "smsMsg" name = "smsMessage" action = "<?=b_path();?>/smssend.php" method="post" >
			<input type="hidden" name="action" value="go">
			<input type="hidden" name="msgNumber" value="0">
			<?php
			if(!is_array($sms_important_info['userId']))
			{
				$sms_important_info['userId'] = array($sms_important_info['userId']);
			}
			foreach($sms_important_info['userId'] as $key_user_id => $k_ui)
			{
			?>
			<input type="hidden" name="user_id[]" value="<?php echo $k_ui;?>">
			<?php
			}
			?>
			<input type="hidden" name="teacher_id" value="<?php echo $this->params['form']['teacher_id'];?>">
			<table class = "table-border valign-top align-left border-collapse" cellpadding = "2" cellspacing = "0">
				<tr>
					<th class = "th-title-max-width">Message : </th>
					<th>
						<?	if (isset($sms_important_info["smsMessage"])) { ?>
						<div><span>Extend Message:</span><font color = "#FF6600"><?=$sms_important_info["smsMessage"];?></font></div>
						<?	} ?>
						<div><TEXTAREA ID="msg0" NAME="msg[0]" ROWS="3" COLS="60" onclick = "checkBytes(this)" onselect = "checkBytes(this)" onload = "checkBytes(this)" onchange = "checkBytes(this)" onkeyup = "checkBytes(this)" onkeydown = "checkBytes(this)"><?=(isset($sms_important_info["smsMessage"])) ? $sms_important_info["smsMessage"]:'';?></TEXTAREA></div>
						<div><TEXTAREA ID="msg1" NAME="msg[1]" ROWS="3" COLS="60" onclick = "checkBytes(this)" onselect = "checkBytes(this)" onload = "checkBytes(this)" onchange = "checkBytes(this)" onkeyup = "checkBytes(this)" onkeydown = "checkBytes(this)"></TEXTAREA></div>
						<div><TEXTAREA ID="msg2" NAME="msg[2]" ROWS="3" COLS="60" onclick = "checkBytes(this)" onselect = "checkBytes(this)" onload = "checkBytes(this)" onchange = "checkBytes(this)" onkeyup = "checkBytes(this)" onkeydown = "checkBytes(this)"></TEXTAREA></div>
						<div class = ""><span id = "over">0</span>/<span id = "under">0</span> <span id = "txtPage">0</span> | <span id = "bytemsg">0</span> | <span id = "strlenmsg">0</span> <!--  <input type="text" name="msg" maxlength="80" size=80> --> Maximum of 80 Bytes. </div>
					</th>
				</tr>
				<tr>
					<th class = "th-title-max-width">Student Contact : </th>
					<th>
						<div><input type="text" name="rphone" value="<?=(isset($sms_important_info["smsrphone"])) ? $sms_important_info["smsrphone"] : "" ;?>" <?=($loginInfo["management_level"] > 1) ? "" : "readonly" ;?>></div>
					</th>
				</tr>
				<tr>
					<th class = "th-title-max-width">Destination : </th>
					<th>
						<div><input type="text" name="destination" value="<?=(isset($sms_important_info["smsdestination"])) ? $sms_important_info["smsdestination"] : "" ;?>"  <?=($loginInfo["management_level"] > 1) ? "" : "readonly" ;?>></div>
					</th>
				</tr>
				<tr>
					<th>Select Phone Number: </th>
					<th>
						<input type = "radio" name = "phone_number" onclick = "changePhoneNumber('phone1')" checked>Phone 1 (070-4897-5487)<br>
						<input type = "radio" name = "phone_number" onclick = "changePhoneNumber('phone2')" >Phone 2 (070-4898-5407)
					</th>
				</tr>
				<tr class = "hide">
					<th class = "th-title-max-width">Our Contact : </th>
					<th>
						<div>
							<input type="text" name="sphone1" maxlength="4" value="070">-
							<input type="text" name="sphone2" maxlength="4" value="4897">-
							<input type="text" name="sphone3" maxlength="4" value="5487">
						</div>
					</th>
				</tr>
				<tr class = "hide">
					<th class = "th-title-max-width">Date : </th>
					<th><div><input type="text" name="rdate" maxlength="8" value = ""> 예)20090909</div></th>
				</tr>
				<tr class = "hide">
					<th class = "th-title-max-width">Time : </th>
					<th><div><input type="text" name="rtime" maxlength="6" value = "">예)173000 ,오후 5시 30분,예약시간은 최소 10분 이상으로 설정.</div></th>
				</tr>
				<tr class = "hide">
					<th class = "th-title-max-width">return url</th>
					<th><div><input type="text" name="returnurl" maxlength="64" value="<?=b_path();?>/smsresult.php" ></div></th>
				</tr>
				<tr class = "hide">
					<th class = "th-title-max-width">test flag</th>
					<th><div><input type="text" name="testflag" maxlength="1"> 예) 테스트시: Y (for test return Y)</div></th>
				</tr>
				<tr class = "hide">
					<th class = "th-title-max-width">nointeractive</th>
					<th><div><input type="text" name="nointeractive" maxlength="1"> 예) 사용할 경우 : 1, 성공시 대화상자(alert)를 생략.</div></th>
				</tr>
				<tr class = "hide">
					<th class = "th-title-max-width">Repeat Flag : </th>
					<th><div><input type="checkbox" name="repeatFlag" value="Y"></div></th>
				</tr>
				<tr class = "hide">
					<th class = "th-title-max-width">Repeat Number : </th>
					<th><div><select name="repeatNum"> 
				            <option value="1">1</option>
				            <option value="2">2</option>
				            <option value="3">3</option>
				            </select> 예) 1~10회 가능.</div></th>
				</tr>
				<tr class = "hide">
					<th class = "th-title-max-width">Repeat Time : </th>
					<th><div><select name="repeatTime"> 
				            <option value="15">15</option>
				            <option value="20">20</option>
				            <option value="25">25</option>
				            </select> 예)15분 이상부터 가능.  분마다</div></th>
				</tr>
			</table>
			<input type = "hidden" name = "teacher_user" value = "<?=( isset($search) && strlen(trim($search)) > 0 && $search == "search" ) ? "Y" : "N" ;?>">
		  </form>
		  <a class = "button" href = "javascript:void(0)" onclick = "return smsMsg()">SEND [All msg]</a>
		  <?/*<a class = "button" href = "<?=c_path();?>/lessons/teacher_management/">BACK</a>*/?>
		  <a class = "button" href = "javascript:void(0)" onclick = "window.close()">CLOSE</a>
		  <?	if ( (isset($search) && $search == "search") || isset($sms_important_info["smsrphone"]) && isset($sms_important_info["smsdestination"])) {} else { ?>
		  <font color = "#FF0000">Some data are missing!</font>
		  <?	} ?>
 	</div>
</div>
<script type="text/javascript">
	var byteIs;
	var max_byte = 80;
	var min_byte;
	var max_page = 1;
	var limitBytes = max_byte * max_page;
	var untilByte;
	var curTotalMsg;
	var lastByte; 
	var over = document.getElementById("over");
	var under = document.getElementById("under");
	var txtPage = document.getElementById("txtPage");
	var strlenPage = document.getElementById("strlenmsg");
	var byteMsg = document.getElementById("bytemsg");
	var Msg = new Array();
	Msg[0] = 0;
	Msg[1] = 0;
	Msg[2] = 0;
 	byteIs = 0;

	var byteSize1 = function getLengthInBytes(str) {
						var b = str.match(/[^\x00-\xff]/g);
						var bArray = {"byte" : (str.length + (!b ? 0: b.length)), "strlen" : str.length};
						return bArray;
					}

	function checkBytes (txtmsg) {
		byteIs = byteSize1(txtmsg.value);
		
		curText = new String(txtmsg.value);
		strlen = curText.length;
		
		if ( byteIs["byte"] > limitBytes ) {
			strlen = curText.length;
			alert(limitBytes + " bytes");
			thisText = curText.substring(0, (byteIs["strlen"] -1) );
			txtmsg.value = thisText;
			byteIs["byte"] = lastByte || byteIs["byte"];
			strlen = (byteIs["strlen"] -1);
		}
		
		lastByte = byteIs["byte"];
		curTotalMsg = Math.ceil(byteIs["byte"] / max_byte);
		txtPage.innerHTML = curTotalMsg;
		under.innerHTML = curTotalMsg * max_byte;
		over.innerHTML = byteIs["byte"];
		strlenPage.innerHTML = strlen;
		
		Msg[0] = byteSize1($("#msg0").val());
		Msg[1] = byteSize1($("#msg1").val());
		Msg[2] = byteSize1($("#msg2").val());

		byteMsg.innerHTML = Msg[0]["byte"] + Msg[1]["byte"] + Msg[2]["byte"];
	}
</script>