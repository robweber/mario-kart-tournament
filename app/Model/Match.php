<?php 

App::uses('AppModel', 'Model');
class Match extends AppModel {
    public $name = 'Match';
	public $table = 'matches';
	
	public $belongsTo = array(
		'Driver' => array(
			'className'=>'Driver',
			'foreignKey'=>'driver_id'
		)
	);
}

?>