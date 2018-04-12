<ul class="nav nav-pills pull-right">
	<?php if($controller == 'administration'): ?>
		<li role="presentation"><?php echo $this->Html->link('Home','/') ?></li>
		<li role="presentation" <?php echo isActive('index',$path)?>><?php echo $this->Html->link('Setup','/admin/') ?></li>
		<li role="presentation" <?php echo isActive('users',$path)?>><?php echo $this->Html->link('Users','/admin/users') ?></li>
	
	<?php else: ?>
		<li role="presentation" <?php echo isActive('home',$path)?>><?php echo $this->Html->link('Home','/') ?></li>
		<li role="presentation" <?php echo isActive('scoreboard',$path)?>><?php echo $this->Html->link('Scoreboard','/tournaments/scoreboard') ?></li>
		<li role="presentation" <?php echo isActive('rules',$path)?>><?php echo $this->Html->link('Rules','/tournaments/rules') ?></li>
	
		<?php if($this->Session->check('driver_id')): ?>
			<li role="presentation"><?php echo $this->Html->link('Logout','/tournaments/logout') ?></li>
		<?php endif; ?>
	<?php endif; ?>
</ul>

<?php 

//finds if this is the active menu item
function isActive($item,$path){
	$result = '';
	
	if($item == 'home' && ($path == 'home' || $path == 'driver'))
	{
		$result = 'class="active"';
	}
	else if($item == $path)
	{
		$result = 'class="active"';
	}
	
	return $result;
}
?>