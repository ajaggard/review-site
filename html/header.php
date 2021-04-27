<html>
	<head>
		<title><?php echo $page_title ?></title>
		<script src="../js/jquery-3.4.1.js"></script>
		<script src="../js/common.js"></script>
		<link rel="stylesheet" type="text/css" href="../css/common.css">
		<?php if ( isset($page_ident) ) { ?>
				<script src="../js/<?php echo $page_ident ?>.js"></script>
				<link rel="stylesheet" type="text/css" href="../css/<?php echo $page_ident ?>.css">
		<?php } ?>
	</head>
	<body>
		<div id="header_content">
			<a class="head-logo" href="../html/home.php">My Reviews</a>
			<div class="head-menu">
				<a href="../html/home.php">Home</a>
				<a href="../html/archive.php">Archive</a>
				<a href="../html/about.php">About Us</a>
			</div>
		</div>