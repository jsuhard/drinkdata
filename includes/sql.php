<?php

if(defined('DEV'))	$sql_log = array();

/**
 * \details Se connecte à la base de données configurée dans config.php
 * \return identifiant de connexion
 */
function sql_connect() {
	
	 try
	 {
		$connect = new PDO(PDO_DSN, PDO_USERNAME, PDO_PASSWORD);	
		$connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	 }
	 catch (PDOException $e)
	 {
	         trigger_error("SQL : " . $e->getMessage() ." en utilisant ". PDO_DSN, E_USER_ERROR);
	 } 
				
return $connect;
}

function sql_execute($bdd, $query, $args = NULL) {
	
	if(defined('DEV')) { 
		global $sql_log;
		$type = strtolower(strstr($query, ' ', true));
		$debug = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT);
		$firstCall = end($debug);
		$file = basename($firstCall['file']) .':' . $firstCall['line'] .':'. $firstCall['class'] .'->'. $firstCall['function'];
		$sql_log[$type][] = array('query' => $query, 'args' => $args, 'time' => microtime(true), 'file' => $file);
	}

	try {  
		$_req = $bdd->prepare($query);
	
		if(NULL != $args) {
			$_req->execute($args);
		}else {
			$_req->execute();
		}

	} catch (PDOException $e) {
		$errmsg = 'SQL : ' . $e->getMessage() .'	Requete: '. $query;
		if(NULL != $args) {	$errmsg .= ' arguments: '. implode('|', $args); }
		trigger_error($errmsg, E_USER_ERROR);
	}

	if(defined('DEV')) {
		$time_start = end($sql_log[$type])['time'];
		$index = count($sql_log[$type]) -1;
		$sql_log[$type][$index]['time'] = round((microtime(true) - $time_start)*1000, 0);
	}

return $_req;
}

function sql_select($query, $args = NULL, $limit = 0) {
	$query = 'SELECT '. $query;
	if($limit > 0)	$query .= ' LIMIT '. $limit;
return sql_execute(sql_connect(), $query, $args)->fetchAll(PDO::FETCH_ASSOC);
}

function sql_select_uniq($query, $args = NULL) {
return current(sql_execute(sql_connect(), 'SELECT '. $query .' LIMIT 1', $args)->fetch(PDO::FETCH_NUM));
}

/**
 * \details Ajoute un ou plusieurs enregistrement dans la table $table
 * \param string $table
 * \param array $champs 
 * \param array $values
 * \return integer Dernier ID ajouté
 */
function sql_insert($table, $champs, $values) {
	$bdd = sql_connect();
	sql_execute($bdd, 'INSERT INTO '. $table .' ('. implode(',', $champs) .') VALUES (:'. implode(',:', $champs) .')', $values);
return $bdd->lastInsertId();
}

/**
 * \details Met à jour un ou plusieurs enregistrement de la table $table
 * \param string $table
 * \param array $champs 
 * \param array $values
 * \param string $condition (sans le WHERE)
 */
function sql_update($table, $champs, $values, $condition = null) {

//$query = $champs[0] .'='. $values[0];
$query = $champs[0] .'= ?';
for($i=1; $i < count($champs); $i++) {
	//$query .= ','. $champs[$i] .'='. $values[$i];
	$query .= ','. $champs[$i] .'= ?';
}

if($condition != null)	$query .= ' WHERE '. $condition;

sql_execute(sql_connect(), 'UPDATE '. $table .' SET '. $query .' ', $values);
}

/**
 * \details Supprime plusieurs enregistrements de la table $table
 * suivant les conditions définies par WHERE $condition_champs = $condition_values
 * \param string $table
 * \param array $condition_champs 
 * \param array $condition_values
 */
function sql_delete($table, $condition_champs, $condition_values) {
	$query = 'DELETE FROM '. $table;
	if(count($condition_champs) > 0)	$query .= ' WHERE '. $condition_champs[0] .' = ? ';
	for($i = 1; $i < count($condition_champs); $i++) {
		$query .= ' AND '. $condition_champs[$i] .' = ? ';
	}
	sql_execute(sql_connect(), $query, $condition_values);
}

function sql_query($query, $args = NULL) {
	$bdd = sql_connect();
	
	$_req = sql_execute($bdd, $query, $args);

	if(stripos($query,'select') !== false) {
		return $_req->fetchAll(PDO::FETCH_ASSOC);
	}else {
		return $bdd->lastInsertId();	
	}
	
}

/*
 * deprecated functions
 */
 
function deprecated($new, $old) {trigger_error('merci d\'utiliser '. $new .'() à la place de '. $old .'()', E_USER_DEPRECATED);}

/**
 * \deprecated Merci d'utiliser sql_connect()
 */
function connect_sql() { deprecated('sql_connect', __FUNCTION__); return sql_connect();}

/**
 * \deprecated Merci d'utiliser sql_select('* FROM toto')
 */
function query_sql($query, $args = NULL) { deprecated('sql_query', __FUNCTION__); return sql_query($query, $args); }

?>
