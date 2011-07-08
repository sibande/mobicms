<?php
/**
 * Project helpers.
 *
 * @package     mobicms
 * @subpackage  app
 * @author      Jose Sibande <jbsibande@gmail.com>
 */

class FHelper
{
  /**
   * Creates many directories at once.
   *
   * @param   string  path where these dirs should be created
   * @param   array   list of dirs to be created
   * @return  void
   */
  static public function mkdirs($path, $dirs=array())
  {
    foreach ($dirs as $dir)
    {
      $structure = $path.'/'.$dir;
      if ( ! is_dir($structure))
      {
	mkdir($structure, 0777, TRUE);
      }
    }
  }

  /**
   * Stores temporary flash data.
   *
   * @return  void
   */
  static public function set_flash($key, $value)
  {
    if ( ! isset($_SESSION['_fuuze_flash_object']))
    {
      $_SESSION['_fuuze_flash_object'] = array();
    }
    
    $_SESSION['_fuuze_flash_object'][$key]['value'] = $value;
    $_SESSION['_fuuze_flash_object'][$key]['url'] = $_SERVER['REQUEST_URI'];
    $_SESSION['_fuuze_flash_object'][$key]['count'] = 0;
  }

  /**
   * Manages flash data.
   *
   * @return void
   */
  static public function flash()
  {
    if ( ! isset($_SESSION['_fuuze_flash_object']))
    {
      return;
    }
    //var_dump($_SESSION['_fuuze_flash_object']);
    foreach ($_SESSION['_fuuze_flash_object'] as $k=>$data)
    {
      $_SESSION['_fuuze_flash_object'][$k]['count']++;
      if ($_SESSION['_fuuze_flash_object'][$k]['count'] > 1)
      {
	unset($_SESSION['_fuuze_flash_object'][$k]);
      }
    }
  }
  
  /**
   * Gets flash message.
   *
   * @param   string  flash key
   * @return          flash data
   */
  static public function get_flash($key)
  {
    if (isset($_SESSION['_fuuze_flash_object'][$key]))
    {
      return $_SESSION['_fuuze_flash_object'][$key]['value'];
    }
    return NULL;
  }

  /**
   * Replaces and translates unwanted characters leaving [a-zA-Z_-]
   * Taken from the Symfony jobeet tutorial.
   *
   * @param   string  text
   * @return  string  slug text
   */
  static public function slugify($text)
  {
    // replace non letter or digits by -
    $text = preg_replace('#[^\\pL\d]+#u', '-', $text);
    // trim
    $text = trim($text, '-');
    // transliterate
    if (function_exists('iconv'))
    {
      $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    }
    // lowercase
    $text = strtolower($text);
    // remove unwanted characters
    $text = preg_replace('#[^-\w]+#', '', $text);
    if (empty($text))
    {
      return 'n-a';
    }
    return $text;
  }

}