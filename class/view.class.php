<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 

/**
 * View
 * handles the page-ination and sorting and stuff
 */
class View {

  public $id; 
  protected $_state = array(); 
  protected $_cache; 

  private static $allowed_sorts;

  /**
   * constructor
   * reads for a previous state of this session_id() in temp_data table
   */
  public function __construct($id = null) { 

    // Our session
    $sid = Dba::escape(session_id()); 

    if (is_null($id)) { 
      $this->reset(); 
      $data = Dba::escape(serialize($this->_state)); 
      
      $sql = "INSERT INTO `temp_data` (`sid`,`data`) VALUES (`$sid`,`$data)"; 
      $db_results = Dba::write($sql); 
      $this->id = Dba::insert_id(); 
  
      // We're done here!
      return true; 
    } 

    // We've passed it in, so we need to look it up
    $this->id = $id; 
    $id = Dba::escape($id); 
    $sql = "SELECT `data` FROM `temp_data` WHERE `id`='$id' AND `sid`='$sid'"; 
    $db_results = Dba::read($sql); 

    if ($results = Dba::fetch_assoc($db_results)) { 
      $this->_state = unserialize($results['data']); 
      return true; 
    }

    Event::error('VIEW','View not found or expired, likely due to upgrade or maybe something bad happened'); 

    return false; 

  } // constructor

  /**
   * gc
   * Garbage collection for this table
   */
  public static function gc() { 

    $sql = "DELETE FROM `temp_data` USING `temp_data` LEFT JOIN `session` ON `sessions`.`id`=`temp_data`.`sid` " . 
          "WHERE `session`.`id` IS NULL"; 
    $db_results = Dba::write($sql); 

  } // gc

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
   * get_saved_objects
   * Check and see if we've got a cache for this query
   */
  public function get_saved_objects() { 

    if (is_array($this->_cache)) { 
      return $this->_cache; 
    } 

    // Not cached, lets go get them!
    return $this->get_objects(); 

  } // get_saved_objects

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
      $results[] = $data; 
    } 

    // We could do post-processing here if we wanted

    $this->save_objects($results); 

    return $results;

  } // get_objects

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
   * 

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
    $this->reset_order(); 
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
   * set_start
   * Resets our start record
   */
  public function set_start($start) { 

    $this->_state['start'] = intval($start); 

  } // set_start

  /**
   * set_offset
   * Sets the offset to whatever we want!
   */
  public function set_offset($offset) { 

    $this->_state['offset'] = intval($offset); 

  } // set_offset

  /**
   * reset_filters
   * Resets the filtes we've applied
   */
  public function reset_filters() { 

    $this->_state['filter'] = array(); 

  } // reset_filters

  /**
   * reset_order
   * Resets the order!
   */
  public function reset_order() { 

    unset($this->_state['order']); 

  } // reset_order

} // View
