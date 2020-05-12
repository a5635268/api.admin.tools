#!/bin/bash
/usr/bin/php /home/wwwroot/XGservice/think optimize:autoload
/usr/bin/php /home/wwwroot/XGservice/think optimize:config
/usr/bin/php /home/wwwroot/XGservice/think optimize:schema
/usr/bin/php /home/wwwroot/XGservice/think optimize:route
