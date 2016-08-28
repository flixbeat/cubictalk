<?php
	# added by dave
	# 6-23-2016
	# used to redirect to same page when changing page language
	class LanguageRedirectController extends AppController {

		var $name = 'LanguageRedirect';
		var $helpers = array('Js','Javascript');

		# uses no table, requires model
		var $uses = array();

		function change_lang($lang,$controller,$action,$params = null){
			# set it to session
			$this->Session->write('lang',$lang); 
			# selected language session
			$this->redirect(array('controller'=>$controller,'action'=>$action,$params));
		}

	}
?>