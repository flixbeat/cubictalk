<?php
class PaymentsController extends AppController {

	var $name = 'Payments';
	var $helpers = array('Html','Ajax','Javascript');
	var $components = array("RequestHandler","CheckHandler","LanguageHandler");

	function beforeFilter(){
		//checkLoginInfo($this);
	}
 
 function viewPdf($id = null) {
  $messageBoolean = false;
  if (isset($this->params["form"]["searchSetting"]) && $this->params["form"]["searchSetting"] == 1) {
   $id = $this->params["form"]["view_pdf"];

   if (!$id) { 
    $this->Session->setFlash('Sorry, there was no property ID submitted.'); 
    $this->redirect(array('action'=>'index'), null, true); 
   } 
   Configure::write('debug',0); // Otherwise we cannot use this method while developing 
  
  
   $id = intval($id); 
  
   $property = $this->printClasses($id); // here the data is pulled from the database and set for the view 

   if (empty($property)) { 
    $this->Session->setFlash('Sorry, there is no property with the submitted ID.'); 
    $this->redirect(array('action'=>'index'), null, true); 
   } 

   $this->layout = 'pdf'; //this will use the pdf.ctp layout 
   $this->render(); 
  } else {
   $messageBoolean = true;
  }
  $this->set("messageBoolean", $messageBoolean);
 }
 
 function printClasses ($payment_id=null) {
  Configure::write('debug',0);
  $this->loadModel("Lesson");
  $this->Payment->bindModel(array("belongsTo"=>array(
    "Lesson"=>array("className"=>"Lesson", "foreignKey"=>false, "conditions"=>"Payment.id=Lesson.payment_id")
  , "Teacher"=>array("className"=>"Teacher", "foreignKey"=>false, "conditions"=>"Lesson.teacher_id=Teacher.id")
  )));
  $fields = array(
    "Lesson.id", "Lesson.reserve_time", "Lesson.lesson_status"
  , "Teacher.id", "Teacher.tea_name"
  );
  $printClasses = $this->Payment->find("all", array("fields"=>$fields, "conditions"=>array("Lesson.payment_id"=>$payment_id), "order"=>"Lesson.reserve_time ASC"));
  $this->set("printClasses", $printClasses);

  return $printClasses;
 }
	
