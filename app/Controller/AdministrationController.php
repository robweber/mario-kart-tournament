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
	public $uses = array('Game','Cup','Setting','Driver');

	public function beforeRender(){
		$settings = $this->Setting->find('list',array('fields'=>array('Setting.name','Setting.value')));
		$this->set('settings',$settings);
	}

	public function index() {
		$this->set('title_for_layout','Administration');
		
		if($this->request->is('post'))
		{
			$this->Session->setFlash('Settings Updated');
			
			//update the active game id
			$this->Setting->query('update settings set value = ' . $this->data['ActiveGame']['ActiveGame']  . ' where name = "active_game"');
			
			//update the total number of rounds
			$this->Setting->query('update settings set value = ' . $this->data['ActiveGame']['TotalRounds']  . ' where name = "total_rounds"');
			
			//update the multiplier
			$this->Setting->query('update settings set value = ' . $this->data['ActiveGame']['Multiplier']  . ' where name = "score_multiplier"');
			
		}
		
		$games = $this->Game->find('list',array('fields'=>array('Game.id','Game.name')));
		$this->set('games',$games);
	}

	public function users(){
		$drivers = $this->Driver->find('all',array('order'=>'Driver.name desc'));
		$this->set('drivers',$drivers);
	}

	public function delete($userid){
		$this->Driver->delete($userid);
		$this->Session->setFlash('User Deleted');
		
		$this->redirect('/admin/users');
	}

	public function start(){
		//start the tournament
		$this->Setting->query('update settings set value = "true" where name = "tournament_active"');
		$this->Setting->query('update settings set value = "false" where name = "game_over"');
		
		//update the drivers stats
		$this->Setting->query('update drivers set score = 0');
		$this->Setting->query('update drivers set games_played = 0');
		$this->Setting->query('update drivers set active = "false"');
		
		//get the active game
		$settings = $this->Setting->find('list',array('fields'=>array('Setting.name','Setting.value')));
		
		//get a list of all the cups
		$cups = $this->Cup->find('all',array('conditions'=>array('Cup.game_id'=>$settings['active_game'])));
		
		//get the first two players
		$drivers = $this->Driver->find('all');
		
		$p1 = rand(0,count($drivers)-1);
		$p2 = $p1;
		
		while($p2 == $p1)
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
		
		//get a cup for them to play
		$cup_id = rand(0,count($cups) - 1);
		
		while(($player1['Driver']['level'] == 0 || $player2['Driver']['level'] == 0) and $cups[$cup_id]['Cup']['level'] == 1)
		{
			//redraw
			$cup_id = rand(0,count($cups) - 1);
		}
		
		$this->Setting->query('update settings set value = "' . $cups[$cup_id]['Cup']['name'] . '" where name = "active_cup"');
		
		$this->Session->setFlash("Tournament Started");
		$this->redirect('/admin');
	}

	public function stop(){
		//stop the tournament
		$this->Setting->query('update settings set value = "false" where name = "tournament_active"');
		
		//update the drivers stats
		$this->Setting->query('update drivers set games_played = 0');
		$this->Setting->query('update drivers set active = "false"');
		$this->Setting->query('update drivers set score = 0');
		
		$this->Session->setFlash("Tournament Stopped");
		$this->redirect('/admin');
	}
}
