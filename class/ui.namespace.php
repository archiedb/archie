<?php namespace UI;
// vim: set softtabstop=2 ts=2 sw=2 expandtab: 

/**
 * redirect
 * Uses header Location: to redirect
 * FIXME: check path to make sure it's allowed
 */
function redirect($path) {

  header('Location:' . \Config::get('web_path') . $path);
  exit; 

} // redirect

/**
 * return_url
 * We have a specific list of urls we're allowed to redirect to!
 */
function return_url($input) {

  // We need to keep the original intact here, but #'s of an ID at the end are ok
  $check = rtrim($input,'0..9'); 
  $allowed_urls = array('/records/view/',
                    '/records/edit/',
                    '/users/view',
                    '/level/edit/',
                    '/level/view/',
                    '/manage/site/view',
                    '/krotovina/edit/',
                    '/krotovina/view/',
                   '/users/manage/disabled',
                  '/users/manage/online',
                  '/users/manage',
                  '/users/manage/all',
                   '/feature/edit/',
                    '/feature/view/'); 

  if (in_array($check,$allowed_urls)) { 
    return $input;
  }

  return '/'; 

} // return url

/**
 * access_denied
 * Throw up "DO NOT PASS GO" and exit
 */
function access_denied($msg='') {

  include_once template('/header');
  include_once template('/menu');
  $header = '<h4>Error: Insufficient Access</h4>';
  $size = ' alert-block';
  $css_class = ' alert-error alert-danger';
  $message = strlen($msg) ? $msg : 'Unable to continue you do not have sufficient access to perform this action';
  require template('/event');
  include_once template('/footer');
  exit;

} // access_denied

/**
 * build a view link based on the uid and type
 * if there's no uid passed (0/null) then return
 * just ''
 */
function record_link($uid,$type,$text='') {

  $type_map = array(
    'record'=>'records',
    'level'=>'level',
    'user'=>'users',
    'feature'=>'feature',
    'krotovina'=>'krotovina',
  );

  if (empty($uid)) { return ''; }
  if (empty($text)) { $text = $uid; }
  if (empty($type_map[$type])) { return $uid; }

  $url = \Config::get('web_path') . '/' . $type_map[$type] . '/view/' . scrub_out($uid);

  $return = '<a href="' . $url . '" title="View Record" alt="View Record">' . scrub_out($text) . '</a>';

  return $return;

} // record_link

/** 
 * build a search link
 */
function search_link($field,$value,$text='') {

    if (empty($field)) { return $text; }
    if (empty($text)) { $text = $value; }
    if (empty($value)) { return $text; }

    $url = \Config::get('web_path') . '/record/search/' . $field . '/'  . scrub_out($value);
    return '<a href="' . $url . '">' . scrub_out($text) . '</a>';

} // search_link

/** 
 * form_value
 * Look in _POST, ${variable} or passed and output it if it exists
 */
function form_value($name,$return=false) { 

    $form_value = '';

    if (is_array($name)) {
      foreach ($name as $field=>$value) {
        switch ($field) {
          case 'post':
            if (!empty($_POST[$value])) {
              $form_value = $_POST[$value];
              break 2;
            }
          break;
          case 'get':
            if (!empty($_GET[$value])) {
              $form_value = $_GET[$value];
              break 2;
            }
          break;
          case 'var':
            if (!empty($value)) {
              $form_value = $value;
              break 2;
            }
          break;
        }
      } // end foreach
      if ($return) {
        return $form_value;
      }
      echo scrub_out($form_value);
      return true; 
    } // end if array
          
    if (empty($_POST[$name])) { echo ''; return; }
    
    if ($return) {
        return $_POST[$name];
    }

    echo scrub_out($_POST[$name]); 
    
} // form_value

/**
 * sort_icon
 * Takes a field and sort (ASC/DESC), and returns the html for the correct icon baesd on its current sort
 * state
 */