	 function index(){
		$loginInfo = $this->CheckHandler->checkLoginInfo();
		$mb_id = $loginInfo['User']['mb_id'];
	  
		$this->loadModel('Dictionary');
		$this->loadModel('DictionaryDetail');
		$dictionary_payment_status = $this->Dictionary->find('first',array('fields'=>array('id')
				,'conditions'=>array('name'=>'payment_status')));
		$payment_status_id = $dictionary_payment_status['Dictionary']['id'];
// 		$dictionary_payment_status = $this->DictionaryDetail->find('all',array('fields'=>array('*')
// 				,'conditions'=>array('dictionary_id'=>$dictionary_payment_status['Dictionary']['id'])));
// 		$payment_status = array();
// 		foreach($dictionary_payment_status as $keydictionary_payment_status => $kdps){
// 			$name = $kdps['DictionaryDetail']['name'];
// 			if(strlen($kdps['DictionaryDetail']['express_name_ko']) > 0){
// 				$payment_status[$name] = $kdps['DictionaryDetail']['express_name_ko'];
// 			}elseif(strlen($kdps['DictionaryDetail']['express_name_en']) > 0){
// 				$payment_status[$name] = $kdps['DictionaryDetail']['express_name_en'];
// 			}
// 		}
// 		$this->set('payment_status',$payment_status);
		
	// 	$payment_info = $this->Payment->find('all',array(
	// 			'order' => 'Payment.created DESC'
	// 			,'fields' => array('Payment.id','Payment.mb_id','Payment.start_day','Payment.last_day'
	// 				,'Payment.bonus_update'
	// 				,'Payment.amount'
	// 				,'Payment.goods_name'
	// 				,'Payment.goods_months'
	// 				,'Payment.lesson_num'
	// 				,'Payment.amount'
	// 				,'Payment.status'
	// 				,'Payment.pay_ok'
	// 				,'Payment.created')
	// 			,'conditions' => array('Payment.mb_id' => $mb_id,'Payment.pay_ok' => 'Y')
	// 			,'limit'=>20
	// 			,'passit'=>$this->passedArgs
	// 	));
	
		$payment_kinds = $this->Dictionary->find('first',array('fields'=>array('id')
				,'conditions'=>array('name'=>'payment_kinds')));
		$payment_kinds = $payment_kinds['Dictionary']['id'];
		$pay_ok = array('Y','C','PC');
		$fields = array('Payment.id','Payment.mb_id','Payment.start_day','Payment.last_day'
				,'Payment.bonus_update','Payment.amount','Payment.goods_name','Payment.goods_months'
				,'Payment.lesson_num','Payment.amount','Payment.status','Payment.pay_ok','Payment.created'
				,'DictionaryDetail.name','DictionaryDetail.express_name_en','DictionaryDetail.express_name_ko'
				,'DictionaryDetail2.name','DictionaryDetail2.express_name_en','DictionaryDetail2.express_name_ko');
		$conditions = array('Payment.mb_id' => $mb_id,'Payment.pay_ok' => $pay_ok);
		$this->Payment->bindModel(array('belongsTo'=>array(
				'DictionaryDetail'=>array('className'=>'DictionaryDetail'
						,'type'=>'left'
						,'foreignKey'=>false
						,'conditions'=>'Payment.goods_name=DictionaryDetail.name AND DictionaryDetail.dictionary_id='. $payment_kinds)
				,'DictionaryDetail2'=>array('className'=>'DictionaryDetail'
						,'type'=>'left'
						,'foreignKey'=>false
						,'conditions'=>'Payment.pay_ok=DictionaryDetail2.name AND DictionaryDetail2.dictionary_id='. $payment_status_id)
		)),false);
		$this->paginate=array(
				'fields'=>$fields
				,'conditions'=>$conditions
				,'order'=>array('Payment.created DESC')
				,'limit'=>20
				,'passit'=>$this->passedArgs
		);
		$payment_info = $this->paginate('Payment');
		if(count($payment_info) > 0){
			$this->set('user_last_day', $loginInfo['User']['last_day']);
		}
		$this->set('payment_info', $payment_info);

		# dave
		$language = $this->LanguageHandler->set_lang();
		$this->set('language',$language);
	}

