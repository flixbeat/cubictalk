<?php
	class LanguageController extends AppController{
		var $name = 'Languages';
		var $helpers = array('Form','Javascript','Html','Ajax');

		function manage($route_id = null){
			# first load
			if(!empty($route_id))
				$this->set('route_id',$route_id);
			# on submit
			else{
				if(!empty($this->data)){
					pr($this->data);
					$this->Language->create();
					$this->Language->save($this->data);
				}
			}
		}

		function index(){
			$controllers = $this->Language->query("SELECT id,controller,action FROM language_routes");
			$this->set('controller',$controllers);
		}

		# table data
		function edit_language_ui($route_id = null){
			if(!empty($this->data)){
				$route_id = $this->data['LanguageRoute']['id'];
				$languages = $this->Language->query("SELECT id,route_id,variable,en_text,ko_text FROM languages WHERE route_id = $route_id");
				if( sizeof($languages) == 0){
					$this->set('route_id',$route_id);
				}
				else{
					$this->set('languages',$languages);
				}
			}
			# will be called when edit() function called
			else if(!empty($route_id)){
				$languages = $this->Language->query("SELECT id,route_id,variable,en_text,ko_text FROM languages WHERE route_id = $route_id");
				$this->set('route_id',$route_id);
				$this->set('languages',$languages);
			}
		}

		# @param: id - languages.id
		function edit_translation_ui($id = null){
			if(!empty($id)){
				$texts = $this->Language->query("SELECT id,variable,en_text,ko_text FROM languages WHERE id = $id");
				$this->set('texts',$texts);
			}
		}

		function add_variable_ui($route_id = null) {
			$texts = $this->Language->query("SELECT id,route_id,variable,en_text,ko_text FROM languages WHERE route_id = $route_id");
			if( sizeof($texts) == 0){
				$texts = array(array('languages'=>array('route_id'=>$route_id)));
			}

			$this->set('texts',$texts);
		}

		function add(){
			if(!empty($this->data)){
				$route_id = $this->data['Language']['route_id'];
				$this->Language->create();
				$this->Language->save($this->data);
				$this->redirect(array('controller'=>'language','action'=>'add_variable_ui',$route_id));
				
			}
		}

		function edit(){
			if(!empty($this->data)){
				$this->Language->create();
				$this->Language->save($this->data);

				# get route id
				$q = $this->Language->query("SELECT route_id FROM languages WHERE id = ".$this->data['Language']['id']);
				$route_id = $q[0]['languages']['route_id'];

				$this->redirect(array('controller'=>'language','action'=>'edit_language_ui',$route_id));

			}
		}

		function delete($id = null){
			$this->data['Language']['id'] = $id;

			# get route id first
			$q = $this->Language->query("SELECT route_id FROM languages WHERE id = ".$this->data['Language']['id']);
			$route_id = $q[0]['languages']['route_id'];
			# then delete
			$this->Language->delete($this->data);
			# and redirect
			$this->redirect(array('controller'=>'language','action'=>'edit_language_ui',$route_id));
		}

		function sample(){
			$q = $this->Language->query("SELECT * FROM languages WHERE route_id = 105");

			$LANG = array();
			$selected_page_language = "ko";

			foreach ($q as $tran) {
				$var = $tran['languages']['variable'];
				$en_text = $tran['languages']['en_text'];
				$ko_text = $tran['languages']['ko_text'];

				if($selected_page_language == "en")
					$LANG[$var] = $en_text;
				else if($selected_page_language == "ko")
					$LANG[$var] = $ko_text;
			}
			# retrieving from database
			
			echo $LANG['fluency'];
			echo '<hr>';
			echo $LANG['grammar'];

		}
	}
?>