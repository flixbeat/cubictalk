<?php
	class LanguageRouteController extends AppController{
		var $name = 'LanguageRoutes';
		var $helpers = array('Javascript','Html','Ajax');
		var $components = array('RequestHandler');
		
		function index(){
			$controllers = $this->LanguageRoute->query("SELECT controller FROM language_routes GROUP BY controller");
			$this->set('controllers',$controllers);

			# arnie's
			$option = $this->LanguageRoute->query("SELECT controller FROM language_routes GROUP BY controller"); //find all from db
			$this->set('options', $option);

			# karl's
			$select = $this->LanguageRoute->query("SELECT * FROM language_routes GROUP BY controller "); 
			$this->set('data', $select); 
		}

		function ajax(){
			Configure::write('debug', 0);
			$action = $_POST['action'];
			if($action=='add_controller'){
				$newController = $_POST['controller'];
				$newAction = $_POST['function_action'];
				$this->data['LanguageRoute']['controller'] = $newController;
				$this->data['LanguageRoute']['action'] = $newAction;
				$this->LanguageRoute->create();
				if($this->LanguageRoute->save($this->data)){
					# query all controller name
					$controllers = $this->LanguageRoute->query("SELECT id,controller FROM language_routes GROUP BY controller");
					echo json_encode($controllers);
				}
				
			}
			else if($action=='load_controller_table'){
				$controllers = $this->LanguageRoute->query("SELECT controller FROM language_routes GROUP BY controller");
				$this->loadControllerTable($controllers);
			}
			else if($action=='load_action_table'){
				$controller = $_POST['controller'];
				$actions = $this->LanguageRoute->query("SELECT action FROM language_routes WHERE controller LIKE '$controller'");
				$this->loadActionTable($actions,$controller);
			}
			else if($action=='add_action'){
				$controller = $_POST['controller'];
				$action = $_POST['act'];

				$this->data['LanguageRoute']['controller'] = $controller;
				$this->data['LanguageRoute']['action'] = $action;

				$this->LanguageRoute->create();

				if(! isset($_POST['loadOnly'])){
					$this->LanguageRoute->save($this->data);
				}

				$actions = $this->LanguageRoute->query("SELECT action FROM language_routes WHERE controller LIKE '$controller'");
				$this->loadActionTable($actions,$controller);
			}
		}

		function loadControllerTable($controllers){
			echo '<tr>';
			echo '<th>Controllers</th>';
			echo '</tr>';
			foreach ($controllers as $controller) {
				echo '<tr>';
				echo '<td>'.$controller['language_routes']['controller'].'</td>';
				echo '</tr>';
			}
		}

		function loadActionTable($actions,$controller){
			echo '<tr>';
			echo "<th>Actions for $controller Controller</th>";
			echo '</tr>';
			foreach ($actions as $action) {
				echo '<tr>';
				echo '<td>'.$action['language_routes']['action'].'</td>';
				echo '</tr>';
			}	
		}

		// ======================== ARNIE's

		function delete_language($action = null) {
			$this->LanguageRoute->delete($this->data);
		}


		function next_page($controller = null) {
			$actions = $this->LanguageRoute->query("SELECT id, action FROM language_routes WHERE controller LIKE '$controller'");
			$this->set('actions',$actions);
		}

		function ajax_arnie(){
			Configure::write('debug',0);
			echo 1;
		}


		// ======================== karl's

		function next_page_update($controller = null){
			$selAct = $this->LanguageRoute->query("SELECT action FROM language_routes WHERE controller LIKE '$controller'");
			$this->set('actions', $selAct);
			$this->set('controller',$controller);
		}

		function update_controller(){
			$controller = $this->data['LanguageRoute']['controller'];
			$oldController = $this->data['LanguageRoute']['old_controller'];
			$this->LanguageRoute->query("UPDATE language_routes SET controller = '$controller' WHERE controller LIKE '$oldController' ");
		}

		function update_action(){
		
			$oldAction = $this->data['LanguageRoute']['old_action'];
			$newAction = $this->data['LanguageRoute']['action'];
			$controller = $this->data['LanguageRoute']['controller'];
			$this->LanguageRoute->query("UPDATE language_routes SET action = '$newAction' WHERE controller LIKE '$controller' AND action = '$oldAction' ");
		
		}

	}
?>