	function info(){
		if($this->RequestHandler->isAjax()){
			$loginInfo = $this->CheckHandler->checkLoginInfo();
			Configure::write('debug',0);
			$_p = array(1 => $this->params['form']['id'], 2 => $loginInfo['User']['id'], 3 => $this->params['form']['sday'], 4 => $this->params['form']['lday']);
			$_d = array(1 => $this->Payment->query("SELECT count(Lesson.lesson_status) as F FROM lessons as Lesson WHERE Lesson.user_id = '$_p[2]' AND Lesson.payment_id = '$_p[1]' AND Lesson.lesson_status = 'F'"),
						2 => $this->Payment->query("SELECT count(Lesson.lesson_status) as A FROM lessons as Lesson WHERE Lesson.user_id = '$_p[2]' AND Lesson.payment_id = '$_p[1]' AND Lesson.lesson_status = 'A'"),
						3 => $this->Payment->query("SELECT count(Lesson.lesson_status) as R FROM lessons as Lesson WHERE Lesson.user_id = '$_p[2]' AND Lesson.payment_id = '$_p[1]' AND Lesson.lesson_status = 'R'"),
						4 => $this->Payment->query("SELECT count(Lesson.lesson_status) as CP FROM lessons as Lesson WHERE Lesson.user_id = '$_p[2]' AND Lesson.payment_id = '$_p[1]' AND Lesson.lesson_status = 'CP'")
					//	,5 => $this->Payment->query("SELECT count(Lesson.lesson_status) as EM FROM lessons as Lesson WHERE Lesson.user_id = '$_p[2]' AND Lesson.reserve_time >= '$_p[3]' AND Lesson.reserve_time <= '$_p[4]' AND (Lesson.lesson_status = 'E' OR Lesson.lesson_status = 'M')")
					);
			
			$content_page  = '<ul>';
			$content_page .= '	<li>';
			$content_page .= '		<div id="div-instruction" style="background: #FFFFFF; font-family: Arial Black; color: #000; padding: 5px;" align="left">결제기간별로 받으신 수업을 표시합니다. 숫자를 누르시면 상세를 보실 수 있습니다.</div>';
			$content_page .= '	</li>';
			
			$content_page .= '	<li>';
			$content_page .= '		<div id="tbl-infos" style="background: #FFFFFF; padding: 5px;">';
			$content_page .= '		<div id="div-title" style="background: #FFFFFF; padding: 5px;" align="center">';
			$content_page .= '<table>';
			$content_page .= '	<thead>';
			$content_page .= '		<tr><th><div style="border:1px dashed #FFFFFF;">결제기간</div></th></tr>';
			$content_page .= '		<tr><th>';
			$content_page .= '<table border="1" style="border-collapse:collapse; width: 600px;">';
			$content_page .= '	<thead>';
			$content_page .= '		<tr>';
			$content_page .= '			<th>START DAY:</th>';
			$content_page .= '			<th>'.$_p[3].'</th>';
			$content_page .= '			<th>LAST DAY</th>';
			$content_page .= '			<th>'.$_p[4].'</th>';
			$content_page .= '		</tr>';
			$content_page .= '	</thead>';
			$content_page .= '</table>';
			$content_page .= '		</th></tr>';
			$content_page .= '	</thead>';
			$content_page .= '</table>';
			
			$content_page .= '		</div>';
			$content_page .= '<table class="tbl-payment" border="1" style="border-collapse:collapse;">';
			$content_page .= '	<thead>';
			$content_page .= '		<tr>';
			
			$content_page .= '			<th class="th-payment" style="width: 100px;">예약상태</th>';
			
			$content_page .= '			<th class="th-payment" style="width: 100px;">수업완료</th>';
			$content_page .= '			<th class="th-payment" style="width: 100px;">결석</th>';
			
			$content_page .= '			<th class="th-payment" style="width: 100px;">포인트전환</th>';
			
			//$content_page .= '			<th class="th-payment" style="width: 100px;">연장</th>';
			$content_page .= '			<th class="th-payment" style="width: 300px;">총 받으신 수업 (수업완료 + 결석 + 포인트전환)</th>';
			$content_page .= '		</tr>';
			$content_page .= '	</thead>';
			$content_page .= '	<tbody>';
			$content_page .= '		<tr>';
			
			$content_page .= '			<th class="th-payment" ';
										if ($_d[3][0][0]['R'] > 0) :
			$content_page .= 'onclick="javascript:status('."'R', '$_p[1]', '$_p[3]', '$_p[4]'".')"';
										endif;
			$content_page .= '>'.$_d[3][0][0]['R'];
			$content_page .= '</th>';
			
			$content_page .= '			<th class="th-payment" ';
										if ($_d[1][0][0]['F'] > 0) :
			$content_page .= ' 			onclick="javascript:status('."'F', '$_p[1]', '$_p[3]', '$_p[4]'".')"';
										endif;
			$content_page .= '>'.$_d[1][0][0]['F'];
			$content_page .= '</th>';
			
			$content_page .= '			<th class="th-payment" ';
										if ($_d[2][0][0]['A'] > 0) :
			$content_page .= 'onclick="javascript:status('."'A', '$_p[1]', '$_p[3]', '$_p[4]'".')"';
										endif;
			$content_page .= '>'.$_d[2][0][0]['A'];
			$content_page .= '</th>';
			
			$content_page .= '			<th class="th-payment" ';
										if ($_d[4][0][0]['CP'] > 0) :
			$content_page .= 'onclick="javascript:status('."'CP', '$_p[1]', '$_p[3]', '$_p[4]'".')"';
										endif;
			$content_page .= '>'.$_d[4][0][0]['CP'];
			$content_page .= '</th>';
			
			//$content_page .= '			<th class="th-payment" ';
			//							if ($_d[5][0][0]['EM'] > 0) :
			//$content_page .= 'onclick="javascript:status('."'EM', '$_p[3]', '$_p[4]'".')"';
			//							endif;
			//$content_page .= '>'.$_d[5][0][0]['EM'];
			//$content_page .= '</th>';
			
			$content_page .= '			<th class="th-payment">'.($_d[1][0][0]['F']+$_d[2][0][0]['A']+$_d[4][0][0]['CP']).'</th>';
			$content_page .= '		</tr>';
			$content_page .= '	</tbody>';
			$content_page .= '</table>';
			
			$content_page .= '		</div>';
			$content_page .= '	</li>';
			$content_page .= '	<li>';
			$content_page .= '		<div id="tbl-details" style="cursor:pointer; background: #FFFFFF; padding: 5px; height: 305px; overflow:auto;" align="left">';
			$content_page .= '		</div>';
			$content_page .= '	</li>';
			$content_page .= '	<li>';
			$content_page .= '		<div id="close" onclick="javascript:divClose('.$_p[1].')" style="cursor:pointer; height: 40px; width: 500px;"></div>';
			$content_page .= '	</li>';
			$content_page .= '</ul>';
			
			echo $content_page;
			
		}
	}
	
