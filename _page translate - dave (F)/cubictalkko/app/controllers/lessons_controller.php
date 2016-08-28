<?php
class LessonsController extends AppController {
 	//header("Content-Type: text/html; Charset=UTF-8"); 
	var $name = 'Lessons';  
	var $helpers = array('Html','Ajax','Js','Javascript','Paginator');
	var $components = array(
			'DefaultHandler'
			,'RequestHandler'
			,'ModelHandler'
			,'CheckHandler'
			,'ModelDefaultHandler'
			,'Programmer1Handler'
			,'HomeworkHandler'
			,'LanguageHandler'
	);
//	var $paginate = array('Lesson' => array('limit' => 5));
	var $HoldStatusRestrictDate = '2015-01-01';
	var $point_begin = 10; // 10 point will receive by default
	
	function beforeFilter(){
		// セッションのチェック
		checkLoginInfo($this);
	}
	
	function afterFilter(){
		//check not read memo
		doAfterController($this);
	}
	
	function getMyPayment($conditions = array()){
		if(count($conditions) > 0){
			$this->loadModel('Dictionary');
			$dictionary = $this->Dictionary->find('first',array('fields'=>array('id')
					,'conditions'=>array('name'=>'payment_kinds')));
			$dictionary_id = $dictionary['Dictionary']['id'];
			
			$this->loadModel('Payment');
			$this->Payment->bindModel(array('belongsTo'=>array(
					'DictionaryDetail'=>array(
							'className'=>'DictionaryDetail'
							,'type'=>'left'
							,'foreignKey'=>false
							,'conditions'=>'DictionaryDetail.name=Payment.goods_name AND DictionaryDetail.dictionary_id = '. $dictionary_id)
			)),false);
		
			$payment_list = $this->Payment->find('all',array(
					'fields'=>array('Payment.*','DictionaryDetail.*')
					,'conditions'=>$conditions
					,'order'=>'Payment.created DESC'));
			return $payment_list;
		}
		else {
			return array();
		}
	}
	
	function getPaymentAndLesson()
	{
		$loginInfo = $this->Session->read('loginInfo');
		$today = date('Y-m-d');

		$this->loadModel('Dictionary');
		$dictionary = $this->Dictionary->find('first',array('fields'=>array('id')
				,'conditions'=>array('name'=>'payment_kinds')));
		$dictionary_id = $dictionary['Dictionary']['id'];

		$this->loadModel('Payment');
		$this->Payment->bindModel(array('belongsTo'=>array(
				'DictionaryDetail'=>array(
						'className'=>'DictionaryDetail'
						,'type'=>'left'
						,'foreignKey'=>false
						,'conditions'=>'DictionaryDetail.name=Payment.goods_name AND DictionaryDetail.dictionary_id = '. $dictionary_id)
		)
		));
		
		$payment_list = $this->Payment->find('all',array(
				'fields'=>array(
						'Payment.*'
						,'DictionaryDetail.*'
				)
				,'conditions'=>array(
						'Payment.pay_ok'=>'Y'
						,'Payment.mb_id'=>"{$loginInfo['User']['mb_id']}"
						,'DATE(Payment.last_day) >=' => "{$today}"
				)
				,'order'=>'Payment.created ASC'
		));

		$payment_info = array();
		$lesson_with_payment_id = array();

		if(isset($payment_list)
				&& count($payment_list) > 0)
		{
			$pid = array();

			foreach($payment_list as $key_payment_list => $k_pl){
				$pid[] = $k_pl['Payment']['id'];
				$payment_info[$k_pl['Payment']['id']] = array('Payment'=>$k_pl['Payment']
						,'DictionaryDetail'=>$k_pl['DictionaryDetail']);
			}

			$this->loadModel('Lesson');
			$this->Lesson->bindModel(array('belongsTo'=>array(
					'Payment'=>array('className'=>'Payment','type'=>'left','foreignKey'=>false,'conditions'=>'Lesson.payment_id=Payment.id')
			)));
			$lesson_with_payment_id = $this->Lesson->find('all',array(
				'fields'=>array(
					'Lesson.id'
					,'Lesson.lesson_status'
					,'Lesson.payment_id'
					,'count(Lesson.id) as total_lesson_status'
					,'MAX(Lesson.reserve_time) as max_reserve_time'
					)
				,'conditions'=>array(
						'Lesson.payment_id'=>$pid
						,'Lesson.user_id'=>"{$loginInfo['User']['id']}"
						,'Lesson.lesson_status'=>$this->Programmer1Handler->lesson_status
					)
				,'group'=>array('Lesson.payment_id','Lesson.lesson_status')
				,'order'=>'Payment.created ASC'
			));
		}

		$user_start_day = date('Y-m-d',strtotime($loginInfo['User']['start_day']));
		$user_last_day = date('Y-m-d',strtotime($loginInfo['User']['last_day']));

		$lesson_no_payment_id = $this->Lesson->find('all',array(
			'fields'=>array(
				'Lesson.id'
				,'Lesson.lesson_status'
				,'Lesson.payment_id'
				,'count(Lesson.id) as total_lesson_status'
				,'MAX(Lesson.reserve_time) as max_reserve_time'
			)
			,'conditions'=>array(
				'Lesson.user_id'=>$loginInfo['User']['id']
				,'Lesson.payment_id'=>0
				,'Lesson.lesson_status'=>$this->Programmer1Handler->lesson_status
				,'DATE(Lesson.reserve_time) >= '=>"{$user_start_day}"
				,'DATE(Lesson.reserve_time) <= '=>"{$user_last_day}"
			)
			,'group'=>array('Lesson.payment_id','Lesson.lesson_status')
		));

		//$getPaymentAndLesson = array_merge($lesson_with_payment_id,$lesson_no_payment_id);

		// Get All 'Fix' reservation_kinds
		$dictionary = $this->Dictionary->find('first',array('fields'=>array('id'),'conditions'=>array('name'=>'fixed_payment_kinds')));
		$RenewGetAllReservationKinds = array('M','MD','M3','M2','LANDLINE10','MOBILE10','LANDLINE20','MOBILE20','TTEM3','TMEM3','TTEM2','TMEM2');
		if(isset($dictionary['Dictionary']['id'])){
			$this->loadModel('DictionaryDetail');
			$dictionary = $this->DictionaryDetail->find('all',array('fields'=>array('express_name_en'),'conditions'=>array('dictionary_id'=>$dictionary['Dictionary']['id'])));
			foreach ($dictionary as $key_dictionary => $kd) {
				$RenewGetAllReservationKinds[] = $kd['DictionaryDetail']['express_name_en'];
			}
		}
		$this->set('RenewGetAllReservationKinds',$RenewGetAllReservationKinds);

		$this->set('payment_info',$payment_info);
		$this->set('payment_list',$payment_list);
		$this->set('lesson_with_payment_id',$lesson_with_payment_id);
		$this->set('lesson_no_payment_id',$lesson_no_payment_id);
		$this->set('lesson_status_value',$this->Programmer1Handler->lesson_status_value);

		//return $getPaymentAndLesson;
	}
	// End function getPaymentAndLesson()
	
	function testmail(){
		$loginInfo = $this->Session->read('loginInfo');
		if(isset($loginInfo['User']['class_receive_mail'])
				&& $loginInfo['User']['class_receive_mail'] > 0){
			// Mail System
			$this->requestAction('/phpmailers/mail/',array('form'=>array('post'=>array('Lesson'=>array('id'=>936838)),'mail_type'=>'Extend')));
		}
	}
	
	function getMyCurrentBook($user_id=null,$mb_id=null){
		if(empty($user_id)){
			$loginInfo = $this->Session->read('loginInfo');
			$user_id = $loginInfo['User']['id'];
		}
		$this->loadModel('Bookmark');
		$this->Bookmark->bindModel(array('belongsTo'=>array(
				'Ftpbook'=>array('className'=>'Ftpbook'
						,'foreignKey'=>false
						,'type'=>'LEFT'
						,'conditions'=>'Bookmark.ftpbook_id=Ftpbook.id'
		))),false);
		$getMyCurrentBook = $this->Bookmark->find('first',array('fields'=>array('Bookmark.id'
				,'Ftpbook.id'
				,'Ftpbook.ftpsite_id'
				,'Ftpbook.symbol'
		)
				,'conditions'=>array('Bookmark.user_id'=>$user_id
						,'Bookmark.currentbook'=>'Y'
				)
				,'order'=>'Bookmark.modified DESC'
		));
		$getMyCurrentBook = array('getMyCurrentBook'=>$getMyCurrentBook);
		//$this->set('getMyCurrentBook',$getMyCurrentBook);
		return $getMyCurrentBook;
	} // End function getMyCurrentBook($user_id=null,$mb_id=null){}
	
	function setReceiveMailClass(){
		$loginInfo = $this->Session->read('loginInfo');
		if($this->RequestHandler->isAjax()){
			Configure::write('debug',0);
			$this->layout = 'ajax';
			if($this->params['form']['action'] == 'post'){
				if($this->params['form']['id'] == $loginInfo['User']['id']){
					$receive_mail_class = $this->params['form']['ReceiveMailClass'];
					$this->loadModel('User');
					$extend_class_mail_status = 0;
					if($receive_mail_class == 1){
						// To subtitute
						$extend_class_mail_status = 1;
						$this->User->updateAll(array('class_receive_mail'=>1),array('id'=>$loginInfo['User']['id']));
						$setReceiveMailClass = 'All classes are substitute of another teacher.';
					}else{
						// To extend
						$extend_class_mail_status = 0;
						$this->User->updateAll(array('class_receive_mail'=>$extend_class_mail_status),array('id'=>$loginInfo['User']['id']));
						$setReceiveMailClass = 'All classes are extended.';
					}
					$loginInfo = $this->User->find('first',array('fields'=>array('*'),'conditions'=>array('id'=>$loginInfo['User']['id'])));
					$this->Session->write('loginInfo',$loginInfo);
					if(isset($loginInfo['User']['class_receive_mail'])){
						$loginInfo['User']['class_receive_mail'] = $extend_class_mail_status;
						$this->Session->write('loginInfo',$loginInfo);
					}
				}
				echo json_encode(array('setReceiveMailClass'=>$setReceiveMailClass));
			}
		}
	} // End function setSubstituteClass(){}
	
	function setSubstituteClass(){
		$loginInfo = $this->Session->read('loginInfo');
		if($this->RequestHandler->isAjax()){
			Configure::write('debug',0);
			$this->layout = 'ajax';
			if($this->params['form']['action'] == 'post'){
				if($this->params['form']['id'] == $loginInfo['User']['id']){
					$subtitute = $this->params['form']['SubstituteClass'];
					$this->loadModel('User');
					$classes_status = 0;
					if($subtitute == 1){
						// To subtitute
						$classes_status = 1;
						$this->User->updateAll(array('classes_status'=>1),array('id'=>$loginInfo['User']['id']));
						$setSubstituteClass = 'All classes are substitute of another teacher.';
					}else{
						// To extend
						$classes_status = 0;
						$this->User->updateAll(array('classes_status'=>$classes_status),array('id'=>$loginInfo['User']['id']));
						$setSubstituteClass = 'All classes are extended.';
					}
					$loginInfo = $this->User->find('first',array('fields'=>array('*'),'conditions'=>array('id'=>$loginInfo['User']['id'])));
					$this->Session->write('loginInfo',$loginInfo);
					if(isset($loginInfo['User']['classes_status'])){
						$loginInfo['User']['classes_status'] = $classes_status;
						$this->Session->write('loginInfo',$loginInfo);
					}
				}
				echo json_encode(array('setSubstituteClass'=>$setSubstituteClass));
			}
		}
	} // End function setSubstituteClass(){}

