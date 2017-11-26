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
	public $uses = array('Driver','Setting','Cup');

	public function login($id){
		$this->Session->write('driver_id',$id);
		$this->redirect('/tournaments/driver');
	}

	public function beforeFilter(){
		$settings = $this->Setting->find('list',array('fields'=>array('Setting.name','Setting.value')));
		$this->set('settings',$settings);
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
	
	public function scoreboard(){
		$this->set('title_for_layout','Scoreboard');
		$drivers = $this->Driver->find('all',array('order'=>'Driver.score desc'));
		$this->set('drivers',$drivers);
		
		$settings = $this->Setting->find('list',array('fields'=>array('Setting.name','Setting.value')));
		
		$this->set('player1',$this->_findDriver($drivers, $settings['player_1']));
		$this->set('player2',$this->_findDriver($drivers, $settings['player_2']));
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
		
		//check if logged in
		if(!$this->Session->check('driver_id'))
		{
			$this->redirect('/');
		}
		
		//load the driver
		$driver = $this->Driver->find('first',array('conditions'=>array('Driver.id'=>$this->Session->read('driver_id'))));
		
		if($this->request->is('post'))
		{
			//save the score
			$score = $this->data['Driver']['score'];
			
			if($driver['Driver']['level'] == 0)
			{
				$score = $score * 1.5;
			}
			
			$driver['Driver']['games_played'] = $driver['Driver']['games_played'] + 1;
			$driver['Driver']['score'] = $driver['Driver']['score'] + $score;
			$driver['Driver']['active'] = 'false';
			
			$this->Driver->save($driver);
			
			$this->_updateTournament();
			
			$this->Session->setFlash('Saved');
		}
		
		$this->set('driver',$driver);
			
	}
	
	public function logout(){
		$this->Session->destroy();
		
		$this->redirect('/');
	}
	
	public function _updateTournament(){
		//check if there are any active users
		$activeDrivers = $this->Driver->find('all',array('conditions'=>array('Driver.active'=>'true')));
		
		if(count($activeDrivers) == 0)
		{
			//we need to update the tournament
			$settings = $this->Setting->find('list',array('fields'=>array('Setting.name','Setting.value')));
		
			//get a list of all the cups
			$cups = $this->Cup->find('all',array('conditions'=>array('Cup.game_id'=>$settings['active_game'])));
			
			//list drivers by games played
			$drivers = $this->Driver->find('all',array('conditions'=>array('Driver.games_played < 2'),'order'=>'Driver.games_played asc'));
			
			if(count($drivers) >= 2)
			{
				$p1 = 0;
				$p2 = $p1;
		
				//drivers should be in the same "round"
				while($p2 == $p1 && $drivers[$p1]['Driver']['games_played'] == $drivers[$p2]['Driver']['games_played'])
				{
					$p2 = rand(0,count($drivers)-1);
				}
				
				$player1 = $drivers[$p1];
				$player2 = $drivers[$p2];
				
				$player1['Driver']['active'] = 'true';
				$player2['Driver']['active'] = 'true';
				
				$this->Driver->save($player1);
				$this->Driver->save($player2);
				
				$this->Setting->query('update settings set value = ' . $player1['Driver']['id'] . ' where name = "player_1"');
				$this->Setting->query('update settings set value = ' . $player2['Driver']['id'] . ' where name = "player_2"');
			}
			else if(count($drivers) == 1)
			{
				//we have an odd number
				$player1 = $drivers[0];
				
				$player1['Driver']['active'] = 'true';
				
				$this->Driver->save($player1);
				
				$this->Setting->query('update settings set value = ' . $player1['Driver']['id'] . ' where name = "player_1"');
				$this->Setting->query('update settings set value = -1 where name = "player_2"');
			}
			else
			{
				//game over!
				$this->Setting->query('update settings set value = "true" where name = "game_over"');
			}
			
			//get a cup for them to play
			$cup_id = rand(0,count($cups) - 1);
			$this->Setting->query('update settings set value = "' . $cups[$cup_id]['Cup']['name'] . '" where name = "active_cup"');
		}
	}

	public function _findDriver($drivers,$id){
		
		foreach($drivers as $driver){
			
			if($driver['Driver']['id'] == $id){
				return $driver;
			}
		}

		return null;
	}
}
