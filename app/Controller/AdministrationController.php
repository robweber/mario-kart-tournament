<?php

App::uses('AppController', 'Controller');

/**
 * Static content controller
 *
 * Override this controller by placing a copy in controllers directory of an application
 *
 * @package       app.Controller
 * @link https://book.cakephp.org/2.0/en/controllers/pages-controller.html
 */
class AdministrationController extends AppController {
	public $uses = array('Game','Setting');

	public function beforeRender(){
		$settings = $this->Setting->find('list',array('fields'=>array('Setting.name','Setting.value')));
		$this->set('settings',$settings);
	}

	public function index() {
		$this->set('title_for_layout','Administration');
		
		if($this->request->is('post'))
		{
			$this->Session->setFlash('Active Game Updated');
			
			//update the active game id
			$this->Setting->query('update settings set value = ' . $this->data['ActiveGame']['ActiveGame']  . ' where name = "active_game"');
		}
		
		$games = $this->Game->find('list',array('fields'=>array('Game.id','Game.name')));
		$this->set('games',$games);
	}
}