	function lesson_cancel_ok_2($lesson_id = null){
		global $const; 
		$this->set('const',$const);
		$loginInfo = $this->Session->read('loginInfo');
		
		$point_deduction = 10; // as Default point deduction;
		if(isset($this->params['form']['point_deduction'])){
			$point_deduction = $this->params['form']['point_deduction'];
		}
		if(isset($this->params['form']['point_deduction_confirm'])
				&& $this->params['form']['point_deduction_confirm'] == true){
			$point_deduction = $this->params['form']['point_deduction'];
		} // End if(isset($this->params['form']['point_deduction_confirm'])
				// && $this->params['form']['point_deduction_confirm'] == true){}
		
		$pay_plan = $loginInfo['User']['pay_plan'];
		$user_id = $loginInfo['User']['id'];
		$types = $loginInfo['User']['types'];
		$mb_id = $loginInfo['User']['mb_id'];
		$nick_name = $loginInfo['User']['nick_name'];
		$user_last_day = $loginInfo['User']['last_day'];
		$message = '';
		
		$lesson_id = $this->params['form']['lesson_id'];
		$cancel_type = $this->params['form']['type'];
		
		$results=$this->Lesson->find('first',array('fields'=>array('*'),'conditions'=>array('id'=>$lesson_id)));
		$lesson_model = $results['Lesson'];
		
		$reservation_kinds = $lesson_model['reservation_kinds'];
		$lesson_id2 = $lesson_model['id'];
		$payment_id = $lesson_model['payment_id'];
		$reserve_time = $lesson_model['reserve_time'];
		$teacher_id = $lesson_model['teacher_id'];
		
		// Find also the Available_lesson without base on Lesson.available_lesson_id;
		$this->loadModel('Available_lesson');
		$available_lesson_info = $this->Available_lesson->find('first',array('fields'=>array('Available_lesson.id')
				,'conditions'=>array('available_time'=>"$reserve_time",'teacher_id'=>$teacher_id,'reserve_status'=>'F')));
		$available_lesson_info=$available_lesson_info['Available_lesson']['id'];
		
		// Get the Teacher Name
		$this->loadModel('Teacher');
		$teacher = $this->Teacher->find('first',array('fields'=>array('Teacher.id,Teacher.tea_name')
				,'conditions'=>array('id'=>$teacher_id)));
		$teacher_name = $teacher['Teacher']['tea_name'];
		
		// 1hour before
		$time_difference = (time() + 60*60*1) - (strtotime($lesson_model['reserve_time']));
		// 24hour before 2011-01-16 start
		$time_difference24 = (time() + 60*60*24) - (strtotime($lesson_model['reserve_time']));
		// 24hour before 2011-01-28 start
		// Point plan 12hours
		$time_difference12 = (time() + 60*60*12) - (strtotime($lesson_model['reserve_time']));
		// Point plan change from 06hours -> lesson_time
		$time_difference06 = (time() + 60*60*6) - (strtotime($lesson_model['reserve_time']));
		
		$spent_point = 0;
		$reservation_sentence = '';
		
		// This function accept other reservation_kinds FIXED SCHEDULE
		// Not include the reservation_kinds M,MD,M2,M3 were already used
		$OtherCancelReservationKinds=$this->Programmer1Handler->OtherCancelReservationKinds();
		$this->set('OtherCancelReservationKinds',$OtherCancelReservationKinds);
		
		if($reservation_kinds == 'K'){
			$spent_point = 0;
			$reservation_sentence = '큐빅예약';
		}
		elseif($reservation_kinds == 'M'){
			//  2011-11-01 modified
			//  In case of cancellation , back to point 5 or 10
			//  $spent_point = 10; from original status
			$spent_point = $point_deduction;
			$reservation_sentence = '일괄예약';
		}
		elseif(in_array($reservation_kinds,array('M3','M2'))){
			//  In case of cancellation , back to point 5 or 10
			//  $spent_point = 10; from original status
			$spent_point = $point_deduction;
			$reservation_sentence = '일괄예약';
		}
		elseif($reservation_kinds == $const['P']){
			$spent_point = $lesson_model['spend_point'];
			$reservation_sentence = '포인트예약';
		}
		elseif($reservation_kinds == 'MD'){
			//  2011-11-01 modified
			//  In case of cancellation , back to point 5 or 10
			$spent_point = $point_deduction;
			$reservation_sentence = '낮시간 할인';
		}
		elseif($reservation_kinds == 'T'){
			$spent_point = 10;
			$reservation_sentence = '체험수업';
		}
		elseif($reservation_kinds == 'TM'){ // Test Mobile
			$spent_point = 10;
			$reservation_sentence = '체험수업';
		}
		// Other reservation_kinds
		elseif(in_array($reservation_kinds,$OtherCancelReservationKinds)){
			$spent_point = 10;
			$reservation_sentence = 'New(OTHER) Reservation Kinds';
		}// End if($reservation_kinds == 'K'){}elseif($reservation_kinds == 'M'){}
		// elseif($reservation_kinds == $const['P']){}elseif($reservation_kinds == 'MD'){}
		// elseif($reservation_kinds == 'T'){};
		
		// Spend point
		// if pay plan is M,E1,E2,E3,E4
		if($reservation_sentence == '포인트예약' || $reservation_sentence == '일괄예약'
				|| $reservation_sentence = '체험수업'){
			// If it is trial lesson , do not anything 10point restore
			if($pay_plan == 'M'){
			// If User is E1,E2,E3,E4, within 0hours reservation time < 12 hour
			}else{
				// 12hours
				if($time_difference06 > 0 && $time_difference < 0){$spent_point = $spent_point * 0.5;}
				// 2012-01-08 cancel type M(extend class after last class)
				if($cancel_type == 'M'){$spent_point = 0;}
			} // End if ($pay_plan == 'M');
		} // End if ($reservation_sentence == '포인트예약' || $reservation_sentence == '일괄예약' || $reservation_sentence = '체험수업');
		
		if(!empty($cancel_type) && !empty($lesson_id)){
			// 1hour
			if($time_difference < 0){
				$ct = get_time();
				$current_time = $ct['time_ymdhis'];
				$content =  $reserve_time.' '.$reservation_sentence.$teacher_name;
				
				// 2012-01-08 cancell type
				if($cancel_type == 'M'){
					$content .= ' 일괄예약 수업연장';
				}elseif($cancel_type == 'M2'){
					$content .= " M2**";
				}elseif($cancel_type == 'M3'){
					$content .= " M3**";
				}else{$content .= ' 수업취소';}
				
				
				$point_counts = $this->ModelDefaultHandler->getDefaultPointCount($lesson_id,'Lesson');
				$point_count = $point_counts[0][0]['point_count'];
				
				//
				$lesson_count = $this->Lesson->find('count',array('conditions'=>array('user_id'=>$user_id,'lesson_status'=>'R','reserve_time'=>"$reserve_time")));
				// Already update a quuestionabled lesson_status
				if($lesson_count <= 0){
					$message='<SPAN class=sky_b>^^이미 수강취소 하신 수업이십니다.^^!';
				}else{
					// At first reservation
					if($point_count == 0){
						$sql = " insert into g4_point
						set mb_id = '$mb_id',
						po_datetime = '$current_time',
						po_content = '$content',
						po_point = '$spent_point',
						po_rel_table = 'lessons',
						po_rel_id = $lesson_id,
						po_rel_action = '수업취소' ";
					
						$free_sql_result = $this->Lesson->free_sql($sql);
					
						// ポイントはg4_pointからSUMする
						$select_string = "sum(po_point) AS point ";
						$table = "g4_point ";
						$where_string =  "mb_id = '$mb_id' ";
							
						$point_results = $this->Lesson->select_free_common($select_string,$table,$where_string);
					
						foreach($point_results as $point_result){
							$point = $point_result[0]['point'];
						} // End foreach ($point_results as $point_result);
					 
						// g4_member UPDATEする
						$sql = "update g4_member
						SET mb_point = $point
						WHERE mb_id = '$mb_id'";
						$this->Lesson->free_sql($sql);
					
						// セッションのユーザポイントをUPDATEする
						$loginInfo['User']['points'] = $point;
						$this->Session->write('loginInfo',$loginInfo);
					} // End if($point_count == 0){}
					
					if(in_array($cancel_type,array('M','M2','M3')) && in_array($reservation_kinds,array_merge(array('M','MD','M2','M3'),$OtherCancelReservationKinds))){
						$reserve_only_time=date('H:i:s',strtotime($reserve_time));
						$time_selected=$reserve_only_time;
						if(isset($lesson_model['class_minutes']) && $lesson_model['class_minutes'] > 0){
							$minutes_class=$lesson_model['class_minutes'];
						}else $minutes_class=$this->Programmer1Handler->minutes_class;
						
						
						$last_lesson = $this->Lesson->find('first',array('fields'=>array('Lesson.id','Lesson.payment_id','Lesson.user_id','Lesson.teacher_id','Lesson.reserve_time'
								,'Lesson.reservation_kinds'),'conditions'=>array('Lesson.user_id'=>$user_id,'Lesson.lesson_status'=>array('R')
										,'Lesson.reservation_kinds'=>$reservation_kinds,'TIME(Lesson.reserve_time) = '=>"$reserve_only_time"),'order'=>'Lesson.reserve_time DESC'));
						$extend_only_day = date('Y-m-d',strtotime($last_lesson['Lesson']['reserve_time'] . '+1 day'));
						$search_date = $extend_only_day;
						
						$TimeCondition = $this->Programmer1Handler->TimeCondition($search_date,$time_selected,$minutes_class);
						$s_timed = $time_selected;
						$s_reserve_time_from = $TimeCondition['reserve_time_from'];
						$s_reserve_time_until = $TimeCondition['reserve_time_until'];
						$s_reserve_time25M = $TimeCondition['reserve_time25M'];
						$class_minutes_cond = $TimeCondition['class_minutes_cond'];
						
						$ContractDays = $this->Programmer1Handler->PaymentIdOnContractDays($payment_id,$reservation_kinds,$time_selected);
						
						$insert_lessons=array();
						$search_start_day = date('Y-m-d',strtotime($s_reserve_time_from)) . ' ' . $s_timed;
						$ko_datetime=$search_start_day;
						$currenttime=date('Y-m-d H:i:s');
						$this->Available_lesson->unbindModel(array('belongsTo'=>array('Teacher')));
						
						$check=true;
						while($check == true){
							if(in_array(date('w', strtotime($s_reserve_time_from)),$ContractDays)){
								$user_condition=array(array('Lesson.teacher_id'=>$teacher_id));
								// This is COUNT the lesson exist;
								$emp = $this->Programmer1Handler->findLessonExist($search_date,$s_timed,$s_reserve_time_from,$s_reserve_time_until,$s_reserve_time25M
										,$class_minutes_cond,$teacher_id,$user_id,$user_condition);
								// If $emp totalled more than 0
								// then go and find other day(+1)
								if($emp > 0){
								}else{
									$currenttime=date('Y-m-d H:i:s',strtotime($currenttime . '+3 seconds'));
									$search_start_day = date('Y-m-d',strtotime($s_reserve_time_from)) . ' ' . $s_timed;
									$ko_datetime=$search_start_day;
									$insert_lessons=$this->Programmer1Handler->InsertAvailableLesson(false,$insert_lessons,$s_timed,$s_reserve_time_from,$s_reserve_time_until
											,$s_reserve_time25M,$class_minutes_cond,$ko_datetime,$minutes_class,$teacher_id,$user_id,$payment_id,$reservation_kinds,$currenttime);
									// $this->Available_lesson->deleteAll(array('available_time'=>$reserve_time,'teacher_id'=>$teacher_id),false);
									
									// Insert Lesson Class Multiple
									$total_insert_lessons = count($insert_lessons);
									if($total_insert_lessons > 0){
										$insert_lessons[$total_insert_lessons-=1]['spend_point']=$lesson_model['spend_point'];
										$this->Programmer1Handler->InsertLesson($insert_lessons);
									} // End if($insertToDb == true){}
									
									// WOOOOHHOOOO
									$extend_day=$ko_datetime;
									$new_last_reserve_time=$ko_datetime;
									// payment last_day update
									$payment_update = array();
									if(!empty($payment_id) && strlen ($payment_id) > 0){
										$this->loadModel('Payment');
										$payment_update['id'] = $payment_id;
										$payment_update['last_day'] = date('Y-m-d 00:00:00', strtotime($extend_day));
										$this->Payment->save($payment_update);
									} // End if (!empty($payment_id) && strlen ($payment_id) > 0);
										
									//    extensions table;
									$insert_conditions = array();
									$insert_conditions['user_id'] = $user_id;
									$insert_conditions['lesson_id'] = $lesson_id;
									$insert_conditions['pay_plan'] = $pay_plan;
									$insert_conditions['reservation_kinds'] = $reservation_kinds;
									$insert_conditions['user_nick_name'] = $nick_name;
									$insert_conditions['teacher_name'] = $teacher_name;
									$insert_conditions['reserve_time'] = $reserve_time;
									$insert_conditions['reason'] = "EC";
									$insert_conditions['description'] = $reserve_time . "," . $new_last_reserve_time;
									$insert_conditions['tarket_day'] = $new_last_reserve_time;
									$insert_conditions['extend_day'] = date('Y-m-d H:i:s', strtotime(date('Y-m-d', strtotime($new_last_reserve_time))));
									$insert_conditions['last_day'] = $user_last_day;
									$this->ModelDefaultHandler->insertExtention($insert_conditions,'Lesson');
										
									// Update User last day
									$this->loadModel('User');
									$userInfo = $this->User->find('first',array('fields'=>array('id','last_day'),'conditions'=>array('id'=>$loginInfo['User']['id'])));
									// if last_day is less than extended day
									if(strtotime($extend_day) > strtotime($userInfo['User']['last_day'])){
										$this->User->updateAll(array('last_day'=>'"'.$extend_day.'"','modified'=>'"'.date('Y-m-d H:i:s').'"'),array('id'=>$loginInfo['User']['id']));
									}
									$message = "<SPAN class=sky_b> $reserve_time.'이 연장되어 '. $extend_day. '수업이 추가 되었습니다.'</SPAN>";

									// Mail System
									$this->requestAction('/phpmailers/mail/',array('form'=>array('post'=>array('Lesson'=>array('id'=>$lesson_id),'mail_type'=>'Extend'))));
									
									// get the lesson cancelled that reserve_time has in the korean_holidays
									$getLessonCancelWithKoreanHoliday = array('reserve_time'=>$reserve_time,'payment_id'=>$payment_id,'reservation_kinds'=>$reservation_kinds);
									// $gLCWKHoliday means getLessonCancelWithKoreanHoliday return array;
									$gLCWKHoliday = $this->Programmer1Handler->getLessonCancelWithKoreanHoliday($getLessonCancelWithKoreanHoliday, true);
										
									$message .= $gLCWKHoliday['holidayMessage'];
									
									$check=false;
								} // End if($emp > 0){}else{}
							}
							$s_reserve_time_from = date('Y-m-d H:i:s',strtotime($s_reserve_time_from . '+1 day'));
							$s_reserve_time_until = date('Y-m-d H:i:s',strtotime($s_reserve_time_until . '+1 day'));
							$s_reserve_time25M = date('Y-m-d H:i:s',strtotime($s_reserve_time25M . '+1 day'));
						} // End while($check == true){}
					} // End if(in_array($reservation_kinds,array_merge(array('M','MD','M2','M3'),$OtherCancelReservationKinds))){}
					
					
					if($cancel_type == 'M' && ($reservation_kinds == 'M' || $reservation_kinds == 'MD')){
					}elseif($cancel_type == 'M' && in_array($reservation_kinds,$OtherCancelReservationKinds)){
					} // End if($cancel_type == 'M' && ($reservation_kinds == 'M' || $reservation_kinds == 'MD')){}
					
					if($time_difference < 0){
						$update_common['id'] = $lesson_id2;
						if($cancel_type == 'P'){
							$update_common['lesson_status'] = 'CP';
						}else{
							$update_common['lesson_status'] = 'C';
						} // End if($cancel_type == 'P');
						 
						$update_common['modified'] = $ct['time_ymdhis'];
						$lessons = $this->Lesson->update_common('lessons',$update_common);
						$absent_date=date('Y-m-d',strtotime($reserve_time));
						$conditions_absent=array();
						$conditions_absent['teacher_id']=$teacher_id;
						$conditions_absent['start_date <=']="$absent_date";
						$conditions_absent['end_date >=']="$absent_date";
						$this->loadModel('Absent');
						$checkAbsent=$this->Absent->find('count',array('conditions'=>$conditions_absent));
						if($checkAbsent < 1) {
							// $this->loadModel('Available_lesson');
							 $this->data['Available_lesson']['id'] = $available_lesson_info;
							 $this->data['Available_lesson']['reserve_status'] = 'R';
							 if($reservation_kinds === 'TM'){
							 	$this->data['Available_lesson']['class_minutes'] = 30;
							 }
							 $result = $this->Available_lesson->save($this->data);
							// $this->Available_lesson->updateAll(array('reserve_status'=>"'R'")
							//		,array('available_time'=>$reserve_time,'teacher_id'=>$teacher_id));
						}else{
							$this->Available_lesson->deleteAll(array('id'=>$available_lesson_info),false);
						} // End if($checkAbsent < 1) {}else{};
					}else{
						// update
						$update_common['id'] = $lesson_id;
						$update_common['lesson_status'] = 'A';
						$update_common['modified'] = $ct['time_ymdhis'];
						$lessons = $this->Lesson->update_common('lessons',$update_common);
					
						// update
						// $update_common = null;
						// $update_common['id'] = $available_lesson_id;
						// $update_common['reserve_status'] = 'F';
						// $lessons = $this->Lesson->update_common('available_lessons',$update_common);
						$this->Available_lesson->updateAll(array('reserve_status'=>"'F'",'modified'=>"$currenttime")
								,array('available_time'=>$reserve_time,'teacher_id'=>$teacher_id));
					} // End if($time_difference < 0){}else{};
					
					$this->LanguageHandler->infocancelation($lesson_id2, $cancel_type);
				} // End if($lesson_count <= 0){}else{}
			} // End if($time_difference < 0){}
		}else{
			
		} // End if(!empty($cancel_type) && !empty($lesson_id)){}
		$this->set('message', $message);
	} // End function lesson_cancel_ok_2($lesson_id = null){}
	
function checkStatusHold(){
// 'Recording Counting' as '$rCount' start at 1;
 $rCount = 1;
 $this->set("rCount", $rCount);
 $loginInfo = $this->Session->read('loginInfo');
 $user_id = $loginInfo['User']['id'];
// $checkStatusHOLD = $this->Programmer1Handler->checkStatusHOLD($user_id, "all");
// $this->set('checkStatusHOLD', $checkStatusHOLD);
 $fields = array();
 $today = date("Y-m-d", strtotime($this->HoldStatusRestrictDate));
 $RKNtoF = array('M','MD','M2','M3'); // reservation_kinds Need to Find;
 $this->paginate = array(
  'fields'=>array(
     'Lesson.id','Lesson.reserve_time','Lesson.lesson_status','Lesson.created'
    ,'DictionaryDetail.name, DictionaryDetail.express_name_en, DictionaryDetail.express_name_ko'
   )
  ,'joins'=>array(array(
     'type' => 'LEFT',
     'table'=>'(SELECT dictionary_details.name as name, dictionary_details.express_name_en as express_name_en, dictionary_details.express_name_ko as express_name_ko FROM dictionaries LEFT JOIN dictionary_details ON (dictionaries.id=dictionary_details.dictionary_id) WHERE dictionaries.name = "lesson_status")'
    ,'alias' => 'DictionaryDetail'
    ,'conditions' => array('Lesson.lesson_status = DictionaryDetail.name')
  ))
  ,'conditions'=>array('Lesson.lesson_status'=>'H','Lesson.user_id'=>$user_id,'Lesson.reservation_kinds'=>$RKNtoF,'Lesson.reservation_kinds >='=>"$today")
  ,'order'=>'Lesson.reserve_time ASC'
  ,'limit'=>10
 );
 $data = $this->paginate('Lesson');
 $this->set('data',$data);
 $this->set('paginator',$this->paginate('Lesson'));
}

 function proceedNotice(){
  $loginInfo = $this->Session->read('loginInfo');
  $user_id = $loginInfo['User']['id'];
  $pastTime=date("Y-m-d H:i:s");
  $proceedNotice=$this->Programmer1Handler->proceedNotice($user_id, $pastTime);
  return $proceedNotice;
 } 

	function myPaymentHistory ( $mb_id = null ) {
		$loginInfo = $this -> Session -> read('loginInfo');
		if ( empty($mb_id) || $mb_id != $loginInfo['User']['mb_id'] ) {
			$mb_id = $loginInfo['User']['mb_id'];
		}
		$this -> set( 'getPaymentHistory', $this -> Programmer1Handler -> getPaymentHistory ( $mb_id ) );
		$this -> set( 'loginInfo', $loginInfo );
	}
	
