;<?php exit; ?>
[main]
; Current Config version, used internally to determine if config file needs to be updated
config_version=0.02

; The path to your Archie install, if Archie is installed in a sub-directory (http://localhost/archie) then 
;; The value of this should be /archie. If it's installed in the root then leave it blank
web_path=

; Ticket size for Catalog item tags
;; Current options 88x25mm and 57x32mm
ticket_size=88x25mm

; Path where Archie will try to log events, must be writeable by the webserver
;; This can contain sensitive information, and should not be inside the web space
log_path=/var/log/archie

; This is the root of the attached/fs data
data_root=/var/www/content

; Operational stuff
;; This is the number of records per page
page_limit = 250

; Use RAM to avoid MySQL queries
; It is recommended to turn this on, only turn it off if
; you are getting out of memeory errors
memory_cache = true 

; STL2POV command
; This is required for the 3d model to png
stl2pov_cmd = /usr/local/bin/stl2pov

; Megapov command
; This is required for the 3d model to png
megapov_cmd = /usr/local/megapov/megapov

; Database Related Settings
database_username=root
database_password=
database_hostname=localhost
database_name=archie

; Session related settings, don't change this unless you know
; what you're doing
session_name=archie
remember_length=144000
session_length=86400
session_cookielife=0
session_cookiesecure=0

