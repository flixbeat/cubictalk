<?php
	# created by: dave
	# date: june of 2016
	# purpose: group class functions on user side
	class GroupLessonsController extends AppController{

		var $name = "GroupLessons";
		var $helpers = array('Html','Form','Javascript');
		var $components = array('LanguageHandler','ValidationHandler','DictionaryHandler','PagingHandler');

		function beforeFilter(){
			$this->ValidationHandler->setTimezone('Asia/Manila');
			/*
			for($i=1;$i<=50;$i+=1){
				$this->GroupLesson->query("INSERT INTO group_lessons set teacher_id = 1, topic_title = 'x$i', topic_details = 'x',
				max_student = 2, student_level = 2, kinds_of_lesson = 1, lesson_time = '2016-01-01', 
				how_long = 1, date_created = CURDATE(), point = 10, composition_question_id = 1");
			}
			*/
		}

		function index($pageNumber = null){
			
			$language = $this->LanguageHandler->set_lang();
			$this->set('language',$language);

			$pageNumber = $pageNumber==null ? 1:$pageNumber;

			# get group classes from today to future
			if($pageNumber == 1){
				$q = $this->GroupLesson->query("SELECT *,
					(SELECT count(DISTINCT user_id) FROM lessons where group_lesson_id = gl.id and lesson_status = 'R') as students_reserved,
					(select img_file from teachers where id = gl.teacher_id) as teacher_img
					FROM group_lessons as gl WHERE SUBSTRING(lesson_time, 1, 10) >= curdate() ORDER BY lesson_time DESC LIMIT 10");
			}
			else{
				$startRow = ($pageNumber * 10) - 10;
				$q = $this->GroupLesson->query("SELECT *,
					(SELECT count(DISTINCT user_id) FROM lessons where group_lesson_id = gl.id and lesson_status = 'R') as students_reserved,
					(select img_file from teachers where id = gl.teacher_id) as teacher_img
					FROM group_lessons as gl WHERE SUBSTRING(lesson_time, 1, 10) >= curdate() ORDER BY lesson_time DESC LIMIT $startRow,10");
			}
			
			# get all rows divided by 10
			$totalRows = $this->GroupLesson->query("SELECT count(*)/10 as page_count FROM group_lessons as gl WHERE SUBSTRING(lesson_time, 1, 10) >= curdate()");
			# initialize paging
			$pages = $this->PagingHandler->createPaging($pageNumber,$totalRows);
			# store paging
			$this->set('paging',$this->PagingHandler->writePaging($pageNumber,$pages));
			# give page number to view for numbering
			if($pageNumber == null) $this->set('pageNumber',1);
			else $this->set('pageNumber',$pageNumber);

			$this->set('groupLessons',$q);
		}

		function my_reservations($pageNumber = null){
			$language = $this->LanguageHandler->set_lang();
			$this->set('language',$language);

			$pageNumber = $pageNumber==null ? 1:$pageNumber;

			$userSession = $this->Session->read('loginInfo');
			$userId = $userSession['User']['id'];

			if($pageNumber == 1){
				$groupLessons = $this->GroupLesson->query("SELECT * FROM group_lessons 
				INNER JOIN lessons ON group_lessons.id = lessons.group_lesson_id 
				INNER JOIN teachers ON teachers.id = group_lessons.teacher_id
				WHERE reservation_kinds = 'G' AND user_id = $userId AND lessons.lesson_status = 'R'
				GROUP BY group_lessons.id ORDER BY lesson_time DESC LIMIT 10");
			}
			else{
				$startRow = ($pageNumber * 10) - 10;
				$groupLessons = $this->GroupLesson->query("SELECT * FROM group_lessons 
				INNER JOIN lessons ON group_lessons.id = lessons.group_lesson_id 
				INNER JOIN teachers ON teachers.id = group_lessons.teacher_id
				WHERE reservation_kinds = 'G' AND user_id = $userId AND lessons.lesson_status = 'R'
				GROUP BY group_lessons.id ORDER BY lesson_time DESC LIMIT $startRow,10");
			}

			# get all rows divided by 10
			$totalRows = $this->GroupLesson->query("SELECT count(*)/10 as page_count FROM group_lessons 
				INNER JOIN lessons ON group_lessons.id = lessons.group_lesson_id 
				INNER JOIN teachers ON teachers.id = group_lessons.teacher_id
				WHERE reservation_kinds = 'G' AND user_id = $userId AND lessons.lesson_status = 'R'
				GROUP BY group_lessons.id");

			# initialize paging
			$pages = $this->PagingHandler->createPaging($pageNumber,$totalRows);
			# store paging
			$this->set('paging',$this->PagingHandler->writePaging($pageNumber,$pages));
			# give page number to view for numbering
			if($pageNumber == null) $this->set('pageNumber',1);
			else $this->set('pageNumber',$pageNumber);
			
			$this->set('groupLessons',$groupLessons);
		}

		function class_history($pageNumber = null){
			$language = $this->LanguageHandler->set_lang();
			$this->set('language',$language);

			$pageNumber = $pageNumber==null ? 1:$pageNumber;
			
			# initial page landing get first 10
			if($pageNumber == 1){
				$q = $this->GroupLesson->query("SELECT *,
					(SELECT count(DISTINCT user_id) FROM lessons where group_lesson_id = gl.id) as students_reserved,
					(select img_file from teachers where id = gl.teacher_id) as teacher_img
					FROM group_lessons as gl WHERE SUBSTRING(lesson_time, 1, 10) < curdate() ORDER BY lesson_time DESC LIMIT 10");
			}
			else{
				$startRow = ($pageNumber * 10) - 10;

				$q = $this->GroupLesson->query("SELECT *,
					(SELECT count(DISTINCT user_id) FROM lessons where group_lesson_id = gl.id) as students_reserved,
					(select img_file from teachers where id = gl.teacher_id) as teacher_img
					FROM group_lessons as gl WHERE SUBSTRING(lesson_time, 1, 10) < curdate() ORDER BY lesson_time DESC LIMIT $startRow,10");
			}

			# get all rows divided by 10
			$totalRows = $this->GroupLesson->query("SELECT count(*)/10 as page_count FROM group_lessons as gl WHERE SUBSTRING(lesson_time, 1, 10) < curdate()");
			# initialize paging
			$pages = $this->PagingHandler->createPaging($pageNumber,$totalRows);
			# store paging
			$this->set('paging',$this->PagingHandler->writePaging($pageNumber,$pages));
			# give page number to view for numbering
			if($pageNumber == null) $this->set('pageNumber',1);
			else $this->set('pageNumber',$pageNumber);

			$this->set('groupLessons',$q);

			
		}

		function window_popup_details($groupLessonId){
			Configure::write('debug',0);
			$q = $this->GroupLesson->query("SELECT question FROM composition_questions INNER JOIN group_lessons
			ON group_lessons.composition_question_id = composition_questions.id
			WHERE group_lessons.id = $groupLessonId");
			
			$topicDetails = $q[0]['composition_questions']['question'];

			$this->set('details',$topicDetails);
		}

		function confirmation(){
			$language = $this->LanguageHandler->set_lang();
			$this->set('language',$language);
			
			if( ! empty($this->data['GroupLesson']['groupLessonId']) )
				$groupLessonId = $this->data['GroupLesson']['groupLessonId'];
			else{
				$this->Session->setFlash('An error occured, please try again.','flash_failure');
				$this->redirect(array('controller'=>'group_lessons','action'=>'my_reservations'));
				die();
			}
			

			$q = $this->GroupLesson->query("SELECT *,
			(SELECT count(DISTINCT user_id) FROM lessons where group_lesson_id = gl.id) as students_reserved,
			(select img_file from teachers where id = gl.teacher_id ) as img_file,
			(select mb_id from teachers where id = gl.teacher_id ) as mb_id
			 from group_lessons gl where gl.id = $groupLessonId");
			
			$this->set('groupLessons',$q);
		}

		function submit_confirm($groupLessonId){
			$userSession = $this->Session->read('loginInfo');
			$userId = $userSession['User']['id'];
			$userMbId = $userSession['User']['mb_id'];

			# check to see if the user already reserved
			# redirect if the user has existing reservation
			if($this->ValidationHandler->is_user_reserved_on_group_class($groupLessonId,$userId)){
				$this->Session->setFlash('You are already reserved on this class','flash_failure');
				$this->redirect(array('controller'=>'group_lessons','action'=>'my_reservations'));
				die();
			}

			# check if there are still slots
			if($this->ValidationHandler->are_there_any_slots_on_group_class($groupLessonId)){
				$this->Session->setFlash('Sorry, the class is full, please check for another class.','flash_failure');
				$this->redirect(array('controller'=>'group_lessons','action'=>'index'));
				die();
			}

			# get necessary information from other tables, and check how many
			# available lessons are there
			# ex: 50minutes -> 2 records
			$q = $this->GroupLesson->query("SELECT *,
			(select img_file from teachers where id = gl.teacher_id ) as img_file,
			(select mb_id from teachers where id = gl.teacher_id ) as mb_id,
			al.id
			FROM group_lessons as gl INNER JOIN available_lessons as al 
			ON al.group_lesson_id = gl.id where gl.id = $groupLessonId");
			
			# assign them to variables
			
			$teacherId = $q[0]['gl']['teacher_id'];
			$lessonStatus = 'R';
			$reserveTime = $q[0]['gl']['lesson_time'];
			$textCode = '01';
			$reservationKinds = 'G';
			$point = $q[0]['gl']['point'];
			
			# check if points is enough to reserve
			$qx = $this->GroupLesson->query("SELECT mb_point FROM g4_member where mb_id like '$userMbId'");
			$userPoints = $qx[0]['g4_member']['mb_point'];

			if($userPoints < $point){
				$this->Session->setFlash('Sorry, you don\'t have enough points to reserve.','flash_failure');
				$this->redirect(array('controller'=>'group_lessons','action'=>'index'));
				die();
			}

			# check if user level match the requirement
			$minLevel = $q[0]['gl']['student_level'];

			$qx = $this->GroupLesson->query("SELECT level FROM users WHERE id = $userId");
			$userLevel = $qx[0]['users']['level'];

			if( $userLevel < $minLevel ){
				$this->Session->setFlash('Sorry, your level is not allowed in this class','flash_failure');
				$this->redirect(array('controller'=>'group_lessons','action'=>'index'));
				die();
			}

			# check reservation time vs current time
			$resTime = strtotime($q[0]['gl']['lesson_time']);
			$curTime = strtotime(date('Y-m-d h:i:sa'));

			if( $curTime >= $resTime){
				$this->Session->setFlash('The class had started/ended, you cannot reserve anymore.','flash_failure');
				$this->redirect(array('controller'=>'group_lessons','action'=>'index'));
				die();
			}

			# insert n times where n is the number of records (available_lesson_id)
			$al_id_count = sizeof($q);
			for($i=0;$i<$al_id_count;$i+=1){
				$available_lessons_id = $q[$i]['al']['id'];
				$this->GroupLesson->query("INSERT INTO lessons SET 
					user_id = $userId,
					teacher_id=$teacherId,
					available_lesson_id=$available_lessons_id,
					lesson_status = '$lessonStatus',
					reserve_time = '$reserveTime',
					group_lesson_id = $groupLessonId,
					text_code = '$textCode',
					reservation_kinds = '$reservationKinds',
					created = now()
					");
			}
			
			# get teacher id
			$teacherMbId = $q[0][0]['mb_id'];
			
			# insert data to g4_point table
			$this->GroupLesson->query("INSERT INTO g4_point SET
				mb_id = '$userMbId',
				po_datetime = now(),
				po_content = '$teacherMbId group class',
				po_point = -$point,
				po_rel_id = '$teacherMbId'
				");
			
			# get sum o all points including negatives
			$q = $this->GroupLesson->query("SELECT sum(po_point) as sum_point FROM g4_point where mb_id like '$userMbId' ");
			$sumPoint = $q[0][0]['sum_point'];

			# update session
			$userSession['User']['points'] = $sumPoint;
			$this->Session->write('loginInfo',$userSession);

			# update g4_member point
			$this->GroupLesson->query("UPDATE g4_member SET mb_point = $sumPoint WHERE mb_id like '$userMbId'");
		}

		function cancel($userId,$groupLessonId,$lessonId){
			# validate time
			$q = $this->GroupLesson->query("SELECT lesson_time FROM group_lessons WHERE id = $groupLessonId");

			$reserveTime = strtotime($q[0]['group_lessons']['lesson_time']);
			$currentTime = strtotime(date('Y-m-d h:i:sa'));

			## CLASS CANCELATION RESTRICTION
			# if within 60 minutes of class
			if( round( ($currentTime - $reserveTime)/60 , 2) < 60 && round( ($currentTime - $reserveTime)/60 , 2) > -60 ){ # cannot be canceled
				$this->Session->setFlash('You can only cancel your reservation 1 hour before the class','flash_failure');
				$this->redirect(array('controller'=>'group_lessons','action'=>'index'));
				die();
			}
			# if more than 60 minutes passed
			else if( round( ($currentTime - $reserveTime)/60 , 2) > 60 ){
				$this->Session->setFlash('You can only cancel your reservation 1 hour before the class','flash_failure');
				$this->redirect(array('controller'=>'group_lessons','action'=>'index'));
				die();
			}
			
			$this->GroupLesson->query("UPDATE lessons SET lesson_status = 'T' WHERE id = $lessonId");
			
			$userSession = $this->Session->read('loginInfo');
			$userMbId = $userSession['User']['mb_id'];

			$q = $this->GroupLesson->query("SELECT mb_id from teachers 
				where id = (select teacher_id from group_lessons where id = $groupLessonId GROUP BY teacher_id)");

			$point = $this->GroupLesson->query("SELECT point from group_lessons WHERE id = $groupLessonId");
			$point = $point[0]['group_lessons']['point'];

			$teacherMbId = $q[0]['teachers']['mb_id'];

			$this->GroupLesson->query("INSERT INTO g4_point SET
				mb_id = '$userMbId',
				po_datetime = now(),
				po_content = '$teacherMbId group class',
				po_point = $point,
				po_rel_id = '$teacherMbId'
			");

			# get sum o all points including negatives
			$q = $this->GroupLesson->query("SELECT sum(po_point) as sum_point FROM g4_point where mb_id like '$userMbId' ");
			$sumPoint = $q[0][0]['sum_point'];

			# update session
			$userSession['User']['points'] = $sumPoint;
			$this->Session->write('loginInfo',$userSession);

			# update g4_member point
			$this->GroupLesson->query("UPDATE g4_member SET mb_point = $sumPoint WHERE mb_id like '$userMbId'");
		}

		function window_popup_show_students($groupLessonId){
			$q = $this->GroupLesson->query("SELECT age,sex,nick_name,level FROM users INNER JOIN lessons 
			ON lessons.user_id = users.id INNER JOIN group_lessons
			ON group_lessons.id = lessons.group_lesson_id
			where group_lesson_id = $groupLessonId AND lessons.lesson_status = 'R' GROUP BY user_id");

			$this->set('studentInfo',$q);
		}
	}
?>