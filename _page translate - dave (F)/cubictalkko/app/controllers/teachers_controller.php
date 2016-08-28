<?php
class TeachersController extends AppController {

	var $name = 'Teachers';
	//var $uses = array('Teachers','Lessons');
	var $helpers=array('Ajax','Javascript');
	var $components = array('RequestHandler','ModelHandler','CheckHandler','Programmer1Handler','LanguageHandler');

	function beforeFilter(){
		// セッションのチェック
		
		//2011-08-13 can see everybody the reservation status
		
		//checkLoginInfo($this);
	}
	
	function indexTest(){
		$getAllTeacherAvailableLesson = $this->getAllTeacherAvailableLesson();
		$this->set('getAllTeacherAvailableLesson',$getAllTeacherAvailableLesson);	
	}
	
	function reserveTest(){}
	
	function afterFilter(){
		//check not read memo
		doAfterController($this);
	}
	
	function getPaymentAllNotFix($mb_id=null) {
		if(empty($mb_id)){
			$loginInfo = $this->Session->read('loginInfo');
			$mb_id = $loginInfo['User']['mb_id'];
		}
		$getPaymentAllNotFix = $this->Programmer1Handler->getAllPaymentNotFix($mb_id);
		$this->set('getPaymentAllNotFix',array('userPayment'=>$getPaymentAllNotFix));
		return array('userPayment'=>$getPaymentAllNotFix);
	} // End function getPaymentAllNotFix($mb_id=null) {}
	
	function getAllTeacherAvailableLesson($date=null,$time=null){
		// Important
		if(empty($date)){
			$date = date('Y-m-d');
			$time = date('H') . '24';
			$time = '0024';
		}

		// Important
		// structure of time is both hour and hour
		// example: 0607 this is 06:00 and 07:00
		// make sure 24 are use
		if(empty($time)){
			$time = '0024';
		}

		$start_day = sprintf('%s %s:00:00',$date,substr($time,0,2));
		$end_day = sprintf('%s %s:00:00',$date,substr($time,2,4));

		$this->loadModel('Available_lesson');
		$teaList = $this->Teacher->find('all',array('fields'=>array('id')
				,'conditions'=>array('show'=>'Y')
				,'order'=>'tea_name ASC'));
		$tea_id = array();
		foreach($teaList as $key_teaList => $ktl){
			$tea_id[] = $ktl['Teacher']['id'];
		}
		$teaAvailable = array();
		if(count($tea_id) > 0){
			$this->loadModel('Available_lesson');
			$this->Available_lesson->bindModel(array('belongsTo'=>array(
					'Teacher'=>array('className'=>'Teacher'
							,'foreignKey'=>false
							,'type'=>'LEFT'
							,'conditions'=>'Available_lesson.teacher_id=Teacher.id'))),false);
			$teaAvailable = $this->Available_lesson->find('all',array('fields'=>array('Available_lesson.*'
					,'Teacher.tea_name','Teacher.img_file','Teacher.tea_time')
					,'conditions'=>array('teacher_id'=>$tea_id
							,'reserve_status'=>'R'
							,'available_time >='=>"$start_day"
							,'available_time <='=>"$end_day"
							,'class_minutes' => array(0,30) // 0 or 30 is 25 minutes class
							)
					,'order'=>'Teacher.show_order DESC, Teacher.tea_name ASC, Available_lesson.available_time ASC'
			));
		}
		else {
			// return prompt message
		}
		return array('teaAvailable'=>$teaAvailable);
	} // End function getAllTeacherAvailableLesson(){}
	
// function reserveVideos ($id=null) {
//  $this->loadModel('TeacherVideo');
//  $teacher_video = $this->TeacherVideo->find('all', array('conditions'=>array('TeacherVideo.teacher_id'=>$id)));
//  $this->set('teacher_video',$teacher_video);
// } // End function reserveVideos ($id=null);

function insertVideos(){
	$user_id = $this->params['form']['user_id'];
	$teacher_videos_id = $this->params['form']['teacher_videos_id'];
	$rate = $this->params['form']['video_rate'];
	$comment = addslashes($this->params['form']['comment']);
	$this->loadModel('TeacherVideosEvaluations');
	$query = "INSERT INTO teacher_videos_evaluations(user_id,teacher_videos_id,rate,comment,created,modified) VALUES ($user_id,$teacher_videos_id,$rate,'$comment', NOW(), NOW())";
	$this->Teacher->query($query);
	$query="";
	$this->redirect('getVideo/'.$teacher_videos_id.'/users/');
	$this->autoRender = false;
}

function getVideo ($id=null,$user=null) { //$id is TeacherVideos.id
	$this->set('user',$user);
// 	$loginInfo = $this->Session->read('loginInfo');
// 	$this->set('loginInfo',$loginInfo);
	$getVideoComment = array();
	if (empty($id)) {
		echo "There is a missing data.";
	} else {
		$this->loadModel('TeacherVideos');
		$this->loadModel('TeacherVideosEvaluations');
		$this->TeacherVideos->bindModel(array('belongsTo'=>array('TeacherVideosEvaluations'=>array
				('className'=>'TeacherVideosEvaluations','type'=>'LEFT','foreignKey'=>false,'conditions'=>'TeacherVideos.id=TeacherVideosEvaluations.teacher_videos_id'))));
		$getVideoComment = $this->TeacherVideos->find('all',array('fields'=>array(),'conditions'=>array('TeacherVideos.id'=>$id)));
	}
	$this->set('getVideoComment',$getVideoComment);
}

function getVideoComment ($id=null) { //$id is TeacherVideos.id
	$getVideoComment = array();
	if (empty($id)) {
		echo "There is a missing data.";
	} else {
		$this->loadModel('TeacherVideos');
		$this->loadModel('TeacherVideosEvaluations');
		//$this->loadModel('users');
		$this->TeacherVideos->bindModel(array('belongsTo'=>array(
				'TeacherVideosEvaluations'=>array('className'=>'TeacherVideosEvaluations','type'=>'LEFT','foreignKey'=>false,'conditions'=>'TeacherVideos.id=TeacherVideosEvaluations.teacher_videos_id')
				,'Users'=>array('className'=>'Users','type'=>'LEFT','foreignKey'=>false,'conditions'=>array('TeacherVideosEvaluations.user_id=Users.id'))
		)));
		$getVideoComment = $this->TeacherVideos->find('all',array('fields'=>array
				('TeacherVideos.*','TeacherVideosEvaluations.id,TeacherVideosEvaluations.created','TeacherVideosEvaluations.rate','TeacherVideosEvaluations.comment','Users.mb_id'),
				'conditions'=>array('TeacherVideos.id'=>$id),'order'=>'TeacherVideosEvaluations.created DESC'));
	}
	$this->set('getVideoComment',$getVideoComment);
}

// teacher lesson plans
function listLessonplans ($teacher_id = null, $teacher_name = null, $folder_name = null, $category_id = null) {
	$this -> loadModel("TeacherLessonplan");
	$teacherLessonplan = array();
	$teacherLessonplan["TeacherLessonplan.teacher_id"] = $teacher_id;
	$teacherLessonplan["TeacherLessonplan.category_id"] = $category_id;
	$list_lessonplans = $this -> TeacherLessonplan -> find("all", array("fields" => array(), "conditions" => $teacherLessonplan, "order" => "TeacherLessonplan.created DESC"));
	$this -> set("list_lessonplans", $list_lessonplans);
	$this -> set("folder_content", array("teacher_id" => $teacher_id, "teacher_name" => $teacher_name, "folder_name" => $folder_name, "category_id" =>  $category_id));
	return array( "list_lessonplans" => $list_lessonplans, "folder_content" => array("teacher_id" => $teacher_id, "teacher_name" => $teacher_name, "folder_name" => $folder_name, "category_id" =>  $category_id));
}

function teacherLessonplans ($teacher_id = null, $teacher_name = null, $folder_name = null, $category_id = null) {
	if ( $this->RequestHandler->isAjax() ) {
		Configure::write('debug',0);
		$postId = $this -> params["form"]["id"];
		
		$folder = explode("*", $folder_name);
		
		$teacherLessonplans = $this -> listLessonplans($teacher_id, $teacher_name, $folder[1], $folder[0]);
		$content = "";
//		$content .= pr($teacherLessonplans["list_lessonplans"]);
		if ( isset($teacherLessonplans["list_lessonplans"]) && count($teacherLessonplans["list_lessonplans"]) > 0 ) {
		
		$content .= "<table class = \"table-stripped\" border = \"0\" cellpadding = \"5\" cellspacing = \"\" width = \"95%\">";
		$content .= "<thead>";
		$content .= "<tr>";
		$content .= "<th>#</th>";
		$content .= "<th colspan = \"2\">Filename</th>";
		$content .= "</tr>";
		$content .= "</thead>";
		$content .= "<tbody>";
		$count = 1;
		foreach ( $teacherLessonplans["list_lessonplans"] as $keyTLP => $tlp ) {
		$filename = $tlp["TeacherLessonplan"]["filename"];
		$content .= "<tr>";
		$content .= "<th align = \"center\">$count</th>";
		$content .= "<th>$filename</th>";
		$content .= "<th align = \"center\"><a href = \"javascript:void(0);\" id = \"{$filename}\" class = \"preview\" onclick = \"reviewFile(this)\">View &#38; Download</a></th>";
		$content .= "</tr>";
		$count++;
		}
		$content .= "</tbody>";
		$content .= "</table>";
		$content .= "<div><button class = \"button\" onclick = \"getLessonPlan('$folder_name')\">Close</button></div>";
		$ftp_path = $teacherLessonplans["folder_content"];
		$ftp_path = "/" . $ftp_path["teacher_name"] . "/" . current(explode(' ',$ftp_path["folder_name"]))."_";
		$content .= "<input type = \"hidden\" id = \"ftp_path\" value = \"$ftp_path\">";
			
		} else {
		$content .= "<div class = \"warning-info\">Sorry! The content folder is empty.</div>";
		}
		echo $content;
	}
	
}

// how many payment goods_name can reserve
function reserve_confirm2()
{
	//$this->CheckHandler->checkValidAccess();
	//Constant変数を読むことＣｏｎｔｒｏｌｌｅｒ共通　セッションのログインＩＮＦＯ読む
	global $const; 
	$this -> set( 'const', $const );
	
	$payment_id = $this -> params['form']['payment'];
	
	$count_payment_id_used = $this -> Programmer1Handler -> count_payment_id_used($payment_id, 'confirm');
	$this -> set( 'count_payment_id_used', $count_payment_id_used );
	
	$teacher_id = $this -> params['form']['id'];
	$teacher = $this -> Teacher -> findById( $teacher_id ); //$teacher= $this->Teacher->findById($this->data['id']);
	$this -> set( 'teacher', $teacher );
	
	// get session
	// 2014-10-14 12:38 by levy
	//$teacherSch = $this -> Session -> read( 'teacherSch' );

	$tacherConfirmSch = '';
	//2010-03-24 only one schedule start
	$tacherConfirmSch = array();
	$reservation_time = $this -> params['form']['reservation_time'];
	$tacherConfirmSch[$reservation_time]= $reservation_time;
	//2010-03-24 only one schedule end
	
	$this -> Session -> write( "tacherConfirmSch", $tacherConfirmSch );
	
	//仕様変更先生は自分が自身があるＴｅｘｔを先にＰｒｏｆｉｌｅへ登録
	$text_code_db		 = $teacher['Teacher']['text_code'];
	$text_code_array	 = split( '/' ,$text_code_db );
	$text_combo			 = $this -> ModelHandler -> getTextCodeFromCatalog( 'text', $text_code_array );
	$selected ['text']	 =  '01';
	$this -> set( 'text_combo', $text_combo) ;
	$this -> set( 'selected', $selected );

	// read loginInfo "users info"
	$loginInfo = $this -> Session -> read('loginInfo');

	//2010-02-03 YHLEE ADD START
	$mb_id						 = $loginInfo['User']['mb_id'];
	$user_id					 = $loginInfo['User']['id'];
	$user_point					 = $this -> ModelHandler -> getMbPoint( $mb_id, 'Teacher' );
	$loginInfo['User']['points'] = $user_point;
	$this -> Session -> write( 'loginInfo', $loginInfo );
    //2010-02-03 YHLEE ADD END
    
	$pay_plan		 = $loginInfo['User']['pay_plan'];
	$skype_id		 = $loginInfo['User']['skype_id'];
	$button_show	 = 'Y';
	$have_skype_id	 = 'Y';
	
	//2010-02-03 Reservation Kinds decide
    //POINT and KOMA
    //POINT Pay_plan = M,P,S,
    //E1,E2(reservation_kinds='point_reservation')
    //the other case E1,E2(koma)
	$reservation_kinds = "";
	$reservation_kinds = $this -> params['form']['reservation_kinds'];

	//2011-01-22 to check to cancel point reservation and reserve koma the same time
	//load goods model
	$this -> loadModel('Lesson');
	
	//2010-06-14 how many class user reserved in reserve time start
	$year	 = substr($reservation_time,0,4);
  	$month	 = substr($reservation_time,4,2);
  	$day	 = substr($reservation_time,6,2);
  	$hour	 = substr($reservation_time,8,2);
  	$minites = substr($reservation_time,10,2);
  	
  	//compare last day
  	
  	$temp_reserve_time = sprintf("%04d-%02d-%02d %02d:%02d:00",$year,$month,$day,$hour,$minites);
  	
  	//2013-01-12 ADD (If lesson table , status = R or A or F) can not be reserved
  	$lesson_already_reserved_count = $this->CheckHandler->checkAlreadyReserved($teacher_id,$temp_reserve_time);
  	//2013-01-12 END

  	$point_cancel_no = 0;
  	$messages='';
  	// echo
  	$payment_goods_name = '';
  	$is_contract_finished_msg = "";
  	
	if ( $reservation_kinds == $const['koma_reservation'] ){
		
		$todayReservation = $this -> Programmer1Handler -> getTotalReservation($loginInfo['User']['id'],$const['Teacher'], $payment_id);
		$loginInfo['User']['todayReservation'] = $todayReservation;
		$this->Session->write($const['loginInfo'],$loginInfo);
		
		$this -> loadModel( 'Payment' );
		$getReservationKinds = $this -> Payment -> find( 'first', array( 'fields' => array( 'Payment.goods_name', 'Payment.start_day', 'Payment.last_day' ), 'conditions' => array( 'Payment.id' => $payment_id ) ) );
		
		if ( count($getReservationKinds) > 0 && strlen($getReservationKinds['Payment']['goods_name']) > 0 ) {
			$payment_goods_name	 = $getReservationKinds['Payment']['goods_name'];
			// save Payment id
		}
		
		$payment_goods_name = substr($payment_goods_name, 0, 2);

		if($payment_goods_name == $const['E1']){
			$koma['present'] = 1 - $todayReservation;
		}elseif($payment_goods_name == $const['E2']){
			$koma['present'] = 2 - $todayReservation;
		}elseif($payment_goods_name == 'E3'){
			$koma['present'] = 3 - $todayReservation;
		}elseif($payment_goods_name == 'E4'){
			$koma['present'] = 4 - $todayReservation;
		}elseif($payment_goods_name == 'E5'){
			$koma['present'] = 5 - $todayReservation;
		}elseif($payment_goods_name == 'E6'){
			$koma['present'] = 6 - $todayReservation;
		}elseif($payment_goods_name == 'E7'){
			$koma['present'] = 7 - $todayReservation;							
		}elseif($payment_goods_name == 'E8'){
			$koma['present'] = 8 - $todayReservation;	
		}else{
			// TT,TM others like Telephone and Mobile
			$payment_goods_name = substr($payment_goods_name, 0, 1);
			$payment_goods_name_num = intval($payment_goods_name);
			if($payment_goods_name_num < 1){ $payment_goods_name_num = 1; }
			$koma['present'] = $payment_goods_name_num - $todayReservation;
		}
		
		$koma['use'] = sizeof($tacherConfirmSch);
		$koma['left'] = $koma['present'] - $koma['use'];
		
		//2012-01-22
		$point_cancel_no = $this->Lesson->find('count', array(		
			'fields' => 'Lesson.id',		
			'conditions' => array('Lesson.reservation_kinds' => 'P',		
			'Lesson.lesson_status' => 'C' ,	
			'Lesson.user_id'=>$user_id,
			'Lesson.teacher_id'=>$teacher_id,
			'Lesson.reserve_time'=>$temp_reserve_time )));

	    $this->set('koma', $koma);
	    
	    if($point_cancel_no > 0){
	    	$messages = "<FONT color='#FF0000'>같은 강사님 같은 시간을 포인트로 취소하신 후 큐빅으로는 예약하실 수 없습니다.</FONT>";
	    }
	}

  	$is_contract_finished = 'N';
  	//2011-01-21 bug fixed from last day time setting all last day is 02:00:00
  	$payment_last_day = $loginInfo['User']['last_day'];
  	$payment_start_day = $loginInfo['User']['start_day'];
  	if ( isset($getReservationKinds) && count($getReservationKinds) > 0 && strlen($getReservationKinds['Payment']['last_day']) > 0 ) {
		$payment_last_day	 = $getReservationKinds['Payment']['last_day'];
		$payment_start_day	 = $getReservationKinds['Payment']['start_day'];
	} else if ( isset($count_payment_id_used) && count($count_payment_id_used) > 0 &&  strlen($count_payment_id_used['last_day']) > 0  ) {
		$payment_last_day	 = $count_payment_id_used['last_day'];
		$payment_start_day	 = $count_payment_id_used['start_day'];
	}
  	
  	$last_day_year	 = substr($payment_last_day,0,4);
  	$last_day_month	 = substr($payment_last_day,5,2);
  	$last_day_day	 = substr($payment_last_day,8,2);
  	
//	start_day
	$start_day_once = date("Y-m-d 02:00:00", strtotime($payment_start_day));
	$temp_start_day = date("Y-m-d 02:00:00", strtotime($payment_start_day));
	$start_day_calcu = strtotime($temp_start_day) - strtotime($temp_reserve_time);
	
  	$temp_last_day	 = sprintf("%04d-%02d-%02d 02:00:00",$last_day_year,$last_day_month,$last_day_day);
  	$last_day_calcu = strtotime($temp_last_day.'+1 day') - strtotime($temp_reserve_time);
  	if( $start_day_calcu > 0 || $last_day_calcu <= 0 ){
  		$is_contract_finished = 'Y';
  		if ( $start_day_calcu > 0 ) {
  			$is_contract_finished_msg = "수업기간이 이 전의 날짜는 수업예약을 할 수 없습니다.";
  		} else if ( $last_day_calcu <= 0 ) {
  			$is_contract_finished_msg = "수업기간이 이 후의 날짜는 수업예약을 할 수 없습니다.";
  		}
  	};
  	$this -> set("is_contract_finished_msg", $is_contract_finished_msg);
  	
  	//if it is more than 24:00
  	$start_time = sprintf("%04d-%02d-%02d 01:00:00",$year,$month,$day);
  	if($hour == '00'){
  		//more than 24 minus one day
  		$start_time =  date("Y-m-d H:i:s", strtotime($start_time)- 60*60*24);
  	}
  	
	
	$last_time =  date("Y-m-d H:i:s", strtotime($start_time)+60*60*25);
	
	$conditions['user_id'] = $loginInfo['User']['id'];
	$conditions['start_time'] = $start_time;
	$conditions['last_time'] = $last_time;
	
	$results= $this -> Programmer1Handler -> get_counts_class_reservation_day($conditions, 'Teacher', $payment_id);
	$reservation_count = $results[0][0]['counts'];
	// echo
	
	if( $payment_goods_name == $const['E1'] ){
		$can_i_reserve_this_day = 1 - $reservation_count;
	}elseif($payment_goods_name == $const['E2'] ){
		$can_i_reserve_this_day = 2 - $reservation_count;
	}elseif($payment_goods_name == 'E3' ){
		$can_i_reserve_this_day = 3 - $reservation_count;
	}elseif($payment_goods_name == 'E4' ){
		$can_i_reserve_this_day = 4 - $reservation_count;
	}elseif($payment_goods_name == 'E5' ){
		$can_i_reserve_this_day = 5 - $reservation_count;				
	}elseif($payment_goods_name == 'E6' ){
		$can_i_reserve_this_day = 6 - $reservation_count;				
	}elseif($payment_goods_name == 'E7' ){
		$can_i_reserve_this_day = 7 - $reservation_count;				
	}elseif($payment_goods_name == 'E8' ){
		$can_i_reserve_this_day = 8 - $reservation_count;				
	}else{
		$can_i_reserve_this_day = 99;	
	}
	
	//2012-04-15 START skype id check for new user
	if( $pay_plan == 'M'){
		$using_skype_id = false;
		// but if the user doesn't have mobile please add condition statement!
		if(isset($this->params['form']['class_in_use']) &&
				strtolower($this->params['form']['class_in_use']) === 'telephone'){
			$button_show = 'Y';
			// No Mobile Contact for Mobile User
			if($loginInfo['User']['tel'] == '' || $loginInfo['User']['tel'] == null){
				$messages .= "<SPAN class=sky_b>Please update your contact profile.";
				$button_show = 'N';
			}
		}
		else if(isset($this->params['form']['class_in_use']) &&
				strtolower($this->params['form']['class_in_use']) === 'skype'){
			$button_show = 'Y';
			$using_skype_id = true;
		}
		else {
			$using_skype_id = true;
		}
		
		// No Skype Id for Skype User
		if($using_skype_id && ($skype_id == '' || $skype_id == null)){
			$messages .= "<SPAN class=sky_b>"."스카이프이름 (아이디)를 기입하시지 않으셨습니다.
				큐빅토크 홈페이지 내 정보수정에 정확한 스카이프아이디를 기입부탁드립니다.";
			$button_show = 'N';
		}
	}
	
	//2013-01-12 ADD
	if($lesson_already_reserved_count !=0 ){
		$button_show = 'N';
	}
	//2012-04-15 END
	
	//2010-06-14
	$this -> set('point_cancel_no', $point_cancel_no);
	$this -> set('can_i_reserve_this_day', $can_i_reserve_this_day);
	$this -> set('is_contract_finished', $is_contract_finished);

	$this -> set('button_show', $button_show);
	
	$this -> set('reservation_kinds', $reservation_kinds);
		//エラーメッセージセット
    
	//someone reserved that classes
  	$this -> set('lesson_already_reserved_count', $lesson_already_reserved_count);
	$this -> set('messages', $messages);
	
	
	// get payment info using payment_id
	$payment_id = $this -> params['form']['payment'];
	$this -> set( 'payment_info', $payment_id );

	// payment_goods_name
	$this -> set ( 'payment_goods_name', $payment_goods_name );
}
//------------------------------	