	function status(){
		if($this->RequestHandler->isAjax()){
			$loginInfo = $this->CheckHandler->checkLoginInfo();
			Configure::write('debug',0);
			$_p = " Lesson.lesson_status = '".$this->params['form']['id']."'";
			if ($this->params['form']['id'] == 'EM') :
				$_p = " (Lesson.lesson_status = 'E' OR Lesson.lesson_status = 'M')";
			endif;
			$_p = array(1 => $_p, 2 => $loginInfo['User']['id'], 3 => $this->params['form']['sday'], 4 => $this->params['form']['lday'], 5 => $this->params['form']['info']);
			$_d = $this->Payment->query("SELECT Lesson.id,Lesson.reserve_time, Teacher.tea_name, Teacher.img_file, Lesson.reservation_kinds, Lesson.lesson_status FROM lessons as Lesson JOIN teachers as Teacher ON (Teacher.id = Lesson.teacher_id) WHERE Lesson.user_id = '$_p[2]' AND Lesson.payment_id = '$_p[5]' AND $_p[1]");
			
			$status = array('R' => '예약상태', 'F' => '수업완료', 'A' => '결석', 'CP' => '포인트전환');
			$kinds = array('M' => '고정수업', 'K' => '큐빅수업', 'P' => '포인트수업');
			$content_page  = '<div class="div-title" align="center"><span >'.$status[$this->params['form']['id']].'</span></div>';;
			$content_page .= '<div><table class="tbl-payment" border="1" style="border-collapse:collapse;" width="100%">';
			$content_page .= '	<thead>';
			$content_page .= '		<tr>';
			$content_page .= '			<th class="th-payment text-center">#</th>';
			$content_page .= '			<th class="th-payment text-center">Lesson Time</th>';
			$content_page .= '			<th class="th-payment text-center">Teacher</th>';
			$content_page .= '			<th class="th-payment text-center">예약종류</th>';
			$content_page .= '			<th class="th-payment text-center">코멘트</th>';
			$content_page .= '		</tr>';
			$content_page .= '	</thead>';
			$content_page .= '	<tbody>';
			$_i = 1;
			
			foreach ($_d as $_pdata) :
			$lesson_id=$_pdata['Lesson']['id'];
			$content_page .= '		<tr>';
			$content_page .= '			<th class="th-payment text-center" align="center">'.$_i.'</th>';
			$content_page .= '			<th class="th-payment text-center" align="center">'.$_pdata['Lesson']['reserve_time'].'</th>';
			$content_page .= '			<th class="th-payment text-center" align="center"><table><tr><th class="text-center" style="padding:5px;"><img src="'.c_path().$_pdata['Teacher']['img_file'].'" width="60" height="60" alt="'.$_pdata['Teacher']['tea_name'].'" /></th></tr><tr><th class="text-center" style="padding:5px;">'.$_pdata['Teacher']['tea_name'].'</th></tr></table></th>';
			$content_page .= '			<th class="th-payment text-center" align="center">'.$kinds[$_pdata['Lesson']['reservation_kinds']].'</th>';
			$content_page .= '			<th class="th-payment text-center" align="center"><a href="javascript:void(0)" onclick="comment('.$lesson_id.')"><img src="'.c_path().'/image/mypage/comment.png" border="0" width="35" height="35" title="코" alt="코"></a></th>';
			$content_page .= '		</tr>';
			$_i = $_i + 1;
			endforeach;
			$content_page .= '	</tbody>';
			$content_page .= '</table></div>';
				
			
			echo $content_page;
				
		}
	}
	
