<?php
// Set the database access information as constants:
DEFINE ('DB_USER', 'root');
DEFINE ('DB_PASSWORD', 'F3RI');
DEFINE ('DB_HOST', 'localhost');
DEFINE ('DB_NAME', 'lookatthis');
DEFINE ('DB_PORT', 3308);

$db = @mysqli_connect (DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, DB_PORT) OR die ('Could not connect to MySQL: ' . mysqli_connect_error() );

mysqli_set_charset($db, 'utf8');

// Use this next option if your system doesn't support mysqli_set_charset().
//mysqli_query($dbc, 'SET NAMES utf8');



?>
