<?php
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 

/**
 * __autoload
 * This function automatically loads any missing classes as they are needed so 
 * that we don't use a million include statements which load more than we need.
 */
function __autoload($class) {
        // Lowercase the class
        $class = strtolower($class);

        $file = Config::get('prefix') . "/class/$class.class.php";

        // See if it exists
        if (is_readable($file)) {
                require_once $file;
                if (is_callable($class . '::_auto_init')) {
                        call_user_func(array($class, '_auto_init'));
                }
        }
        // Else log this as a fatal error
        else {
                Event::error('__autoload', "'$class' not found!");
        }

} // __autoload

/**
 * scrub_in
 * Run on inputs, stuff that might get stuck in our db
 */
function scrub_in($input) {

        if (!is_array($input)) {
                return stripslashes(htmlspecialchars(strip_tags($input)));
        }
        else {
                $results = array();
                foreach($input as $item) {
                        $results[] = scrub_in($item);
                }
                return $results;
        }
} // scrub_in

/**
 * scrub_out
 * This function is used to escape user data that is getting redisplayed
 * onto the page, it htmlentities the mojo
 */
function scrub_out($string) {

        $string = htmlentities($string, ENT_QUOTES, Config::get('site_charset'));

        return $string;

} // scrub_out

/*
 * log_event
 * Logs an event to a defined log file based on config options
 */
function log_event($username, $event_name, $event_description, $log_name) {

	if (defined('NO_LOG')) { return true; }

  /* Set it up here to make sure it's _always_ the same */
  $time           = time();
  // Turn time into strings
  switch($log_name) { 
	  case 'query':
      $log_day  = date('Ymd', $time);
    break;
    default:
      $log_day  = date('Ym',$time);
    break;
  }

  $log_time       = date('Y-m-d H:i:s', $time);

  /* must have some name */
  $log_name       = $log_name ? $log_name : 'general';
  $username       = $username ? $username : 'unknown';

  $log_filename   = Config::get('log_path') . "/$log_name.$log_day.log";
  $log_line       = "$log_time [$event_name] :: $event_description \n";

  // Do the deed
  $log_write = error_log($log_line, 3, $log_filename);

  if (!$log_write) {
    echo "Unable to write event to $log_filename\n";
  }

} // log_event

/*
 * ampache_error_handler
 * An error handler for ampache that traps as many errors as it can and logs
 * them.
*/
function ampache_error_handler($errno, $errstr, $errfile, $errline) {

        /* Default level of 1 */
        $level = 1;

        switch ($errno) {
                case E_WARNING:
                        $error_name = 'Runtime Error';
                break;
                case E_COMPILE_WARNING:
                case E_NOTICE:
                case E_CORE_WARNING:
                        $error_name = 'Warning';
                        $level = 6;
                break;
                case E_ERROR:
                        $error_name = 'Fatal run-time Error';
                break;
                case E_PARSE:
                        $error_name = 'Parse Error';
                break;
                case E_CORE_ERROR:
                        $error_name = 'Fatal Core Error';
                break;
                case E_COMPILE_ERROR:
                        $error_name = 'Zend run-time Error';
                break;
                case E_STRICT:
                        $error_name = "Strict Error";
                break;
                default:
                        $error_name = "Error";
                        $level = 2;
                break;
        } // end switch

        // List of things that should only be displayed if they told us to turn
        // on the firehose
        $ignores = array(
                // We know var is deprecated, shut up
                'var: Deprecated. Please use the public/private/protected modifiers',
                // getid3 spews errors, yay!
                'getimagesize() [',
                'Assigning the return value of new by reference is deprecated',
                // The XML-RPC lib is broken (kinda)
                'used as offset, casting to integer'
        );

        foreach($ignores as $ignore) {
                if (strpos($errstr, $ignore) !== false) {
                        $error_name = 'Ignored ' . $error_name;
                        $level = 6;
                }
        }

        if (strpos($errstr,"date.timezone") !== false) {
                $error_name = 'Warning';
                $errstr = 'You have not set a valid timezone (date.timezone) in your php.ini file. This may cause display issues with dates. This warning is non-critical and not caused by Ampache.';
        }

        $log_line = "[$error_name] $errstr in file $errfile($errline)";
	$username = is_object(\UI\sess::$user) ? \UI\sess::$user->username : 'Unknown';
		
        log_event($username,'PHP', $log_line,'error');

} // ampache_error_handler

/**
 * xml_from_array
 * This takes a one dimensional array and creates a XML document from it. For
 * use primarily by the ajax mojo.
 */
function xml_from_array($array, $callback = false, $type = '') {

        $string = '';

        // If we weren't passed an array then return
        if (!is_array($array)) { return $string; }

        // The type is used for the different XML docs we pass
        switch ($type) {
        case 'itunes':
                foreach ($array as $key=>$value) {
                        if (is_array($value)) {
                                $value = xml_from_array($value,1,$type);
                                $string .= "\t\t<$key>\n$value\t\t</$key>\n";
                        }
                        else {
                                if ($key == "key"){
                                $string .= "\t\t<$key>$value</$key>\n";
                                } elseif (is_int($value)) {
                                $string .= "\t\t\t<key>$key</key><integer>$value</integer>\n";
                                } elseif ($key == "Date Added") {
                                $string .= "\t\t\t<key>$key</key><date>$value</date>\n";
                                } elseif (is_string($value)) {
                                /* We need to escape the value */
                                $string .= "\t\t\t<key>$key</key><string><![CDATA[$value]]></string>\n";
                                }
                        }

                } // end foreach

                return $string;
        break;
        case 'xspf':
                foreach ($array as $key=>$value) {
                        if (is_array($value)) {
                                $value = xml_from_array($value,1,$type);
                                $string .= "\t\t<$key>\n$value\t\t</$key>\n";
                        }
                        else {
                                if ($key == "key"){
                                $string .= "\t\t<$key>$value</$key>\n";
                                } elseif (is_numeric($value)) {
                                $string .= "\t\t\t<$key>$value</$key>\n";
                                } elseif (is_string($value)) {
                                /* We need to escape the value */
                                $string .= "\t\t\t<$key><![CDATA[$value]]></$key>\n";
                                }
                        }

                } // end foreach

                return $string;
        break;
       default:
                foreach ($array as $key => $value) {
                        // No numeric keys
                        if (is_numeric($key)) {
                                $key = 'item';
                        }

                        if (is_array($value)) {
                                // Call ourself
                                $value = xml_from_array($value, true);
                                $string .= "\t<content div=\"$key\">$value</content>\n";
                        }
                        else {
                                /* We need to escape the value */
                                $string .= "\t<content div=\"$key\"><![CDATA[$value]]></content>\n";
                        }
                // end foreach elements
                }
                if (!$callback) {
                        $string = "<?xml version=\"1.0\" encoding=\"utf-8\" ?>\n<root>\n" . $string . "</root>\n";
                }

                // Remove invalid XML characters.
                // See http://www.w3.org/TR/2006/REC-xml-20060816/#charsets
                $clean = preg_replace('/[\x{0}-\x{8}\x{b}\x{c}\x{e}-\x{1f}\x{d800}-\x{dfff}\x{fffe}-\x{ffff}]/u', '', $string);

                if ($clean) {
                        return $clean;
                }
                else {
                        return $string;
                }
        break;
        }
} // xml_from_array


?>
