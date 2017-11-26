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
	public $uses = array();


	public function index() {
		$this->set('title_for_layout','Administration');
	}
}
