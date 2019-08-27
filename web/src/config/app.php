<?php
define('DB_HOST',       'postgres'); //inside docker network
define('DB_HOST_DEV',   '127.0.0.1'); //outside docker network
define('DB_PORT',       5432);
define('DB_NAME',       'hellofresh');
define('DB_USER',       'hellofresh');
define('DB_PASSWORD',   'hellofresh');
define('DB_DRIVER',     'pgsql');
define('DB_CHARSET',    'utf8');
define('DB_COLLATION',  'utf8_unicode_ci');
define('JWT_SECRET',    'hfnat050818');
