<?php

if ( ! defined('CONFIG_LOADED'))
{
  define('CONFIG_LOADED', TRUE);
  define('DEBUG', TRUE);
  define('FUUZE_DATA_PER_PAGE', 25);
}

$Fuuze_config = array(
  // URL routes 'url regex' => array('class name', 'action', 'class sub directory separated by _')
  'routes' => array(
    '/^\/$/' => array('Site_Main', 'index', ''),
    '/^\/user\/join\/?$/' => array('Site_User', 'join', ''),
    '/^\/user\/login\/?$/' => array('Site_User', 'login', ''),
    '/^\/user\/logout\/?$/' => array('Site_User', 'logout', ''),
    '/^\/user\/(?P<username>\w+)\/?$/' => array('Site_Profile', 'view', ''),
    '/^\/chat\/?$/' => array('Site_Chat', 'index', ''),
    '/^\/chat\/(?P<chatroom>[a-zA-Z-_]+)\/?$/' => array('Site_Chat', 'chat', ''),
    '/^\/discussion\/?$/' => array('Site_Discussion', 'index', ''),
    '/^\/discussion\/(?P<forum>[a-zA-Z-_]+)\/?$/' => array('Site_Discussion', 'discussion', ''),
    '/^\/discussion\/(?P<forum>[a-zA-Z-_]+)\/add\/?$/' => array('Site_Discussion', 'add', ''),
    '/^\/discussion\/topic\/(?P<topic_id>\d+)\/?$/' => array('Site_Discussion', 'topic', ''),
    ),
  // General config
  'hash_secret_key' => '\xb0\xbd\xd5d.\x1f\x9a\x82\x96\xee\x1aj\x0f\x1b(%G\xda\xd1_o"\xee\xe3', 
  // DB connect
  'db_connect' => array('pgsql:host=localhost;dbname=mobicmsdb', 'root', ''),
  );

return $Fuuze_config;