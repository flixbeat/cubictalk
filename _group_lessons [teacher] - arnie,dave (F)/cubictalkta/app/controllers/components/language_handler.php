<?php
class LanguageHandlerComponent extends Object
{
    /**
     * component statup
     */
	var $_const = null;
	var $_loginInfo;
    function startup(&$controller) {
        $this->controller=&$controller;
		global $const; 
		$this->controller->set('const', $const);
		$this->_const = $const;
		//loginInfo information everytime start
		$this->_loginInfo = $this->checkLoginInfo();
    }//startup()
	
	//session login 情報チェック
	function checkLoginInfo() {
		$loginInfo = $this->controller->Session->read('loginInfo');
	    if( $loginInfo == null){
			$this->controller->redirect(b_path()."/bbs/mypage.php?page=mypage");
		}
		return $loginInfo;
	}
	
    //hidden value 情報チェック
	function checkValidAccess() {
		if($this->controller->Session->read('loginInfo')== null){
			$this->controller->redirect(b_path()."/bbs/mypage.php?page=mypage");
		}
		
		if($this->checkFormElement($this->_const['validAccess'])== false){
			$this->controller->redirect(c_path()."/errors");
		}
	}
	
   //hidden value 情報チェック
	function checkFormElement($element) {
		$result = false;
		foreach($this->controller->params['form'] as $key=>$value){
			if($key == $element){
				$result = true;
				break;
			}
		}
		return $result;
	}
	
	//Check Using point or free palan
	function checkReservationWithPoint() {
		$user_point = null;
		$pay_plan = $this->_loginInfo['User']['pay_plan'];
		
		if($pay_plan ==$this->_const['E1'] || $pay_plan ==$this->_const['E2']
			||  $pay_plan == 'E3' ||  $pay_plan == 'E4'
		  ){
			//get a User point
			$user_point = $this->controller->ModelHandler->getMbPoint($this->_loginInfo['User']['mb_id'],'Teacher'); 
		}  
		
		return $user_point;
	}
	
	//2012-05-23 START
	function check_time_diff($time ,$diff) {
		
		$result = 'Y';
		
		$time_diff = (time()+ 60*60*$diff) -(strtotime($lesson_model['reserve_time']) );
		
		//
		if($time_diff < 0 ){
			$result = 'Y';
		}else{
			$result = 'N';
		}
		
		return $result;
	}
	
