<?php

if ( ! defined('CONFIG_LOADED'))
{
  define('CONFIG_LOADED', TRUE);
  define('APPLICATION_DIR', 'app');
  define('DEBUG', TRUE);
}

$Fuuze_config = array(
  // URL routes 'url regex' => array('class name', 'action', 'class sub directory separated by _')
  'routes' => array(
    '/^\/$/'=>array('Site_Main', 'index', ''),
    '/^\/user\/join$/'=>array('Site_User', 'join', ''),
    '/^\/user\/login$/'=>array('Site_User', 'login', ''),
    ),
  // General config
  'hash_secret_key' => '\xb0\xbd\xd5d.\x1f\x9a\x82\x96\xee\x1aj\x0f\x1b(%G\xda\xd1_o"\xee\xe3', 
  // DB connect
  'db_connect' => array('pgsql:host=localhost;dbname=mobicmsdb', 'root', ''),
  );

return $Fuuze_config;