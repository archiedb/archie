<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 

/**
 * View
 * handles the page-ination and sorting and stuff
 */
class View {

  public $uid; 
  protected $_state = array(); 
  protected $_cache; 

  private static $allowed_sorts;
  private static $allowed_filters; 

  /**
   * constructor
   * reads for a previous state of this session_id() in temp_data table
   */
  public function __construct($uid = null) { 

    // Our session
    $sid = Dba::escape(session_id()); 

    if (is_null($uid) AND !isset($_SESSION['view_uid'])) { 
      $this->reset(); 
      $data = Dba::escape(serialize($this->_state)); 
      
      $sql = "INSERT INTO `temp_data` (`sid`,`data`) VALUES ('$sid','$data')"; 
      $db_results = Dba::write($sql); 
      $this->uid = Dba::insert_id(); 
  
      // We're done here!
      return true; 
    } 

    // We've passed it in, or its in the session, so we need to look it up
    $uid = $uid ? $uid : $_SESSION['view_uid']; 
    $this->uid = $uid; 
    $uid = Dba::escape($uid); 
    $sql = "SELECT `data` FROM `temp_data` WHERE `uid`='$uid' AND `sid`='$sid'"; 
    $db_results = Dba::read($sql); 

    if ($results = Dba::fetch_assoc($db_results)) { 
      $this->_state = unserialize($results['data']); 
      return true; 
    }

    Event::error('VIEW','View not found or expired, likely due to upgrade or maybe something bad happened'); 

    return false; 

  } // constructor

  /**
   * _auto_init
   */
  public static function _auto_init() {

    self::$allowed_filters = array(
      'catalog_id',
      'unit',
      'quad',
      'level',
      'feature',
      'station_index',
      'xrf_matrix_index',
      'weigth',
      'notes',
      'height',
      'width',
      'thickness',
      'user',
      'quanity',
      'xrf_artifact_index',
      'created',
      'lsg_unit',
      'material',
      'classification',
      '3dmodel',
      'image',
      'updated'); 

    self::$allowed_sorts = self::$allowed_filters; 

  } // auto_int

  /**
   * gc
   * Garbage collection for this table
   */
  public static function gc() { 

    $sql = "DELETE FROM `temp_data` USING `temp_data` LEFT JOIN `session` ON `session`.`id`=`temp_data`.`sid` " . 
          "WHERE `session`.`id` IS NULL"; 
    $db_results = Dba::write($sql); 

  } // gc

  /**
   * run
   * This loads in the save data, array splices and
   * does any other magic that needs to be done, and 
   * returns an array of objectids, assumption that
   * we've already applied filters/sorts/whathaveyous
   */
  public function run() { 

    $object_ids = $this->get_saved(); 

    $type = ucfirst($this->get_type());
    $type::build_cache($object_ids); 

    $this->save_uid(); 
    $this->store(); 

    return $object_ids; 

  } // run

  /**
   * set_filter
   * Sets _state's filter to what we ask
   */
  public function set_filter($key,$value) { 

    $this->_state['filter'][$key] = $value; 

    // If we set a filter, our total and start are invalid
    $this->reset_total(); 
    $this->set_start(0); 

  } // set_filter

  /**
   * set_join
   * Sets up the joins for the query, slightly complex because order matters
   */
  public function set_join ($join_type,$table,$source,$dest,$priority) { 

    $this->_state['join'][$priority][$table] = strtoupper($join_type) . ' JOIN ' . $table . ' ON ' . $source . '=' . $dest; 

  } // set_join

  /**
   * set_start
   * Start of our view
   */
  public function set_start($start) { 

    $this->_state['start'] = intval($start); 

  } // set_start

  /**
   * set_total
   */
  public function set_total($total) { 

    $this->_state['total'] = intval($total); 

  } // set_total

  /**
   * set_sort
   * Returns the sort, ASC/DESC
   */
  public function set_sort($sort,$order='') { 

    if (!in_array($sort,self::$allowed_sorts)) { 
      return false; 
    } 

    if ($order) { 
      $order = ($order == 'DESC') ? 'DESC' : 'ASC'; 
      $this->_state['sort'] = array(); 
      $this->_state['sort'][$sort] = $order; 
    }
    elseif ($this->_state['sort'][$sort] == 'DESC') { 
      $this->_state['sort'] = array(); 
      $this->_state['sort'][$sort] = 'ASC'; 
    }
    else { 
      $this->_state['sort'] = array(); 
      $this->_state['sort'][$sort] = 'DESC'; 
    }

    // Resort object
    $this->resort_objects(); 

  } // set_sort

  /**
   * set_offset
   * Set the offset
   */
  public function set_offset($offset) { 

    // Whole numbers only!
    $this->_state['offset'] = abs($offset); 

  } // set_offset 