function sort_icon($sort) {

  // Easy!
  if (!$sort) { return ''; }

  if ($sort == 'ASC') { 
    $return = '<span class="glyphicon glyphicon-chevron-down"></span>';
  }
  else { 
    $return = '<span class="glyphicon glyphicon-chevron-up"></span>';
  }

  return $return;

} // sort_icon

/**
 * field_name
 * Takes a field, and returns the "name" for it (these don't match :S)
 */
function field_name($field) { 

  $names = array('catalog_id'=>'Catalog #',
      'station_index'=>'RN',
      '3dmodel'=>'3D Model',
      'xrf_matrix_index'=>'XRF Matrix',
      'xrf_artifact_index'=>'XRF Artifact',
      'lsg_unit'=>'L.U',
      'elv_nw_start'=>'NW Elevation Start',
      'elv_ne_start'=>'NE Elevation Start',
      'elv_sw_start'=>'SW Elevation Start',
      'elv_se_start'=>'SE Elevation Start',
      'elv_center_start'=>'Center Elevation Start',
      'elv_nw_finish'=>'NW Elevation Finish',
      'elv_ne_finish'=>'NE Elevation Finish',
      'elv_sw_finish'=>'SW Elevation Finish',
      'elv_se_finish'=>'SE Elevation Finish',
      'elv_center_finish'=>'Center Elevation Finish'
      );

  if (in_array($field,array_keys($names))) { return $names[$field]; }

  $field = str_replace('_',' ',$field);

  return ucfirst($field); 

} // field_name

/**
 * boolean_word
 * Take a T/F value and return a pretty response
 */
function boolean_word($boolean,$string='') { 

  if ($string == '') { 
    $string = $boolean ? 'True' : 'False';
  }

  if ($boolean) { 
    return '<span class="label label-success">' . $string . '</span>';
  }
  else {
    //FIXME: Remove label-important, bootstrap 2.x compatibility
    return '<span class="label label-danger label-important">' . $string . '</span>';
  }

  return false; 

} // boolean_word

/**
 * print_var
 * Takes a PHP var/array/whatever and prints it out in a way we want
 */
function print_var($input) { 
  
  $output = NULL;

  if (is_array($input)) { 
    foreach ($input as $value) { 
      $output .= $value . ',';
    }
    $output = trim($output,',');
  }
  else { 
    $output = $input;
  }
  echo $output; 

} // print_var

/**
 * template
 * Returns the full filename for the template
 * uses sess::location() to figure it out
 */
function template($name='') { 

    if (strlen($name)) {
      return \Config::get('prefix') . '/template' . $name . '.inc.php'; 
    }

    $filename = \Config::get('prefix') . '/template'; 
    $filename .= sess::location('page') ? '/' . sess::location('page') : '';
    $filename .= sess::location('action') ? '/' . sess::location('action') : ''; 

    // Add extension
    $filename .= '.inc.php';

    return $filename;     

} // template

/**
 * sess
 * This is a static class that holds our current session state, not sure if 
 * this is the right way to do it, but it's better then globals
 */
class sess {

  public static $user; // our currently logged in user
  private static $location=array(); // clean-url stuff

  private function __construct() {}
  private function __clone() { }

  public static function set_user($user) { 
    // Only users here!
    if (get_class($user) != 'User') { return false; }

    self::$user = $user; 

    // Hardcode the site FIX WITH DB CHANGE
    if (!is_object(self::$user->site)) {
      self::$user->site = new \Site(1); 
    }

    return true; 

  } // set user

  /**
   * set_location
   * takes care of parsing out our url
   */
  public static function set_location($uri) { 

    $urlvar = explode('/',$uri);
    $www_prefix = explode('/',rtrim(\Config::get('web_prefix'),'/'));
    foreach ($www_prefix as $prefix) { 
      array_shift($urlvar); 
    } 

    self::$location = $urlvar;

  } // set_location