	// 2014-03-18 15:00 levi aviles
	function reserve_complete2() {
		global $const; 
		$this -> set('const', $const);
		
		global $language_reserve_complete; 
		
		$this -> set('language_reserve_complete', $language_reserve_complete);
		
		// need payment_id
//		$payment_id = $this -> params['form']['payment'];
//		&& ($payment_id < 0 || $payment_id == NULL)
		$payment_id = 0;
		if ( isset($this -> params['form']['payment']) && $this -> params['form']['payment'] > 0 ) {
			
			$payment_id = $this -> params['form']['payment'];

			$loginInfo			 = $this -> Session -> read('loginInfo');
			$tacherConfirmSch	 = $this -> Session -> read('tacherConfirmSch');
	
			$this -> set( 'teacher_id', $this -> data['Lesson']['teacher_id'] );
		//	//2010-02-10 YHLEE END
			$messages = ''; 
	
			if( $tacherConfirmSch == null ){
		//		// '예약시간을 설정하지 않으신것 같습니다.
				$messages = $language_reserve_complete[0];
				$this -> set( 'messages', $messages );
				return;
			}
		//	//自由検索文に変更
			$where_string  = 'Available_lesson.teacher_id = '. $this -> data['Lesson']['teacher_id'];
			$where_string .= " AND Available_lesson.reserve_status = 'R'";
			
			$number_of_reserve = sizeof( $tacherConfirmSch );
			$i = 1;
			
		//	//2012-02-05 start
			$student_reserve_time	 = null;
			$user_id				 = $loginInfo['User']['id'];
			$teacher_id				 = $this -> data['Lesson']['teacher_id'];
	
			$reserve_count = array();
		//	//2012-02-05 end
		
			foreach( $tacherConfirmSch as $sch ) {
				
				if( $i == 1 ){
					$where_string .= ' AND (';
				}else{
					$where_string .= ' OR ';
				}
		
		//		echo date ( 'Y-m-d H:i:s', strtotime($sch) );
		//		$student_reserve_time  = sprintf( "%d-%d-%d %d:%d:00", substr($sch,0,4),substr($sch,4,2),substr($sch,6,2),substr($sch,8,2),substr($sch,10,2));
				$student_reserve_time  = date ( 'Y-m-d H:i:s', strtotime($sch) );
		//		$where_string		  .= " Available_lesson.available_time = '". sprintf("%d-%d-%d %d:%d:00", substr($sch,0,4), substr($sch,4,2), substr($sch,6,2), substr($sch,8,2), substr($sch,10,2))."'";
				$where_string		  .= " Available_lesson.available_time = '".$student_reserve_time."'";
	
				if( $i == $number_of_reserve ){
					$where_string .= ")";
				}
				$i++;
			}
	
			$reservation_info = $this -> Lesson -> reservation_info( $where_string );
			
			if ( sizeof($tacherConfirmSch) != sizeof($reservation_info) ){
		//		//'죄송합니다. 다른 회원님에 의해 예약이 되었습니다.'
				$messages = $language_reserve_complete[1];
				$this -> set('messages', $messages);
			}else{
		//		//DBをUPDATEする前に十分なポイントを持っているか確認する
		//		//セッションからユーザポイントを求める
		//		//ユーザーが持っているポイントがレッスン予約合計より小さい場合DBUPDATEしないずにメッセージセットする
		//		//ユーザポイントをもう一度求める
		//		$loginInfo = $this -> Session -> read('loginInfo');
				$mb_id = $loginInfo['User']['mb_id'];
	
		//		//load teachers name
		//		//$teachers_name = $this->ModelHandler->getTeacherNames();
	
		//		//No Error In case of normal
		//		//get reservation_kinds variable from confirmation page
				$reservation_kinds = $this -> params['form']['reservation_kinds'];
	
				if( $reservation_kinds == $const['point_reservation'] ){
		//			//get user point from g4member table
					$user_point = $this -> ModelHandler -> getMbPoint($mb_id,'Lesson');
					$loginInfo['User']['points'] = $user_point;
	
		//			//decide errors of lack of points
					$spend_points = $this -> data['Lesson']['spend_point'] * $number_of_reserve;
					if( $loginInfo['User']['points'] < $spend_points ){
		//				//エラーメッセージセット
		//				//'포인트가 부족한 것 같습니다. 포인트 구입후 다시 예약해 주시기 바랍니다.'
						$messages = $language_reserve_complete[2];
						$this -> set( 'messages', $messages );
						return;
					}
				}
		//		//process common regardless point reservation or koma reservation
		//		
		//		//lesson data input
		//		
		//		//ループを回りなから　Insert　Updateする
		//		$ct = get_time();	//現在の時間を求める
		//		$current_time = $ct['time_ymdhis'];
				$current_time = date ( 'Y-m-d H:i:s' );
					
		//		//ポイントテーブルにＩＮＳＥＲＴ
				$i = 0;
				$comment_id = null;
				
				foreach( $reservation_info as $keyRInfo => $r_info ){
					
					//transanction start
					//$this->Lesson->begin();
	
		//			//画面でコメントが入力しているか判断する
					if( $this -> params['form']['comment'.$i] != null && $this -> params['form']['comment'.$i] != '' ){
		//				//commentテーブルのＭＡＸ値を求める
						$max_id = $this -> ModelHandler -> getCommentMaxId();
		//					
		//				//$this->data['Lesson']['comment_id'] = $max_id;
						if( $i == 0 ){
							$comment_id = $max_id;
						} else {
							$comment_id++;
						}
					}
	
		//			//lessonテーブルにインサーとする
					$pay_plan = $loginInfo['User']['pay_plan'];
					
					if( $pay_plan == 'M' && $reservation_kinds == $const['point_reservation'] ){
						$reservation_kinds_data = 'T';
					} else if( $reservation_kinds == $const['point_reservation'] ){
						$reservation_kinds_data = 'P';
					}else if( $reservation_kinds == $const['koma_reservation'] ){
						$reservation_kinds_data = 'K';
					}
					
					$this -> loadModel( 'Dictionary' );
					$this -> loadModel( 'DictionaryDetail' );
					$this -> loadModel( 'Payment' );
					
		//			$this->Lesson->bindModel(array('belongsTo' => array('Teacher'=>array('className' => 'Teacher','foreignKey' => 'teacher_id'))));
					$this -> DictionaryDetail -> bindModel( array( 'belongsTo' => array( 
								'Dictionary' => array( 'className' => 'Dictionary', 'foreignKey' => 'dictionary_id' ),
								'Payment' => array( 'className' => 'Payment', 'foreignKey' => false, 'conditions' => array( 'Payment.goods_name = DictionaryDetail.name' ) ) ) ) );
					
					$getReservationKinds	 = $this -> DictionaryDetail -> find( 'first', array( 'fields' => array( 'DictionaryDetail.express_name_en' ), 'conditions' => array( 'Payment.id' => $payment_id, 'Dictionary.name' => 'payment_kinds' ) ) );
					
					if ( count($getReservationKinds) > 0 && strlen($getReservationKinds['DictionaryDetail']['express_name_en']) > 0 ) {
						$reservation_kinds_data	 = $getReservationKinds['DictionaryDetail']['express_name_en'];
						// save Payment id
					}
					
					$this -> data['Lesson']['payment_id'] = $payment_id;
	
		//			pr($getReservationKinds);
	
					$this -> data['Lesson']['user_id']				 = $loginInfo['User']['id'];
					$this -> data['Lesson']['teacher_id']			 = $this -> data['Lesson']['teacher_id'];
					$this -> data['Lesson']['available_lesson_id']	 = $r_info['Available_lesson']['id'];
					$this -> data['Lesson']['lesson_status']		 = 'R';
					$this -> data['Lesson']['point_id']				 ='';
					
		//			//コメント入力したかによって
					$this -> data['Lesson']['comment_id']			 = $comment_id;
					$this -> data['Lesson']['spend_point']			 = $this -> data['Lesson']['spend_point'];
					$this -> data['Lesson']['reserve_time']			 = $r_info['Available_lesson']['available_time'];
					
		//			//textCodeを何を入れてるかによって
					$this -> data['Lesson']['text_code']			 = $this -> data['Lesson']['text_code'.$i];
					$this -> data['Lesson']['reservation_kinds']	 = $reservation_kinds_data;
					
					$this -> data['Lesson']['lesson_file']			 = null;
					$result = $this -> Lesson -> save($this->data);
					
					$count_payment_id_used = $this -> Programmer1Handler -> count_payment_id_used($payment_id, 'complete');
	
		//			//Commentテーブルにコメントを入力する
		//			//2010-02-04 YHLEE UPDATE START
		//			//change relationship between lesson table and comment table is one by one
		//			//even if uer do not comment data , insert comment table with ''
		//				
		//			//2010-04-21 start
		//			//$lesson_max_id = $this->ModelHandler->getLessonMaxId('Lesson');
		//				
					$lesson_max_id = $this -> Lesson -> getLastInsertID();
					
					// Mail System
					$this->requestAction('/phpmailers/mail/',array('form'=>array('post'=>array('Lesson'=>array('id'=>$lesson_max_id)),'mail_type'=>'Reservation')));
		//				
		//			//pr($lesson_max_id);
		//								
					if($this -> params['form']['comment'.$i] == null){
						$this -> params['form']['comment'.$i]='';
					}
		//					
					$this -> ModelHandler -> insertComment( $loginInfo['User']['id'],
															$this -> data['Lesson']['teacher_id'],
															0,mysql_real_escape_string( trim($this -> params['form']['comment'.$i]) )
															,$lesson_max_id);
		
		//			//update availableLesson table(teacher's reservation table)
					$availableLessonData['id']				 = $r_info['Available_lesson']['id'];
					$availableLessonData['reserve_status']	 = $this -> ModelHandler -> L_Finish;
					$this -> ModelHandler -> updateRserveStatusOnAvailableLesson( $availableLessonData,'Lesson' );
	
						
		//			//ポイントテーブルを件数別インサートする
		//			//仕様変更g4_pointテーブルにインサートする
		//			//料金プランによってポイントテーブルに設定する内容が違う
		//			//2010-02-04 YHLEE UPDATE START 
					if($reservation_kinds == $const['point_reservation']){  
						$spent_point =- $this -> data['Lesson']['spend_point'];
	
						$content = $this -> data['Lesson']['reserve_time']." ".$language_reserve_complete[3];
						$sql = " insert into g4_point 
			                set mb_id = '$mb_id',
			                    po_datetime = '$current_time',
			                    po_content = '$content',
			                    po_point = '$spent_point',
			                    po_rel_table = 'lessons',
			                    po_rel_id = '',
			                    po_rel_action = '$language_reserve_complete[3]' ";
	
						$free_sql_result = $this->Lesson->free_sql($sql);
		
					}else if($reservation_kinds == $const['koma_reservation']){
		//				//今日予約した回数を一つ増やす
						$loginInfo['User']['todayReservation'] = $loginInfo['User']['todayReservation']++;
					}	
		//		
					$i++;
				}
		//		//end of loop
		//			
		//		//ポイントはg4_pointからSUMする
		//		//only in case of point reservation
				if($reservation_kinds == $const['point_reservation']){  
					$select_string	 = "sum(po_point) AS point ";
					$table			 = "g4_point ";
					$where_string	 = "mb_id = '$mb_id' ";
						
					$point_results = $this -> Lesson -> select_free_common( $select_string, $table, $where_string );
					foreach( $point_results as $point_result ){
						$point = $point_result[0]['point'];
					}
					
		//		    //g4_member UPDATEする
					$sql = 	"update g4_member
							SET mb_point = $point 
							WHERE mb_id = '$mb_id'";
							
					$this -> Lesson -> free_sql($sql);
		
		//			//セッションのユーザポイントをUPDATEする
					$loginInfo['User']['points'] = $point;
				}
	
		//		//$this->Lesson->rollback();
	
		//		//2012-02-05 START It can be deleted
				$this -> Lesson -> bindModel( array( 'belongsTo' => array('Teacher' => array('className' => 'Teacher','foreignKey' => 'teacher_id')) ) );
				$reserve_count = $this -> Lesson -> find('all',
						array( 'conditions' => array('Lesson.user_id' => $user_id
							,'Lesson.lesson_status' => 'R'
							,'Lesson.reserve_time'  =>$student_reserve_time)
				)); 
		//			
		//		//if reservation is sucessivly reserved , reserve count sould be 1 
		//		//pr($reserve_count);
		//		//pr(sizeof($reserve_count));
				if(sizeof($reserve_count) > 0){
				}else{
					$messages .= "예약이 현재 이루어지지 않았습니다. 현재 강사님의 예약 화면에서 다시 예약 부탁드립니다.";
				}
		//		//pr($reserve_count);
		//		//2012-02-05 END
				$this -> set( 'reserve_count', $reserve_count );	 
				$this -> Session -> write( 'loginInfo', $loginInfo );
				$this -> set( 'messages', $messages );	
			}
	
		//	//クリックしたセッションを消す
		//	//戻った時に青いまま残っているから
			$this -> Session -> write( 'tacherConfirmSch', null );
	
		//	//TEACHER＿IDを設定(次の予約ために同じ先生のページに飛ぶように)
			$this -> set( 'teacher_id', $this -> data['Lesson']['teacher_id'] );
		} else {
			$cubic_error_condition = array();
			$loginInfo = $this -> Session -> read('loginInfo');
			$today = date('Y-m-d H:i:s');
			$indentify_user = ""; 
			if ( empty($loginInfo) || $loginInfo == null ) {
				$indentify_user = "unidentified user;  <br >lesson_id : cannot identified;  <br >teacher_id : cannot identified;  <br >session has been expired;";
			} else {
				$indentify_user = "identified user; <br >lesson_id : cannot identified;  <br > teacher_id :cannot identified;";
				$cubic_error_condition['user_id'] = $loginInfo['User']['id'];
			}
			
			$cubic_error_condition['error']		 = "001";
			$cubic_error_condition['message']	 = "this -> params[form][payment] : return 0; <br > $indentify_user";
			$cubic_error_condition['controller'] = "lessons_controller";
			$cubic_error_condition['method']	 = "function reserve_complete2(){};";
			$cubic_error_condition['created']	 = $today;
			$cubic_error_condition['modified']	 = $today;
			
			$this -> loadModel('CubicError');
			$this -> CubicError -> create();
			$this -> CubicError -> set($cubic_error_condition);
			$this -> CubicError -> save();
			
			$messages = "수업 예약중 에러가 발생하였습니다.<br />불편을 드려서 죄송하오며 고객센터에 문의를 부탁드립니다.<br />070-7847-5487";
			$this -> set('messages', $messages);
				
		}
		$this -> set('payment_id', $payment_id);
	}

	function weekdays_reqr ($state = null, $reservation_kinds = null) {
		$weekdays_reqr = array();
		$this->loadModel("Dictionary");
		$this->loadModel("DictionaryDetail");
		$this->DictionaryDetail->bindModel( array('belongsTo' => array('Dictionary' => array('className' => 'Dictionary', 'foreignKey' => 'dictionary_id'))) );
		$weekdays = $this->DictionaryDetail->find("all", array("fields" => array("Dictionary.id", "DictionaryDetail.name"), "conditions" => array("Dictionary.name" => $reservation_kinds) ) );
		foreach ($weekdays as $keyWeekdays => $a) {
			if ( $state == "name") {
				$weekdays_reqr[] = date("D", strtotime($a["DictionaryDetail"]["name"]));
			}
			if ( $state == "number") {
				$weekdays_reqr[] = date("N", strtotime($a["DictionaryDetail"]["name"]));
			}
		}
		return $weekdays_reqr;
	}
	
function lesson_cancel_diff ($lesson_id = null) {
 global $const; 
 $this->set('const', $const);
 $loginInfo = $this->Session->read('loginInfo');

 $pay_plan = $loginInfo['User']['pay_plan'];
 $user_id = $loginInfo['User']['id'];
 $mb_id	 = $loginInfo['User']['mb_id'];
 $nick_name = $loginInfo['User']['nick_name'];
 $user_last_day = $loginInfo['User']['last_day'];

 $types = $loginInfo['User']['types'];
 $message = "";

 $lesson_id = $this->params['form']['lesson_id'];
 $cancel_type = $this->params['form']['type'];

 $spent_point = 0;
 $point = 0;
 $reservation_sentence = "";
 $insertdb = false;
 $content = "";
 
 if (!empty($cancel_type) && !empty($lesson_id)) {
  $fields = array("Lesson.id", "Lesson.available_lesson_id", "Lesson.payment_id", "Lesson.user_id", "Lesson.teacher_id", "Lesson.reserve_time", "Lesson.reservation_kinds", "Lesson.spend_point");
  $lesson = $this->Lesson->find("all", array("fields" => $fields, "conditions" => array("Lesson.id" => $lesson_id, "Lesson.lesson_status" => 'C')));
  if (count($lesson) > 0) {
   $message = "Sorry! Cancelation process already finish.";
  } else {
   $fields = array("Lesson.id", "Lesson.available_lesson_id", "Lesson.payment_id", "Lesson.user_id", "Lesson.teacher_id", "Lesson.reserve_time", "Lesson.reservation_kinds", "Lesson.spend_point");
   $lesson = $this->Lesson->find("first", array("fields" =>$fields, "conditions" => array("Lesson.id" => $lesson_id)));
   if (count($lesson) > 0) {
   	$lesson_id2 = $lesson["Lesson"]["id"];
    $available_lesson_id = $lesson["Lesson"]["available_lesson_id"];
    $reserve_time = $lesson["Lesson"]["reserve_time"];
    $reservation_kinds = $lesson["Lesson"]["reservation_kinds"];
    $teacher_id = $lesson["Lesson"]["teacher_id"];
	$payment_id = $lesson["Lesson"]["payment_id"];
	$spend_point = $lesson["Lesson"]["spend_point"];
	
	$this->loadModel("Teacher");
    $teacher = $this->Teacher->findById($teacher_id);
    $teacher_name = $teacher['Teacher']['tea_name'];
					
    $weekdays_arr = $this->weekdays_reqr("number", $reservation_kinds);
    $dateTime = date("Y-m-d");
//    1hour before
    $time_difference = (time()+ 60*60*1) - (strtotime($reserve_time));
    
//    24hour before 2011-01-16 start
    $time_difference24 = (time()+ 60*60*24) -(strtotime($reserve_time));
//    24hour before 2011-01-28 start | Point plan 12hours
    $time_difference12 = (time()+ 60*60*12) -(strtotime($reserve_time));
//    Point plan change from 06hours -> lesson_time
    $time_difference06 = (time()+ 60*60*6) -(strtotime($reserve_time));
    
    if ($reservation_kinds == "M2") {
     $spent_point = 0;
     $reservation_sentence = "M2 RESERVATION KINDS MESSAGE";
    } elseif ($reservation_kinds == "M3") {
     $spent_point = 0;
     $reservation_sentence = "M3 RESERVATION KINDS MESSAGE";
    } // End if ($reservation_kinds == "M2");
    
    
    if ($time_difference < 0) {
     $ct = get_time();
     $current_time = $ct['time_ymdhis'];
     if ($cancel_type == 'M2') {
      $content .= " M2**";
	 } else if ($cancel_type == 'M3') {
      $content .= " M3**";
     } else {
       $content .= " 수업취소";
     } // End if ($cancel_type == 'M2');
     
     $search_conditions['mb_id'] = $mb_id;
     $search_conditions['po_datetime'] = $current_time;
					
//     point count
     $point_counts = $this->ModelDefaultHandler->getDefaultPointCount($lesson_id2,'Lesson');
     $point_count = $point_counts[0][0]['point_count'];
//     check vaild lesson
     $search_conditions['user_id'] = $user_id;
     $search_conditions['reserve_time'] = $reserve_time;
     $search_conditions['lesson_status'] = 'R';
//     check if it is valid lesson or not
     $lesson_counts = $this->ModelDefaultHandler->getDefaultLessonCount($search_conditions,'Lesson');
     $lesson_count = $lesson_counts[0][0]['lesson_count'];
     
     if ($lesson_count <= 0) {
//      already cancelled class do nothing
      $message='<SPAN class=sky_b>^^이미 수강취소 하신 수업이십니다.^^!';
     } else {
      if ($point_count == 0) {
       $sql = " insert into g4_point
                set mb_id = '$mb_id',
                po_datetime = '$current_time',
                po_content = '$content',
                po_point = '$spent_point',
                po_rel_table = 'lessons',
                po_rel_id = $lesson_id2,
                po_rel_action = '수업취소' ";
       $free_sql_result = $this->Lesson->free_sql($sql);
					
       $select_string = "sum(po_point) AS point ";
       $table = "g4_point ";
       $where_string = "mb_id = '$mb_id' ";
       $point_results = array();
       $point_results = $this->Lesson->select_free_common($select_string,$table,$where_string);
       
       foreach ($point_results as $point_result) {
        $point = $point_result[0]['point'];
       } // End foreach ($point_results as $point_result);
								 
//       g4_member UPDATEする
       $sql = "update g4_member
               SET mb_point = $point
               WHERE mb_id = '$mb_id'";
       $this->Lesson->free_sql($sql);
					
       $loginInfo['User']['points'] = $point;
       $this->Session->write('loginInfo',$loginInfo);
      } // End if ($point_count == 0);
      
      if (($cancel_type == 'M2' && $reservation_kinds == 'M2') || ($cancel_type == 'M3' && $reservation_kinds == 'M3')) {
       $last_lesson = $this->Lesson->find( "first", array( "fields" => array("Lesson.id", "Lesson.payment_id", "Lesson.user_id", "Lesson.teacher_id", "Lesson.reserve_time", "Lesson.reservation_kinds"), "conditions" => array("Lesson.user_id" => $user_id , "Lesson.lesson_status" => array("R"), "Lesson.reservation_kinds" => $reservation_kinds), "order" => "Lesson.reserve_time DESC" ) );
       $org_last_reserve_date = $last_lesson["Lesson"]["reserve_time"];
       $org_last_reserve_time = date("H:i:s", strtotime($reserve_time));
       $new_last_reserve_time = $last_lesson["Lesson"]["reserve_time"];
       // Based Korean Time
       // $org_last_reserve_time may 00:00:00 > 05:00:00
       // the $weekdays_arr will change include with Saturday and Remove Monday
       $hours = date('G',strtotime($org_last_reserve_time));
       $hours = (24 + $hours);
       if($hours >= 24 && $hours < 29){ // 24:00 (00:00 AM) - 29:00 (05:00 AM)
       	// Tue, Wed, Thur, Fri, Sat
       	// $weekdays_arr=array('2','3','4','5','6');
       	$new_weekdays_arr=array();
       	foreach($weekdays_arr as $keyweekday_arr => $kwda){
       		$new_weekdays_arr[]=$kwda+=1;
       	}
       	$weekdays_arr = $new_weekdays_arr;
       }
       
       $this->loadModel('Available_lesson');
       while ($insertdb == false) {
       	$new_last_reserve_time = date("Y-m-d", strtotime($new_last_reserve_time . "+ 1 days"));
        $dateTime = date("N", strtotime($new_last_reserve_time));
        $new_last_reserve_time = date("Y-m-d H:i:s", strtotime($new_last_reserve_time." ".$org_last_reserve_time));
        if (in_array($dateTime, $weekdays_arr)) {
         $emp = $this->Lesson->find('count', array('fields' => array('COUNT(Lesson.id) as count'),'conditions' => array('Lesson.reserve_time' => $new_last_reserve_time, 'Lesson.lesson_status' => 'R','Lesson.teacher_id' => $teacher_id)));
         if ($emp == 1) {
          continue;
		 } else {
		  $this->Available_lesson->create();
		  $updateA = $this->Available_lesson->find('count', array('fields' => array('COUNT(reserve_status) as count'),'conditions' => array('available_time' => $new_last_reserve_time, 'reserve_status' => 'R', 'teacher_id' => $teacher_id)));
		  if ($updateA > 0) {
		   $this->Available_lesson->updateAll(array('reserve_status' => "'F'"), array('available_time' => $new_last_reserve_time, 'reserve_status' => 'R', 'teacher_id' => $teacher_id));
	      } else {
		   $this->Available_lesson->set(array('teacher_id' => $teacher_id, 'available_time' => $new_last_reserve_time, 'reserve_status' => 'F'));
		   $this->Available_lesson->save($this->data);
	      } // End if ($updateA > 0);
          
          $alId = $this->Available_lesson->find('first',array('fields' => 'Available_lesson.id', 'conditions' => array('Available_lesson.available_time' => $new_last_reserve_time, 'Available_lesson.reserve_status' => 'F', 'Available_lesson.teacher_id' => $teacher_id)));
		  if (empty($alId)) {
	       $alId['Available_lesson']['id'] = 0;
		  } // End if (empty($alId));
											
	      $this->Lesson->create();
		  $this->Lesson->set(array('teacher_id' => $teacher_id, 'user_id' => $user_id, 'available_lesson_id' => $alId['Available_lesson']['id'], 'lesson_status' => 'R', 'reserve_time' => $new_last_reserve_time, 'reservation_kinds' => $reservation_kinds, 'payment_id'=>$payment_id));
		  $this->Lesson->save($this->data);
//	      update payment last_day
		  $payment_update = array();
		  if (!empty($payment_id) && strlen ($payment_id) > 0) {
		   $this->loadModel('Payment');
		   $payment_update['id'] = $payment_id;
		   $payment_update['last_day'] = date('Y-m-d 00:00:00', strtotime($new_last_reserve_time));
		   $this->Payment->save($payment_update);
		  }
		  
//		  extensions table;
          $insert_conditions = array();
          $insert_conditions['user_id'] = $user_id;
          $insert_conditions['lesson_id'] = $lesson_id2;
          $insert_conditions['pay_plan'] = $pay_plan;
          $insert_conditions['reservation_kinds'] = $reservation_kinds;
          $insert_conditions['user_nick_name'] = $nick_name;
          $insert_conditions['teacher_name'] = $teacher_name;
          $insert_conditions['reserve_time'] = $reserve_time;
		  $insert_conditions['reason'] = "EC";
		  $insert_conditions['description'] = $reserve_time . "," . $new_last_reserve_time;
		  $insert_conditions['tarket_day'] = $new_last_reserve_time;
		  $insert_conditions['extend_day'] = date('Y-m-d H:i:s', strtotime(date('Y-m-d', strtotime($new_last_reserve_time))));
		  $insert_conditions['last_day'] = $user_last_day;
		  $this->ModelDefaultHandler->insertExtention($insert_conditions,'Lesson');

		  if ($this->Lesson->save($this->data)){
		  }else{}
		  // Update User last day
		  $this->loadModel('User');
		  $userInfo = $this->User->find('first',array('fields'=>array('id','last_day'),'conditions'=>array('id'=>$loginInfo['User']['id'])));
		  // if last_day is less than extended day
		  if(strtotime($new_last_reserve_time) > strtotime($userInfo['User']['last_day'])){
		  	$this->User->updateAll(array('last_day'=>'"'.$new_last_reserve_time.'"','modified'=>'"'.date('Y-m-d H:i:s').'"'),array('id'=>$loginInfo['User']['id']));
		  }
		  $insertdb = true;
		  $message = "<SPAN class=sky_b> $reserve_time.'이 연장되어 '. $new_last_reserve_time. '수업이 추가 되었습니다.'</SPAN>";
		  
		// Mail System
		$this->requestAction('/phpmailers/mail/',array('form'=>array('post'=>array('Lesson'=>array('id'=>$lesson_id2)),'mail_type'=>'Extend')));
		  
 // get the lesson cancelled that reserve_time has in the korean_holidays
 $getLessonCancelWithKoreanHoliday = array('reserve_time'=>$reserve_time, 'payment_id'=>$payment_id, 'reservation_kinds'=>$reservation_kinds);
 // $gLCWKHoliday means getLessonCancelWithKoreanHoliday return array;
 $gLCWKHoliday = $this->Programmer1Handler->getLessonCancelWithKoreanHoliday($getLessonCancelWithKoreanHoliday, true);
 
 $message .= $gLCWKHoliday['holidayMessage'];
 
         } // End if ($emp == 1);
        } else {
         continue;
        } // End if (in_array($dateTime, $weekdays_arr));
       } // End while ($insertdb == false);
      } // End if (($cancel_type == 'M2' && $reservation_kinds == 'M2') || ($cancel_type == 'M3' && $reservation_kinds == 'M3'));
      
      if ($time_difference < 0) {
       $update_common['id'] = $lesson_id2;
       if ($cancel_type == 'P') {
        $update_common['lesson_status'] = 'CP';
       } else {
	    $update_common['lesson_status'] = 'C';
       } // End if ($cancel_type == 'P');
       
       $update_common['modified'] = $ct['time_ymdhis'];
       $lessons = $this->Lesson->update_common('lessons',$update_common);
       $this->loadModel('Available_lesson');
	   if($this->Programmer1Handler->checkAbsent($teacher_id, $reserve_time) < 1){
		$this->data['Available_lesson']['id'] = $available_lesson_id;
	    $this->data['Available_lesson']['reserve_status'] = 'R';
	    $result = $this->Available_lesson->save($this->data);
	   }else{
		$this->Available_lesson->deleteAll(array('id'=>$available_lesson_id),false);
	   } // End if($this->Programmer1Handler->checkAbsent($teacher_id, $reserve_time) < 1){}else{}
	  } else {
//       update
       $update_common['id'] = $lesson_id2;
       $update_common['lesson_status'] = 'A';
       $update_common['modified'] = $ct['time_ymdhis'];
       $lessons = $this->Lesson->update_common('lessons',$update_common);
				
//       update 
       $update_common = null;
       $update_common['id'] = $available_lesson_id;
       $update_common['reserve_status'] = 'F';
       $lessons = $this->Lesson->update_common('available_lessons',$update_common);
      } // End if ($time_difference < 0);
								
      $this->LanguageHandler->infocancelation($lesson_id2, $cancel_type);
     } // End if ($lesson_count <= 0);
    } else {
    } // End if ($time_difference < 0);
    
   } else {
   } // End if (count($lesson) > 0);
   
  } // End if (count($lesson) > 0);
 } else {
 } // End if (!empty($cancel_type) && !empty($lesson_id));
 
 $this->set('message', $message);
} // End function lesson_cancel_diff ($lesson_id = null);

