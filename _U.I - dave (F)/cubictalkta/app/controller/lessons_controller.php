<?php
class LessonsController extends AppController {

	var $name = 'Lessons';  
	var $helpers = array('Html','Ajax','Javascript','Form');
	var $components = array("ModelHandler","CheckHandler","ModelDefaultHandler","CustomHandler","RequestHandler","FixedscheduleHandler","Programmer1Handler");
	var $lesson_status = array('R','F','A','E','P','S','M','L','T');
	function beforeFilter(){
		//2011-11-12 ADD
		checkLoginInfo($this);
	}
	
	// 
	function reCreateNewContractDays($payment_id,$teacher_id,$user_info,$time_selected)
	{
		// FILTER WEEKDAYS
		
		// BASE PAYMENT ID PLAN
		$ContractDays = $this->Programmer1Handler->PaymentIdOnContractDays($payment_id,'',$time_selected);
		
		// IF THE USER IS DISTRIBUTOR
		if(isset($user_info['User'])
				&& $user_info['User']['distributor_id'] > 0){
			$this->loadModel('SpecialUser');
			$special_user_info = $this->SpecialUser->find('first',array(
					'fields'=>array('id')
					,'conditions'=>array(
							'active > '=>1
							,'teacher_id'=>$teacher_id
							,'user_id'=>$user_info['User']['id']
							,'time'=>"{$time_selected}"
					)
						
			));
			if(isset($special_user_info['SpecialUser']['id'])){
				$this->loadModel('SpecialUserDay');
				$special_user_day_info = $this->SpecialUserDay->find('all',array(
						'fields'=>array('*')
						,'conditions'=>array('special_user_id'=>$special_user_info['SpecialUser']['id'])));
				$special_user_day = array();
				foreach ($special_user_day_info as $key_special_user_day_info => $k_sudi)
				{
					$special_user_day[] = $k_sudi['SpecialUserDay']['days_info'];
				}
				$ContractDays = $this->Programmer1Handler->ReNewContractDays($time_selected,$special_user_day);
			}
		}
		return $ContractDays;
	}
	
	function newReserveTime($newReserveTime,$ContractDays)
	{
		$loop = TRUE;
		while($loop)
		{
			$w = date('w',strtotime($newReserveTime));
			if(in_array($w,$ContractDays))
			{
				$loop = FALSE;
			}
			else {
				$newReserveTime = date('Y-m-d H:i:s',strtotime($newReserveTime . '+1 days'));
			}
		}
		return $newReserveTime;
	}
	
	// Important Parameters
	// New Teacher Id
	// Lesson Id
	function createBonusThenExtend($new_teacher_id=null,$lesson_id=null,$confirm=FALSE)
	{
		$loginInfo = $this->Session->read('loginInfo');
		$now = date('Y-m-d H:i:s');
		
		if(isset($this->params['form']['teacher_id'])){
			$new_teacher_id = $this->params['form']['teacher_id'];
		}
		if(isset($this->params['form']['lesson_id'])){
			$lesson_id = $this->params['form']['lesson_id'];
		}
		if(isset($this->params['form']['confirm'])){
			$confirm = $this->params['form']['confirm'];
		}
		
			$this->loadModel('Lesson');
			$lesson_info = $this->Lesson->find('first',array('fields'=>array('*'),'conditions'=>array('id'=>$lesson_id)));
			
			$this->loadModel('Available_lesson');
			$this->Available_lesson->unbindModel(array('belongsTo'=>array('Teacher')));
			$available_lesson_info = $this->Available_lesson->find('first',array('fields'=>array('*'),'conditions'=>array('id'=>$lesson_info['Lesson']['available_lesson_id'])));
			
			$this->loadModel('User');
			$user_info = $this->User->find('first',array('fields'=>array('id','distributor_id','nick_name','name','mb_id','skype_id','pay_plan','last_day'),'conditions'=>array('id'=>$lesson_info['Lesson']['user_id'])));
			
			// Find the Last Class of Teacher with same time
			$time_selected = date('H:i:s',strtotime($lesson_info['Lesson']['reserve_time']));
			
			$last_reserve_time = $this->Lesson->find('first',array(
					'fields'=>array('reserve_time')
					,'conditions'=>array(
							'TIME(reserve_time)'=> "{$time_selected}"
							,'OR'=>array(array('teacher_id'=>$lesson_info['Lesson']['teacher_id']),array('user_id'=>$lesson_info['Lesson']['user_id'])) 
					)
					,'order'=>'reserve_time DESC'
			));
			$ContractDays = $this->reCreateNewContractDays($lesson_info['Lesson']['payment_id'],$lesson_info['Lesson']['teacher_id'],$user_info,$time_selected);
			$newReserveTime = date('Y-m-d H:i:s',strtotime($last_reserve_time['Lesson']['reserve_time'] . '+1 days'));
			$extend_new_reserve_time = $this->newReserveTime($newReserveTime,$ContractDays);
			
			$bonus_new_reserve_time = $lesson_info['Lesson']['reserve_time'];
			
			// If New Substitute Teacher is Really available today
			$loop = TRUE;
			while($loop)
			{
				$total_bonus_new_reserve_time = $this->Lesson->find('count',array(
						'conditions'=>array('teacher_id'=>$new_teacher_id,'reserve_time'=>$bonus_new_reserve_time,'lesson_status'=>array('F','R'))
				));
				
				if(!$total_bonus_new_reserve_time){
					$loop = FALSE;
				}
				else $bonus_new_reserve_time = $this->newReserveTime(date('Y-m-d H:i:s',strtotime($bonus_new_reserve_time . '+1 days')),$ContractDays);
			}
			
			$this->loadModel('Teacher');
			$extend_teacher_info = $this->Teacher->find('first',array('fields'=>array('id','tea_name'),'conditions'=>array('id'=>$lesson_info['Lesson']['teacher_id'])));
			$bonus_teacher_info = $this->Teacher->find('first',array('fields'=>array('id','tea_name'),'conditions'=>array('id'=>$new_teacher_id)));
			
			$this->set('lesson_info',$lesson_info);
			$this->set('user_info',$user_info);
			$this->set('extend_teacher_info',$extend_teacher_info);
			$this->set('extend_new_reserve_time',$extend_new_reserve_time);
			$this->set('bonus_teacher_info',$bonus_teacher_info);
			$this->set('bonus_new_reserve_time',$bonus_new_reserve_time);
			
			if($confirm)
			{
				// Update Lesson Status
				$this->Lesson->updateAll(array('lesson_status'=>"'EM'",'modified'=>"'{$now}'"),array('id'=>$lesson_info['Lesson']['id']));
				
				// Delete Available Lesson
				$this->Available_lesson->delete(array('id'=>$lesson_info['Lesson']['available_lesson_id']));
				
				// Find Extend Available_lesson For a New Teacher
				$this->Available_lesson->unbindModel(array('belongsTo'=>array('Teacher')));
				$old_available_lesson_info = $this->Available_lesson->find('first',array('fields'=>array('*'),'conditions'=>array('teacher_id'=>$new_teacher_id,'available_time'=>"{$bonus_new_reserve_time}",'reserve_status'=>'R')));
				
				if(isset($old_available_lesson_info['Available_lesson']['id']) && count($old_available_lesson_info) > 0) {
					$new_bonus_available_lesson_info = $old_available_lesson_info;
				}
				else {
					// Create Bonus Available_lesson For a New Teacher
					$new_bonus_available_lesson_info = $available_lesson_info['Available_lesson'];
					$new_bonus_available_lesson_info['id'] = null;
					$new_bonus_available_lesson_info['teacher_id'] = $new_teacher_id;
					$new_bonus_available_lesson_info['reserve_status'] = 'F';
					$new_bonus_available_lesson_info['available_time'] = "{$bonus_new_reserve_time}";
					$new_bonus_available_lesson_info['created'] = "{$now}";
					$new_bonus_available_lesson_info['modified'] = "{$now}";
					$this->Available_lesson->save($new_bonus_available_lesson_info);
					
					// Find Bonus Available_lesson For a New Teacher
					$this->Available_lesson->unbindModel(array('belongsTo'=>array('Teacher')));
					$new_bonus_available_lesson_info = $this->Available_lesson->find('first',array(
							'fields'=>array('id')
							,'conditions'=>array(
									'teacher_id'=>$new_bonus_available_lesson_info['teacher_id']
									,'available_time'=>"{$new_bonus_available_lesson_info['available_time']}"
									,'created'=>"{$now}"
									,'modified'=>"{$now}"
					)));
				}
				
				// Create Bonus Lesson For a New Teacher
				$new_bonus_lesson_info = $lesson_info['Lesson'];
				$new_bonus_lesson_info['id'] = null;
				$new_bonus_lesson_info['available_lesson_id'] = $new_bonus_available_lesson_info['Available_lesson']['id'];
				$new_bonus_lesson_info['teacher_id'] = $new_teacher_id;
				$new_bonus_lesson_info['reservation_kinds'] = 'BONUS';
				$new_bonus_lesson_info['reserve_time'] = "{$bonus_new_reserve_time}";
				$new_bonus_lesson_info['created'] = "{$now}";
				$new_bonus_lesson_info['modified'] = "{$now}";
				$this->Lesson->save($new_bonus_lesson_info);
				
				// Find Extend Available_lesson For a Same Teacher
				$this->Available_lesson->unbindModel(array('belongsTo'=>array('Teacher')));
				$old_available_lesson_info = $this->Available_lesson->find('first',array('fields'=>array('*'),'conditions'=>array('teacher_id'=>$lesson_info['Lesson']['teacher_id'],'available_time'=>"{$extend_new_reserve_time}",'reserve_status'=>'R')));
				
				if(isset($old_available_lesson_info['Available_lesson']['id']) && count($old_available_lesson_info) > 0) {
					$new_extend_available_lesson_info = $old_available_lesson_info;
				}
				else {
					// Create Extend Available_lesson With Same Teacher
					$new_extend_available_lesson_info = $available_lesson_info['Available_lesson'];
					$new_extend_available_lesson_info['id'] = null;
					$new_extend_available_lesson_info['reserve_status'] = 'F';
					$new_extend_available_lesson_info['available_time'] = "{$extend_new_reserve_time}";
					$new_extend_available_lesson_info['created'] = "{$now}";
					$new_extend_available_lesson_info['modified'] = "{$now}";
					$this->Available_lesson->save($new_extend_available_lesson_info);
						
					// Find Extend Available_lesson For a Same Teacher
					$this->Available_lesson->unbindModel(array('belongsTo'=>array('Teacher')));
					$new_extend_available_lesson_info = $this->Available_lesson->find('first',array(
							'fields'=>array('id')
							,'conditions'=>array(
									'teacher_id'=>$lesson_info['Lesson']['teacher_id']
									,'available_time'=>"{$new_extend_available_lesson_info['available_time']}"
									,'created'=>"{$now}"
									,'modified'=>"{$now}"
							)));
				}
		
				// Create Extend Lesson With Same Teacher
				$new_extend_lesson_info = $lesson_info['Lesson'];
				$new_extend_lesson_info['id'] = null;
				$new_extend_lesson_info['available_lesson_id'] = $new_extend_available_lesson_info['Available_lesson']['id'];
				$new_extend_lesson_info['reserve_time'] = "{$extend_new_reserve_time}";
				$new_extend_lesson_info['created'] = "{$now}";
				$new_extend_lesson_info['modified'] = "{$now}";
				$this->Lesson->save($new_extend_lesson_info);
				
				$insert_conditions['lesson_id'] = $lesson_info['Lesson']['id'];
				$insert_conditions['pay_plan'] = $user_info['User']['pay_plan'];
				$insert_conditions['reservation_kinds'] = $lesson_info['Lesson']['reservation_kinds'];
				$insert_conditions['teacher_name'] = $extend_teacher_info['Teacher']['tea_name'];
				$insert_conditions['reserve_time'] = $lesson_info['Lesson']['reserve_time'];
				$insert_conditions['reason'] = 'EM'; //Extend By Management
				$insert_conditions['user_id'] = $user_info['User']['id'];
				$insert_conditions['user_nick_name'] = $user_info['User']['nick_name'];
				$insert_conditions['pay_plan'] = $user_info['User']['pay_plan'];
				$insert_conditions['tarket_day'] =  "{$extend_new_reserve_time}";
				$insert_conditions['last_day'] = "{$user_info['User']['last_day']}";
				$insert_conditions['extend_day'] = date('Y-m-d',strtotime($extend_new_reserve_time));
				$insert_conditions['who_extend'] = $loginInfo['id'];
				$insert_conditions['description'] = "{$lesson_info['Lesson']['reserve_time']},{$extend_new_reserve_time}";
				$this->ModelDefaultHandler->insertExtention($insert_conditions,'Lesson');
			}
			
			$this->set('confirm',$confirm);
			Configure::write('debug',0);
	}
	// function createBonusThenExtend($new_teacher_id=null,$lesson_id=null,$confirm=FALSE)
	
	// is to display the Test User Classes
	// a fix reservation with the specific Teacher as
	function testUserClass($user_id=null,$teacher_id=null)
	{
		if(isset($this->params['form']['user_id'])){
			$user_id = array($this->params['form']['user_id']);
			$this->passedArgs['user_id'] = $this->params['form']['user_id'];
		}
		elseif(isset($this->passedArgs['user_id'])){
			$user_id = $this->passedArgs['user_id'];
		}
		else $this->passedArgs['user_id'] = $user_id;
		
		if(isset($this->params['form']['teacher_id'])){
			$teacher_id = array($this->params['form']['teacher_id']);
			$this->passedArgs['teacher_id'] = $this->params['form']['teacher_id'] ;
		}
		elseif(isset($this->passedArgs['teacher_id'])){
			$teacher_id = $this->passedArgs['teacher_id'];
		}
		else $this->passedArgs['teacher_id'] = $teacher_id;
		
		$this->loadModel('Lesson');
		$this->Lesson->bindModel(array('belongsTo'=>array(
			'Teacher'=>array('className'=>'Teacher','type'=>'left','foreignKey'=>false,'conditions'=>'Lesson.teacher_id=Teacher.id')
			,'Payment'=>array('className'=>'Payment','type'=>'left','foreignKey'=>false,'conditions'=>'Lesson.payment_id=Payment.id')
		)),false);
		
		$this->paginate = array(
				'fields'=>array('Lesson.reserve_time','Lesson.lesson_status','Lesson.reservation_kinds','Teacher.tea_name','Payment.goods_name')
				,'conditions'=>array('Lesson.user_id'=>$user_id,'Lesson.teacher_id'=>$teacher_id)
				,'order'=>'Lesson.reserve_time DESC'
				,'limit'=>10
		);
		$testUserClass = $this->paginate('Lesson');
		
		$this->set('testUserClass',$testUserClass);
		$this->set('paginator',$testUserClass);
	}
	// End function testUserClass($user_id=null,$teacher_id=null)

	function lessonsScheduleDelete(){
		if(isset($this->params['form']['lesson_id'])
				&& $this->params['form']['lesson_id'] > 0){
			$this->loadModel('Lesson');
			$lessons = $this->Lesson->find('first',array('fields'=>array('*'),'conditions'=>array('id'=>$this->params['form']['lesson_id'])));
			if(isset($lessons['Lesson']['id'])) {
				$today = date('Y-m-d H:i:s');
				$this->loadModel('Available_lesson');
				// If Available_lesson id already used by another user
				// $Available_lesson = $this->Available_lesson->find('first',array('fields'=>array('*'),'conditions'=>array('id' => $lessons['Lesson']['available_lesson_id'])));

				$Available_lesson['Availabe_lesson']['id'] = $lessons['Lesson']['available_lesson_id'];
				$Available_lesson['Availabe_lesson']['reserve_status'] = 'R'; // Re-open the schedule
				$Available_lesson['Availabe_lesson']['modified'] = "{$today}";

				$this->Available_lesson->save($Available_lesson['Availabe_lesson']);

				$lessons['Lesson']['lesson_status'] = 'D';
				$lessons['Lesson']['modified'] = "{$today}";

				$this->Lesson->save($lessons['Lesson']);

				$this->Lesson->bindModel(array('belongsTo'=>array('User'=>array('className'=>'User','type'=>'left','foreignKey'=>false,'conditions'=>'Lesson.user_id=User.id')
						,'Teacher'=>array('className'=>'Teacher','type'=>'Left','foreignKey'=>false,'conditions'=>'Lesson.teacher_id=Teacher.id'))));
				$lessons = $this->Lesson->find('first',array('fields'=>array(
						'Lesson.id','Lesson.reserve_time','Lesson.modified','User.name','User.nick_name','Teacher.tea_name')
						,'conditions'=>array('Lesson.id'=>$this->params['form']['lesson_id'])));
				$this->set('lessons',$lessons);
			}
		}
	} // End function lessonsScheduleDelete(){}
	
	function teacher_extend_lessons_3(){
		$loginInfo = $this->CheckHandler->checkLoginInfo();
		$message = '';
		$message2 = '';
		$sms_message_content = '';
		
		// Either All id or Single

		$extend_type2 = $this->params['form']['extend_type2'];
		$from_where = $this->params['form']['from_where'];
		$lesson_id = $this->params['form']['lesson_id'];
		$extention_kinds = $this->params['form']['extention_kinds'];

		// find the lesson class by id
		$lessons = $this->Lesson->find('first',array('fields'=>array('*'),'conditions'=>array('id'=>$lesson_id)));
		$paymentMessage='';
		if(isset($lessons['Lesson']['payment_id']) && $lessons['Lesson']['payment_id'] > 0){
			$paymentMessage = $this->Programmer1Handler->PaymentExtend($lessons['Lesson']['payment_id']);
		}
		$this->set('PaymentMessage',$paymentMessage);
		
		$this->loadModel('Available_lesson');
		$available_lessons = $this->Available_lesson->find('first',array('fields'=>array('*'),'conditions'=>array(
				'available_time'=>$lessons['Lesson']['reserve_time'],'teacher_id'=>$lessons['Lesson']['teacher_id'])));
		
		$this->loadModel('User');
		$users = $this->User->find('first',array('fields'=>array('*'),'conditions'=>array('id'=>$lessons['Lesson']['user_id'])));
		
		$this->loadModel('Teacher');
		$teachers = $this->Teacher->find('first',array('fields'=>array('id','tea_name'),'conditions'=>array('id'=>$lessons['Lesson']['teacher_id'])));
		
		$OtherCancelReservationKinds=$this->Programmer1Handler->OtherCancelReservationKinds();
		$AllFixedReservationKinds = array_merge(array('M','MD','M3','M2'),$OtherCancelReservationKinds);
		
		// if the Lesson id is extended
		$this->loadModel('Extention');
		$extention_count = $this->Extention->find('count',array('conditions'=>array('lesson_id'=>$lesson_id)));
		if($extention_count < 1){
			
			$insert_conditions['lesson_id'] = $lessons['Lesson']['id'];
			// $insert_conditions['pay_plan'] = $user['User']['pay_plan'];
			$insert_conditions['reservation_kinds'] = $lessons['Lesson']['reservation_kinds'];
			$insert_conditions['teacher_name'] = $teachers['Teacher']['tea_name'];
			$insert_conditions['reserve_time'] = $lessons['Lesson']['reserve_time'];
			$insert_conditions['reason'] = $extention_kinds; //extended , missing
				
			$insert_conditions['user_id'] = $users['User']['id'];
			$insert_conditions['user_nick_name'] = $users['User']['nick_name'];
			$insert_conditions['pay_plan'] = $users['User']['pay_plan'];
			$insert_conditions['tarket_day'] = $lessons['Lesson']['reserve_time'];
			$insert_conditions['who_extend'] = $loginInfo['id'];
			
			$teacher_id = $lessons['Lesson']['teacher_id'];
			$user_id = $lessons['Lesson']['user_id'];
			$payment_id = $lessons['Lesson']['payment_id'];
			$reserve_time = $lessons['Lesson']['reserve_time'];
			$reservation_kinds = $lessons['Lesson']['reservation_kinds'];
			if(isset($lessons['Lesson']['class_minutes']) && $lessons['Lesson']['class_minutes'] > 0){
				$minutes_class=$lessons['Lesson']['class_minutes'];
			}else $minutes_class=$this->Programmer1Handler->minutes_class;
			
			$reserve_only_time=date('H:i:s',strtotime($reserve_time));
			$time_selected=$reserve_only_time;
			$today = date('Y-m-d');
			
			$last_lesson = $this->Lesson->find('first',array('fields'=>array('*')
					,'conditions'=>array(
							'Lesson.lesson_status'=>array('R')
							,'Lesson.reservation_kinds'=>$reservation_kinds
							,'TIME(Lesson.reserve_time) = '=>"$reserve_only_time"
							,'Lesson.reserve_time >= '=>"'{$today}'"
							,'OR'=>array(
									array('Lesson.user_id'=>$user_id)
									,array('Lesson.teacher_id'=>$teacher_id)))
					,'order'=>'Lesson.reserve_time DESC'
			));
			
			// If last_lesson is nothing execute the else and it will started tomorrow
			if(isset($last_lesson['Lesson'])){
			}
			else {
				// Get the lesson information this lesson_id
				$last_lesson['Lesson'] = $lessons['Lesson'];
				$last_lesson['Lesson']['reserve_time'] = date('Y-m-d ' + $reserve_only_time,strtotime('+1 days'));
			}
			
			$extend_only_day=date('Y-m-d',strtotime($last_lesson['Lesson']['reserve_time'] . '+1 day'));
			$search_date=$extend_only_day;
			
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
					$user_condition=array(array('Lesson.teacher_id'=>$teacher_id),array('Lesson.user_id'=>$user_id));
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
						if(in_array($reservation_kinds,$AllFixedReservationKinds)){
							if($extend_type2=='M' || !in_array($reservation_kinds,array('K','P'))){
								$insert_lessons=$this->Programmer1Handler->InsertAvailableLesson(false,$insert_lessons,$s_timed,$s_reserve_time_from,$s_reserve_time_until
										,$s_reserve_time25M,$class_minutes_cond,$ko_datetime,$minutes_class,$teacher_id,$user_id,$payment_id,$reservation_kinds,$currenttime);
							}
						}
						// $this->Available_lesson->deleteAll(array('available_time'=>$reserve_time,'teacher_id'=>$teacher_id),false);
						$check=false;
					} // End if($emp > 0){}else{}
				}
				$s_reserve_time_from = date('Y-m-d H:i:s',strtotime($s_reserve_time_from . '+1 day'));
				$s_reserve_time_until = date('Y-m-d H:i:s',strtotime($s_reserve_time_until . '+1 day'));
				$s_reserve_time25M = date('Y-m-d H:i:s',strtotime($s_reserve_time25M . '+1 day'));
			} // End while($check == true){}
			
			$extend_day_org = $ko_datetime;
			$extend_day = $ko_datetime;

			// ex_count to save excount
			$mb_id = $users['User']['mb_id'];
			$before_ex_count = $users['User']['ex_count'];
			$ex_count = $users['User']['ex_count'];
			$pay_plan = $users['User']['pay_plan'];
			$last_day = $users['User']['last_day'];
			
			// to point insert
			$mb_id = $users['User']['mb_id'];
			// $user_id = $user['User']['id'];
			$ct = get_time();
			// $current_time = $ct['time_ymdhis'] ;
			$current_time = date('Y-m-d H:i:s');
			$content = $reserve_time.'수업연기'.
			$spent_point = $lessons['Lesson']['spend_point'];
				
			$extend_day_org = '';
			
			echo date('Y',strtotime($last_day)) . '<br>';
			echo date('m',strtotime($last_day)) . '<br>';
			echo date('d',strtotime($last_day)) . '<br>';
			if(date('Y',strtotime($last_day)) < 2000 || (date('m',strtotime($last_day)) < 1
					|| date('d',strtotime($last_day)) < 1)){
				$last_day = $reserve_time;
				echo  $last_day . '<br>';
			}else echo $last_day . 'Last Days<br>';
			
			$insert_conditions['extend_day'] = $extend_day;
			$insert_conditions['last_day'] = $last_day;
			$update_conditions['last_day'] = $extend_day;
			
			// update the lessons
			$this->Lesson->updateAll(array('lesson_status'=>"'$extention_kinds'"),array('id'=>$lesson_id));
			// update the available_lesson
			$this->Available_lesson->updateAll(array('reserve_status'=>"'$extention_kinds'")
					,array('id'=>$available_lessons['Available_lesson']['id']));
			
			if($extend_type2=='K' && $reservation_kinds == 'K'){
				// if($reservation_kinds=='K'){
					$update_conditions['user_id'] = $users['User']['id'];
					$update_conditions['ex_count'] = null;
					
					if($pay_plan == 'E1'){
						$update_conditions['ex_count'] = 0;
						$update_conditions['last_day'] = $extend_day;
					}elseif($pay_plan == 'E2' || $pay_plan == 'E3' || $pay_plan == 'E4'){
						$ex_count = $ex_count+10;
						$update_conditions['last_day'] = $last_day;
						
						// E2
						if($ex_count == 20){
							if($pay_plan == 'E2'){
								$update_conditions['last_day'] = $extend_day;
								$ex_count = 0;
							}
						// E3
						}else if($ex_count == 30){
							if($pay_plan == 'E3'){
								$update_conditions['last_day'] = $extend_day;
								$ex_count = 0;
							}
						// E4
						}else if($ex_count == 40){
							if($pay_plan == 'E4'){
								$update_conditions['last_day'] = $extend_day;
								$ex_count = 0;
							}
						}
						$update_conditions['ex_count'] = $ex_count;
					} // End if($pay_plan == 'E1'){}
					// elseif($pay_plan == 'E2' || $pay_plan == 'E3' || $pay_plan == 'E4'){}
					$extend_msg = $before_ex_count.','.$ex_count;
					$insert_conditions['description'] = $extend_msg;
					$this->ModelDefaultHandler->insertExtention($insert_conditions,'Lesson');
					$this->ModelDefaultHandler->updateUser($update_conditions,'Lesson');
					
					$reserve_time_coupon = date('Y-m-d H:i', strtotime($insert_conditions['reserve_time']));
					 
					$message = "큐빅토크입니다 큐빅수업연장 {$insert_conditions['teacher_name']} {$reserve_time_coupon} 죄송합니다";
					$message2 = "큐빅토크입니다 큐빅수업연장 {$insert_conditions['teacher_name']} {$reserve_time_coupon} 죄송합니다";
					$sms_message_content = "큐빅토크입니다 큐빅수업연장 {$insert_conditions['teacher_name']} {$reserve_time_coupon} 죄송합니다";
				// // Fixed Reservation
				// }else{
					
				// } // End if($reservation_kinds=='K'){}else{}
				
			// Point
			}elseif($extend_type2=='P' || $reservation_kinds=='P'){
				$sql = " insert into g4_point
				set mb_id = '$mb_id',
				po_datetime = '$current_time',
				po_content = '$content',
				po_point = '$spent_point',
				po_rel_table = 'lessons',
				po_rel_id = '',
				po_rel_action = '수업' ";
				$free_sql_result = $this->Lesson->free_sql($sql);
				
				//In case of point the sume point
				$select_string = "sum(po_point) AS point ";
				$table = "g4_point ";
				$where_string =  "mb_id = '$mb_id' ";
				 
				$point_results = $this->Lesson->select_free_common($select_string,$table,$where_string);
				foreach($point_results as $point_result){
					$point = $point_result[0]['point'];
				}
				
				//Update g4_member
				$sql =  "update g4_member
				SET mb_point = $point
				WHERE mb_id = '$mb_id'";
				$this->Lesson->free_sql($sql);
				
				$insert_conditions['description'] = $point;
				$this->ModelDefaultHandler->insertExtention($insert_conditions,'Lesson');
				
				$reserve_time_point = date('Y-m-d H:i', strtotime($insert_conditions['reserve_time']));
				
				$message = "큐빅토크입니다 포인트수업연장 {$insert_conditions['teacher_name']} {$reserve_time_point} 죄송합니다";
				$message2 = "큐빅토크입니다 포인트수업연장 {$insert_conditions['teacher_name']} {$reserve_time_point} 죄송합니다";
				$sms_message_content = "큐빅토크입니다 포인트수업연장 {$insert_conditions['teacher_name']} {$reserve_time_point} 죄송합니다";

			// Fixed Reservation Kinds
			}elseif($extend_type2=='M' || in_array($reservation_kinds,$AllFixedReservationKinds)){
				if(in_array($reservation_kinds,array('M','MD'))){
					// Insert Lesson Class Multiple
					$total_insert_lessons = count($insert_lessons);
					if($total_insert_lessons > 0){
						$insert_lessons[$total_insert_lessons-=1]['spend_point']=$lessons['Lesson']['spend_point'];
						$this->Programmer1Handler->InsertLesson($insert_lessons);
					} // End if($insertToDb == true){}
					
					$extend_msg = $before_ex_count.','.$ex_count;
					$insert_conditions['description'] = $extend_msg;
					
					$this->ModelDefaultHandler->insertExtention($insert_conditions,'Lesson');
					$message = "$reserve_time.'이 연장되어 '. $extend_day. '수업이 추가 되었습니다.'";
					$message2 = '큐빅토크입니다 '. $teachers['Teacher']['tea_name'] . ' ' . date('m-d hi',strtotime($reserve_time)).'이연장되어 '.date('m-d hi',strtotime($extend_day)).'수업이추가되었습니다';
					
					// Tell day to today
					$teacher_name = explode(' ',$teachers['Teacher']['tea_name']);
					$sms_message_content = 'T.' . ucwords($teacher_name[0]) . ' ';
					$name_of_days = '';
					if(isset($this->Programmer1Handler->name_of_days['ko'])
							&& is_array($this->Programmer1Handler->name_of_days['ko'])){
						$name_of_days = "({$this->Programmer1Handler->name_of_days['ko'][date('w',strtotime($reserve_time))]})";
					}
					$sms_message_content .= date("m-d{$name_of_days} H:i", strtotime($reserve_time)) . '이 연장되어 ';
					$name_of_days = '';
					if(isset($this->Programmer1Handler->name_of_days['ko'])
							&& is_array($this->Programmer1Handler->name_of_days['ko'])){
						$name_of_days = "({$this->Programmer1Handler->name_of_days['ko'][date('w',strtotime($extend_day))]})";
					}
					$sms_message_content .= date("m-d{$name_of_days} H:i", strtotime($extend_day)) . '수업이 추가되었습니다';
					
					$this->Programmer1Handler->ActionDetails($loginInfo['id'],$lesson_id,'Extend',$this->AllTeacher(),$this->RequestHandler->getClientIp(),null,null,$extention_kinds);
				}elseif(in_array($reservation_kinds,array('M3','M2'))){
					// Insert Lesson Class Either Single or Multiple
					$total_insert_lessons = count($insert_lessons);
					if($total_insert_lessons > 0){
						$insert_lessons[$total_insert_lessons-=1]['spend_point']=$lessons['Lesson']['spend_point'];
						$this->Programmer1Handler->InsertLesson($insert_lessons);
					} // End if($insertToDb == true){}
					
					$extend_msg = $before_ex_count.','.$ex_count;
					$insert_conditions['description'] = $extend_msg;
					$this->ModelDefaultHandler->insertExtention($insert_conditions,'Lesson');
					$message = "$reserve_time.'이 연장되어 '. $extend_day. '수업이 추가 되었습니다.'";
					
					// Tell day to today
					$teacher_name = explode(' ',$teachers['Teacher']['tea_name']);
					$sms_message_content = 'T.' . ucwords($teacher_name[0]) . ' ';
					$name_of_days = '';
					if(isset($this->Programmer1Handler->name_of_days['ko'])
							&& is_array($this->Programmer1Handler->name_of_days['ko'])){
						$name_of_days = "({$this->Programmer1Handler->name_of_days['ko'][date('w',strtotime($reserve_time))]})";
					}
					$sms_message_content .= date("m-d{$name_of_days} H:i", strtotime($reserve_time)) . '이 연장되어 ';
					$name_of_days = '';
					if(isset($this->Programmer1Handler->name_of_days['ko'])
							&& is_array($this->Programmer1Handler->name_of_days['ko'])){
						$name_of_days = "({$this->Programmer1Handler->name_of_days['ko'][date('w',strtotime($extend_day))]})";
					}
					$sms_message_content .= date("m-d{$name_of_days} H:i", strtotime($extend_day)) . '수업이 추가되었습니다';
					
					$this->Programmer1Handler->ActionDetails($loginInfo['id'],$lesson_id,'Extend',$this->AllTeacher(),$this->RequestHandler->getClientIp(),null,null,$extention_kinds);
				}elseif(in_array($reservation_kinds,$OtherCancelReservationKinds)){
					// Insert Lesson Class Multiple
					$total_insert_lessons = count($insert_lessons);
					if($total_insert_lessons > 0){
						$insert_lessons[$total_insert_lessons-=1]['spend_point']=$lessons['Lesson']['spend_point'];
						$this->Programmer1Handler->InsertLesson($insert_lessons);
					} // End if($insertToDb == true){}
					
					$extend_msg = $before_ex_count.','.$ex_count;
					$insert_conditions['description'] = $extend_msg;
					$this->ModelDefaultHandler->insertExtention($insert_conditions,'Lesson');
					$message = "$reserve_time.'이 연장되어 '. $extend_day. '수업이 추가 되었습니다.'";
					$message2 = '큐빅토크입니다 '. $teachers['Teacher']['tea_name'] . ' ' . date('m-d hi',strtotime($reserve_time)).'이연장되어 '.date('m-d hi',strtotime($extend_day)).'수업이추가되었습니다';
					
					// Tell day to today
					$teacher_name = explode(' ',$teachers['Teacher']['tea_name']);
					$sms_message_content = 'T.' . ucwords($teacher_name[0]) . ' ';
					$name_of_days = '';
					if(isset($this->Programmer1Handler->name_of_days['ko'])
							&& is_array($this->Programmer1Handler->name_of_days['ko'])){
						$name_of_days = "({$this->Programmer1Handler->name_of_days['ko'][date('w',strtotime($reserve_time))]})";
					}
					$sms_message_content .= date("m-d{$name_of_days} H:i", strtotime($reserve_time)) . '이 연장되어 ';
					$name_of_days = '';
					if(isset($this->Programmer1Handler->name_of_days['ko'])
							&& is_array($this->Programmer1Handler->name_of_days['ko'])){
						$name_of_days = "({$this->Programmer1Handler->name_of_days['ko'][date('w',strtotime($extend_day))]})";
					}
					$sms_message_content .= date("m-d{$name_of_days} H:i", strtotime($extend_day)) . '수업이 추가되었습니다';
					
					$this->Programmer1Handler->ActionDetails($loginInfo['id'],$lesson_id,'Extend',$this->AllTeacher(),$this->RequestHandler->getClientIp(),null,null,$extention_kinds);
				// What kind of reservation is this
				}else{
					echo 'What kind of reservation kinds is this ' . $reservation_kinds . '?';
				}
			}elseif($extend_type2=='N'){
			}else{}
			
			$this->loadModel('User');
			$users = $this->User->find('first',array('fields'=>array('*'),'conditions'=>array('id'=>$lessons['Lesson']['user_id'])));
			echo $last_day;
			pr($insert_conditions);
			$this->set('user', $users);
			$this->set('lesson', $lessons);
			$this->set('teacher', $teachers);
			$this->set('last_day', $last_day);
			$this->set('insert_conditions',$insert_conditions);
			$this->set('ex_count',$ex_count);
		}else{
			$message = 'this lesson already extended !! ';
		}

		$this->set('extention_count',$extention_count);
		$this->set('message',$message);
		$this->set('message2',$message2);
		$this->set('sms_message_content',$sms_message_content);
	} // End function teacher_extend_lessons_3(){}
	