	function receipt ($idA = null, $idB = null) {
		$loginInfo = $this->CheckHandler->checkLoginInfo();
		$_p = array(1 => $loginInfo['User']['mb_id'], 2 => $loginInfo['User']['name']);
		$this->loadModel("PaymentStatistic");
		$this->PaymentStatistic->bindModel(array("belongsTo"=>array("Payment"=>array("foreignkey"=>"payment_id"))));
		$exist = $this->PaymentStatistic->find("all", array("fields"=>array("Payment.goods_name", "Payment.amount", "Payment.start_day", "Payment.last_day", "PaymentStatistic.finish", "PaymentStatistic.absent", "PaymentStatistic.start_day", "PaymentStatistic.last_day"), "conditions"=>array("Payment.id"=>$idA, "Payment.mb_id"=>"$_p[1]", "Payment.pay_ok"=>"Y") ));
		
		if (count($exist) > 0) {
			$_p[3] = $exist;
			
			if (isset($exist[0]["PaymentStatistic"]["start_day"]) || isset($exist[0]["PaymentStatistic"]["last_day"])) {
				
			} else {
				$getDays = $this->Payment->query("SELECT Payment.goods_name, Payment.amount, Payment.start_day, Payment.last_day FROM payments as Payment WHERE Payment.id = '$idA' AND Payment.mb_id = '$_p[1]' AND Payment.pay_ok = 'Y'");
			}
			
			$days["start_day"] = (isset($exist[0]["PaymentStatistic"]["start_day"])) ? $exist[0]["PaymentStatistic"]["start_day"] : $getDays[0]['Payment']['start_day'];
			$days["last_day"]  = (isset($exist[0]["PaymentStatistic"]["last_day"])) ? $exist[0]["PaymentStatistic"]["last_day"] : $getDays[0]['Payment']['last_day'];

			$_p[4] = array(0=>array(0=>array("Absent"=>$exist[0]["PaymentStatistic"]["absent"])));
			$_p[5] = array(0=>array(0=>array("Finish"=>$exist[0]["PaymentStatistic"]["finish"])));
		} else {
			$_p[3] = $this->Payment->query("SELECT Payment.goods_name, Payment.amount, Payment.start_day, Payment.last_day FROM payments as Payment WHERE Payment.id = '$idA' AND Payment.mb_id = '$_p[1]' AND Payment.pay_ok = 'Y'");

			$days["start_day"] = $_p[3][0]['Payment']['start_day'];
			$days["last_day"] = $_p[3][0]['Payment']['last_day'];
			
			$day[1] = date('Y-m-d 02:00:00', strtotime(date('Y-m-d', strtotime($_p[3][0]['Payment']['start_day']))));
			$day[2] = date('Y-m-d 02:00:00', strtotime(date('Y-m-d', strtotime($_p[3][0]['Payment']['last_day'])+(60*60*24))));
	
			$_p[4] = $this->Payment->query("SELECT COUNT(*) as Absent FROM lessons as Lesson JOIN users as User ON (Lesson.user_id = User.id) JOIN payments as Payment ON (Payment.mb_id = User.mb_id)  WHERE Payment.id = '$idA' AND Payment.mb_id = '$_p[1]' AND Payment.pay_ok = 'Y' AND Lesson.lesson_status = 'A' AND Lesson.reserve_time >= '$day[1]' AND Lesson.reserve_time <= '$day[2]'");
			$_p[5] = $this->Payment->query("SELECT COUNT(*) as Finish FROM lessons as Lesson JOIN users as User ON (Lesson.user_id = User.id) JOIN payments as Payment ON (Payment.mb_id = User.mb_id)  WHERE Payment.id = '$idA' AND Payment.mb_id = '$_p[1]' AND Payment.pay_ok = 'Y' AND Lesson.lesson_status = 'F' AND Lesson.reserve_time >= '$day[1]' AND Lesson.reserve_time <= '$day[2]'");
		}
		//pr($_p);
		$this->set('_p',$_p);
		$this->set('_days',$days);
	}
	
