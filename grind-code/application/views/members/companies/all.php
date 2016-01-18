<!DOCTYPE html>
<html>
	<body>
		<?php
		foreach ($companies as $company) {?>
			<img height='100px' width='150px' src="<?php echo($company['logo']) ?>">
			<p><?php echo($company['name']) ?></p>
			<p><?php echo($company['description']) ?></p>
		<?
		}
		?>
	</body>
</html>