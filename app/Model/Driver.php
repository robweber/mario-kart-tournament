<?php 

App::uses('AppModel', 'Model');
class Driver extends AppModel {
    public $name = 'Driver';
	
	public $hasMany = array(
        'Match' => array(
            'className' => 'Match',
            'order' => 'Match.match_num ASC'
        )
    );
}

?>