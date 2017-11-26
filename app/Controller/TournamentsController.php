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
class TournamentsController extends AppController {
	public $uses = array('Driver');


	public function login($id){
		$this->Session->write('driver_id',$id);
		$this->redirect('/tournaments/driver');
	}

	public function home() {
		//check if user is logged in
		if($this->Session->check('driver_id'))
		{
			//render the input page
			$this->redirect('/tournaments/driver');
		}
		else
		{
			//get all the current users
			
			$drivers = $this->Driver->find('all');
			$this->set('drivers',$drivers);
		}
	}
	
	public function add_driver(){
		$this->set('title_for_layout','Add Driver');
		
		if($this->request->is('post'))
		{
			$this->Session->setFlash('Driver Added');
			
			$this->Driver->save($this->data['Driver']);
			
			//save the model id
			$this->Session->write('driver_id',$this->Driver->getLastInsertID());
			$this->redirect('/tournaments/home');
		}
	}
	
	public function driver(){
		//load the driver
		$driver = $this->Driver->find('first',array('conditions'=>array('Driver.id'=>$this->Session->read('driver_id'))));
		$this->set('driver',$driver);
			
	}
	
	public function logout(){
		$this->Session->destroy();
		
		$this->redirect('/');
	}
}