  /**
   * set_select
   */
  public function set_select($field) { 

    $this->_state['select'][$field] = $field; 

  } // set_select

  /**
   * set_type
   * The type of objects we're checking today
   */
  public function set_type($type) { 

    switch ($type) { 
      case 'record':
        $this->_state['type'] = $type;  
        $this->set_base_sql(true); 
      break; 
    } // end switch 

  } // set_type

  /**
   * set_base_sql
   * ok overkill - but maybe not in the future? 
   */
  public function set_base_sql($force = false) { 

    if (strlen($this->_state['base']) && !$force) { return true; } 

    // Maybe in the future we'll switch?!
    $this->set_select('`record`.`uid`'); 
    $sql = 'SELECT %%SELECT%% FROM `record` '; 

    $this->_state['base'] = $sql; 

  } // set_base_sql

  /** 
   * get_type
   * Returns the type
   */
  public function get_type() { 
    
    $type = isset($this->_state['type']) ? $this->_state['type'] : false; 
    return $type; 

  } // get_type

  /*
   * get_saved
   * Check and see if we've got a cache for this query
   */
  public function get_saved() { 

    if (is_array($this->_cache)) { 
      return $this->_cache; 
    } 

    // Not cached, lets go get them!
    return $this->get_objects(); 

  } // get_saved

  /**
   * get_objects
   * Gets an array of ids of the objects we're looking at based
   * on our filters,order,join,what-have-yous
   */
  public function get_objects() { 

    // First get our SQL statement
    $sql = $this->get_sql(true); 
    $db_results = Dba::read($sql); 

    $results = array(); 
    while ($data = Dba::fetch_assoc($db_results)) { 
      $results[$data['uid']] = $data['uid']; 
    } 

    // We could do post-processing here if we wanted

    $this->save_objects($results); 

    return $results;

  } // get_objects

  /**
   * get_total
   */
  public function get_total($objects = null) { 

    if (is_array($objects)) { 
      return count($objects); 
    } 

    if (isset($this->_state['total'])) { 
      return $this->_state['total']; 
    } 

    // Otherwise we need to go to the database
    $db_results = Dba::read($this->get_sql(false)); 
    $num_rows = Dba::num_rows($db_results); 

    $this->_state['total'] = $num_rows; 

    return $num_rows; 

  } // get_total

  /**
   * get_select
   * Returns all of the selects in a friendly sql form
   */
  private function get_select() { 

    $select_string = implode($this->_state['select'],','); 
    return $select_string; 

  } // get_select

  /** 
   * get_base_sql
   * returns base sql statement with any select'ed objects added
   */
  private function get_base_sql() { 

    $sql = str_replace('%%SELECT%%',$this->get_select(),$this->_state['base']); 
    return $sql; 

  } // get_base_sql

  /**
   * get_filter_sql
   * construct the sql for the filter based on our filters
   */
  public function get_filter_sql() { 

    if (!is_array($this->_state['filter'])) { 
      return ''; 
    } 

    $sql = "WHERE 1=1 AND "; 

    foreach ($this->_state['filter'] as $key=>$value) { 
      $sql .= $this->sql_filter($key,$value); 
    }

    $sql = rtrim($sql,'AND ') . ' '; 

    return $sql; 

  } // get_filter_sql 

  /**
   * get_sort
   * singular but returns an array of sorted objects
   */
  public function get_sort() { 

    return $this->_state['sort']; 

  } // get_sort

  /**
   * get_sort_sql
   * Construct the sorting sql statement 
   */
  private function get_sort_sql() { 

    if (isset($this->_state['sort']) && !is_array($this->_state['sort'])) { 
      return ''; 
    } 

    $sql = 'ORDER BY '; 

    foreach ($this->_state['sort'] as $key=>$value) { 
      $sql .= $this->sql_sort($key,$value); 
    } 

    $sql = rtrim($sql,'ORDER BY '); 
    $sql = rtrim($sql,','); 

    return $sql; 

  } // get_sort_sql 

  /**
   * get_limit_sql
   * build the limit statement for our sql
   */
  private function get_limit_sql() { 

    $sql = ' LIMIT ' . intval($this->get_start()) . ',' . intval($this->get_offset()); 

    return $sql; 

  } // get_limit_sql

  /**
   * get_join_sql
   * Build and return the sql statement for any joins
   */
  private function get_join_sql() { 

    if (!isset($this->_state['join']) || !is_array($this->_state['join'])) { 
      return ''; 
    } 

    $sql = ''; 

    foreach ($this->_state['join'] as $joins) { 
      foreach ($joins as $join) { 
        $sql .= $join . ' ';
      } // foreach joins at this level
    } // foreach joins at this level? ok wtf? 

    return $sql; 

  } // get_join_sql 

