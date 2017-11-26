<?php 

App::uses('AppModel', 'Model');
class Game extends AppModel {
    public $name = 'Game';
	public $hasMany = array(
        'Cup' => array(
            'className' => 'Cup'
        )
    );
}

?>