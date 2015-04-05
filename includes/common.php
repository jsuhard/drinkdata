<?php

define('PDO_DSN', 'sqlite:drink.sqlite');
require 'includes/sql.php';

require 'includes/classes/fetchable/class.fetchable.php';
require 'includes/classes/fetchable/class.user.php';

define('POSITION_GOOD', 10);
define('POSITION_BAD', 30);

function getInteger($nombre) {
        return intval($nombre);
}

function getIntegerPositif($nombre) {
        return max(0, getInteger($nombre));
}

function getUniqueColor($index) {
	
	switch($index) {
		case 0:
			return '#F7464A';
		case 1:
			return '#46BFBD';
		case 2:
			return '#FDB45C';
	}
}

function getUniqueHighlightColor($index) {
	
	switch($index) {
		case 0:
			return '#FF5A5E';
		case 1:
			return '#5AD3D1';
		case 2:
			return '#FFC870';
	}
}

$user = User::fetch($_COOKIE['user_id']);
if(!is_object($user)) {
	$user = User::fetch(1);
}



?>
