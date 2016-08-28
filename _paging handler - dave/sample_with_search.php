<?php
	function x($pageNumber=null){
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
			echo $pages;
			# store paging
			$this->set('paging',$this->PagingHandler->writePaging($pageNumber,$pages));
			# give page number to view for numbering
			if($pageNumber == null) $this->set('pageNumber',1);
			else $this->set('pageNumber',$pageNumber);

			$this->set('groupLessons',$res);

		}
	}
	
?>