	function paymentinfo($idA = null, $idB = null, $idC = null) {
		$loginInfo = $this->CheckHandler->checkLoginInfo();
		$_p = array(1 => $loginInfo['User']['mb_id'], 2 => $loginInfo['User']['name'], 3 => $loginInfo['User']['last_day'], 4 => $loginInfo['User']['c_number']);
		$_p[5] = $this->Payment->query("SELECT Payment.id, Payment.lesson_num, Payment.amount, Payment.bonus_update, Payment.start_day, Payment.last_day, Payment.created FROM payments as Payment WHERE Payment.id = '$idA' AND Payment.mb_id = '$_p[1]' AND Payment.pay_ok = 'Y'");
		$day[1] = date('Y-m-d 02:00:00', strtotime(date('Y-m-d', strtotime($_p[5][0]['Payment']['start_day']))));
		$day[2] = date('Y-m-d 02:00:00', strtotime(date('Y-m-d', strtotime($_p[5][0]['Payment']['last_day'])+(60*60*24))));
		
		$_p[6] = $this->Payment->query("SELECT COUNT(*) as Class FROM lessons as Lesson JOIN users as User ON (Lesson.user_id = User.id) JOIN payments as Payment ON (Payment.mb_id = User.mb_id)  WHERE Payment.id = '$idA' AND Payment.mb_id = '$_p[1]' AND Payment.pay_ok = 'Y' AND Lesson.lesson_status IN ('R','F','A') AND Lesson.reserve_time >= '$day[1]' AND Lesson.reserve_time <= '$day[2]' AND Lesson.reservation_kinds = 'K'");
		if ($idC != null) :
			$_p[7] = $idC;
		endif;
		$this->set('_p',$_p);
	}
	
	function additional ($idA = null, $idB = null) {
		if ($idB != null) {
			$loginInfo = $this->CheckHandler->checkLoginInfo();
			$_p = array(1 => $loginInfo['User']['id'], 2 => $loginInfo['User']['mb_id'], 3 => $this->params['form']['additional']);
			$this->Payment->query("UPDATE payments SET bonus_update = 'Y' WHERE id = '$idA'");
			$this->Payment->query("UPDATE users SET c_number = '$_p[3]' WHERE id = '$_p[1]'");
			$_p = '/paymentinfo/'.$idA.'/'.strtotime(date('Y-m-d H:i:s')).'/'.'true/';
			$this->redirect(array('action' => $_p));
		}
	}
}

