<?php
	# created by dave
	# 07-12-2016
	# purpose: for generic validations
	class ValidationHandlerComponent extends Object{


	    function startup(&$controller) {
	        $this->controller=&$controller;
	    }

	    function setTimezone($timezone){
	    	date_default_timezone_set($timezone);
	    }

		function is_user_reserved_on_group_class($groupLessonId,$userId){
			$x = $this->controller->modelClass;
			$q = $this->controller->$x->query("SELECT user_id FROM lessons WHERE group_lesson_id = $groupLessonId AND user_id = $userId AND lesson_status = 'R'");
			if(sizeof($q)>0) return true;
			else return false;
		}

		function are_there_any_slots_on_group_class($groupLessonId){
			$x = $this->controller->modelClass;
			# 1. get number of students reserved
			$q = $this->controller->$x->query("SELECT count(DISTINCT user_id) as students_reserved FROM lessons where group_lesson_id = $groupLessonId");

			$studentsReserved = $q[0][0]['students_reserved'];

			# 2. get max student
			$q = $this->controller->$x->query("SELECT max_student FROM group_lessons where id = $groupLessonId");
			$maxStudent = $q[0]['group_lessons']['max_student'];

			# 3. check if full
			if($studentsReserved == $maxStudent) return true;
			else return false;
		}

		function changeTime($dateTime,$min){
			$x = $this->controller->modelClass;
			$q = $this->controller->$x->query( "SELECT date_add('$dateTime', interval $min MINUTE) as new_time");
			return $q[0][0]['new_time'];
		}

	}
?>