	function set_langauge ($_lang = null) {
		$_text = array();
		if ($_lang == 'en') {
			// Title name
			$_text['SEARCH']			 = 'Search';
			$_text['TODAY']				 = 'Today';
			$_text['STUDENT']			 = 'Student';
			$_text['TEACHER']			 = 'Teacher';
			$_text['RESERVE_TIME']		 = 'Reserve Time';
			$_text['STATUS']			 = 'Status';
			$_text['-']					 = '-----'; // extended class [button]
			$_text['REASON']			 = 'Reason';
			$_text['COMMENT']			 = 'Comment';
			$_text['READ']				 = 'read';
			
			// MENU NAVIGATION
			$_text['EXTEND THE CLASS']			 = 'Extend The Class';
			$_text['HISTORY']					 = 'History';
			
			$_text['CREATED']					 = 'CREATED';
			$_text['RESPONDED']					 = 'RESPONDED';
			$_text['NO LESSON BASIS']			 = 'NO LESSON BASIS';
			$_text['HISTORY NOT LESSON BASIS']	 = 'HISTORY NOT LESSON BASIS';
			
			// Extension[s]
			$_text['KINDS']			 = 'KINDS';
			$_text['REASON']		 = 'REASON';
			$_text['DESCRIPTION']	 = 'DESCRIPTION';
			$_text['EXTEND DAY']	 = 'EXTEND DAY';
			
			// submit cancel or close
			$_text['SUBMIT']	 = 'Submit';
			$_text['CANCEL']	 = 'Cancel';
			$_text['CLOSE']		 = 'Close';
			$_text['DATE POST']	 = 'Date post';
			$_text['MODIFIED']	 = 'Modified';
			
			// Reservation Kinds
			$_text['Status']['R'] = 'Reserve';
			$_text['Status']['A'] = 'A';
			$_text['Status']['F'] = 'Finish';
			$_text['Status']['E'] = 'Extend';
			$_text['Status']['M'] = 'M';
			
			// Reservation Kinds
			$_text['Kinds']['K']	 = 'K';
			$_text['Kinds']['P']	 = 'P';
			$_text['Kinds']['M']	 = 'FIX';
			$_text['Kinds']['M2']	 = '2DAYS FIX';
			$_text['Kinds']['M3']	 = '3DAYS FIX';
			$_text['Kinds']['MD']	 = 'DAYTIME';
			
			// reason
			$_text['Reason']['01']	 = 'no teacher';
			$_text['Reason']['E']	 = 'E';
			$_text['Reason']['EM']	 = 'EM';
			$_text['Reason']['M']	 = 'M';
			
			// not included reservation kind
			$_text['D'] = 'delete';
		}
		
		if ($_lang == 'ko') {
			// Title name
			$_text['SEARCH']		 = '검색';
			$_text['TODAY']			 = '오늘';
			$_text['STUDENT']		 = '닉네임';
			$_text['TEACHER']		 = '강사이름';
			$_text['RESERVE_TIME']	 = '예약시간';
			$_text['STATUS']		 = '수업상태';
			$_text['-']				 = '-----'; // extended class [button]
			$_text['REASON']		 = '연장이유';
			$_text['COMMENT']		 = 'Comment';
			$_text['READ']			 = '보기';
			
			// MENU NAVIGATION
			$_text['EXTEND THE CLASS']			 = '수업별연장';
			$_text['HISTORY']					 = '수업별연장내역';
			
			$_text['CREATED']					 = 'CREATED';
			$_text['RESPONDED']					 = 'RESPONDED';
			$_text['NO LESSON BASIS']			 = '수업없는연장요청';
			$_text['HISTORY NOT LESSON BASIS']	 = '수업없는연장내역';
			
			// Extension[s]
			$_text['KINDS']			 = '수업종류';
			$_text['REASON']		 = '연장이유';
			$_text['DESCRIPTION']	 = '상세';
			$_text['EXTEND DAY']	 = '수업종료일';
			
			// submit cancel or close
			$_text['SUBMIT']	 = '연장요청';
			$_text['CANCEL']	 = '취소';
			$_text['CLOSE']		 = '닫기';
			$_text['DATE POST']	 = '글쓴시간';
			$_text['MODIFIED']	 = '글수정시간';
			
			// Reservation Status
			$_text['Status']['R'] = '예약';
			$_text['Status']['A'] = '결석';
			$_text['Status']['F'] = '수업완료';
			$_text['Status']['E'] = '연장';
			$_text['Status']['M'] = '연장';
			
			// Reservation Kinds
			$_text['Kinds']['K']	 = '큐빅';
			$_text['Kinds']['P']	 = '포인트';
			$_text['Kinds']['M']	 = '주5회고정';
			$_text['Kinds']['M2']	 = '주2회고정';
			$_text['Kinds']['M3']	 = '주3회고정';
			$_text['Kinds']['MD']	 = '낮고정';
			
			// reason
			$_text['Reason']['01']	 = '예약강사없음';
			$_text['Reason']['E']	 = '수업진행안됌';
			$_text['Reason']['EC']	 = '고객에 의한 수업 연장';
			$_text['Reason']['EM']	 = '회사사정으로 수업진행 안됌';
			$_text['Reason']['M']	 = '강사님 사정으로 수업진행 안됌';
			
			// not included reservation kind
			$_text['D'] = 'delete';
		}
		
		return $_text;
	}
	
	function infocancelation ($_lesson_id = null, $_kinds = null) {
		$_p = array($_lesson_id, date('Y-m-d H:i:s'), $_kinds);
		$con->free_sql("INSERT INTO infocancelations (lesson_id, cancelation_time, kinds, created, modified) VALUES ('$_p[0]', '$_p[1]', '$_p[2]', '$_p[1]', '$_p[1]') ");
	}
	
