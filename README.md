-------------------------------------------------------------------------------
---------                 README - Archie - 0.0.1-RC3               -----------
-------------------------------------------------------------------------------

Contents:

  1. License
  2. Contact Info
 
1. License

  This Application falls under the Standard GPL v2. See Licence 
  included with this tar file. Credit for code is listed in the 
  changelog, or in the respective files.  

2. Contact Info

  Hate it?  Love it?  Let us know.  Let us know if you think of any
    more features, bugs, etc.

  Public Source Repository: https://github.com/vollmerk/archie
  E-mail: contact@archiedb.com
  Web: http://archiedb.com

 3. Cronjobs
 
  Automated Report generation and QRCode re-generation requires
  you to setup a cron job that checks for outstanding requests
  It's recommend to run this every 5 min. It will not launch
  a second copy if the process is already running. 

  A crontab entry would look something like

  */5 * * * * www-data php /var/www/bin/report.cron.php.inc

 4. QRCode Cache Directory

  To help improve performance of the QRCodes, it's recommended
  to enable write access to ./lib/phpqrcode/cache for the webserver
  it will work without this access, but it makes things a 
  little faster. 
