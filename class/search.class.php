<?php 
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 


class Search { 

  private function __construct() {}
  private function __clone() {}

	// record
	public static function record($field,$value,$order='station_index') { 

    switch ($field) { 
      case 'item': 
	      $where_sql = self::item_where_sql($value); 
      break; 
      default: 
        $where_sql = self::default_where_sql($field,$value); 
      break; 
    }

		// If something went wrong getting the where
		if (Error::occurred()) { 
			return array(); 
		} 

		// If it's one of the "FK" entries do the the other search first
		// FIXME: Hackish!!! :( 
		$fk_array = array('material','classification','user','created','updated','lsg_unit','quad'); 

		//FIXME: This is so wrong
		$offset = Dba::escape(intval($_POST['offset'])); 
		$limit = Dba::escape(intval(Config::get('page_limit')));  

		// Get he total
		$sql = "SELECT `uid` FROM `record` WHERE $where_sql"; 
		$db_results = Dba::read($sql); 
		$GLOBALS['total'] = Dba::num_rows($db_results); 

    $order_by_allowed = array('unit','level','quad','feature','station_index','xrf_matrix_index','weight','height','width','thickness','quanity','xrf_artifact_index','created','updated');
    // We can't always have nice things
    if (!in_array($order,$order_by_allowed)) { 
      $order = 'station_index';
    }

    $order = Dba::escape($order); 

		//FIXME: We need something for classification and material 
		//FIXME: Need to have pagination!
		//FIXME: Assume for now there is always a site...
		$sql = "SELECT `uid` FROM `record` WHERE $where_sql ORDER BY `$order` LIMIT $offset,$limit"; 
		$db_results = Dba::read($sql); 

		
		while ($row = Dba::fetch_assoc($db_results)) { 
			$results[] = new Record($row['uid']); 
		} 

		return $results; 

	} // record 	

	// This is site-catalog#
	private static function item_where_sql($value) { 

		$elements = explode("-",$value); 
		
		// Only the catalog #
		if (count($elements) == '1') { 
			$site = Dba::escape(Config::get('site')); 
			$catalog_id = Dba::escape($elements['0']); 
		} 
		// They gave us both
		else { 
			$site = Dba::escape($elements['0']); 
			$catalog_id = Dba::escape($elements['1']); 
		} 

		$where_sql = "`site`='$site' AND `catalog_id`='$catalog_id'"; 

		return $where_sql; 

	} // item_where_sql

	private static function default_where_sql($field,$value) { 

		$allowed_array = array('site','catalog_id','unit','level','lsg_unit','station_index','xrf_matrix_index','weight','height','width','thickness','quanity','material','classification','notes','xrf_artifact_index','user','created','updated','quad','feature'); 

		if (!in_array($field,$allowed_array)) { 
			Error::add('general','Invalid Field specified for search'); 
			return false;  
		} 

		$field = Dba::escape($field); 
		$value = Dba::escape($value); 
		$site = Dba::escape(Config::get('site')); 

		$where_sql = "`$field` LIKE '%$value%' AND `site`='$site'"; 
		
		return $where_sql; 

	} // default_where_sql
} // end search class
?>
