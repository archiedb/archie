<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 

class Cron {

  private static $data_dir; // Path to our data dir 
  public $task; 

  /**
   * constructor
   */
  public function __construct($task) { 

    // Check and see if it's valid
    if (!$this->has_task($task)) { 
      Event::error('CRON','Unknown task:' . $task); 
      return false; 
    } 

    $this->task = $task; 

    return true; 

  } // constructor

  /**
   * has_task
   * check for the required function
   */
  private function has_task($task) { 
  
    $methods = get_class_methods('Cron'); 

    if (in_array('run_' . $task,$methods)) { 
      return true; 
    }

    return false; 

  } // has_task

  /**
   * auto_init
   * Setup the data root variable, and make sure the path exists
   * make it if it doesn't
   */
  public static function _auto_init() { 

    self::$data_dir = Config::get('data_root') . '/cron';
    if (!is_dir(self::$data_dir)) { 
      $retval = mkdir(self::$data_dir,0755,true); 
    }

  } // _auto_init

  /**
   * current_requests
   * return array of filenames of the current requests
   */
  public static function current_requests() { 

    $results = array();

    if (is_dir(self::$data_dir)) {
      if ($dh = opendir(self::$data_dir)) {
        while (($file = readdir($dh)) !== false) {
          $info = pathinfo($file);
          if ($info['extension'] == 'request') {
              $results[] = self::$data_dir . '/' . $file;
          }
        }
      }
    }
    return $results;

  } // current_requests

 /**
   * request_filename
   * Just generate the filename we want
   */
  private function request_filename() {

    $filename = self::$data_dir . '/' . $this->task . '.request';

    return $filename;

  } // request_filename
  
  /**
   * running_filename
   * This returns the filename if the job is running
   */
  private function running_filename() { 

    $filename = self::$data_dir . '/' . $this->task . '.running';

    return $filename; 

  } // running_filename

  /**
   * last_filename
   * filename for when we are done
   */
  private function last_filename() {

    $filename = self::$data_dir . '/' . $this->task . '.last';

    return $filename;

  } // last_filename 

  /**
   * request
   * create a file that the cron job is looking for
   */
  public function request($option='') { 

    $filename = $this->request_filename(); 

    $data = $option . "\n" . \UI\sess::$user->email;

    $retval = (file_put_contents($filename,$data) === false) ? false : true; 

    if (!$retval) { 
      Err::add('general','Unable to create report request'); 
    }

    return $retval; 

  } // request

  /**
   * last_run
   * return the date of the last run
   */
  public function last_run() { 

    $filename = $this->last_filename(); 

    return filemtime($filename); 

  } // last_run

  /**
   * state
   * This returns a string of the current state of the request
   */
  public function state() { 

    $filename = $this->request_filename(); 

    if (file_exists($filename)) { 
      return '<span class="label label-info">Request Pending...</span>';
    }

    $filename = $this->running_filename(); 

    if (file_exists($filename)) { 
      return '<span class="label label-warning">In Progress...</span>';
    }

    $filename = $this->last_filename(); 

    if (!file_exists($filename)) { 
      return '<span class="label label-important">Never</span>';
    }
    else {
      return '<span class="label label-success">' . date('d-M-Y H:i:s',filemtime($filename)) . '</span>';
    }


  } // state

  /**
   * run
   * Generic function that we use to encaspulate the runs
   */
  public function run($options) { 

    $function = 'run_' . $this->task;

    $run_file = $this->running_filename();

    if (file_exists($run_file)) { 
      // Figure out if it's old, if it is trash it and go ahead and run, allow 2 hours
      if (filemtime($run_file) > (time()-6400)) {
        Event::error('RUN','Attempted to double run ' . $this->task . ' aborting second run'); 
        return false; 
      }
      else {
        Event::error('RUN','Stale run found for ' . $this->task . ' unlinking file and starting run');
        unlink($run_file);
      }
    }

    // Indicate this task is in progress
    touch($run_file); 

    $retval = $this->{$function}($options); 

    // Done!
    unlink($this->running_filename()); 

    if ($retval) { 
      touch($this->last_filename());
    }

    return $retval; 

  } // run

  /**
   * run_thumb
   * This runs the thumbnail regeneration 
   */
  public function run_thumb($options) { 

    Content::regenerate_thumb(); 

    return true;  

  } // run_thumb

  /**
   * run_ticket
   * Regen tickets
   */
  public function run_ticket($options) {

    Content::regenerate_ticket();

    return true;

  } // run_ticket
  
  /**
   * run_qrcode
   * This runs the qrcode regen
   */
  public function run_qrcode($options) { 

    Content::regenerate_qrcodes($options); 

    return true; 

  } // run_qrcode

  /**
   * run_3dmodel_thumb
   * This runs a regen of the 3dmodel thumbs
   */
  public function run_3dmodel_thumb($options) { 

    Content::regenerate_3dmodel_thumb(); 

    return true; 
  
  } // run_3dmodel_thumb

  /** 
   * run_scatterplots
   * Runs the python scatterplot generator
   */
  public function run_scatterplots($options) { 

      // This is a system call (sketch?)
      $command = Config::get('prefix') . '/bin/build-scatter-plots';
      $handle = popen($command,"r");
      while ($read = fread($handle,2096)) { 
        echo $read;
        ob_flush();
        flush();
      }
      pclose($handle);

      return true; 

  } // run_scatterplots

} // cron class 

?>