  /**
   * location
   * return the specified part of the clean url
   */
  public static function location($section) { 

    switch ($section) { 
      case '0':
      case 'page':
        return isset(self::$location['0']) ? self::$location['0'] : ''; 
      break;
      case '1':
      case 'action':
        return isset(self::$location['1']) ? self::$location['1'] : false;
      break;
      case '2':
      case 'object':
      case 'objectid':
        return isset(self::$location['2']) ? self::$location['2'] : false;
      break;
      case '3':
        return isset(self::$location['3']) ? self::$location['3'] : false;
      break;
      case '4': 
        return isset(self::$location['4']) ? self::$location['4'] : false;
      break; 
      case 'absolute':
        $page = isset(self::$location['0']) ? self::$location['0'] : '';
        $action = isset(self::$location['1']) ? self::$location['1'] : '';
        $objectid = isset(self::$location['2']) ? self::$location['2'] : '';
        return rtrim('/' . $page . '/' . $action . '/' . $objectid,'/'); 
      break; 
    }

    return false; 

  } // url

} // sess

/**
 * function by Wes Edling .. http://joedesigns.com
 * feel free to use this in any project, i just ask for a credit in the source code.
 * a link back to my site would be nice too.
 *
 *
 * Changes: 
 * 2012/01/30 - David Goodwin - call escapeshellarg on parameters going into the shell
 * 2012/07/12 - Whizzkid - Added support for encoded image urls and images on ssl secured servers [https://]
 */

/**
 * SECURITY:
 * It's a bad idea to allow user supplied data to become the path for the image you wish to retrieve, as this allows them
 * to download nearly anything to your server. If you must do this, it's strongly advised that you put a .htaccess file 
 * in the cache directory containing something like the following :
 * <code>php_flag engine off</code>
 * to at least stop arbitrary code execution. You can deal with any copyright infringement issues yourself :)
 */

/**
 * @param string $imagePath - either a local absolute/relative path, or a remote URL (e.g. http://...flickr.com/.../ ). See SECURITY note above.
 * @param array $opts  (w(pixels), h(pixels), crop(boolean), scale(boolean), thumbnail(boolean), maxOnly(boolean), canvas-color(#abcabc), output-filename(string), cache_http_minutes(int))
 * @return new URL for resized image.
 */
