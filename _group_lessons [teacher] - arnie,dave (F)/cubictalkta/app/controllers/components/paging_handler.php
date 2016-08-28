<?php
	# created by dave
	# 07-19-2016
	# purpose: use pagination is pages
	
	class PagingHandlerComponent extends Object{
		
	    function startup(&$controller) {
		    $this->controller=&$controller;
	    }

	    # initialize paging
		function createPaging($pageNumber,$totalRows){
			if($totalRows==null) return 0;
			# convert it to string for explosion
			$totalRows = $totalRows[0][0]['page_count']."";
			# explode string getting whole number and decimal
			$r = explode('.', $totalRows);
			# check if whole number or decimal and assign page
			$pages = isset($r[1]) ? $r[0]+1:$r[0];
			# return # of pages
			return $pages;
		}

		# returns html paging 
		function writePaging($pageNumber,$pageCount){
			$paging = "";
			$controller = $this->controller->params['controller'];
			$action = $this->controller->params['action'];
			if($pageNumber > 1){

				$orgPageCount = $pageCount;

				$pageCount = $pageNumber + 4;

				if( $pageCount > $orgPageCount){
					$remainingPage = $orgPageCount - $pageNumber;
					$pageCount = $pageNumber + $remainingPage;
				}
				
				$paging .= '<ul class="pagination">';
				# displays previous
				$paging .= "<li><a href = '".c_path()."/$controller/$action/".($pageNumber-1)."'>Prev</li>";
				# diplays ...
				if($pageNumber > 3){
					$paging .= "<li><a href = '".c_path()."/$controller/$action/".((int)($pageNumber/2)-1)."'>".'...'."</a></li>";
				}
				# displays previous page
				if($pageNumber <= 2){
					$paging .= "<li><a href = '".c_path()."/$controller/$action/".($pageNumber-1)."'>".($pageNumber-1)."</a></li>";
					
				}
				# displays 2 previous page
				else if($pageNumber > 2){
					$paging .= "<li><a href = '".c_path()."/$controller/$action/".($pageNumber-2)."'>".($pageNumber-2)."</a></li>";
					$paging .= "<li><a href = '".c_path()."/$controller/$action/".($pageNumber-1)."'>".($pageNumber-1)."</a></li>";
				}
				# create pages
				for($page = $pageNumber; $page <= $pageCount; $page+=1){

					if($page == $pageNumber)
						$class = 'active';
					else
						$class = '';

					$paging .= "<li class = '$class'><a href = '".c_path()."/$controller/$action/$page'>$page</a></li>";
				}
				# displays ...
				if( $orgPageCount > ($pageNumber + 4)){
					$pageN = $pageNumber;
					# check if page number will exceed total pages
					if($pageN*2 > $orgPageCount){
						# select the last page
						$pageN = $orgPageCount;
					}
					# if not
					else{
						# button [...] will select current page number times 2
						$pageN *= 2;
					}
					$paging .= "<li><a href = '".c_path()."/$controller/$action/".$pageN."'>".'...'."</a></li>";
				}
				# displays next
				if( ($pageNumber + 1) <= $orgPageCount ){
					$paging .= "<li><a href = '".c_path()."/$controller/$action/".($pageNumber+1)."'>Next</li>";
				}
				$paging .= '</ul>';
			}
			# if page number = 1
			else{
				$orgPageCount = $pageCount;
				$pageCount = $pageCount>5 ? 5:$pageCount;

				$paging .= '<ul class="pagination">';
				for($page = 1; $page <= $pageCount; $page+=1){
					if($page == $pageNumber)
						$class = 'active';
					else
						$class = '';
					$paging .= "<li class = '$class'><a href = '".c_path()."/$controller/$action/$page'>$page</a></li>";
				}
				# displays ...
				if( $orgPageCount > ($pageNumber + 4)){
					$paging .= "<li><a href = '".c_path()."/$controller/$action/".(int)($orgPageCount/2)."'>".'...'."</a></li>";
				}
				# displays next
				if( ($pageNumber + 1) <= $orgPageCount ){
					$paging .= "<li><a href = '".c_path()."/$controller/$action/".($pageNumber+1)."'>Next</li>";
				}
				$paging .= '</ul>';
			}

			return $paging;
		}

	}

?>