	function recordings(){ }
	function vc_manual(){
		$default=1; $vcmanual=array(1=>"video_class_manual", 2=>"check_sound_hardware");
		if (isset($this->params["form"]["manual"])) 
			$default=$this->params["form"]["manual"];
		$this->set("vcmanual",array("default"=>$default,"manual_type"=>$vcmanual[$default]));
	}
	
	function rfolders(){
			$loginInfo = $this->Session->read('loginInfo');
			$user_id = $loginInfo["User"]["id"];
			$getAllTeacher = $this->Lesson->query("SELECT Teacher.id, Teacher.tea_name FROM lessons as Lesson JOIN teachers as Teacher ON (Teacher.id = Lesson.teacher_id) WHERE Lesson.user_id = $user_id GROUP BY Teacher.id ORDER BY Teacher.tea_name ASC");
			if (count($getAllTeacher) > 0) {
				foreach ($getAllTeacher as $teacher) {
					$stud_tea_name[] = $teacher["Teacher"]["tea_name"];
					$stud_tea_id[$teacher["Teacher"]["tea_name"]] = $teacher["Teacher"]["id"];
				}
			}
			$page=1;
			if(isset($this->params["form"]["page"]))
				$page=$this->params["form"]["page"];
			$this->set("page_select",$page);
			$this->set("getAllTeaId",$stud_tea_id);
			$this->set("getAllTeaName",$stud_tea_name);
			$this->set("loginInfo",$loginInfo);
	}
		
