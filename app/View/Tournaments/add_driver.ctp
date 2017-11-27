<?php echo $this->Form->create('Driver') ?>

<?php echo $this->Form->input('name',array('label'=>'Name')) ?>

<?php echo $this->Form->input('level',array('type'=>'radio','options'=>array(0=>'Normal',1=>'Advanced'),'value'=>'0','legend'=>'Skill Level')); ?>

<?php 
$avatars  = array('baby_daisy','baby_luigi','baby_mario','baby_peach','bowser','daisy','donkey','koopa','luigi','mario','mario2','peach','waluigi','wario','yoshi');

//echo $this->Form->input('image', array('type' => 'radio', 'options' => $myOptions,  'legend' => 'Pick an Avatar','class'=>'avatar')); 

?>
<?php $mod = 0; ?>
<fieldset class="avatar-selector">
	<legend>Pick an Avatar</legend>
	<table>
	<?php foreach($avatars as $avatar): ?>
		<?php if($mod % 2 == 0): ?>
			<tr>
		<?php endif; ?>
		<td>
			<input type="radio" name="data[Driver][image]" id="<?php echo $avatar ?>" value="avatars/<?php echo $avatar ?>.jpg"/>
			<label class="avatar <?php echo $avatar ?>" for="<?php echo $avatar ?>"></label>
		</td>
		<?php if($mod % 2 == 1): ?>
			</tr>
		<?php endif; ?>
		<?php $mod = $mod + 1 ?>
	<?php endforeach; ?>
	</table>
</fieldset>
<?php echo $this->Form->submit() ?>