//2015-04-20 Rarry
function changeAllLessonStatus(){
	$rCount=1;
	$this->set('rCount',$rCount);
	$lesson_status_all = $this->Programmer1Handler->getStatusKind('lesson_status');
	$this->set('lesson_status_all',$lesson_status_all);
	if(isset($this->params['form']['action_confirm']) && $this->params['form']['action_confirm']=='changeAllLessonStatus'){
		if(isset($this->params['form']['lesson_id']) && !empty($this->params['form']['lesson_id']) && is_array($this->params['form']['lesson_id'])){
			$this->Programmer1Handler->changeAllLessonStatus($this->params['form']['lesson_id'],$this->params['form']['newLessonStatus'],$this->params);
		} // End if(isset($this->params['form']['lesson_id']) && !empty($this->params['form']['lesson_id']) && is_array($this->params['form']['lesson_id'])){}
	} // End if(isset($this->params['form']['action_confirm']) && $this->params['form']['action_confirm']=='changeAllLessonStatus'){}
} // End function function changeAllLessonStatus(){};
	
//2015-03-24 Rarry
function changeAllReservationKinds(){
 $rCount=1;
 $this->set('rCount',$rCount);
 $reservation_kinds_all = $this->Programmer1Handler->getStatusKind('reservation_kind');
 $this->set('reservation_kinds_all',$reservation_kinds_all);
 if(isset($this->params['form']['action_confirm']) && $this->params['form']['action_confirm']=='changeAllReservationKinds'){
  if(isset($this->params['form']['lesson_id']) && !empty($this->params['form']['lesson_id']) && is_array($this->params['form']['lesson_id'])){
   $this->Programmer1Handler->changeAllReservationKinds($this->params['form']['lesson_id'],$this->params['form']['newReservationKinds']);
  } // End if(isset($this->params['form']['lesson_id']) && !empty($this->params['form']['lesson_id']) && is_array($this->params['form']['lesson_id'])){}
 } // End if(isset($this->params['form']['action_confirm']) && $this->params['form']['action_confirm']=='changeAllReservationKinds'){}
} // End function changeAllReservationKinds(){};
	
 function teacherProceed($teacher_id=null){ // for Compositions only
  $this->loadModel("CompositionAnswer");
  $tCC=array(); // $teacherCompositionCondition = $tCC;
  $tCC["CompositionAnswer.teacher_id"]=$teacher_id;
  $tCC["CompositionAnswer.answer_by_user !="]="";
  $tCC["CompositionAnswer.answer_by_teacher ="]="";
  $fields="";
  $teacherProceed=$this->CompositionAnswer->find("count", array("fields"=>array(), "conditions"=>$tCC));
  return $teacherProceed;
 }

//	2014-06-26
	function teacherClass ($id = null, $numClass = null) {
		$loginInfo = $this -> CheckHandler -> checkLoginInfo();
		$this -> set('loginInfo', $loginInfo);
		$search_type = 'today_lesson';
		$this -> set('search_type', $search_type);
//		if ( empty($loginInfo) ) {
			
//		}
		$today		 = date('Y-m-d');
		$tomorrow	 = date('Y-m-d', strtotime($today.'+1 day') );
		$AL_condition = array();
		$AL_condition['Available_lesson.teacher_id'] = $id;
		$AL_condition['Available_lesson.available_time >= '] = $today;
		$AL_condition['Available_lesson.available_time < ']	 = $tomorrow;
		$this -> loadModel('Available_lesson');
		$TeacherSched = $this -> Available_lesson -> find('all', array('fields' => array('COUNT(*) as TeacherSched'), 'conditions' => $AL_condition ));
		$this -> set('TeacherSched', $TeacherSched);
		
		$lesson_condition = array();
		$today											 = date('Y-m-d 02:00:00');
		$tomorrow										 = date('Y-m-d 01:00:00', strtotime(date('Y-m-d').'+1 day') );
//		$lesson_condition['Teacher.show']				 = 'Y';
		$lesson_condition['Lesson.teacher_id']			 = $id;
		$lesson_condition['Lesson.lesson_status']		 = array('R', 'F', 'A');
		$lesson_condition['Lesson.reserve_time >= ']	 = $today;
		$lesson_condition['Lesson.reserve_time <= ']	 = $tomorrow;
		$this -> loadModel('User');
		$this -> Lesson -> bindModel( array( 'belongsTo' => array( 'User' => array('className' => 'User', 'foreignKey' => false, 'conditions' => 'User.id = Lesson.user_id') ) ) );
		$TeacherClass = $this -> Lesson -> find( 'all', array('fields' => array(), 'conditions' => $lesson_condition, 'order' => 'Lesson.reserve_time ASC') );
		$this -> set('TeacherClass', $TeacherClass);
		
		$this -> loadModel('Teacher');
		$teacher_condition = array();
		$teacher_condition['id'] = $id;
		$TeacherInfo = $this -> Teacher -> find( 'first', array('fields' => array(), 'conditions' => $teacher_condition) );
		$this -> set('TeacherInfo', $TeacherInfo);
	}
	
	// 2014-05-19
	function countUsersPlan ($pay_plan = 'M') {
		$getPlayplan = array();
		$today		 = date('Y-m-d');
		$display	 = '';
		$this -> loadModel ('User');
		$getPlayplan = $this -> User -> find( 'all', array( 'fields' => array('count(User.pay_plan) as pay_plan'), 'conditions' => array('User.pay_plan != ' => $pay_plan, 'User.last_day >= ' => "$today") ) );
		return $getPlayplan;
	}
	
	// SMS
	function smsOn ($search = null) {
		$loginInfo = $this -> CheckHandler -> checkLoginInfo();
		$this -> set('loginInfo', $loginInfo);
		
		$this->params['form']['teacher_id'] = $loginInfo['id'];
		
		$sms_important_info = array();
		$sms_important_info2 = array();
		$search_condition = array();
		$users_info = "";
		$form = array();
		if ( isset($this -> params["form"]["search_setting"]) && $this -> params["form"]["search_setting"] == 1 ) {
			$form = $this -> params["form"];
			if ( strlen(trim($this -> params["form"]["mb_id"])) > 0 ) {
				$search_condition["User.mb_id LIKE"] = $this -> params["form"]["mb_id"].'%';
			}
			if ( strlen(trim($this -> params["form"]["name"])) > 0 ) {
				$search_condition["User.name LIKE"] = $this -> params["form"]["name"].'%';
			}
			if ( strlen(trim($this -> params["form"]["nick_name"])) > 0 ) {
				$search_condition["User.nick_name LIKE"] = $this -> params["form"]["nick_name"].'%';
			}
			if ( strlen(trim($this -> params["form"]["skype_id"])) > 0 ) {
				$search_condition["User.skype_id LIKE"] = $this -> params["form"]["skype_id"].'%';
			}

			if ( isset($search_condition) && count($search_condition) > 0 ) {
				$this -> loadModel("User");
				$users_info = $this -> User -> find("all", array('fields' => array(), "conditions" => array("OR" => $search_condition), "order" => "User.mb_id ASC"));	
			}
		}
		$this -> set("search_condition", $form);
		$this -> set("user_info", $users_info);
		$this -> set("search", $search);

		if ( isset($this -> params["form"]["sendsms"]) && $this -> params["form"]["sendsms"] == 1 && isset($this -> params["form"]["userId"]) && $this -> params["form"]["userId"] > 0 ) {
			$sms_important_info["userId"] = $this -> params["form"]["userId"];

//			$sms_important_info["userMbId"] = $this -> params["form"]["userMbId".$sms_important_info["userId"]];
//			$sms_important_info["userHp"] = $this -> params["form"]["userHp".$sms_important_info["userId"]];
//			$sms_important_info["userName"] = $this -> params["form"]["userName".$sms_important_info["userId"]];
//			$sms_important_info["userNickName"] = $this -> params["form"]["userNickName".$sms_important_info["userId"]];
//			$sms_important_info["smsMessage"] = $this -> params["form"]["smsMessage".$sms_important_info["userId"]];
			if ( isset($this -> params["form"]["smsMessage".$sms_important_info["userId"]]) ) {
				$sms_important_info["smsMessage"] = $this -> params["form"]["smsMessage".$sms_important_info["userId"]];
			}
			$sms_important_info2 = $this -> Programmer1Handler -> smsMobileNumber ($this -> params["form"]["userId"], $this -> params["form"]);
		}

		$this -> set("sms_important_info", array_merge($sms_important_info, $sms_important_info2));
	}
	
