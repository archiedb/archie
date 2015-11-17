### Archie Helper Applications (CLI)

```backup/local-backup```

  Python script for backing up data to a local directory, reads the backup/backup.cfg.php
  file for settings. See the backup.cfg.php.dist for possible settings

```build-scatter-plots [LEVEL UID]```

  PREREQ - python-matplotlib, python-mysqldb, imagemagick (convert)

  Using matplotlib this script takes the cords entered for the objects located in the level and
  creates 3 scatterplots of the data (X,Y) (X,Z) (Y,Z). These images are generated and saved
  in the content directory and then assoicated with the Level. This script can be scheduled
  to be run in the web interface or via cron. If it is run without arugments all level images
  are re-generated.

  The pre-reqs for this script are checked in the web interface under Manage -> Status
 
```build-changelog.sh```

  Simple script to auto-generate a changelog, used for development

```reset-password.php.inc [username] [password]```

  Resets the password for a web interface user, takes two arguments
  first is username, second is password. Must be run as follows

  php reset-password.php.inc USERNANE NEWPASSWORD

```validate-records.php.inc```

  Command Line script that looks at Archie Database and attempts returns any records
  which do not meet current data integrity requirements. This can also be used to
  test for upgrade compatability

```update-archie```

  Check for local edits, if there are none do a git pull
 
### Scripts to be run from CRON

```report.cron.php.inc```

  Creates the CVS files and other related reporting functionality that can take
  a lot of proccessing, this should be run from cron, and is scheduled via the
  web interface

```task.cron.php.inc```

  Regenerates QRcodes and other supplimentary data generation tasks, this 
  should be run from cron and is scheduled via the web interface

### Migration Scripts

```legacy/import.records.php.inc,  legacy/migrate.2013-2014.php.inc```

  These are older scripts which were used to import or translate data beteween
  versions of ARCHIE they are not used, but are left for historical record
  so that we can trace back what was done to the data.
