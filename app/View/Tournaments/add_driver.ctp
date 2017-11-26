<?php echo $this->Form->create('Driver') ?>

<?php echo $this->Form->input('name',array('label'=>'Name')) ?>

<?php echo $this->Form->input('level',array('type'=>'radio','options'=>array(0=>'Normal',1=>'Advanced'),'legend'=>'Skill Level')); ?>

<?php 
$myOptions          = array();
$myOptions['avatars/baby_daisy.jpg'] = $this->Html->image('avatars/baby_daisy.jpg');
$myOptions['avatars/baby_luigi.jpg'] = $this->Html->image('avatars/baby_luigi.jpg');
$myOptions['avatars/baby_mario.jpg'] = $this->Html->image('avatars/baby_mario.jpg');
$myOptions['avatars/baby_peach.jpg'] = $this->Html->image('avatars/baby_peach.jpg');
$myOptions['avatars/bowser.jpg'] = $this->Html->image('avatars/bowser.jpg');
$myOptions['avatars/daisy.jpg'] = $this->Html->image('avatars/daisy.jpg');
$myOptions['avatars/donkey.jpg'] = $this->Html->image('avatars/donkey.jpg');
$myOptions['avatars/koopa.jpg'] = $this->Html->image('avatars/koopa.jpg');
$myOptions['avatars/luigi.jpg'] = $this->Html->image('avatars/luigi.jpg');
$myOptions['avatars/mario.jpg'] = $this->Html->image('avatars/mario.jpg');
$myOptions['avatars/mario2.jpg'] = $this->Html->image('avatars/mario2.jpg');
$myOptions['avatars/peach.jpg'] = $this->Html->image('avatars/peach.jpg');
$myOptions['avatars/waluigi.jpg'] = $this->Html->image('avatars/waluigi.jpg');
$myOptions['avatars/wario.jpg'] = $this->Html->image('avatars/wario.jpg');
$myOptions['avatars/yoshi.jpg'] = $this->Html->image('avatars/yoshi.jpg');

echo $this->Form->input('image', array('type' => 'radio', 'options' => $myOptions,  'legend' => 'Pick an Avatar')); 

?>

<?php echo $this->Form->submit() ?>