	function rfiles($id=null){
			$loginInfo = $this->Session->read('loginInfo');
			$user_id = $loginInfo["User"]["id"];
			$getAllTeacher = $this->Lesson->query("SELECT Teacher.id, Teacher.tea_name FROM teachers as Teacher WHERE Teacher.id = $id");
			
			$this->set("getAllTeaName",$getAllTeacher);
			
			$this->set("loginInfo",$loginInfo); }
			
function index(){
		$this->set('lessons', $this->User->find('all'));
	}


function add()
    {
        if (!empty($this->data))
        {
            if ($this->User->save($this->data))
            {
                $this->flash('Your lessons has been saved.','/lessons');
                $this->redirect(array('action' => 'index'));
            }
        }
    }
    

function edit($id = null)
{
    if (empty($this->data))
    {
        $this->User->id = $id;
        $this->data = $this->User->read();
    }
    else
    {
        if ($this->User->save($this->data['Lessons']))
        {
            $this->flash('投稿を更新しました。','/lessons');
        }
    }
}

//2009-10-07 CUBICTALKとCUBICBOARD連動 
/************************************************************************************************************************************************/
function mypage($id = null, $mb_id = null)
{
	global $const; 
	$this->set('const', $const);
	$loginInfo = $this->Session->read('loginInfo');
	if($mb_id == null && $loginInfo == null){
		$this->redirect(b_path()."/bbs/mypage.php");
	}
	
	$this->getPaymentAndLesson();
	
	# dave
	$language = $this->LanguageHandler->set_lang();
	$this->set('language',$language);

	#$this->LanguageHandler->change_lang();

// 	// Get All 'Fix' reservation_kinds
// 	$this->loadModel('Dictionary');
// 	$dictionary = $this->Dictionary->find('first',array('fields'=>array('*'),'conditions'=>array('name'=>'fixed_payment_kinds')));
// 	$RenewGetAllReservationKinds = array('M','MD','M3','M2','LANDLINE10','MOBILE10','LANDLINE20','MOBILE20','TTEM3','TMEM3','TTEM2','TMEM2');
// 	if(isset($dictionary['Dictionary']['id'])){
// 		$this->loadModel('DictionaryDetail');
// 		$dictionary = $this->DictionaryDetail->find('all',array('fields'=>array('*'),'conditions'=>array('dictionary_id'=>$dictionary['Dictionary']['id'])));
// 		foreach ($dictionary as $key_dictionary => $kd) {
// 			$RenewGetAllReservationKinds[] = $kd['DictionaryDetail']['express_name_en'];
// 		}
// 	}
// 	$this->set('RenewGetAllReservationKinds',$RenewGetAllReservationKinds);
// 	$GetAndCountPaymentToLesson = $this->Programmer1Handler->GetAndCountPaymentToLesson();
// 	$this->set('GetAndCountPaymentToLesson',$GetAndCountPaymentToLesson);

	$user_id = $loginInfo['User']['id'];
	
	// <!-- Begin Getting Current Book
	$getMyCurrentBook = $this->getMyCurrentBook($user_id);
	$this->set('getMyCurrentBook',$getMyCurrentBook);
	// --> End Getting Current Book
	
	$this->set('checkStatusHOLD', $this->Programmer1Handler->checkStatusHOLD($user_id, "count",'',$this->HoldStatusRestrictDate));
	$this->set('leveltest', $this->Programmer1Handler->userlevels($user_id));
	
	//現在時間を求める
	$current_time = get_time();
	$today = $current_time['time_ymd'];
	
	//foreach文エラーをならないように
	$fomattedDays = array();
	
	 // Using Cakephp Paging;
	 $today=date('Y-m-d');
	 $fields=array('Lesson.*','Teacher.*','Payment.*');
	 $conditions=array();
	 $conditions['Lesson.user_id']=$user_id;
	 $conditions['Lesson.reserve_time >= ']="$today";
	 $lesson_status=array('R','F','A','E','EM','P');
	 $conditions['Lesson.lesson_status']=$lesson_status;
	 $this->paginate=array(
	 		'fields'=>$fields
	 		,'joins'=>array(
	 				array('table'=>'teachers','alias'=>'Teacher','type'=>'LEFT','conditions'=>array('Lesson.teacher_id=Teacher.id'))
	 				,array('table'=>'payments','alias'=>'Payment','type'=>'LEFT','conditions'=>array('Lesson.payment_id=Payment.id'))
	 		)
	 		,'conditions'=>$conditions
	 		,'order'=>array('Lesson.reserve_time ASC')
	 		,'limit'=>20
	 		,'passit'=>$this->passedArgs
	 );
	 $lessons=$this->paginate('Lesson');

	//2012-11-04 PAGEING END

	foreach($lessons as $lesson){
		$dbDay =$lesson['Lesson']['reserve_time'];
		$fomattedDays[] = change_date_format($dbDay , 'OVERDAY');
	}
	
	//pr($fomattedDays);
	
	// Level Test Start 2010-10-10
	$level_status = $loginInfo['User']['level_status'];
	
	if($level_status == -1){
		$level_status_string = '미 레벨테스트';
	}else if($level_status == 0 ){
		$level_status_string = '레벨테스트 진행중';
	}else{
		$level_status_string = '레벨테스트 완료';
	}
	// Level Test END 2010-10-10
	
	//Pay Plan Start 2010-10-18
	$pay_plan = $loginInfo['User']['pay_plan'];
	$c_date = $loginInfo['User']['c_date'];
	$c_number = $loginInfo['User']['c_number'];
	$c_bonus = $loginInfo['User']['c_bonus'];
	
	//set a pay plan
	$pay_plan_info = array();
	
	$pay_plan_info['count'] = 0;
	
	//E2case is mutiply times higher 
	if($pay_plan == 'E1'){
		$pay_plan_info['total_count'] = ($c_number * 1 ) + $c_bonus;
	}else if($pay_plan == 'E2'){
		$pay_plan_info['total_count'] = ($c_number * 2 ) + $c_bonus;
	}else if($pay_plan == 'E3'){
		$pay_plan_info['total_count'] = ($c_number * 3 ) + $c_bonus;
	}else if($pay_plan == 'E4'){
		$pay_plan_info['total_count'] = ($c_number * 4 ) + $c_bonus;
	}else{
		$pay_plan_info['total_count'] = $c_number + $c_bonus;
	}
	
	$pay_plan_info['c_number'] = $c_number;
	
	$types = $loginInfo['User']['types'];
	
	$types_string = '횟수';
	
	if( $types == 'F' ){
		$types_string = '고정(한달 20회)';
	}
	
	
	if($pay_plan == 'E1'){
		if($c_number == 0){
			$pay_plan_info['name'] = '매일 25분 플랜';
		}else{
			$pay_plan_info['name'] = $types_string. $c_number . '회 25분 플랜';
		}
		
	}else if($pay_plan == 'E2'){
		if($c_number == 0){
			$pay_plan_info['name'] = '매일 50분 플랜';
		}else{
			$pay_plan_info['name'] = $types_string. $c_number . '회 50분 플랜';
		}
	}else if($pay_plan == 'E3'){
		$pay_plan_info['name'] = '매일 75분 플랜';
	}else if($pay_plan == 'E4'){
		$pay_plan_info['name'] = '매일 100분 플랜';
	}else if($pay_plan == 'S5'){
		$pay_plan_info['name'] = '일괄예약플랜';
	}else if($pay_plan == 'P'){
		$pay_plan_info['name'] = '포인트플랜';	
	}else if($pay_plan == 'M'){
		$pay_plan_info['name'] = '무료체험';
	}else{
		$pay_plan_info['name'] = '수업플랜없음';
	}
	
	//2011-01-22 Start : 반일 1/3일 1/4일 연장처리
	$ex_count = $loginInfo['User']['ex_count'];
	
	$mypage_info['last_day'] = '';
	
	if($ex_count == '10'){
		if($pay_plan == 'E2'){
			$mypage_info['last_day'] = '반일';
		}if($pay_plan == 'E3'){
			$mypage_info['last_day'] = '1/3일';
		}if($pay_plan == 'E4'){
			$mypage_info['last_day'] = '1/4일';
		}
	}
	//compare pay_plan and ex_count
	
	//2011-01-22 End :
	
	//횟수플랜의 경우 받은횟수 카운트
	if($c_number != 0){
		
		$conditions = array();
		
		$conditions['user_id'] = $user_id;
		$conditions['reserve_time'] = $c_date;
		
		// 2014-03-17 levi aviles there is M2, M3 inside function getClassNoStatus
		$tmpClassNo = $this->ModelHandler->getClassNoStatus($conditions,'Lesson');
		
		//counts
		$lesson_status = array('F','R','A','E','EM','M','H');
		
		$reservation_kinds = array('K','P','M');
		
		$OR_array = array ("Lesson.lesson_status" => $lesson_status);
		
		/*$count_statuses = $this->Lesson->find('all', 
			array('conditions' => array('Lesson.user_id' => $user_id,
										'Lesson.reserve_time >='=> $c_date,
										array('OR' => $OR_array),
										'Lesson.reservation_kinds' => $reservation_kinds ),
				  'fields' => array('Lesson.reservation_kinds', 'Lesson.lesson_status', 'count(Lesson.lesson_status) as count'),						
				  'group' => array('Lesson.reservation_kinds','Lesson.lesson_status')
				  
			));
        
		foreach($count_statuses as $status){
			
		}	*/
		//pr($tmpClassNo);
		
		$lessons_counts['K'] = array('R'=>0,'F'=>0, 'A'=>0, 'C'=>0, 'E'=>0, 'EM'=>0, 'M'=>0);
		$lessons_counts['P'] = array('R'=>0,'F'=>0, 'A'=>0, 'C'=>0, 'E'=>0, 'EM'=>0, 'M'=>0);
		$lessons_counts['M'] = array('R'=>0,'F'=>0, 'A'=>0, 'C'=>0, 'E'=>0, 'EM'=>0, 'M'=>0);
		$lessons_counts['MD'] = array('R'=>0,'F'=>0, 'A'=>0, 'C'=>0, 'E'=>0, 'EM'=>0, 'M'=>0);
		// 2014-03-17 15:34 levi aviles M2 & M3 new reservation_kinds
		$lessons_counts['M2'] = array('R'=>0,'F'=>0, 'A'=>0, 'C'=>0, 'E'=>0, 'EM'=>0, 'M'=>0);
		$lessons_counts['M3'] = array('R'=>0,'F'=>0, 'A'=>0, 'C'=>0, 'E'=>0, 'EM'=>0, 'M'=>0);
		
	    foreach($tmpClassNo as $tmpCount){
	    	$lesson_status = $tmpCount['Lesson']['lesson_status'];
	    	$reservation_kinds = $tmpCount['Lesson']['reservation_kinds'];
	    	$count = $tmpCount[0]['count'];
			$lessons_counts[$reservation_kinds][$lesson_status] = $count;
		}
			
		//$classNo = $tmpClassNo;
		//pr($lessons_counts);
		$pay_plan_info['count'] = $lessons_counts;
	}
	
		// 2014-03-17 15:36 levi aviles array('Lesson.reservation_kinds' => 'M'),array('Lesson.reservation_kinds' => 'MD') 
		//2012-05-23 FIXED STUDENTS LAST DAY 
		$last_fix_schedules = $this->Lesson->find('all', array(
			'fields' => array('MAX(Lesson.reserve_time) AS max_reserve_time') ,
			'conditions' => array(
				'Lesson.user_id' => $user_id,
				'OR' => array(array('Lesson.reservation_kinds' => 'M'),array('Lesson.reservation_kinds' => 'MD'), array('Lesson.reservation_kinds' => 'M2'), array('Lesson.reservation_kinds' => 'M3')),
				'Lesson.lesson_status' => 'R',
				'Lesson.reserve_time >=' => $today
				),
			'group'=>array('TIME(Lesson.reserve_time)'),
			'order'=>array('Lesson.reserve_time DESC')
		));
		
		$last_fix_schedule = $last_fix_schedules;
		
		//pr($last_fix_schedules); 
		
		if($last_fix_schedule == null){
			$fix_schedule_message = '현재날짜 이후로 신청하신 고정수업 스케줄이 없습니다.';
		}else{
			$last_sched = '<div>'; $o=1; $a=false;
			foreach ($last_fix_schedules as $last_fix_schedule_time) :
				if ($a == true) {
					$last_sched .= '<div>'; $a=false; }
				$last_sched .= '[<b>'.$last_fix_schedule_time[0]['max_reserve_time'].'</b>]';
				if ($o%2==0) {
					$last_sched .= "</div>"; $a=true; }
				$o++;
			endforeach;
			$fix_schedule_message = $last_sched;
		}
		//2012-05-23 FIXED STUDENTS

		//added 2012-08-05 levy aviles
		$this->rstatus();
		
		
	//2012-01-22 payments tables 
	
	//Pay Plan End 2010-10-18
	$this->set('mypage_info',$mypage_info);	
	// include Cakephp Paging;
	$this->set('lessons',$lessons);
	$this->set('paginator',$lessons);
	
	$this->set('fomattedDays',$fomattedDays);
	$this->set('level_status_string',$level_status_string);
	$this->set('pay_plan_info',$pay_plan_info);
	
	$this->set('fix_schedule_message',$fix_schedule_message);
	
	/*=============================================================================================*/
	$this -> loadModel('Dictionary');
	$this -> loadModel('DictionaryDetail');
	$this -> DictionaryDetail -> bindModel( array('belongsTo' => array('Dictionary' => array('className' => 'Dictionary', 'foreignKey' => 'dictionary_id'))) );
	$reservationKinds = $this -> DictionaryDetail -> find('all', array(
			'order' => 'DictionaryDetail.name ASC',
			'fields' => array('DictionaryDetail.name','DictionaryDetail.express_name_ko','DictionaryDetail.express_name_en'),
			'conditions' => array('Dictionary.name' => 'reservation_kind')));
	$this -> set('reservationKinds', $reservationKinds);
	
	$this -> DictionaryDetail -> bindModel( array('belongsTo' => array('Dictionary' => array('className' => 'Dictionary', 'foreignKey' => 'dictionary_id'))) );
	$lessonStatus = $this -> DictionaryDetail -> find('all', array(
			'order' => 'DictionaryDetail.name ASC',
			'fields' => array('DictionaryDetail.name','DictionaryDetail.express_name_ko','DictionaryDetail.express_name_en'),
			'conditions' => array('Dictionary.name' => 'lesson_status')));
	$this -> set('lessonStatus', $lessonStatus);
	/*=============================================================================================*/
	//$_TotalHomework = $this->HomeworkHandler->TotalHomework($user_id);
	//if ($_TotalHomework > 0){
	//	$_content_total_homework = '<div style="position:absolute;z-index:9999;border:1px solid #000;padding:5px;background:#FFF;font-size:20px;left:220px" align="center">'.$_TotalHomework.'</div>';
	//	$this->set('content_total_homework',$_content_total_homework);
	//}
	
	$count = true;
	$this -> set( 'getPaymentHistory', $this -> Programmer1Handler -> getPaymentHistory ( $loginInfo['User']['mb_id'], $count ) );
	$this -> set( 'getEvaerCodeThruPayment', $this -> Programmer1Handler -> getEvaerCodeThruPayment($loginInfo['User']['mb_id']) );

	$proceedNotice=$this->proceedNotice();
	$this->set("proceedNotice", $proceedNotice);

	$ChangeLanguageTo = $this->DefaultHandler->GetLanguage();
	$this->set('ChangeLanguageTo',$ChangeLanguageTo['ko']);
	
	// Getting list of payment where the last_day is ongoing
	$today = date('Y-m-d');
	$this->set('getMyListOfPayment',$this->getMyPayment(array('Payment.pay_ok'=>array('Y','PC'),'Payment.mb_id'=>"{$loginInfo['User']['mb_id']}")));

	# added by dave
	# 6-22-2016
	# enable language translation
	# $this->set('LANG',$this->LanguageHandler->multilingual('lessons','mypage'));
}
/************************************************************************************************************************************************/

function search(){
	$loginInfo = $this->Session->read('loginInfo');
	$today = date('Y-m-d');

	$conditions = array();
	$lesson_list = array();
	$lesson_status = array();
	$reservation_kinds = array();
	$total_lesson_status = array();
	
	$userId = $loginInfo['User']['id'];
	$conditions['Lesson.user_id'] = $loginInfo['User']['id'];
	
	$this->loadModel('Dictionary');
	$this->Dictionary->bindModel(array('belongsTo'=>array(
			'DictionaryDetail'=>array('className'=>'DictionaryDetail','type'=>'left','foreignKey'=>false,'conditions'=>'Dictionary.id=DictionaryDetail.dictionary_id')
	)));
	$dictionary_lesson_status = $this->Dictionary->find('all',array('fields'=>array('DictionaryDetail.*'),'conditions'=>array('Dictionary.name'=>'lesson_status')));
	
	foreach($dictionary_lesson_status as $key_dictionary_lesson_status => $k_dls){
		$lesson_status[] = $k_dls['DictionaryDetail']['name'];
	}
	$conditions['Lesson.lesson_status'] = $lesson_status;

	$this->set('dictionary_lesson_status',$dictionary_lesson_status);
	
	$this->Dictionary->bindModel(array('belongsTo'=>array(
			'DictionaryDetail'=>array('className'=>'DictionaryDetail','type'=>'left','foreignKey'=>false,'conditions'=>'Dictionary.id=DictionaryDetail.dictionary_id')
	)));
	$dictionary_reservation_kinds = $this->Dictionary->find('all',array('fields'=>array('DictionaryDetail.*'),'conditions'=>array('Dictionary.name'=>'reservation_kind')));

	foreach($dictionary_reservation_kinds as $key_dictionary_reservation_kinds => $k_drk){
		$reservation_kinds[] = $k_drk['DictionaryDetail']['name'];
	}
	$conditions['Lesson.reservation_kinds'] = $reservation_kinds;

	$this->set('dictionary_reservation_kinds',$dictionary_reservation_kinds);

	if(isset($this->params['form']['search_from'])){

		if(isset($this->params['form']['payment_id'])){
			$this->passedArgs['payment_id'] = $this->params['form']['payment_id'];
		}

		if(isset($this->params['form']['reservation_kinds'])
				&& strlen($this->params['form']['reservation_kinds']) > 0){
			$this->passedArgs['reservation_kinds'] = $this->params['form']['reservation_kinds'];
		}

		if(isset($this->params['form']['lesson_status'])
				&& strlen($this->params['form']['lesson_status']) > 0){
			$this->passedArgs['lesson_status'] = $this->params['form']['lesson_status'];
		}

		if(strtotime($this->params['form']['search_from']) > 0){
			$this->passedArgs['search_from'] = date('Y-m-d',strtotime($this->params['form']['search_from']));
		}

		if(strlen($this->params['form']['search_to']) > 0){
			$this->passedArgs['search_to'] = date('Y-m-d',strtotime($this->params['form']['search_to']));
		}
	}
	
	// Getting list of payment where the last_day is ongoing
	$this->getMyPayment = $this->getMyPayment(array('Payment.pay_ok'=>array('Y','PC'),'Payment.mb_id'=>"{$loginInfo['User']['mb_id']}"));
	
	if(isset($this->passedArgs['payment_id'])){
		/* 
		if($this->passedArgs['payment_id'] != 'all'){
			$this->loadModel('Payment');
			$payment_info = $this->Payment->find('first',array('fields'=>array('Payment.*'),'conditions'=>array('Payment.id'=>$this->passedArgs['payment_id'])));

			if(!isset($this->passedArgs['search_from']) && empty($this->passedArgs['search_from'])){
				$this->passedArgs['search_from'] = date('Y-m-d',strtotime($payment_info['Payment']['start_day'] . '-2 years'));
			}

			if(!isset($this->passedArgs['search_to']) && empty($this->passedArgs['search_to'])){
				$this->passedArgs['search_to'] = date('Y-m-d',strtotime($payment_info['Payment']['start_day']));
			}
		}
		 */
	}
	else{
		if(isset($this->getMyPayment[0])){
			$this->passedArgs['payment_id'] = $this->getMyPayment[0]['Payment']['id'];
			/* 
			$this->passedArgs['search_from'] = date('Y-m-d',strtotime($this->getMyPayment[0]['Payment']['start_day'] . '-2 years'));
			$this->passedArgs['search_to'] = date('Y-m-d',strtotime($this->getMyPayment[0]['Payment']['start_day']));
			 */
		}
	}
	
	$this->set('getMyListOfPayment',$this->getMyPayment);
	
	// The passed arguments
	if(isset($this->passedArgs['payment_id'])){
		if(intval($this->passedArgs['payment_id']) > 0){
			$conditions['Lesson.payment_id'] = array($this->passedArgs['payment_id']);
			
			// Getting total of F, A, C, CP
			$total_lesson_status = $this->Lesson->find('all',array(
					'fields'=>array('COUNT(Lesson.id) as total_lesson_status','Lesson.lesson_status','Lesson.payment_id')
					,'conditions'=>array(
							'Lesson.user_id'=>$loginInfo['User']['id']
							,'Lesson.lesson_status'=>array('F','A','R','CP')
							,'Lesson.payment_id'=>array($this->passedArgs['payment_id'])
					)
					,'group'=>'Lesson.lesson_status'
			));
		}
	}
	
	if(isset($this->passedArgs['reservation_kinds']) && $this->passedArgs['reservation_kinds'] != 'all'){
		$conditions['Lesson.reservation_kinds'] = array($this->passedArgs['reservation_kinds']);
	}
	
	if(isset($this->passedArgs['lesson_status']) && $this->passedArgs['lesson_status'] != 'all'){
		$conditions['Lesson.lesson_status'] = array($this->passedArgs['lesson_status']);
	}
	
	if(isset($this->passedArgs['search_from'])){
		$conditions['DATE(Lesson.reserve_time) >='] = "{$this->passedArgs['search_from']}";
	}
	
	if(isset($this->passedArgs['search_to'])){
		$conditions['DATE(Lesson.reserve_time) <='] = "{$this->passedArgs['search_to']}";
	}

	$this->paginate=array(
			'fields'=>array('Lesson.*','Teacher.tea_name','Teacher.img_file'/* ,'DictionaryDetailLessonStatus.*','DictionaryDetailReservationKinds.*' */)
			,'joins'=>array(
					array('table'=>'teachers','alias'=>'Teacher','type'=>'LEFT','conditions'=>array('Lesson.teacher_id=Teacher.id'))
					,false
			)
			,'conditions'=>$conditions
			,'order'=>array('Lesson.reserve_time ASC','Teacher.tea_name ASC')
			,'limit'=>10
			,'passit'=>$this->passedArgs
	);
	$lessons = $this->paginate('Lesson');
	
	$this->set('lessons', $lessons);
	$this->set('lesson_status_value',$this->Programmer1Handler->lesson_status_value);
	$this->set('total_lesson_status',$total_lesson_status);
}


//2009-10-07 CUBICTALKとCUBICBOARD連動 
function teacher_mypage($id = null, $mb_id = null)
{
	//セッション情報がなくなった場合、Cubicboardのログインページに遷移する
	$loginInfo = $this->Session->read('loginInfo');
	if($mb_id == null && $loginInfo == null){
		$this->redirect(b_path()."/bbs/mypage.php");
	}
	
	//CubicBoardから遷移した場合、CubicBoardから遷移する以外には
	//いつも$mb_idはNULL
	if($mb_id != null ){
		//mb_idを利用して、teacher_idを求める
		$teacher = $this->ModelHandler->getTeacherByMbId($mb_id);
		//先生のポイントを求める
		$mbPoint = $this->ModelHandler->getMbPoint($mb_id);
		$teacher_id = $teacher['id'];
		$teacher['points'] = $mbPoint;
		$teacher['mb_id'] = $mb_id;
		
		//teacher情報をloginInfoセッション情報に登録する
		$this->Session->write('loginInfo',$teacher);
		
		//Lesson、UserJoinして情報から予約されたレッスン情報を求める
		$lessons = $this->ModelHandler->getTeacherReservation($teacher_id);
		//print_r($lessons);
		
	
		//Menuから遷移する時、先生ＩＤで検索される
		//$idがＮＵＬＬではない
	}else{
		$lessons = $this->ModelHandler->getTeacherReservation($id);
	}
	
	$this->set('lessons',$lessons);	
}

//2009-11-17 先生のマイページからＦＡボタンを押して、レッスン
//状況変えて先生のページに戻る
function teacher_change_lesson_status($lesson_id = null, $update = null)
{
	$teacher = $this->Session->read('loginInfo');
	$lessonData['lesson_status'] = $update;
	$lessonData['id'] = $lesson_id;
	$this->ModelHandler->updateLessonStatus($lessonData , 'Lesson');
	
	$this->redirect(array('action' => 'teacher_mypage/'.$teacher['id']));
}

function myhistory()
{
	$loginInfo = $this->Session->read('loginInfo');
	$user_id = $loginInfo['User']['id'];
	
	// <!-- Begin get the setting of web page
	$this->loadModel('User');
	$UserInfo = $this->User->find('first',array('fields'=>array('*'),'conditions'=>array('id'=>$user_id)));
	$this->set('UserInfo',$UserInfo);
	// --> End get the setting of web page
	
	
	$ct = get_time();
	$today = $ct['time_ymd'];
	//lesson status
	$lesson_status = array('R'=>'수업예약','F'=>'수업완료','A'=>'수업결석','C'=>'수업취소',
			'E'=>'수업연장','EM'=>'수업연장');

	$this->set('lesson_status',$lesson_status);

	$this->loadModel('Dictionary');
	$this->Dictionary->bindModel(array('belongsTo'=>array(
			'DictionaryDetail'=>array('className'=>'DictionaryDetail','type'=>'LEFT','foreignKey'=>false,'conditions'=>array('Dictionary.id=DictionaryDetail.dictionary_id'))
	)));
	$reservation_kinds=$this->Dictionary->find('all',array('fields'=>array('DictionaryDetail.name'),'conditions'=>array('Dictionary.name'=>'reservation_kind')));
	$reservation_kinds_name=array();
	foreach ($reservation_kinds as $keyreservation_kinds => $krk){ // $krk means $keyreservation_kinds;
		$reservation_kinds_name[]=$krk['DictionaryDetail']['name'];
	}
	$this->Lesson->unbindModel(array('belongsTo'=>array('Teacher')),false);
	$fields=array(
			'Lesson.*'
			,'Teacher.id','Teacher.tea_name','Teacher.img_file'
// 			,'Payment.id','Payment.created'
	);
	$conditions=array();
	$conditions['Lesson.user_id']=$user_id;
	$conditions['Lesson.reserve_time < ']="$today";
	$lesson_status=array('R','F','A','E','EM','C');
	$reservation_kinds=array();

	$conditions['Lesson.reservation_kinds']=$reservation_kinds_name;
// 	$this->passedArgs['reservation_kinds']=json_encode($reservation_kinds_name);
	if(isset($this->params['form']['reservation_kinds']) && strlen(trim(str_replace(" ","",$this->params['form']['reservation_kinds']))) > 0){
		$reservation_kinds=array($this->params['form']['reservation_kinds']);
		if($this->params['form']['reservation_kinds']=='M'){
			$GetAllReservationKinds = $this->Programmer1Handler->GetAllReservationKinds();
			$reservation_kinds=array_merge(array('M','MD','M3','M2'),$GetAllReservationKinds);
		}
		$conditions['Lesson.reservation_kinds']=$reservation_kinds;
		$this->passedArgs['reservation_kinds']=json_encode($reservation_kinds);
	}
	$conditions['Lesson.lesson_status']=$lesson_status;
// 	$this->passedArgs['lesson_status']=json_encode($lesson_status);
	if(isset($this->params['form']['lesson_status']) && strlen(trim(str_replace(" ","",$this->params['form']['lesson_status']))) > 0 && $this->params['form']['lesson_status']!='ALL'){
		$conditions['Lesson.lesson_status']=array($this->params['form']['lesson_status']);
		$this->passedArgs['lesson_status']=json_encode($this->params['form']['lesson_status']);
	}
	
	if (!empty($this->passedArgs['reservation_kinds']) && strlen(trim(str_replace(" ","",$this->passedArgs['reservation_kinds']))) > 0) {
		$conditions['Lesson.reservation_kinds']=json_decode($this->passedArgs['reservation_kinds']);
	}
	if (!empty($this->passedArgs['lesson_status']) && strlen(trim(str_replace(" ","",$this->passedArgs['lesson_status']))) > 0) {
		$conditions['Lesson.lesson_status']=json_decode($this->passedArgs['lesson_status']);
	}
	
	$this->paginate=array(
			'fields'=>$fields
			,'joins'=>array(
				 array('table'=>'teachers','alias'=>'Teacher','type'=>'LEFT','conditions'=>array('Lesson.teacher_id=Teacher.id'))
// 			,array('table'=>'payments','alias'=>'Payment','type'=>'LEFT','conditions'=>array('Lesson.payment_id=Payment.id'))
			)
			,'conditions'=>$conditions
			,'order'=>array('Lesson.reserve_time DESC')
			,'limit'=>10
			,'passit'=>$this->passedArgs
	);
	$myhistory=$this->paginate('Lesson');
	$this->set('myhistory',$myhistory);
	$this->set('paginator',$myhistory);
} // End function myhistory(){}

//Lesson 予約完了
function reserve_complete()
{
	global $const; 
	$this->set('const', $const);
	
	global $language_reserve_complete; 
	
	$this->set('language_reserve_complete', $language_reserve_complete);
	
	//セッション情報を取る
	$loginInfo = $this->Session->read('loginInfo');
	$tacherConfirmSch = $this->Session->read('tacherConfirmSch');
	
	//2010-02-10 YHLEE START
	//set hidden value in complete page
	//TEACHER＿IDを設定(次の予約ために同じ先生のページに飛ぶように)
	$this->set('teacher_id',$this->data['Lesson']['teacher_id']);
	//2010-02-10 YHLEE END
	$messages = ''; 
	if($tacherConfirmSch == null){
		// '예약시간을 설정하지 않으신것 같습니다.
		$messages = $language_reserve_complete[0];
		$this->set('messages', $messages);
		return;
	}
	//自由検索文に変更
	$where_string = 'Available_lesson.teacher_id = '. $this->data['Lesson']['teacher_id'];
	$where_string .= " AND Available_lesson.reserve_status = 'R'";
	$number_of_reserve = sizeof($tacherConfirmSch);
	$i = 1;
	
	//2012-02-05 start
	$student_reserve_time = null;
	$user_id = $loginInfo['User']['id'];
	$teacher_id = $this->data['Lesson']['teacher_id'];
	
	$reserve_count = array();
	//2012-02-05 end
	
	foreach($tacherConfirmSch as $sch) {
		if($i == 1){
			$where_string .= ' AND (';
		}else{
			$where_string .= ' OR ';
		}
		
		$student_reserve_time = sprintf("%d-%d-%d %d:%d:00",
		substr($sch,0,4),substr($sch,4,2),substr($sch,6,2),substr($sch,8,2),substr($sch,10,2));
		
		$where_string .= " Available_lesson.available_time = '". sprintf("%d-%d-%d %d:%d:00",
		substr($sch,0,4),substr($sch,4,2),substr($sch,6,2),substr($sch,8,2),substr($sch,10,2))."'";
		
		if($i == $number_of_reserve){
			$where_string .= ")";
		}
		$i++;
	}
    
	$reservation_info = $this->Lesson->reservation_info($where_string);
	
	
	if(sizeof($tacherConfirmSch)!= sizeof($reservation_info)){
		//'죄송합니다. 다른 회원님에 의해 예약이 되었습니다.'
		$messages=$language_reserve_complete[1];
		$this->set('messages', $messages);
	}else{
		//DBをUPDATEする前に十分なポイントを持っているか確認する
		//セッションからユーザポイントを求める
		//ユーザーが持っているポイントがレッスン予約合計より小さい場合DBUPDATEしないずにメッセージセットする
		//ユーザポイントをもう一度求める
		$loginInfo = $this->Session->read('loginInfo');
		$mb_id = $loginInfo['User']['mb_id'];
		
		//load teachers name
		//$teachers_name = $this->ModelHandler->getTeacherNames();
		
		//No Error In case of normal
		//get reservation_kinds variable from confirmation page
		$reservation_kinds = $this->params['form']['reservation_kinds'];
		
		if($reservation_kinds ==$const['point_reservation']){
			//get user point from g4member table
			$user_point = $this->ModelHandler->getMbPoint($mb_id,'Lesson');
			$loginInfo['User']['points'] = $user_point;
			
			//decide errors of lack of points
			if($loginInfo['User']['points'] < $this->data['Lesson']['spend_point']*$number_of_reserve){
				//エラーメッセージセット
				//'포인트가 부족한 것 같습니다. 포인트 구입후 다시 예약해 주시기 바랍니다.'
				$messages = $language_reserve_complete[2];
				$this->set('messages', $messages);
				return;
			}
		}
		//process common regardless point reservation or koma reservation
		
		//lesson data input
		
			//ループを回りなから　Insert　Updateする
			$ct = get_time();	//現在の時間を求める
			$current_time = $ct['time_ymdhis'];
			
			
			
			//ポイントテーブルにＩＮＳＥＲＴ
			$i = 0;
			$comment_id = null;
			
			foreach($reservation_info as $r_info){
				
				//transanction start
				//$this->Lesson->begin();
				
				//画面でコメントが入力しているか判断する
				if($this->params['form']['comment'.$i]!=null 
					&& $this->params['form']['comment'.$i]!=''){
					//commentテーブルのＭＡＸ値を求める
					$max_id = $this->ModelHandler->getCommentMaxId();
					
					//$this->data['Lesson']['comment_id'] = $max_id;
					if($i==0){
						$comment_id = $max_id;
					}else{
						$comment_id++;
					}
				}
				
				//lessonテーブルにインサーとする
				$pay_plan = $loginInfo['User']['pay_plan'];
				
				$this->data['Lesson']['class_minutes'] = 0; // 25 or 30 Minutes class
				if($pay_plan == 'M' && $reservation_kinds ==$const['point_reservation']){
					$reservation_kinds_data = 'T';
					
					if(isset($this->params['form']['class_in_use'])){
						if($this->params['form']['class_in_use'] === 'telephone'){
							$reservation_kinds_data = 'TM';
							$this->data['Lesson']['class_minutes'] = 10;
						}
					}
				} else if($reservation_kinds ==$const['point_reservation']){
					$reservation_kinds_data = 'P';
				}else if($reservation_kinds ==$const['koma_reservation']){
					$reservation_kinds_data = 'K';
				}
				
				$this->data['Lesson']['user_id'] = $loginInfo['User']['id'];
				$this->data['Lesson']['teacher_id'] = $this->data['Lesson']['teacher_id'];
				$this->data['Lesson']['available_lesson_id'] = $r_info['Available_lesson']['id'];
				$this->data['Lesson']['lesson_status'] = 'R';
				$this->data['Lesson']['point_id'] ='';
				
				//コメント入力したかによって
				$this->data['Lesson']['comment_id'] = $comment_id;
				$this->data['Lesson']['spend_point'] = $this->data['Lesson']['spend_point'];
				$this->data['Lesson']['reserve_time'] = $r_info['Available_lesson']['available_time'];
				
				//textCodeを何を入れてるかによって
				$this->data['Lesson']['text_code'] = $this->data['Lesson']['text_code'.$i];
				$this->data['Lesson']['reservation_kinds'] = $reservation_kinds_data;
				
				$this->data['Lesson']['lesson_file'] = null;
				$result = $this->Lesson->save($this->data);
				
				//Commentテーブルにコメントを入力する
				//2010-02-04 YHLEE UPDATE START
				//change relationship between lesson table and comment table is one by one
				//even if uer do not comment data , insert comment table with ''
				
				//2010-04-21 start
				//$lesson_max_id = $this->ModelHandler->getLessonMaxId('Lesson');
				
				$lesson_max_id = $this->Lesson->getLastInsertID();

				// Mail System
				$this->requestAction('/phpmailers/mail/',array('form'=>array('post'=>array('Lesson'=>array('id'=>$lesson_max_id)),'mail_type'=>'Reservation')));
				
				//pr($lesson_max_id);
								
				if($this->params['form']['comment'.$i] == null){
					$this->params['form']['comment'.$i]='';
				}
					
				$this->ModelHandler->insertComment($loginInfo['User']['id'],
				$this->data['Lesson']['teacher_id'],
				0,mysql_real_escape_string(trim($this->params['form']['comment'.$i]))
				,$lesson_max_id);

				//update availableLesson table(teacher's reservation table)
				$availableLessonData['id'] = $r_info['Available_lesson']['id'];
				$availableLessonData['reserve_status'] = $this->ModelHandler->L_Finish;
				$this->ModelHandler->updateRserveStatusOnAvailableLesson($availableLessonData,'Lesson');
				
				// One more time Updating Available_lesson
				// Identifying the Reservation if its a 25 (or 30) minute,10 minutes and others
				$today = date('Y-m-d H:i:s');
				$this->loadModel('Available_lesson');
				$available_lesson = array();
				$available_lesson['id'] = $r_info['Available_lesson']['id'];
				$available_lesson['class_minutes'] = $this->data['Lesson']['class_minutes'];
				$available_lesson['modified'] = "{$today}";
				$this->Available_lesson->save($available_lesson);

			  //ポイントテーブルを件数別インサートする
			  //仕様変更g4_pointテーブルにインサートする
			  //料金プランによってポイントテーブルに設定する内容が違う
			  //2010-02-04 YHLEE UPDATE START 
			  if($reservation_kinds == $const['point_reservation']){  
					$spent_point = -$this->data['Lesson']['spend_point'];
				
				
				$content = $this->data['Lesson']['reserve_time']." ".$language_reserve_complete[3];
	    		  $sql = " insert into g4_point 
	                set mb_id = '$mb_id',
	                    po_datetime = '$current_time',
	                    po_content = '$content',
	                    po_point = '$spent_point',
	                    po_rel_table = 'lessons',
	                    po_rel_id = '',
	                    po_rel_action = '$language_reserve_complete[3]' ";
	    		  
					$free_sql_result = $this->Lesson->free_sql($sql);
			  
			  }else if($reservation_kinds == $const['koma_reservation']){
				//今日予約した回数を一つ増やす
				$loginInfo['User']['todayReservation'] = $loginInfo['User']['todayReservation']++;
			  }	
		
			  	$i++;
			}
			//end of roop
			
			//ポイントはg4_pointからSUMする
			//only in case of point reservation
			if($reservation_kinds == $const['point_reservation']){  
				$select_string = "sum(po_point) AS point ";
				$table = "g4_point ";
				$where_string =  "mb_id = '$mb_id' ";
				
				$point_results = $this->Lesson->select_free_common($select_string,$table,$where_string);
			    foreach($point_results as $point_result){
			    	$point = $point_result[0]['point'];
			    }
		    
				//g4_member UPDATEする
				$sql =  "update g4_member
					   SET mb_point = $point 
						WHERE mb_id = '$mb_id'";
				$this->Lesson->free_sql($sql);
				
				//セッションのユーザポイントをUPDATEする
				$loginInfo['User']['points'] = $point;
			}
			
			//$this->Lesson->rollback();
			
			//2012-02-05 START It can be deleted
			$this->Lesson->bindModel(array('belongsTo' => array('Teacher'=>array('className' => 'Teacher','foreignKey' => 'teacher_id'))));
			$reserve_count = $this->Lesson->find('all',
						array( 
						      'conditions' => array('Lesson.user_id' => $user_id
													  ,'Lesson.lesson_status'  => 'R'
													  ,'Lesson.reserve_time'  =>$student_reserve_time)
			)); 
			
			//if reservation is sucessivly reserved , reserve count sould be 1 
			//pr($reserve_count);
			//pr(sizeof($reserve_count));
			if(sizeof($reserve_count) > 0){
				
			}else{
				$messages .= "예약이 현재 이루어지지 않았습니다. 현재 강사님의 예약 화면에서 다시 예약 부탁드립니다.";
			}
			//pr($reserve_count);
			//2012-02-05 END
			$this->set('reserve_count', $reserve_count);	 
			$this->Session->write('loginInfo',$loginInfo);
			$this->set('messages', $messages);	
		}
	
	
	//クリックしたセッションを消す
	//戻った時に青いまま残っているから
	$this->Session->write('tacherConfirmSch',null);
	
	//TEACHER＿IDを設定(次の予約ために同じ先生のページに飛ぶように)
	$this->set('teacher_id',$this->data['Lesson']['teacher_id']);
}

function teacher_reserve_complete()
{
	//TEACHER＿IDを設定(次の予約ために同じ先生のページに飛ぶように)
	$ct = get_time();	//現在の時間を求める
	$current_time = $ct['time_ymdhis'];
	$tacherConfirmSch = $this->Session->read('tacherConfirmSch');
	$teacher_id = $this->data['Lesson']['teacher_id'];
	print_r($this->data);
	foreach($tacherConfirmSch as $sch) {
		$available_time = sprintf("%d-%d-%d %d:%d:00",
		substr($sch,0,4),substr($sch,4,2),substr($sch,6,2),substr($sch,8,2),substr($sch,10,2));
		$sql = " insert into available_lessons  
                set teacher_id = '$teacher_id' ,
                    available_time = '$available_time',
                    reserve_status = 'R',
                    teacher_comment = '',
                    created = '$current_time',
                    modified = '$current_time'";
                   
		$free_sql_result = $this->Lesson->free_sql($sql);
	}
	
	//クリックしたセッションを消す
	//戻った時に青いまま残っているから
	$this->Session->write('tacherConfirmSch',null);
	
	$this->set('messages',null);
	$this->set('teacher_id',$this->data['Lesson']['teacher_id']);
}

