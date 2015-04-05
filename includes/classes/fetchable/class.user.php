<?php

class User extends Fetchable {
	private $firstname;
	private $lastname;
	private $name;
	private $mail;

	function __construct($id) {
		$this->table = 'users';
		
		$infos = sql_select('u.id as uid, u.firstname, u.lastname, u.email FROM '. $this->table .' u WHERE u.id=:uid', array('uid' => $id));
		$this->id = $infos[0]['uid'];
		$this->firstname = $infos[0]['firstname'];
		$this->lastname = $infos[0]['lastname'];
		$this->mail = $infos[0]['email'];
	}

	function getId() {	return $this->id; }
	function getName() {	return $this->firstname . ' '. $this->lastname; }
	
	function getPositions() {
	        $query = 'latitude AS lat, longitude AS lng, SUM(quantity) AS weight
		FROM "data"
		WHERE latitude IS NOT NULL
		AND longitude IS NOT NULL
		AND user_id = :uid
		GROUP BY latitude, longitude
		ORDER BY SUM(quantity) DESC';

	return sql_select($query, array('uid' => $this->id));
	}

function getPositionsCenter() {
/*
        $query = 'SUM(latitude * quantity)/SUM(quantity) AS lat, SUM(longitude * quantity)/SUM(quantity) AS lng
FROM "data"
WHERE latitude IS NOT NULL
AND longitude IS NOT NULL';
*/
$query = 'SUM(latitude * quantity)/SUM(quantity) AS lat, SUM(longitude * quantity)/SUM(quantity) AS lng, SUM(quantity)
FROM "data"
WHERE latitude IS NOT NULL
AND longitude IS NOT NULL
AND user_id = :uid
GROUP BY round(latitude, 0), round(longitude, 0)
ORDER BY SUM(quantity) DESC
LIMIT 1';

return sql_select($query, array('uid' => $this->id))[0];
}

function getPlaces() {
$query = 'SUM(latitude * quantity)/SUM(quantity) AS lat, SUM(longitude * quantity)/SUM(quantity) AS lng, SUM(accuracy * quantity)/SUM(quantity) AS accuracy, GROUP_CONCAT(DISTINCT comment) AS name, MAX(gmaps_place_id) AS gmaps_place_id, SUM(quantity) AS quantity, type, COUNT(comment)
FROM "data" 
WHERE latitude IS NOT NULL
AND longitude IS NOT NULL
AND user_id = :uid
GROUP BY LOWER(comment), substr(latitude, 1, 4), substr(longitude, 1, 4), type 
ORDER BY comment';

return sql_select($query, array('uid' => $this->id));
}

	function getMostType() {
		$query = 'AVG(quantity) AS quantity, type
		FROM data
		WHERE user_id = :uid
		GROUP BY type
		ORDER BY SUM(quantity) DESC
		LIMIT 1';

	return sql_select($query, array('uid' => $this->id))[0];
	}
	
	function getQuantityByDay() {
		$query = 'sum(quantity) as quantity, strftime(\'%w\', date, \'unixepoch\', \'localtime\') AS day
		from data
		WHERE user_id = :uid
		group by strftime(\'%w\', date, \'unixepoch\', \'localtime\')';
		$tmp = sql_select($query, array('uid' => $this->id));
		$items = array(0,0,0,0,0,0,0);
		foreach($tmp as $item) {
			$items[$item['day']] = $item['quantity'];
		}
		$sunday = array_shift($items);
		$items[] = $sunday;
	return $items;
	}

	function getQuantityByMonth() {
		$query = 'sum(quantity) as quantity, strftime(\'%m\', date, \'unixepoch\', \'localtime\') AS month
		from data
		WHERE user_id = :uid
		group by strftime(\'%m\', date, \'unixepoch\', \'localtime\')';
		$tmp = sql_select($query, array('uid' => $this->id));
		$items = array(0,0,0,0,0,0,0,0,0,0,0,0);
		foreach($tmp as $item) {
			$items[$item['month']-1] = $item['quantity'];
		}
	return $items;
	}

	function getQuantityByDayByMonth() {
		$query = 'sum(quantity) as quantity, strftime(\'%m\', date, \'unixepoch\', \'localtime\') AS month
		from data
		WHERE user_id = :uid
		group by strftime(\'%m\', date, \'unixepoch\', \'localtime\')';
		$tmp = sql_select($query, array('uid' => $this->id));
		$items = array(0,0,0,0,0,0,0,0,0,0,0,0);
		foreach($tmp as $item) {
			$items[$item['month']-1] = round($item['quantity'] / cal_days_in_month(CAL_GREGORIAN, $item['month'], 2015), 2);
		}
	return $items;
	}

	function getAverageQuantityByDay() {
		$query = 'sum(quantity)/count(strftime(\'%w\', date, \'unixepoch\', \'localtime\')) as quantity, strftime(\'%w\', date, \'unixepoch\', \'localtime\') AS day
		from data
		WHERE user_id = :uid
		group by strftime(\'%w\', date, \'unixepoch\', \'localtime\')';
		$tmp = sql_select($query, array('uid' => $this->id));
		$items = array(0,0,0,0,0,0,0);
		foreach($tmp as $item) {
			$items[$item['day']] = $item['quantity'];
		}
		$sunday = array_shift($items);
		$items[] = $sunday;
	return $items;
	}

	function getQuantityByType() {
		$query = 'sum(quantity) as quantity, type
		from data
		WHERE user_id = :uid
		group by type
		order by quantity DESC';
	return sql_select($query, array('uid' => $this->id));
	}

}

?>