	function page_lessons_search_content ($page = FALSE, $page_num = NULL, $pagenum = NULL) {
		
		$limit = 0;
		$_count = 1;
		$_content_paging = null;
		//No Page First set 1
		if (!isset($page_num) || !is_numeric($page_num)) {
			$select = 1;
			$limit = 0;
			$page_num = null;
		}
		
		if (isset($page_num) && is_numeric($page_num) >= $pagenum) {
			if ($page_num >= 2 && $page_num < 6) {
				$select = $page_num;
				$limit = ($select - 1) * 10;
			}
			if ($page_num > 5) {
				$select = $page_num;
				$limit = ($select - 1) * 10;
			}
			if ($page_num == $pagenum) {
				$select = $page_num;
				$limit = ($select - 1) * 10;
			}
		}
		//pr($this->params['form']);
		if (isset($page_num)) {
			$page = $page_num;
		} else {
			$page = 1;
		}
		if ($pagenum > 0) {
			$ceil = ceil($page / 5);
			$last = ($ceil * 5); //until
			$select = ($last - 4); //start counting
		
			$_content_paging  = '<div id="paging">';
			//$_content_paging .= '<form name="pageform" method="post">';
			if ($page > 5) {
				$_content_paging .= '<span><input type="button" onclick="document.form11.thepage.value = 1; document.form11.submit();" value="First"></span></span>';
			} else {
				$_content_paging .= '<span><input type="button" value="First" disabled></span>';
			}
			if ($page > 5) {
				$_content_paging .= '<span><input type="button" onclick="document.form11.thepage.value = '.($select-1).'; document.form11.submit();'.'" name="pprev" value="Prev -5page"></span>';
			} else {
				$_content_paging .= '<span><input type="button" name="pprev" value="Prev -5page" disabled></span>';
			}
		
			//$page = $select;
		
			if ($page == $select) {
				$page = $select;
			}
		
			if ($page > $last && $page < $select) {
				$page = $page_num;
			}
		
			if ($last >= $pagenum) {
				$last = $pagenum;
			}
		
			for($i=$select;$i<=$last;$i++) {
				if ($i == $page) {
					$_content_paging .= '<span><input type="button" value="'.$i.'" disabled></span>';
				} else {
					$_content_paging .= '<span><input type="button" onclick="document.form11.thepage.value = this.value; document.form11.submit();" value="'.$i.'"></span>';
				}
		
			}
			if ($pagenum > $last){
				$_content_paging .= '<span><input type="button" onclick="document.form11.thepage.value = '.($select+5).'; document.form11.submit();" name="pnext" value="Next + 5page"></span>';
			} else {
				$_content_paging .= '<span><input type="button" name="pnext" value="Next + 5page" disabled></span>';
			}
		
			if ($pagenum > $last){
				$_content_paging .= '<span><input type="button" onclick=" document.form11.thepage.value = '.$pagenum.'; document.form11.submit();" value="Last"></span>';
			} else {
				$_content_paging .= '<span><input type="button" value="Last" disabled></span>';
			}
		
			$_content_paging .= '<span><input type="button" value="Page. '.$page.'" disabled></span>';
			//$_content_paging .= '</form>';
		
			$_content_paging .= '</div>';
		}
		$limit = $limit.', 10';
		if ($page > 1) {
			$_count = ((($page - 1) * 10) + 1);
		}
		$_return = array($limit, $_count, $_content_paging);
		return $_return;
	}
	