	function field ($teacher_id = null) {
    	$this->set('_count', 1);
    	$this->set('_teacher', $this->Teacher->query("SELECT Teacher.tea_name FROM teachers as Teacher WHERE Teacher.id = '$teacher_id' "));
    	$this->set('_exist_fields', $this->Teacher->query("SELECT MyField.id, MyField.field, Field.name, MyField.description FROM fields as Field JOIN myfields as MyField ON (MyField.field = Field.id) WHERE MyField.teacher_id = '$teacher_id' "));
    }

function index($id=null){
		$express_day;
    
    	global $const;
    	$this->set('const',$const);
    
    	$teacherChar = $this->Programmer1Handler->teacherChar();
    	$teaChar = array();
    	$this->set('teacherChar', $teacherChar);
    
    	$teachers_all_info = $this->ModelHandler->getTeacherAll('Teacher');
    
    	$click_day = array();
    
    	$teachers_info;
    
    	// Only Available Teacher will display
    	// Default
    	$selected = array();
    	$selected ['time'] = date('H') . '24';
    	
    	$today_strtotime = date('Ymd');
    	$cal_date = $today_strtotime;
    	if(isset($this->data['Teacher']['cal_date'])
    			&& $cal_date != $this->data['Teacher']['cal_date']){
    		$selected ['time'] = '0024';
    		$cal_date = $this->data['Teacher']['cal_date'];
    	}
    	$express_day = $today_strtotime;
    	if(isset($this->data['Teacher']['express_day'])){
    		$express_day = $this->data['Teacher']['express_day'];
    	}
    	
    	if(isset($this->data['Teacher']['cal_time'])
    			&& strlen(str_replace(' ','',$this->data['Teacher']['cal_time'])) > 0
    			&& isset($this->params['form']['searchParams'])
    			&& $this->params['form']['searchParams'] == 1){
    		$selected ['time'] = $this->data['Teacher']['cal_time'];
    	}
    	
    	$hankaku_space = " ";
    	if($cal_date == null){
    		$search_day = $ct['time_ymd_no']; ;
    	}else{
    		$search_day = $cal_date;
    	}
    	
    	// <!-- Begin to process of displaying Teachers' Available Schedule
    	// read loginInfo "users info"
    	$loginInfo = $this->Session->read('loginInfo');
    	$teacher_schedule_setting = 0;

    	if((isset($this->params['form']['modify'])
    			&& $this->params['form']['modify'] == 1)
    			|| ($loginInfo['User']['created'] == $loginInfo['User']['modified']) ){
    		$teacher_schedule_setting = 0;
    		if((isset($this->params['form']['teacher_schedule_setting'])
    				&& $this->params['form']['teacher_schedule_setting'] == 1)
    				|| ($loginInfo['User']['created'] == $loginInfo['User']['modified'])){
    			$teacher_schedule_setting = 1;
    		}
    		
    		$modified = date('Y-m-d H:i:s');
    		$this->loadModel('User');
    		$UpdateUserInfo = array();
    		$UpdateUserInfo['id'] = $loginInfo['User']['id'];
    		$UpdateUserInfo['teacher_schedule_setting'] = $teacher_schedule_setting;
    		$UpdateUserInfo['modified'] = $modified;
    		//$this->User->updateAll(array('teacher_schedule_setting'=>$teacher_schedule_setting,'modified'=>"'$modified'"),array('id'=>$loginInfo['User']['id']));
    		$this->User->save($UpdateUserInfo);
    		$loginInfo['User']['teacher_schedule_setting'] = $teacher_schedule_setting;
    		$this->Session->write('loginInfo',$loginInfo);
    	}
    	elseif(isset($loginInfo['User']['teacher_schedule_setting'])
    			&& $loginInfo['User']['created'] !=  $loginInfo['User']['modified']){
    		$teacher_schedule_setting = $loginInfo['User']['teacher_schedule_setting'];
    	}
    	else {
    		$loginInfo['User']['teacher_schedule_setting'] = 1;
    		$this->Session->write('loginInfo',$loginInfo);
    	}
    	
    	$getAllTeacherAvailableLesson = array();
    	if(isset($loginInfo['User']['teacher_schedule_setting']) && $loginInfo['User']['teacher_schedule_setting'] == 1){
    		// getAllTeacherAvailableLesson
    		$getAllTeacherAvailableLesson = $this->getAllTeacherAvailableLesson(date('Y-m-d',strtotime($search_day)),$selected['time']);
    	}
    	$this->set('teacher_schedule_setting',$teacher_schedule_setting);
    	$this->set('getAllTeacherAvailableLesson',$getAllTeacherAvailableLesson);
    	
    	$getPaymentAllNotFix = $this->getPaymentAllNotFix($loginInfo['User']['mb_id']);
    	$this->set('getPaymentAllNotFix',$getPaymentAllNotFix);
    	// --> End processing of displaying Teachers' Available Schedule
    	
    	// Time Setting
    	$selected_time = $selected['time'];
		if(isset($this->params['form']['searchParams']) && $this->params['form']['searchParams'] == 1){
			if($selected_time != $hankaku_space && $selected_time != null){
	    		$start_time = substr($selected_time,0,2);
	    		$end_time = substr($selected_time,2,4);
	    	
	    		$start_day = sprintf("%s-%s-%s %s:00",substr($search_day,0,4),substr($search_day,4,2),substr($search_day,6,2),
	    				$start_time);
	    	
	    		$end_day = sprintf("%s-%s-%s %s:00",substr($search_day,0,4),substr($search_day,4,2),substr($search_day,6,2),
	    				$end_time);
	    	}else{
	    		//START day from 2AM
	    		$start_day = sprintf("%s-%s-%s 02:00",substr($search_day,0,4),substr($search_day,4,2),substr($search_day,6,2)
	    		);
	    	
	    		//START day from 2AM
	    		$end_day = sprintf("%s-%s-%s 23:00",substr($search_day,0,4),substr($search_day,4,2),substr($search_day,6,2)
	    		);
	    		//2hours
	    		$end_day = change_time($end_day,'AHEAD',2);
	    	}
    		$selected ['time'] = $this->data['Teacher']['cal_time'];
    	}
    	else {
    		$selected ['time'] = ' ';
    		$start_time = substr($selected_time,0,2);
    		$end_time = substr($selected_time,2,4);
    		$start_day = sprintf("%s-%s-%s %s:00",substr($search_day,0,4),substr($search_day,4,2),substr($search_day,6,2),
    				$start_time);
    		
    		$end_day = sprintf("%s-%s-%s %s:00",substr($search_day,0,4),substr($search_day,4,2),substr($search_day,6,2),
    				$end_time);
    	}
		$conditions = array(array('Available_lesson.available_time >=' =>$start_day,
    			'Available_lesson.available_time <=' =>$end_day),
    			'Available_lesson.reserve_status' => 'R'
    	);
    	
    	$this->Teacher->bindModel(array(
    			'hasMany' => array(
    					'Available_lesson' =>
    					array('className' => 'Available_lesson',
    							'foreignKey' => 'teacher_id',
    							'conditions'=> $conditions
    					)
    			)));
    	
    	$bind_conditions = array('Teacher.show'=>'Y');
    	$bind_order =  array('Teacher.show_order DESC');
    	$teachers_info = $this->Teacher->find('all',array('fields'=>array('*'),
    			'conditions' => $bind_conditions,
    			'order' =>$bind_order ));
    	
    	$this->set('express_day', $express_day);
    
    	//検索した先生情報をせっと
    	$this->set('teachers_info', $teachers_info );
    	$this->set('teachers_all_info', $teachers_all_info );

    	//textbookのコンボボックス
    	$text_combo = $this->Teacher->common_info('text');

    	//Levelのコンボボックスの設定
    	$level_combo = $this->Teacher->common_info('l_ko');
    
    	$this->set('text_combo', $text_combo);
    	$this->set('level_combo', $level_combo);
    	
        $this->set('selected', $selected);
        $this->set('click_day', $click_day);

        # dave
		$language = $this->LanguageHandler->set_lang();
		$this->set('language',$language);

} // End function index($id = null){}

//2010-02-13 YHLEE ADD START
//student can reserve among many teacher schedule
function reserve_oneday($id = null){
	//calander express day
	$express_day;
	
	$click_day = array();
	
	$teachers_info;

	if (empty($this->data))
    {
		//カレンダの PREV,NEXTボタンをクリックしてINDEXページ遷移した場合
	    if($id !=null ){
			$express_day =$id;
		}else{
			//MENU画面から先生検索画面に遷移した場合
			//セッションの初期化
			$this->Session->delete("tacherConfirmSch");
			$this->Session->delete("teacherSch");
			$this->Session->delete("click_day");
			////最初は現在日
			$express_day = date("Y").sprintf('%02d',date("n"));
		}
		$this->set('express_day', $express_day);
		
    	
		//show teacher who have open have schedule
		//current time
		$current_time = $this->ModelHandler->_timeymd;
		$start_time = sprintf("%s 00:00",$current_time);
		$end_time = sprintf("%s 24:00",$current_time);
		//make sql
		$sub_query =" AND (`Available_lesson`.`available_time` BETWEEN '{$start_time}' AND  '{$end_time}'";
		
    	//先生のＳＣＨＥＤＵＬＥを求める（現在日基準）
		$available_lessons = $this->ModelHandler->getTeacherAvailableLesson($this->ModelHandler->_timeymd,"Teacher");
		
    	$this->set('teachers', $this->Teacher->find('all'));
		
		
		//選択されたコンボボックスの値保存
		$selected ['time'] = ' ';
		$selected ['text'] =  ' ';
		$selected ['point'] =  ' ';
		$selected ['level'] =  ' ';
		$selected ['age'] =  ' ';
		$selected ['sex'] =  ' '; 
		
		$this->Session->write('click_day', $click_day);
		$teachers_info =  $this->Teacher->find('all'); 
		//$this->set('teachers_info', $this->Teacher->find('all') ); 
		  
	}else{
		//print_r ($this->data);
		$selected ['time'] = $this->data['Teacher']['cal_time'];
		$selected ['text'] = $this->data['Teacher']['text_code'];
		$selected ['point'] = $this->data['Teacher']['tea_point'];
		$selected ['level'] = $this->data['Teacher']['tea_level'];
		$selected ['age'] =  $this->data['Teacher']['tea_age'];
		$selected ['sex'] =  $this->data['Teacher']['tea_sex'];    
		
		//セッションの読む
		//仕様変更先生のSCHEDULE検索は一日に限るように変更
		$click_day = $this->Session->read('click_day');
		if($this->data['Teacher']['cal_date']!=null && $this->data['Teacher']['cal_date']!=""){
			//既存にキーがあるか検査する
			$is_exist = false;
			if (array_key_exists($this->data['Teacher']['cal_date'],$click_day)){
				unset ($click_day[$this->data['Teacher']['cal_date']]);
			}else{ 
				$click_day[$this->data['Teacher']['cal_date']] = $this->data['Teacher']['cal_date'];
			}
			$this->Session->write('click_day',$click_day);
		}
		
		
		//検索条件を組み立てる
		$hankaku_space = " ";
		
		$sub_query = "";
		
		//時間検索
		$i = 0;
		$size=sizeof($click_day);
		//print_r($click_day);
		if($size > 0 && ($selected ['time']=="" ||  $selected['time'] == $hankaku_space)){
			foreach($click_day as $click_ymd){
				//sprintf("%s-%s-%s",substr($click_ymd,0,4));
				if ($i==0 && $size =1 ){
					$sub_query .=" AND date(`Available_lesson`.`available_time`) = '{$click_ymd}'";
				}elseif($i==0){	
					$sub_query .=" AND (date(`Available_lesson`.`available_time`) = '{$click_ymd}'";
				}elseif($i == $size-1 ){
					$sub_query .=" OR date(`Available_lesson`.`available_time`) = '{$click_ymd}')";
				}else{
					$sub_query .=" OR date(`Available_lesson`.`available_time`) = '{$click_ymd}'";
				}
				$i++;
			}
		}elseif($size > 0 && ($selected ['time']!="" &&  $selected['time'] != $hankaku_space)){
			foreach($click_day as $click_ymd){
					$start_time = sprintf("%s-%s-%s %s:00",substr($click_ymd,0,4),substr($click_ymd,4,2),substr($click_ymd,6,2)
					,substr($selected ['time'],0,2));
					$end_time = sprintf("%s-%s-%s %s:00",substr($click_ymd,0,4),substr($click_ymd,4,2),substr($click_ymd,6,2)
					,substr($selected ['time'],2,2));
					if ($i==0 && $size =1 ){
						$sub_query .=" AND `Available_lesson`.`available_time` BETWEEN '{$start_time}' AND  '{$end_time}'";
					}elseif($i==0){	
						$sub_query .=" AND (`Available_lesson`.`available_time` BETWEEN '{$start_time}' AND  '{$end_time}'";
					}elseif($i == $size-1 ){
						$sub_query .=" OR `Available_lesson`.`available_time` BETWEEN '{$start_time}' AND  '{$end_time}')";
					}else{
						$sub_query .=" OR `Available_lesson`.`available_time` BETWEEN '{$start_time}' AND  '{$end_time}'";
					}
					$i++;
			}
		}elseif($size == 0 && ($selected ['time']!="" &&  $selected['time'] != $hankaku_space)){
			$l_day = date("j", mktime(0, 0, 0, date("n") + 1, 0, date("Y")));
			$start_time = sprintf("%s-%s-01 %s:",date("Y"),date("n"),substr($selected ['time'],0,2));
			$end_time = sprintf("%s-%s-%s %s:",date("Y"),date("n"),$l_day
				,substr($selected ['time'],2,2));
			//$sub_query .=" AND `Available_lesson`.`available_time` BETWEEN '{$start_time}' AND  '{$end_time}'";
			
			$start_time = sprintf("%s:",substr($selected ['time'],0,2));
			$end_time = sprintf("%s:",substr($selected ['time'],2,2));
			$sub_query .=" AND (`Available_lesson`.`available_time` LIKE '%{$start_time}%'  
							OR `Available_lesson`.`available_time` LIKE '%{$end_time}%') ";

		}

		//point 検索条件
		if($selected['point'] !=null && $selected['point'] != $hankaku_space){
			$start_point = substr($selected['point'],0,2);
			$end_point = substr($selected['point'],2,2);
			$sub_query .=" AND `Teacher`.`tea_point` BETWEEN {$start_point} and {$end_point}"; 
		}
		
		//教材
		if($selected['text']!=null && $selected['text'] != $hankaku_space){
			
			$sub_query .=" AND `Text`.`textcode` = '{$selected['text']}'"; 
		}
		
		//LEVEL
		if($selected['level']!=null && $selected['level'] != $hankaku_space){
			
			$sub_query .=" AND `Teacher`.`tea_level` = '{$selected['level']}'"; 
		}
		
		//年齢
		if($selected['age'] !=null && $selected['age'] != $hankaku_space){
			$start_age = substr($selected['age'],0,2);
			$end_age = substr($selected['age'],2,2);
			$sub_query .=" AND `Teacher`.`tea_age` BETWEEN {$start_age} and {$end_age}"; 
		}
		
		//性別
		if($selected['sex']!=null && $selected['sex'] != $hankaku_space){
			
			$sub_query .=" AND `Teacher`.`tea_sex` = '{$selected['sex']}'"; 
		}
		
		
		$teachers_info = $this->Teacher->teachers_info($sub_query);
		$available_lessons = $this->ModelHandler->getTeacherAvailableLessonBySQL($sub_query,'Teacher');
		
		//
		$this->set('express_day', $this->data['Teacher']['express_day']);
	
	//FORM値がある場合IF
	}
	
