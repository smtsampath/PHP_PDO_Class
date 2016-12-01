	<?php

	# Load Global debug mode
	include("environment_variables.php");

	$host = "localhost";
	$MySQLUsername = "root";
	$MySQLPassword = "boise1";
	$database = "Stunited";

	$SALT_A = "Salt";
	$SALT_B = "Auth";

	define('DB_HOST', $host);
	define('DB_USER', $MySQLUsername);
	define('DB_PASS', $MySQLPassword);
	define('DB_NAME', $database);