function resize($imagePath,$opts=null){
	$imagePath = urldecode($imagePath);
	# start configuration
	$cacheFolder = \Config::get('prefix') . '/lib/cache/'; # path to your cache folder, must be writeable by web server
	$remoteFolder = $cacheFolder.'remote/'; # path to the folder you wish to download remote images into

	$defaults = array('crop' => false, 'scale' => 'false', 'thumbnail' => false, 'maxOnly' => false, 
	   'canvas-color' => 'transparent', 'output-filename' => false, 
	   'cacheFolder' => $cacheFolder, 'remoteFolder' => $remoteFolder, 'quality' => 90, 'cache_http_minutes' => 20);

	$opts = array_merge($defaults, $opts);    

	$cacheFolder = $opts['cacheFolder'];
	$remoteFolder = $opts['remoteFolder'];
	
  // Set Default convert path
  $path_to_convert = '/usr/bin/convert'; # this could be something like /usr/bin/convert or /opt/local/share/bin/convert

  // Try to find convert
  $convert_paths = array('/usr/bin/convert','/opt/local/bin/convert');
  foreach ($convert_paths as $convert_path) { 
    if (is_executable($convert_path)) {
      $path_to_convert = $convert_path;
      break 1;
    }
  } // foreach possible location

	
	## you shouldn't need to configure anything else beyond this point

	$purl = parse_url($imagePath);
	$finfo = pathinfo($imagePath);
	$ext = $finfo['extension'];

	# check for remote image..
	if(isset($purl['scheme']) && ($purl['scheme'] == 'http' || $purl['scheme'] == 'https')):
		# grab the image, and cache it so we have something to work with..
		list($filename) = explode('?',$finfo['basename']);
		$local_filepath = $remoteFolder.$filename;
		$download_image = true;
		if(file_exists($local_filepath)):
			if(filemtime($local_filepath) < strtotime('+'.$opts['cache_http_minutes'].' minutes')):
				$download_image = false;
			endif;
		endif;
		if($download_image == true):
			$img = file_get_contents($imagePath);
			file_put_contents($local_filepath,$img);
		endif;
		$imagePath = $local_filepath;
	endif;

	if(file_exists($imagePath) == false):
		$imagePath = $_SERVER['DOCUMENT_ROOT'].$imagePath;
		if(file_exists($imagePath) == false):
			return 'image not found';
		endif;
	endif;

	if(isset($opts['w'])): $w = $opts['w']; endif;
	if(isset($opts['h'])): $h = $opts['h']; endif;

	$filename = md5_file($imagePath);

	// If the user has requested an explicit output-filename, do not use the cache directory.
	if(false !== $opts['output-filename']) :
		$newPath = $opts['output-filename'];
	else:
        if(!empty($w) and !empty($h)):
            $newPath = $cacheFolder.$filename.'_w'.$w.'_h'.$h.(isset($opts['crop']) && $opts['crop'] == true ? "_cp" : "").(isset($opts['scale']) && $opts['scale'] == true ? "_sc" : "").'.'.$ext;
        elseif(!empty($w)):
            $newPath = $cacheFolder.$filename.'_w'.$w.'.'.$ext;	
        elseif(!empty($h)):
            $newPath = $cacheFolder.$filename.'_h'.$h.'.'.$ext;
        else:
            return false;
        endif;
	endif;

	$create = true;

    if(file_exists($newPath) == true):
        $create = false;
        $origFileTime = date("YmdHis",filemtime($imagePath));
        $newFileTime = date("YmdHis",filemtime($newPath));
        if($newFileTime < $origFileTime): # Not using $opts['expire-time'] ??
            $create = true;
        endif;
    endif;

	if($create == true):
		if(!empty($w) and !empty($h)):

			list($width,$height) = getimagesize($imagePath);
			$resize = $w;
		
			if($width > $height):
				$resize = $w;
				if(true === $opts['crop']):
					$resize = "x".$h;				
				endif;
			else:
				$resize = "x".$h;
				if(true === $opts['crop']):
					$resize = $w;
				endif;
			endif;

			if(true === $opts['scale']):
				$cmd = $path_to_convert ." ". escapeshellarg($imagePath) ." -resize ". escapeshellarg($resize) . 
				" -quality ". escapeshellarg($opts['quality']) . " " . escapeshellarg($newPath);
			else:
				$cmd = $path_to_convert." ". escapeshellarg($imagePath) ." -resize ". escapeshellarg($resize) . 
				" -size ". escapeshellarg($w ."x". $h) . 
				" xc:". escapeshellarg($opts['canvas-color']) .
				" -depth 8 +swap -gravity center -composite -quality ". escapeshellarg($opts['quality'])." ".escapeshellarg($newPath);
			endif;
						
		else:
			$cmd = $path_to_convert." " . escapeshellarg($imagePath) . 
			" -thumbnail ". (!empty($h) ? 'x':'') . $w ."". 
			(isset($opts['maxOnly']) && $opts['maxOnly'] == true ? "\>" : "") . 
			" -quality ". escapeshellarg($opts['quality']) ." ". escapeshellarg($newPath);
		endif;

		$c = exec($cmd, $output, $return_code);
        if($return_code != 0) {
            error_log("Tried to execute : $cmd, return code: $return_code, output: " . print_r($output, true));
            // This seems to be a false positive in some cases still not sure why!
    //        return false;
		}
	endif;
	# return cache file path
	return str_replace(\Config::get('prefix'),'',$newPath);
	
}