	function lesson_cancel($lesson_id_get = null){
		$loginInfo = $this->Session->read('loginInfo');
		$lesson_id = '';
		if(isset($this->params['form']['cancel_point'])){
			$lesson_id = $this->params['form']['cancel_point'];
			$pay_plan = $loginInfo['User']['pay_plan'];
			$user_id = $loginInfo['User']['id'];
			$start_day = $loginInfo['User']['start_day'];
			$last_day = $loginInfo['User']['last_day'];
			
			// Find Information of Lesson Id
			$results = $this->ModelDefaultHandler->select_lessons($lesson_id,'Lesson');
			$lesson_model = $results['Lesson'];
			
			$reserve_time = $lesson_model['reserve_time'];
			$teacher_id = $lesson_model['teacher_id'];
			$payment_id = $lesson_model['payment_id'];
			$reservation_kinds = $lesson_model['reservation_kinds'];
			$condiCancelName = array('number_cancel_'.$reservation_kinds,'number_cancel_'.$reservation_kinds.'_point_begin','number_cancel_'.$reservation_kinds.'_point_deduction');
			$numOfCancel = $this->Programmer1Handler->numberOfCancelation($condiCancelName);
			$OtherCancelPaymentGoodsName=$this->Programmer1Handler->OtherCancelPaymentGoodsName();
			// This function accept other reservation_kinds FIXED SCHEDULE
			// Not include the reservation_kinds M,MD,M2,M3 were already used
			$OtherCancelReservationKinds=$this->Programmer1Handler->OtherCancelReservationKinds();
			$numOfCancelOk = false;
			$numberLessonNOC = array();
			$numberCancelled = 0;
			$message = '';
			$numOfCancelDefault = 1;
			$reservation_info_accept = array('M','M2','M3','MD');
			$paymentInfo = '';
			$goodsNameDictionary = '';
			$numOfCancelMsg = '';
			$goodsNamePlan = '';
			$goodsNamePlanNum = 1;
			$point_deduction = 0;
			$point_deduction_confirm = false;

			// get the lesson cancelled that reserve_time has in the korean_holidays
			$getLessonCancelWithKoreanHoliday = array('reserve_time'=>$reserve_time,'payment_id'=>$payment_id,'reservation_kinds'=>$reservation_kinds);
			// $gLCWKHoliday means getLessonCancelWithKoreanHoliday return array;
			$gLCWKHoliday = $this->Programmer1Handler->getLessonCancelWithKoreanHoliday($getLessonCancelWithKoreanHoliday);
			$this->set('gLCWKHoliday',$gLCWKHoliday);
				
			if($payment_id != 0){
				$this->loadModel('Payment');
				$numbergoods_months_org = $this->Payment->find('first',array('fields'=>array('Payment.goods_name','Payment.goods_months','Payment.start_day'
						, 'Payment.last_day','Payment.created'),'conditions'=>array('Payment.id'=>$payment_id)));
				// This condition why other payment doesn't have point deduction;
				// if(in_array($reservation_kinds, $reservation_info_accept)){
					// This condition allows other reservation to limits the cancelation
					if(count($numOfCancel) > 0){
						if(isset($numOfCancel['number_cancel_'.$reservation_kinds])){
							$numOfCancelDefault = $numOfCancel['number_cancel_'.$reservation_kinds];
							$goodsNamePlan = substr ($numbergoods_months_org['Payment']['goods_name'], 0 , 2);
							$goodsNamePlanNum = intval(substr($goodsNamePlan, -1 , 1));
							if($goodsNamePlanNum < 1){$goodsNamePlanNum=1;}
						}
					} // End if(count($numOfCancel) > 0){}
					$numbergoods_months = $numOfCancelDefault * $numbergoods_months_org['Payment']['goods_months'] * $goodsNamePlanNum;
					$this->Lesson->bindModel(array('belongsTo'=>array('Payment'=>array('className'=>'Payment','foreignKey'=>'payment_id'))));
					$numberLessonNOC = $this->Lesson->find('all', array('fields'=>array('Lesson.id'),'conditions'=>array(
							'Lesson.lesson_status'=> array('C','CP'),'Lesson.payment_id' => $payment_id
							//,'(Payment.goods_name LIKE "%F" OR Payment.goods_name LIKE "%F2" OR Payment.goods_name LIKE "%F3" OR Payment.goods_name LIKE "%FD")'
							,'OR'=>array(array('Payment.goods_name LIKE "%F"'),array('Payment.goods_name LIKE "%F2"')
									,array('Payment.goods_name LIKE "%F3"'),array('Payment.goods_name LIKE "%FD"')
									,array('Payment.goods_name'=>$OtherCancelPaymentGoodsName)))));
							
					// $numberCancelled is total of (query by all lessons cancelled [no exception]) and (query by all lessons cancelled [only] belongs to korean holiday)
					$numberCancelled = count($numberLessonNOC) - $gLCWKHoliday['KorHolidayPaymentId'];
					if($numberCancelled < 0){$numberCancelled = 0;}
					if($numberCancelled >= $numbergoods_months){
						$message = 'The number of cancelations ('.$numbergoods_months.') have been used.';
						$numOfCancelOk = true;
								
						if(isset($numOfCancel["number_cancel_{$reservation_kinds}_point_deduction"])
						&& !empty($numOfCancel["number_cancel_{$reservation_kinds}_point_deduction"])){
							$point_deduction = $numOfCancel["number_cancel_{$reservation_kinds}_point_deduction"];
							$point_deduction_confirm = true;
						} // End if (isset($numOfCancel["number_cancel_{$reservation_kinds}_point_deduction"])
						// && !empty($numOfCancel["number_cancel_{$reservation_kinds}_point_deduction"])){
					}
					else{
						$point_deduction = 10; // by default
						if(isset($this->point_begin)){
							$point_deduction = $this->point_begin;
						}
						elseif(isset($this->Programmer1Handler->point_begin)){
							$point_deduction = $this->Programmer1Handler->point_begin;
						}
						if(isset($numOfCancel["number_cancel_{$reservation_kinds}_point_begin"])
						&& !empty($numOfCancel["number_cancel_{$reservation_kinds}_point_begin"])){
							$point_deduction = $numOfCancel["number_cancel_{$reservation_kinds}_point_begin"];
						} // End if(isset($numOfCancel["number_cancel_{$reservation_kinds}_point_begin"])
						// && !empty($numOfCancel["number_cancel_{$reservation_kinds}_point_begin"])){}
					} // End if($numberCancelled >= $numbergoods_months){}else{}
				// } // End if(in_array($reservation_kinds, $reservation_info_accept)){}
					
				$this->set('point_deduction_confirm',$point_deduction_confirm);
				$this->set('point_deduction',$point_deduction);
				
				$goodsNameDictionary='25 minutes class';
				$this->loadModel('Dictionary');
				$dictionaryPayment=$this->Dictionary->find('first',array('fields'=>array('id'),'conditions'=>array('name'=>'payplan')));
				$dicPayment=array();
				$dicPayment['name']=$numbergoods_months_org['Payment']['goods_name'];
				if(isset($dictionaryPayment)){
					$dicPayment['dictionary_id']=$dictionaryPayment['Dictionary']['id'];
					$this->loadModel('DictionaryDetail');
					$dictionaryPayment = $this->DictionaryDetail->find('first',array('fields'=>array('DictionaryDetail.name'
							,'DictionaryDetail.express_name_en','DictionaryDetail.express_name_ko'),'conditions'=>$dicPayment));
						
					if(strlen($dictionaryPayment['DictionaryDetail']['express_name_en']) > 0){
						$goodsNameDictionary = $dictionaryPayment['DictionaryDetail']['express_name_en'];
					}
					if(strlen($dictionaryPayment['DictionaryDetail']['express_name_ko']) > 0){
						$goodsNameDictionary = $dictionaryPayment['DictionaryDetail']['express_name_ko'];
					}
				} // End if(isset($dictionaryPayment)){}
				
				$paymentInfo = '<div style = "font-weight:bold; color:#0099FF;">[ '
						.date('Y-m-d',strtotime($numbergoods_months_org['Payment']['created']))
						.' ], 결제하신 [ '.$goodsNameDictionary.' ], [ '.$numbergoods_months_org['Payment']['goods_months']
						.' ] 개월 수업과정입니다.</div><br>';
			} // End if($payment_id != 0){}
				
			$this->set('StatusCancel',array('CancelMsg'=>$message,'numOfCancelOk'=>$numOfCancelOk,'numOfCancel'=>$numberCancelled
					,'numOfCancelMsg'=>$numOfCancelMsg,'paymentInfo'=>$paymentInfo));
			
			$last_lesson = '';
			$new_last_reserve_time = '';
			
			$reserve_only_time=date('H:i:s',strtotime($reserve_time));
			$time_selected=$reserve_only_time;
			$ContractDays = $this->Programmer1Handler->PaymentIdOnContractDays($payment_id,$reservation_kinds,$time_selected);
			
			$last_lesson = $this->Lesson->find('first',array('fields'=>array('Lesson.id','Lesson.payment_id','Lesson.user_id','Lesson.teacher_id','Lesson.reserve_time'
					,'Lesson.reservation_kinds'),'conditions'=>array('Lesson.user_id'=>$user_id,'Lesson.lesson_status'=>array('R')
							,'Lesson.reservation_kinds'=>$reservation_kinds,'TIME(Lesson.reserve_time) = '=>"$reserve_only_time"),'order'=>'Lesson.reserve_time DESC'));
			$extend_only_day=date('Y-m-d',strtotime($last_lesson['Lesson']['reserve_time'] . '+1 day'));
			$check=true;
			while($check == true){
				if(in_array(date('w', strtotime($extend_only_day)),$ContractDays)){
					$check = false;
				}else $extend_only_day=date('Y-m-d',strtotime($extend_only_day . '+1 day'));
			} // End while($check == true){}
			
			if(in_array($reservation_kinds,array('M2','M3')) || in_array($reservation_kinds,$OtherCancelReservationKinds)){
				// $new_last_reserve_time = "$reserve_time 이 연장되어 $extend_only_day 수업이 추가 됩니다.";
				$new_last_reserve_time = $extend_only_day;
			}
			
			$extend_day = sprintf('%s %s',$extend_only_day,$reserve_only_time);
			
			// Get the Teacher Name
			$this->loadModel('Teacher');
			$teacher = $this->Teacher->find('first',array('fields'=>array('Teacher.id,Teacher.tea_name'),'conditions'=>array('id'=>$teacher_id)));
			$teacher_name = $teacher['Teacher']['tea_name'];
			
			$ct = get_time();
			$current_time = $ct['time_ymdhis'];
			$weekday_extend_button = 'Y';
			$time_difference = (time()+ 60*60*1) - (strtotime($lesson_model['reserve_time']));
			// 24hour before 2010-11-28 start
			$time_difference24 = (time()+ 60*60*24) - (strtotime($lesson_model['reserve_time']));
			// 24hour before 2011-01-28 start
			// Point plan change from 12hours -> 6hours
			$time_difference12 = (time()+ 60*60*12) - (strtotime($lesson_model['reserve_time']));
			// Point plan change from 06hours -> lesson_time
			$time_difference06 = (time()+ 60*60*6) - (strtotime($lesson_model['reserve_time']));
			
			$time_string = array('24'=>'24','12'=>'12','6'=>'6');
			
			//checking the point when they reservate
			$spent_point = $lesson_model['spend_point'];
			$cancelMsg = $teacher_name."강사님의 강의예약을 취소합니다.";
			
			if($reservation_kinds == 'P'){
				$cancelMsg .= "<BR>이수업은 포인트 예약입니다.";
			}else if($reservation_kinds == 'K'){
				$cancelMsg .= "<BR>이수업은 큐빅 예약입니다.";
			}else if($reservation_kinds == 'M'){
				$cancelMsg .= "<BR>이수업은 일괄 예약입니다.";
			}
			
			$cancelMsg .= " <SPAN class=t14_green_b>현재시간: $current_time 입니다.<br>";
			
			// number of cancel and payment info
			$cancelMsg .= $numOfCancelMsg.$paymentInfo;
			
			//Before starting the class
			if($time_difference < 0){
				//Point Plan and Trial Lesson Free reservation with Point
				if($reservation_kinds == 'P' && $pay_plan != 'M'){
					//In case of Point Plan 12시간내의 취소
					if($pay_plan != 'P'){
						if($time_difference06 > 0 && $time_difference < 0){
							$spent_point = $spent_point * 0.5;
							$cancelMsg .= "<SPAN class=sky_b>포인트플랜 경우 예약하시고 하루전에(6시간)안에 취소한
							포인트는 <b>$spent_point</b> 포인트 </SPAN>복구처리됩니다.";
						}
					//Other Plan(manager plan)
					}else{
						if($time_difference06 > 0 && $time_difference < 0){
							$spent_point = $spent_point * 0.5;
							$cancelMsg .= "<SPAN class=sky_b>매일플랜, 횟수플랜의 경우 포인트로 예약하시고 하루전에(6시간)안에 취소한 
							포인트는 <b>$spent_point</b> 포인트 </SPAN>복구처리됩니다.";
						}
					} // End if($pay_plan != 'P'){}else{}
					
				//큐빅예약의 경우 Free Reservation with cubic
				}elseif($reservation_kinds == 'K'){
					$cancelMsg .= '<SPAN class=sky_b>취소하신 후 예약을 </SPAN>다시 하실 수 있습니다..';
							
				// Maybe on this else can provide Cancel Message for In case of Management Reservation Fixed student
				}else{
					// This function accept other reservation_kinds FIXED SCHEDULE
					// Not include the reservation_kinds M,MD,M2,M3 were already used
					$OtherCancelReservationKinds=$this->Programmer1Handler->OtherCancelReservationKinds();
					$this->set('OtherCancelReservationKinds',$OtherCancelReservationKinds);

					//In case of Management Reservation Fixed student
					if(in_array($reservation_kinds, array('M','MD'))){
						$lesson_cancel_count = $numberCancelled;
						if($time_difference06 > 0 && $time_difference < 0){
						$weekday_extend_button = 'N'; //weekdays extend button disable
						$cancelMsg .= "<SPAN class=sky_b>일괄 예약의 경우에는  수업시간 $time_string[6]시간 안에 취소한
						수업은 결석처리가 되지만 5포인트를 적립해드립니다. 25분 한 수업의 경우  10포인트이며 포인트를 이용해서
						언제든지 원하시는 선생님 원하는 시간을 고객님이 직접 예약할 수 있습니다." ;
						//6hours before lesson time 10point restore
						}else{
						$cancelMsg .= "<SPAN class=sky_b>일괄 예약의 경우에는  수업시간 $time_string[6]시간 이전에 취소한
								수업은  <span style=\"color: #FF0000;\">{$point_deduction}포인트</span>를 적립해드립니다. 수업취소로 획득하신 포인트는 동안  수업기간중에만
								쓰실 수 있습니다. 일괄예약의 경우 취소는 한달에 5번을 최대로 합니다.<br>";
								if (empty($point_deduction_confirm) || $point_deduction_confirm == false) {
								$cancelMsg .= "<br><SPAN class=sky_b>또는  현재의 수업시간 <font color=\"#FF0000\">$reserve_time 을 취소</font>하고
								<font color=\"#FF0000\">$extend_day 수업이 추가</font> 됩니다. 연장수업은 토요일 일요일 제외한  평일에 자동으로 배치됩니다.<br>";
						}
						$cancelMsg .= "<SPAN class=sky_b>현재 고객님은  수업기간 중 <font color='#FF0000'><b>$lesson_cancel_count</b> 번</font>
						고정수업을 취소하셨습니다.한달 $numOfCancelDefault 번의 고정 수업 취소기준을 주의부탁드립니다.";
						}
					}elseif(in_array($reservation_kinds, array('M2','M3'))){
						$new_last_reserve_time = "$reserve_time 이 연장되어 $new_last_reserve_time 수업이 추가 됩니다.";
						$this->set("new_last_reserve_time", $new_last_reserve_time);
						if($time_difference06 > 0 && $time_difference < 0){
							$weekday_extend_button = 'N'; //weekdays extend button disable
							$cancelMsg .= "<SPAN class=sky_b>일괄 예약의 경우에는  수업시간 $time_string[6]시간 안에 취소한
							수업은 결석처리가 되지만 5포인트를 적립해드립니다. 25분 한 수업의 경우  10포인트이며 포인트를 이용해서
							언제든지 원하시는 선생님 원하는 시간을 고객님이 직접 예약할 수 있습니다." ;
						} else {
							$lesson_cancel_count = $numberCancelled;
							$cancelMsg .= "<br><SPAN class=sky_b>현재 고객님은  수업기간 중 <font color='#FF0000'><b>$lesson_cancel_count</b> 번</font>
							고정수업을 취소하셨습니다.한달 $numOfCancelDefault 번의 고정 수업 취소기준을 주의부탁드립니다.";
						}
						
					// All reservation_kinds depends to $OtherCancelReservationKinds
					}elseif(in_array($reservation_kinds,$OtherCancelReservationKinds)){
						// if(in_array($reservation_kinds,array('TTEM3','TMEM3'))){
							$new_last_reserve_time = "$reserve_time 이 연장되어 $new_last_reserve_time 수업이 추가 됩니다.";
							$this->set("new_last_reserve_time", $new_last_reserve_time);
						// }
						
						$lesson_cancel_count = $numberCancelled;
						if($time_difference06 > 0 && $time_difference < 0){
							$weekday_extend_button = 'N'; //weekdays extend button disable
							$cancelMsg .= "<SPAN class=sky_b>일괄 예약의 경우에는  수업시간 $time_string[6]시간 안에 취소한
							수업은 결석처리가 되지만 5포인트를 적립해드립니다. 25분 한 수업의 경우  10포인트이며 포인트를 이용해서
							언제든지 원하시는 선생님 원하는 시간을 고객님이 직접 예약할 수 있습니다." ;
							//6hours before lesson time 10point restore
						}else{
							$cancelMsg .= "<SPAN class=sky_b>일괄 예약의 경우에는  수업시간 $time_string[6]시간 이전에 취소한
							수업은  <span style=\"color: #FF0000;\">10포인트</span>를 적립해드립니다. 수업취소로 획득하신 포인트는 동안  수업기간중에만
							쓰실 수 있습니다. 일괄예약의 경우 취소는 한달에 5번을 최대로 합니다.<br>";
							if (empty($point_deduction_confirm) || $point_deduction_confirm == false) {
								$cancelMsg .= "<br><SPAN class=sky_b>또는  현재의 수업시간 <font color=\"#FF0000\">$reserve_time 을 취소</font>하고
								<font color=\"#FF0000\">$extend_day 수업이 추가</font> 됩니다. 연장수업은 토요일 일요일 제외한  평일에 자동으로 배치됩니다.<br>";
							}
							$cancelMsg .= "<SPAN class=sky_b>현재 고객님은  수업기간 중 <font color='#FF0000'><b>$lesson_cancel_count</b> 번</font>
							고정수업을 취소하셨습니다.한달 $numOfCancelDefault 번의 고정 수업 취소기준을 주의부탁드립니다.";
						}
					} // End if(in_array($reservation_kinds, array('M2','M3'))){}else{}
				} // End if($reservation_kinds == 'P' && $pay_plan != 'M'){}elseif($reservation_kinds == 'K'){}else{}
				$cancelMsg .= $gLCWKHoliday['holidayMessage'];
			}else{
				$cancelMsg .= '<SPAN class=sky_b>수업시간 한시간 이전의 취소는결석처리됩니다.</SPAN>';
			} // End if($time_difference < 0){}else{}
			
			
			$update_lesson=$results['Lesson'];
			
			$this->set('cancelMsg', $cancelMsg);
			$this->set('lesson', $update_lesson);
			$this->set('weekday_extend_button', $weekday_extend_button);
			
		}else{} // End if(isset($this->params['form']['cancel_point'])){}else{}

		$this->set('lesson_id',$lesson_id);
	} // End function lesson_cancel_1($lesson_id_get = null){}

//レッソンのCANCEL DB処理
function lesson_cancel_ok ($lesson_id = null) {
 global $const; 
 $this->set('const', $const);
 $loginInfo = $this->Session->read('loginInfo');
	
	$point_deduction = 10; // as Default point deduction;
	if(isset($this->params['form']['point_deduction'])){
		$point_deduction = $this->params['form']['point_deduction'];
	}
	if(isset($this->params['form']['point_deduction_confirm'])
			&& $this->params['form']['point_deduction_confirm'] == true){
		$point_deduction = $this->params['form']['point_deduction'];
	} // End if(isset($this->params['form']['point_deduction_confirm'])
		// && $this->params['form']['point_deduction_confirm'] == true){}
	
 $pay_plan = $loginInfo['User']['pay_plan'];
 $user_id = $loginInfo['User']['id'];
	
// 2011-08-23 fixed flag setting
 $types = $loginInfo['User']['types'];
 $message = '';
// LessonテーブルのレッスンステータスをUPDATEする
// 予約(0)→CANCEL(2)
	
 $lesson_id = $this->params['form']['lesson_id'];
 $cancel_type = $this->params['form']['type'];

 $where_string = "Lesson.id = $lesson_id ";
 $lessons = $this->Lesson->lesson_info($where_string);
	
// レッスン情報から一行目の情報を取る
 $lesson = query_to_model($lessons,"Lesson", 0);
	
// 2010-06-10 deleted
// $cancelOK = $this->ModelHandler->judgeCancelOK($loginInfo,$lesson_id,'Lesson');
	
// 2010-06-09 decide cancel lesson
 $results = $this->ModelDefaultHandler->select_lessons($lesson_id,'Lesson');
 $lesson_model = $results['Lesson']; 
 $reservation_kinds = $lesson_model['reservation_kinds'];
 $lesson_id2 = $lesson_model['id'];
	
// 2014-05-15 17:03 levi aviles
 $payment_id = $lesson_model['payment_id'];
	
// 2012-01-08
 $reserve_time = $lesson_model['reserve_time'];
	
// 2011-05-10 lesson
 $teacher_id = $lesson_model['teacher_id'];
	
// Get a teacher name
 $this->loadModel('Teacher');

 $teacher = $this->Teacher->findById($teacher_id); 
 $teacher_name = $teacher['Teacher']['tea_name'];
	
// Load Teacher Model to get Teacher Name
// $this->loadModel('Teacher');
	
// 1hour before 
 $time_difference = (time()+ 60*60*1) -(strtotime($lesson_model['reserve_time']));
	
// 24hour before 2011-01-16 start
 $time_difference24 = (time()+ 60*60*24) -(strtotime($lesson_model['reserve_time']));
// 24hour before 2011-01-28 start
// Point plan 12hours
 $time_difference12 = (time()+ 60*60*12) -(strtotime($lesson_model['reserve_time']));
	
// Point plan change from 06hours -> lesson_time
 $time_difference06 = (time()+ 60*60*6) -(strtotime($lesson_model['reserve_time']));
	
 $spent_point = 0;
 $reservation_sentence = '';

 if ($reservation_kinds == 'K') {
  $spent_point = 0;
  $reservation_sentence = '큐빅예약';
 } elseif ($reservation_kinds == 'M') {
//  2011-11-01 modified 
//  In case of cancellation , back to point 5 or 10
//  $spent_point = 10; from original status
  $spent_point = $point_deduction;
  $reservation_sentence = '일괄예약';	
 } elseif (in_array($reservation_kinds,array('M3','M2'))) {
//  In case of cancellation , back to point 5 or 10
//  $spent_point = 10; from original status
  $spent_point = $point_deduction;
  $reservation_sentence = '일괄예약';	
 } elseif ($reservation_kinds == $const['P']) {
  $spent_point = $lesson['spend_point'] ;
  $reservation_sentence = '포인트예약';
 } elseif ($reservation_kinds == 'MD') {
//  2011-11-01 modified 
//  In case of cancellation , back to point 5 or 10
  $spent_point = $point_deduction;
  $reservation_sentence = '낮시간 할인';	
 } elseif ($reservation_kinds == 'T') {
  $spent_point = 10;
  $reservation_sentence = '체험수업';	
 } // End if ($reservation_kinds == 'K');
	
// Spend point
// if pay plan is M , E1,E2,E3,E4
 if ($reservation_sentence == '포인트예약' || $reservation_sentence == '일괄예약' || $reservation_sentence = '체험수업') {
// If it is trial lesson , do not anything 10point restore
  if ($pay_plan == 'M') {
//   If User is E1,E2,E3,E4, within 0hours reservation time < 12 hour 	
  } else {
//   12hours
   if ($time_difference06 > 0 && $time_difference < 0) {
    $spent_point = $spent_point * 0.5;
   }
//   2012-01-08 cancel type M(extend class after last class)
   if ($cancel_type == 'M') {
    $spent_point = 0;
   }
  } // End if ($pay_plan == 'M');
 } // End if ($reservation_sentence == '포인트예약' || $reservation_sentence == '일괄예약' || $reservation_sentence = '체험수업');
	
	
// K,P,M reservation common
// $time_difference one hour before lesson time
	
 if ($time_difference < 0) {
//  ポイントテーブルを件数別インサートする
//  仕様変更g4_pointテーブルにインサートする
  $ct = get_time();	//現在の時間を求める
  $current_time = $ct['time_ymdhis'];
		  
  $content =  $lesson['reserve_time']." ".$reservation_sentence.$teacher_name;
		  
//  2012-01-08 cancell type
  if ($cancel_type == 'M') {
   $content .= " 일괄예약 수업연장";
  } else {
   $content .= " 수업취소";
  } // End if ($cancel_type == 'M');
		  
  $lesson_id = $lesson['id'];
  $loginInfo = $this->Session->read('loginInfo');
  $mb_id = $loginInfo['User']['mb_id'];
  $nick_name = $loginInfo['User']['nick_name'];
  $user_last_day = $loginInfo['User']['last_day'];
		  
//  2010-11-24 cheking the already have the same records
//  there are some bugs sometimes multiful records are inserted
//  I am not sure it is effective or not anyway check the redundunt check 
//  with mb_id and current_time
		  
  $search_conditions['mb_id'] = $mb_id;
  $search_conditions['po_datetime'] = $current_time;

//  point count
  $point_counts = $this->ModelDefaultHandler->getDefaultPointCount($lesson_id,'Lesson');  
  $point_count = $point_counts[0][0]['point_count'];
		  
//  check vaild lesson
  $search_conditions['user_id'] = $user_id;
  $search_conditions['reserve_time'] = $lesson_model['reserve_time'];
  $search_conditions['lesson_status'] = 'R';  

//  check if it is valid lesson or not
  $lesson_counts = $this->ModelDefaultHandler->getDefaultLessonCount($search_conditions,'Lesson');     
  $lesson_count = $lesson_counts[0][0]['lesson_count'];
		
//  CUBIC , POINT , BY MANAGER is same logic 
//  pr($lesson_count);
  if ($lesson_count <= 0) {
//   already cancelled class do nothing
   $message='<SPAN class=sky_b>^^이미 수강취소 하신 수업이십니다.^^!';
  } else { 
   if ($point_count == 0) {
    $sql = " insert into g4_point 
             set mb_id = '$mb_id',
             po_datetime = '$current_time',
             po_content = '$content',
             po_point = '$spent_point',
             po_rel_table = 'lessons',
             po_rel_id = $lesson_id,
             po_rel_action = '수업취소' ";
				      
    $free_sql_result = $this->Lesson->free_sql($sql);
					 
//    ポイントはg4_pointからSUMする
    $select_string = "sum(po_point) AS point ";
    $table = "g4_point ";
    $where_string =  "mb_id = '$mb_id' ";
					
    $point_results = $this->Lesson->select_free_common($select_string,$table,$where_string);
				    
    foreach ($point_results as $point_result) {
     $point = $point_result[0]['point'];
    } // End foreach ($point_results as $point_result);
			    
//    g4_member UPDATEする
    $sql = "update g4_member
            SET mb_point = $point 
            WHERE mb_id = '$mb_id'";
    $this->Lesson->free_sql($sql);
				
//    セッションのユーザポイントをUPDATEする
    $loginInfo['User']['points'] = $point;
    $this->Session->write('loginInfo',$loginInfo);
   } // End if ($point_count == 0);
		
//   2012-01-08 add for extend class
//   Auto matically extention
   if ($cancel_type == 'M' && ($reservation_kinds == 'M' || $reservation_kinds == 'MD')) {
			
//   1.Find the last class
//   change for user for user get a lesson more than one time find the same tiem
    $reserve_only_time =  date("H:i:s", strtotime($reserve_time));
//    transfer data &reservation_kinds to $reservation_kinds1
    $reservation_kinds1 = '';
					
	if ($reservation_kinds == 'M') {
    	$reservation_kinds1 = 'M';
    } elseif ($reservation_kinds == 'MD') {
		$reservation_kinds1 = 'MD';
    } // End if ($reservation_kinds == 'M');
					

    //$last_lesson_time = $last_lesson['Lesson']['reserve_time'];
    
    $reserve_only_time=date('H:i:s',strtotime($reserve_time));
    $time_selected=$reserve_only_time;
    if(isset($lesson_model['class_minutes']) && $lesson_model['class_minutes'] > 0){
    	$minutes_class=$lesson_model['class_minutes'];
    }else $minutes_class=$this->Programmer1Handler->minutes_class;
    
    $last_lesson = $this->Lesson->find('first',array('fields'=>array('Lesson.id','Lesson.payment_id','Lesson.user_id','Lesson.teacher_id','Lesson.reserve_time'
    		,'Lesson.reservation_kinds'),'conditions'=>array('Lesson.user_id'=>$user_id,'Lesson.lesson_status'=>array('R')
    				,'Lesson.reservation_kinds'=>$reservation_kinds,'TIME(Lesson.reserve_time) = '=>"$reserve_only_time"),'order'=>'Lesson.reserve_time DESC'));
    $extend_only_day = date('Y-m-d',strtotime($last_lesson['Lesson']['reserve_time'] . '+1 day'));
    $search_date = $extend_only_day;
    
    $TimeCondition = $this->Programmer1Handler->TimeCondition($search_date,$time_selected,$minutes_class);
    $s_timed = $time_selected;
    $s_reserve_time_from = $TimeCondition['reserve_time_from'];
    $s_reserve_time_until = $TimeCondition['reserve_time_until'];
    $s_reserve_time25M = $TimeCondition['reserve_time25M'];
    $class_minutes_cond = $TimeCondition['class_minutes_cond'];
    
    $ContractDays = $this->Programmer1Handler->PaymentIdOnContractDays($payment_id,$reservation_kinds,$time_selected);
    
    $insert_lessons=array();
    $search_start_day = date('Y-m-d',strtotime($s_reserve_time_from)) . ' ' . $s_timed;
    $ko_datetime=$search_start_day;
    $currenttime=date('Y-m-d H:i:s');
    $this->loadModel('Available_lesson');
    $this->Available_lesson->unbindModel(array('belongsTo'=>array('Teacher')));
    
    $check=true;
					
//    2.insert class after last class + oneday
//    fixed student is no sat sunday class so check the satday or sunday
    $increasing_day = 1;
//    0 is sunday and 6 is saturday
    $check = true;

    while ($check == true) {
    	if(in_array(date('w', strtotime($s_reserve_time_from)),$ContractDays)){
    		$user_condition=array(array('Lesson.teacher_id'=>$teacher_id));
    		// This is COUNT the lesson exist;
    		$emp = $this->Programmer1Handler->findLessonExist($search_date,$s_timed,$s_reserve_time_from,$s_reserve_time_until,$s_reserve_time25M
    				,$class_minutes_cond,$teacher_id,$user_id,$user_condition);
    		// If $emp totalled more than 0
    		// then go and find other day(+1)
    		if($emp > 0){
    		}else{
    			$currenttime=date('Y-m-d H:i:s',strtotime($currenttime . '+3 seconds'));
    			$search_start_day = date('Y-m-d',strtotime($s_reserve_time_from)) . ' ' . $s_timed;
    			$ko_datetime=$search_start_day;
    			$insert_lessons=$this->Programmer1Handler->InsertAvailableLesson(false,$insert_lessons,$s_timed,$s_reserve_time_from,$s_reserve_time_until
    					,$s_reserve_time25M,$class_minutes_cond,$ko_datetime,$minutes_class,$teacher_id,$user_id,$payment_id,$reservation_kinds,$currenttime);
    			// $this->Available_lesson->deleteAll(array('available_time'=>$reserve_time,'teacher_id'=>$teacher_id),false);
    				
    			// Insert Lesson Class Multiple
    			$total_insert_lessons = count($insert_lessons);
    			if($total_insert_lessons > 0){
    				$insert_lessons[$total_insert_lessons-=1]['spend_point']=$lesson_model['spend_point'];
    				$this->Programmer1Handler->InsertLesson($insert_lessons);
    			} // End if($insertToDb == true){}
    				
    			// WOOOOHHOOOO
    			$extend_day=$ko_datetime;
    			$new_last_reserve_time=$ko_datetime;
    			// payment last_day update
    			$payment_update = array();
    			if(!empty($payment_id) && strlen ($payment_id) > 0){
    				$this->loadModel('Payment');
    				$payment_update['id'] = $payment_id;
    				$payment_update['last_day'] = date('Y-m-d 00:00:00', strtotime($extend_day));
    				$this->Payment->save($payment_update);
    			} // End if (!empty($payment_id) && strlen ($payment_id) > 0);
    	
    			//    extensions table;
    			$insert_conditions = array();
    			$insert_conditions['user_id'] = $user_id;
    			$insert_conditions['lesson_id'] = $lesson_id;
    			$insert_conditions['pay_plan'] = $pay_plan;
    			$insert_conditions['reservation_kinds'] = $reservation_kinds;
    			$insert_conditions['user_nick_name'] = $nick_name;
    			$insert_conditions['teacher_name'] = $teacher_name;
    			$insert_conditions['reserve_time'] = $reserve_time;
    			$insert_conditions['reason'] = "EC";
    			$insert_conditions['description'] = $reserve_time . "," . $new_last_reserve_time;
    			$insert_conditions['tarket_day'] = $new_last_reserve_time;
    			$insert_conditions['extend_day'] = date('Y-m-d H:i:s', strtotime(date('Y-m-d', strtotime($new_last_reserve_time))));
    			$insert_conditions['last_day'] = $user_last_day;
    			$this->ModelDefaultHandler->insertExtention($insert_conditions,'Lesson');
    	
    			// Update User last day
    			$this->loadModel('User');
    			$userInfo = $this->User->find('first',array('fields'=>array('id','last_day'),'conditions'=>array('id'=>$loginInfo['User']['id'])));
    			// if last_day is less than extended day
    			if(strtotime($extend_day) > strtotime($userInfo['User']['last_day'])){
    				$this->User->updateAll(array('last_day'=>'"'.$extend_day.'"','modified'=>'"'.date('Y-m-d H:i:s').'"'),array('id'=>$loginInfo['User']['id']));
    			}
    			$message = "<SPAN class=sky_b> $reserve_time.'이 연장되어 '. $extend_day. '수업이 추가 되었습니다.'</SPAN>";
    	
    			// Mail System
    			$this->requestAction('/phpmailers/mail/',array('form'=>array('post'=>array('Lesson'=>array('id'=>$lesson_id),'mail_type'=>'Extend'))));
    				
    			// get the lesson cancelled that reserve_time has in the korean_holidays
    			$getLessonCancelWithKoreanHoliday = array('reserve_time'=>$reserve_time,'payment_id'=>$payment_id,'reservation_kinds'=>$reservation_kinds);
    			// $gLCWKHoliday means getLessonCancelWithKoreanHoliday return array;
    			$gLCWKHoliday = $this->Programmer1Handler->getLessonCancelWithKoreanHoliday($getLessonCancelWithKoreanHoliday, true);
    	
    			$message .= $gLCWKHoliday['holidayMessage'];
    				
    			$check=false;
    		} // End if($emp > 0){}else{}
    	}
    	$s_reserve_time_from = date('Y-m-d H:i:s',strtotime($s_reserve_time_from . '+1 day'));
    	$s_reserve_time_until = date('Y-m-d H:i:s',strtotime($s_reserve_time_until . '+1 day'));
    	$s_reserve_time25M = date('Y-m-d H:i:s',strtotime($s_reserve_time25M . '+1 day'));
    } // End while ($check == true);

   } // End if ($cancel_type == 'M' && ($reservation_kinds == 'M' || $reservation_kinds == 'MD')); automatically extend end
			
//   ALL LOGIC is common
   if ($time_difference < 0) {
//    updateしたい項目をセットする(lesson　テーブル)
    $update_common['id'] = $lesson['id'];
				
//    2012-12-22 YHLEE MODIFIED
    if ($cancel_type == 'P') {
     $update_common['lesson_status'] = 'CP';
    } else {
     $update_common['lesson_status'] = 'C';
    } // End if ($cancel_type == 'P');
				
    $update_common['modified'] = $ct['time_ymdhis'];
    $lessons = $this->Lesson->update_common('lessons',$update_common);

    if ($this->Programmer1Handler->checkAbsent($teacher_id, $reserve_time) < 1) {
     //updateしたい項目をセットする(available_lesson　テーブル)
     //$update_common = null;
     //$update_common['id'] = $lesson['available_lesson_id'];
     //$update_common['reserve_status'] = 'R';
     //$lessons = $this->Lesson->update_common('available_lessons',$update_common);
     //UPDATE available_lessons SET reserve_status = 'R' WHERE id = 1
     $this->loadModel('Available_lesson');
     $this->data['Available_lesson']['id'] = $lesson['available_lesson_id'];
     $this->data['Available_lesson']['reserve_status'] = 'R';
     $result = $this->Available_lesson->save($this->data);
    }else{
    	$this->loadModel('Available_lesson');
		$this->Available_lesson->deleteAll(array('id'=>$lesson['available_lesson_id']),false);
	} // End if($this->Programmer1Handler->checkAbsent($teacher_id, $reserve_time) < 1){}else{}
				
//    2011-05-10 START
   } else {
//    updateしたい項目をセットする(lesson　テーブル)
    $update_common['id'] = $lesson['id'];
    $update_common['lesson_status'] = 'A';
    $update_common['modified'] = $ct['time_ymdhis'];
    $lessons = $this->Lesson->update_common('lessons',$update_common);
				
//    updateしたい項目をセットする(available_lesson　テーブル)
    $update_common = null;
    $update_common['id'] = $lesson['available_lesson_id'];
    $update_common['reserve_status'] = 'F';
    $lessons = $this->Lesson->update_common('available_lessons',$update_common);
   } // End if ($time_difference < 0);
   
   $this->LanguageHandler->infocancelation($lesson_id, $cancel_type);
   
  } // End if ($lesson_count <= 0); $lesson_count > 0 that time lesson_status = 'R' 
 } // End if ($time_difference < 0); $time_difference < 0 before reservation time
 $this->set('message', $message);	
} // End function lesson_cancel_ok ($lesson_id = null);

function rstatus() {
	if ($this->RequestHandler->isAjax()) {
	$loginInfo = $this->Session->read('loginInfo');
	Configure::write('debug',0);
	$this->loadModel('Complain');
		$_gunread = $this->Complain->find('all',array(
					'conditions' => array(
							'Complain.user_id' => $loginInfo['User']['id'],
							'Complain.r_status' => 25),
					'group' => array('lesson_id')
													));
		if (count($_gunread) > 0) {
			// $_Count-Group-Un-Read-Item
			$_cguri = 0;
			foreach ($_gunread as $list) {
				$_hold = $list['Complain']['lesson_id'];
				$strRemove = array("-",":"," ");
				$c_guri[$_cguri] = str_replace($strRemove, '_', $_hold);
				$_cunread = $this->Complain->find('count', array(
									'conditions' => array(
											'Complain.user_id' => $loginInfo['User']['id'],
											'Complain.lesson_id' => $list['Complain']['lesson_id'],
											'Complain.r_status' => 25
										)));
				$abc[$c_guri[$_cguri]] = $_cunread;
				$_cguri++;			
			}
			$result = json_encode($abc);
			echo $result;
		} else { return false;}
	}
}

function evaluate()
{
	
}

function view (){
	if ($this->RequestHandler->isAjax()) {
		Configure::write('debug',0);
		$loginInfo = $this->Session->read('loginInfo');
		$StudentId = $loginInfo['User']['id'];
		$lesson_id = $this->params['form']['id'];
		$_fdb_teacher  = $this->Lesson->query("SELECT * FROM lessons as Lesson LEFT JOIN comments as Comment ON (Comment.lesson_id = Lesson.id) LEFT JOIN teachers as Teacher ON (Teacher.id = Lesson.teacher_id) WHERE Lesson.id = '$lesson_id'");
		$_fdb_comments = $this->Lesson->query("SELECT * FROM lessons as Lesson LEFT JOIN comments as Comment ON (Comment.lesson_id = Lesson.id) LEFT JOIN teachers as Teacher ON (Teacher.id = Lesson.teacher_id) WHERE Lesson.id = '$lesson_id' AND Comment.t_t_comment != '' ");
		//$_content_coment_page  = '<div id="db'.$_fdb_comments[0]['Lesson']['id'].'" class="divback" onclick="document.getElementById('."'db".$_fdb_comments[0]['Lesson']['id']."'".').style.display = '."'none'".'; document.getElementById('."'df".$_fdb_comments[0]['Lesson']['id']."'".').style.display = '."'none'".'; return false;" align="center"></div>';
		//$_content_coment_page .= '<div id="db'.$_fdb_comments[0]['Lesson']['id'].'" class="divfront" style="position:absolute;z-index:9999;left:35%;">';
			
	if (count($_fdb_comments) > 0) {
			$_content_coment_page  = '<table border="0" style="border-collapse:collapse;" cellpadding="0" cellspacing="0" width="100%">';
			$_content_coment_page .= '<tr><th colspan="2" valign="top" align="left"><div style="padding:0 5px 0 5px;letter-spacing:2px;"><font color="#006600" face="arial" size="5"><b>강사: '.$_fdb_teacher[0]['Teacher']['tea_name']."&#32;(".change_date_format($_fdb_teacher[0]['Lesson']['reserve_time'] , 'OVERDAY').")".'</b></font></div></th><th colspan="2" valign="top" align="left"></th></tr>';
			$_content_coment_page .= '<tr><th colspan="2" valign="top" align="left" style="height:10px;"></th><th colspan="2" valign="top" align="left" style="height:10px;"></th></tr>';
			$_content_coment_page .= '<tr><th valign="top" align="left">';
			$_content_coment_page .= '<TABLE border="0" style="cellSpacing:1;cellPadding:5;width:549px;">';
			
			$_content_coment_page .= '<TR>';
			$_content_coment_page .= '<TD align="left">';
			$_content_coment_page .= '<div align="left" style="padding:0 5px 0 2px;"><div align="left" class="lesson_comment"><font><b>TEACHING MATERIAL</b></font></div>';
			$_content_coment_page .= '<div align="left"><TEXTAREA class="txtarea_comment" rows="8" cols="64">'.stripslashes($_fdb_comments[0]['Comment'][t_matrial]).'</TEXTAREA></div></div>';
			$_content_coment_page .= '</TD>';
			$_content_coment_page .= '</TR>';
			
			$_content_coment_page .= '<TR>';
			$_content_coment_page .= '<TD align="left">';
			$_content_coment_page .= '<div align="left" style="padding:0 5px 0 2px;"><div align="left" class="lesson_comment"><font><b>COMMENT FOR STUDENT</b></font></div>';
			$_content_coment_page .= '<div align="left"><TEXTAREA class="txtarea_comment" rows="8" cols="64">'.stripslashes($_fdb_comments[0]['Comment'][t_comment]).'</TEXTAREA></div></div>';
			$_content_coment_page .= '</TD>';
			$_content_coment_page .= '</TR>';
			
			$_content_coment_page .= '<TR>';
			$_content_coment_page .= '<TD align="left">';
			$_content_coment_page .= '<div align="left" style="padding:0 5px 0 2px;"><div align="left" class="lesson_comment"><font><b>SKYPE MESSAGE</b></font></div>';
			$_content_coment_page .= '<div align="left"><TEXTAREA class="txtarea_comment" rows="8" cols="64">'.stripslashes($_fdb_comments[0]['Comment'][t_t_comment]).'</TEXTAREA></div></div>';
			$_content_coment_page .= '</TD>';
			$_content_coment_page .= '</TR>';
			
			$_content_coment_page .= '</TABLE>';
			
			$_content_coment_page .= '</th><th style="width:10px;"></th>';
			$_content_coment_page .= '</th><th style="width:10px;"></th>';
			$_content_coment_page .= '</th><th valign="top" align="left">';
			
			$_content_coment_page .= '<TABLE border="0" style="cellSpacing:1;cellPadding:5;width:549px;">';
			
			$_content_coment_page .= '<TR>';
			$_content_coment_page .= '<TD align="left">';
			$_content_coment_page .= '<div align="left" style="padding:0 5px 0 2px;"><div align="left" class="lesson_comment"><font><b>VOCABULARY AND IDIOMATIC EXPRESSION[S]</b></font></div>';
			$_content_coment_page .= '<div align="left"><TEXTAREA class="txtarea_comment" rows="8" cols="64">'.stripslashes($_fdb_comments[0]['Comment'][t_vocabulary]).'</TEXTAREA></div></div>';
			$_content_coment_page .= '</TD>';
			$_content_coment_page .= '</TR>';
			
			$_content_coment_page .= '<TR>';
			$_content_coment_page .= '<TD align="left">';
			$_content_coment_page .= '<div align="left" style="padding:0 5px 0 2px;"><div align="left" class="lesson_comment"><font><b>ENGLISH PATTERNS or GRAMMAR</b></font></div>';
			$_content_coment_page .= '<div align="left"><TEXTAREA class="txtarea_comment" rows="8" cols="64">'.stripslashes($_fdb_comments[0]['Comment'][t_idioms]).'</TEXTAREA></div></div>';
			$_content_coment_page .= '</TD>';
			$_content_coment_page .= '</TR>';
			
			$_content_coment_page .= '<TR>';
			$_content_coment_page .= '<TD align="left">';
			$_content_coment_page .= '<div align="left" style="padding:0 5px 0 2px;"><div align="left" class="lesson_comment"><font><b>STUDENT MISTAKES (Mispronunciation)</b></font></div>';
			$_content_coment_page .= '<div align="left"><TEXTAREA class="txtarea_comment" rows="8" cols="64">'.stripslashes($_fdb_comments[0]['Comment']["t_pattern"]).'</TEXTAREA></div></div>';
			$_content_coment_page .= '</TD>';
			$_content_coment_page .= '</TR>';
			
			$_content_coment_page .= '</TABLE>';
			
			$_content_coment_page .= '</th></tr>';
			
			$_content_coment_page .= '</table>';
			
		} else {
			$_content_coment_page  = '<div class="close no-border-radius" data-dismiss="modal" aria-hidden="true" align="right"></div>';
			$_content_coment_page .= '<div style="height:30px;"></div><div align="center" style="padding:10px;"><img id="img-result" border="0" src="'.c_path().'/image/no-result-found-lesson-comments.png"></div>';
		}
			
		echo $_content_coment_page;
	}
}

