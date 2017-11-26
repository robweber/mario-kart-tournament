<?php echo $this->Form->create('Driver') ?>

<?php echo $this->Form->input('name',array('label'=>'Name')) ?>

<?php echo $this->Form->input('level',array('type'=>'radio','options'=>array(0=>'Normal',1=>'Advanced'),'legend'=>'Skill Level')); ?>

<?php 
$myOptions          = array();
$myOptions['avatars/mario.jpg'] = $this->Html->image('avatars/mario.jpg');
$myOptions['avatars/mario2.jpg'] = $this->Html->image('avatars/mario.jpg');

echo $this->Form->input('image', array('type' => 'radio', 'options' => $myOptions,  'legend' => 'Pick an Avatar')); 

?>

<?php echo $this->Form->submit() ?>
