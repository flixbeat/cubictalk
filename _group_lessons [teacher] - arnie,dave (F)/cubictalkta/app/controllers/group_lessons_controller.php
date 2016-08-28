<?php
	class GroupLessonsController extends AppController{

		var $name = "GroupLessons";
		var $helpers = array('Html','Form','Ajax','Javascript');
		var $components = array('LanguageHandler','DictionaryHandler','PagingHandler','ValidationHandler');

		function beforeFilter(){
			#$this->ValidationHandler->setTimezone('Asia/Manila');
		}

		function index(){
			$q = $this->GroupLesson->query("SELECT *,(select img_file from teachers where id = gl.teacher_id) as teacher_img FROM group_lessons as gl");
			$this->set('groupLessons',$q);

		}

		function window_popup_details($groupLessonId){
			Configure::write('debug',0);
			$q = $this->GroupLesson->query("SELECT topic_details FROM group_lessons WHERE id = $groupLessonId");
			$topicDetails = $q[0]['group_lessons']['topic_details'];
			$this->set('details',$topicDetails);
		}

		function confirm() { 
			$q = $this->GroupLesson->query("SELECT img_file FROM teachers WHERE id = 1"); //declare var 
			$this->set('imageFiles', $q);
		}

		function insert_ui() {
			$teacher_id = $this->Session->read('loginInfo');	
			$teachId = $this->data['GroupLesson']['teacher_id'] = $teacher_id['id'][0];
			//$teachLevel = $this->data['GroupLesson']['management_level'] = $teacher_id['management_level'][0];
			$topicTitle = $this->data['GroupLesson']['topic_title'];
			$topicDetails = $this->data['GroupLesson']['topic_details'];
			$topicDetails = $this->data['GroupLesson']['composition_question_id'];
			$maxStud = $this->data['GroupLesson']['max_student'];

			$studentLevel = $this->data['GroupLesson']['student_min_level'];
			$this->data['GroupLesson']['student_level'] = $studentLevel; 
		
			$lessonDate = $this->data['GroupLesson']['less_date'] . ' ' .$this->data['GroupLesson']['less_time'];
			$this->data['GroupLesson']['lesson_time'] = $lessonDate;

			$lessString = explode('/',$this->data['GroupLesson']['less_date']);
			$timeString = explode(':',$this->data['GroupLesson']['less_time']);
			
			$dateString = $this->data['GroupLesson']['lesson_time'] = $lessString[2] . '-' . $lessString[0] . '-' . $lessString[1] . ' ' .$this->data['GroupLesson']['less_time'];

	
			$studPoint = $this->data['GroupLesson']['point'];
			$lesKind = $this->data['GroupLesson']['les_kind'];	
			$this->data['GroupLesson']['kinds_of_lesson'] = $lesKind;
			$currDateTime = date("Y-m-d H:i:s");
			$curDate = $this->data['GroupLesson']['date_created'] = $currDateTime;
	
			$this->GroupLesson->save($this->data); // save for the first query		

			$last_group_less_id = $this->GroupLesson->getInsertID();
			$loop_count = $this->data['GroupLesson']['how_long'];	
				for($i=0; $i < $loop_count; $i++) {	
					$min = "";	
					if($i == 2) {
						$min = 60;	
					} else if($i ==1) {
						$min = 30;
					} else if($i ==0) {
						$min = 0;
					}
					$this->GroupLesson->query("INSERT INTO available_lessons SET teacher_id = $teachId, reserve_status = 'R', 
						created = now(), modified = now(), group_lesson_id = $last_group_less_id, available_time = (SELECT DATE_ADD(lesson_time, INTERVAL $min MINUTE) AS lesson_time FROM group_lessons WHERE id = $last_group_less_id) ");
						echo '<p>Group Lesson Created!!</p>';	
				}
			
		}

		function cancel($groupLessonId){
			# validate time
			$this->GroupLesson->query("DELETE FROM group_lessons WHERE id = $groupLessonId");
			$this->Session->setFlash('You canceled the class','flash_info');
			$this->redirect(array('controller'=>'group_lessons','action'=>'reservation'));
			die();
		}

		function insert(){
			//SELECT mb_id FROM teachers INNER JOIN composition_questions ON teachers.mb_id = composition_questions.assigned_to 
			$question = $this->GroupLesson->query("SELECT * FROM composition_questions");
			$this->set('question', $question);
			
			$teacher_id = $this->Session->read('loginInfo');
			$this->set('teacher_id', $teacher_id);

			$teachLevel = $this->data['GroupLesson']['management_level'] = $teacher_id['management_level'][0];
			$this->set('teachLevel', $teachLevel);	

			$teacher = $this->GroupLesson->query("SELECT mb_id, id FROM teachers where teachers.show LIKE 'Y'");
			$this->set('teacher', $teacher);
		}
		
		function showDetails($id =null) {
			//$id = $this->GroupLesson->query("SELECT id FROM composition_questions");
			//$compoId = $this->data['GroupLesson']['question'];
			// $this->set('id', $id);

			$showDetails = $this->GroupLesson->query("SELECT question FROM composition_questions WHERE id = $id");
			$this->set('showDetails', $showDetails);
		}

		function showCompoQues() {

			if(empty($this->data)){
				$questions = $this->GroupLesson->query("SELECT title, cq.id, cq.created, cq.modified, question, group_composition_id, img_file FROM teachers AS t INNER JOIN composition_questions AS cq ON t.id = cq.submitted_by ORDER BY cq.created DESC");
				$this->set('questions', $questions);
			}
			else{
				$teaName = $this->data['teacher_name'];
				$questions = $this->GroupLesson->query("SELECT title, cq.id, cq.created, cq.modified, question, group_composition_id, img_file FROM teachers AS t INNER JOIN composition_questions AS cq ON t.id = cq.submitted_by WHERE t.tea_name LIKE '%$teaName%' ORDER BY cq.created DESC");
				$this->set('questions', $questions);
			}
			
			# datalist
			$teacherNames = $this->GroupLesson->query("SELECT tea_name FROM teachers where teachers.show = 'Y'");
			$this->set('teacherNames',$teacherNames);
		}

		# =====================================DAVE CODE==============================================
		# added 7-22-2016
		# added list of group classes reservations and group classes history

		function reservation($pageNumber = null){
			$pageNumber = $pageNumber==null ? 1:$pageNumber;
			
			if( isset($this->data) ){
				$this->Session->write('searchInfo',$this->data);
			}

			$searchInfo = $this->Session->read('searchInfo');
			# SEARCH
			if( isset($searchInfo) ) {

				$teaName = $searchInfo['GroupLesson']['tea_name'];
				$startDate = $searchInfo['GroupLesson']['start_date'];
				$endDate = $searchInfo['GroupLesson']['end_date'];

				$this->set('teaName'.$teaName);
				$this->set('startDate'.$startDate);
				$this->set('endDate'.$endDate);

				$q = "SELECT *,
						(SELECT count(DISTINCT user_id) FROM lessons where group_lesson_id = gl.id and lesson_status = 'R') as students_reserved,
						(select img_file from teachers where id = gl.teacher_id) as teacher_img
						FROM group_lessons as gl 
						INNER JOIN teachers ON gl.teacher_id = teachers.id";

				$whereClause = " WHERE 1";

				if($teaName != '') $whereClause .= " AND teachers.tea_name LIKE '$teaName' ";
				if($startDate != null && $endDate != null ) $whereClause .= " AND ( SUBSTRING(lesson_time, 1, 10) BETWEEN '$startDate' AND '$endDate')";
				if($startDate != null && $endDate == null ) $whereClause .= " AND SUBSTRING(lesson_time, 1, 10) LIKE '$startDate'";

				$startRow = ($pageNumber * 10) - 10;
				$whereClause .= " ORDER BY lesson_time DESC";

				$q .= $whereClause;

				$res = $this->GroupLesson->query($q . " LIMIT $startRow,10");
				
				# get all rows divided by 10
				$totalRows = $this->GroupLesson->query("SELECT count(*)/10 as page_count,
						(SELECT count(DISTINCT user_id) FROM lessons where group_lesson_id = gl.id and lesson_status = 'R') as students_reserved,
						(select img_file from teachers where id = gl.teacher_id) as teacher_img
						FROM group_lessons as gl 
						INNER JOIN teachers ON gl.teacher_id = teachers.id" . $whereClause );

				# initialize paging
				$pages = $this->PagingHandler->createPaging($pageNumber,$totalRows);
				# store paging
				$this->set('paging',$this->PagingHandler->writePaging($pageNumber,$pages));
				# give page number to view for numbering
				if($pageNumber == null) $this->set('pageNumber',1);
				else $this->set('pageNumber',$pageNumber);

				$this->set('groupLessons',$res);

			}
			# ON INITIAL PAGE LOAD
			else{
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
			
			# datalist
			$teacherNames = $this->GroupLesson->query("SELECT tea_name FROM teachers where teachers.show = 'Y'");
			$this->set('teacherNames',$teacherNames);
		}

		function class_history($pageNumber = null){
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

		function window_popup_details_2($groupLessonId){
			Configure::write('debug',0);
			$q = $this->GroupLesson->query("SELECT question FROM composition_questions INNER JOIN group_lessons
			ON group_lessons.composition_question_id = composition_questions.id
			WHERE group_lessons.id = $groupLessonId");
			
			$topicDetails = $q[0]['composition_questions']['question'];

			$this->set('details',$topicDetails);
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