	//検索した先生情報をせっと
	$this->set('teachers_info', $teachers_info );
	//$this->set('available_lessons', $available_lessons);
	
	//textbookのコンボボックス
	$text_combo = $this->Teacher->common_info('text');
	
    
	//Levelのコンボボックスの設定
	$level_combo = $this->Teacher->common_info('l_ko');
	
    $this->set('text_combo', $text_combo);
    $this->set('level_combo', $level_combo);
    $this->set('selected', $selected);
    $this->set('click_day', $click_day); 
   
}
    
//Teacherのプロパイル
function profile($id = null)
{
	$teacher['Teacher'] = $this->ModelHandler->getTeacherById($id);
	//
	$text_code_db = $teacher['Teacher']['text_code'];
	$text_code_array = split('/' ,$text_code_db);
	$text_combo = $this->ModelHandler->getTextCodeFromCatalog('text',$text_code_array);
	
	$teacher['Text'] = $text_combo;
	
	//$teacher['Text'] = $this->ModelHandler->getTextByTeacherId($id);
	$this->set('teacher', $teacher);
	
	
	// ADDED LEVY AVILES from HERE
	$this->teacher_book($id);
	$this->evaluation($id);
	$this->field($id);
	
	$this -> set("lessonPlanCategory", $this -> Programmer1Handler -> teacherLessonPlan($id));
 $this->set('teacher_video', $this->Programmer1Handler->teacherVideo($id));
} // End function profile($id = null);

function teacher_book($teacher_id = null){
	$this -> loadModel('TeacherBook');
	$this -> loadModel('Ftpbook');
	$this -> TeacherBook -> bindModel ( array('belongsTo' => array('Ftpbook'=>array('className' => 'Ftpbook','foreignKey' => false, 'conditions' => 'TeacherBook.ftpbook_id = Ftpbook.id')) ) );
	$this -> Ftpbook -> bindModel(array('belongsTo' => array('Ftpsite'=>array('className' => 'Ftpsite','foreignKey' => 'ftpsite_id'))));
	$_fdb_teacher_book = $this -> TeacherBook -> find('all', array('fields' => array(), 'conditions' => array('TeacherBook.teacher_id' => $teacher_id), 'order' => 'Ftpbook.ftpsite_id ASC' ));
	
	$books_category_name = $this -> Programmer1Handler -> books_category_name( 'books_category_name' );
	$books_level_name	 = $this -> Programmer1Handler -> books_category_name( 'books_level_name' );
			
//	$this -> set( 'books_category_name', $books_category_name );
//	$this -> set( 'books_level_name', $books_level_name );
	
	$content_result = '<table class = "info_tbl table-striped" border="0" cellpadding = "0" cellspacing = "0" width = "100%" style="border-collapse:collapse;">';
	if(count($_fdb_teacher_book) > 0){
		
		$description		 = '';
		$available_script	 = array();
		if ( isset($books_category_name['info']) ) {
			foreach ( $books_category_name['info'] as $keyBCN => $bcn ) {
				$kinds_name_id					 = $bcn['DictionaryDetail']['id'];
				$kinds_name						 = $bcn['DictionaryDetail']['name'];
				$kinds_name_img[$kinds_name_id]	 = $bcn['DictionaryDetail']['name'];
										
				$new_kinds_en[$kinds_name_id]	 = $bcn['DictionaryDetail']['express_name_en'];
				$new_kinds_ko[$kinds_name_id]	 = $bcn['DictionaryDetail']['express_name_ko'];
									
				if ( strlen(trim($new_kinds_ko[$kinds_name_id])) > 0 ) {
					$description = $new_kinds_ko[$kinds_name_id];
				} else if ( strlen(trim($new_kinds_en[$kinds_name_id])) > 0 ) {
					$description = $new_kinds_en[$kinds_name_id];
				}
					$available_script['kinds'.$kinds_name_id] = $description;
			}
		}
		
		if ( isset($books_level_name['info']) ) {
			foreach ( $books_level_name['info'] as $keyBLN => $bln ) {						
				$levels_name_id						 = $bln['DictionaryDetail']['id'];
				$levels_name						 = $bln['DictionaryDetail']['name'];
//				$levels_name_img[$levels_name_id]	 = $bln['DictionaryDetail']['express_name_en'];
											
				$new_levels_en[$levels_name_id]		 = $bln['DictionaryDetail']['express_name_en'];
				$new_levels_ko[$levels_name_id]		 = $bln['DictionaryDetail']['express_name_ko'];
				$levels_name_img[$levels_name_id]	 = $bln['DictionaryDetail']['express_name_en'];
											
				if ( strlen(trim($new_levels_ko[$levels_name_id])) > 0 ) {
					$description = $new_levels_ko[$levels_name_id];
				} else if ( strlen(trim($new_levels_en[$levels_name_id])) > 0 ) {
					$description = $new_levels_en[$levels_name_id];
				}
				$available_script['levels'.$levels_name_id] = $description;
			}
		}
		
		$_count = 1;
		$content_result .= '<thead><tr>';
		$content_result .=	 '<td align="center" style="background:#888888;"><div style="color:#FFFFFF;font-weight:bold;letter-spacing:1px;">#</div></td>';
		$content_result .=	 '<td align="center" style="background:#888888;"><div style="color:#FFFFFF;font-weight:bold;letter-spacing:1px;">책제목</div></td>';
		$content_result .=	 '<td align="center" style="background:#888888;"><div style="color:#FFFFFF;font-weight:bold;letter-spacing:1px;">설명</div></td>';
		$content_result .=	 '<td align="center" style="background:#888888;"><div style="color:#FFFFFF;font-weight:bold;letter-spacing:1px;">종류</div></td>';
		$content_result .= '</tr></thead><tbody>';
		foreach ($_fdb_teacher_book as $ListTeacherBook) {
		$content_result .= '<tr>';
		$content_result .=	 '<td id="tbi" align="center" style="font-size:14px;">'.$_count.'</td>';
		$content_result .=	 '<td id="tbi" align="center" style="font-size:12px;"><a href="'.c_path().'/ftpbooks/show_book/'.$ListTeacherBook['Ftpbook']['id'].'">'.$ListTeacherBook['Ftpbook']['title'].'</a></td>';
		$content_result .=	 '<td id="tbi" align="center" style="font-size:12px;"><a href="'.$ListTeacherBook['Ftpbook']['description'].'">INFO</a></td>';
				$category	 = $ListTeacherBook['Ftpbook']['category']; 
				if ( isset($ftpbook_img_path['kind_img'.$category]) || isset($available_script['kinds'.$category]) ) {
					if ( isset($ftpbook_img_path['kind_img'.$category]) ) {
						$img_kinds_path	 = $ftpbook_img_path['kind_img'.$category];
					}
					$description	 = $available_script['kinds'.$category];
				}

		$content_result .=	 '<td id="tbi" align="center" style="font-size:12px;">'.$description.'</td>';
		$content_result .= '</tr>';
		$_count = $_count + 1;
		}
		$content_result .= '</tbody></table>';
	} else {
		$content_result .= '<tr>';
		$content_result .=	 '<td><div style="color:#FF0000;font-weight:bold;letter-spacing:1px;" align="center">0(zero) FOUND!..</div></td>';
		$content_result .= '</tr>';
		$content_result .= '</table>';
	}
	$this->set('content_result',$content_result);
}