	function page_lessons_search ($userId = null, $from_plus_time = null, $to_plus_one = null, $kindsArr = null, $statusArr = null) {
		$_kindsArr = null;
		foreach ($kindsArr as $ListKindsArr) :
			if (!empty($_kindsArr)) {
			$_kindsArr = $_kindsArr.', '."'".$ListKindsArr."'";
			} else {
				$_kindsArr = "'".$ListKindsArr."'";
			}
		endforeach;
		$_statusArr = null;
		foreach ($statusArr as $ListStatusArr) :
		if (!empty($_statusArr)) {
			$_statusArr = $_statusArr.', '."'".$ListStatusArr."'";
		} else {
			$_statusArr = "'".$ListStatusArr."'";
		}
		endforeach;
		$_totalLessonsSearch = $con->query(
				"
				SELECT
				count(*) AS COUNT
				FROM lessons as Lesson
				WHERE Lesson.user_id = '$userId'
				AND Lesson.reserve_time >= '$from_plus_time'
				AND Lesson.reserve_time <= '$to_plus_one'
				AND ((Lesson.reservation_kinds IN ($_kindsArr))
				AND (Lesson.lesson_status IN ($_statusArr)))
				");
		$count = $_totalLessonsSearch[0][0]['COUNT'];
		$_pages = ceil($count / 10);
		return $_pages;
	}
	
	
	# added by dave
	# 6-22-2016
	# enable page language translation version 2
	function multi2($controller,$action){

		# use dynamic controller
		$con = null;
		if($controller == "lessons")
			$con = $this->controller->Lesson;
		else if($controller == "teachers")
			$con = $this->controller->Teacher;
		else if($controller == "leveltests")
			$con = $this->controller->Leveltest;
		else if($controller == "payments")
			$con = $this->controller->Payment;
		else if($controller == "complains")
			$con = $this->controller->Complain;
		else if($controller == "bookmarks")
			$con = $this->controller->Bookmark;
		else if($controller == "fixedschedules")
			$con = $this->controller->Fixedschedule;
		else if($controller == "styles")
			$con = $this->controller->Style;
		else if($controller == "favorites")
			$con = $this->controller->Favorite;
		else if($controller == "homeworks")
			$con = $this->controller->Homework;
		else if($controller == "friends")
			$con = $this->controller->Friend;
		else if($controller == "extendeds")
			$con = $this->controller->Extended;
		else if($controller == "group_lessons")
			$con = $this->controller->GroupLesson;

		# get languages first (en,ko,...)
		$languages = $con->query("SELECT name,express_name_en,express_name_ko FROM dictionary_details WHERE dictionary_id = (SELECT id FROM dictionaries WHERE name LIKE 'language'); ");

		# storage R
		$LANG = array(); # 1st dimension

		foreach($languages as $l){
			$name = $l['dictionary_details']['name']; # en,ko,...
			$LANG[$name] = array(); # 2nd dimension
		}

		# get route id basing from controller and action parameter supplied
		$q = $con->query("SELECT id FROM language_routes WHERE controller LIKE '$controller' AND action LIKE '$action'");
		$route_id = $q[0]['language_routes']['id'];

		# get variables and translations based from route id
		$q = $con->query("SELECT * FROM languages WHERE route_id = $route_id");

		# structure the array
		foreach ($q as $tran) {
			$var = $tran['languages']['variable'];
			$en_text = $tran['languages']['en_text'];
			$ko_text = $tran['languages']['ko_text'];

			$LANG['en'][$var] = $en_text;
			$LANG['ko'][$var] = $ko_text;
		}

		return $LANG;

		# retrieving from database
		# echo $LANG['en']['var'];

	}

	# added by dave
	# 06-23-2016
	# used to set languages and store default language session
	# usually called in a controller's action - $this->LanguageHandler->set_lang()
	function set_lang(){
		# set initial language 
		if($this->controller->Session->read('lang') == "")
			$this->controller->Session->write('lang','ko'); # default language

		# get current controller and action
		$controller = $this->controller->params['controller'];
		$action = $this->controller->params['action'];

		# get language array
		$language = $this->multi2($controller,$action);

		# return language array
		return $language;
	}
	
	# added by dave
	# 6-20-2016
	# enable page language translation
	/*
	function multilingual($controller,$action,$lang){
		# get route id basing from controller and action parameter supplied
		$q = $con->query("SELECT id FROM language_routes WHERE controller LIKE '$controller' AND action LIKE '$action'");
		$route_id = $q[0]['language_routes']['id'];

		# get variables and translations based from route id
		$q = $con->query("SELECT * FROM languages WHERE route_id = $route_id");

		# storage array
		$LANG = array();

		# whatever language page is selected
		$selected_page_language = $lang;

		# structure the array
		foreach ($q as $tran) {
			$var = $tran['languages']['variable'];
			$en_text = $tran['languages']['en_text'];
			$ko_text = $tran['languages']['ko_text'];

			# check language
			if($selected_page_language == "en")
				$LANG[$var] = $en_text;
			else if($selected_page_language == "ko")
				$LANG[$var] = $ko_text;
			# default = english
			else{
				$LANG[$var] = $en_text;
			}
		}

		# return the array
		return $LANG;

		# retrieving from database
		# echo $LANG['var'];

	}
	*/

	/*
	# added by dave
	# 06-22-2016
	# set language translation on page loads
	function set_lang(){

		# populate drop down
		$langOptions = $this->Lesson->query("SELECT name,express_name_en,express_name_ko FROM dictionary_details WHERE dictionary_id = (SELECT id FROM dictionaries WHERE name LIKE 'language'); ");
		$this->set('langOptions',$langOptions);
		
		# set initial language 
		if($this->controller->Session->read('lang') == ""){
			$this->controller->Session->write('lang','ko'); # selected language session
		}

		# set translations
		$multiLang = $this->multi2('lessons','mypage');
		return $multiLang;
		#$this->set('language',$multiLang);
		
	}
	*/

}//ModelHandlerComponent
?>
