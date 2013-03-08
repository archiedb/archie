<?php 
/* vim:set tabstop=8 softtabstop=8 shiftwidth=8 noexpandtab: */

class image { 

	public $uid; 
	public $filename; // data in the database
	public $thumb_filename; 
	public $raw; 
	public $record; 
	public $type; 


	// Constructor
	public function __construct($uid) { 

		$uid = Dba::escape($uid); 
		$sql = "SELECT * FROM `image` WHERE `uid`='$uid'";
		$db_results = Dba::read($sql); 

		$results = Dba::fetch_assoc($db_results); 

		foreach ($results as $key=>$value) { 
			$this->$key = $value; 
		} 

		// Translate
		$this->filename = $this->data; 
		$this->thumb_filename = $this->data . '.thumb'; 

	} // uid

	/* 
	 * generate_thumb
	 * This makes a new thumbnail, happens when we upload an image
	 */
	public static function generate_thumb($raw,$size=array(),$type) { 


		if (!self::test_image($raw)) { 
			Event::error('Image','Unable to generate thumbnail, invalid image passed'); 
			return false; 
		} 


		if (!function_exists('gd_info')) { 
			Event::error('Image','PHP-GD not found, unable to generate thumbnail'); 
			return false; 
		} 

		if (($type == 'jpg' OR $type == 'jpeg') AND !(imagetypes() & IMG_JPG)) { 
			Event::error('Image','PHP-GD Does not support JPGs unable to generate thumbnail'); 
			return false; 
		} 

		if ($type == 'png' AND !imagetypes() & IMG_PNG) { 
			Event::error('Image','PHP-GD Does not support PNGs unable to generate thumbnail'); 
			return false; 
		} 
		

		$image = ImageCreateFromString($raw); 

		if (!$image) { 
			Event::error('Image','Failed to create image from raw string, source is malformed'); 
			return false; 
		} 		

		// We want to maintain the aspect ration, so figure out our new dimensions within passed constraints
		$image_size = array('height'=>imagesy($image), 'width'=>imagesx($image)); 

		$ratio = $image_size['height'] / $image_size['width']; 

		$resized = array(); 

		if ($image_size['height'] > $image_size['width']) { 
			$resized['height'] = $size['height']; 
			$resized['width'] = $size['height']/$ratio; 
		}
		elseif ($image_size['width'] > $image_size['height']) { 
			$resized['width'] = $size['width']; 
			$resized['height'] = $size['width'] * $ratio; 
		}
		else { 
			$resized = $size; 
		}
		
		// Create blank image of requested size
		$thumbnail = ImageCreateTrueColor($resized['width'],$resized['height']); 

		if (!ImageCopyResampled($thumbnail,$image,0,0,0,0,$resized['width'],$resized['height'],$image_size['width'],$image_size['height'])) { 
			Event::error('Image','Unable to resize thumbnail, PHP-GD failure'); 
			return false; 
		}

		// Start the output buffer, we're doing a little trick here
		ob_start(); 

		switch ($type) { 
			case 'jpg': 
			case 'jpeg': 
				ImageJpeg($thumbnail,null,75); 
			break; 
			case 'png': 
				ImagePng($thumbnail); 
			break; 
		}  	

		$thumb_raw = ob_get_contents(); 
		ob_end_clean(); 

		if (!strlen($thumb_raw)) { 
			Event::error('Image','Unknown Error generating thumbnail'); 
			return false; 
		} 

		return $thumb_raw; 


	} // generate_thumb 

	/** 
	 * test_image
	 * Run some basic test to make sure that they've actually 
	 * tried to upload an image
	 */ 
	public static function test_image($raw) { 

		if (strlen($raw) < 10) { 
			Event::error('Image','Invalid image passed, less then 10 characters long!!!'); 
			return false; 
		} 

		// Use PHP:GD to do the rest of the tests
		if (function_exists('ImageCreateFromString')) { 
			$image = ImageCreateFromString($raw); 
			// Make sure the function worked and the image is at least 5x5 px
			if (!$image || imagesx($image) < 5 || imagesy($image) < 5) { 
				Event::error('Image','Image failed PHP-GD test, or is less then 5x5'); 
				return false; 
			} 
		} 

		return true; 

	} // test_image

} // image
?>
