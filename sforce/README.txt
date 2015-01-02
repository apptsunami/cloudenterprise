2/26/2008

PREREQUISITE:

This version requires PHP v5.2+, Sforce API v15.0 and 
Salesforce.com PHP toolkit v13.0 which can be downloaded at 
http://wiki.apexdevnet.com/index.php/PHP_Toolkit.

INSTALLATION:

Install the Salesforce.com PHP toolkit into a directory $SS_DIR/'sforce'
and include $SS_DIR in the include_path of php.ini.  Alternatively you can 
add the following line at the beginning of your main php:

ini_set('include_path', ini_get('include_path').".:$SS_DIR);

(Replace '$SS_DIR' with the actual directory name.)

