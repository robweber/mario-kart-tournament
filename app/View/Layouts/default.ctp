<!DOCTYPE html>
<html>
<head>
	<?php echo $this->Html->charset(); ?>
	<title>
		Mario Kart Tournament - 
		<?php echo $this->fetch('title'); ?>
	</title>
	<?php
		echo $this->Html->meta('icon');

		echo $this->Html->css('cake.generic');

		echo $this->fetch('meta');
		echo $this->fetch('css');
		echo $this->fetch('script');
	?>
</head>
<body>
	<div id="container">
		<div id="header">
			<h1><?php echo $this->Html->link('Welcome to Mario Kart!', '/'); ?></h1>
		</div>
		<div align="center">
			<div id="content">
	
				<?php echo $this->Flash->render(); ?>
	
				<?php echo $this->fetch('content'); ?>
			</div>
		</div>
		<div id="footer">
			<p><?php echo $this->Html->link('Scoreboard','/tournaments/scoreboard') ?> | <?php echo $this->Html->link('Logout','/tournaments/logout') ?> | Created by Rob Weber</p>
		</div>
	</div>
	<?php echo $this->element('sql_dump'); ?>
</body>
</html>