  /**
   * get_sql 
   * Return a completely build sql statement, has optional T/F
   * pass, which tells us if we should include the limit statement
   */
  public function get_sql($limit = true) { 

    $sql = $this->get_base_sql(); 

    $filter_sql = $this->get_filter_sql(); 
    $join_sql = $this->get_join_sql(); 
    $order_sql = $limit ? $this->get_sort_sql() : ''; // Don't sort if we don't have a limit 
    $limit_sql = $limit ? $this->get_limit_sql() : ''; // Don't limit if we don't want it? 
    $final_sql = $sql . $join_sql . $filter_sql . $order_sql . $limit_sql; 

    return $final_sql; 

  } // get_sql

  /**
   * get_filter
   */
  public function get_filter($key) { 

    return isset($this->_state['filter'][$key]) ? $this->_state['filter'][$key] : false; 

  } // get_filter

  /**
   * get_start
   */
  public function get_start() { 

    return $this->_state['start']; 

  } // get_start

  /**
   * get_offset
   * return our offset
   */
  public function get_offset() { 

      return $this->_state['offset']; 

  } 

  /**
   * get_allowed_filters
   * Return the allowed filters based on type
   */
  public static function get_allowed_filters($type) { 

    return self::$allowed_filters; 

  } // get_allowed_filters

  /**
   * get_allowed_sorts
   * Return the allowed sorts
   */
  public static function get_allowed_sorts($type) { 

    return self::$allowed_sorts; 

  } // get_allowed_sorts
  /**
   * sql_filter
   * Take a filter name and value (we're filtering) and return the
   * sql construct
   */
  private function sql_filter($filter,$value) { 

    $filter_sql = ''; 

    switch($filter) { 
      case 'notes':
        $filter_sql = " `record`.`notes` LIKE '%" . Dba::escape($value) . "%' AND "; 
      break; 
      case 'quad':
        $filter_sql = " `record`.`quad` = '" . Dba::escape(Quad::name_to_id($value)) . "' AND "; 
      break; 
      case 'lsg_unit':
        $filter_sql = " `record`.`lsg_unit` = '" . Dba::escape(Lsgunit::name_to_id($value)) . "' AND "; 
      break; 
      case 'feature':
        $filter_sql = " `record`.`feature` = '" . Dba::escape($value) . "' AND "; 
      break;
      case 'user':
        $user = User::get_from_username($value);
        if (!$user->uid) { 
          $filter_sql = " 1=0 AND ";
        } else {
          $filter_sql = " `record`.`user` = '" . Dba::escape($user->uid) . "' AND "; 
        }
      break; 
      case 'material': 
        $uid = Material::name_to_id($value); 
        if (!$uid) { 
          $filter_sql = " 1=0 AND "; 
        } else { 
          $filter_sql = " `record`.`material` = '" . Dba::escape($uid) . "' AND ";
        }
      break;
      case 'classification':
        $uid = Classification::name_to_id($value); 
        if (!$uid) { 
          $filter_sql = " 1=0 AND "; 
        } else { 
          $filter_sql = " `record`.`classification` = '" . Dba::escape($uid) . "' AND "; 
        }  
      break; 
      case 'unit':
        $filter_sql = " `record`.`unit` = '" . Dba::escape($value) . "' AND "; 
      break; 
      case 'notes':
        $filter_sql = " `record`.`notes` LIKE '%" . Dba::escape($value) . "%' AND "; 
      break; 
      case 'created':
      case 'updated':
        $unix_time = strtotime($value); 
        $start = $unix_time - 86400; 
        $end = $unix_time + 85400; 
        $filter_sql = " (`record`.`$filter` >= '" . Dba::escape($start) . "' AND `record`.`$filter` <= '" . Dba::escape($end) . "') AND";
      break;
      case 'image':
        $value_check = strlen($value) ? "AND `image`.`notes` LIKE '%" . Dba::escape($value) . "%'" : '';
        $this->set_join('left','`image`','`image`.`record`','`record`.`uid`',100); 
        $filter_sql = " (`image`.`uid` IS NOT NULL $value_check) AND "; 
      break;
      case '3dmodel':
        $value_check = strlen($value) ? "AND `media`.`notes` LIKE '%" . Dba::escape($value) . "%'" : '';
        $this->set_join('left','`media`','`media`.`record`','`record`.`uid`',100); 
        $filter_sql = " (`media`.`type`='3dmodel' AND `media`.`uid` IS NOT NULL $value_check) AND ";
      break;
      case 'item':
      case 'station_index':
      case 'level':
      case 'height':
      case 'width':
      case 'thickness':
      case 'catalog_id':
      case 'quanity':
      case 'weight':
      case 'xrf_matrix_index':
      case 'xrf_artifact_index':
        $filter_sql = " `record`.`$filter` = '" . Dba::escape(intval($value)) . "' AND "; 
      break; 
    } // filter

    return $filter_sql; 

  } // sql_filter

