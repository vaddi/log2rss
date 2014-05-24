# log2rss #

A simple PHP RSS parser who read log files (from a included cron job) and parse the last to an RSS Feed.


## Dependencies ##

*  [PHP][]4 better [PHP][]5
*  PHP XML-[PHP Dom][]
*  Apache Webserver-[Apache][]


## Installation ##

Get last version from github.com by following command:

    git clone git://github.com/vaddi/log2rss.git log2rss

Just open the url in you Browser and follow the instructions from the installer. It checks for Path, Files, url and a few neccessary php functions. After anything ist "greenish" there's a instruction how to handle the cronjob file (which location) and as a hint, how to setup as a minutly cronjob. The installer script will give you examples of how to tell the cron job which log will be parsed.

Depending on Security issues it will only grep the neccessary log entries to the given webfolder/tmp.log location so that the php script needs access to the included tmp.log file and cannot get sensitive data from you logfiles.


## Credits ##

[Apache]: http://httpd.apache.org/
[PHP Dom]: http://de.php.net/manual/en/book.dom.php
[PHP]: http://php.net/