	function page($user_id = null,$today = null, $alkinds = null, $arkinds = null){
		$_totalHomework = $this->Lesson->query(
										"
										SELECT
										count(*) AS COUNT 
										FROM lessons as Lesson
										WHERE Lesson.user_id = '$user_id'
										AND Lesson.reserve_time < '$today'
										AND Lesson.lesson_status IN ($alkinds)
										AND Lesson.reservation_kinds IN ($arkinds)
										");
		$count = $_totalHomework[0][0]['COUNT'];
		$_pages = ceil($count / 10);
		return $_pages;
	}

	function changeskypeid(){
		$loginInfo = $this->Session->read('loginInfo');
		
		if ($this->params['form']['mb_id'] == $loginInfo['User']['mb_id']) {
			$id = $this->params['form']['id'];
			$skype_id = $this->params['form']['user_id'];
			$field = $this->params['form']['field'];
			$this->Lesson->query("UPDATE users SET $field = '$skype_id' WHERE id = $id");
			
			$mb_id = $this->params['form']['mb_id'];
			$this->redirect(array('controller' => 'users', 'action' => 'login/mypage/'.$mb_id));
		} else 
			$this->redirect(array('controller' => 'users', 'action' => 'login/mypage/'.$loginInfo['User']['mb_id']));
		return false;
	}
	
	function room () {
		//$loginInfo = $this->Session->read('loginInfo');
		$this->set("loginInfo", $this->Session->read('loginInfo'));
	}

//クラス最後の｝
}

?>