  /**
   * sql_sort
   * Build the sql ORDER BY stuff we need
   */
  private function sql_sort($field,$order) { 

    $order = ($order == 'DESC') ? 'DESC' : 'ASC';

    switch ($field) { 
      case 'lsg_unit':
      case 'notes':
      case 'updated':
      case 'created':
      case 'height': 
      case 'width': 
      case 'thickness': 
      case 'quanity': 
      case 'weight': 
      case 'feature':
      case 'level': 
      case 'quad': 
      case 'unit': 
      case 'catalog_id':
      case 'xrf_matrix_index':
      case 'xrf_artifact_index':
      case 'station_index':
        $sql = "`record`.`$field`";
      break;
      case '3dmodel':
        $sql = "`media`.`uid`";
        $this->set_join('left','`media`','`media`.`record`','`record`.`uid`',100); 
      break;
      case 'image':
        $sql = "`image`.`uid`";
        $this->set_join('left','`image`','`image`.`record`','`record`.`uid`',100); 
      break;
      case 'material':
        $sql = "`material`.`name`"; 
        $this->set_join('left','`material`','`material`.`uid`','`record`.`material`',100); 
      break;
      case 'classification': 
        $sql = "`classification`.`name`";
        $this->set_join('left','`classification`','`classification`.`uid`','`record`.`classification`',100); 
      break; 
      case 'user': 
        $sql = '`users`.`username`';
        $this->set_join('left','`users`','`users`.`uid`','`record`.`user`',100); 
      break; 
    } 

    if ($sql) { $sql_sort = "$sql $order,"; }

    return $sql_sort; 

  } // sql_sort

  /**
   * resort_objects
   * Takes existing objects and re-sorts them called internally
   * by set_sort(); 
   */
  private function resort_objects() { 

    // We want an sql statement, with LIMIT intact
    $sql = $this->get_sql(true); 

    $db_results = Dba::read($sql); 

    while ($row = Dba::fetch_assoc($db_results)) { 
      $results[$row['uid']] = $row['uid']; 
    }

    $this->save_objects($results);

    return true;     

  } // resort_objects

  /**
   * store
   * Store our current state in the database
   */
  public function store() { 

    $sid = Dba::escape(session_id());
    $uid = Dba::escape($this->uid); 
    $data = Dba::escape(serialize($this->_state)); 

    $sql = "UPDATE `temp_data` SET `data`='$data' " . 
      "WHERE `sid`='$sid' AND `uid`='$uid'"; 
    $db_results = Dba::write($sql); 

  } // store

  /**
   * save_objects
   * array of object ids we need to save
   */
  public function save_objects($object_ids) { 

    $this->_cache = $object_ids; 
//    $this->set_total(count($object_ids)); 
    $sid = Dba::escape(session_id()); 
    $uid = Dba::escape($this->uid); 
    $objects = Dba::escape(serialize($this->_cache)); 

    $sql = "UPDATE `temp_data` SET `objects`='$objects' " .
        "WHERE `sid`='$sid' AND `uid`='$uid'"; 
    $db_results = Dba::write($sql); 

    return true; 

  } // save_objects

  /**
   * save_uid
   */
  public function save_uid() { 

    $_SESSION['view_uid'] = $this->uid; 

  } // save_uid

  /**
   * reset
   * Reset the entire view, normally only done if no view is found
   * sets our defaults
   */
  public function reset() { 

    $this->reset_base(); 
    $this->reset_select(); 
    $this->reset_join(); 
    $this->reset_filters(); 
    $this->set_start('0'); 
    $this->set_offset(Config::get('page_limit') ? Config::get('page_limit') : '50'); 
    $this->reset_sort(); 
    $this->reset_total(); 

  } // reset

  /**
   * reset_base
   * reset the base string
   */
  public function reset_base() { 

    $this->_state['base'] = NULL; 

  } // reset_base  

  /**
   * reset_total
   * Reset the total number of objects
   */
  public function reset_total() { 

    unset($this->state['total']); 

  } // reset_total

  /**
   * reset_select
   * resets the fields we've selected to return
   */
  public function reset_select() { 

    $this->_state['select'] = array(); 

  } // reset_select 

  /**
   * reset_join
   * resets any 'join' statements we may have aquired
   */
  public function reset_join() { 

    unset($this->_state['join']); 

  } // reset_join

  /**
   * reset_filters
   * Resets the filtes we've applied
   */
  public function reset_filters() { 

    $this->_state['filter'] = array(); 

  } // reset_filters

  /**
   * reset_sort
   * Resets the order!
   */
  public function reset_sort() { 

    unset($this->_state['sort']); 

  } // reset_sort

} // View