function evaluation($teacher_id = null) {
	$this->loadModel('TeacherEvaluate');
	$this->loadModel('Evaluation');
	$tdfirst = false;
	$_fdb_teacher_evaluation = $this->Evaluation->query(
											"SELECT
												AVG(Evaluation.child) as StudentChild,
												AVG(Evaluation.beginner) as StudentBeginner,
												AVG(Evaluation.intermediate) as StudentIntermediate,
												AVG(Evaluation.advanced) as StudentAdvanced
											FROM evaluations as Evaluation
											WHERE Evaluation.by_whom = 'U'
											AND Evaluation.teacher_evaluation_id = '$teacher_id'
											");
	
	$content_teacher_evaluation  = ' <form name="form1" method="post"> ';
	$content_teacher_evaluation .= ' <table> ';
	$content_teacher_evaluation .= '	<tr> ';
	$content_teacher_evaluation .= '		<td align="center"><DIV id="divpointer" class="cont_title" onclick="launchWindow()" style="letter-spacing:2px;"><STRONG>이 강사님을 평가해주세요[클릭!] </STRONG></DIV></td> ';
	$content_teacher_evaluation .= '	</tr> ';
	$content_teacher_evaluation .= ' </table> ';
	$content_teacher_evaluation .= ' <div id="evaluation_content_page" style="border:1px solid;filter: progid:DXImageTransform.Microsoft.Shadow(direction=135,strength=2,color=888);background:#FFF;padding:10px;" align="center">  ';
	$content_teacher_evaluation .= '	<table style="border-collapse:collapse;"> ';
	$content_teacher_evaluation .= '	<tr> ';
	$content_teacher_evaluation .= '		<td align="center" colspan="2"><DIV id="divpointer" class="cont_title" onclick="launchWindow('."'close'".')" style="letter-spacing:2px;"><STRONG>Teacher Evaluation [ CANCEL/CLOSE ]</STRONG></DIV></td> ';
	$content_teacher_evaluation .= '	</tr> ';
	$content_teacher_evaluation .= '	<tr> ';
	$content_teacher_evaluation .= '		<td align="center"  colspan="2"> ';
	$content_teacher_evaluation .= '        <table id="tableradio" border="1" style="border-collapse:collapse;">';
	$content_teacher_evaluation .= '        <tr>';
	$content_teacher_evaluation .= '        	<td colspan="11" style="font-weight:bold;font-size:24px;letter-spacing:1px;background:#888;color:#000;">평가하기</td>';
	$content_teacher_evaluation .= '        </tr>';
	$content_teacher_evaluation .= '        <tr>';
	$content_teacher_evaluation .= '        	<td align="left" style="font-weight:bold;font-size:20px;letter-spacing:1px;background:#888;color:#000;">어린이:</td>';
	for ($i=1;$i<=10;$i++) {
		//$a = $i * 20; 
		if (count($_fdb_teacher_evaluation) > 0 && $_fdb_teacher_evaluation[0][0]['StudentChild'] != null && round($_fdb_teacher_evaluation[0][0]['StudentChild']) == $i) {
	$content_teacher_evaluation .= '        	<td id="tdradio"><input type="radio" onclick="bargraph(this.name);" name="child" value="'.$i.'" checked="checked"> '.$i.'</td>';
		} else {
			if ($tdfirst == false) {
	$content_teacher_evaluation .= '        	<td id="tdradio"><input type="radio" onclick="bargraph(this.name);" name="child" value="'.$i.'" checked> '.$i.'</td>';
				$tdfirst = true;
			} else {
	$content_teacher_evaluation .= '        	<td id="tdradio"><input type="radio" onclick="bargraph(this.name);" name="child" value="'.$i.'"> '.$i.'</td>';
			}
		}
	}
	$content_teacher_evaluation .= '        </tr>';
	
	
	$content_teacher_evaluation .= '        <tr>';
	$content_teacher_evaluation .= '        	<td align="left" style="font-weight:bold;font-size:20px;letter-spacing:1px;background:#888;color:#000;">초급자:</td>';
	
	$tdfirst = false;
	for ($i=1;$i<=10;$i++) {
		//$a = $i * 20;
		if (count($_fdb_teacher_evaluation) > 0 && $_fdb_teacher_evaluation[0][0]['StudentBeginner'] != null && round($_fdb_teacher_evaluation[0][0]['StudentBeginner']) == $i) {
	$content_teacher_evaluation .= '                <td id="tdradio"><input type="radio" onclick="bargraph(this.name);" name="beginner" value="'.$i.'" checked="checked"> '.$i.'</td>';
		} else {
			if ($tdfirst == false) {
	$content_teacher_evaluation .= '                <td id="tdradio"><input type="radio" onclick="bargraph(this.name);" name="beginner" value="'.$i.'" checked> '.$i.'</td>';
				$tdfirst = true;
			} else {
	$content_teacher_evaluation .= '                <td id="tdradio"><input type="radio" onclick="bargraph(this.name);" name="beginner" value="'.$i.'"> '.$i.'</td>';
			}
		}
	}
	$content_teacher_evaluation .= '        </tr>';
	
	$content_teacher_evaluation .= '        <tr>';
	$content_teacher_evaluation .= '                <td align="left" style="font-weight:bold;font-size:20px;letter-spacing:1px;background:#888;color:#000;">중급자:</td>';
	
	$tdfirst = false;
	for ($i=1;$i<=10;$i++) {
		//$a = $i * 20;
		if (count($_fdb_teacher_evaluation) > 0 && $_fdb_teacher_evaluation[0][0]['StudentIntermediate'] != null && round($_fdb_teacher_evaluation[0][0]['StudentIntermediate']) == $i) {
	$content_teacher_evaluation .= '                <td id="tdradio"><input type="radio" onclick="bargraph(this.name);" name="intermediate" value="'.$i.'" checked="checked"> '.$i.'</td>';
		} else {
			if ($tdfirst == false) {
	$content_teacher_evaluation .= '                <td id="tdradio"><input type="radio" onclick="bargraph(this.name);" name="intermediate" value="'.$i.'" checked> '.$i.'</td>';
				$tdfirst = true;
			} else {
	$content_teacher_evaluation .= '                <td id="tdradio"><input type="radio" onclick="bargraph(this.name);" name="intermediate" value="'.$i.'"> '.$i.'</td>';
			}
		}
	}
	$content_teacher_evaluation .= '        </tr>';
	
	$content_teacher_evaluation .= '        <tr>';
	$content_teacher_evaluation .= '                <td align="left" style="font-weight:bold;font-size:20px;letter-spacing:1px;background:#888;color:#000;">고급자:</td>';
	
	$tdfirst = false;
	for ($i=1;$i<=10;$i++) {
		//$a = $i * 20;
		if (count($_fdb_teacher_evaluation) > 0 && $_fdb_teacher_evaluation[0][0]['StudentAdvanced'] != null && round($_fdb_teacher_evaluation[0][0]['StudentAdvanced']) == $i) {
	$content_teacher_evaluation .= '                <td id="tdradio"><input type="radio" onclick="bargraph(this.name);" name="advanced" value="'.$i.'" checked="checked"> '.$i.'</td>';
		} else {
			if ($tdfirst == false) {
	$content_teacher_evaluation .= '                <td id="tdradio"><input type="radio" onclick="bargraph(this.name);" name="advanced" value="'.$i.'" checked> '.$i.'</td>';
				$tdfirst = true;
			} else {
	$content_teacher_evaluation .= '                <td id="tdradio"><input type="radio" onclick="bargraph(this.name);" name="advanced" value="'.$i.'"> '.$i.'</td>';
			}
		}
	}
	$content_teacher_evaluation .= '        </tr>';
	$content_teacher_evaluation .= '        </table>';
	$content_teacher_evaluation .= '		</td> ';
	$content_teacher_evaluation .= '	</tr> ';
	
	$content_teacher_evaluation .= '	<tr> ';
	$content_teacher_evaluation .= '		<td align="center" style="padding:8px;" > ';
	$content_teacher_evaluation .= '		<TABLE width="150" height="250" background="'.c_path().'/img/graph_bg.gif">';
	$content_teacher_evaluation .= '		<tr>';
	$content_teacher_evaluation .= '				<td valign="top">';
	$content_teacher_evaluation .= '				<TABLE width="130" height="205" align="right">';
	$content_teacher_evaluation .= '				<tr>';
	$content_teacher_evaluation .= '					<TD align="center" valign="bottom"><IMG src="'.c_path().'/img/graph_bar.gif" alt="" id="child" width="15" height="20"></TD>';
	$content_teacher_evaluation .= '					<TD align="center" valign="bottom"><IMG src="'.c_path().'/img/graph_bar.gif" alt="" id="beginner" width="15" height="20"></TD>';
	$content_teacher_evaluation .= '					<TD align="center" valign="bottom"><IMG src="'.c_path().'/img/graph_bar.gif" alt="" id="intermediate" width="15" height="20"></TD>';
	$content_teacher_evaluation .= '					<TD align="center" valign="bottom"><IMG src="'.c_path().'/img/graph_bar.gif" alt="" id="advanced" width="15" height="20"></TD>';
	$content_teacher_evaluation .= '				</tr>';
	$content_teacher_evaluation .= '				</TABLE>';
	$content_teacher_evaluation .= '				</td>';
	$content_teacher_evaluation .= '		</tr>';
	$content_teacher_evaluation .= '		</TABLE>';
	$content_teacher_evaluation .= '		</td> ';
	$content_teacher_evaluation .= '		<td align="center" style="padding:8px;" > ';
	$content_teacher_evaluation .= '		<TABLE width="150" height="250" background="'.c_path().'/img/graph_bg.gif">';
	$content_teacher_evaluation .= '		<tr>';
	$content_teacher_evaluation .= '			<td valign="top">';
	$content_teacher_evaluation .= '			<TABLE width="130" height="205" align="right">';
	$content_teacher_evaluation .= '			<tr>';
	if (count($_fdb_teacher_evaluation) > 0 && $_fdb_teacher_evaluation[0][0]['StudentChild'] != null && $_fdb_teacher_evaluation[0][0]['StudentBeginner'] != null && $_fdb_teacher_evaluation[0][0]['StudentIntermediate'] != null && $_fdb_teacher_evaluation[0][0]['StudentAdvanced'] != null) {
	$content_teacher_evaluation .= '				<TD align="center" valign="bottom"><IMG src="'.c_path().'/img/graph_bar.gif" alt="" width="15" height="'.(round($_fdb_teacher_evaluation[0][0]['StudentChild']) * 20).'"></TD>';
	$content_teacher_evaluation .= '				<TD align="center" valign="bottom"><IMG src="'.c_path().'/img/graph_bar.gif" alt="" width="15" height="'.(round($_fdb_teacher_evaluation[0][0]['StudentBeginner']) * 20).'"></TD>';
	$content_teacher_evaluation .= '				<TD align="center" valign="bottom"><IMG src="'.c_path().'/img/graph_bar.gif" alt="" width="15" height="'.(round($_fdb_teacher_evaluation[0][0]['StudentIntermediate']) * 20).'"></TD>';
	$content_teacher_evaluation .= '				<TD align="center" valign="bottom"><IMG src="'.c_path().'/img/graph_bar.gif" alt="" width="15" height="'.(round($_fdb_teacher_evaluation[0][0]['StudentAdvanced']) * 20).'"></TD>';
	
	$content_teacher_evaluation_student = '				<TD align="center" valign="bottom"><IMG src="'.c_path().'/img/graph_bar.gif" alt="" width="15" height="'.(round($_fdb_teacher_evaluation[0][0]['StudentChild']) * 20).'"></TD>';
	$content_teacher_evaluation_student .= '				<TD align="center" valign="bottom"><IMG src="'.c_path().'/img/graph_bar.gif" alt="" width="15" height="'.(round($_fdb_teacher_evaluation[0][0]['StudentBeginner']) * 20).'"></TD>';
	$content_teacher_evaluation_student .= '				<TD align="center" valign="bottom"><IMG src="'.c_path().'/img/graph_bar.gif" alt="" width="15" height="'.(round($_fdb_teacher_evaluation[0][0]['StudentIntermediate']) * 20).'"></TD>';
	$content_teacher_evaluation_student .= '				<TD align="center" valign="bottom"><IMG src="'.c_path().'/img/graph_bar.gif" alt="" width="15" height="'.(round($_fdb_teacher_evaluation[0][0]['StudentAdvanced']) * 20).'"></TD>';
	} else {
	$content_teacher_evaluation .= '				<TD align="center" valign="bottom"><IMG src="'.c_path().'/img/graph_bar.gif" alt="" width="15" height="0"></TD>';
	$content_teacher_evaluation .= '				<TD align="center" valign="bottom"><IMG src="'.c_path().'/img/graph_bar.gif" alt="" width="15" height="0"></TD>';
	$content_teacher_evaluation .= '				<TD align="center" valign="bottom"><IMG src="'.c_path().'/img/graph_bar.gif" alt="" width="15" height="0"></TD>';
	$content_teacher_evaluation .= '				<TD align="center" valign="bottom"><IMG src="'.c_path().'/img/graph_bar.gif" alt="" width="15" height="0"></TD>';
	
	$content_teacher_evaluation_student = '				<TD align="center" valign="bottom"><IMG src="'.c_path().'/img/graph_bar.gif" alt="" width="15" height="0"></TD>';
	$content_teacher_evaluation_student .= '				<TD align="center" valign="bottom"><IMG src="'.c_path().'/img/graph_bar.gif" alt="" width="15" height="0"></TD>';
	$content_teacher_evaluation_student .= '				<TD align="center" valign="bottom"><IMG src="'.c_path().'/img/graph_bar.gif" alt="" width="15" height="0"></TD>';
	$content_teacher_evaluation_student .= '				<TD align="center" valign="bottom"><IMG src="'.c_path().'/img/graph_bar.gif" alt="" width="15" height="0"></TD>';
	}
	$content_teacher_evaluation .= '			</tr>';
	$content_teacher_evaluation .= '			</TABLE>';
	$content_teacher_evaluation .= '			</td>';
	$content_teacher_evaluation .= '		</tr>';
	$content_teacher_evaluation .= '		</TABLE>';
	$content_teacher_evaluation .= '		</td> ';
	$content_teacher_evaluation .= '	</tr> ';
	$content_teacher_evaluation .= '	<tr> ';
	$content_teacher_evaluation .= '		<td align="center"><DIV class="cont_title" style="letter-spacing:2px;"><STRONG>나의평가</STRONG></DIV></td> ';
	$content_teacher_evaluation .= '		<td align="center"><DIV class="cont_title" style="letter-spacing:2px;"><STRONG>평균치</STRONG></DIV></td> ';
	$content_teacher_evaluation .= '	</tr> ';
	$content_teacher_evaluation .= '	<tr> ';
	$content_teacher_evaluation .= '		<td align="center" colspan="2"><DIV id="divpointer" class="cont_title" onclick="evaluations('."'".$teacher_id."'".')" style="letter-spacing:2px;"><STRONG>평가 저장하기(눌러주세요)</STRONG></DIV></td> ';
	$content_teacher_evaluation .= '	</tr> ';
	$content_teacher_evaluation .= '	</table> ';
	$content_teacher_evaluation .= '  </div><div id="mask"></div> ';
	$content_teacher_evaluation .= '  </form> ';
	
	
	$_fdb_teacher_evaluation_m = $this->Evaluation->query(
										"SELECT
											AVG(Evaluation.child) as ManagerChild,
											AVG(Evaluation.beginner) as ManagerBeginner,
											AVG(Evaluation.intermediate) as ManagerIntermediate,
											AVG(Evaluation.advanced) as ManagerAdvanced
										FROM evaluations as Evaluation
										WHERE Evaluation.by_whom = 'M'
										AND Evaluation.teacher_evaluation_id = '$teacher_id'
										");
	
	$content_teacher_evaluation_manager  = ' <TABLE cellpadding="7" cellspacing="3" style="background:#E3E3E3;color:red;letter-spacing:2px;"> ';
	$content_teacher_evaluation_manager .= ' 	<caption class="cont_title" style="background:#E3E3E3;color:red;letter-spacing:2px;text-shadow: 2px 2px 5px #888888;" >매니저평가:</caption> ';
	$content_teacher_evaluation_manager .= ' 	<TR> ';
	$content_teacher_evaluation_manager .= ' 	<TD bgcolor="#FFFFFF"> ';
	$content_teacher_evaluation_manager .= ' 		<TABLE width="150" height="250" cellpadding="0" cellspacing="0" background="'.c_path().'/img/graph_bg.gif"> ';
	$content_teacher_evaluation_manager .= ' 		<TR> ';
	$content_teacher_evaluation_manager .= ' 			<TD valign="top"> ';
	$content_teacher_evaluation_manager .= ' 			<TABLE width="130" height="205" align="right" cellpadding="0" cellspacing="0"> ';
	$content_teacher_evaluation_manager .= ' 			<TR> ';
	if (count($_fdb_teacher_evaluation_m) > 0 && $_fdb_teacher_evaluation_m[0][0]['ManagerChild'] != null && $_fdb_teacher_evaluation_m[0][0]['ManagerBeginner'] != null && $_fdb_teacher_evaluation_m[0][0]['ManagerIntermediate'] != null && $_fdb_teacher_evaluation_m[0][0]['ManagerAdvanced'] != null) {
	$content_teacher_evaluation_manager .= '				<TD align="center" valign="bottom"><IMG src="'.c_path().'/img/graph_bar.gif" alt="" width="15" height="'.(round($_fdb_teacher_evaluation_m[0][0]['ManagerChild']) * 20).'"></TD>';
	$content_teacher_evaluation_manager .= '				<TD align="center" valign="bottom"><IMG src="'.c_path().'/img/graph_bar.gif" alt="" width="15" height="'.(round($_fdb_teacher_evaluation_m[0][0]['ManagerBeginner']) * 20).'"></TD>';
	$content_teacher_evaluation_manager .= '				<TD align="center" valign="bottom"><IMG src="'.c_path().'/img/graph_bar.gif" alt="" width="15" height="'.(round($_fdb_teacher_evaluation_m[0][0]['ManagerIntermediate']) * 20).'"></TD>';
	$content_teacher_evaluation_manager .= '				<TD align="center" valign="bottom"><IMG src="'.c_path().'/img/graph_bar.gif" alt="" width="15" height="'.(round($_fdb_teacher_evaluation_m[0][0]['ManagerAdvanced']) * 20).'"></TD>';;
	} else {
	$content_teacher_evaluation_manager .= '				<TD align="center" valign="bottom"><IMG src="'.c_path().'/img/graph_bar.gif" alt="" width="15" height="0"></TD>';
	$content_teacher_evaluation_manager .= '				<TD align="center" valign="bottom"><IMG src="'.c_path().'/img/graph_bar.gif" alt="" width="15" height="0"></TD>';
	$content_teacher_evaluation_manager .= '				<TD align="center" valign="bottom"><IMG src="'.c_path().'/img/graph_bar.gif" alt="" width="15" height="0"></TD>';
	$content_teacher_evaluation_manager .= '				<TD align="center" valign="bottom"><IMG src="'.c_path().'/img/graph_bar.gif" alt="" width="15" height="0"></TD>';;
	}
	$content_teacher_evaluation_manager .= ' 			</TR> ';
	$content_teacher_evaluation_manager .= ' 			</TABLE> ';
	$content_teacher_evaluation_manager .= ' 			</TD> ';
	$content_teacher_evaluation_manager .= ' 		</TR> ';
	$content_teacher_evaluation_manager .= ' 		</TABLE> ';
	$content_teacher_evaluation_manager .= ' 	</TD> ';
	$content_teacher_evaluation_manager .= ' 	</TR> ';
	$content_teacher_evaluation_manager .= ' </TABLE> ';
	
	$this->set('content_teacher_evaluation_manager',$content_teacher_evaluation_manager);
	$this->set('content_teacher_evaluation_student',$content_teacher_evaluation_student);
	$this->set('content_teacher_evaluation',$content_teacher_evaluation);
}

function submit($teacher_id = null){
	$loginInfo = $this->CheckHandler->checkLoginInfo();
	$idUser = $loginInfo['User']['id'];
	
	if (isset($this->params['form']['child']) && isset($this->params['form']['beginner']) && isset($this->params['form']['intermediate']) && isset($this->params['form']['advanced'])) {
		$child			 = $this->params['form']['child'];
		$beginner		 = $this->params['form']['beginner'];
		$intermediate	 = $this->params['form']['intermediate'];
		$advanced		 = $this->params['form']['advanced'];
		$by_whom		 = 'U';
		
		$this->loadModel('Evaluation');
		$_fdb_evaluation = $this->Evaluation->query(
										"SELECT
											*
										FROM evaluations as Evaluation
										WHERE Evaluation.teacher_evaluation_id = '$teacher_id'
										AND Evaluation.by_whom = 'U'
										AND Evaluation.by_whom_id = '$idUser'
										");
		if (count($_fdb_evaluation) > 0) {
			$modified		 = "'".date('Y-m-d H:i:s')."'";
			$this->Evaluation->updateAll(array('child' => $child, 'beginner' => $beginner, 'intermediate' => $intermediate, 'advanced' => $advanced, 'modified' => $modified), array('teacher_evaluation_id' => $teacher_id, 'by_whom' => $by_whom, 'by_whom_id' => $idUser));
		} else {
			$this->Evaluation->set(array('teacher_evaluation_id' => $teacher_id, 'by_whom' => $by_whom, 'by_whom_id'=> $idUser, 'child' => $child, 'beginner' => $beginner, 'intermediate' => $intermediate, 'advanced' => $advanced));
			$this->Evaluation->save();
		}
	}
	$url = '/profile/'.$teacher_id;
	$this->redirect(array('action' => $url));
}

// ADDED LEVY AVILES up to HERE


//Loginしたら

//Lesson 予約
function reserve($id = null, $month_button = null ,$reserve_last_click_day=null)
{

	//Constant変数を読むことＣｏｎｔｒｏｌｌｅｒ共通　セッションのログインＩＮＦＯ読む
	global $const; 
	$this->set('const', $const);
	$loginInfo = $this->Session->read($const['loginInfo']);
	
	$teacherChar = $this->Programmer1Handler->teacherChar();
	$teaChar = array();
	$this->set('teacherChar',$teacherChar);
	
	//2011-01-01 read all lesson table for Fixed student and Trial lesson User
	//load goods model
	$this->loadModel('Lesson');
	
	//$this->layout = 'ajax';
	$teacher_id ; 
	//先生選択画面から先生の映上をクリックしてきた場合
	if($id != null){
		
		$teacher['Teacher'] = $this->ModelHandler->getTeacherById($id);
		//$teacher['Teacher'] = $teacher1;
		//Available_Lessonの情報を求める
		$teacher['Available_lesson'] = $this->ModelHandler->getAvailableLessonByTeacherId($teacher['Teacher']['id']);
		$teacher_id = $id;
	}else{
		//予約画面から自画面遷移する時
		$teacher_id = $this->data['Teachers']['id'];
		
		$teacher['Teacher'] = $this->ModelHandler->getTeacherById($teacher_id);
		$teacher['Available_lesson'] = $this->ModelHandler->getAvailableLessonByTeacherId($teacher_id);
		
		//$teacher= $this->Teacher->findById($this->data['Teachers']['id']);
	}
	//自画面から遷移する場合または先生検索画面から遷移した時も、
	//ＰＬＡＮがＥ１、Ｅ２の場合は、残りのコーマを求めてセッションに保存する
	
	if($loginInfo['User']['pay_plan'] == $const['E1']
		|| $loginInfo['User']['pay_plan'] == $const['E2']
		|| $loginInfo['User']['pay_plan'] == 'E3'
		|| $loginInfo['User']['pay_plan'] == 'E4'
		|| $loginInfo['User']['pay_plan'] == 'E5'
		|| $loginInfo['User']['pay_plan'] == 'E6'
		|| $loginInfo['User']['pay_plan'] == 'E7'
		|| $loginInfo['User']['pay_plan'] == 'E8'
		){
		$todayReservation = $this->ModelHandler->getTodayReservation($loginInfo['User']['id'],$const['Teacher']);
		$loginInfo['User']['todayReservation'] = $todayReservation;
		$this->Session->write($const['loginInfo'],$loginInfo);
	}
	

	//セッションの読む
	$click_day = $this->Session->read('click_day');
	//引数を宣言
	$year;
	$month;
	$day;
	$fomatted_ymd;
	
	//先生検索から遷移した場合
	if (empty($this->data))
    {
		$year = date("Y");
		$month = sprintf('%02d',date("n"));
		$day =sprintf('%02d',date("j"));

		$present_ymd = $year.$month.$day;
		
    	//PREV NEXT　ボタンを押した場合
		if($month_button != null && $month_button != 0){
			//print_r("month_buttonmonth_buttonmonth_buttonmonth_button".$month_button);
			//もし現在日付の同じ場合は現在日付で表示する
			if(substr($present_ymd,0,6) == substr($month_button,0,6)){
				$fomatted_ymd = $present_ymd;
			}else{
				$fomatted_ymd = $month_button;
			}
		//前画面から日を選択しなかった場合
		}elseif($reserve_last_click_day!=null){
			$fomatted_ymd = $reserve_last_click_day;
		}elseif(sizeof($click_day) == 0){
			//現在の日日をセット
			$fomatted_ymd = $present_ymd;
		//日日を選択した場合
		}else{
			//array sort する
			ksort($click_day);
			$fomatted_ymd;
			foreach($click_day as $key=>$value){
				$fomatted_ymd = $key;
				//配列にある日付を昇順して一番目の日付を選択してループを抜ける
				break;
			}
		}
		//上記三つの場合変数　$fomatted_ymdがあるから共通で処理
		$year = substr($fomatted_ymd,0,4);
		$month = substr($fomatted_ymd,4,2);
		$day = substr($fomatted_ymd,6,2);	
    }else{
    	$year = substr($this->data['Teachers']['cal_date'],0,4);
		$month = substr($this->data['Teachers']['cal_date'],4,2);
		$day = substr($this->data['Teachers']['cal_date'],6,2);
		$fomatted_ymd = $this->data['Teachers']['cal_date'];
    	//print_r($this->data);
    }
    //選択した日付をVIEWに転送
    $this->set('fomatted_ymd', $fomatted_ymd);
    //予約確認画面から戻る時のみするために使うセッション変数
    $this->Session->write('reserve_last_click_day',$fomatted_ymd);
    
   
    //仕様変更　セッション変数からポスト変数に変更する
	
     $teacher['Available_lesson'] = $this->ModelHandler->getAvailableLessonByTeacherId($teacher_id);
    
     
     //先生のSCHEDULE保ってる配列
	 $teacherSch = array();
     foreach($teacher['Available_lesson'] as $a_lesson){
     	if($a_lesson['reserve_status'] == 'R'){
     		$available_time = $a_lesson['available_time'];
     		$available_time = change_date_format($available_time,'NOHIPON');
     		$teacherSch[]= $available_time;
     	}
     }
     //print_r($teacher['Available_lesson']);
     $this->set('teacher', $teacher);
     // 2014-10-14 12:38 by levy
     //$this->Session->write('teacherSch',$teacherSch);
    
    ////PREV NEXT ボタンを押した場合、カレンダの中で先生IDを表示するために先生IDを引数として渡すため
	$this->set('reserve_calendar', reserve_calendar($year, $month, $day,$teacher_id));
	
	//予約画面からボタンをクリックして、確認画面から戻った場合、ボタンの状態をそのまま、表示するために
	$tacherConfirmSch = $this->Session->read('tacherConfirmSch');
	if($tacherConfirmSch == null){
		$tacherConfirmSch = array();
	}
	
	// get all reservation_kinds
	$GetAllReservationKinds = $this->Programmer1Handler->GetAllReservationKinds();
	
	//2012-04-28 Start
	$ct = get_time();
	$fixstudents = $this->Lesson->find('all', array(
			'fields' => array('DATE(Max(Lesson.reserve_time)) AS DATE' ,'TIME(Max(Lesson.reserve_time)) AS TIME', 'Lesson.reservation_kinds') ,
			'conditions' => array(
				'Lesson.teacher_id' => $teacher_id,
				'Lesson.reservation_kinds' => $GetAllReservationKinds,
				'Lesson.lesson_status' => 'R',
				'Lesson.reserve_time >'=>$ct['time_ymd']),
			'group'=> array('Lesson.user_id','TIME(Lesson.reserve_time)'),
			'order'=>array('TIME(Lesson.reserve_time)')
	));	
	
	$fix_date		 = array();
	$fix_time		 = array();
	$fix_date_names	 = array();
	$fix_date_kinds	 = array();
	$fix_kinds		 = array();
	$fix			 = "";
	$fixDays		 = "";
	$tooltips		 = "";
	//2012-04-29
	foreach( $fixstudents as $fixstudent){
		$fix		 = "";
		$fixDays	 = "";
		$tooltips	 = "";
		array_push($fix_date , $fixstudent[0]['DATE']);
		array_push($fix_time , substr($fixstudent[0]['TIME'],0,5));
		if ( $fixstudent['Lesson']['reservation_kinds'] == 'M3') {
			$fix = "화목가능";
//old		$fixDays = "M2";
			$fixDays = $fixstudent['Lesson']['reservation_kinds'];
		} else if ( $fixstudent['Lesson']['reservation_kinds'] == 'M2') {
			$fix = "월수금가능";
//old		$fixDays = "M3";
			$fixDays = $fixstudent['Lesson']['reservation_kinds'];
		}
		 else if ( in_array($fixstudent['Lesson']['reservation_kinds'], array('M', 'MD')) ) {
			$fix = "월수금가능";
			$fixDays = $fixstudent['Lesson']['reservation_kinds'];
		}
		 else {
		 	$fix = "월수금가능";
			$fixDays = $fixstudent['Lesson']['reservation_kinds'];
		 }
		$fix_date_names = date('Y-m-d H:i', strtotime($fixstudent[0]['DATE']." ".$fixstudent[0]['TIME']));
//		array_push($fix_date_kinds , $fix_date_names);
//		$fix_kinds["$fix_date_names"] = $fix;
//		or reservation_kinds
//		$fix_kinds["$fix_date_names"] = $fixstudent['Lesson']['reservation_kinds'];
		$fix_kinds["$fix_date_names"]["img"] = $fixDays;
		$fix_kinds["$fix_date_names"]["tooltip"] = $tooltips;
//echo
//		echo $fixstudent['Lesson']['reservation_kinds'] . '=>' . $fix_date_names. '=>' . $fix_kinds["$fix_date_names"].'<br />';
	}
	//2012-04-28 End
	//echo 'Year = '.$year.'<br>';
	//echo 'month = '.$month.'<br>';
	//echo 'day = '.$day.'<br>';
//	pr($teacher);
	//pr($teacherSch);
	//pr($tacherConfirmSch);
	//pr($fix_date);
	//pr($fix_time);
	//pr($_fixedSched);
	
	$available_lesson =  availableLesson($year,$month,$day,$teacher,$teacherSch,$tacherConfirmSch ,$fix_date, $fix_time,$this->Programmer1Handler->findFixedSched($teacher['Teacher']['id']),$this->Programmer1Handler->dateReserved($loginInfo['User']['id'],$teacher['Teacher']['id'], ($year.'-'.$month.'-'.$day) ), $fix_kinds);
	
	//先生スケーズルを求める
   foreach($teacher['Available_lesson'] as $lesson){
   	$lesson_time[] =  $lesson['available_time'];
   	$avilable_time16 = substr($lesson['available_time'],0,16);
   	$lesson_sub_day[] =	$avilable_time16;
    //ステータスも保存する
    //0:予約未完了 1:予約完了
    $lesson_status[$avilable_time16] = $lesson['reserve_status']; 
   }
  
	//2010-02-03 YHLEE ADD START
	//Point Button Show Flag 
	$user_point = $this->CheckHandler->checkReservationWithPoint();
	$point_button_show = false;
	$config = $this->Session->read('config');

	if( ($user_point!=null && $user_point >= $config['Config']['min_reserve_point']) || $loginInfo['User']['pay_plan'] == 'M' ){
		$point_button_show = true;
	}
	
	$this->set('point_button_show', $point_button_show);
	
	//decide reservation_kind in controller
	//2010 - 09 - 11(E3,E4 add)
	$reservation_kinds = 'point'; //default
	$pay_plan = $loginInfo['User']['pay_plan'];
	if($pay_plan == $const['P'] || $pay_plan == $const['M'] 
	|| $pay_plan == $const['S']  ){
		$reservation_kinds = $const['point_reservation'];
	}else if($pay_plan == $const['E1'] 
	          || $pay_plan == $const['E2']
	          || $pay_plan == 'E3'
	          || $pay_plan == 'E4'
	          || $pay_plan == 'E5'
	          || $pay_plan == 'E6'
	          || $pay_plan == 'E7'
	          || $pay_plan == 'E8'
	          || $pay_plan == 'S5'  ){
		$reservation_kinds = $const['koma_reservation'];
	}
	
	//2010-10-22 START
	//Pay Plan Start 2010-10-18
	//set a pay plan
	
	$over_class = 'NO';
	
	$c_number = $loginInfo['User']['c_number'];
	
	if($c_number > 0){
		
		$pay_plan = $loginInfo['User']['pay_plan'];
		$c_date = $loginInfo['User']['c_date'];
	
		$c_bonus = $loginInfo['User']['c_bonus'];
	
		$pay_plan_info = array();
		
		//E2case is mutiply times higher 
		if($pay_plan == 'E2'){
			$pay_plan_info['total_count'] = ($c_number *2) + $c_bonus;
		}else if($pay_plan == 'E3'){
			$pay_plan_info['total_count'] = ($c_number *3) + $c_bonus;
		}else if($pay_plan == 'E4'){
			$pay_plan_info['total_count'] = ($c_number *4) + $c_bonus;
		}else if($pay_plan == 'E5'){
			$pay_plan_info['total_count'] = ($c_number *5) + $c_bonus;
		}else if($pay_plan == 'E6'){
			$pay_plan_info['total_count'] = ($c_number *6) + $c_bonus;	
		}else if($pay_plan == 'E7'){
			$pay_plan_info['total_count'] = ($c_number *7) + $c_bonus;	
		}else if($pay_plan == 'E8'){
			$pay_plan_info['total_count'] = ($c_number *8) + $c_bonus;					
		}else {
			$pay_plan_info['total_count'] = $c_number + $c_bonus;
		}
		
		$conditions = array();
		
		$user_id = $loginInfo['User']['id'];
		
		$conditions['user_id'] = $user_id;
		
		$conditions['reserve_time'] = $c_date;
		
		//2011-01-01 Modified 
		$types = $loginInfo['User']['types'];
		
		if($types == 'FC'){
			$tmpClassNo = $this->ModelHandler->getClassNoFC($conditions,'Teacher');
		}else{
			$tmpClassNo = $this->ModelHandler->getClassNo($conditions,'Teacher');
		}
		
		$classNo = $tmpClassNo[0][0]['classNo'];
		
		
		
		//In case of fixed student cancel number has included
		
		
		if($types == 'F'){
			
			$cancel_no = $this->Lesson->find('count', array(
			'fields' => 'DISTINCT Lesson.id',
			'conditions' => array('Lesson.reservation_kinds' => 'M',
				'Lesson.lesson_status' => 'C' ,
				'Lesson.user_id'=>$user_id )));	
			
			$classNo = $cancel_no + $classNo;
			
			//pr($cancel_no);
			//pr($classNo);
			//pr($pay_plan_info['total_count']);
		}
		
		//Judge is is over than esmated class no
		
		if($classNo >= $pay_plan_info['total_count']){
			$over_class = 'YES';
		}
		
	}
	//2010-10-22 END
	
	//2011-04-08 START judge the leveltest teacher only for payplan type = 'M'
    
	//
	$have_to_get_level_test = 'N';
	
	if($loginInfo['User']['pay_plan'] == $const['M']){
    	//Check the howmany time he get a free lesson
    	$user_id = $loginInfo['User']['id'];
		//pr($loginInfo['User']['pay_plan']);
    	//count 'F' 0 and 'A'1
		//get searchs goods info by goods_name
		$number_F = $this->Lesson->find('count', array(
		'fields' => 'DISTINCT Lesson.id',
		'conditions' => array('Lesson.lesson_status' => 'F' ,'Lesson.user_id'=>$user_id )));
		
		//$number_A = $this->Lesson->find('count', array(
		//'fields' => 'DISTINCT Lesson.id',
		//'conditions' => array('Lesson.lesson_status' => 'A',)));
		
		//judge if teacher is leveltest or not
		//basically homebased teacher do not leveltest
		$teacher = $this->Teacher->findById($teacher_id);
		$teacher_level = $teacher['Teacher']['level'];
		//pr($teacher); 
		//first test is absent or first time
		//pr($number_F);
		//pr($teacher_level);
		if($number_F == 0 && $teacher_level == 0){
			$have_to_get_level_test = 'Y';
		}
   
    }
	//2011-04-18 END 
	
	$this->set('over_class', $over_class); 
	$this->set('reservation_kinds', $reservation_kinds);
	$this->set('have_to_get_level_test', $have_to_get_level_test); 
	//2010-02-03 YHLEE ADD END
	
	
	
	$this->set('available_lesson', $available_lesson);
	
	
	
	
	//pr($this->Programmer1Handler->PaymentDay($loginInfo['User']['mb_id']));
	$this -> set('PaymentDay', $this -> Programmer1Handler -> PaymentDay($loginInfo['User']['mb_id']));

//	get user payment data from payment table atleast 10 record
//	2014-05-27 16:16 levi aviles
	$this -> set ( 'getUserPaymentData', $this -> Programmer1Handler -> getUserPaymentData( $loginInfo['User']['mb_id'] ) );
	
	//エラーメッセージセット
	$messages='';
	$this->set('messages', $messages);

 $this->set('teacher_video', $this->Programmer1Handler->teacherVideo($teacher_id));
} // End function reserve ();

//ユーザからLesson 確認
function reserve_confirm()
{
	//$this->CheckHandler->checkValidAccess();
	//Constant変数を読むことＣｏｎｔｒｏｌｌｅｒ共通　セッションのログインＩＮＦＯ読む
	global $const; 
	$this->set('const', $const);
	
	$teacher_id = $this->params['form']['id'];
	$teacher= $this->Teacher->findById($teacher_id);
	
	//予約確認画面で先生の情報を表示するため、先生情報をSELECTする
	$this->set('teacher', $teacher);
	
	//選択された予約時間をセッションから受け取る
	//画面から予約時間を表示することと、戻るボタンを押してからOPENボタンの情報を維持するため
	$teacherSch = $this->Session->read('teacherSch');
	
	$tacherConfirmSch;
		
	//2010-03-24 only one schedule start
	$tacherConfirmSch = array();
	$reservation_time = $this->params['form']['reservation_time'];
	$tacherConfirmSch[$reservation_time]= $reservation_time;
	//2010-03-24 only one schedule end
	
	$this->Session->write("tacherConfirmSch",$tacherConfirmSch);
	
	//仕様変更先生は自分が自身があるＴｅｘｔを先にＰｒｏｆｉｌｅへ登録
	$text_code_db = $teacher['Teacher']['text_code'];
	$text_code_array = split('/' ,$text_code_db);
	$text_combo = $this->ModelHandler->getTextCodeFromCatalog('text',$text_code_array);
	$selected ['text'] =  '01';
	$this->set('text_combo', $text_combo);
	$this->set('selected', $selected);
	
	//現在のユーザポイントを求めるため、usersテーブルをSELECT
	//lessons tableの 情報を求める
	$loginInfo = $this->Session->read('loginInfo');
	
	
	
	//2010-02-03 YHLEE ADD START
	$mb_id = $loginInfo['User']['mb_id'];
	$user_id = $loginInfo['User']['id'];
	$user_point = $this->ModelHandler->getMbPoint($mb_id,'Teacher');
	$loginInfo['User']['points'] = $user_point;
	$this->Session->write('loginInfo',$loginInfo);
    //2010-02-03 YHLEE ADD END
    
	$pay_plan = $loginInfo['User']['pay_plan'];
	$skype_id = $loginInfo['User']['skype_id'];
	$button_show = 'Y';
	$have_skype_id = 'Y';
	
	//2010-02-03 Reservation Kinds decide
    //POINT and KOMA
    //POINT Pay_plan = M,P,S,
    //E1,E2(reservation_kinds='point_reservation')
    //the other case E1,E2(koma)
	$reservation_kinds;
	$reservation_kinds = $this->params['form']['reservation_kinds'];
    
    //コマの設定
	//print_r($loginInfo['User']['pay_plan']);
	//2010-09-11 add
	
	//2011-01-22 to check to cancel point reservation and reserve koma the same time
	//load goods model
	$this->loadModel('Lesson');
	
	//2010-06-14 how many class user reserved in reserve time start
	$year = substr($reservation_time,0,4);
  	$month = substr($reservation_time,4,2);
  	$day = substr($reservation_time,6,2);
  	$hour = substr($reservation_time,8,2);
  	$minites = substr($reservation_time,10,2);
  	
  	//compare last day
  	
  	$temp_reserve_time = sprintf("%04d-%02d-%02d %02d:%02d:00",$year,$month,$day,$hour,$minites);
  	
  	//2013-01-12 ADD (If lesson table , status = R or A or F) can not be reserved
  	$lesson_already_reserved_count = $this->CheckHandler->checkAlreadyReserved($teacher_id,$temp_reserve_time);
  	//pr($lesson_already_reserved_count);
  	//2013-01-12 END
	
 
  	
  	$point_cancel_no = 0;
  	$messages='';
  	
	if($reservation_kinds==$const['koma_reservation']){
		
		$todayReservation = $this->ModelHandler->getTodayReservation($loginInfo['User']['id'],$const['Teacher']);
		$loginInfo['User']['todayReservation'] = $todayReservation;
		$this->Session->write($const['loginInfo'],$loginInfo);
		
		$loginInfo['User']['pay_plan'] = 0;
		
		if($loginInfo['User']['pay_plan'] == $const['E1']){
			$koma['present'] = 1 - $todayReservation;
			
		}elseif($loginInfo['User']['pay_plan'] == $const['E2']){
			$koma['present'] = 2 - $todayReservation;
		}elseif($loginInfo['User']['pay_plan'] == 'E3'){
			$koma['present'] = 3 - $todayReservation;
		}elseif($loginInfo['User']['pay_plan'] == 'E4'){
			$koma['present'] = 4 - $todayReservation;
		}elseif($loginInfo['User']['pay_plan'] == 'E5'){
			$koma['present'] = 5 - $todayReservation;
		}elseif($loginInfo['User']['pay_plan'] == 'E6'){
			$koma['present'] = 6 - $todayReservation;
		}elseif($loginInfo['User']['pay_plan'] == 'E7'){
			$koma['present'] = 7 - $todayReservation;							
		}elseif($loginInfo['User']['pay_plan'] == 'E8'){
			$koma['present'] = 8 - $todayReservation;	
		}
		
		$koma['use'] = sizeof($tacherConfirmSch);
		$koma['left'] = $koma['present'] - $koma['use'];
		
		//2012-01-22
		$point_cancel_no = $this->Lesson->find('count', array(		
			'fields' => 'Lesson.id',		
			'conditions' => array('Lesson.reservation_kinds' => 'P',		
			'Lesson.lesson_status' => 'C' ,	
			'Lesson.user_id'=>$user_id,
			'Lesson.teacher_id'=>$teacher_id,
			'Lesson.reserve_time'=>$temp_reserve_time )));

		//pr($point_cancel_no);
		
	    $this->set('koma', $koma);
	    
	    if($point_cancel_no > 0){
	    	$messages = "<FONT color='#FF0000'>같은 강사님 같은 시간을 포인트로 취소하신 후 큐빅으로는 예약하실 수 없습니다.</FONT>";
	    }
	}
	
	
	
  	
  	$is_contract_finished = 'N';
  	//2011-01-21 bug fixed from last day time setting all last day is 02:00:00
  	$last_day_year = substr($loginInfo['User']['last_day'],0,4);
  	$last_day_month = substr($loginInfo['User']['last_day'],5,2);
  	$last_day_day = substr($loginInfo['User']['last_day'],8,2);
  	
  	$temp_last_day = sprintf("%04d-%02d-%02d 02:00:00",$last_day_year,$last_day_month,$last_day_day);
  	if(strtotime($temp_last_day)- strtotime($temp_reserve_time) < 0){
  		$is_contract_finished = 'Y';
  	};
  	
  	//if it is more than 24:00
  	$start_time = sprintf("%04d-%02d-%02d 01:00:00",$year,$month,$day);
  	if($hour == '00'){
  		//more than 24 minus one day
  		$start_time =  date("Y-m-d H:i:s", strtotime($start_time)- 60*60*24);
  	}
  	
	
	$last_time =  date("Y-m-d H:i:s", strtotime($start_time)+60*60*25);
	
	$conditions['user_id'] = $loginInfo['User']['id'];
	$conditions['start_time'] = $start_time;
	$conditions['last_time'] = $last_time;
	
	$results= $this->ModelHandler->get_counts_class_reservation_day($conditions, 'Teacher');
	$reservation_count = $results[0][0]['counts'];
	
	if($loginInfo['User']['pay_plan'] == $const['E1']
	){
			$can_i_reserve_this_day = 1 - $reservation_count;
	}elseif($loginInfo['User']['pay_plan'] == $const['E2']
		){
			$can_i_reserve_this_day = 2 - $reservation_count;
	}elseif($loginInfo['User']['pay_plan'] == 'E3'
		){
			$can_i_reserve_this_day = 3 - $reservation_count;
	}elseif($loginInfo['User']['pay_plan'] == 'E4'
	){
			$can_i_reserve_this_day = 4 - $reservation_count;
	}elseif($loginInfo['User']['pay_plan'] == 'E5'
	){
			$can_i_reserve_this_day = 5 - $reservation_count;				
	}elseif($loginInfo['User']['pay_plan'] == 'E6'
	){
			$can_i_reserve_this_day = 6 - $reservation_count;				
	}elseif($loginInfo['User']['pay_plan'] == 'E7'
	){
			$can_i_reserve_this_day = 7 - $reservation_count;				
	}elseif($loginInfo['User']['pay_plan'] == 'E8'
	){
			$can_i_reserve_this_day = 8 - $reservation_count;				
	}else{
		$can_i_reserve_this_day = 99;	
	}
	
	//2012-04-15 START skype id check for new user
	if( $pay_plan == 'M'){
		$using_skype_id = false;
		// but if the user doesn't have mobile please add condition statement!
		if(isset($this->params['form']['class_in_use']) &&
				strtolower($this->params['form']['class_in_use']) === 'telephone'){
			$button_show = 'Y';
			// No Mobile Contact for Mobile User
			if($loginInfo['User']['hp'] == '' || $loginInfo['User']['hp'] == null){
				$messages .= "<SPAN class=sky_b>Please update your Mobile Contact.";
				$button_show = 'N';
			}
		}
		else if(isset($this->params['form']['class_in_use']) &&
				strtolower($this->params['form']['class_in_use']) === 'skype'){
			$button_show = 'Y';
			$using_skype_id = true;
		}
		else {
			$using_skype_id = true;
		}
		
		// No Skype Id for Skype User
		if($using_skype_id && ($skype_id == '' || $skype_id == null)){
			$messages .= "<SPAN class=sky_b>"."스카이프이름 (아이디)를 기입하시지 않으셨습니다.
				큐빅토크 홈페이지 내 정보수정에 정확한 스카이프아이디를 기입부탁드립니다.";
			$button_show = 'N';
		}
	}
	
	//2013-01-12 ADD
	if($lesson_already_reserved_count !=0 ){
		$button_show = 'N';
	}
	//2012-04-15 END
	
	//2010-06-14
	$this->set('point_cancel_no', $point_cancel_no);
	$this->set('can_i_reserve_this_day', $can_i_reserve_this_day);
	$this->set('is_contract_finished', $is_contract_finished);

	$this->set('button_show', $button_show);
	
	$this->set('reservation_kinds', $reservation_kinds);
		//エラーメッセージセット
    
	//someone reserved that classes
  	$this->set('lesson_already_reserved_count', $lesson_already_reserved_count);
	$this->set('messages', $messages);
	
	$this->set('payment_info', $this->params['form']['payment']);
}

//2012-04-29 START
function fix_schedule()
{
	global $const; 
	$this->set('const', $const);
	$loginInfo = $this->Session->read($const['loginInfo']);
	$time = $this->params['form']['time'];
	$teacher_id = $this->params['form']['teacher_id'];
	$user_id = $loginInfo['User']['id'];
	
	pr($time);
	pr($teacher_id);
	
	// 2012-05-06 start

	//$this->loadModel('Available_lesson');
	$this->loadModel('Lesson');
	$this->loadModel('Fixed_history');
    
 	$fixedstudent = null;
    
 	//check lesson status to prevent the double schedules
 	$teachers = $this->Teacher->find('all', array('conditions' => array(
	    							'Teacher.id' => $teacher_id 
	    							)));
	    							
	$teacher = $teachers[0];
 	//
 	$schedule_array = array();
 	$date_array = array();
 	
 	//temporily setting 20
 	$fixed_historys = $this->Fixed_history->find('all', array('conditions' => array(
	    							'Fixed_history.user_id' => $user_id), 
	    							'order' => array('Fixed_history.id DESC'),
 									'limit' => 1
	    							));

	$left_num = $fixed_historys[0]['Fixed_history']['left_num'];
 	$how_many_day = $left_num;
 	
 	$start_day =  date('Y-m-d',time());
 	
 	$temp_reserved_day  = $start_day;
 	
 	$date_string_korean = array(0=>'일요일',1=>'월요일',2=>'화요일',3=>'수요일',4=>'목요일',5=>'금요일',6=>'토요일');
 	
 	for($i=0; $i<$how_many_day;$i++){
 		$temp_reserved_time = $temp_reserved_day . " " .$time;
 		$temp_reserved_time_stamp = strtotime($temp_reserved_time);
 		
 		//0 sunday 6 is saturday
 		$date = date('w',$temp_reserved_time_stamp);
 		//pr($date);
 		
 		if( $date ==0 || $date == 6){
 			$temp_reserved_day =  date('Y-m-d',strtotime("+ 1 day", $temp_reserved_time_stamp));
 			$i--;
 			continue;
 		}		 
	    
 		$count = $this->Lesson->find('count', array('conditions' => array(
	    							
	    							'Lesson.reserve_time' => $temp_reserved_time 
	    							,'Lesson.teacher_id' => $teacher_id 
	    							,'Lesson.lesson_status' => array('F','R','A')
	    							)));
	   //Only this case can saved
	   if($count == 0){
	   							    		     
			array_push($schedule_array,$temp_reserved_time);
			array_push($date_array,$date_string_korean[$date]);
			
	   }else{
	   		$temp_reserved_day =  date('Y-m-d',strtotime("+ 1 day", $temp_reserved_time_stamp));
 			$i--;
 			continue;
	   }   			 							
	 	
	   $temp_reserved_day =  date('Y-m-d',strtotime("+ 1 day", $temp_reserved_time_stamp));
 	 }
   
 	 //pr($teacher);
 	 //pr($schedule_array);
 	 $this->set('teacher_id', $teacher_id);
 	 $this->set('time', $time);
 	 $this->set('teacher', $teacher);
 	 $this->set('schedule_array', $schedule_array);
 	 $this->set('date_array', $date_array);
 	 
}	
//2012-04-29 END

function fix_schedule_complete()
{
	global $const; 
	$this->set('const', $const);
	$loginInfo = $this->Session->read($const['loginInfo']);
	$time = $this->params['form']['time'];
	$teacher_id = $this->params['form']['teacher_id'];
	$user_id = $loginInfo['User']['id'];
	
	pr($loginInfo);
	pr($teacher_id);
	
	// 2012-05-06 start

	//$this->loadModel('Available_lesson');
	$this->loadModel('Lesson');
	$this->loadModel('Available_lesson');
	$this->loadModel('Fixed_history');
	
    
 	$fixedstudent = null;
    
 	//check lesson status to prevent the double schedules
 	$teachers = $this->Teacher->find('all', array('conditions' => array(
	    							'Teacher.id' => $teacher_id 
	    							)));
	    							
	$teacher = $teachers[0];
 	//
 	$schedule_array = array();
 	$date_array = array();
 	
 	//temporily setting 20
 	$fixed_historys = $this->Fixed_history->find('all', array('conditions' => array(
	    							'Fixed_history.user_id' => $user_id), 
	    							'order' => array('Fixed_history.id DESC'),
 									'limit' => 1
	    							));

	$left_num = $fixed_historys[0]['Fixed_history']['left_num'];
	$fixed_history_id = $fixed_historys[0]['Fixed_history']['id'];
	
 	$how_many_day = $left_num;
 	
 	$start_day =  date('Y-m-d',time());
 	
 	$temp_reserved_day  = $start_day;
 	
 	$date_string_korean = array(0=>'일요일',1=>'월요일',2=>'화요일',3=>'수요일',4=>'목요일',5=>'금요일',6=>'토요일');
 	
 	for($i=0; $i<$how_many_day;$i++){
 		$temp_reserved_time = $temp_reserved_day . " " .$time;
 		$temp_reserved_time_stamp = strtotime($temp_reserved_time);
 		
 		//0 sunday 6 is saturday
 		$date = date('w',$temp_reserved_time_stamp);
 		//pr($date);
 		
 		if( $date ==0 || $date == 6){
 			$temp_reserved_day =  date('Y-m-d',strtotime("+ 1 day", $temp_reserved_time_stamp));
 			$i--;
 			continue;
 		}		 
	    
 		$count = $this->Lesson->find('count', array('conditions' => array(
	    							
	    							'Lesson.reserve_time' => $temp_reserved_time 
	    							,'Lesson.teacher_id' => $teacher_id 
	    							,'Lesson.lesson_status' => array('F','R','A')
	    							)));
	   //Only this case can saved
	   if($count == 0){
	   	
	   		//Check available_lesson
		    			$id = $this->Available_lesson->find('first', array(
		    								'fields' => array('id'),
	    									'conditions' => array(
		    									'Available_lesson.available_time' => $temp_reserved_time 
		    									,'Available_lesson.teacher_id' => $teacher_id)
		    									));
		    			
		    			$Available_lesson_id = null;
		    			pr($id);
		    			if(sizeof($id) != 0){
		    				$Available_lesson_id = $id['Available_lesson']['id'];
		    			}
		    			
		    			pr($Available_lesson_id);
		    			
		    			$this->data['Available_lesson']['id'] =  $Available_lesson_id;
		    			$this->data['Available_lesson']['teacher_id'] =  $teacher_id;
						$this->data['Available_lesson']['available_time'] =  $temp_reserved_time;
						$this->data['Available_lesson']['reserve_status'] =  'F';
						
		    			if($Available_lesson_id == null){
		    				$this->Available_lesson->create();
		    			}
		    			
				        $this->Available_lesson->save($this->data);
	
				        //Lesson Table Insert
					    $this->data['Lesson']['user_id'] =  $user_id;
						$this->data['Lesson']['teacher_id'] = $teacher_id;
						if($Available_lesson_id == null){
							$this->data['Lesson']['available_lesson_id'] = $this->Available_lesson->getLastInsertID();	
						}else{
							$this->data['Lesson']['available_lesson_id'] = $Available_lesson_id;	
						}
						$this->data['Lesson']['lesson_status'] = 'R';
						$this->data['Lesson']['spend_point'] = $teacher['Teacher']['tea_point'];
						$this->data['Lesson']['reserve_time'] = $temp_reserved_time;
						$this->data['Lesson']['evaluate_status'] = 'N';
						$this->data['Lesson']['leveltest'] = 'N';
						$this->data['Lesson']['reservation_kinds'] = 'M';
						
						$this->Lesson->create();       
				        $this->Lesson->save($this->data);
	   							    		     
			array_push($schedule_array,$temp_reserved_time);
			array_push($date_array,$date_string_korean[$date]);
			
	   }else{
	   		$temp_reserved_day =  date('Y-m-d',strtotime("+ 1 day", $temp_reserved_time_stamp));
 			$i--;
 			continue;
	   }   			 							
	 	
	   $temp_reserved_day =  date('Y-m-d',strtotime("+ 1 day", $temp_reserved_time_stamp));
 	 }
   
 	 
 	 //2012-05-13
	 $this->data['Fixed_history']['left_num'] = 0;
	 $this->data['Fixed_history']['action'] = 'R';
	 $this->Fixed_history->save($this->data);
						
 	 $this->set('teacher_id', $teacher_id);
 	 $this->set('time', $time);
 	 $this->set('teacher', $teacher);
 	 $this->set('schedule_array', $schedule_array);
 	 $this->set('date_array', $date_array);
 	 
}	
//2012-04-29 END

//
function fix_schedule_control()
{
	global $const; 
	$this->set('const', $const);
	$loginInfo = $this->Session->read($const['loginInfo']);
	$time = $this->params['form']['time'];
	$teacher_id = $this->params['form']['teacher_id'];
	$user_id = $loginInfo['User']['id'];
	
	pr($loginInfo);
	pr($teacher_id);
	
	// 2012-05-06 start

	//$this->loadModel('Available_lesson');
	$this->loadModel('Lesson');
	$this->loadModel('Available_lesson');
	$this->loadModel('Fixed_history');
	
    
 	$fixedstudent = null;
    
 	//check lesson status to prevent the double schedules
 	$teachers = $this->Teacher->find('all', array('conditions' => array(
	    							'Teacher.id' => $teacher_id 
	    							)));
	    							
	$teacher = $teachers[0];
 	//
 	$schedule_array = array();
 	$date_array = array();
 	
 	//temporily setting 20
 	$fixed_historys = $this->Fixed_history->find('all', array('conditions' => array(
	    							'Fixed_history.user_id' => $user_id), 
	    							'order' => array('Fixed_history.id DESC'),
 									'limit' => 1
	    							));

	$left_num = $fixed_historys[0]['Fixed_history']['left_num'];
	$fixed_history_id = $fixed_historys[0]['Fixed_history']['id'];
	
 	$how_many_day = $left_num;
 	
 	$start_day =  date('Y-m-d',time());
 	
 	$temp_reserved_day  = $start_day;
 	
 	$date_string_korean = array(0=>'일요일',1=>'월요일',2=>'화요일',3=>'수요일',4=>'목요일',5=>'금요일',6=>'토요일');
 	
 	for($i=0; $i<$how_many_day;$i++){
 		$temp_reserved_time = $temp_reserved_day . " " .$time;
 		$temp_reserved_time_stamp = strtotime($temp_reserved_time);
 		
 		//0 sunday 6 is saturday
 		$date = date('w',$temp_reserved_time_stamp);
 		//pr($date);
 		
 		if( $date ==0 || $date == 6){
 			$temp_reserved_day =  date('Y-m-d',strtotime("+ 1 day", $temp_reserved_time_stamp));
 			$i--;
 			continue;
 		}		 
	    
 		$count = $this->Lesson->find('count', array('conditions' => array(
	    							
	    							'Lesson.reserve_time' => $temp_reserved_time 
	    							,'Lesson.teacher_id' => $teacher_id 
	    							,'Lesson.lesson_status' => array('F','R','A')
	    							)));
	   //Only this case can saved
	   if($count == 0){
	   	
	   		//Check available_lesson
		    			$id = $this->Available_lesson->find('first', array(
		    								'fields' => array('id'),
	    									'conditions' => array(
		    									'Available_lesson.available_time' => $temp_reserved_time 
		    									,'Available_lesson.teacher_id' => $teacher_id)
		    									));
		    			
		    			$Available_lesson_id = null;
		    			pr($id);
		    			if(sizeof($id) != 0){
		    				$Available_lesson_id = $id['Available_lesson']['id'];
		    			}
		    			
		    			pr($Available_lesson_id);
		    			
		    			$this->data['Available_lesson']['id'] =  $Available_lesson_id;
		    			$this->data['Available_lesson']['teacher_id'] =  $teacher_id;
						$this->data['Available_lesson']['available_time'] =  $temp_reserved_time;
						$this->data['Available_lesson']['reserve_status'] =  'F';
						
		    			if($Available_lesson_id == null){
		    				$this->Available_lesson->create();
		    			}
		    			
				        $this->Available_lesson->save($this->data);
	
				        //Lesson Table Insert
					    $this->data['Lesson']['user_id'] =  $user_id;
						$this->data['Lesson']['teacher_id'] = $teacher_id;
						if($Available_lesson_id == null){
							$this->data['Lesson']['available_lesson_id'] = $this->Available_lesson->getLastInsertID();	
						}else{
							$this->data['Lesson']['available_lesson_id'] = $Available_lesson_id;	
						}
						$this->data['Lesson']['lesson_status'] = 'R';
						$this->data['Lesson']['spend_point'] = $teacher['Teacher']['tea_point'];
						$this->data['Lesson']['reserve_time'] = $temp_reserved_time;
						$this->data['Lesson']['evaluate_status'] = 'N';
						$this->data['Lesson']['leveltest'] = 'N';
						$this->data['Lesson']['reservation_kinds'] = 'M';
						
						$this->Lesson->create();       
				        $this->Lesson->save($this->data);
	   							    		     
			array_push($schedule_array,$temp_reserved_time);
			array_push($date_array,$date_string_korean[$date]);
			
	   }else{
	   		$temp_reserved_day =  date('Y-m-d',strtotime("+ 1 day", $temp_reserved_time_stamp));
 			$i--;
 			continue;
	   }   			 							
	 	
	   $temp_reserved_day =  date('Y-m-d',strtotime("+ 1 day", $temp_reserved_time_stamp));
 	 }
   
 	 
 	 //2012-05-13
	 $this->data['Fixed_history']['left_num'] = 0;
	 $this->data['Fixed_history']['action'] = 'R';
	 $this->Fixed_history->save($this->data);
						
 	 $this->set('teacher_id', $teacher_id);
 	 $this->set('time', $time);
 	 $this->set('teacher', $teacher);
 	 $this->set('schedule_array', $schedule_array);
 	 $this->set('date_array', $date_array);
 	 
}	
//2012-04-29 END

function checkSession(){
        //if (!$this->Session->check('loginInfo')){
            //$this->redirect('/users/login');
            //print_r($this->Session->check('loginInfo'));
        //}
    }
}

//共通関数
function availableLesson($year, $month, $day, $teacher, &$teacherSch,$tacherConfirmSch,$fix_date,$fix_time,$_fixedSched,$_dateReserved,$fix_kinds) {
   $teacher_path = c_path();
   //先生可能な日付と時間が合う場合は、openでいるボタンを表示する
   //先生の予約がない場合、エラーにならないするために宣言する
   $lesson_sub_day= array();
   $is_before_click_conunt = 0;
   
   //先生スケーズルを求める
   foreach($teacher['Available_lesson'] as $lesson){
   	$lesson_time[] =  $lesson['available_time'];
   	$avilable_time16 = substr($lesson['available_time'],0,16);
   	$lesson_sub_day[] =	$avilable_time16;
    //ステータスも保存する
    //0:予約未完了 1:予約完了
    $lesson_status[$avilable_time16] = $lesson['reserve_status'];
   }
  //print_r($lesson_status); 
  $fomatted_month = sprintf('%02d',$month);
   
  //月末
  $l_day = date("j", mktime(0, 0, 0, $month + 1, 0, $year));
  
  //PRE, NEXTボタンの処理のための日付を設定
  $pre_date = mktime(0, 0, 0, $month  , intVal($day)-7, $year);
  $next_date = mktime(0, 0, 0, $month  , intVal($day)+7, $year);
  $pre_sent_date = sprintf("%d%02d%02d",date("Y", $pre_date), date("n",  $pre_date) , date("j", $pre_date));
  $next_sent_date = sprintf("%d%02d%02d",date("Y", $next_date), date("n",  $next_date) , date("j", $next_date));

  $tmp = <<<EOM
  <TABLE width="100%;" border="0" align="center" cellpadding="3" cellspacing="1" bgcolor="#CCCCCC">
  <COL width="40" span="7">
  	<TBODY>
  	<TR>
    	<TD height="20" colspan="10" background="/cubictalkko_mod/img/bg_gra_orange.jpg" bgcolor="#F9F9F9">
    	<TABLE width="100%;" border="0" cellpadding="0" cellspacing="0">
                      <TBODY><TR>
                        <TD><DIV align="left"><A href="javascript:day_click({$pre_sent_date});"><IMG src="/cubictalkko_mod/img/arrow03_l.gif" alt="전달" border="0"></A></DIV></TD>
                        <TD><DIV align="center"><SPAN style="font-size:15px"><STRONG>
                        

  {$year}년{$fomatted_month}월

                       
                        </STRONG></SPAN></DIV></TD>
                        <TD><DIV align="right"><A href="javascript:day_click({$next_sent_date});"><IMG src="/cubictalkko_mod/img/arrow03_r.gif" alt="다음달" border="0"></A></DIV></TD>
                      </TR>
                    </TBODY>
         </TABLE>
    	 </TD>
    </TR>
EOM;

  //変数宣言
  $date = array('(일)','(월)','(화)','(수)','(목)','(금)','(토)');
  $color = array('0'=>'#F5FAFA','1'=>'#FFFFFF','2'=>'#FFFFFF','3'=>'#FFFFFF','4'=>'#FFFFFF','5'=>'#FFFFFF','6'=>'#FFF4F9');     //土日はカラ変更
  
  //for ($i = $day; $i < $l_day + 1;$i++) {
  $tmp .= "<TR>";
  $tmp .= "<TD width=\"3%;\" height=\"20\" nowrap=\"\" bgcolor=\"#F0F0F0\"><DIV align=\"center\"></DIV></TD>";
  
  //
  $ct = get_time();
  //2010-03-21 YHLEE START
  //2hours ahead
  
  //2010-03-21 YHLEE END //2010-07-01 revised
  // Based 30 minutes revised 2015-05-13 14:41
  // TIME LAPSED it will close the time after 10 minutes left;
  $currentTimeNoHipon = change_date_format(date("Y-m-d H:i:s", time()+ (10*60*1)),'NOHIPON');
  
  for ($i = $day; $i < $day+7;$i++) {
  	//最初$day変数が"0"が入ってるため数字変換した。
  	$i=intval($i);
  	
  	//曜日の取得
    $week = date("w", mktime(0, 0, 0, $month, $i, $year));
    $week_array[] = $week;
    
    //入力された日付から一週間
    //31超える場合は32、33ように表示されるから
  	$express_day = date("j", mktime(0, 0, 0, $month, $i, $year));
    $tmp .= "<TD width=\"3%;\" height=\"20\" nowrap=\"\" bgcolor=\"#F0F0F0\">
      <DIV align=\"center\"><STRONG>{$express_day}日{$date[$week]}</STRONG></DIV>
      </TD>";
  }
  
  $tmp .= " <TD width=\"7%;\" height=\"20\" nowrap=\"\" bgcolor=\"#F0F0F0\"><DIV align=\"center\"><STRONG>고정날짜</STRONG></DIV> </TD> ";
  // column days
  $tmp .= " <TD width=\"1%;\" height=\"20\" nowrap=\"\" bgcolor=\"#F0F0F0\"><DIV align=\"center\"><STRONG>수업종류</STRONG></DIV> </TD> "; 
  
  $tmp .= "</TR>\n";			//trを閉じる
  
  //24時間を30分単位で表示する
  for($hour=6;$hour<26;$hour++){
  	$formatted_hours = sprintf('%02d',$hour);
  	
  //color編集
  	if($hour<6){
  		$time_color = '#F9F9F9';	
  	}elseif($hour<12){
  		$time_color = "#E7FAFE";
  	}elseif($hour<18){
  		$time_color = "#FFFAD7";
  	}else{
  		$time_color = "#FFDCB9";
  	}
  	
  	for($minutes = 0; $minutes<60;$minutes+=30){
  		mktime($hour, $minutes, $month, $i, $year);
  		
  		$formatted_minutes = sprintf('%02d',$minutes);
  		$avilable_time = $formatted_hours.':'.$formatted_minutes;
  		if ($avilable_time != '25:30') {
  		$tmp .= "<TR><TD height=\"20\" bgcolor={$time_color}><DIV align=\"center\">{$avilable_time}</DIV></TD>";
  		
  		//空いている空間表示
	  	for($i=0;$i<7;$i++){
	  		
	  	//先生可能な日付と時間が合う場合は、openでいるボタンを表示する
		//日付が違う場合は時間を比較する必要なし
		//2010-01-28 Modified START
		$cday = date("d", mktime(0, 0, 0, $month, $day+$i, $year));
		$cmonth = date("m", mktime(0, 0, 0, $month, $day+$i, $year));
		$cyear = date("Y", mktime(0, 0, 0, $month, $day+$i, $year));
		
		$compere_day = ($cyear."-".sprintf('%02d',$cmonth)."-".sprintf('%02d',$cday)." ".$avilable_time);
// 		if($formatted_hours >= 24 && $formatted_hours < 25){
// 			$overYear = date("Y", mktime(0, 0, 0, $month, $day+$i, $year)+ 60*60*24*1);
// 			$overMonth = date("m", mktime(0, 0, 0, $month, $day+$i, $year)+ 60*60*24*1);
// 			$overDay = date("d", mktime(0, 0, 0, $month, $day+$i, $year)+ 60*60*24*1);
// 			// 24 hours
// 			// greater than 24hours will turn to 01-06 hours
// 			$over_hours = sprintf('%02d',($formatted_hours-24));
// 			$javascript_day = ($overYear.sprintf('%02d',$overMonth).sprintf('%02d',$overDay).$over_hours.$formatted_minutes);
// 			$avilable_time = $over_hours.':'.$formatted_minutes;
// 			$compere_day = ($overYear."-".sprintf('%02d',$overMonth)."-".sprintf('%02d',$overDay)." ".$avilable_time);
// 		}elseif($formatted_hours >= 25 && $formatted_hours < 26){
// 			// 24 hours
// 			// greater than 24hours will turn to 01-06 hours
// 			$over_hours = sprintf('%02d',($formatted_hours-24));
// 			$javascript_day = ($cyear.sprintf('%02d',$cmonth).sprintf('%02d',$cday).$over_hours.$formatted_minutes);
// 			$avilable_time = $over_hours.':'.$formatted_minutes;
// 			$compere_day = ($cyear."-".sprintf('%02d',$cmonth)."-".sprintf('%02d',$cday)." ".$avilable_time);
// 		}else{
// 			$javascript_day = ($cyear.sprintf('%02d',$cmonth).sprintf('%02d',$cday).$formatted_hours.$formatted_minutes);
// 		}
		// 
		if($formatted_hours >= 24){
			$overYear = date("Y", mktime(0, 0, 0, $month, $day+$i, $year)+ 60*60*24*1);
			$overMonth = date("m", mktime(0, 0, 0, $month, $day+$i, $year)+ 60*60*24*1);
			$overDay = date("d", mktime(0, 0, 0, $month, $day+$i, $year)+ 60*60*24*1);
			// 24 hours
			// greater than 24hours will turn to 01-06 hours
			$over_hours = sprintf('%02d',($formatted_hours-24));
			$javascript_day = ($overYear.sprintf('%02d',$overMonth).sprintf('%02d',$overDay).$over_hours.$formatted_minutes);
			$avilable_time = $over_hours.':'.$formatted_minutes;
			$compere_day = ($overYear."-".sprintf('%02d',$overMonth)."-".sprintf('%02d',$overDay)." ".$avilable_time);
		}else{
			$javascript_day = ($cyear.sprintf('%02d',$cmonth).sprintf('%02d',$cday).$formatted_hours.$formatted_minutes);
		}
		//$compere_day = ($year."-".sprintf('%02d',$month)."-".sprintf('%02d',$day+$i)." ".$avilable_time);
		//$javascript_day = ($year.sprintf('%02d',$month).sprintf('%02d',$day+$i).$formatted_hours.$formatted_minutes);
  	 	//2010-01-28 Modified END
  	 	
		$is_same_day = 0;			     //講師の予約が入ってるが判断するフラグ
		$stopatone = 0;
		//カレンダと先生が予約した日付を比較して同じだったら「１」違う場合「０」
		foreach($lesson_sub_day as $sub_day){
  	 		if (!strcasecmp($sub_day,$compere_day)){
   				$is_same_day = 1;
   				$is_same_day_status = $lesson_status[$sub_day];
   			}
  	 	}
	  		
  	 		if($is_same_day == 0){
  	 			$tmp .= "<TD height=\"20\" bgcolor={$color[$week_array[$i]]}><DIV align=\"center\"></DIV></TD>";
	  		}elseif($is_same_day == 1){
	  			//もう予約が終わったものに関してはCLOSEアイコンを表示する
	  			//print_r($sub_day);
	  			if($is_same_day_status == 'F' || ($currentTimeNoHipon > $javascript_day)){
	  				if (in_array(date('Y-m-d H:i:s', strtotime($compere_day)), $_dateReserved)) {
	  					// $tmp .= "<TD height=\"20\" bgcolor={$color[$week_array[$i]]}><DIV align=\"center\"><IMG name=\"img_".$javascript_day."\" src=\"/cubictalkko_mod/img/ico_mine.png\" alt=\"\" border=\"0\"></DIV></TD>";
	  					$tmp .= "<TD height=\"20\" bgcolor={$color[$week_array[$i]]}><div class=\"div-container\"><DIV align=\"center\"><span class=\"btn-status-sched bg-colored-green\">MINE</span></DIV></div></TD>";
	  				} else {
	  					// $tmp .= "<TD height=\"20\" bgcolor={$color[$week_array[$i]]}><DIV align=\"center\"><IMG name=\"img_".$javascript_day."\" src=\"/cubictalkko_mod/img/ico_close.gif\" alt=\"\" border=\"0\"></DIV></TD>";
	  					$tmp .= "<TD height=\"20\" bgcolor={$color[$week_array[$i]]}><div class=\"div-container\"><DIV align=\"center\"><span class=\"btn-status-sched bg-colored-black\">CLOSE</span></DIV></div></TD>";
	  				}
	  				$tmp .= "<INPUT type=\"hidden\" name=\"sch_".$javascript_day."\" value=\"0\">";
	  			}else{
		  			$tmp .= "<TD height=\"20\" bgcolor={$color[$week_array[$i]]}><DIV align=\"center\">";
		  			
		  			//予約して予約確認画面からもどった場合はCLOSEで表示する Start
		  			$is_before_click = false;
		  		
		  			if($is_before_click == true){
		  				// $tmp .= " <A href=\"javascript:change_sts({$javascript_day});\"><IMG name=\"img_".$javascript_day."\" src=\"/cubictalkko_mod/img/ico_open2.gif\" alt=\"{$teacher['Teacher']['tea_point']}ポイント\" border=\"0\"></A>";
		  				$tmp .= " <A id=\"btn_status_sched_{$javascript_day}\" class=\"btn-status-sched bg-colored-blue\" href=\"javascript:change_sts({$javascript_day});\">OPEN</A>";
		  				$tmp .= "<INPUT type=\"hidden\" name=\"sch_".$javascript_day."\" value=\"2\">";
		  				$is_before_click_conunt++;
		  			}else{
		  				// $tmp .= " <A href=\"javascript:change_sts({$javascript_day});\"><IMG name=\"img_".$javascript_day."\" src=\"/cubictalkko_mod/img/ico_open.gif\" alt=\"{$teacher['Teacher']['tea_point']}ポイント\" border=\"0\"></A>";
		  				$tmp .= " <A id=\"btn_status_sched_{$javascript_day}\" class=\"btn-status-sched bg-colored-orange\" href=\"javascript:change_sts({$javascript_day});\">OPEN</A>";
		  				$tmp .= "<INPUT type=\"hidden\" name=\"sch_".$javascript_day."\" value=\"1\">";
		  			}
		  			$tmp .= "</DIV></TD>";
		  			
		  			//End
		  			
		  			//授業がある日を配列に入れる
		  			//$teacherSch[] = $javascript_day;
	  			}
	  		}
	  	}
	  	//pr($avilable_time);
	  	$is_fixed		 = in_array($avilable_time , $fix_time);
	  	$keyName		 = "";
	  	$keyDays		 = "";
	  	$keyImg			 = "";
	  	$keyImgTooltip	 = "";
	  	if($is_fixed == true){
	  		$key = array_search($avilable_time, $fix_time);
	  		$keyName		 = "";
	  		$keyImg			 = "";
	  		$keyImgTooltip	 = "";
	  		if ( isset($fix_kinds[date('Y-m-d H:i', strtotime($fix_date[$key]." ".$avilable_time))]["img"]) && strlen($fix_kinds[date('Y-m-d H:i', strtotime($fix_date[$key]." ".$avilable_time))]["img"]) > 0 ) {
//old  			$keyName = $fix_kinds[date('Y-m-d H:i', strtotime($fix_date[$key]." ".$avilable_time))];
	  			$keyDays = $fix_kinds[date('Y-m-d H:i', strtotime($fix_date[$key]." ".$avilable_time))]["img"];
	  			$keyImgTooltip = $fix_kinds[date('Y-m-d H:i', strtotime($fix_date[$key]." ".$avilable_time))]["tooltip"];
	  			$keyImg	 = "<div id = \"tooltipPlug\" ><div><img src = \"{$teacher_path}/image/payPlanDays/sched{$keyDays}.png\" border = \"0\"></div> $keyImgTooltip</div>";
	  		}
	  		$tmp .= " <TD height=\"20\" bgcolor={$time_color}><DIV align=\"center\">{$fix_date[$key]}<br />{$keyName}</DIV></td>\n";	
	  	}else{
	  		if (isset($_fixedSched[$avilable_time.':00'])) {
//	  			$time_aaa = $_fixedSched[$avilable_time.':00'];
	  		$tmp .= " <TD height=\"20\" bgcolor={$time_color}><DIV align=\"center\"><A href='javascript:go_fix_schedule(\"{$avilable_time}\");'>고정가능</A></DIV></td>\n";
	  		} else {
	  		$tmp .= " <TD height=\"20\" bgcolor={$time_color}><DIV align=\"center\"></DIV></td>\n";
	  		}
	  	}
	  	// column days
  		$tmp .= " <TD width=\"1%;\" height=\"20\" nowrap=\"\" bgcolor=\"#F0F0F0\"><DIV align=\"center\"><STRONG>{$keyImg}</STRONG></DIV> </TD> "; 
	  	$tmp .= "</TR>";
  	 } // 25:30 will not display
  	} // minutes
  } // hour
 //for ($i = $day; $i < $l_day + 1;$i++) {
  $tmp .= "<TR>";
  $tmp .= "<TD width=\"3%;\" height=\"20\" nowrap=\"\" bgcolor=\"#F0F0F0\"><DIV align=\"center\"></DIV></TD>";
  for ($i = $day; $i < $day+7;$i++) {
  	$i = intval($i);
    //曜日の取得
    $week = date("w", mktime(0, 0, 0, $month, $i, $year));
    $week_array[] = $week;
    
    //入力された日付から一週間
    //31超える場合は32、33ように表示されるから
  	$express_day = date("j", mktime(0, 0, 0, $month, $i, $year));
      $tmp .= "<TD width=\"4%;\" height=\"20\" nowrap=\"\" bgcolor=\"#F0F0F0\">
      <DIV align=\"center\"><STRONG>{$express_day}日{$date[$week]}</STRONG>
      </DIV></TD>";
  }
  
  $tmp .= " <TD width=\"7%;\" height=\"20\" nowrap=\"\" bgcolor=\"#F0F0F0\"><DIV align=\"center\"><STRONG>고정날짜</STRONG></DIV> </TD> "; 
  // column days
  $tmp .= " <TD width=\"1%;\" height=\"20\" nowrap=\"\" bgcolor=\"#F0F0F0\"><DIV align=\"center\"><STRONG>수업종류</STRONG></DIV> </TD> "; 
  $tmp .= "  </TR>\n";			//trを閉じる
   
  $tmp .= <<<EOM
  	  	<TR>
    	<TD height="20" colspan="10" background="/cubictalkko_mod/img/bg_gra_orange.jpg" bgcolor="#F9F9F9">
    	<TABLE width="100%;" border="0" cellpadding="0" cellspacing="0">
                      <TBODY><TR>
                        <TD><DIV align="left"><A href="javascript:day_click({$pre_sent_date});"><IMG src="/cubictalkko_mod/img/arrow03_l.gif" alt="전달" border="0"></A></DIV></TD>
                        <TD><DIV align="center"><SPAN style="font-size:15px"><STRONG>{$year}년{$fomatted_month}월</STRONG></SPAN></DIV></TD>
                        <TD><DIV align="right"><A href="javascript:day_click({$next_sent_date});"><IMG src="/cubictalkko_mod/img/arrow03_r.gif" alt="다음달" border="0"></A></DIV></TD>
                      </TR>
                    </TBODY>
         </TABLE>
    	 </TD>
    </TR>
EOM;

  $tmp .= "</TBODY></TABLE>";
  $tmp .="<INPUT type=\"hidden\" name=\"rsv_cnt\" value={$is_before_click_conunt}>";
  
  return $tmp;
}

//先生から予約した時のカレンダ表示
function reserve_calendar($year, $month, $day, $teacher_id) {
  //月末
  $l_day = date("j", mktime(0, 0, 0, $month + 1, 0, $year));
  $fomatted_month = sprintf('%02d',$month);
  //月末分繰り返す
  $formatted_ym = $year.$fomatted_month;
  
  //12月からつぎへボタンを押すと年も変わる理由で年度もdate関数から処理
  $formatted_ym_b = date("Y", mktime(0, 0, 0, $month , 0, $year)).  sprintf('%02d',date("n", mktime(0, 0, 0, $month , 0, $year)))."01";
  $formatted_ym_a = date("Y", mktime(0, 0, 0, $month+2 , 0, $year)).  sprintf('%02d',date("n", mktime(0, 0, 0, $month+2 , 0, $year)))."01";
  
  //
  
  //初期出力
  $tmp = <<<EOM
<TABLE width="100%;" border="0" align="center" cellpadding="3" cellspacing="1" bgcolor="#CCCCCC">
                <COL width="40" span="7">
                <TBODY><TR>
                  <TD height="20" colspan="8" background="/cubictalkko_mod/img/bg_gra_orange.jpg" bgcolor="#F9F9F9">
                  <TABLE width="100%;" border="0" cellpadding="0" cellspacing="0">
                      <TBODY><TR>
                        <TD><DIV align="left">
EOM;
  
 //1番目の引数は先生ID、二番目の引数はBEFOREボタン          
  $tmp .="<A href=\"/cubictalkko_mod/teachers/reserve/{$teacher_id}/{$formatted_ym_b} \"><IMG 
                              border=0 alt=전달 src=\"/cubictalkko_mod/img/arrow03_l.gif\"></A>";
  $tmp .= <<<EOM
                                
                                </DIV></TD>
                                <TD>
                                <DIV align=center><SPAN 
                                style="FONT-SIZE: 15px"><STRONG>
EOM;
                                
 $tmp .=$year."년".$fomatted_month."월";
 
 $tmp .= <<<EOM
</STRONG></SPAN></DIV></TD>
                                <TD>
                                <DIV align=right>
EOM;
//1番目の引数は先生ID、二番目の引数はNEXTボタン   
$tmp .="<A href=\"/cubictalkko_mod/teachers/reserve/{$teacher_id}/{$formatted_ym_a} \"><IMG 
                                border=0 alt=다음달
                                src=\"/cubictalkko_mod/img/arrow03_r.gif\"></A>";
 $tmp .= <<<EOM
                                
                                </DIV></TD></TR></TBODY></TABLE></TD></TR>
                                
                            <TR>
                            <TD width="12%;" bgColor=#f5f5f5>
                              <DIV align=center></DIV></TD>
                            <TD width="12%;" bgColor=#f5f5f5>
                              <DIV align=center>일</DIV></TD>
                            <TD width="12%;" bgColor=#f5f5f5>
                              <DIV align=center>월</DIV></TD>
                            <TD width="12%;" bgColor=#f5f5f5>
                              <DIV align=center>화</DIV></TD>
                            <TD width="12%;" bgColor=#f5f5f5>
                              <DIV align=center>수</DIV></TD>
                            <TD width="12%;" bgColor=#f5f5f5>
                              <DIV align=center>목</DIV></TD>
                            <TD width="12%;" bgColor=#f5f5f5>
                              <DIV align=center>금</DIV></TD>
                            <TD width="12%;" bgColor=#f5f5f5>
                              <DIV align=center>토</DIV></TD></TR>
EOM;
  
  //$loginInfo = $session->read('loginInfo');
  
  for ($i = 1; $i < $l_day + 1;$i++) {
  	$formatted_day = sprintf('%02d',$i);
  	$formatted_ymd = $formatted_ym.$formatted_day;
	
  	//色判定
  	$bgcolor[0] ="#FFFF99";
  	$bgcolor[1] ="#fff4f9";
  	$bgcolor[2] ="#f5fafa";
  	$bgcolor[3] ="#ffffff";
  	
    //曜日の取得
    $week = date("w", mktime(0, 0, 0, $month, $i, $year));
    //曜日が日曜日の場合
    if ($week == 0) {
      $tmp .= "  <TR>\n";
      $tmp .="<TD bgcolor=\"#FFFFFF\">
  				<DIV align=\"center\">
  				<IMG src=\"/cubictalkko_mod/img/arrow03.gif\" alt=\" this week\" width=\"17\" height=\"17\" border=\"0\">
  				</DIV>
  				</TD>";
    }
    //1日の場合
    if ($i == 1) {
      if($week != 0){
      	 $tmp .="<TD bgcolor=\"#FFFFFF\">
  				<DIV align=\"center\">
  				<IMG src=\"/cubictalkko_mod/img/arrow03.gif\" alt=\" this week\" width=\"17\" height=\"17\" border=\"0\">
  				</DIV>
  				</TD>";
      }
      $tmp .= str_repeat("    <td  bgColor=#fff4f9>&nbsp;</td>\n", $week);
    }
    //クリックした日が黄色に表示される
    
    //if you click the day , it change the color to yellows
    //if ($i == $day && $year == date("Y") && $month == date("n")) {
    if ($i == $day) {
      //現在の日付の場合
      $tmp .= "    <td bgColor={$bgcolor[0]}><DIV align=\"center\"><A href=\"javascript:day_click({$formatted_ymd});\">{$i}</A></DIV></td>\n";
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
  
  //$test = $ajax->link('View Post',array( 'controller' => 'Teachers', 'action' => 'update_lesson' ),array( 'update' => 'update_lesson'));
  return $tmp;
}

function chage_time_db_format($tmp_time) {
    //2010-06-14 how many class user reserved in reserve time start
	$year = substr($tmp_time,0,4);
  	$month = substr($tmp_time,4,2);
  	$day = substr($tmp_time,6,2);
  	$hour = substr($tmp_time,8,2);
  	$minites = substr($tmp_time,10,2);
  	
  	//compare last day
  	$temp_result_time = sprintf("%04d-%02d-%02d %02d:%02d:00",$year,$month,$day,$hour,$minites);
  	
  	return $temp_result_time;
  	
} 	