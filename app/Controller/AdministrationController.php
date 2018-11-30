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
	public $uses = array('Game','Cup','Setting','Driver','Match');
    public $components = array('Session','Sms');
    
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
            
            //update the send sms value
            $this->Setting->query('update settings set value = ' . $this->data['ActiveGame']['SendSms'] . ' where name = "send_sms"');
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
		$this->Driver->query('update drivers set games_played = 0, wins = 0, losses = 0, score = 0, active = "false"');
		
		//delete the old bracket
		$this->Match->query("truncate matches");
		
		//get the active game
		$settings = $this->Setting->find('list',array('fields'=>array('Setting.name','Setting.value')));
		
		//figure out the seeds
		$drivers = $this->Driver->find('all',array('order'=>'Driver.phone DESC, Driver.id DESC'));
		$matchCount = count($drivers);

		//first round players are a multiple of 4
		while($matchCount % 4 != 0)
		{
			$matchCount ++;
		}
		
		$topNext = True;
		$gotTwo = False;
		$topBracket = 1;
		$bottomBracket = $matchCount/2; //equals total matches
		$i = 0;
		
		//distribute the players in the bracket (alternate top/bottom)
		while($i < $matchCount)
		{
			$match_num = -1;
			if($topNext)
			{
				$match_num = $topBracket;
				
				if($gotTwo)
				{
					$topBracket ++;
					$gotTwo = False;
					$topNext = False;
				}
				else
				{
					//will be tru next time
					$gotTwo = True;
				}
			}
			else
			{
				$match_num = $bottomBracket;
				
				if($gotTwo)
				{
					$bottomBracket --;
					$gotTwo = False;
					$topNext = True;
				}
				else
				{
					//will be true nextTime
					$gotTwo = True;		
				}
			}
			
			//use a real driver if we can
			$driverId = -1;
			if($i < count($drivers))
			{
				$driverId = $drivers[$i]['Driver']['id'];
			}
			
			$this->Match->create();
			$this->Match->set(array('driver_id'=>$driverId,'bracket_level'=>1,'match_num'=>$match_num));
			$this->Match->save();
			
			$i ++;
		}
		
		//create the rest of the matches (unknown drivers)
		$gamesLeft = count($drivers);
		$currentLevel = 1;
		if($gamesLeft % 2 == 1)
		{
			//account for bye
			$gamesLeft ++;	
		}
		
		$gamesLeft = $gamesLeft/2;
		while($gamesLeft > 1)
		{
			$currentLevel ++;
			
			if($gamesLeft % 2 == 1)
			{
				//account for bye
				$gamesLeft ++;	
			}
			
			for($i = 1; $i <= ($gamesLeft /2); $i++)
			{
				//make 2 for each match
				
				$this->Match->create();
				$this->Match->set(array('driver_id'=>-1,'bracket_level'=>$currentLevel,'match_num'=>$i));
				$this->Match->save();
				
				$this->Match->create();
				$this->Match->set(array('driver_id'=>-1,'bracket_level'=>$currentLevel,'match_num'=>$i));
				$this->Match->save();
			}
			
			$gamesLeft = $gamesLeft /2;
		}
		
		//get the first two drivers
		$match1 = $this->Match->find('all',array('conditions'=>array('Match.bracket_level'=>1,'Match.match_num' => 1)));
		$player1 = $match1[0];
		$player2 = $match1[1];
		
		//get a list of all the cups
		$cups = $this->Cup->find('all',array('conditions'=>array('Cup.game_id'=>$settings['active_game'])));
		
		//set the active drivers
		$player1['Driver']['active'] = 'true';
		$player2['Driver']['active'] = 'true';
		
		$this->Driver->save($player1);
		$this->Driver->save($player2);
		
		$this->Setting->query('update settings set value = ' . $player1['Driver']['id'] . ' where name = "player_1"');
		$this->Setting->query('update settings set value = ' . $player2['Driver']['id'] . ' where name = "player_2"');
		
		//set the active match to the first one
		$this->Setting->query('update settings set value = 1 where name = "active_match"');
		$this->Setting->query('update settings set value = 1 where name = "active_level"');
		
		//get a cup for them to play
		$cup_id = rand(0,count($cups) - 1);
		
		$this->Setting->query('update settings set value = "' . $cups[$cup_id]['Cup']['name'] . '" where name = "active_cup"');
		
        //notify each player
        if($settings['send_sms'] == 1)
        {
            $this->Sms->notifyNext($player1['Driver']['phone'], $player1['Driver']['name'], $player2['Driver']['name'],$cups[$cup_id]['Cup']['name'] . ' Cup');
            $this->Sms->notifyNext($player2['Driver']['phone'], $player2['Driver']['name'], $player1['Driver']['name'],$cups[$cup_id]['Cup']['name'] . ' Cup');
        }
        
		$this->Session->setFlash("Tournament Started");
		$this->redirect('/admin');
	}

	public function stop(){
		//stop the tournament
		$this->Setting->query('update settings set value = "false" where name = "tournament_active"');
		
		//delete the bracket
		$this->Match->query("truncate matches");
		
		//update the drivers stats
		$this->Setting->query('update drivers set games_played = 0, wins = 0, losses = 0, score = 0, active = "false"');
		
		$this->Session->setFlash("Tournament Stopped");
		$this->redirect('/admin');
	}
}
