<?php
	# created by dave
	# july 8 2016
	# used to access dictionaries table for rules
	class DictionaryHandlerComponent extends Object{

		function startup(&$controller) {
	        $this->controller=&$controller;
	    }

		function getGroupLessonsMaxStudent(){
			$x = $this->controller->modelClass;
			$q = $this->controller->$x->query("SELECT value FROM dictionaries WHERE name = 'group_lessons_max_students'");
			return $q[0]['dictionaries']['value'];
		}

		function getGroupLessonsMaxLevel(){
			$x = $this->controller->modelClass;
			$q = $this->controller->$x->query("SELECT value FROM dictionaries WHERE name = 'group_lessons_max_level'");
			return $q[0]['dictionaries']['value'];
		}

		function getEvaluationMaxRating(){
			$x = $this->controller->modelClass;
			$q = $this->controller->$x->query("SELECT value FROM dictionaries WHERE name = 'eval_ratings_max'");
			return $q[0]['dictionaries']['value'];
			
		}

	}
?>