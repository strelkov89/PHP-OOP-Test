<?php

namespace app\views;

?>

<!DOCTYPE html>
<html>
	<head>
	    <title>Расписание поездок</title>
	    <?php foreach($config['assets']['js'] as $js_item): ?>
	    	<script src="/assets/<?= $js_item ?>"></script>
    	<?php endforeach; ?>
    	<?php foreach($config['assets']['css'] as $css_item): ?>
	    	<link rel="stylesheet" type="text/css" href="/assets/<?= $css_item ?>">
    	<?php endforeach; ?>
	</head>
	<body>

		<section>
			<div class="container">

				<?php include_once "form.php"; ?>
				<?php include_once "calendar.php"; ?>

			</div>
		</section>

	</body>
</html>