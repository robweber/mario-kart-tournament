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
	public $uses = array('Driver','Setting','Cup','Game','Match');
    public $components = array('Session','Sms');
    
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
		
		//get all the drivers
		$drivers = $this->Driver->find('all',array('order'=>'Driver.score desc'));
		$this->set('drivers',$drivers);
		
		$settings = $this->Setting->find('list',array('fields'=>array('Setting.name','Setting.value')));
		
		//get current players
		$this->set('player1',$this->_findDriver($drivers, $settings['player_1']));
		$this->set('player2',$this->_findDriver($drivers, $settings['player_2']));
		
		//get current match
		$this->set('match',$this->Match->find('all',array('conditions'=>array('Match.bracket_level'=>$settings['active_level'],'Match.match_num'=>$settings['active_match']))));
		
		//load the active game
		$game = $this->Game->find('first',array('conditions'=>array('Game.id'=>$settings['active_game'])));
		$this->set('game',$game);
		
		//load the tournament
		$tournament = array();
		
	}
	
	public function bracket(){
		$this->layout = '';
		
		$matches = $this->Match->find('all',array('order'=>'Match.bracket_level asc, Match.match_num asc, Match.id asc'));
		$this->set('matches',$matches);
		
	}
	
	public function add_driver(){
		$this->set('title_for_layout','Add Driver');
		
		if($this->request->is('post'))
		{
			$this->Session->setFlash('Driver Added');
			
			$this->Driver->save($this->data['Driver']);
			
            //send a test SMS, if the phone is there
            $this->Sms->sendSMS($this->data['Driver']['phone'],'Welcome to Mario Kart! This is just a test, you\'ll be notified when it\'s your turn to play');
            
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
		$this->set('driver',$driver);
	}
    
	public function update_score(){
		if($this->request->is('post'))
		{
			//get the settings are current game information
			$settings = $this->Setting->find('list',array('fields'=>array('Setting.name','Setting.value')));
			$game = $this->Game->find('first',array('conditions'=>array('Game.id'=>$settings['active_game'])));
			
			$driver = $this->Driver->find('first',array('conditions'=>array('Driver.id'=>$this->Session->read('driver_id'))));
			
			//save the score
			$score = $this->data['Driver']['score'];
			
			if($score <= $game['Game']['max_score'])
			{
				$driver['Driver']['games_played'] = $driver['Driver']['games_played'] + 1;
				$driver['Driver']['score'] = $driver['Driver']['score'] + $score;
				$driver['Driver']['active'] = 'false';
				
				$this->Driver->save($driver);
				
				$this->_updateTournament($driver['Driver']['id'],$score,$settings);
				
				$this->Session->setFlash('Saved');
			}
			else
			{
				//can't update the score
				$this->Session->setFlash('Error - Score is too high', 'default', array("class"=>"error"));
				
			}
		}
		
		$this->redirect('/tournaments/driver');
	}
	
	public function logout(){
		$this->Session->destroy();
		
		$this->redirect('/');
	}
	
	public function rules(){
		$this->set('title_for_layout','Rules');
	}
	
	public function _updateTournament($driverId,$score,$settings){
		//update the score for this match
		$match = $this->Match->find('first',array('conditions'=>array('Match.driver_id'=>$driverId,'Match.bracket_level'=>$settings['active_level'],'Match.match_num'=>$settings['active_match'])));
		$match['Match']['score'] = $score;
		
		$this->Match->save($match);
		
		//check if there are any active users
		$activeDrivers = $this->Driver->find('all',array('conditions'=>array('Driver.active'=>'true')));
		
		if(count($activeDrivers) == 0)
		{
			//we need to update the tournament
		
			//get a list of all the cups
			$cups = $this->Cup->find('all',array('conditions'=>array('Cup.game_id'=>$settings['active_game'])));
			
			//set the winner of this match
			$currentMatch = $this->Match->find('all',array('conditions'=>array('Match.bracket_level'=>$settings['active_level'],'Match.match_num'=>$settings['active_match'])));

			if($currentMatch[0]['Match']['score'] > $currentMatch[1]['Match']['score'])
			{
				//move this player to the next round
				$this->_updateMatch($currentMatch[0]['Match']['driver_id'],$settings['active_level'], $settings['active_match']);
				
				//set winner/loser
				$currentMatch[0]['Driver']['wins'] ++;
				$currentMatch[1]['Driver']['losses'] ++;
				
				$this->Driver->save($currentMatch[0]);
				$this->Driver->save($currentMatch[1]);
			}
			else
			{
				$this->_updateMatch($currentMatch[1]['Match']['driver_id'],$settings['active_level'], $settings['active_match']);
				
				//set winner/loser
				$currentMatch[1]['Driver']['wins'] ++;
				$currentMatch[0]['Driver']['losses'] ++;
				
				$this->Driver->save($currentMatch[0]);
				$this->Driver->save($currentMatch[1]);
			}
			
			//get the next active match
			$nextMatch = $this->_nextMatch($settings['active_level'], $settings['active_match']);
			
			
			if($nextMatch != null)
			{
				//update the active players
				$player1 = $nextMatch[0];
				$player2 = $nextMatch[1];
				
				$player1['Driver']['active'] = 'true';
				$player2['Driver']['active'] = 'true';
			
				$this->Driver->save($player1);
				$this->Driver->save($player2);
			
				$this->Setting->query('update settings set value = ' . $player1['Driver']['id'] . ' where name = "player_1"');
				$this->Setting->query('update settings set value = ' . $player2['Driver']['id'] . ' where name = "player_2"');
				
				//update the active matches
				$this->Setting->query('update settings set value = ' . $nextMatch[0]['Match']['bracket_level'] . ' where name = "active_level"');
				$this->Setting->query('update settings set value = ' . $nextMatch[0]['Match']['match_num'] . ' where name = "active_match"');
                
                //get a cup for them to play
                $cup_id = rand(0,count($cups) - 1);
            
                $this->Setting->query('update settings set value = "' . $cups[$cup_id]['Cup']['name'] . '" where name = "active_cup"');
               
                //notify each player
                $this->Sms->notifyNext($player1['Driver']['phone'], $player1['Driver']['name'], $player2['Driver']['name'],$cups[$cup_id]['Cup']['name'] . ' Cup');
                $this->Sms->notifyNext($player2['Driver']['phone'], $player2['Driver']['name'], $player1['Driver']['name'],$cups[$cup_id]['Cup']['name'] . ' Cup');
                 
			}
			else
			{
				//game over!
				$this->Setting->query('update settings set value = "true" where name = "game_over"');
			}

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
	
	public function _nextMatch($currentLevel,$currentMatch){
		$result = null;
		
		//see if we can just increase the match
		while($this->Match->find('count',array('conditions'=>array('Match.bracket_level'=>$currentLevel,'Match.match_num'=>($currentMatch + 1)))) > 0 && $result == null)
		{
			//get the match and make sure it isn't a bye
			$aMatch = $this->Match->find('all',array('conditions'=>array('Match.bracket_level'=>$currentLevel,'Match.match_num'=>($currentMatch + 1))));

			if($aMatch[0]['Match']['driver_id'] != -1 && $aMatch[1]['Match']['driver_id'] != -1)
			{
				//we're good
				$result = $aMatch;
			}
			else
			{
				$currentMatch ++;
				
				//check for a bye
				if($aMatch[0]['Match']['driver_id'] != -1)
				{
					//update the next match for this player
					$this->_updateMatch($aMatch[0]['Match']['driver_id'], $currentLevel, $currentMatch);
				}
				else if($aMatch[1]['Match']['driver_id'] != -1)
				{
					$this->_updateMatch($aMatch[1]['Match']['driver_id'], $currentLevel, $currentMatch);
				}
			}
		}

		//if that doesn't work go up one level
		if($result == null)
		{
			if($this->Match->find('count',array('conditions'=>array('Match.bracket_level'=>($currentLevel + 1),'Match.match_num'=>1))) > 0)
			{
				//get this match
				$result = $this->Match->find('all',array('conditions'=>array('Match.bracket_level'=>($currentLevel + 1),'Match.match_num'=>1)));
			}
		}
		
		return $result;
	}
	
	function _updateMatch($driverId,$level,$match){
		$level ++;
		
		//make match even
		if($match % 2 != 0)
		{
			$match ++;
		}
		$match = $match /2; //number of next match in sequence
		
		//see if there is a next match
		if($this->Match->find('count',array('conditions'=>array('Match.bracket_level'=>$level,'Match.match_num'=>$match))) > 0)
		{
			//get the match
			$aMatch = $this->Match->find('first',array('conditions'=>array('Match.driver_id'=>-1,'Match.bracket_level'=>$level,'Match.match_num'=>$match),'order'=>'Match.id asc'));
			
			//set the driver
			$aMatch['Match']['driver_id'] = $driverId;
			
			$this->Match->save($aMatch);
		}
	}
}