// test user search 2014-05-15
function testUser () {
 $testUser = array();
 $start_date = date('Y-m-d 01:00:00');
 $last_date = date('Y-m-d 01:00:00', strtotime($start_date.'+1 day'));
 $data = array('mb_id'=>'', 'name'=>'', 'nick_name'=>'', 'skype_id'=>''); // default
 if (isset($this -> params['form']['search'])) {
  if (isset($this->params['form']['mb_id']) && strlen($this->params['form']['mb_id']) > 1) {
   $a = trim($this->params['form']['mb_id']);
   $search['User.mb_id'] = "$a";
  } elseif (isset($this->params['form']['name']) && strlen($this->params['form']['name']) > 1) {
   $a = trim($this->params['form']['name']);
   $search['User.name'] = "$a";
  } elseif (isset($this->params['form']['nick_name']) && strlen($this->params['form']['nick_name']) > 1) {
   $a = trim($this->params['form']['nick_name']);
   $search['User.nick_name'] = "$a";
  } elseif (isset($this->params['form']['skype_id']) && strlen($this->params['form']['skype_id']) > 1) {
   $a = trim($this->params['form']['skype_id']);
   $search['User.skype_id'] = "$a";
  }
  
  if (isset($this->params['form']['start_date']) && strlen($this->params['form']['start_date']) > 1) {
   $start_date = date('Y-m-d 01:00:00',strtotime($this->params['form']['start_date']));
  }
  
  if (isset($this->params['form']['last_date']) && strlen($this->params['form']['last_date']) > 1) {
   $last_date = date('Y-m-d 01:00:00',strtotime($this->params['form']['last_date']));
  } else {
   $last_date = date('Y-m-d 01:00:00', strtotime($start_date.'+1 day'));
  }
  $data = $this->params['form'];
 }
// $this -> User -> bindModel( array('belongsTo' => array('Lesson' => array('className' => 'Lesson', 'foreignKey' => false, 'conditions' => 'Lesson.user_id = User.id'),
//																   'Teacher' => array('className' => 'Teacher', 'foreignKey' => false, 'conditions' => 'Lesson.teacher_id = Teacher.id')))  );
 $search['Lesson.reserve_time >='] = "$start_date";
 $search['Lesson.reserve_time <='] = "$last_date";
 $search['Lesson.reservation_kinds'] = array('T','TM'); // default;
 $search['Lesson.lesson_status'] = array('F','A','R'); // default;
 $options = array(array('table'=>'lessons','type'=>'LEFT','alias'=>'Lesson','conditions'=>'User.id=Lesson.user_id')
  ,array('table'=>'teachers','type'=>'LEFT','alias'=>'Teacher','conditions'=>'Lesson.teacher_id=Teacher.id')
 );
 $fields = array('User.id','User.mb_id','User.name','User.nick_name','User.skype_id','User.pay_plan','User.pay_plan2','User.created','Teacher.tea_name'
  ,'Lesson.id','Lesson.reservation_kinds','Lesson.reserve_time', 'Lesson.created'
  ,'(SELECT teachers.tea_name FROM users LEFT JOIN lessons ON (users.id=lessons.user_id) LEFT JOIN teachers ON (lessons.teacher_id=teachers.id) WHERE users.id=User.id AND User.pay_plan2 IN ("E1F","E2F","E1FD","E2FD","E1F2","E1F3") order by lessons.reserve_time DESC limit 1) as current_teacher'
  ,'(SELECT teachers.id FROM users LEFT JOIN lessons ON (users.id=lessons.user_id) LEFT JOIN teachers ON (lessons.teacher_id=teachers.id) WHERE users.id=User.id AND User.pay_plan2 IN ("E1F","E2F","E1FD","E2FD","E1F2","E1F3") order by lessons.reserve_time DESC limit 1) as current_teacher_id'
 );
 $this->loadModel('User');
 $testUser = $this->User->find('all',array('fields'=>$fields,'joins'=>$options,'conditions'=>$search));
 
 $this->set('data', $data);
 $this->set('testUser', $testUser);
}
	
	function autoEnglish () {
    	Configure::write('debug',1);
    	$this->loadModel('User');
    	$this->set('users', $this -> User -> find('all', array('fields' => array('User.nick_name'),'conditions' => array('User.nick_name LIKE' => '%'.trim($this->params['url']['q']).'%'))));
    	$this->layout = 'ajax';
    }
    
    function autoKorean () {
    	Configure::write('debug',1);
    	$this->loadModel('User');
    	$this->set('users', $this -> User -> find('all', array('fields' => array('User.name'),'conditions' => array('User.name LIKE' => '%'.trim($this->params['url']['q']).'%'))));
    	$this->layout = 'ajax';
    }
    
    function autoIdname () {
    	Configure::write('debug',1);
    	$this->loadModel('User');
    	$this->set('users', $this -> User -> find('all', array('fields' => array('User.mb_id'),'conditions' => array('User.mb_id LIKE' => '%'.trim($this->params['url']['q']).'%'))));
    	$this->layout = 'ajax';
    }
    
    function autoSkype () {
    	Configure::write('debug',1);
    	$this->loadModel('User');
    	$this->set('users', $this -> User -> find('all', array('fields' => array('User.skype_id'),'conditions' => array('User.skype_id LIKE' => '%'.trim($this->params['url']['q']).'%'))));
    	$this->layout = 'ajax';
    }
    //-----------------------------------------
	
	// M2, M3 Extend
	function weekdays_reqr ($state = null, $reservation_kinds = null) {
		$weekdays_reqr = array();
		$this -> loadModel("Dictionary");
		$this -> loadModel("DictionaryDetail");
		$this -> DictionaryDetail -> bindModel( array('belongsTo' => array('Dictionary' => array('className' => 'Dictionary', 'foreignKey' => 'dictionary_id'))) );
		$weekdays = $this -> DictionaryDetail -> find( "all", array("fields" => array("Dictionary.id", "DictionaryDetail.name"), "conditions" => array("Dictionary.name" => $reservation_kinds) ) );
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
	
	function teacher_extend_lessons2 () {
		$loginInfo = $this->CheckHandler->checkLoginInfo();

		$search_type = null;
	
		//$current_ymd = $this->ModelHandler->_timeymd;
	
		$session_search	 = $this -> Session -> read('inputted_datas');
	
		$teacher_id		 = $session_search['teacher_id'];
		$nick_name		 = $session_search['nick_name'];
		$skype_id		 = $session_search['skype_id'];
		$start_date		 = $session_search['start_date'];
		$last_date		 = $session_search['last_date'];

		$extend_type2	 = $this -> params['form']['extend_type2'];
		$from_where		 = $this -> params['form']['from_where'];
		$lesson_id		 = $this -> params['form']['lesson_id'];
		$extention_kinds = $this -> params['form']['extention_kinds'];
		
		
		// get the class info
		$lessons = $this -> ModelHandler -> getLessonByLessonId($lesson_id,'Lesson'); 
		$lesson	 = $lessons[0];
		
		$message = '';
		$sms_message_content = '';
		
		$payment_id		 = $lesson['Lesson']['payment_id'];
		$this -> set( 'PaymentMessage', $this -> Programmer1Handler -> PaymentExtend( $payment_id ) );

		$this -> loadModel('Extention');
		$last_lesson	 = $this -> Extention -> find( 'count', array( 'conditions' => array( 'Extention.lesson_id' =>  $lesson['Lesson']['id'] ) ) );
		$extention_count = $last_lesson;
		 	
		 	
		if ( $extention_count == 0 ){
			
			$teacher_id	 = $lesson['Lesson']['teacher_id'];
			$user_id	 = $lesson['Lesson']['user_id'];

			//Get Teacher Information
			$search_conditions_t				 = null;
			$search_conditions_t['teacher_id']	 = $teacher_id;
			$teachers	 = $this -> ModelDefaultHandler -> getDefaultTeacher($search_conditions_t, 'Lesson');
			$teacher	 = $teachers[0];
		 		
			//users Information
			$search_conditions_u					 = null;
			$search_conditions_u['user_id']			 = $user_id;
			$search_conditions_u['user_nick_name']	 = null;
			$users	 = $this -> ModelDefaultHandler -> getDefaultUser( $search_conditions_u, 'Lesson' );
		 	$user	 = $users[0];
		 		
			$insert_conditions['lesson_id']			 = $lesson['Lesson']['id'];
	//		$insert_conditions['pay_plan']			 = $user['User']['pay_plan'];
			$insert_conditions['reservation_kinds']	 = $lesson['Lesson']['reservation_kinds'];
			$insert_conditions['teacher_name']		 = $teacher['Teacher']['tea_name'];
			$insert_conditions['reserve_time']		 = $lesson['Lesson']['reserve_time'];
			$insert_conditions['reason']			 = $extention_kinds; //extended , missing
		 
			$insert_conditions['user_id']			 = $user['User']['id'];
			$insert_conditions['user_nick_name']	 = $user['User']['nick_name'];
			$insert_conditions['pay_plan']			 = $user['User']['pay_plan'];
			$insert_conditions['tarket_day']		 = $lesson['Lesson']['reserve_time'];
			$insert_conditions['who_extend'] = $loginInfo['id'];
			
			$last_day								 = $user['User']['last_day'];
		 	
			//ex_count
			//to save excount
			$before_ex_count = $user['User']['ex_count'];
			$ex_count		 = $user['User']['ex_count'];
			$pay_plan		 = $user['User']['pay_plan'];
			 		
			 		
			//to point insert
			$mb_id				 = $user['User']['mb_id'];
			$user_id			 = $user['User']['id'];
			$ct					 = get_time();
		//	$current_time		 =   $ct['time_ymdhis'] ;
			$current_time		 = date( 'Y-m-d H:i:s' );
			$reserve_time		 = $lesson['Lesson']['reserve_time'];
			$content			 = $reserve_time.'수업연기'.
			$spent_point		 = $lesson['Lesson']['spend_point'];
			$reservation_kinds	 = $lesson['Lesson']['reservation_kinds'];
			 		
	//		echo $extend_type2 ." ** ". $reservation_kinds ." ** ". $pay_plan ." ** ". $ex_count;
			
			$today = date('Y-m-d');
			
			$extend_day_org = '';
			
			if ( $extend_type2 != 'P' ) {
				
				// get last class of student depending reservation_kinds (M2, M3)
				$reserve_only_time = '';
				$reserve_only_time = date( "H:i:s", strtotime($reserve_time) );
				$last_lesson = $this -> Lesson -> find( 'first', array(
							'conditions' => array(
										'Lesson.reservation_kinds'  => "$reservation_kinds",
										'Lesson.lesson_status'  => 'R',
										'Lesson.reserve_time >= '=>"'{$today}'",
										'Lesson.reserve_time LIKE'  => '%'.$reserve_only_time,
										'OR' => array( array('Lesson.user_id' => $user_id),
													   array('Lesson.teacher_id' => $teacher_id) )),
							'order' => array('Lesson.reserve_time DESC')
				));
	
				$last_dayOn = "";
				//$last_dayOn = $last_lesson['Lesson']['reserve_time'];
				
				// If last_lesson is nothing execute the else and it will started tomorrow
				if(isset($last_lesson['Lesson'])){
					$last_dayOn = $last_lesson['Lesson']['reserve_time'];
				}
				else {
					// Get the lesson information this lesson_id
					$last_lesson['Lesson'] = $lesson['Lesson'];
					$last_dayOn = date('Y-m-d ' + $reserve_only_time,strtotime('+1 days'));
				}
				
				//extend_day depending on reservation_kinds
				$weekdays_arr	 = $this -> weekdays_reqr( 'number', $lesson['Lesson']['reservation_kinds'] );
				$extend_day_org	 = date( "Y-m-d H:i:s", strtotime( $last_dayOn.'+1 day' ) );
				$range = 0;
				// identify 00:00,00:30,01:00,...,04:30 or greater than 05:00
				// Convert to long time it will become 24:00 (MidNight) to greater than 29:00 (05:00) AM
				$hours=(date('G',strtotime($last_dayOn)) + 24);
				if($hours >= 24 && $hours < 29){ // 24:00 (00:00 AM) and 29:00 (05:00 AM)
					$new_weekdays_arr=array();
					// if Mon is 1 then it will become Tue
					// since we cannot input Mon class
					foreach($weekdays_arr as $keyweekdays_arr => $kwda){
						$new_weekdays_arr[] = $kwda+=1;
					}
					$weekdays_arr = $new_weekdays_arr;
				}
				while ( $range < 1 ) {
					if ( in_array( date('N', strtotime($extend_day_org)), $weekdays_arr ) ) {
	//					$emp = $this -> Lesson -> find( 'count', array( 'fields' => array( 'COUNT(Lesson.id) as count' ), 'conditions' => array( 'Lesson.reserve_time' => $extend_day_org, 'Lesson.lesson_status' => 'R', 'OR' => array( 'Lesson.user_id' => $user_id, 'Lesson.teacher_id' => $teacher_id ) )));
						$emp = $this -> Lesson -> find( 'count', array( 'fields' => array( 'COUNT(Lesson.id) as count' ), 'conditions' => array( 'Lesson.reserve_time' => $extend_day_org, 'Lesson.lesson_status' => 'R', 'Lesson.teacher_id' => $teacher_id )));
						if ( $emp < 1 ) {
							$range++;
						} else {
							$extend_day_org =  date( "Y-m-d H:i:s", strtotime( $extend_day_org.'+1 day' ) );
						}
					} else {
						$extend_day_org =  date( "Y-m-d H:i:s", strtotime( $extend_day_org.'+1 day' ) );
					}
				}
			}
			
			$extend_day						 = $extend_day_org;
			$insert_conditions['last_day']	 = $last_day;
			$insert_conditions['extend_day'] = $extend_day;
			
			// Lesson update lesson_status to $extention_kinds
		 	$update_common['id']			 = $lesson_id;
		 	$update_common['lesson_status']	 = $extention_kinds; //Extended OR MISSING
		 	$this -> Lesson -> update_common( 'lessons', $update_common );
		 	
		 	// Available_lesson update reserve_status to $extention_kinds
			$update_common					 = null;
			$update_common['id']			 = $lesson['Lesson']['available_lesson_id'];
			$update_common['reserve_status'] = $extention_kinds;  //Extended OR MISSING
			$this -> Lesson -> update_common( 'available_lessons', $update_common );
			
			//Extend User's last day
			//ONLY COMA resrvation increase only
			if( $extend_type2 == 'K' && $reservation_kinds == 'K' ){
			// 
			 		$update_conditions['user_id']	 = $user['User']['id'];
			 		$update_conditions['ex_count']	 = null;
			// 			
			 		if( $pay_plan == 'E1' ) {
			 			$update_conditions['ex_count'] = 0;
			 			$update_conditions['last_day'] = $extend_day;
			 		}
			 		
					if( $pay_plan == 'E2' || $pay_plan == 'E3' || $pay_plan == 'E4' ){
			 			$ex_count = $ex_count + 10;
			 			$update_conditions['last_day'] = $last_day;
			
			// 			//E2
			 			if( $ex_count == 20 ){
			 				if( $pay_plan == 'E2' ){
			 					$update_conditions['last_day'] = $extend_day;
			 					$ex_count = 0;
			 				}
			// 				//E3
			 			}else if( $ex_count == 30 ){
			 				if($pay_plan == 'E3'){
			 					$update_conditions['last_day'] = $extend_day;
			 					$ex_count = 0;
			 				}
			// 				//E4
			 			}else if( $ex_count == 40 ){
			 				if( $pay_plan == 'E4' ){
								$update_conditions['last_day'] = $extend_day;
			 					$ex_count = 0;
			 				}
			 			}
	
			 			$update_conditions['ex_count'] = $ex_count;
			 		}
			 			
				// extend message
				$extend_msg							 = $before_ex_count.','.$ex_count;
				$insert_conditions['description']	 = $extend_msg;
		
				$this -> ModelDefaultHandler -> insertExtention($insert_conditions,'Lesson');
				$this -> ModelDefaultHandler -> updateUser($update_conditions,'Lesson');
				
				$reserve_time_coupon = date('Y-m-d H:i', strtotime($insert_conditions['reserve_time']));
				 
				$message = "큐빅토크입니다 큐빅수업연장 {$insert_conditions['teacher_name']} {$reserve_time_coupon} 죄송합니다";
				$message2 = "큐빅토크입니다 큐빅수업연장 {$insert_conditions['teacher_name']} {$reserve_time_coupon} 죄송합니다";
				$sms_message_content = "큐빅토크입니다 큐빅수업연장 {$insert_conditions['teacher_name']} {$reserve_time_coupon} 죄송합니다";
	
			}
	
			//2011-11-14 modified
			//point plan test user incresed point
			if( $extend_type2 == 'P' && ( $reservation_kinds == 'M2' || $reservation_kinds == 'M3' ) ) {
			 		$sql = " insert into g4_point
			 		set mb_id = '$mb_id',
			 		po_datetime = '$current_time',
			 		po_content = '$content',
			 		po_point = '$spent_point',
			 		po_rel_table = 'lessons',
			 		po_rel_id = '',
			 		po_rel_action = '수업' ";
			 
			 		$free_sql_result = $this -> Lesson -> free_sql($sql);
			 
			 		//In case of point the sume point
			 		$select_string = "sum(po_point) AS point ";
			 		$table = "g4_point ";
			 		$where_string =  "mb_id = '$mb_id' ";
			 			
			 		$point_results = $this -> Lesson -> select_free_common( $select_string, $table, $where_string );
			 
			 		foreach($point_results as $point_result){
			 			$point = $point_result[0]['point'];
			 		}
			 
			 	//Update g4_member
			 	$sql =  "update g4_member
			 	SET mb_point = $point
			 	WHERE mb_id = '$mb_id'";
			 		
			 	$this -> Lesson -> free_sql($sql);
			 		
			 	$insert_conditions['description'] = $point;
			 	$this -> ModelDefaultHandler -> insertExtention( $insert_conditions, 'Lesson' );
			 	
			 	$reserve_time_point = date('Y-m-d H:i', strtotime($insert_conditions['reserve_time']));
			 	
			 	$message = "큐빅토크입니다 포인트수업연장 {$insert_conditions['teacher_name']} {$reserve_time_point} 죄송합니다";
			 	$message2 = "큐빅토크입니다 포인트수업연장 {$insert_conditions['teacher_name']} {$reserve_time_point} 죄송합니다";
			 	$sms_message_content = "큐빅토크입니다 포인트수업연장 {$insert_conditions['teacher_name']} {$reserve_time_point} 죄송합니다";

			}
	
			// In case of fixed student
			// 	echo $extend_type2." ** ". $reservation_kinds;
			if( $extend_type2 == 'M' || ( $reservation_kinds == 'M2' || $reservation_kinds == 'M3' ) ){
		
				$this->loadModel('Available_lesson');
				
				$available_lesson_id	 = $last_lesson['Lesson']['available_lesson_id'];
				$last_Available_lesson	 = $this -> Available_lesson -> find( 'first', array( 'conditions' => array('Available_lesson.id' => $available_lesson_id )) );
				$last_lesson_time		 = $last_lesson['Lesson']['reserve_time'];
				
			//	check current lesson and last lesson
				if( $last_lesson_time == $reserve_time ){}

				$last_Available_lesson['Available_lesson']['id']			 = null;
				$last_Available_lesson['Available_lesson']['available_time'] = $extend_day;
				$this-> Available_lesson -> save( $last_Available_lesson );
				$lastInsertId = $this -> Available_lesson -> getLastInsertId();

			// 	//lesson table
				$last_lesson['Lesson']['id']					 = null;
				$last_lesson['Lesson']['reserve_time']			 = $extend_day;
				$last_lesson['Lesson']['teacher_id']			 = $teacher_id;
				$last_lesson['Lesson']['available_lesson_id']	 = $lastInsertId;
				$last_lesson['Lesson']['payment_id']			 = $payment_id;
				
		 	//	$last_lesson['Lesson']['created'] = date('Y-m-d H:i:s');
				$last_lesson['Lesson']['modified'] = date('Y-m-d H:i:s');
				
				$this -> Lesson -> save( $last_lesson );
				$insert_conditions['last_day'] = $extend_day;
			// 		
				$insert_conditions['description'] = $reserve_time.','.$extend_day;
				$this -> ModelDefaultHandler -> insertExtention( $insert_conditions, 'Lesson' );
				$message = "$reserve_time.'이 연장되어 '. $extend_day. '수업이 추가 되었습니다.'";
				
				// Tell day to today
				$teacher_name = explode(' ',$teacher['Teacher']['tea_name']);
				$sms_message_content = 'T.' . ucwords($teacher_name[0]) . ' ';
				$name_of_days = '';
				if(isset($this->Programmer1Handler->name_of_days['ko'])
						&& is_array($this->Programmer1Handler->name_of_days['ko'])){
					$name_of_days = "({$this->Programmer1Handler->name_of_days['ko'][date('w',strtotime($reserve_time))]})";
				}
				$sms_message_content .= date("m-d{$name_of_days} H:i", strtotime($reserve_time)) . '이 연장되어 ';
				$name_of_days = '';
				if(isset($this->Programmer1Handler->name_of_days['ko'])
						&& is_array($this->Programmer1Handler->name_of_days['ko'])){
					$name_of_days = "({$this->Programmer1Handler->name_of_days['ko'][date('w',strtotime($extend_day))]})";
				}
				$sms_message_content .= date("m-d{$name_of_days} H:i", strtotime($extend_day)) . '수업이 추가되었습니다';
				
				$this -> Programmer1Handler -> ActionDetails( $loginInfo['id'], $lesson_id, 'Extend', $this -> AllTeacher(), $this -> RequestHandler -> getClientIp(), null, null, $extention_kinds );
			// 	
			}
		
			// do nothing
			if( $extend_type2 == 'N' ){	}
 		
			//after Update One more time
			$users = $this -> ModelDefaultHandler -> getDefaultUser( $search_conditions_u, 'Lesson' );
			$user = $users[0];

			$this -> set( 'user', $user );
			$this -> set( 'lesson', $lesson );
			$this -> set( 'teacher', $teacher );
			$this -> set( 'last_day', $last_day );
			$this -> set( 'insert_conditions', $insert_conditions );
			$this -> set( 'ex_count', $ex_count );
		}else{
			$message = 'this lesson already extended !! ';
		}
		// 	 
		$this->set( 'extention_count', $extention_count );
		$this->set( 'message', $message );
		$this->set('sms_message_content',$sms_message_content);

	}
	//----------------------------------------
	
	
	function sample () {
		if ( isset($this->params["form"]["submit"]) ) {
			if ( isset($this->params["form"]["post"]) && strlen($this->params["form"]["post"]) > 0 ) {
				echo "THIS IS INPUT TYPE=SUBMIT";
			}
		} else {
			if ( isset($this->params["form"]["post"]) && strlen($this->params["form"]["post"]) > 0 ) {
				echo "THIS IS INPUT TYPE=IMAGE";
			}
		}
		
	}
	
	function sample2 () {
		if ( strlen($this->params["form"]["post"]) > 0 ) {
			echo $this->params["form"]["post"];
		}
	}
	
	function sample3 ($post = null) {
		echo $post;
	}
	
	function sample4 () {
		if($this -> RequestHandler -> isAjax()){
			Configure::write('debug',1);
			echo $this->params["form"]["post"];
		}
	}
	
	function sample5 ($post = null) {
		if($this -> RequestHandler -> isAjax()){
			Configure::write('debug',1);
			echo $this->params["form"]["post"];
		}
	}
	
	function board(){}
	
	function fixSced() {
		$loginInfo = $this->CheckHandler->checkLoginInfo();
		$this->loadModel("FixscheduleTeacher");
		return $this->FixscheduleTeacher->find("all", array("conditions"=>array("FixscheduleTeacher.teacher_id"=>$loginInfo["id"]),"order"=>"FixscheduleTeacher.time ASC"));
	}
	
	function AllTeacher() {
		$AllTeacher = $this->Lesson->query("SELECT Teacher.id, Teacher.tea_name FROM teachers as Teacher WHERE Teacher.show = 'Y'");
		foreach ($AllTeacher as $id => $tea_name) {
			$Teachers[$tea_name['Teacher']['id']] = $tea_name['Teacher']['tea_name']; }
		return $Teachers;
	}

	function extension() {
		$_totalExtend = $this->Lesson->query(
									"SELECT
										count(*) as COUNT
									FROM extendeds as Extend
									JOIN lessons as Lesson
									ON (Lesson.id = Extend.lesson_id)
									WHERE Extend.status = 'R'
									");
		return $_totalExtend[0][0]['COUNT'];
	}
	
	function homework(){
		$_totalExtend = $this->Lesson->query(
									"SELECT
										count(*) as COUNT
									FROM homeworks as Homework
									JOIN lessons as Lesson
									ON (Lesson.id = Homework.lesson_id)
									LEFT JOIN teachers as Teacher
									ON (Teacher.id = Lesson.teacher_id)
									JOIN users as User
									ON (User.id = Lesson.user_id)
									JOIN homework_ans as HomeworkAns
									ON (HomeworkAns.homework_id = Homework.lesson_id)
									WHERE Homework.flag = 1
									AND HomeworkAns.comment = ''
									AND HomeworkAns.rate = 0
									");
		return $_totalExtend[0][0]['COUNT'];
	}
	

	# added by dave
	# 6-21-2016
	# edit age and sex pop up
	function edit_age_sex_form($userId = null,$age = null,$sex = null) {
		$this->set('userId',$userId);
		$this->set('age',$age);
		$this->set('sex',$sex);
	}

	# added by dave
	# 6-21-2016
	# edit age and sex update
	function edit_age_sex_func(){
		if(!empty($this->data)){
			$id = $this->data['Lesson']['id'];
			$age = $this->data['Lesson']['age'];
			$sex = $this->data['Lesson']['sex'];
			$this->Lesson->query("UPDATE users SET age = $age, sex = '$sex' WHERE id = $id");
		}
	}

	function teacher_management1()
	{
		//セッション情報がなくなった場合、Cubicboardのログインページに遷移する
	
		$loginInfo = $this->CheckHandler->checkLoginInfo();
		//2010-02-22 YHLEE START
	
	
		//redirect back if the user/teacher doesn't have the privilege to access this page
		if($loginInfo['management_level'] < 2 || $loginInfo['show'] != 'Y'){
			//$this->redirect(array('controller' => 'orders', 'action' => 'thanks'));
			echo "<script language='javascript'>window.history.back(-1);</script>";
		}
		//
		
		
		
	}


//2009-10-07 CUBICTALKとCUBICBOARD連動 


//2009-10-07 CUBICTALKとCUBICBOARD連動 
function teacher_mypage($teacher_id_arg = null,$search_type_arg = null,$page_arg = null)
{
	//セッション情報がなくなった場合、Cubicboardのログインページに遷移する
	$loginInfo = $this->Session->read('loginInfo');
		
	$teacher_id = $loginInfo['id'];
	
	// Call Composition teacherProceed
	$this->set("teacherProceed", $this->teacherProceed($teacher_id));

	$this->set("fixSched", $this->fixSced());
	
    //2010-02-22 YHLEE START
    $skype_id = '';
    $nick_name = '';
    $search_from = '';
    $search_to = '';
    
    //pr($this->params['form']);
    
	if($search_type_arg != null){
		$search_type = $search_type_arg;
	}else{
		$search_type = null;
		
		if(isset($this->params['form']['search_type'])){
			$search_type = $this->params['form']['search_type'];
		}else{
			$search_type = 'today_lesson';
		}
		
		//to find the skype id nick_name
		if(!empty($this->params['form']['skype_id'])){
			$skype_id = $this->params['form']['skype_id'];
		}
		
		
		if(!empty($this->params['form']['nick_name']))
		{
			$nick_name = $this->params['form']['nick_name'];
		}

	    //to find the skype id nick_name
		if(!empty($this->params['form']['search_from'])){
			$search_from = $this->params['form']['search_from'];
			//pr('tttttttttttttttttttttt'.$search_from);
			if(!empty($search_from) || $search_from != '' ){
				$search_from = sprintf("%s 02:00:00",$search_from);
			}
		}

		if(!empty($this->params['form']['search_to'])){
			$search_to = $this->params['form']['search_to'];
			
			if(!empty($search_to)){
				$search_to = sprintf("%s 02:00:00",$search_to);
			}	
		}
	}
	
	//2010-02-22 YHLEE END
	$current_timeymdhis=$this->ModelHandler->_timeymdhis;
	//2010-10-29 MODIFIED 
	$current_ymd = date("Y-m-d",strtotime($current_timeymdhis)- 60*60);	
	
	if($search_type=='last_lesson'){
		$start_time = sprintf("%s 02:00:00",'2011-08-16');
	}else if($search_type=='today_lesson'){
		$start_time = sprintf("%s 02:00:00",$current_ymd);
		//1hour plus + korean time = philiphine time
		$start_time = date("Y-m-d H:i:s",strtotime($start_time)+ 60*60);
	}else if($search_type=='tommorrow_lesson'){
		$oneday = 60*60*24;
		$tomorrow = date("Y-m-d", time()-(60*60)+ $oneday);
		$start_time = sprintf("%s 02:00:00",$tomorrow);		
	}else if($search_type=='after_today_lesson'){
		//24hours
		$oneday = 60*60*24;
		$tomorrow = date("Y-m-d", time()-(60*60)+ $oneday);
		$start_time = sprintf("%s 02:00:00",$tomorrow);
	}
	
	if( $search_type=='today_lesson'){
		
		$finish_time = sprintf("%s 00:00:00",$current_ymd);
		//1hour plus + korean time = philiphine time
		$finish_time = date("Y-m-d H:i:s",strtotime($finish_time)+ 60*60*25);
	}elseif( $search_type=='tommorrow_lesson'){
		$twoday = 60*60*24*2;
		$twodays_after = date("Y-m-d", time()-(60*60)+ $twoday);
		$finish_time = sprintf("%s 02:00:00",$twodays_after);		
	}else if($search_type=='last_lesson'){
		$finish_time = sprintf("%s 00:00:00",'2099-01-01');
		//1hour plus + korean time = philiphine time
		$finish_time = date("Y-m-d H:i:s",strtotime($finish_time)+ 60*60*25);
	}else if($search_type =='after_today_lesson'){
		$finish_time = sprintf("%s 24:00:00",'2099-01-01');
	}
	//END
	
	$search_conditions = null;

	
	$search_conditions['skype_id'] = $skype_id;
	$search_conditions['nick_name'] = $nick_name;
	$search_conditions['search_from'] = $search_from;
	$search_conditions['search_to'] = $search_to;
	
	//START from 08-25
//	global $const;
//	$this->set('const',$const);
//	
//	$configs = $this->ModelHandler->getConfig('Lesson');
//	$config = $configs[0];
//	//$config = 10;
//	$this->Session->write('config',$config);
//	
//	$configs = $this->Session->read('config');
//	$rows = $configs['Config']['teacher_lessons_rows'];
	
//	$page = null;
//	
//	$rows = 30;
//	
//	if(isset($this->params['form']['page'])){
//		$page = $this->params['form']['page'];
//		
//		if($page == null){
//			$page = null;
//		}
//	}	
//	
//	if($page_arg != null){
//		$page = $page_arg;
//	}
//	
//	
//	$total_count = $this->ModelHandler->getTeacherReservation($teacher_id,$start_time,$finish_time,'Lesson',$search_conditions,'YES',null,$rows);
//	
//
//	
//	$total_count = $total_count[0][0]['total_count'];
//	$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산 
//	if($page==null){
//		$page = 0;
//	}
//	
//	if (!$page) { $page = 1; } // 페이지가 없으면 첫 페이지 (1 페이지)
//	$from_record = ($page - 1) * $rows; // 시작 열을 구함
//	$get_paging = get_paging($rows, $page, $total_page, $search_type);
	
	
	$from_record = 0;
	
	$rows =40;
	
	$lessons = $this->ModelHandler->getTeacherReservation($teacher_id,$start_time,$finish_time,'Lesson',$search_conditions,'NO',$from_record,$rows);
	
	
	
	//try to find leveltests result
	$this->loadModel('Leveltest');
	
	$leveltests = array();
	foreach($lessons as $lesson){
		
		$user_id = $lesson['Lesson']['user_id'];
		
		$leveltest = $this->Leveltest->find('first', 
					array(    'fields' => array('Leveltest.l_t'),
					          'conditions' => array('Leveltest.user_id' => $user_id),
						      'order' => array('Leveltest.id DESC')
					));
		
	    if($leveltest != null){
	    	if($leveltest['Leveltest']['l_t'] == null){
	    		array_push($leveltests,'0');	
	    	}else{
				array_push($leveltests,$leveltest['Leveltest']['l_t']);
	    	}			
	    }else{
	    	array_push($leveltests,'No Leveltest');		
	    }
					
	}			
	
	//pr($leveltests);		
	$show_lesson_status = array('R'=>'R','F'=>'F','A'=>'A','S'=>'S','M'=>'M','E'=>'E','T'=>'T','L'=>'L','B'=>'B');
	
	//$this->set('page',$page);
	//$this->set('get_paging',$get_paging);
	$this->set('search_conditions',$search_conditions);	
	$this->set('show_lesson_status',$show_lesson_status);	
	$this->set('leveltests',$leveltests);	
	
	
	//pr($lessons);
	
	//LevelTest Results 
	
	//2011-05-01 LevelTest Sart
	//if leveltest was done , show button is showed
	
	
	//2011-01-19 lesson Count Start
	
	//$lessoncounts = $this->ModelDefaultHandler->getDefaultLessonCount($teacher_id,$start_time,$finish_time,'Lesson');
	
	//2010-01-19 lesson Count End
	
	$this->set('OriginalTeacher', $this->AllTeacher());
	
	$this->set('search_type',$search_type);	
	
	$this->set('lessons',$lessons);	
	
	$this->set('_TotalExtension',$this->extension());
	
	$this->set('_TotalHomework',$this->homework());
}


function teacher_mypage_search($from_where = null)
{
	$loginInfo = $this->Session->read('loginInfo');
	
	//2011-09-18 
    $skype_id = null;
    $nick_name = null;
    $search_from = null;
    $search_to = null;
	
    $search_type = null;
	
    //pr($leveltests);		
	$show_lesson_status = array('R'=>'R','F'=>'F','A'=>'A','S'=>'S','M'=>'M','T'=>'T','L'=>'L','E'=>'E');
	
    $teacher_id = $loginInfo['id'];

	if(!empty( $this->params['form']['search_type'])){
		$search_type = trim($this->params['form']['search_type']);		
	 }
	 
	if(!empty( $this->params['form']['skype_id'])){
		$skype_id = trim($this->params['form']['skype_id']);		
	 }
	 
	if(!empty($this->params['form']['nick_name'])){
		$nick_name = trim($this->params['form']['nick_name']);		
	 }
	 
	if(!empty($this->params['form']['search_from'])){
		$search_from = trim($this->params['form']['search_from']);		
	 }
	 
	if(!empty($this->params['form']['search_to'])){
		$search_to = trim($this->params['form']['search_to']);		
	 }
	 
	
    if($from_where != null ){
    	
    	$mypage_search = $this->Session->read('mypage_search');
    	
    	$skype_id = $mypage_search['skype_id'];
		
		$nick_name = $mypage_search['nick_name'];
		
		$search_from = $mypage_search['search_from'];
		
		$search_to = $mypage_search['search_to'];
		
    }else{
    	
		$mypage_search['skype_id'] = $skype_id;
		
		$mypage_search['nick_name'] = $nick_name;
		
		$mypage_search['search_from'] = $search_from;
		
		$mypage_search['search_to'] = $search_to;
    }
	$this->Session->write('mypage_search', $mypage_search);
	
	
	if( $search_from != null){
		
		//
		$search_from =  date("Y-m-d H:i:s",strtotime('+2 hour', strtotime($search_from)));
		
		if( $search_to == null ){
			
			 $search_to =  date("Y-m-d H:i:s",strtotime('+1 day', strtotime($search_from)));
			 
			 $search_to =  date("Y-m-d H:i:s",strtotime('+2 hour', strtotime($search_to)));
			 
		}else{
			$search_to =  date("Y-m-d H:i:s",strtotime('+2 hour', strtotime($search_to)));
		}
	}
	
	$search_conditions['skype_id'] = $skype_id;
	$search_conditions['nick_name'] = $nick_name;
	$search_conditions['search_from'] = $search_from;
	$search_conditions['search_to'] = $search_to;
	
	
	$lessons = $this->ModelHandler->getTeacherReservationSearch($teacher_id, $search_conditions , 'Lesson' );
	
	//try to find leveltests result
	$this->loadModel('Leveltest');
	
	$leveltests = array();
	foreach($lessons as $lesson){
		
		$user_id = $lesson['Lesson']['user_id'];
		
		$leveltest = $this->Leveltest->find('first', 
					array(    'fields' => array('Leveltest.l_t'),
					          'conditions' => array('Leveltest.user_id' => $user_id),
						      'order' => array('Leveltest.id DESC')
					));
		
	    if($leveltest != null){
	    	if($leveltest['Leveltest']['l_t'] == null){
	    		array_push($leveltests,'0');	
	    	}else{
				array_push($leveltests,$leveltest['Leveltest']['l_t']);
	    	}			
	    }else{
	    	array_push($leveltests,'No Leveltest');		
	    }
					
	}			
	
	$this->set('lessons',$lessons);	
	
	$this->set('search_type','mypage_search');
	
	$this->set('mypage_search',$mypage_search);
		
	$this->set('show_lesson_status',$show_lesson_status);

	$this->set('leveltests',$leveltests);	
}	


function teacher_admin()
{
	//セッション情報がなくなった場合、Cubicboardのログインページに遷移する
	$loginInfo = $this->Session->read('loginInfo');
	
	$teacher_id = $loginInfo['id'];
	
//2010-02-22 YHLEE START
	$search_type = null;
	
	if(isset($this->params['form']['search_type'])){
		$search_type = $this->params['form']['search_type'];
	}else{
		$search_type = 'today_lesson';
	}
	
	
	$current_ymd = $this->ModelHandler->_timeymd;
	
	$start_time = sprintf("%s 02:00:00",'2010-12-11');
	$finish_time = sprintf("%s 01:00:00",'2010-12-30');
	
	
	$lessons = $this->ModelHandler->getTeacherReservation($teacher_id,$start_time,$finish_time,'Lesson');
	
	$this->set('lessons',$lessons);	
}

//2010-02-03 YHLEE ADD START
//Head teacher can manage the other teacher's schedule
function teacher_management()
{
	//セッション情報がなくなった場合、Cubicboardのログインページに遷移する
	
	$loginInfo = $this->CheckHandler->checkLoginInfo();
	//2010-02-22 YHLEE START
	
	
	//redirect back if the user/teacher doesn't have the privilege to access this page
	if($loginInfo['management_level'] < 1 ){
		//$this->redirect(array('controller' => 'orders', 'action' => 'thanks'));
		echo "<script language='javascript'>window.history.back(-1);</script>";
	}
	//
	
	
	
	$search_type = null;
	
	if(isset($this->params['form']['search_type'])){
		$search_type = $this->params['form']['search_type'];
	}else{
		$search_type = 'today_lesson';
	}
	
	//2010-02-22 YHLEE END
	$current_ymd = $this->ModelHandler->_timeymd;
	if($search_type=='last_lesson' ||  $search_type =='cancel_lesson'){
		$start_time = sprintf("%s 02:00:00",$current_ymd);
	}else if($search_type=='today_lesson'){
		$start_time = sprintf("%s 02:00:00",$current_ymd);	
	}else if($search_type=='after_today_lesson'){
		//24hours
		$oneday = 60*60*24;
		$tomorrow = date("Y-m-d", time()+ $oneday);
		$start_time = sprintf("%s 02:00:00",$tomorrow);
	}else if($search_type=='change_teacher' || $search_type =='change_teacher_complete'
			|| $search_type =='cancel_lesson'){
		$start_time = sprintf("%s 02:00:00",$current_ymd);	
	}
	
	if($search_type=='last_lesson' || $search_type=='today_lesson'){
		
		//tmp excise
		//$start_time = sprintf("%s 00:00:00",'2010-09-18');
		//$finish_time = sprintf("%s 00:00:00",'2010-10-19');
		
		$finish_time = sprintf("%s 00:00:00",$current_ymd);
		//1hour plus + korean time = philiphine time
		$finish_time = date("Y-m-d H:i:s",strtotime($finish_time)+ 60*60*25);	
	}else if($search_type =='after_today_lesson'){
		$finish_time = sprintf("%s 24:00:00",'2099-01-01');
	}else if($search_type =='change_teacher' || $search_type =='change_teacher_complete'
			|| $search_type =='cancel_lesson'){
		$finish_time = sprintf("%s 24:00:00",'2099-01-01');
	}
	
	// If have change teacher request start
	//1.get a lesson information by lesson_id
		if($search_type =='change_teacher_complete'){
		$lesson_id = $this->params['form']['lesson_id'];
		$new_teacher_id = $this->params['form']['new_teacher_id'];
		$lessons = $this->ModelHandler->getLessonByLessonId($lesson_id,'Lesson'); 
		
		//1.get original teacher id and change status
		$original_teacher_id = $lessons[0]['Lesson']['teacher_id'];
		
		if($new_teacher_id != $original_teacher_id ){
			
			$update_common['id'] = $lesson_id;
			$update_common['lesson_status'] = 'S'; //substitute
			$this->Lesson->update_common('lessons',$update_common);
			
				
			//updateしたい項目をセットする(available_lesson　テーブル)
			$update_common = null;
			$update_common['id'] = $lessons[0]['Lesson']['available_lesson_id'];
			$update_common['reserve_status'] = 'S';  //subsitute
			$this->Lesson->update_common('available_lessons',$update_common);
			
			//2.check new teacher has already reserve or not with lesson time and new_teacher_id
			$search_conditions['teacher_id'] = $new_teacher_id; 
			$search_conditions['reserve_time'] = $lessons[0]['Lesson']['reserve_time'];
			$lesson_query_time = $search_conditions['reserve_time'];
			$reservation_kinds = $lessons[0]['Lesson']['reservation_kinds'];
			$spend_point = $lessons[0]['Lesson']['spend_point'];
			
			//Get User_id ID from nick_user_nick_name
			$teacher = $this->ModelHandler->get_teacherid_by_mbid($new_teacher_id, 'Lesson');
		
			$user_id = $lessons[0]['Lesson']['user_id'];
			
			
			//checking the teacher's
			$available_lesson_counts = $this->ModelHandler->get_count_available_lessons($new_teacher_id,$lesson_query_time,'Lesson');
			$lesson_counts = $available_lesson_counts[0][0]['counts'];
			//insert available_lesson
			
			 $available_lesson_Data['teacher_id'] = $new_teacher_id;
			 $available_lesson_Data['available_time'] = $lesson_query_time;
			 $available_lesson_Data['reserve_status'] = 'F';
			 $available_lesson_Data['teacher_comment'] = '';
				 
			if($lesson_counts == '0'){
					
				$this->ModelHandler->insert_available_lessons($available_lesson_Data,'Lesson');
			
			}else{
				
				$this->ModelHandler->update_available_lessons($available_lesson_Data,'Lesson');
			}
			
			//get availalesson id by teacher_id and reserve_time
			$available_lesson = $this->ModelHandler->get_available_lessons($new_teacher_id,$lesson_query_time,'Lesson');
			$available_lesson_id = $available_lesson[0]['Available_lesson']['id'];
			
			//lessonテーブルにインサーとする
			$lessonData['user_id'] = $user_id;
			$lessonData['teacher_id'] = $new_teacher_id;
			$lessonData['available_lesson_id'] = $available_lesson_id;
			$lessonData['lesson_status'] = 'R';
			$lessonData['point_id'] ='';
			
			$lessonData['comment_id'] = '';
			$lessonData['spend_point'] = $spend_point;
			$lessonData['reserve_time'] = $lesson_query_time;
			
			$lessonData['text_code'] = '01';
			
			//2010-06-17 start
			$lessonData['reservation_kinds'] = $reservation_kinds;
			//2010-06-17 end
			$this->ModelHandler->insertLesson2($lessonData,'Lesson');
		}
		//print_r($is_open_class);
	}
	// teacher request end
	
	// 2010-03-29 START
	//updateしたい項目をセットする(lesson　テーブル)
	if($search_type =='cancel_lesson'){
		$lesson_id = $this->params['form']['lesson_id'];
		$lessons = $this->ModelHandler->getLessonByLessonId($lesson_id,'Lesson');
		$update_common['id'] = $lesson_id;
		$update_common['lesson_status'] = 'H';
		$this->Lesson->update_common('lessons',$update_common);
		
		//updateしたい項目をセットする(available_lesson　テーブル)
		$update_common = null;
		$update_common['id'] = $lessons[0]['Lesson']['available_lesson_id'];
		$update_common['reserve_status'] = 'R';
		$this->Lesson->update_common('available_lessons',$update_common);
	}
	// 2010-03-29 END
	
	// 2010-09-29 START
	if($search_type =='miss_lesson'){
		$lesson_id = $this->params['form']['lesson_id'];
		$lessons = $this->ModelHandler->getLessonByLessonId($lesson_id,'Lesson');
		$update_common['id'] = $lesson_id;
		$update_common['lesson_status'] = 'M';
		$this->Lesson->update_common('lessons',$update_common);
		
		//updateしたい項目をセットする(available_lesson　テーブル)
		$update_common = null;
		$update_common['id'] = $lessons[0]['Lesson']['available_lesson_id'];
		$update_common['reserve_status'] = 'R';
		$this->Lesson->update_common('available_lessons',$update_common);
	}
	// 2010-03-29 END
	$allTeachersLessons = '';
//	$allTeachersLessons = $this->ModelHandler->getAllTeacherReservation('Lesson',$start_time,$finish_time);
	
	//2010-03-27 change teacher start
	//2010-10-20 remove the if clause
	//if($search_type =='change_teacher' ||  $search_type =='change_teacher_complete'){
		$allTeachers = $this->ModelHandler->getAllTeacher('Lesson');
		$allTeachers_select = get_all_teachers($allTeachers);
		$this->set('allTeachers',$allTeachers);	
		$this->set('allTeachers_select',$allTeachers_select);			
	//}
	//2010-03-27 change teacher end 
	$this->set('allTeachersLessons',$allTeachersLessons);
	
	$this->set('search_type',$search_type);	
	
//	2014-05-19 count the users that pay_plan != (is not equal) 'M'
	$getPayPlan = array();
	$getPayPlan = $this -> countUsersPlan ('M');
	$this -> set( 'getPayPlan', $getPayPlan );
	
//	2014-06-26 levi aviles
//	get today's class
	$this -> loadModel('Teacher');
	$this -> Lesson -> bindModel( array( 'belongsTo' => array( 'User' => array('className' => 'User', 'foreignKey' => false, 'conditions' => 'User.id = Lesson.user_id'), 'Teacher' => array( 'className' => 'Teacher', 'foreignKey' => false, 'conditions' => 'Teacher.id = Lesson.teacher_id') ) ) );
	$lesson_condition								 = array();
	$today											 = date('Y-m-d 02:00:00');
	$tomorrow										 = date('Y-m-d 01:00:00', strtotime(date('Y-m-d').'+1 day') );
	$al_today										 = date('Y-m-d', strtotime($today));
	$al_tomorrow									 = date('Y-m-d', strtotime($tomorrow));
//	$lesson_condition['Teacher.show']				 = 'Y';
	$lesson_condition['Lesson.lesson_status']		 = array('R', 'F', 'A');
	$lesson_condition['Lesson.reserve_time >= ']	 = $today;
	$lesson_condition['Lesson.reserve_time <= ']	 = $tomorrow;
	$totalTodayClass = $this -> Lesson -> find( 'all', array('fields' => array('COUNT(Lesson.id) as TotalClass'), 'conditions' => $lesson_condition) );
	$this -> set('totalTodayClass', $totalTodayClass);
	
//	get the total class of each Teachers
//	$lesson_condition								 = array();
//	$lesson_condition['Lesson.reserve_time >= ']	 = $today;
//	$lesson_condition['Lesson.reserve_time <= ']	 = $tomorrow;
	$toSort = "TotalClass DESC";
	if ( isset($this -> params['form']['toSort']) ) {
		$toSort = $this -> params['form']['toSort'];
	}

	$this -> Lesson -> bindModel( array( 'belongsTo' => array( 'User' => array('className' => 'User', 'foreignKey' => false, 'conditions' => 'User.id = Lesson.user_id'), 'Teacher' => array( 'className' => 'Teacher', 'foreignKey' => false, 'conditions' => 'Teacher.id = Lesson.teacher_id') ) ) );
	$TeacherClass = $this -> Lesson -> find( 'all', array('fields' => array(
				'Teacher.id',
				'Teacher.tea_name', 
				'COUNT(*) as TotalClass',
				"(SELECT COUNT(*) FROM available_lessons as Available_lesson WHERE Available_lesson.teacher_id = Teacher.id AND DATE( Available_lesson.available_time ) >= '$al_today' AND DATE( Available_lesson.available_time ) < '$al_tomorrow') as TotalSched",
				"ROUND(((COUNT(*) / (SELECT COUNT(*) FROM available_lessons as Available_lesson WHERE Available_lesson.teacher_id = Teacher.id AND DATE( Available_lesson.available_time ) >= '$al_today' AND DATE( Available_lesson.available_time ) < '$al_tomorrow')) * 100)) as TotalRate"), 'conditions' => $lesson_condition, 'group' => 'Teacher.id', 'order' => $toSort) );
	$this -> set('TeacherClass', $TeacherClass);
	
	$GetAllReservationKinds = $this->Programmer1Handler->dictionary('reservation_kind');
	$this->set('GetAllReservationKinds',$GetAllReservationKinds);
}

function availableTeachers(){
	if($this -> RequestHandler -> isAjax()){
		//Configure::write('debug',0);
		$reserveTime = $_POST['reserveTime'];
		$index = $_POST['index'];
		//echo $reserveTime;
		$this -> loadModel('Available_lesson');
		$this -> Available_lesson -> bindModel(array('belongsTo' => array('Teacher' => array('className' => 'Teacher', 'foreignKey' => 'teacher_id'))));
		
		$availableTeacherList = $this -> Available_lesson -> find('all', array('fields' => array('Teacher.tea_name', 'Available_lesson.teacher_id'), 'conditions' => array('Teacher.show' => 'Y','Available_lesson.available_time' => $reserveTime, 'Available_lesson.reserve_status' => 'R')));
		if(!empty($availableTeacherList)){
			echo 'Available teacher/s:<hr />';
			foreach($availableTeacherList as $a)
				echo "<input type = 'radio' name = 'availableTeacher' id = 'availableTeacher' value = '".$a['Available_lesson']['teacher_id']."' />".$a['Teacher']['tea_name'].'<br />';
		}else 
			echo "<input type = 'radio' name = 'availableTeacher' id = 'availableTeacher' style = 'display: none' /><h3>No teacher available!</h3>";
		
		echo "<a href = 'javascript: closeLightbox($index)'>close</a>";
	}
}

//2010-12-15 YHLEE ADD START
//find lesson search condition is teacher_id , user_nick_name, user_skype_id, lesson_date_from, lesson_date_to
function teacher_search_lessons($fromwhre = null)
{
	$loginInfo = $this->CheckHandler->checkLoginInfo();
	//2010-02-22 YHLEE START
	$search_type = null;
	
	if(isset($this->params['form']['search_type'])){
		$search_type = $this->params['form']['search_type'];
	}else{
		$search_type = 'today_lesson';
	}
	
	$current_ymd = $this->ModelHandler->_timeymd;

	if($fromwhre != null){
		$session_search = $this->Session->read('inputted_datas');
		$teacher_id = $session_search['teacher_id'];
		$nick_name = $session_search['nick_name'];
		$skype_id = $session_search['skype_id'];
		$start_date = $session_search['start_date'];
		$last_date = $session_search['last_date'];
		$pay_plan = $session_search['pay_plan'];
		$reservation = $session_search['reservation'];
	}else{
		$teacher_id = $this->params['form']['teacher_id'];
		$nick_name = $this->params['form']['nick_name'];
		$skype_id = $this->params['form']['skype_id'];
		$start_date = $this->params['form']['start_date'];
		$last_date = $this->params['form']['last_date'];
		$pay_plan = $this->params['form']['pay_plan'];
		$reservation = $this->params['form']['reservation'];
		
		
		if($teacher_id == 'all'){
			$teacher_id = null;
		}
		//
		//$this->set('search_conditions',$search_conditions);
		$inputted_datas['teacher_id'] = $teacher_id;
		$inputted_datas['nick_name'] = $nick_name;
		$inputted_datas['skype_id'] = $skype_id;
		$inputted_datas['start_date'] = $start_date;
		$inputted_datas['last_date'] = $last_date;
		$inputted_datas['pay_plan'] = $pay_plan;
		$inputted_datas['reservation'] = $reservation;
		
		$this->Session->write('inputted_datas',$inputted_datas);
	}
	//receiving the parameters from form
	//date change from web format to DB format
//	if($start_date != null){
//		$start_date = change_to_db_date($start_date,'YMD','nohyph');
//	}
//	if($last_date != null){
//		$last_date = change_to_db_date($last_date,'YMD','nohyph');
//	}
	
	
	
	$search_conditions['teacher_id'] = $teacher_id;
	$search_conditions['nick_name'] = $nick_name;
	$search_conditions['skype_id'] = $skype_id;
	$search_conditions['start_date'] = $start_date;
	$search_conditions['last_date'] = $last_date;
	$search_conditions['pay_plan'] = $pay_plan;
	$search_conditions['reservation'] = $reservation;
	
	//print_r(substr($start_date,4,2));
	//print_r($last_date);
	//print_r($skype_id);
	
	// 2011-06-11
	if($pay_plan == 'M'){
		
	}
	
	//teacher search lesson
	$searchLessons = $this->ModelHandler->getSearchLessons('Lesson',$search_conditions);
	
	//all teachers
	$allTeachers = $this->ModelHandler->getAllTeacher('Lesson');
	
	//2010-03-27 change teacher end
	//검색조건
	//pr($searchLessons);
	// 2011-06-11
	
	//try to find leveltests result
	$this->loadModel('Leveltest');
	
	$leveltests = array();
	foreach($searchLessons as $lesson){
		
		$user_id = $lesson['Lesson']['user_id'];
		
		$leveltest = $this->Leveltest->find('first', 
					array(    'fields' => array('Leveltest.l_t'),
					          'conditions' => array('Leveltest.user_id' => $user_id),
						      'order' => array('Leveltest.id DESC')
					));
		
	    if($leveltest != null){
	    	if($leveltest['Leveltest']['l_t'] == null){
	    		array_push($leveltests,'0');	
	    	}else{
				array_push($leveltests,$leveltest['Leveltest']['l_t']);
	    	}			
	    }else{
	    	array_push($leveltests,'No Leveltest');		
	    }
					
	}			
	
	$risky_members = array();
	
	if($pay_plan == 'M'){
		
		
		//pr($searchLessons);
		
		foreach($searchLessons as $searchLesson){
			
			$search_conditions = array('mb_id'=>null,'mb_id2'=>null,'mb_login_ip'=>null );
			
			$mb_id = $searchLesson['User']['mb_id'];
			
			$search_conditions['mb_id'] = $mb_id;
			
			$members = $this->ModelDefaultHandler->getDefaultG4_member($search_conditions,'Lesson');
			
			$member = $members[0];
			
			//pr($member);
			
			$mb_login_ip = $member['g4_member']['mb_login_ip'];
			
			$search_conditions = array('mb_id'=>null,'mb_id2'=>null,'mb_login_ip'=>null);
			
			$search_conditions['mb_id2'] = $mb_id;
			$search_conditions['mb_login_ip'] = $mb_login_ip;
			
			$same_members = $this->ModelDefaultHandler->getDefaultG4_member($search_conditions,'Lesson');
			
			if(sizeof($same_members)!= 0){
				$temp_members[$mb_id] = $same_members; 
				array_push($risky_members,$temp_members);
			}
			
			
			//pr($same_members);
      }
		//pr($risky_members);	
	}
	
	// 2011-07-10 start
	
	$change_kinds = array('S'=>'Substitute','M'=>'Missing','SM'=>'Subs-by-Mgmt');
	
	$this->set('change_kinds',$change_kinds);
	
	$this->set('risky_members',$risky_members);
	
	$this->set('searchLessons',$searchLessons);
	$this->set('allTeachers',$allTeachers);	
	$this->set('search_type',$search_type);

	$this->set('leveltests',$leveltests);
	
	$GetAllReservationKinds = $this->Programmer1Handler->dictionary('reservation_kind');
	$this->set('GetAllReservationKinds',$GetAllReservationKinds);
}

//
function teacher_change_lessons()
{
	$loginInfo = $this->CheckHandler->checkLoginInfo();
	//2010-02-22 YHLEE START
	
	$search_type = null;
	
	$current_ymd = $this->ModelHandler->_timeymd;

	// If have change teacher request start
	//1.get a lesson information by lesson_id
	
		$lesson_id = $this->params['form']['lesson_id'];
		//2011-07-10
		$chnage_kinds = $this->params['form']['change_kinds'];
		
		$new_teacher_id = $this->params['form']['new_teacher_id'];
		$lessons = $this->ModelHandler->getLessonByLessonId($lesson_id,'Lesson'); 
		
		//1.get original teacher id and change status
		$original_teacher_id = $lessons[0]['Lesson']['teacher_id'];
		
		
		
		
		
		
		if($new_teacher_id != $original_teacher_id ){
			
			$this->Programmer1Handler->ActionDetails($loginInfo['id'], $lesson_id, 'Substitute', $this->AllTeacher(), $this->RequestHandler->getClientIp(), $original_teacher_id, $new_teacher_id);
			
			$update_common['id'] = $lesson_id;
			$update_common['lesson_status'] = $chnage_kinds; //substitute or substitute by management
			$this->Lesson->update_common('lessons',$update_common, $original_teacher_id);
			
				
			//updateしたい項目をセットする(available_lesson　テーブル)
			$update_common = null;
			$update_common['id'] = $lessons[0]['Lesson']['available_lesson_id'];
			
			//2011-07-10
			$update_common['reserve_status'] = $chnage_kinds;  //subsitute
			
			$this->Lesson->update_common('available_lessons',$update_common);
			
			//2.check new teacher has already reserve or not with lesson time and new_teacher_id
			$search_conditions['teacher_id'] = $new_teacher_id; 
			$search_conditions['reserve_time'] = $lessons[0]['Lesson']['reserve_time'];
			$lesson_query_time = $search_conditions['reserve_time'];
			$reservation_kinds = $lessons[0]['Lesson']['reservation_kinds'];
			$spend_point = $lessons[0]['Lesson']['spend_point'];
			$lesson_status = $lessons[0]['Lesson']['lesson_status'];
			
			
			//Get User_id ID from nick_user_nick_name
			$teacher = $this->ModelHandler->get_teacherid_by_mbid($new_teacher_id, 'Lesson');
		
			$user_id = $lessons[0]['Lesson']['user_id'];
			
			
			//checking the teacher's
			$available_lesson_counts = $this->ModelHandler->get_count_available_lessons($new_teacher_id,$lesson_query_time,'Lesson');
			$lesson_counts = $available_lesson_counts[0][0]['counts'];
			//insert available_lesson
			
			 $available_lesson_Data['teacher_id'] = $new_teacher_id;
			 $available_lesson_Data['available_time'] = $lesson_query_time;
			 $available_lesson_Data['reserve_status'] = 'F';
			 $available_lesson_Data['teacher_comment'] = '';
				 
			if($lesson_counts == '0'){
					
				$this->ModelHandler->insert_available_lessons($available_lesson_Data,'Lesson');
			
			}else{
				
				$this->ModelHandler->update_available_lessons($available_lesson_Data,'Lesson');
			}
			
			//get availalesson id by teacher_id and reserve_time
			$available_lesson = $this->ModelHandler->get_available_lessons($new_teacher_id,$lesson_query_time,'Lesson');
			$available_lesson_id = $available_lesson[0]['Available_lesson']['id'];
			
			//lessonテーブルにインサーとする
			$lessonData['user_id'] = $user_id;
			$lessonData['teacher_id'] = $new_teacher_id;
			$lessonData['available_lesson_id'] = $available_lesson_id;
			
			//change not always R to lesson status
			$lessonData['lesson_status'] = $lesson_status;
			$lessonData['point_id'] ='';
			
			$lessonData['comment_id'] = '';
			$lessonData['spend_point'] = $spend_point;
			$lessonData['reserve_time'] = $lesson_query_time;
			
			$lessonData['text_code'] = '01';
			
			//2010-06-17 start
			$lessonData['reservation_kinds'] = $reservation_kinds;
			//2010-06-17 end
//			2014-11-18 Levi Payment Id
			$lessonData['payment_id'] = $lessons[0]['Lesson']['payment_id'];
			
			$lessonData['original_teacher_id'] = $original_teacher_id;

			
			$this->ModelHandler->insertLesson2($lessonData,'Lesson');
			
			//update comments
			//2011-03-28 START
			
			//2011-03-28 END
		}
		
		//redirect the search condditions
		$this->redirect(array('action' => 'teacher_search_lessons/change/'.$lesson_id));
} // 

//each lesson extend when they click the button
function teacher_extend_lessons()
{
	$loginInfo = $this->CheckHandler->checkLoginInfo();
	//2010-02-22 YHLEE START
	
	$search_type = null;
	
	$current_ymd = $this->ModelHandler->_timeymd;
	
	$session_search = $this->Session->read('inputted_datas');
	
	$teacher_id = $session_search['teacher_id'];
	$nick_name = $session_search['nick_name'];
	$skype_id = $session_search['skype_id'];
	$start_date = $session_search['start_date'];
	$last_date = $session_search['last_date'];
	
	//2011-02-03 add start
	$extend_type2 = $this->params['form']['extend_type2'];
	//2011-02-03 add end
	
	// 1. get a lesson information by lesson_id
	$from_where = $this->params['form']['from_where'];
	$lesson_id = $this->params['form']['lesson_id'];
	//2011-01-29 Add Start
	$extention_kinds = $this->params['form']['extention_kinds'];
	//2011-01-29 Add End
	
	$lessons = $this->ModelHandler->getLessonByLessonId($lesson_id,'Lesson');
	$lesson = $lessons[0];
	
	$teacher_id = $lesson['Lesson']['teacher_id'];
	$user_id = $lesson['Lesson']['user_id'];

	// Get Teacher Information
	$search_conditions_t['teacher_id'] = $teacher_id;
	$teachers = $this->ModelDefaultHandler->getDefaultTeacher($search_conditions_t,'Lesson');
	$teacher = $teachers[0];
		 		
	// users Information
	$search_conditions_u['user_id'] = $user_id;
	$search_conditions_u['user_nick_name'] = null;
	$users = $this->ModelDefaultHandler->getDefaultUser($search_conditions_u,'Lesson');
	$user = $users[0];
		
	// message initialize
	$message = '';
	$message2 = '';
	$sms_message_content = '';
		 
	// GET PAYMENT INFO
	$this->loadModel('Payment');
	$payment_info = $this->Payment->find('first',array('fields'=>array('*'),'conditions'=>array('id'=>$lesson['Lesson']['payment_id'])));
		 
	$this->set('PaymentMessage',$this->Programmer1Handler->PaymentExtend($lesson['Lesson']['payment_id']));

	// check the extends table
	$this->loadModel('Extention');
	$extention_count = $last_lesson = $this->Extention->find('count',array('conditions' => array('Extention.lesson_id' => $lesson['Lesson']['id'])));

	if($extention_count == 0){
		// Lesson Satus
		$update_common['id'] = $lesson_id;

		// 2011-01-29 Updated
		$update_common['lesson_status'] = $extention_kinds; //Extended OR MISSING
		$this->Lesson->update_common('lessons',$update_common);
		// 2011-01-29 Updated End
		 
		// updateしたい項目をセットする(available_lesson　テーブル)
		$update_common = null;
		$update_common['id'] = $lesson['Lesson']['available_lesson_id'];
		$update_common['reserve_status'] = $extention_kinds;  //Extended OR MISSING
		$this->Lesson->update_common('available_lessons',$update_common);
		 		
		$insert_conditions['lesson_id'] = $lesson['Lesson']['id'];
		$insert_conditions['pay_plan'] = $user['User']['pay_plan'];
		$insert_conditions['reservation_kinds'] = $lesson['Lesson']['reservation_kinds'];
		$insert_conditions['teacher_name'] = $teacher['Teacher']['tea_name'];
		$insert_conditions['reserve_time'] = $lesson['Lesson']['reserve_time'];
		$insert_conditions['reason'] = $extention_kinds; //extended , missing
		 
		$insert_conditions['user_id'] = $user['User']['id'];
		$insert_conditions['user_nick_name'] = $user['User']['nick_name'];
		$insert_conditions['pay_plan'] = $user['User']['pay_plan'];
		$insert_conditions['tarket_day'] = $lesson['Lesson']['reserve_time'];
		 	 
		$last_day = $user['User']['last_day'];
		$extend_day = date('Y-m-d H:i:s',strtotime('+1 day', strtotime($last_day)));

		$insert_conditions['last_day'] = $last_day;
		$insert_conditions['extend_day'] = $extend_day;
		$insert_conditions['who_extend'] = $loginInfo['id'];

		// ex_count to save excount
		$before_ex_count = $user['User']['ex_count'];
		$ex_count = $user['User']['ex_count'];
		$pay_plan = $user['User']['pay_plan'];

		// to point insert
		$mb_id = $user['User']['mb_id'];
		$user_id = $user['User']['id'];
		$ct = get_time();
		$current_time =   $ct['time_ymdhis'] ;
		$reserve_time = $lesson['Lesson']['reserve_time'];
		$content = "{$reserve_time}수업연기";
		$spent_point = $lesson['Lesson']['spend_point'];
		$reservation_kinds = $lesson['Lesson']['reservation_kinds'];
		
		// For Pay Plan Goods Name E1 or E2 return to POINT 10pt.
		if(isset($payment_info['Payment']) && in_array($payment_info['Payment']['goods_name'],array('E1','E2'))){
			$sql = " insert into g4_point
			set mb_id = '$mb_id',
			po_datetime = '$current_time',
			po_content = '$content',
			po_point = '$spent_point',
			po_rel_table = 'lessons',
			po_rel_id = '',
			po_rel_action = '수업' ";
			$free_sql_result = $this->Lesson->free_sql($sql);
		 			
			// In case of point the sume point
			$sql = " select sum(po_point) AS point from g4_point where mb_id = '{$mb_id}' ";
			$point_results = $this->Lesson->free_sql($sql);
		 			
			foreach($point_results as $point_result){
				$point = $point_result[0]['point'];
			}
		 			
			// Update g4_member
			$sql = " update g4_member SET mb_point = $point WHERE mb_id = '$mb_id' ";
			$this->Lesson->free_sql($sql);
		 		 
			$insert_conditions['description'] = $point;
			$this->ModelDefaultHandler->insertExtention($insert_conditions,'Lesson');
			
			$update_conditions['user_id'] = $user['User']['id'];
			$update_conditions['last_day'] = $extend_day;
			$update_conditions['ex_count'] = $ex_count;
			
			$this->ModelDefaultHandler->updateUser($update_conditions,'Lesson');
		 		
			$reserve_time_point = date('Y-m-d H:i', strtotime($insert_conditions['reserve_time']));
		 		
			$message = "큐빅토크입니다 포인트수업연장 {$insert_conditions['teacher_name']} {$reserve_time_point} 죄송합니다";
			$message2 = "큐빅토크입니다 포인트수업연장 {$insert_conditions['teacher_name']} {$reserve_time_point} 죄송합니다";
			$sms_message_content = "큐빅토크입니다 포인트수업연장 {$insert_conditions['teacher_name']} {$reserve_time_point} 죄송합니다";
		 }
		 else {
			// Extend User's last day
			// ONLY COMA resrvation increase only
			if($extend_type2 == 'K' && ($reservation_kinds == 'K')){
				$update_conditions['user_id'] = $user['User']['id'];
				$update_conditions['ex_count'] = null;
		 		
				if($pay_plan == 'E1'){
					$update_conditions['ex_count'] = 0;
					$update_conditions['last_day'] = $extend_day;
				}
		 		// 2011-01-29 start
				if($pay_plan == 'E2' || $pay_plan == 'E3' || $pay_plan == 'E4'){
					$ex_count = $ex_count+10;
					// Do not change the last day
					$update_conditions['last_day'] = $last_day;

					if($ex_count == 20){ // E2
						if($pay_plan == 'E2'){
							$update_conditions['last_day'] = $extend_day;
							$ex_count = 0;
						}
					}
					elseif($ex_count == 30){ // E3
						if($pay_plan == 'E3'){
							$update_conditions['last_day'] = $extend_day;
							$ex_count = 0;
						}
					}
					else if($ex_count == 40){ // E4
						if($pay_plan == 'E4'){
							$update_conditions['last_day'] = $extend_day;
							$ex_count = 0;
						}
					}
					
					$update_conditions['ex_count'] = $ex_count;
				}

		 		//2012-12-17 UPDATE
				$extend_msg = "{$before_ex_count},{$ex_count}";
				$insert_conditions['description'] = $extend_msg;

				$this->ModelDefaultHandler->insertExtention($insert_conditions,'Lesson');
				$this->ModelDefaultHandler->updateUser($update_conditions,'Lesson');

				$reserve_time_coupon = date('Y-m-d H:i', strtotime($insert_conditions['reserve_time']));
		 			 
				$message = "큐빅토크입니다 큐빅수업연장 {$insert_conditions['teacher_name']} {$reserve_time_coupon} 죄송합니다";
				$message2 = "큐빅토크입니다 큐빅수업연장 {$insert_conditions['teacher_name']} {$reserve_time_coupon} 죄송합니다";
				$sms_message_content = "큐빅토크입니다 큐빅수업연장 {$insert_conditions['teacher_name']} {$reserve_time_coupon} 죄송합니다";
			}
			// End if($pay_plan == 'E2' || $pay_plan == 'E3' || $pay_plan == 'E4'){}

			// 2011-11-14 modified
			// point plan test user incresed point
			if($extend_type2 == 'P' || $reservation_kinds == 'P'){
				$sql = " insert into g4_point
				set mb_id = '$mb_id',
				po_datetime = '$current_time',
				po_content = '$content',
				po_point = '$spent_point',
				po_rel_table = 'lessons',
				po_rel_id = '',
				po_rel_action = '수업' ";
				$free_sql_result = $this->Lesson->free_sql($sql);
		 				
				// In case of point the sume point
				$select_string = "sum(po_point) AS point ";
				$table = "g4_point ";
				$where_string =  "mb_id = '$mb_id' ";
				$point_results = $this->Lesson->select_free_common($select_string,$table,$where_string);

				foreach($point_results as $point_result){
					$point = $point_result[0]['point'];
				}
		 				
				// Update g4_member
				$sql =  "update g4_member
				SET mb_point = $point
				WHERE mb_id = '$mb_id'";
				$this->Lesson->free_sql($sql);
		 			 
				$insert_conditions['description'] = $point;
				$this->ModelDefaultHandler->insertExtention($insert_conditions,'Lesson');

				$reserve_time_point = date('Y-m-d H:i', strtotime($insert_conditions['reserve_time']));
		 		
				$message = "큐빅토크입니다 포인트수업연장 {$insert_conditions['teacher_name']} {$reserve_time_point} 죄송합니다";
				$message2 = "큐빅토크입니다 포인트수업연장 {$insert_conditions['teacher_name']} {$reserve_time_point} 죄송합니다";
				$sms_message_content = "큐빅토크입니다 포인트수업연장 {$insert_conditions['teacher_name']} {$reserve_time_point} 죄송합니다";
			}
			// End if($extend_type2 == 'P' || $reservation_kinds == 'P'){}
			
			// In case of fixed student
			if($extend_type2 == 'M' || $reservation_kinds == 'M' || $reservation_kinds == 'MD'){

				// 1.Find the last class
				// change for user for user get a lesson more than one time find the same tiem
				// last_lesson may result 2015 or less than today; prevent date less than today by conditional greater than equal today
				$reserve_only_time =  date("H:i:s", strtotime($reserve_time));

				$last_lesson = $this->Lesson->find('first',
					array('conditions' => array('Lesson.user_id' => $user_id
						,'OR' => array(array('Lesson.reservation_kinds'=>'M'), array('Lesson.reservation_kinds'=>'MD'))
							,'Lesson.lesson_status' => 'R'
							,'Lesson.reserve_time LIKE' => '%'.$reserve_only_time)
							,'Lesson.reserve >=' => "'{$current_ymd}'"
							,'order' => array('Lesson.reserve_time DESC')
				));

				$this->loadModel('Available_lesson');
				$available_lesson_id = $last_lesson['Lesson']['available_lesson_id'];
				$last_Available_lesson = $this->Available_lesson->find('first',array('conditions' => array('Available_lesson.id' => $available_lesson_id)));
				
				// If last_lesson is nothing execute the else and it will started tomorrow
				if(isset($last_lesson['Lesson'])){
					$last_lesson_time = $last_lesson['Lesson']['reserve_time'];
				}
				else {
					// Get the lesson information this lesson_id 
					$last_lesson['Lesson'] = $lesson['Lesson'];
					$last_lesson_time = date('Y-m-d ' + $reserve_only_time,strtotime('+1 days'));
				}
				
				//check current lesson and last lesson
				if($last_lesson_time == $reserve_time){ }
				// 2.insert class after last class + oneday

				// fixed student is no sat sunday class so check the satday or sunday
				$increasing_day = 1;

				// 0 is sunday and 6 is saturday
				$check = true;
				$extend_only_day =  $last_lesson_time;
				while($check == true):
		 			$extend_only_day =  date('Y-m-d',strtotime('+1 day', strtotime($extend_only_day)));
		 			$date = date('w',strtotime($extend_only_day));
		 			// identify 00:00,00:30,01:00,...,04:30 or greater than 05:00
		 			// Convert to long time it will become 24:00 (MidNight) to greater than 29:00 (05:00) AM
		 			$hours=(date('G',strtotime($last_lesson_time)) + 24);
		 			if($hours >= 24 && $hours < 29){ // 24:00 (00:00 AM) and 29:00 (05:00 AM)
		 				if(!($date == 0 || $date == 1)){$check = false;} // No sunday and No monday
		 			}else{
		 				if(!($date == 0 || $date == 6)){$check = false;} //sunday saturday
		 			}
				endwhile;
		 			 
				$extend_day =  sprintf("%s %s",$extend_only_day,$reserve_only_time);
		 			 
				$last_Available_lesson['Available_lesson']['id']= null;
				$last_Available_lesson['Available_lesson']['available_time'] = $extend_day;
				$this->Available_lesson->save($last_Available_lesson);
				$lastInsertId= $this->Available_lesson->getLastInsertId();
		 			 
				// lesson table
				$last_lesson['Lesson']['id'] = null;
				$last_lesson['Lesson']['reserve_time'] = $extend_day;
				$last_lesson['Lesson']['teacher_id'] = $teacher_id;
				$last_lesson['Lesson']['lesson_status'] = 'R';
				$last_lesson['Lesson']['payment_id'] = $payment_info['Payment']['id'];
				$last_lesson['Lesson']['available_lesson_id'] = $lastInsertId;
		 		
				// $last_lesson['Lesson']['created'] = date('Y-m-d H:i:s');
				$last_lesson['Lesson']['modified'] = date('Y-m-d H:i:s');
				$this->Lesson->save($last_lesson);

				$insert_conditions['description'] = $reserve_time.','.$extend_day;
				$this->ModelDefaultHandler->insertExtention($insert_conditions,'Lesson');
				$message = "{$reserve_time}이 연장되어 {$extend_day}수업이 추가 되었습니다";
				$message2 = "큐빅토크입니다 {$teacher['Teacher']['tea_name']} " . date('m-d hi',strtotime($reserve_time)).'이연장되어 '.date('m-d hi',strtotime($extend_day)).'수업이추가되었습니다';
		 			 
				// Tell day to today
				$teacher_name = explode(' ',$teacher['Teacher']['tea_name']);
				$sms_message_content = 'T.' . ucwords($teacher_name[0]) . ' ';
				$name_of_days = '';
				if(isset($this->Programmer1Handler->name_of_days['ko'])
						&& is_array($this->Programmer1Handler->name_of_days['ko'])){
					$name_of_days = "({$this->Programmer1Handler->name_of_days['ko'][date('w',strtotime($reserve_time))]})";
				}
				$sms_message_content .= date("m-d{$name_of_days} H:i", strtotime($reserve_time)) . '이 연장되어 ';
				$name_of_days = '';
				if(isset($this->Programmer1Handler->name_of_days['ko'])
						&& is_array($this->Programmer1Handler->name_of_days['ko'])){
					$name_of_days = "({$this->Programmer1Handler->name_of_days['ko'][date('w',strtotime($extend_day))]})";
				}
				$sms_message_content .= date("m-d{$name_of_days} H:i", strtotime($extend_day)) . '수업이 추가되었습니다';
				$this->Programmer1Handler->ActionDetails($loginInfo['id'], $lesson_id, 'Extend', $this->AllTeacher(), $this->RequestHandler->getClientIp(), null,null, $extention_kinds);
			}
			// End if($extend_type2 == 'M' || $reservation_kinds == 'M' || $reservation_kinds == 'MD'){}
		}
		// if(isset($payment_info['Payment']) && in_array($payment_info['Payment']['goods_name'],array('E1','E2'))){}
		 
		// Get the information after Updating User Information
		$users = $this->ModelDefaultHandler->getDefaultUser($search_conditions_u,'Lesson');
		$user = $users[0];

		
		$this->set('lesson',$lesson);
		$this->set('teacher',$teacher);
		$this->set('last_day',$last_day);
		$this->set('insert_conditions',$insert_conditions);
		$this->set('ex_count',$ex_count);
	}else{
		$message = 'this lesson already extended !! ';
	}
	// 

	$this->set('user',$user);
	$this->set('extention_count',$extention_count);
	$this->set('message',$message);
	$this->set('message2',$message2);
	$this->set('sms_message_content',$sms_message_content);
}
// End function teacher_extend_lessons()

function teacher_extend_all()
{
 $loginInfo = $this->CheckHandler->checkLoginInfo();
//  2010-02-22 YHLEE START
 $search_type = null;
 $current_ymd = $this->ModelHandler->_timeymd;
//  Read Session
 $session_search = $this->Session->read('inputted_datas');
 $teacher_id = $session_search['teacher_id'];
 $nick_name = $session_search['nick_name'];
 $skype_id = $session_search['skype_id'];
 $start_date = $session_search['start_date'];
 $last_date = $session_search['last_date'];
//  2011-02-03 add start
 $extend_type2 = $this->params['form']['extend_type2'];
//  2011-02-03 add end
//  1.get a lesson information by lesson_id
 $from_where = $this->params['form']['from_where'];
 $lesson_id = $this->params['form']['lesson_id'];
//  2013-01-03 Add Start
 $extention_kinds = $this->params['form']['all_extend'];
//  2013-01-03 Add End
//  2012-01-03 YHLEE MODIFIED
 $lesson_id_array = ($this->params['form']['lesson_id']);
//  Lesson Satus
 $i=0;
//  pr($lesson_id_array);
//  message initialize;
 $current_datetime=date('Y-m-d H:i:s');
 $messages = array();
 $longTermPaymentId=array();
 foreach($lesson_id_array as $lesson_id){
  $lessons = $this->ModelHandler->getLessonByLessonId($lesson_id,'Lesson'); 
//   1.get original teacher id and change status
  $lesson = $lessons[0];
//   check the extends table 
  $this->loadModel('Extention');
  $last_lesson = $this->Extention->find('count',array('conditions'=>array('Extention.lesson_id'=>$lesson['Lesson']['id'])));
  $extention_count = $last_lesson;
  if($extention_count == 0 && $lesson['Lesson']['lesson_status'] == 'R'){
  	
   $teacher_id = $lesson['Lesson']['teacher_id'];
   $user_id = $lesson['Lesson']['user_id'];
   $payment_id = $lesson['Lesson']['payment_id'];
//    Lesson Satus 
   $update_common['id'] = $lesson_id;
//     2011-01-29 Updated
   $update_common['lesson_status'] = $extention_kinds; //Extended OR MISSING
   $not_kinds_M2_M3 = array("M2", "M3");
   if(!in_array($lesson['Lesson']['reservation_kinds'], $not_kinds_M2_M3)){
    $extension_status = array();
    $extension_status["modified"] = "'".$current_datetime."'";
    if(isset($this->params["form"]["extension_status"]) && strlen($this->params["form"]["extension_status"]) > 0){
     $extension_status["lesson_status"] = "'".$this->params["form"]["extension_status"]."'";
    } // End if(isset($this->params["form"]["extension_status"]) && strlen($this->params["form"]["extension_status"]) > 0){};
    
    $this->Lesson->updateAll($extension_status,array('id' => $lesson['Lesson']['id']));
    $availableLesson_del = array();
    $availableLesson_del["id"] = $lesson['Lesson']['available_lesson_id'];
    $this->loadModel("Available_lesson");
	$this->Available_lesson->delete($availableLesson_del);
    if(isset($this->params["form"]["longtermHolidaySetting"]) && $this->params["form"]["longtermHolidaySetting"] == "longtermHolidaySetting"){
     if(!in_array($lesson['Lesson']['payment_id'], $longTermPaymentId)){
      $searchDataForLongTerm = array();
      $dataForLongTerm["Lesson.payment_id"] = $lesson['Lesson']['payment_id'];
      $maxLongterm = $this->Lesson->find("all",array("fields" => array("MAX(Lesson.long_term) as max_long_term"),"conditions" => $dataForLongTerm));
      $longTermPaymentId[] = $lesson['Lesson']['payment_id'];
      $maxLongterm = $maxLongterm[0][0]["max_long_term"] + 1;
     } // End if(!in_array($lesson['Lesson']['payment_id'], $longTermPaymentId)){};
//      update lesson long_term
     $updateDataLongTerm = array();
     $updateDataLongTerm["Lesson"]["id"] = $lesson['Lesson']['id'];
     $updateDataLongTerm["Lesson"]["long_term"] = $maxLongterm;
     $updateDataLongTerm["Lesson"]["created"] = $current_datetime;
     $updateDataLongTerm["Lesson"]["modified"] = $current_datetime;
     $this->Lesson->save($updateDataLongTerm);
    } // End if(isset($this->params["form"]["longtermHolidaySetting"]) && $this->params["form"]["longtermHolidaySetting"] == "longtermHolidaySetting"){}
   } // End if(!in_array($lesson['Lesson']['reservation_kinds'], $not_kinds_M2_M3)){};
//    2011-01-29 Updated End

//    Get Teacher Information
   $search_conditions_t['teacher_id'] = $teacher_id;
   $teachers = $this->ModelDefaultHandler->getDefaultTeacher($search_conditions_t,'Lesson');
   $teacher = $teachers[0];
   
//    Get User Information
   $search_conditions_u['user_id'] = $user_id;
   $search_conditions_u['user_nick_name'] = null;
   $users = $this->ModelDefaultHandler->getDefaultUser($search_conditions_u,'Lesson');
   $user = $users[0];
				
   $insert_conditions['lesson_id'] = $lesson['Lesson']['id'];
   $insert_conditions['pay_plan'] = $user['User']['pay_plan'];
   $insert_conditions['reservation_kinds'] = $lesson['Lesson']['reservation_kinds'];
   $insert_conditions['teacher_name'] = $teacher['Teacher']['tea_name'];
   $insert_conditions['reserve_time'] = $lesson['Lesson']['reserve_time'];
   $insert_conditions['reason'] = $extention_kinds; //extended , missing
   $insert_conditions['user_id'] = $user['User']['id'];
   $insert_conditions['user_nick_name'] = $user['User']['nick_name'];
   $insert_conditions['pay_plan'] = $user['User']['pay_plan'];
   $insert_conditions['tarket_day'] = $lesson['Lesson']['reserve_time'];
   $last_day = $user['User']['last_day'];
   $extend_day = date("Y-m-d H:i:s",strtotime('+1 day', strtotime($last_day)));
   $insert_conditions['last_day'] = $last_day;
   $insert_conditions['extend_day'] = $extend_day;
   $insert_conditions['who_extend'] = $loginInfo['id'];
//    ex_count
//    to save excount
   $before_ex_count = $user['User']['ex_count'];
   $ex_count = $user['User']['ex_count'];
   $pay_plan = $user['User']['pay_plan'];
//    to point insert 
   $mb_id = $user['User']['mb_id'];
   $user_id = $user['User']['id'];
   $ct = get_time();
   $current_time = $ct['time_ymdhis'] ;
   $reserve_time = $lesson['Lesson']['reserve_time'];
   $content = $reserve_time.'수업연기'.
   $spent_point = $lesson['Lesson']['spend_point'];
   $reservation_kinds = $lesson['Lesson']['reservation_kinds'];
//    Extend User's last day

//    Extend All Lesson Reservations kinds K Only;
//    ONLY COMA resrvation increase only
   if($extend_type2 == 'K' && $reservation_kinds == 'K'){
    $update_conditions['user_id'] = $user['User']['id'];
    $update_conditions['ex_count'] = null;
    
    if($pay_plan == 'E1'){
     $update_conditions['ex_count'] = 0;
     $update_conditions['last_day'] = $extend_day;
//      2011-01-29 start 	
    } // End if($pay_plan == 'E1'){};
    
    if($pay_plan == 'E2' || $pay_plan == 'E3' || $pay_plan == 'E4'){
     $ex_count = $ex_count+10;
//      Do not change the last day
     $update_conditions['last_day'] = $last_day;
     
//      E2
     if($ex_count == 20){
      if($pay_plan == 'E2'){
       $update_conditions['last_day'] = $extend_day;
       $ex_count = 0;
      } // End if($ex_count == 20){};
//      E3
     }else if($ex_count == 30){
      if($pay_plan == 'E3'){
       $update_conditions['last_day'] = $extend_day;
       $ex_count = 0;
      } // End if($pay_plan == 'E3'){};
//      E4
     }else if($ex_count == 40){
      if($pay_plan == 'E4'){
       $update_conditions['last_day'] = $extend_day;
       $ex_count = 0;
      } // End if($pay_plan == 'E4'){};
     } // End if($ex_count == 20){}else if($ex_count == 30){}else if($ex_count == 40){};

     $update_conditions['ex_count'] = $ex_count;
    } // if($pay_plan == 'E2' || $pay_plan == 'E3' || $pay_plan == 'E4'){};
//     print_r($update_conditions);

//     2012-12-17 UPDATE
    $extend_msg = $before_ex_count.','.$ex_count;
    $insert_conditions['description'] = $extend_msg;
    $this->ModelDefaultHandler->insertExtention($insert_conditions,'Lesson');
    $this->ModelDefaultHandler->updateUser($update_conditions,'Lesson');
//     2011-02-03 
   } // End if($extend_type2 == 'K' && $reservation_kinds == 'K'){};
//    2011-11-14 modified
 
//    Extend All Lesson Reservations kinds P Only;
//    point plan test user incresed point
   if($extend_type2 == 'P' || $reservation_kinds == 'P'){
   	
    $sql = " insert into g4_point 
             set mb_id = '$mb_id',
             po_datetime = '$current_time',
             po_content = '$content',
             po_point = '$spent_point',
             po_rel_table = 'lessons',
             po_rel_id = '',
             po_rel_action = '수업' ";
    $free_sql_result = $this->Lesson->free_sql($sql);
    
//     In case of point the sume point
    $select_string = "sum(po_point) AS point ";
    $table = "g4_point ";
    $where_string =  "mb_id = '$mb_id' ";
    $point_results = $this->Lesson->select_free_common($select_string,$table,$where_string);
    
    foreach($point_results as $point_result){
	 $point = $point_result[0]['point'];
    } // End foreach($point_results as $point_result){};
    
//     Update g4_member
    $sql = " update g4_member
             SET mb_point = $point 
             WHERE mb_id = '$mb_id'";
    $this->Lesson->free_sql($sql);
    $insert_conditions['description'] = $point;
    $this->ModelDefaultHandler->insertExtention($insert_conditions,'Lesson');
//     fixed student	
   } // End if($extend_type2 == 'P' || $reservation_kinds == 'P'){};
   
//    Extend All Lesson Reservations kinds M or MD Only;
//    In case of fixed student
   $kinds_M_MD = array('M','MD'); 
   if($extend_type2 == 'M' || in_array($reservation_kinds, $kinds_M_MD)){
//     1.Find the last class
//     change for user for user get a lesson more than one time find the same tiem
    $reserve_only_time = date("H:i:s", strtotime($reserve_time));
    $last_lesson = $this->Lesson->find('first',array('conditions' =>
     array('Lesson.user_id' => $user_id
          ,'Lesson.reservation_kinds' => $kinds_M_MD
          ,'Lesson.lesson_status' => 'R'
          ,'Lesson.reserve_time LIKE' => '%'.$reserve_only_time)
          ,'order' => array('Lesson.reserve_time DESC')
     ));
    
    $this->loadModel('Available_lesson');
    $available_lesson_id = $last_lesson['Lesson']['available_lesson_id'];
    $last_Available_lesson = $this->Available_lesson->find('first',array('conditions'=>array('Available_lesson.id'=>$available_lesson_id)));
    $last_lesson_time = $last_lesson['Lesson']['reserve_time'];
    
//     check current lesson and last lesson
    if($last_lesson_time == $reserve_time){}
//     2.insert class after last class + oneday
//     fixed student is no sat sunday class so check the satday or sunday
     $increasing_day = 1;
     
//      0 is sunday and 6 is saturday
//      check the weekdays;
     $check = true;
     $extend_only_day = $last_lesson_time;
     while($check == true):
      $extend_only_day = date("Y-m-d",strtotime('+1 day', strtotime($extend_only_day)));
      $date = date("w",strtotime($extend_only_day));
      // identify 00:00,00:30,01:00,...,04:30 or greater than 05:00
      // Convert to long time it will become 24:00 (MidNight) to greater than 29:00 (05:00) AM
      $hours=(date('G',strtotime($last_lesson_time)) + 24);
      if($hours >= 24 && $hours < 29){ // 24:00 (00:00 AM) and 29:00 (05:00 AM)
      	// No sunday and No monday
      	if(!($date == 0 || $date == 1)){$check = false;}
      }else{
      	//sunday saturday
      	if(!($date == 0 || $date == 6)){$check = false;}
      } // End  if($hours >= 24 && $hours < 29){}else{}
     endwhile; // End while($check == true):endwhile;
     
     $extend_day =  sprintf("%s %s",$extend_only_day,$reserve_only_time);
//      Insert new available_lesson;
     $last_Available_lesson['Available_lesson']['id']= null;
     $last_Available_lesson['Available_lesson']['available_time'] = $extend_day;
     $this->Available_lesson->save($last_Available_lesson);
     $lastInsertId= $this->Available_lesson->getLastInsertId();
//      lesson table
     $last_lesson['Lesson']['id'] = null;
     $last_lesson['Lesson']['reserve_time'] = $extend_day;
     $last_lesson['Lesson']['available_lesson_id'] = $lastInsertId;
//      maintain the payment_id; 2014-12-23
     $last_lesson['Lesson']['payment_id'] = $payment_id;
     $last_lesson['Lesson']['reservation_kinds'] = $reservation_kinds;
//      $last_lesson['Lesson']['created'] = date('Y-m-d H:i:s');
     $last_lesson['Lesson']['modified'] = $current_datetime;
     $this->Lesson->save($last_lesson);
//      done to insert new extended lesson;

     $insert_conditions['description'] = $reserve_time.','.$extend_day;
     $this->ModelDefaultHandler->insertExtention($insert_conditions,'Lesson');
     $message = $teacher['Teacher']['tea_name']." teacher ".$user['User']['nick_name']." user ".$reserve_time."' reserve_time '. $extend_day. 'extended.'"; 
     array_push($messages,$message);
     
    } // End if($extend_type2 == 'M' || in_array($reservation_kinds, $kinds_M_MD)){};
    
    if($extend_type2 == 'N'){
//      nothing
    } // End if($extend_type2 == 'N'){}
    
//     after Update One more time 
    $users = $this->ModelDefaultHandler->getDefaultUser($search_conditions_u,'Lesson');
    $user = $users[0];
    $this->set('user',$user);
    $this->set('lesson',$lesson);
    $this->set('teacher',$teacher);
    $this->set('last_day',$last_day);
    $this->set('insert_conditions',$insert_conditions);
    $this->set('ex_count',$ex_count);
    
   }else{
   	
    $message = 'this lesson already extended !!';
    array_push($messages,$message);
    
   } // End if($extention_count == 0 && $lesson['Lesson']['lesson_status'] == 'R'){};
   $i++;
  } // End foreach($lesson_id_array as $lesson_id){};
  $this->set( 'extention_count', $extention_count );
  $this->set('messages', $messages);
//   pr($messages);
} // End function teacher_extend_all(){};



//2011-05-19 start
//WHEN TEACHER CHANGE THE LESSON STATUS
function teacher_change_status_lessons()
{
	$loginInfo = $this->CheckHandler->checkLoginInfo();
	//2010-02-22 YHLEE START
	
	$lesson_status_kinds = $this->params['form']['lesson_status_kinds'];
	$lesson_id = $this->params['form']['lesson_id'];
	
	$lessons = $this->ModelHandler->getLessonByLessonId($lesson_id,'Lesson'); 
	
	$lesson = $lessons[0];
		
	$teacher_id = $lesson['Lesson']['teacher_id'];
	$user_id = $lesson['Lesson']['user_id'];

	$this->Lesson->set(array('id'=>$lesson_id ,'lesson_status'=>$lesson_status_kinds));
	$this->Lesson->save();
	
	//CANCEL BY MANAGER
	if($lesson_status_kinds == 'C'){
		
		$this->loadModel('Available_lesson');
		$available_lesson_id = $lesson['Lesson']['available_lesson_id'];
		$this->Available_lesson->set(array('id'=>$available_lesson_id,'reserve_status'=>'R'));
		$this->Available_lesson->save();
		
		//$update_common = null;
		//$update_common['id'] = $lesson['Lesson']['available_lesson_id'];
		//$update_common['reserve_status'] = 'R';
		///$lessons = $this->Lesson->update_common('available_lessons',$update_common);
	}
	
	$this->set('lesson',$lesson);
		
}

//2011-06-23 start
//2012-12-27 YHLEE ADD
//2015-03-25 Rarry
function teacher_change_all_status()
{
 $loginInfo = $this->CheckHandler->checkLoginInfo();
 $this->loadModel('Available_lesson');
//  2010-02-22 YHLEE START
 $lesson_id_array = $this->params['form']['lesson_id'];
 $all_type = $this->params['form']['all_type'];
 $prevStatus = array();
//  Lesson Satus
 foreach($lesson_id_array as $lesson_id){
  $lessons = $this->ModelHandler->getLessonByLessonId($lesson_id,'Lesson');
  $lesson_status = $lessons[0]['Lesson']['lesson_status'];
  
  if($all_type == 'DELETE'){
   $changing_lesson_status = 'D';
   if(isset($this->params['form']['delete_status'])){ //2015-03-25 Rarry
    $changing_lesson_status = $this->params['form']['delete_status'];
   } // End if(isset($this->params['form']['delete_status'])){};
  }elseif($all_type == 'HOLD'){
   $changing_lesson_status = 'H';
  }elseif($all_type == 'HOLD-DELETE'){
   $changing_lesson_status = 'HD';
  } // End if($all_type == 'DELETE'){}elseif($all_type == 'HOLD'){}elseif($all_type == 'HOLD-DELETE'){};

  $prevStatus[$lesson_id] = $lesson_status;
			
  if($all_type != 'HOLD-DELETE'){
  	
//    if($lesson_status == 'R'){
    $available_lesson_id = $lessons[0]['Lesson']['available_lesson_id'];
    $data = array('id' => $lesson_id ,'lesson_status'=> $changing_lesson_status);
    $this->Lesson->saveAll($data);
    $this->Available_lesson->delete($available_lesson_id);
//    }elseif($lesson_status == 'H'){
//     $available_lesson_id = $lessons[0]['Lesson']['available_lesson_id'];
//     $data = array('id' => $lesson_id ,'lesson_status'=> "HD");
//     $this->Lesson->saveAll($data);
//     $this->Available_lesson->delete($available_lesson_id);
//    } // End if($lesson_status == 'R'){}elseif($lesson_status == 'H'){};
   
  }else{
   $available_lesson_id = $lessons[0]['Lesson']['available_lesson_id'];
   $data = array('id' => $lesson_id ,'lesson_status'=> "HD");
   $this->Lesson->saveAll($data);
   $this->Available_lesson->delete($available_lesson_id);
  } // End if($all_type != 'HOLD-DELETE'){}else{};
  
 } // End foreach($lesson_id_array as $lesson_id){};
 $this->loadModel("Teacher");
 $this->loadModel("User");
	
 $this->Lesson->bindModel(array('belongsTo' => array('Teacher'=>array('className' => 'Teacher','foreignKey' => 'teacher_id')) ));
 $this->Lesson->bindModel(array('belongsTo' => array('User'=>array('className' => 'User','foreignKey' => 'user_id')) ));
 $lesson = $this->Lesson->find("all", array( "fields"=>array("Lesson.id", "Teacher.tea_name", "User.mb_id", "Lesson.reserve_time","Lesson.lesson_status"), "conditions"=>array("Lesson.id"=>$lesson_id_array), "order"=>"Lesson.reserve_time DESC" ));
 $this->set('prevStatus',$prevStatus);
 $this->set('lesson',$lesson);
} // End function teacher_change_all_status(){};

//2010-12-26 YHLEE ADD START
function teacher_extend_users()
{
	$loginInfo = $this->CheckHandler->checkLoginInfo();
	
	//2011-02-03 start
	$allTeachers = $this->ModelHandler->getAllTeacher('Lesson');
	$allTeachers_select = get_all_teachers($allTeachers);
	
	$this->set('allTeachers_select',$allTeachers_select);			
	//2011-02-03 end
	
	
	//2014-05-08 reservation_kinds
	// 'reservation_kind' is base on dictionary table (name)
	// 'lesson_status' is base on dictionary table (name)
	// $this -> Programmer1Handler -> getStatusKind => get lesson_status || get reservation_kinds
	// lesson_status = 'lesson_status'
	// reservation_kinds = 'reservation_kinds'
	$this -> set('reservation_kinds_info', $this -> Programmer1Handler -> getStatusKind( 'reservation_kind' ) );
	$this -> set('lesson_status_info', $this -> Programmer1Handler -> getStatusKind( 'lesson_status' ) );
	$this -> set('distributorMgmt', $this -> Programmer1Handler -> distributorMgmt( 'lesson_status' ) );
	
	// Get the fix/static Week day or Default Days of the week
	// 2016-03-11 Please use in weekdays start Sunday as 1, Monday as 2 until Saturday as 7
	$this->set('weekname_value',$this->Programmer1Handler->weekname_value);
}

//2010-12-26 YHLEE ADD START
function teacher_extend_users_confirm($from_where_re= null)
{
	$loginInfo = $this->CheckHandler->checkLoginInfo();
	
	//receiving the parameter
	$from_where = $this->params['form']['from_where'];
	
	$this->set('from_where',$from_where);

	if($from_where == 'search'){
		$name = $this->params['form']['name'];
		$teacher_id = $this->params['form']['teacher_id'];
		$mb_id = trim($this->params['form']['mb_id']);
		$nick_name = trim($this->params['form']['nick_name']);
		$skype_id = trim($this->params['form']['skype_id']);
		$start_date = trim($this->params['form']['start_date']);
		$last_date = trim($this->params['form']['last_date']);
		
		$payment_id = trim($this->params['form']['payment_id']);
		
		//2011-05-27 start
		$start_time = $this->params['form']['start_time'];
		$last_time = $this->params['form']['last_time'];
		//2011-05-27 end
		
		$pay_plan1 = $this->params['form']['pay_plan1'];
		$pay_plan2 = $this->params['form']['pay_plan2'];
		$reservation_kinds = $this->params['form']['reservation_kinds'];
		$lesson_status = $this->params['form']['lesson_status'];
		
		// distributor_id
		$distributorTrigger = $this->params['form']['distributorTrigger'];
		$distributor_id = $this->params['form']['distributor_id'];
		
		//new session set
		$input_datas['name'] = $name;
		$input_datas['teacher_id'] = $teacher_id;
		$input_datas['nick_name'] = $nick_name;
		$input_datas['skype_id'] = $skype_id;
		$input_datas['start_date'] = $start_date;
		$input_datas['last_date'] = $last_date;
		$input_datas['start_time'] = $start_time;
		$input_datas['last_time'] = $last_time;
		$input_datas['pay_plan1'] = $pay_plan1;
		$input_datas['pay_plan2'] = $pay_plan2;
		$input_datas['reservation_kinds'] = $reservation_kinds;
		
		$input_datas['lesson_status'] = $lesson_status;
		
		$input_datas['payment_id'] = $payment_id;
		
		// distributor_id
		$input_datas['distributorTrigger'] = $distributorTrigger;
		$input_datas['distributor_id'] = $distributor_id;
		
		//print_r($input_datas);
		
		$this->Session->delete('input_datas');
		$this->Session->write('input_datas',$input_datas);

	}else if($from_where == 'confirm' || $from_where_re == 'confirm'){
		
		$input_datas = $this->Session->read('input_datas');
		
		$name = $input_datas['name'];
		$teacher_id = $input_datas['teacher_id'];
		$nick_name = $input_datas['nick_name'];
		$skype_id = $input_datas['skype_id'];
		$start_date = $input_datas['start_date'];
		$last_date = $input_datas['last_date'];
		
		$start_time = $input_datas['start_time'];
		$last_time = $input_datas['last_time'];
		
		$pay_plan1 = $input_datas['pay_plan1'];
		$pay_plan2 = $input_datas['pay_plan2'];
		$lesson_status = $input_datas['lesson_status'];
		
		// distributor_id
		$distributorTrigger = $input_datas['distributorTrigger'];
		$distributor_id = $input_datas['distributor_id'];
	}
	//pay_plan1 , pay_plan2 = all;


	if($pay_plan1 == 'all'){
		$pay_plan1 = null;
	}
	
	if($pay_plan2 == 'all'){
		$pay_plan2 = null;
	}
	
	
	//receiving the parameters from form
	//date change from web format to DB format
	
	$time_string = '02:00:00';
	
	//2011-05-27 Time add
	if($start_time != null){
		$start_time_string = change_time_string($start_time);
	}else{
		$start_time_string = $time_string;
	}
   
	if($last_time != null ){
		$last_time_string = change_time_string($last_time);
	}else{
		$last_time_string = $time_string;
	}
	
	//2011-05-27 end
	
	if($start_date != null){
		$start_date_ymd = $start_date;
		//E1 Extend ond day
		$start_date = sprintf("%s %s",$start_date_ymd,$start_time_string);
		//if last_date is null only extend one day 
		if($last_date == null){ 
			$last_date_ymd =  date("Y-m-d",strtotime('+1 day', strtotime($start_date)));
			$last_date = sprintf("%s %s",$last_date_ymd,$last_time_string );
		
		//if $last_date
		}else{
			$last_date_ymd = $last_date;
			
			//E1 Extend ond day
			
			//
			if($last_time == null){
				
				$last_date_ymd =  date("Y-m-d",strtotime('+1 day', strtotime($last_date_ymd)));
				$last_date = sprintf("%s 02:00:00",$last_date_ymd);
			}else{
				$last_date = sprintf("%s %s",$last_date_ymd, $last_time_string );
			}
			
		} 
	}
	
	// this will be 2015-07-010 so 11 characters
	if(strlen(str_replace(' ','',$start_date)) < 8){
		$start_date=date('Y-m-d');
		$last_date=date('Y-m-d 02:00:00', strtotime($start_date . '+1 day'));
	}
	
	//if($last_date != null){
	//	$last_date = change_to_db_date($last_date,'YMD');
	//}
	$search_conditions['name'] = $name;
	$search_conditions['teacher_id'] = $teacher_id;
	$search_conditions['mb_id'] = $mb_id;
	$search_conditions['nick_name'] = $nick_name;
	$search_conditions['skype_id'] = $skype_id;
	$search_conditions['start_date'] = $start_date;
	$search_conditions['last_date'] = $last_date;
	if($start_time != null){
		$search_conditions['start_time'] = $start_time_string;
	}else{
		$search_conditions['start_time'] = $start_time;
	}
	
	if($last_time != null ){
		$search_conditions['last_time'] =  $last_time_string;
	}else{
		$search_conditions['last_time'] = $last_time;
	}
	
	
	$search_conditions['pay_plan1'] = $pay_plan1;
	$search_conditions['pay_plan2'] = $pay_plan2;
	$search_conditions['reservation_kinds'] = $reservation_kinds;
	
	if(strlen($lesson_status) < 0){$lesson_status='R';}
	$search_conditions['lesson_status'] = $lesson_status;

	$search_conditions['payment_id'] = $payment_id;
	
	// distributor_id
	$search_conditions['distributorTrigger'] = $this->params['form']['distributorTrigger'];
	$search_conditions['distributor_id'] = $distributor_id;
	
	// In distributor_info
	// You may change the payment id of all your student by the distributor management
	$distributor_info = array();
	if(isset($this->params['form']['distributorTrigger'])
			&& $this->params['form']['distributorTrigger'] == 1
			&& $distributor_id != 'select') {
		$this->loadModel('Distributor');
		$distributor_info = $this->Distributor->find('first',array(
				'fields'=>array('Distributor.mb_id')
				,'conditions'=>array('Distributor.id'=>$distributor_id)
		));
	}
	$this->set('distributor_info',$distributor_info);
	
	// Search by Days of week
	if(isset($this->params['form']['weekname_value'])){
		$search_conditions['weekname_value'] = $this->params['form']['weekname_value'];
	}

	//2011-02-14 START
	if($teacher_id == 'all'){
		$search_conditions['teacher_id'] = null;
	}
	//2011-02-14 END
	
	$no_reservation_users = array();
	$no_reservation_day = array();
	
	//print_r($lesson_status);
	
	//reservation_users setting
	$this->set('reservation_users',null);
	$this->set('no_reservation_users',null);
	$this->set('extended_users',null);
	
	//No class E1,E2,or regardless of everyday or count lessons(횟수수업)
	if($lesson_status == 'N'){

		pr($last_date_ymd);
		
		pr($start_date_ymd);
		
		//user's last_day > searching start_day
		$validUsersAll = $this->ModelHandler->getValidUsers($search_conditions,'Lesson');
		
		$how_many_days = round((strtotime($last_date_ymd) -strtotime($start_date_ymd)) / (60*60*24)); 

	    //print_r($how_many_days);

		foreach ($validUsersAll as $validUser){
			
			$temp_start_date = $start_date;
			$temp_last_date = $start_date;
			$before_extend_day = $validUser['User']['last_day'];
			$after_extend_day = $validUser['User']['last_day'];
			
			//2012-12-25 Change no reserved user extend the class
			//last_day_ymd is one day +

			pr($how_many_days);
			
			for($i=1;$i<=$how_many_days;$i++){
				
				$temp_start_date = $temp_last_date;
				
				$search_conditions['user_id'] = $validUser['User']['id'];
				$search_conditions['start_date'] = $temp_start_date;
				$temp_last_date = date("Y-m-d H:i:s",strtotime('+1'.' day', strtotime($temp_start_date)));
				$search_conditions['last_date'] = $temp_last_date;
				
				//Chcek if there is one record in lesson data base 
				$reserve_counts = $this->ModelHandler->getUserReserveClass($search_conditions,'Lesson');
				
				$reserve_count = $reserve_counts[0][0];
				
				//if no reservation
				if($reserve_count['reservation_count'] == 0){
					
					//2011-12-25
				   $search_conditions['tarket_day'] = $search_conditions['start_date'];
				   $search_conditions['first_extend_day'] = null;	 
				   
				   $search_count = $this->ModelDefaultHandler->getDefaultExtention($search_conditions,'Lesson', 'COUNT');
				   
				  
				   //if already extended , do not extend
				   if($search_count[0][0]['count'] == 0){
					  
						//repeat 
						$before_extend_day = $after_extend_day; 
						
						$validUser['User']['target_day'] = $temp_start_date;
						$validUser['User']['before_extend_day'] = $before_extend_day;
						
						//1day PLUS
						$after_extend_day = date("Y-m-d H:i:s",strtotime('+1 day', strtotime($before_extend_day)));
						$validUser['User']['after_extend_day'] =  $after_extend_day;
		 				array_push($no_reservation_users, $validUser);
				    }
					//print_r($validUser);
				}
			}
		}
		
		//$this->Session->write('no_reservation_users',$no_reservation_users);
		//$this->Session->delete('reservation_users');
		$this->set('no_reservation_users',$no_reservation_users);
			
			$extended_users = Array();
			
			//if you click the [excute button]change user complete
			if($from_where == 'confirm'){
				
				$insert_conditions['lesson_id'] = null;
				$insert_conditions['pay_plan'] = null;
				$insert_conditions['reservation_kinds'] = null;
				$insert_conditions['teacher_name'] = null;
				$insert_conditions['reserve_time'] = null;
				$insert_conditions['reason'] = '01';
				
				foreach($no_reservation_users as $user){	
				   $insert_conditions['user_id'] = $user['User']['id'];
				   $insert_conditions['user_nick_name'] = $user['User']['nick_name'];
				   $insert_conditions['pay_plan'] = $user['User']['pay_plan'];
				   $insert_conditions['tarket_day'] = $user['User']['target_day'];
				   $insert_conditions['last_day'] =  $user['User']['before_extend_day'];
//				   if($extend_type == 'plus'){
//				   		$extend_day = change_to_db_date($user['User']['last_day'],'YMDHISONE','hyph');
//				   }else if($extend_type == 'minus'){
//				   		$extend_day = change_to_db_date($user['User']['last_day'],'YMDHISONEMINUS','hyph');
//				   }
				   
				   $insert_conditions['extend_day'] = $user['User']['after_extend_day'];
				   
				  
				   
				   $search_conditions['user_id'] = $user['User']['id'];
				   $search_conditions['tarket_day'] = $user['User']['target_day'];
				   $search_conditions['first_extend_day'] = null;
				    
				   $search_count = $this->ModelDefaultHandler->getDefaultExtention($search_conditions,'Lesson', 'COUNT');
				
				   //pr($search_count);
				   if($search_count[0][0]['count'] == 0){
				   	   //insert extention
					   $this->ModelDefaultHandler->insertExtention($insert_conditions,'Lesson');
					   //update user here
					   $update_conditions['user_id'] = $user['User']['id'];
					   $update_conditions['last_day'] = $user['User']['after_extend_day'];
				   	   $update_conditions['ex_count'] = null;
				   	   $this->ModelDefaultHandler->updateUser($update_conditions,'Lesson');
					   array_push($extended_users, $user);
				   }
				}
				
			   //Update User Only One time
				
			  //UPDATE USER
			  if(sizeof($extended_users)!= 0){
				   
				   //SELECT USER to confirm
				   $select_conditions = $insert_conditions;
			  	   
				   $users = $this->ModelDefaultHandler->getDefaultUser($select_conditions,'Lesson');
				   
				   //
				   $ex_select_conditions['user_id'] = $insert_conditions['user_id'];
				   $ex_select_conditions['first_extend_day'] = $extended_users[0]['User']['after_extend_day'];
				   $ex_select_conditions['last_extend_day'] = $extended_users[$last_record]['User']['after_extend_day'];
				   
				   $extentions = $this->ModelDefaultHandler->getDefaultExtention($ex_select_conditions,'Lesson');
				   
				   $this->set('users',$users);
				   $this->set('extentions',$extentions);
				  
				   $this->set('extended_users',$extended_users);
			  }
			  
			  
			}
			
			
	//ONLY Showig Lesson Details
	}else{
		$reservation_users = $this->ModelHandler->getSearchLessonsD($search_conditions,'Lesson');
		$count_F = $this->ModelHandler->getSearchLessonsDCF($search_conditions,'Lesson');
		$count_A = $this->ModelHandler->getSearchLessonsDCA($search_conditions,'Lesson');
		$count_CP = $this->ModelHandler->getSearchLessonsDCCP($search_conditions,'Lesson');
		
		//pr($count_CP);
		
		$count_A = $count_A[0][0]['A'];
		$count_F = $count_F[0][0]['F'];
		$count_CP = $count_CP[0][0]['CP'];
		$count_T = $count_A + $count_F+ $count_CP;
		
		//$this->Session->write('reservation_users',$reservation_users);
		//$this->Session->delete('no_reservation_users');
		
		
		$this->set('count_F',$count_F);
		$this->set('count_A',$count_A);
		$this->set('count_CP',$count_CP);
		$this->set('count_T',$count_T);
		
		$this->set('reservation_users',$reservation_users);
		
		$this->set('no_reservation_users',null);
	}
	
	//the result
	
	$this->set('search_conditions',$search_conditions);
	$this->set('start_date',$start_date);
	
}

//2010-12-26 YHLEE ADD START
//Not used function
function teacher_extend_users_complete()
{
	$loginInfo = $this->CheckHandler->checkLoginInfo();
	
	$start_date = $this->params['form']['start_date'];
	$extend_type = $this->params['form']['extend_type'];
	
	$no_reservation_users = $this->Session->read('no_reservation_users');
	
	$reservation_users = $this->Session->read('reservation_users');
	
	//reseson 04 techinical problem
	if($no_reservation_users != null){
		$insert_conditions['lesson_id'] = null;
		$insert_conditions['pay_plan'] = null;
		$insert_conditions['reservation_kinds'] = null;
		$insert_conditions['teacher_name'] = null;
		$insert_conditions['reserve_time'] = null;
		$insert_conditions['reason'] = '04';
	}
	
	$extended_users = Array();
	
	if($no_reservation_users != null){
		foreach($no_reservation_users as $user){	
		   $insert_conditions['user_id'] = $user['User']['id'];
		   $insert_conditions['user_nick_name'] = $user['User']['nick_name'];
		   $insert_conditions['pay_plan'] = $user['User']['pay_plan'];
		   $insert_conditions['tarket_day'] = $start_date;
		   $insert_conditions['last_day'] =  $user['User']['last_day'];
		   if($extend_type == 'plus'){
		   		$extend_day = change_to_db_date($user['User']['last_day'],'YMDHISONE','hyph');
		   }else if($extend_type == 'minus'){
		   		$extend_day = change_to_db_date($user['User']['last_day'],'YMDHISONEMINUS','hyph');
		   }
		   
		   $insert_conditions['extend_day'] = $extend_day;
		   
		   
		   $this->ModelDefaultHandler->insertExtention($insert_conditions,'Lesson');
		   
		   //UPDATE USER
		   $update_conditions = $insert_conditions; 
		   $this->ModelDefaultHandler->updateUser($update_conditions,'Lesson');
		   
		   $user['User']['extend_day'] =  $insert_conditions['extend_day'];
		   $user['User']['ex_count'] = 0;
		   
		   array_push($extended_users, $user);
		}
	}
	
	if($reservation_users != null){
		
		foreach($reservation_users as $user){
		    $insert_conditions['lesson_id'] = $user['Lesson']['id'];
			$insert_conditions['reservation_kinds'] = $user['Lesson']['reservation_kinds'];;
			$insert_conditions['teacher_name'] = $user['Teacher']['tea_name'];
			$insert_conditions['reserve_time'] =  $user['Lesson']['reserve_time'];
			$insert_conditions['reason'] = '02';
			
		    //ONLY Lesson
		    $insert_conditions['lesson_status'] =  'E';
			
		   $insert_conditions['user_id'] = $user['User']['id'];
		   $insert_conditions['user_nick_name'] = $user['User']['nick_name'];
		   $insert_conditions['pay_plan'] = $user['User']['pay_plan'];
		   $insert_conditions['tarket_day'] = $start_date;
		   
		   //user DB read one more time
		   $searchUsers = $this->ModelDefaultHandler->getDefaultUser($insert_conditions,'Lesson');
		   $searchUser = $searchUsers[0];
		   $searchUser_last_day = $searchUser['User']['last_day'];
		   $searchUser_ex_count = $searchUser['User']['ex_count'];
		   
		   //print_r($searchUser);
		   
		   $insert_conditions['last_day'] =  $searchUser_last_day;
		   $ex_count = $searchUser_ex_count;
		   
		  
		   //E1,E2,E3,E4
		   if($user['User']['pay_plan'] == 'E1'){
			   if($extend_type == 'plus'){
			   		$extend_day = change_to_db_date($searchUser_last_day,'YMDHISONE','hyph');
			   }else if($extend_type == 'minus'){
			   		$extend_day = change_to_db_date($searchUser_last_day,'YMDHISONEMINUS','hyph');
			   }
		   }else if($user['User']['pay_plan'] == 'E2'){
		   	  if($extend_type == 'plus'){
		   	  	$ex_count = $searchUser_ex_count;	
		   	  	$ex_count = $ex_count+1;
		   	  	print_r('$searchUser_ex_count'.$searchUser_ex_count);
		   	  	print_r('$ex_count'.$ex_count);
		   	  	if($ex_count < 2){
		   	  		$extend_day = $searchUser_last_day;
		   	  	}else{
		   	  		$extend_day = change_to_db_date($searchUser_last_day,'YMDHISONE','hyph');
		   	  		$ex_count = 0;
		   	  		
		   	  	}
		   	  }	
		   }
		   
		   $insert_conditions['ex_count'] = $ex_count;
		   $insert_conditions['extend_day'] = $extend_day;
		  
		   
		   $this->ModelDefaultHandler->insertExtention($insert_conditions,'Lesson');
		   
		   //UPDATE USER
		  
		   $this->ModelDefaultHandler->updateUser($insert_conditions,'Lesson');
		   
		   //UPDATE Lesson
		    $this->ModelDefaultHandler->updateLesson($insert_conditions,'Lesson');
		     
		   $user['User']['extend_day'] =  $insert_conditions['extend_day'];
		   $user['User']['ex_count'] =   $insert_conditions['ex_count'];
		    
		   array_push($extended_users, $user);
		}
	}	
	
	$this->Session->delete('no_reservation_users');
	$this->Session->delete('reservation_users');
	$this->set('extended_users',$extended_users);
}


//2010-12-25 YHLEE ADD START
//
function teacher_extend_lesson()
{
	$loginInfo = $this->CheckHandler->checkLoginInfo();
	//2010-02-22 YHLEE START
	$search_type = null;
	
	//just in case it can be deleted start
	if(isset($this->params['form']['search_type'])){
		$search_type = $this->params['form']['search_type'];
	}
	// end
	
	$lesson_id = $this->params['form']['lesson_id'];
	
	//get a lesson details from lesson _id
	$search_conditions['lesson_id'] = $lesson_id; 
	$lessons = $this->ModelDefaultHandler->getDefaultLesson($search_conditions,'Lesson');	
	$lesson = $lessons[0];
	
	//get a user details by user_id
	$search_conditions['user_id'] = $lesson['user_id'];
	$lessons = $this->ModelDefaultHandler->getDefaultUser($search_conditions,'Lesson');	
	$user = $users[0];
	
	//get a teacher details by teacher_id
	$search_conditions['user_id'] = $lesson['user_id'];
	$lessons = $this->ModelDefaultHandler->getDefaultUser($search_conditions,'Lesson');	
	$user = $users[0];
	
	//insert extentions
	$insert_conditions['user_id'] = $user['user_id'];
	$insert_conditions['lesson_id'] = $lesson['lesson_id'];
	$insert_conditions['pay_plan'] = $user['pay_plan'];
	$insert_conditions['reservation_kinds'] = $lesson['reservation_kinds'];
	$insert_conditions['ex_count'] = 1;
	$insert_conditions['user_nick_name'] = $user['nick_name'];
	$insert_conditions['teacher_name'] = $teacher['tea_name'];
	$insert_conditions['last_date'] = $user['last_day'];
	
	$this->ModelDefaultHandler->insertExtention($insert_conditions,'Lesson');	
	
	//E1,E2,E3,E4 P S1 change status
	$lessonData['lesson_status'] = 'E';
	$lessonData['id'] = $lesson_id;
	$this->ModelHandler->updateLessonStatus($lessonData , 'Lesson');
	
	//E1 Extend ond day
	$update_conditions[last_day] = date("Y-m-d H:i:s",strtotime('+1 month', strtotime($g4[time_ymdhis])));
	
	//receiving the parameters from form
	//date change from web format to DB format
	if($start_date != null){
		$start_date = change_to_db_date($start_date,'YMD','nohyph');
	}
	if($last_date != null){
		$last_date = change_to_db_date($last_date,'YMD','nohyph');
	}
	
	
	$search_conditions['teacher_id'] = $teacher_id;
	$search_conditions['nick_name'] = $nick_name;
	$search_conditions['skype_id'] = $skype_id;
	$search_conditions['start_date'] = $start_date;
	$search_conditions['last_date'] = $last_date;
	
	//print_r(substr($start_date,4,2));
	//print_r($last_date);
	//print_r($skype_id);
	
	//teacher search lesson
	$searchLessons = $this->ModelHandler->getSearchLessons('Lesson',$search_conditions);
	
	//all teachers
	$allTeachers = $this->ModelHandler->getAllTeacher('Lesson');
	
	//2010-03-27 change teacher end 
	$this->set('searchLessons',$searchLessons);
	$this->set('allTeachers',$allTeachers);	
	$this->set('search_type',$search_type);	
}


//2010-10-17 START
function teacher_salary()
{
	//セッション情報がなくなった場合、Cubicboardのログインページに遷移する
	
	$loginInfo = $this->CheckHandler->checkLoginInfo();
	
	//get teacher_id
	$loginInfo = $this->Session->read('loginInfo');
	
	$teacher_id = $loginInfo['id'];
	
	//2010-02-22 YHLEE START
	$search_type = null;
	
	$search_type = $this->params['form']['search_type'];
	
	//OFFICE BASE TEACHER										
	//2010-11-22 - 2010-12-05 FOR OFFICE BASE YHLEE START										
	
	$current_ymd = $this->ModelHandler->_timeymd;
										
	$t_start_time  = sprintf("%s 02:00:00",'2012-10-16');									
	$t_finish_time = sprintf("%s 02:00:00",'2012-11-01');
	
	//first weekends									
	$w1_start_time  = sprintf("%s 02:00:00",'2012-10-20');									
	$w1_finish_time = sprintf("%s 02:00:00",'2012-10-22');

	//second weekends									
	$w2_start_time  = sprintf("%s 02:00:00",'2012-10-27');									
	$w2_finish_time = sprintf("%s 02:00:00",'2012-10-29');

	//third weekends								
	$w3_start_time  = sprintf("%s 02:00:00",'2012-10-26');									
	$w3_finish_time = sprintf("%s 02:00:00",'2012-10-27');

	//special weekends								
	$s_start_time  = sprintf("%s 02:00:00",'9999-99-99');									
	$s_finish_time = sprintf("%s 02:00:00",'9999-99-99');	
			
	if($loginInfo['tea_kinds'] == 'H') {
	// FROM START TIME LAST TIME								
	$totals = $this->ModelHandler->getTeacherSalary($teacher_id,$t_start_time,$t_finish_time,'Lesson');						
	//first weekends																									
	$firsts = $this->ModelHandler->getTeacherSalary($teacher_id,$w1_start_time,$w1_finish_time,'Lesson');									
	//second weekends															
	$seconds = $this->ModelHandler->getTeacherSalary($teacher_id,$w2_start_time,$w2_finish_time,'Lesson');
	//third weekends								
	$thirds = $this->ModelHandler->getTeacherSalary($teacher_id,$w3_start_time,$w3_finish_time,'Lesson');
	//specials								
	$specials = $this->ModelHandler->getTeacherSalary($teacher_id,$s_start_time,$s_start_time,'Lesson');
	//missing
	$missings = $this->ModelHandler->getMissingClass($teacher_id,$t_start_time,$t_finish_time,'Lesson');
	//others
	$others = $this->ModelHandler->getOthersClass($teacher_id,$t_start_time,$t_finish_time,'Lesson');
	//2011-02-05 Misssing Class End
	
	}else{
	// FROM START TIME LAST TIME								
	$totals = $this->ModelHandler->getTeacherSalary($teacher_id,$t_start_time,$t_finish_time,'Lesson');						
	//first weekends																									
	$firsts = $this->ModelHandler->getTeacherSalary($teacher_id,$w1_start_time,$w1_finish_time,'Lesson');									
	//second weekends															
	$seconds = $this->ModelHandler->getTeacherSalary($teacher_id,$w2_start_time,$w2_finish_time,'Lesson');
	//third weekends								
	$thirds = $this->ModelHandler->getTeacherSalary($teacher_id,$w3_start_time,$w3_finish_time,'Lesson');
	//specials								
	$specials = $this->ModelHandler->getTeacherSalary($teacher_id,$s_start_time,$s_finish_time,'Lesson');
	//missing
	$missings = $this->ModelHandler->getMissingClass($teacher_id,$t_start_time,$t_start_time,'Lesson');
	//others
	$others = $this->ModelHandler->getOthersClass($teacher_id,$t_start_time,$t_finish_time,'Lesson');
	//2011-02-05 Misssing Class End
	}									
//2010-11-22 - 2010-12-05 FOR OFFICE BASE YHLEE END										
	
	//pr($totals);
	
	//make reserve time array
	//2011-02-05 Missing lesson Start 
	$lesson_count = array('first_F'=>0,'first_A'=>0,'second_F'=>0,'second_A'=>0,'third_F'=>0,'third_A'=>0,
	'weekday_F'=>0,'weekday_A'=>0,'special_F'=>0,'special_A'=>0,'all_F'=>0,'all_A'=>0,'missing_A'=>0);
	
	
	$first_reservation = array();
	$first_F = 0;
	$first_A = 0;
	foreach ($firsts as $first){
		// judge it is weekends or not
		array_push($first_reservation,$first['Lesson']['reserve_time']);
		if($first['Lesson']['lesson_status'] == 'F'){
			$lesson_count['first_F']++;
		}else if($first['Lesson']['lesson_status'] == 'A'){
			$lesson_count['first_A']++;
		}
		
	}
	
	//make reserve time array 
	$second_reservation = array();
	
	foreach ($seconds as $second){
		// judge it is weekends or not
		array_push($second_reservation,$second['Lesson']['reserve_time']);
		if($second['Lesson']['lesson_status'] == 'F'){
			$lesson_count['second_F']++;
		}else if($second['Lesson']['lesson_status'] == 'A'){
			$lesson_count['second_A']++;
		}
	}
	
	////make reserve time array 
	$third_reservation = array();
	
	foreach ($thirds as $third){
		// judge it is weekends or not
		array_push($second_reservation,$third['Lesson']['reserve_time']);
		if($third['Lesson']['lesson_status'] == 'F'){
			$lesson_count['third_F']++;
		}else if($third['Lesson']['lesson_status'] == 'A'){
			$lesson_count['third_A']++;
		}
	}
	 
	//make reserve time array 2010-01-05
	$special_reservation = array();
	foreach ($specials as $special){
		// judge it is weekends or not
		array_push($special_reservation,$special['Lesson']['reserve_time']);
		if($special['Lesson']['lesson_status'] == 'F'){
			$lesson_count['special_F']++;
		}else if($special['Lesson']['lesson_status'] == 'A'){
			$lesson_count['special_A']++;
		}
	}
	
	
	//only weekdays computting
	$weekdays = array();
	foreach ($totals as $total){
		// judge it is weekends or not
		if(!in_array($total['Lesson']['reserve_time'],$first_reservation)
		&& !in_array($total['Lesson']['reserve_time'],$second_reservation)
		&& !in_array($total['Lesson']['reserve_time'],$third_reservation)
		&& !in_array($total['Lesson']['reserve_time'],$special_reservation)){
			array_push($weekdays,$total);
		}
		
	}
	
	//Missing class Count
	$lesson_count['missing_A'] = count($missings);
	//make reserve time array 

	
	foreach ($weekdays as $weekday){
	
		if($weekday['Lesson']['lesson_status'] == 'F'){
			$lesson_count['weekday_F']++;
		}else if($weekday['Lesson']['lesson_status'] == 'A'){
			$lesson_count['weekday_A']++;
		}
	}
	
	//pr($weekdays);
	//computting
	$basic_salary =  $loginInfo['basic_salary'];
	$weekdays_rate =  $loginInfo['weekdays_rate'];
	$weekends_rate =  $loginInfo['weekends_rate'];
	$special_rate =  $loginInfo['special_rate'];
	
	$weekdays_a_rate =  $loginInfo['weekdays_a_rate'];
	$weekends_a_rate =  $loginInfo['weekends_a_rate'];
	$special_a_rate =  $loginInfo['special_a_rate'];
	
	$absent_rate = 30;
	
	
	$lesson_amount = array('first_F'=>0,'first_A'=>0,'second_F'=>0,'second_A'=>0,'third_F'=>0,'third_A'=>0,
	'weekday_F'=>0,'weekday_A'=>0,'first_sum'=>0,'second_sum'=>0,'weekday_sum'=>0,'total_sum'=>0,'all_F'=>0,'all_A'=>0
	,'missing_A'=>0);
	
	$lesson_amount['basic_salary'] = $basic_salary;
	
	$lesson_amount['half_basic_salary'] = $basic_salary/2;
	
	//2011-02-07 start
	$missing_rate = 50;
	//2011-02-07 end
	
	//
	$lesson_count['all_F'] = $lesson_count['weekday_F'] +  $lesson_count['first_F'] + $lesson_count['second_F'] + $lesson_count['third_F'];
	$lesson_count['all_A'] = $lesson_count['weekday_A'] +  $lesson_count['first_A'] + $lesson_count['second_A'] + $lesson_count['third_A'] ;
	//
	
	$lesson_amount['weekday_F'] = $lesson_count['weekday_F'] * $weekdays_rate;
	$lesson_amount['weekday_A'] = $lesson_count['weekday_A'] * $weekdays_a_rate;
	$lesson_amount['weekday_sum'] = $lesson_amount['weekday_F'] + $lesson_amount['weekday_A']; 
	
	//First
	$lesson_amount['first_F'] = $lesson_count['first_F'] * $weekends_rate;
	$lesson_amount['first_A'] = $lesson_count['first_A'] * $weekends_a_rate;
	$lesson_amount['first_sum'] = $lesson_amount['first_F'] + $lesson_amount['first_A'];
	
	//Second
	$lesson_amount['second_F'] = $lesson_count['second_F'] * $weekends_rate;
	$lesson_amount['second_A'] = $lesson_count['second_A'] * $weekends_a_rate;
	$lesson_amount['second_sum'] = $lesson_amount['second_F'] + $lesson_amount['second_A'];
	
	//Third
	$lesson_amount['third_F'] = $lesson_count['third_F'] * $weekends_rate;
	$lesson_amount['third_A'] = $lesson_count['third_A'] * $weekends_a_rate;
	$lesson_amount['third_sum'] = $lesson_amount['third_F'] + $lesson_amount['third_A'];
	
	//2011-01-05
	$lesson_amount['special_F'] = $lesson_count['special_F'] * $special_rate;
	$lesson_amount['special_A'] = $lesson_count['special_A'] * $special_a_rate;
	$lesson_amount['special_sum'] = $lesson_amount['special_F'] + $lesson_amount['special_A'];
	
	//2011-02-07
	$lesson_amount['missing_A'] = $lesson_count['missing_A'] * $missing_rate;
	
	//
	$lesson_amount['all_F'] = $lesson_amount['weekday_F'] + $lesson_amount['first_F']+ $lesson_amount['second_F']+ $lesson_amount['third_F'] ;
	$lesson_amount['all_A'] = $lesson_amount['weekday_A'] + $lesson_amount['first_A']+ $lesson_amount['second_A']+ $lesson_amount['third_A'] ;
	
	$lesson_amount['total_sum'] = $lesson_amount['half_basic_salary'] +$lesson_amount['weekday_sum'] + $lesson_amount['first_sum']
		+ $lesson_amount['second_sum'] + $lesson_amount['third_sum'] + $lesson_amount['special_sum'] - $lesson_amount['missing_A']; 
	
	//
	$lesson_amount['weekends_sum'] =  $lesson_amount['first_sum']+ $lesson_amount['second_sum']+ $lesson_amount['third_sum'];
	
	//2011-06-04 START LEVEL TEST
	//load goods model
	
	//price of peso
	$incentives = array('leveltest'=>10);
	
	$this->loadModel('Leveltest');
	
	$c_leveltest = $this->Leveltest->find('count', 
		array(  'fields' => 'COUNT(DISTINCT Leveltest.id) as count',
				'conditions' => array('Leveltest.teacher_id' => $teacher_id,
				array('Leveltest.created >=' =>$t_start_time,'Leveltest.created <=' =>$t_finish_time) 
		),
	));
	
	//comments
	$this->loadModel('Comment');
	$c_comments = $this->Comment->find('all', 
		array( 'conditions' => array('Comment.teacher_id' => $teacher_id,
				array('Comment.created >=' =>$t_start_time,'Comment.created <=' =>$t_finish_time),
		),
	));
	
	//2012-03-20 START
	//M : matrials comments
	//C : comments for students
	//S : skype comments
	$min_n=array('M'=>10,'C'=>50, 'S'=>100);
	$count_n=array('M'=>0,'C'=>0, 'S'=>0);
	$total_c_n = sizeof($c_comments);
	foreach($c_comments as $comment){
		
		$t_matrial_n = strlen($comment['Comment']['t_matrial']);
		$t_comment_n = strlen($comment['Comment']['t_comment']);
		$t_t_comment_n = strlen($comment['Comment']['t_t_comment']);
		
		//compare
		if($t_matrial_n >= $min_n['M']){
			$count_n['M']++;
		}
		
		if($t_comment_n >= $min_n['C']){
			$count_n['C']++;
		}
		
		if($t_comment_n >= $min_n['S']){
			$count_n['S']++;
		}
		
		$count_n['M']/$total_c_n;
	}
	
	
	
	//2012-03-20 END
	//pr($c_comments);
	
	
	$c_incentives = array('c_leveltest'=>$c_leveltest);
	
	$leveltest_sum = $c_leveltest * $incentives['leveltest'];
	
	$incentives_sum = array('leveltest_sum'=>$leveltest_sum);
	
	
	
	$this->set('incentives',$incentives);
	$this->set('c_incentives',$c_incentives);  
	$this->set('incentives_sum',$incentives_sum); 
	//2011-06-04 END
	
	
	//
	$this->set('lesson_count',$lesson_count); 
	$this->set('lesson_amount',$lesson_amount); 
	
	
	
	
	$this->set('totals',$totals);
	$this->set('firsts',$firsts);	
	$this->set('seconds',$seconds);
	$this->set('thirds',$thirds);
	$this->set('weekdays',$weekdays);
	$this->set('specials',$specials);
	$this->set('missings',$missings);

	//TITLE(수정)									
	$this->set('total_title','06.16.2012 - 06.30.2011');									
	$this->set('first_weekends_title','06.16.2012 - 06.17.2012');									
	$this->set('second_weekends_title','06.23.2012 - 06.24.2011');
	$this->set('third_weekends_title','06.30.2011');
										
	$this->set('weekdays_title','weekdays');
	$this->set('special_title','NO SPECIAL WEEK');
	
	$this->set('missing_title','Miss');
}

function teacher_salary_all(){
	
	$current_ymd = $this->ModelHandler->_timeymd;
										
	$s_weekday = sprintf("%s 02:00:00",'2012-04-01');									
	$f_weekday = sprintf("%s 02:00:00",'2012-04-16');
	
	$s_weekends1 = sprintf("%s 02:00:00",'2012-04-05');									
	$f_weekends1 = sprintf("%s 02:00:00",'2012-04-06');
	
	$s_weekends2 = sprintf("%s 02:00:00",'2012-04-08');									
	$f_weekends2 = sprintf("%s 02:00:00",'2012-04-09');
	
	$periods = array();
	
	$periods[0] = array($s_weekday,$f_weekday);
	$periods[1] = array($s_weekends1,$f_weekends1);
	$periods[2] = array($s_weekends1,$f_weekends2);
	
	
	//$teacher_id = 1;
	
	$lesson_kinds = array ('R','F','A','E','C','M');
	
	$lessons_total = array();
	
	foreach( $periods as $period ){
		
		$lessons = $this->Lesson->find('all', array(
					'fields' => array('Lesson.teacher_id','Lesson.lesson_status','count(Lesson.lesson_status) AS count'),
					'conditions' => array(
		 			array ('OR' =>array('Lesson.lesson_status'=> $lesson_kinds))
		 			,'Lesson.reserve_time >='=> $period[0]
		 			,'Lesson.reserve_time <='=> $period[1]
		 			)
		 			,'group' => array('Lesson.teacher_id','Lesson.lesson_status')
		 			,'order' =>	array('Lesson.teacher_id')
		 			));
	
		array_push($lessons_total,$lessons);
			
	}
	
    pr('TTTTT'); 
	pr($lessons_total[0]);
	
	pr('TTTTT'); 
	pr($lessons_total[1]);
	
	pr('TTTTT'); 
	pr($lessons_total[2]);
	 
	 //pr($lessons);
	
}

//2010-05-20 head teacher make lesson
function teacher_make_lesson()
{
	//2011-02-03 Start
	$allTeachers = $this->ModelHandler->getAllTeacher('Lesson');
	$allTeachers_select = get_all_teachers($allTeachers);
	$this->set('allTeachers',$allTeachers);	
	$this->set('allTeachers_select',$allTeachers_select);
	//2011-02-03 End
}

//2010-05-20 complete


//2010-06-15 head teacher make lesson
function teacher_make_lesson_confirm()
{
	//receive parameters
	$nick_name = trim($this->params['form']['nick_name']);
	//$teacher_mb_id = trim($this->params['form']['teacher_mb_id']);
	$teacher_id = trim($this->params['form']['teacher_id']);
	$lesson_date = trim($this->params['form']['lesson_date']);
	$lesson_time = trim($this->params['form']['lesson_time']);
	$kinds = trim($this->params['form']['kinds']);
	$day = trim($this->params['form']['day']);
	
	//Get User_id ID from nick_user_nick_name
	
	$user = $this->ModelHandler->get_userid_by_nickname($nick_name, 'Lesson');
	//$teacher = $this->ModelHandler->get_teacherid_by_mbid($teacher_mb_id, 'Lesson');
	
	//Default Teacher
	$search_conditions['teacher_id'] = $teacher_id;
	
	$teacher = $this->ModelDefaultHandler->getDefaultTeacher($search_conditions,'Lesson');
	
	$user_id = $user[0]['User']['id'];
	$teacher_id = $teacher[0]['Teacher']['id'];
	
	$lesson_query_time = sprintf("%s-%s-%s %s:%s:00",
		substr($lesson_date,0,4),substr($lesson_date,4,2),substr($lesson_date,6,2),substr($lesson_time,0,2),substr($lesson_time,2,4));
	
	print_r($lesson_query_time);
	
	
	for($i=0;$i<$day;$i++){
		
		if($i==0){
			$onday_plus = 0;
		}else{
			$onday_plus = 1;
		}
		
		$lesson_query_time = date("Y-m-d H:i:s",strtotime('+'.$onday_plus. 'day', strtotime($lesson_query_time)));
	
		//checking the teacher's
		$available_lesson_counts = $this->ModelHandler->get_count_available_lessons($teacher_id,$lesson_query_time,'Lesson');
		$lesson_counts = $available_lesson_counts[0][0]['counts'];
		//insert available_lesson
		
		 $available_lesson_Data['teacher_id'] = $teacher_id;
		 $available_lesson_Data['available_time'] = $lesson_query_time;
		 $available_lesson_Data['reserve_status'] = 'F';
		 $available_lesson_Data['teacher_comment'] = '';
			 
		if($lesson_counts == '0'){
				
			$this->ModelHandler->insert_available_lessons($available_lesson_Data,'Lesson');
		
		}else{
			
			$this->ModelHandler->update_available_lessons($available_lesson_Data,'Lesson');
		}
		
		//get availalesson id by teacher_id and reserve_time
		$available_lesson_id_array = $this->ModelHandler->get_available_lessons($teacher_id,$lesson_query_time,'Lesson');
		
		$available_lesson_id = $available_lesson_id_array[0]['Available_lesson']['id'];
		
		pr($available_lesson_id);
		
		//lessonテーブルにインサーとする
		$lessonData['user_id'] = $user_id;
		$lessonData['teacher_id'] = $teacher_id;
		$lessonData['available_lesson_id'] = $available_lesson_id;
		$lessonData['lesson_status'] = 'R';
		$lessonData['point_id'] ='';
		
		$lessonData['comment_id'] = '';
		$lessonData['spend_point'] = $teacher[0]['Teacher']['tea_point'];
		$lessonData['reserve_time'] = $lesson_query_time;
		
		$lessonData['text_code'] = '01';
		
		//2010-06-17 start
		$lessonData['reservation_kinds'] = $kinds;
		//2010-06-17 end
		$this->ModelHandler->insertLesson2($lessonData,'Lesson');
	}
	//for End
	//print_r($lesson_counts);
	//
}	

//2010-06-15 complete


//2010-09-03 head teacher make lesson
function teacher_make_contract()
{

}
//2010-09-03 complete



//2009-11-17 先生のマイページからＦＡボタンを押して、レッスン
//状況変えて先生のページに戻る
function teacher_change_lesson_status($lesson_id = null, $update = null ,  $search_type = null, $page = null)
{

 $checkLessonReserveTimeBefore = $this->Programmer1Handler->checkLessonReserveTimeBefore($lesson_id);
 $this->set('checkLessonReserveTimeBefore',$checkLessonReserveTimeBefore);
 
 if ($checkLessonReserveTimeBefore[0] == 'yes') { // if the teacher's allow to change lesson status;

		$teacher = $this->Session->read('loginInfo');
		
		$lessonData['lesson_status'] = $update;
		$lessonData['id'] = $lesson_id;
		
		//2012-11-10 START(Without comments can not be change status)
		
		//2012-11-10 END
		$comments = $this -> CheckHandler ->checkCommentBeforeChangeStatus($lesson_id);
		
		$matrials = $comments['matrials'];
		$comment = $comments['comment'];
		$skype = $comments['skype'];
		
	    
		
		
		$result = true;
		if( $matrials == 0 || $comment == 0 || $skype == 0){
			
			if($matrials == 0 ){
				//echo '<script>alert("please input the matrials in comment")</script>';					
			}
			
			$result = false;
		}
		
		if($result == true){
			/***********UPDATE FIXEDSCHEDULE**********sept 5 12*****/
			$this -> FixedscheduleHandler -> updateFixedscheduleColumns($lessonData['lesson_status'],$lessonData['id']);
			/*********EN$D OF UPDATE FIXEDSCHEDULE**********/
			
			$this->ModelHandler->updateLessonStatus($lessonData , 'Lesson');
			
			//2010-10-17 point accumulating events START
			//only in case of F point accumulating
			if($lessonData['lesson_status']=='F'){
				//insert to mb_4 point table 	
			}
			//2010-10-17 point accumatint events END
			
			//
			if($search_type == 'mypage_search'){
				
				$this->redirect(array('action' => 'teacher_mypage_search/'.$search_type));
				
			}else{
				
				$this->redirect(array('action' => 'teacher_mypage/'.$teacher['id'].'/'.$search_type));
			}
		
		}else{
			//comments set
			$this->set('comments',$comments );
			$this->set('lesson_id',$lesson_id );
			
		}
	//}
 }

}

function teacher_admin_change_status($lesson_id = null, $update = null)
{
	$teacher = $this->Session->read('loginInfo');
	$lessonData['lesson_status'] = $update;
	$lessonData['id'] = $lesson_id;
	$this->ModelHandler->updateLessonStatus($lessonData , 'Lesson');
	
	//2010-10-17 point accumulating events START
	
	if($lessonData['lesson_status']=='E' || $lessonData['lesson_status']=='P' 
		){
		//insert lessondetails
	}
	
	$this->redirect(array('action' => 'teacher_admin/'.$teacher['id']));
}

function myhistory($user_id = null)
{
	$loginInfo = $this->Session->read('loginInfo');
	$user_id = $loginInfo['User']['id'];
	
	//Available_lesson.reserve_status状態が予約完了のみ表示
	/*$find_condition = array(
	'conditions' =>"Lesson.user_id = {$user_id} AND ( Available_lesson.reserve_status = '2' 
					OR  Available_lesson.reserve_status = '3' 
					OR  Available_lesson.reserve_status = '4') " 
							,'order'=>array('Available_lesson.available_time DESC')
							,'limit' => 30 );*/
	//授業状態０：未予約　１：予約完了　２：キャセル　３：評価未送信　４：評価送信
	//$find_condition = array(
	//'conditions' =>"Lesson.user_id = {$user_id}  AND (Lesson.lesson_status = '3' OR Lesson.lesson_status = '4')  " 
	//						,'order'=>array(' Lesson.reserve_time DESC')
	//						,'limit' => 30 );
							
	//$lessons = $this->Lesson->find('all',$find_condition);
	
	$lessons = $this->ModelHandler->getMyHistoryByUserId($user_id,'Lesson');
	//print_r($lessons);
	
	$this->set('lessons',$lessons);	
}

function teacher_myhistory( $page=null )
{
	//login 情報チック
	$loginInfo = $this->CheckHandler->checkLoginInfo();
	//
	global $const;
	$this->set('const',$const);
	
	$configs = $this->ModelHandler->getConfig('Lesson');
	$config = $configs[0];
	$this->Session->write('config',$config);
	
	$configs = $this->Session->read('config');
	$rows = $configs['Config']['teacher_lessons_rows'];

	$total_count = $this->ModelHandler->getTeacherLessonHistory($loginInfo['id'],'Lesson','YES');
	$total_count = $total_count[0][0]['total_count'];
	$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산 
	if($page==null){
		$page = 0;
	}
	if (!$page) { $page = 1; } // 페이지가 없으면 첫 페이지 (1 페이지)
	$from_record = ($page - 1) * $rows; // 시작 열을 구함
	$get_paging = get_paging($rows, $page, $total_page, "lessons/teacher_myhistory");
	
	
	$lessons = $this->ModelHandler->getTeacherLessonHistory($loginInfo['id'],'Lesson','NO',$from_record);
	
	
	
	
	$this->set('get_paging',$get_paging);
	$this->set('lessons',$lessons);	
	
}

//Lesson 予約完了


function teacher_reserve_complete()
{
	//TEACHER＿IDを設定(次の予約ために同じ先生のページに飛ぶように)
	$ct = get_time();	//現在の時間を求める
	$current_time = $ct['time_ymdhis'];
	$tacherConfirmSch = $this->Session->read('tacherConfirmSch');
	$teacher_id = $this->data['Lesson']['teacher_id'];
	
	$this->loadModel('Available_lesson');
	
	if(isset($this->params['form']['teacher_time']) && count($this->params['form']['teacher_time']) > 0){
		$tacherConfirmSch = $this->params['form']['teacher_time'];
	}
	
	$class_minutes = false;
	if(isset($this->params['form']['class_minutes']) && ($this->params['form']['class_minutes'] == 1 || $this->params['form']['class_minutes'] == true)){
		$class_minutes = true;
	}
	
	//print_r($this->data);
	$today = date('Y-m-d H:i:s');
	foreach($tacherConfirmSch as $sch) {
		$available_time = sprintf("%d-%d-%d %d:%d:00",
		substr($sch,0,4),substr($sch,4,2),substr($sch,6,2),substr($sch,8,2),substr($sch,10,2));
		//2010-03-22 one hour plus START
		$available_time = change_ph_time($available_time,'AHEAD'); 
		//2010-03-22 one hour plus END
		
		//Check available_lesson
    	$id = $this->Available_lesson->find('first', array(
    						'fields' => array('id'),
    						'conditions' => array(
    							'Available_lesson.available_time' => $available_time 
    							,'Available_lesson.teacher_id' => $teacher_id
    							// R = already open, T = somebody canceled or transfer
    							,'Available_lesson.reserve_status' => array('R','T'))
    							));
    	
    	$Available_lesson_id = null;
    	
    	if(sizeof($id) != 0){
    		$Available_lesson_id = $id['Available_lesson']['id'];
    	}
		
		$this->data['Available_lesson']['id'] =  $Available_lesson_id;
    	$this->data['Available_lesson']['teacher_id'] =  $teacher_id;
		$this->data['Available_lesson']['available_time'] =  $available_time;
		$this->data['Available_lesson']['reserve_status'] =  'R';
		$this->data['Available_lesson']['modified'] =  $today;
		if($class_minutes === true){
			$this->data['Available_lesson']['class_minutes'] =  '10';
		}
			
    	if($Available_lesson_id == null){
    		$this->data['Available_lesson']['created'] =  $today;
    		$this->Available_lesson->create();
    	}
    	//common
        $this->Available_lesson->save($this->data);
				        
//		$sql = " insert into available_lessons  
//                set teacher_id = '$teacher_id' ,
//                    available_time = '$available_time',
//                    reserve_status = 'R',
//                    teacher_comment = '',
//                    created = '$current_time',
//                    modified = '$current_time'";
//                   
//		$free_sql_result = $this->Lesson->free_sql($sql);
	}
	
	//クリックしたセッションを消す
	//戻った時に青いまま残っているから
	$this->Session->write('tacherConfirmSch',null);
	
	$this->set('messages',null);
	$this->set('teacher_id',$this->data['Lesson']['teacher_id']);
}

function teacher_cancel_complete()
{
	//TEACHER＿IDを設定(次の予約ために同じ先生のページに飛ぶように)
	$ct = get_time();	//現在の時間を求める
	$current_time = $ct['time_ymdhis'];
	$tacherConfirmSch = $this->Session->read('tacherConfirmSch');
	$teacher_id = $this->data['Lesson']['teacher_id'];
	
	if(isset($this->params['form']['teacher_time']) && count($this->params['form']['teacher_time']) > 0){
		$tacherConfirmSch = $this->params['form']['teacher_time'];
	}
	
	$class_minutes = false;
	if(isset($this->params['form']['class_minutes']) && ($this->params['form']['class_minutes'] == 1 || $this->params['form']['class_minutes'] == true)){
		$class_minutes = true;
	}
	
	//print_r($this->data);
	foreach($tacherConfirmSch as $sch) {
		$available_time = sprintf("%d-%d-%d %d:%d:00",
		substr($sch,0,4),substr($sch,4,2),substr($sch,6,2),substr($sch,8,2),substr($sch,10,2));
		
		//2010-03-22 one hour plus START
		$available_time = change_ph_time($available_time,'AHEAD'); 
		//2010-03-22 one hour plus END
		//2011-04-07 change status from C to T (C is user cancel T is teacher cancel )
		$sql = " UPDATE available_lessons  
                 SET 
                    reserve_status = 'T',
                    modified = '$current_time' 
                 WHERE teacher_id = '$teacher_id' 
                 AND available_time = '$available_time'";
                   
		$free_sql_result = $this->Lesson->free_sql($sql);
	}
	
	//クリックしたセッションを消す
	//戻った時に青いまま残っているから
	$this->Session->write('tacherConfirmSch',null);
	
	$this->set('messages',null);
	$this->set('teacher_id',$this->data['Lesson']['teacher_id']);
}

function teacher_lessonplan($_id = null)
{
	$matrials = $this->ModelHandler->getMatrials('Lesson');
	$this->set('_matrials', $this->Lesson->query("SELECT Pdf.id, Pdf.title FROM pdfs as Pdf WHERE 1 = 1 GROUP BY Pdf.title ORDER BY Pdf.id ASC"));
	$_p = array(1 => null, 2 => null);
	if (!empty($_id ) && isset($this->params['form']['title'])) {
		$_p = array(1 => $this->params['form']['title'], 2 => $this->params['form']['title']);
		$matrials = $this->Lesson->query("SELECT Pdf.id, Pdf.title, Pdf.sub_title, Pdf.polder, Pdf.pdf FROM pdfs as Pdf WHERE Pdf.title = '$_p[1]' ORDER BY Pdf.id ASC");
	}
	$this->set('_pdata',$_p);
	$this->set('matrials',$matrials);
}

function teacher_websites()
{
	$matrials = $this->ModelHandler->getMatrials('Lesson');
	$this->set('matrials',$matrials);
}

//2010-02-03 YHLEE ADD START
//Head teacher can manage the other teacher's schedule
function temp_teacher_management()
{
	//セッション情報がなくなった場合、Cubicboardのログインページに遷移する
	
	$loginInfo = $this->CheckHandler->checkLoginInfo();
	//2010-02-22 YHLEE START
	$search_type = null;
	
	if(isset($this->params['form']['search_type'])){
		$search_type = $this->params['form']['search_type'];
	}else{
		$search_type = 'today_lesson';
	}
	
	//2010-02-22 YHLEE END
	$current_ymd = $this->ModelHandler->_timeymd;
	if($search_type=='last_lesson' ||  $search_type =='cancel_lesson'){
		$start_time = sprintf("%s 00:00:00",'2010-10-18');
	}else if($search_type=='today_lesson'){
		$start_time = sprintf("%s 01:00:00",$current_ymd);	
	}else if($search_type=='after_today_lesson'){
		//24hours
		$oneday = 60*60*24;
		$tomorrow = date("Y-m-d", time()+ $oneday);
		$start_time = sprintf("%s 00:00:00",$tomorrow);
	}else if($search_type=='change_teacher' || $search_type =='change_teacher_complete'
			|| $search_type =='cancel_lesson'){
		$start_time = sprintf("%s 00:00:00",$current_ymd);	
	}
	
	if($search_type=='last_lesson' || $search_type=='today_lesson'){
		//tmp excise
		$start_time = sprintf("%s 00:00:00",'2010-09-18');
		$finish_time = sprintf("%s 00:00:00",'2010-10-19');
		//
		
		//$finish_time = sprintf("%s 00:00:00",$current_ymd);
		//1hour plus + korean time = philiphine time
		$finish_time = date("Y-m-d H:i:s",strtotime($finish_time)+ 60*60*25);	
	}else if($search_type =='after_today_lesson'){
		$finish_time = sprintf("%s 24:00:00",'2099-01-01');
	}else if($search_type =='change_teacher' || $search_type =='change_teacher_complete'
			|| $search_type =='cancel_lesson'){
		$finish_time = sprintf("%s 24:00:00",'2099-01-01');
	}
	
	// If have change teacher request start
	//1.get a lesson information by lesson_id
		if($search_type =='change_teacher_complete'){
		$lesson_id = $this->params['form']['lesson_id'];
		$new_teacher_id = $this->params['form']['new_teacher_id'];
		$lessons = $this->ModelHandler->getLessonByLessonId($lesson_id,'Lesson'); 
		
		//1.get original teacher id and change status
		$original_teacher_id = $lessons[0]['Lesson']['teacher_id'];
		
		if($new_teacher_id != $original_teacher_id ){
			
			$update_common['id'] = $lesson_id;
			$update_common['lesson_status'] = 'S'; //substitute
			$this->Lesson->update_common('lessons',$update_common);
			
				
			//updateしたい項目をセットする(available_lesson　テーブル)
			$update_common = null;
			$update_common['id'] = $lessons[0]['Lesson']['available_lesson_id'];
			$update_common['reserve_status'] = 'S';  //subsitute
			$this->Lesson->update_common('available_lessons',$update_common);
			
			//2.check new teacher has already reserve or not with lesson time and new_teacher_id
			$search_conditions['teacher_id'] = $new_teacher_id; 
			$search_conditions['reserve_time'] = $lessons[0]['Lesson']['reserve_time'];
			$lesson_query_time = $search_conditions['reserve_time'];
			$reservation_kinds = $lessons[0]['Lesson']['reservation_kinds'];
			$spend_point = $lessons[0]['Lesson']['spend_point'];
			
			//Get User_id ID from nick_user_nick_name
			$teacher = $this->ModelHandler->get_teacherid_by_mbid($new_teacher_id, 'Lesson');
		
			$user_id = $lessons[0]['Lesson']['user_id'];
			
			
			//checking the teacher's
			$available_lesson_counts = $this->ModelHandler->get_count_available_lessons($new_teacher_id,$lesson_query_time,'Lesson');
			$lesson_counts = $available_lesson_counts[0][0]['counts'];
			//insert available_lesson
			
			 $available_lesson_Data['teacher_id'] = $new_teacher_id;
			 $available_lesson_Data['available_time'] = $lesson_query_time;
			 $available_lesson_Data['reserve_status'] = 'F';
			 $available_lesson_Data['teacher_comment'] = '';
				 
			if($lesson_counts == '0'){
					
				$this->ModelHandler->insert_available_lessons($available_lesson_Data,'Lesson');
			
			}else{
				
				$this->ModelHandler->update_available_lessons($available_lesson_Data,'Lesson');
			}
			
			//get availalesson id by teacher_id and reserve_time
			$available_lesson = $this->ModelHandler->get_available_lessons($new_teacher_id,$lesson_query_time,'Lesson');
			$available_lesson_id = $available_lesson[0]['Available_lesson']['id'];
			
			//lessonテーブルにインサーとする
			$lessonData['user_id'] = $user_id;
			$lessonData['teacher_id'] = $new_teacher_id;
			$lessonData['available_lesson_id'] = $available_lesson_id;
			$lessonData['lesson_status'] = 'R';
			$lessonData['point_id'] ='';
			
			$lessonData['comment_id'] = '';
			$lessonData['spend_point'] = $spend_point;
			$lessonData['reserve_time'] = $lesson_query_time;
			
			$lessonData['text_code'] = '01';
			
			//2010-06-17 start
			$lessonData['reservation_kinds'] = $reservation_kinds;
			//2010-06-17 end
			$this->ModelHandler->insertLesson2($lessonData,'Lesson');
		}
		//print_r($is_open_class);
	}
	// teacher request end
	
	// 2010-03-29 START
	//updateしたい項目をセットする(lesson　テーブル)
	if($search_type =='cancel_lesson'){
		$lesson_id = $this->params['form']['lesson_id'];
		$lessons = $this->ModelHandler->getLessonByLessonId($lesson_id,'Lesson');
		$update_common['id'] = $lesson_id;
		$update_common['lesson_status'] = 'H';
		$this->Lesson->update_common('lessons',$update_common);
		
		//updateしたい項目をセットする(available_lesson　テーブル)
		$update_common = null;
		$update_common['id'] = $lessons[0]['Lesson']['available_lesson_id'];
		$update_common['reserve_status'] = 'R';
		$this->Lesson->update_common('available_lessons',$update_common);
	}
	// 2010-03-29 END
	
	// 2010-09-29 START
	if($search_type =='miss_lesson'){
		$lesson_id = $this->params['form']['lesson_id'];
		$lessons = $this->ModelHandler->getLessonByLessonId($lesson_id,'Lesson');
		$update_common['id'] = $lesson_id;
		$update_common['lesson_status'] = 'M';
		$this->Lesson->update_common('lessons',$update_common);
		
		//updateしたい項目をセットする(available_lesson　テーブル)
		$update_common = null;
		$update_common['id'] = $lessons[0]['Lesson']['available_lesson_id'];
		$update_common['reserve_status'] = 'R';
		$this->Lesson->update_common('available_lessons',$update_common);
	}
	// 2010-03-29 END
	
	$allTeachersLessons = $this->ModelHandler->getAllTeacherReservation('Lesson',$start_time,$finish_time);
	
	//2010-03-27 change teacher start
	//2010-10-20 remove the if clause
	//if($search_type =='change_teacher' ||  $search_type =='change_teacher_complete'){
		$allTeachers = $this->ModelHandler->getAllTeacher('Lesson');
		
		$this->set('allTeachers',$allTeachers);		
	//}
	//2010-03-27 change teacher end 
	$this->set('allTeachersLessons',$allTeachersLessons);
	
}

//2011
function teacher_make_contract_confirm()
{

	if($this->data['Payment']['abc'] ==  $this -> Session -> read('dennis')){//session
	
		if (!empty($this->data) && isset($this->data)) {
			//load goods model
			$this->loadModel('Good');
			//get searchs goods info by goods_name
			$good = $this->Good->findbyGoodsName($this->data['Payment']['goods_name']);
			$amount = $good['Good']['goods_amount']; 
			$goods_name  = $good['Good']['goods_name'];
		
			//load payments model		
			$this->loadModel('Payment');	
			//set pay_type to cash value
			$this->data['Payment']['pay_type'] = 'cash';
			//set payment amount value
			$this->data['Payment']['amount'] = $amount;
			
			//insert payment on payments value
			if($this->Payment->save($this->data)){
				//read inserted record on payments table
				$payments = $this->Payment->read();
				//set payment record
				$this->set('payments',$payments);
				
				//load users model
				$this->loadModel('User');
				//search user record on table user by mb_id	
				$user = $this->User->findbyMbId($this->data['Payment']['mb_id']);
				//read user record by id
				$this->User->read(null, $user['User']['id']);
				
				//assign new value on array to be use on user table
				$this->User->set(array(
					 'pay_plan' => $goods_name,
					 'start_day' => $payments['Payment']['start_day']
					 ));

				//update user record	 
				 $this->User->save();
				//read user record
				 $user= $this->User->read();
				$this->set(compact('user'));
				
				//set session var to black
				$this -> Session -> write('dennis','');
			} // end if the new payment is inserted on table payments
	
		}//end if the data is not empty
	
		
	
	}else{
	
		$this->redirect(array('action' => 'teacher_make_contract'));
	
	}//end session
}
	//Added by Dennis
//Feb 19,2011
//Create a display on teacher statistics  according to the conditions


//Added by Dennis
//Feb 19,2011
//Create a display on teacher statistics  according to the conditions
function teacher_statistics_report()
{
	

	//load teachers model
	$this->loadModel('Teacher');
	//assign status on array
	$status = array('F','R','E','M');
	//load available_lessons model
	$this->loadModel('Available_lesson');
	$teachers = array();
	
	if($this->data['Teacher']['id'] != null){
		//if the condition are equal to the id create an array
		$teachersOption = array($this->data['Teacher']['id'] => $this->data['Teacher']['id']);
		
	}else{
		//if condition are equal to all query on table teachers and create new array
		$teachersOption = $this->Teacher->find('list', array('fields' => array('Teacher.id', 'Teacher.tea_name')));
	}
	
	
		while ($temp_value = current($teachersOption)) {
	        // change array to integer
			$id = intval(key($teachersOption));
			
			$teacher = array();
	        // find teacher name on teachers table
			$teacherName = $this->Teacher->findbyId($id);
			// add teacher name on teacher array
		 	array_push($teacher,$teacherName['Teacher']['tea_name']);
		
		 	//reset status array
			reset($status);
	        $ctr =0;$reservation = 0;
	        
	        //get the value of teacher according to status
	         while (list($key, $val) = each($status)) {	
	         	 $reservation = $this->Available_lesson->getTeacherReservations($id,$this->data['Teacher']['start_day'],$this->data['Teacher']['end_day'],$val);
				 $ctr += $reservation;
				 $status_count[$val] =  $reservation;
				}
	
				//create and assign status on new array	
				$status2 = array('F'=>'FINISH','R'=>'RESERVATION','E'=>'EXTENTION','M'=>'MISSING');
				while (list($key, $val) = each($status2)) {
					//calculate rate
					$rate = ($ctr <= 0) ?  0 :  ($status_count[$key]/$ctr)*100;
					array_push($teacher,$status_count[$key]);
					array_push($teacher,round($rate));
				}
				//add array teacher to new array teachers
				array_push($teachers,$teacher);
				
				unset($teacher);			
			//go to the new teacher id	
	    	next($teachersOption);
		}//end while loop for teachers
	$this->set(compact('teachers'));
	
}
//teacher_statistics_report

//Added by Dennis
//Feb 19,2011
//Create a display on teacher statistics
function teacher_statistics_search() {
	$this->loadModel('Teacher'); //load teachers model
	//retrieve teachers name on table teachers
	$teachersOption = $this->Teacher->find('list', array('fields' => array('Teacher.id', 'Teacher.tea_name')));
	//set data on new variables to be use on view 
	$this->set(compact('teachersOption'));
	
}
//end statistics
//Added by Dennis 
//Feb 20,2010
//search teacher schedules
function teacher_schedules_search(){
	$this->Lesson->bindModel(array('belongsTo' => array('Teacher'=>array('className' => 'Teacher','foreignKey' => 'teacher_id'))));
	
	$this->loadModel('Teacher'); //load teachers model
	//retrieve teachers name on table teachers
	$teachersOption = $this->Teacher->find('list', array('fields' => array('Teacher.id', 'Teacher.tea_name'),
													 'conditions' => array('Teacher.show'=> 'Y')
		));
	//create array on status level
	$status2 = array('F'=>'FINISH','R'=>'RESERVATION','E'=>'EXTENSION','M'=>'MISSING');
	//declare new array as time container
	$statustime = array();
	//for loop for creating value	
	for ($i = 05; $i <= 25; $i++) {
		//declare var i as integer
		$i = intval($i);
		//check if var i is less than 10 
		//if yes  add zero 
		if($i < 10) $i = '0'.$i;
		//declare time	
		$hour = $i .":00";
		//insert value on array
		$statustime[$hour] = $hour;
		$hour2 = $i .":30";
		//insert value on array
		$statustime[$hour2] = $hour2;
	}
	
	//2011-08-13 START
	$this->loadModel('Available_lesson');
	
	//$this->Lesson->bindModel(array('belongsTo' => array('Teacher'=>array('className' => 'Teacher','foreignKey' => 'teacher_id'))));
    	 
//	$this->Available_lesson->bindModel(array('belongsTo'=>array('Teacher'=>
//		array(
//		 'className' => 'Teacher',
//		 'foreignKey' => 'teacher_id'
//	))));
	
	//$teachersOption = $this->Available_lesson->find('all',
	    //array('conditions' => array('Lesson.id' => '1'))
		//array('fields' => array('Teacher.id', 'Teacher.tea_name'))
	//	);
	//2011-08-13 END
	
	//$this->Available_lesson->bindModel(array('belongsTo' => array('Teacher'=>array('className' => 'Teacher','foreignKey' => 'teacher_id'))));
		
		
	$time = get_time();
	
	$today = $time['time_ymd'];
	
	$start_day = sprintf("%s 02:00:00",$today);
	
	//$extend_only_day =  date("Y-m-d",strtotime('+1 day', strtotime($extend_only_day)));
	$end_day = date("Y-m-d H:i:s", strtotime('+1 day',strtotime($start_day)));
	
	$teachersSchs = $this->Available_lesson->find('all', 
		array('fields' => array('Teacher.id', 'Teacher.tea_name','Available_lesson.available_time','Available_lesson.reserve_status'),
			   'conditions' => array('Available_lesson.available_time >='=>$start_day, 'Available_lesson.available_time <='=>$end_day),
			    'order' => array('Teacher.id','Available_lesson.available_time ASC'), //string or array defining order
				'group' => array('Available_lesson.available_time','Teacher.tea_name') //fields to GROUP BY
			)
		);	
//	
	//pr($teachersSchs);
	
	//2012-01-22 teacher managing schedule
	$search_day = date("Y-m-d", strtotime($start_day));
	
	$fill_array = array_fill_keys($statustime, 'N');
	
	$teacher_sch_all = array();
	
	$index = 0 ;
	$array_size = sizeof($teachersSchs);
	$before_teacher_id;
	
	//By classfying the teacher id
	$arr_all_teachers = array();
	
	$count = 0;
	$before_teacher_id = -1;
	$arr_size = sizeof($teachersSchs);
	
	foreach($teachersSchs as $teachersSch){
			
			$teacher_id = $teachersSch['Teacher']['id'];
			$teacher_name = $teachersSch['Teacher']['tea_name'];
			$available_time = $teachersSch['Available_lesson']['available_time'];
			$reserve_status = $teachersSch['Available_lesson']['reserve_status'];
			
			if($teacher_id != $before_teacher_id ){
				if($count != 0){
					array_push($arr_all_teachers,$arr_teacher);
				}
				$arr_teacher = array();
			}
			
			$arr['id'] = $teacher_id;
			$arr['name'] = $teacher_name; 
			$arr['available_time'] = $available_time;
			$arr['reserve_status'] = $reserve_status;
			array_push($arr_teacher,$arr);
			$before_teacher_id = $teacher_id;
			
			if($arr_size-1 == $count){
				array_push($arr_all_teachers,$arr_teacher);
			}
			$count++;
	}	
	
	//pr($arr_all_teachers);
	
	$arr_search_time = array();
	
	foreach($fill_array as $key=>$value ){
		
		$search_time = sprintf("%s %s:00",$search_day,$key);
		$arr_search_time[] = $search_time;
	};	
	
	//pr($arr_search_time);
	
	$sch_all_array = array();
	
	foreach($arr_all_teachers as $arr_teacher ){
		
		$sch_array = array();
		$new_fill_array = array();
		$new_fill_array =$fill_array; 
		
		foreach($arr_teacher as $arr ){
			
			$available_time = $arr['available_time'];
			$reserve_status = $arr['reserve_status'];
			$compare_time = substr($available_time, 11, 5);
			
			if(array_key_exists($compare_time,$new_fill_array)){
				$new_fill_array[$compare_time] = $reserve_status;
				//echo(test);
			}
			
			//pr($compare_time);
			
			$sch_array['id'] = $arr['id'];
			$sch_array['name'] = $arr['name'];
			$sch_array['sch'] = $new_fill_array;
		}
		
		$sch_all_array[] = $sch_array;
	};

	//pr($sch_all_array);
	
	
	
	
	
	//set data on new variables to be use on view 
	$this->set(compact('teachersOption','status2','statustime'));
	$this->set('sch_all_array',$sch_all_array);
	
}
//end teacher_schedules_sear

//Added by Dennis
//Feb 20, 2011
//Display teacher search result
function teacher_schedules_report() {
	//load teachers model
	//$this->loadModel('Teacher');
	$this->loadModel('Available_lesson');
	//set as null
	$stat_conditions = null;
	$status_time = null;
	$status_level = null;
	
		//if teacher id is not null add conditions		
		if($this->data['Teacher']['id'] != null){
			$stat_conditions = array('Available_lesson.teacher_id' => $this->data['Teacher']['id']);
		}
		
		//if teacher statustime not null add conditions
		if($this->data['Teacher']['statustime'] != null){
			$status_time =  $this->data['Teacher']['statustime'];
		}
		pr($status_time);
		//if status level not null add condition
		if($this->data['Teacher']['statuslevel'] != null){
			$status_level=	array('Available_lesson.reserve_status' => $this->data['Teacher']['statuslevel']);
		}
		
		//2011-02-26 YHLEE MODIFY START
		//Change Korean Time to Philiphine Time
		$ph_start_time = $this->data['Teacher']['start_day'] .' '. $status_time;
		$ph_end_time = $this->data['Teacher']['end_day'] .' '. $status_time;
		pr($ph_start_time);
		pr($ph_end_time);
		$ko_start_time = change_ph_time($ph_start_time,'AHEAD');
		$ko_end_time = change_ph_time($ph_start_time,'AHEAD');
		pr($ko_start_time);
		//2011-02-26 YHLEE MODIFY END
		//search available lessons according to the conditions conditions	
		
		$statistics = $this->Available_lesson->getTeacherStatistics($stat_conditions, $ko_start_time, $ko_end_time, $status_time,$status_level);
		$this->set(compact('statistics'));	
		
}
//end teacher_schedules_report

function salarydynamic() {
	//セッション情報がなくなった場合、Cubicboardのログインページに遷移する
	
	$loginInfo = $this->CheckHandler->checkLoginInfo();
	
	//get teacher_id
	$loginInfo = $this->Session->read('loginInfo');
	
	$teacher_id = $loginInfo['id'];
									
	$current_ymd = $this->ModelHandler->_timeymd;									

	//Added by Dennis
	$start_payment = null;
	$end_payment = null;
	$payment_allowed = null;
	$start_time = 0;
	$finish_time = 0;
	$start_special_day = null;
	$end_special_day = null;
	
	$this->loadModel('Salary');
	$rangeMonth = $this->Salary->find('all', array(
								'conditions' => array('Salary.flag' => 'Y'),
								'recursive' => 0
								));
	$this->loadModel('SpecialDate');
	$specialMonth = $this->SpecialDate->find('all');	
							
	

	if(count($rangeMonth) != 0){							
	$start_payment = $rangeMonth['0']['Salary']['start_day'];
	$end_payment = $rangeMonth['0']['Salary']['end_day'];
	$payment_allowed = $rangeMonth['0']['Salary']['flag'];
	}
	//$payment_allowed = 'N';
	if($payment_allowed != 'N'){	
	
	//Added by Dennis
	//get date as arrays
	$arraydays = $this->CustomHandler->makeArrayDate(strtotime($start_payment),strtotime($end_payment));
	//pr($arraydays);
	
	
	$allspecialdateArray =array();
	foreach($specialMonth as $s){
		$allspecialdate = $this->CustomHandler->makeArrayDate(strtotime($s['SpecialDate']['special_date_start']),strtotime($s['SpecialDate']['special_date_end']));
		$allspecialdateArray = array_merge($allspecialdateArray,$allspecialdate);
		
	}						

	//get the special date on the month
	$arraySpecial = array();
	foreach($allspecialdateArray as $value){
		if (in_array($value, $arraydays)) {
		 array_push($arraySpecial, $value);
		}
	}	
		
	//chunck by weeks
	$byweeks = array_chunk($arraydays,7);
	//pr($byweeks);
	
	
	//assign weeks on weekname
	for($a = 0; $a < count($byweeks); $a++){
		if($a == 0){
			$first_week =  $byweeks[0];
		}elseif($a == 1){
			$second_week =  $byweeks[1];
		}elseif($a == 2){
			$third_week =  $byweeks[2];
			
		}
	}
	//pr($first_week);
	
	//initial to null		
	$first_week_saturday = null;
	$second_week_saturday = null;
	$third_week_saturday = null;
	$first_week_sunday = null;
	$second_week_sunday = null;
	$third_week_sunday = null;
		
		//get saturday and sunday
		for($a = 0; $a < count($byweeks); $a++){
			foreach($byweeks[$a] as $weeks){
				
					$daynumber =  date('N',strtotime($weeks));
					if (6 == $daynumber) {
						if($a == 0){
							$first_week_saturday = $weeks;
						}elseif($a == 1){
							$second_week_saturday =	$weeks;
						}elseif($a == 2){
							$third_week_saturday = $weeks;
						}
					}
					
					if (7 == $daynumber) {
						if($a == 0){
							$first_week_sunday = $weeks;
						}elseif($a == 1){
							$second_week_sunday =	$weeks;
						}elseif($a == 2){
							$third_week_sunday = $weeks;
						}
					}
				
			}
			
		}
		//end saturday and sunday
		
		
	//total records	by teacher id					
	$totals = $this->CustomHandler->countWork($teacher_id,$start_payment,$end_payment,'F');
	$missings = $this->CustomHandler->countWork($teacher_id, $start_payment, $end_payment,'M');
	//pr($totals);
	//pr($missings);

	$week_container = array('first_week_saturday'=>$first_week_saturday,
							'first_week_sunday' =>$first_week_sunday,
							'second_week_saturday' =>$second_week_saturday,
							'second_week_sunday' =>$second_week_sunday,
							'third_week_saturday' =>$third_week_saturday,
							'third_week_sunday' =>$third_week_sunday			
							);
	//pr($week_container);
		$weekends = array();			
		while(list($key,$value)=each($week_container)) {
			if(!in_array(${$key},$arraySpecial)){
				
				${$key.'_finished'}  = $this->CustomHandler->withParam($totals,${$key},'F');
				${$key.'_absent'} = $this->CustomHandler->withParam($totals,${$key},'A');
				if($value != null){
					 array_push($weekends,$value);
				}
			}
		}
		
		
		//pr($weekends);
		$temp_special_week_finished = array();
		$special_week_finished_mixed = array();
		$temp_special_week_absent = array();
		$special_week_absent_mixed = array();
		
		while(list($key,$value)=each($arraySpecial)) {
			$temp_special_week_finished  = $this->CustomHandler->withParam($totals,$value,'F');	
			$special_week_finished_mixed+= $temp_special_week_finished;
			$temp_special_week_absent  = $this->CustomHandler->withParam($totals,$value,'A');	
			$special_week_absent_mixed+= $temp_special_week_absent;
		}
			
		
		$date_special_and_weekends = array_merge($arraySpecial,$weekends);
		//pr($date_special_and_weekends);
		$temp_special_weekdays_finished = array();
		$weekdays_week_finished_mixed = array();
		$temp_weekdays_week_absent = array();
		$weekdays_week_absent_mixed = array();
		while(list($key,$value)=each($arraydays)) {
			if(!in_array($value, $date_special_and_weekends)){
				$temp_weekdays_week_finished  = $this->CustomHandler->withParam($totals,$value,'F');	
				$weekdays_week_finished_mixed+= $temp_weekdays_week_finished;
				$temp_weekdays_week_absent  = $this->CustomHandler->withParam($totals,$value,'A');	
				$weekdays_week_absent_mixed+= $temp_weekdays_week_absent;
			}
		}
		
		//count per day
		$weekdays_week_finished = intval(count($weekdays_week_finished_mixed));
		$weekdays_week_absent = intval(count($weekdays_week_absent_mixed));
		$special_week_finished = intval(count($special_week_finished_mixed));
		$special_week_absent = intval(count($special_week_absent_mixed));		
		$week_missing = count($missings);
	
		$first_week_finished = $this->CustomHandler->countHours($first_week_saturday_finished, $first_week_sunday_finished);
		$first_week_absent = $this->CustomHandler->countHours($first_week_saturday_absent, $first_week_sunday_absent);
		$second_week_finished = $this->CustomHandler->countHours($second_week_saturday_finished, $first_week_sunday_finished);
		$second_week_absent = $this->CustomHandler->countHours($second_week_saturday_absent, $second_week_sunday_absent);
		$third_week_finished = $this->CustomHandler->countHours($third_week_saturday_finished, $third_week_sunday_finished);
		$third_week_absent = $this->CustomHandler->countHours($third_week_saturday_absent, $third_week_sunday_absent);
		
		//combine filter data
		$first_combine = $this->CustomHandler->combineArray($first_week_saturday_finished, $first_week_sunday_finished,$first_week_saturday_absent, $first_week_sunday_absent);
		$second_combine = $this->CustomHandler->combineArray($second_week_saturday_finished, $second_week_sunday_finished,$second_week_saturday_absent, $second_week_sunday_absent);	
		$third_combine = $this->CustomHandler->combineArray($third_week_saturday_finished, $third_week_sunday_finished,$third_week_saturday_absent, $third_week_sunday_absent);
		$special_combine = $special_week_finished_mixed + $special_week_absent_mixed;
		$weekdays_combine = $weekdays_week_finished_mixed + $weekdays_week_absent_mixed;
		//pr($special_combine);
		
		//pr($loginInfo);
		//computting
		$basic_salary =  $loginInfo['basic_salary'];
		$weekdays_rate =  $loginInfo['weekdays_rate'];
		$weekends_rate =  $loginInfo['weekends_rate'];
		$special_rate =  $loginInfo['special_rate'];
		
		$weekdays_a_rate =  $loginInfo['weekdays_a_rate'];
		$weekends_a_rate =  $loginInfo['weekends_a_rate'];
		$special_a_rate =  $loginInfo['special_a_rate'];
	
		$absent_rate = 30;
		$missing_rate = 200;
		
		$lesson_amount = array('first_F'=>0,'first_A'=>0,'second_F'=>0,'second_A'=>0,'third_F'=>0,'third_A'=>0,
								'weekday_F'=>0,'weekday_A'=>0,'special_F' => 0, 'special_A'=>0,
								'first_sum'=>0,'second_sum'=>0,'third_sum'=>0,'weekday_sum'=>0,'weekends_sum'=> 0,'total_sum'=>0,
								'all_F'=>0,'all_A'=>0	,'missing_A'=>0);
		
		$lesson_count = array('first_F'=>0,'first_A'=>0,'second_F'=>0,'second_A'=>0,'third_F'=>0,'third_A'=>0,
							'weekday_F'=>0,'weekday_A'=>0,'special_F' => 0, 'special_A'=>0,
							'first_sum'=>0,'second_sum'=>0,'third_sum'=>0,'weekday_sum'=>0,'special_sum' => 0,'total_sum'=>0,'all_F'=>0,'all_A'=>0	,'missing_A'=>0);

		$lesson_amount['basic_salary'] = $basic_salary;
		$lesson_amount['half_basic_salary'] = $basic_salary/2;
		
		$lesson_amount['weekday_F'] = $weekdays_week_finished * $weekdays_rate;
		$lesson_amount['weekday_A'] = $weekdays_week_absent * $weekdays_a_rate;
		$lesson_amount['first_F'] = $first_week_finished * $weekends_rate;
		$lesson_amount['first_A'] = $first_week_absent * $weekends_a_rate;
		$lesson_amount['second_F'] = $second_week_finished * $weekends_rate;
		$lesson_amount['second_A'] = $second_week_absent * $weekends_a_rate;
		$lesson_amount['third_F'] = $third_week_finished * $weekends_rate;
		$lesson_amount['third_A'] = $third_week_absent * $weekends_a_rate;
		$lesson_amount['special_F'] = $special_week_finished * $special_rate;
		$lesson_amount['special_A'] = $special_week_absent * $special_a_rate;
		$lesson_amount['missing_A'] = $week_missing * $missing_rate;
		
		$lesson_amount['special_sum'] = $lesson_amount['special_F'] + $lesson_amount['special_A'];
		$lesson_amount['weekday_sum'] = $lesson_amount['weekday_F'] + $lesson_amount['weekday_A'];
		$lesson_amount['first_sum'] = $lesson_amount['first_F'] + $lesson_amount['first_A'];
		$lesson_amount['second_sum'] = $lesson_amount['second_F'] + $lesson_amount['second_A'];
		$lesson_amount['third_sum'] = $lesson_amount['third_F'] + $lesson_amount['third_A'];
		
		$lesson_amount['weekends_sum'] = $lesson_amount['first_sum'] + $lesson_amount['second_sum'] + $lesson_amount['third_sum'];
		$lesson_amount['all_F'] = $lesson_amount['weekday_F'] + $lesson_amount['first_F'] + $lesson_amount['second_F'] + $lesson_amount['third_F'] + $lesson_amount['special_F']
									+ $lesson_amount['missing_A'];
		$lesson_amount['all_A'] = $lesson_amount['weekday_A'] + $lesson_amount['first_A'] + $lesson_amount['second_A'] + $lesson_amount['third_A'] + $lesson_amount['special_A'];							
		$lesson_amount['total_sum'] = $lesson_amount['half_basic_salary'] + $lesson_amount['weekday_sum'] +$lesson_amount['first_sum'] + $lesson_amount['second_sum'] +
									$lesson_amount['third_sum']	+ $lesson_amount['special_sum'] + $lesson_amount['missing_A'];					
		
		
		$lesson_count['all_F'] = $weekdays_week_finished +  $special_week_finished + $first_week_finished + $second_week_finished + $third_week_finished + $week_missing;
		$lesson_count['all_A'] = $weekdays_week_absent +  $special_week_absent + $first_week_absent + $second_week_absent + $third_week_absent;
		$lesson_count['weekday_F'] = $weekdays_week_finished;
		$lesson_count['weekday_A'] = $weekdays_week_absent;
		$lesson_count['first_F'] =  $first_week_finished;
		$lesson_count['first_A'] = $first_week_absent;
		$lesson_count['second_F'] = $second_week_finished;
		$lesson_count['second_A'] = $second_week_absent;
		$lesson_count['third_F'] =  $third_week_finished;
		$lesson_count['third_A'] = $third_week_absent;
		$lesson_count['special_F'] = $special_week_finished;
		$lesson_count['special_A'] = $special_week_absent;
		$lesson_count['missing_A'] = $week_missing;
	
	$this->set('lesson_count',$lesson_count); 
	$this->set('lesson_amount',$lesson_amount); 
	
	
	
	$this->set('totals',$totals);
	$this->set('firsts',$first_combine);	
	$this->set('seconds',$second_combine);
	$this->set('thirds',$third_combine);
	$this->set('weekdays',$weekdays_combine);
	$this->set('specials',$special_combine);
	$this->set('missings',$missings);


	if(intval(count($first_combine)) != 0){
		$this->set('first_weekends_title',$this->CustomHandler->convertDatetoTextual($first_week_saturday,$first_week_sunday));									
	}else{
		$this->set('first_weekends_title','No first weeks');
	}
	if(intval(count($second_combine)) != 0){
		$this->set('second_weekends_title',$this->CustomHandler->convertDatetoTextual($second_week_saturday,$second_week_sunday));									
	}else{
		$this->set('second_weekends_title','No second weeks');
	}
	if(intval(count($third_combine)) != 0){
		$this->set('third_weekends_title',$this->CustomHandler->convertDatetoTextual($third_week_saturday,$third_week_sunday));									
	}else{
		$this->set('third_weekends_title','No third weeks');
	}
									
	$this->set('weekdays_title','weekdays');
	if(count($special_combine) == 0){
		$this->set('special_title','No Special Rate this Week');
	}else{
		$this->set('special_title','Special Days');
	}
	$this->set('missing_title','Missing');
	
	}// end check payment allowed  
} /*** salary dynamic ******/

}

//Common function lesson START
//
function get_all_teachers($allTeachers = null)
{
 	$str = "<select name='teacher_id' >";
 	
 	$str .= "<option value='all' selected >ALL</option>\n";
					      
        foreach($allTeachers as $teacher) {
        $teacher_id = $teacher['Teacher']['id'];
        $teacher_name = $teacher['Teacher']['tea_name'];
        ;
     $str .= "<option value='$teacher_id'>$teacher_name</option>\n";
     
     }
     $str .= "</select>";				
     return $str;				
}

function change_time_string($time_string = null)
{
	$time = substr($time_string,0,2);
	$minutes = substr($time_string,2,2);
	$changed_time_string = sprintf("%s:%s:00",$time,$minutes);
	
	return $changed_time_string;
}
//2012-05 -12 start
//$date ex 2012-02-12 00:00:00
//result 2012-02-12 02:00:00
function change_next_day($date = null , $next_day_time)
{
	$next_day =  date("Y-m-d", strtotime('+1 day',strtotime($date)));
	$next_day_time =  sprintf("%s %s",$next_day,$next_day_time);
	return $next_day_time;
}




?>
