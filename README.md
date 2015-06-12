ARCHIE
=======

An open source digitial inventory system designed for field and lab use by
archelogists.  

Build Status: MASTER 
![Master Branch](https://travis-ci.org/archiedb/archie.svg?branch=master "Master Branch")

Contents
--------

  1. License
  2. Contact Info
  3. Dependencies
 
1. License

  This Application falls under the Standard GPL v2. See Licence 
  included with this tar file. Credit for code is listed in the 
  changelog, or in the respective files.  

2. Contact Info

  Hate it?  Love it?  Let us know.  Let us know if you think of any
    more features, bugs, etc.

 * Public Source Repository: [GitHub Repo](https://github.com/archiedb/archie)
 * Website: [Archiedb.com](http://archiedb.com)
 * E-mail: [contact@archiedb.com](mailto:contact@archiedb.com)

3. Dependencies

	* Webserver that supports
	  * rewrites
	  * php
	* MySQL
	* PHP 5.1+ with 
	  * PHP-GD
	  * PHP-MySQL
	* Cron or method to schedule regular script runs
	* BASH shell for CLI scripts

4. Cronjobs

  Automated Report generation and QRCode re-generation requires you to setup a 
  cron job that checks for outstanding requests It's recommend to run this 
  every 5 min. It will not launch a second copy if the process is already 
  running.

  Your two cron-jobs should look something like this
<pre>
  */5 * * * * www-data php /var/www/bin/report.cron.php.inc
  */5 * * * * www-data php /var/www/bin/task.cron.php.inc
</pre>

5. QRCode Cache Directory

  To help improve performance of the QRCodes, it's recommended to enable write
  access to ./lib/phpqrcode/cache for the webserver it will work without this 
  access, but it makes things a little faster.

6. 3d Model to PNG Programs

  We use stl2pov nad megapov to convert 3d model files to a thumbnail
  for preview, this is not reqiured for Archie to work, it just makes
  things a little prettier. 

