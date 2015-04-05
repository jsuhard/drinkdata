<?php

abstract class Fetchable {
	protected $id;
	protected $table;
	
   private static $liste_fetch = array();
         
   public static function fetch($id) {
		$id = getIntegerPositif($id);
		if($id < 1)	return false;

		$calledClassName = get_called_class();
                if(isset(self::$liste_fetch[$calledClassName][$id])) {
                        return self::$liste_fetch[$calledClassName][$id];
                }else {
			//echo 'New '. $calledClassName .'('. $id .')!<br/>';
			$tmp = new $calledClassName($id);
			if($tmp->getId() < 1)	return false;
                        self::$liste_fetch[$calledClassName][$id] = $tmp; //new $calledClassName($id);
                        return self::$liste_fetch[$calledClassName][$id];
                }
        }
	public static function discard($id) {
   	$id = getIntegerPositif($id);
      if($id < 1)     return;

      $calledClassName = get_called_class();
      if(isset(self::$liste_fetch[$calledClassName][$id])) {
      	unset( self::$liste_fetch[$calledClassName][$id]);
      }
   }
   public function dump() {
   	$id = 'debug_'. substr(str_shuffle(str_repeat('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789',5)),0,5);
   	$name = 'Dump::'. get_called_class();
   	$link = URL_BASE .'doc/html/class'. get_called_class() .'.html';
   	echo '<div class="panel panel-info">
   				<a class="close" data-dismiss="alert" href="#" aria-hidden="true">&times;</a>
  					<div class="panel-heading">
    					<h3 class="panel-title"><a data-toggle="collapse" href="#'. $id .'">'. $name .' <span class="glyphicon glyphicon-collapse-down"></span></a></h3>
  					</div>
  					<div id="'. $id .'" class="panel-collapse collapse">
						<pre>';
		var_export($this);
				echo '</pre>
						<a href="'. $link .'" target="_blank">Doc de la classe</a>
					</div>
				</div>';
   }
   protected function updateOneField($champ, $valeur) {
   	sql_update($this->table, array($champ), array($valeur), 'id = '.$this->getId());
   return $valeur;
   }
	abstract public function getId();
}

?>
