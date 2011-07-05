<?php
/**
 * Code snippets and template helpers
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
   * Creates file url
   *
   * @param   string  file id
   * @param   string  image file size, returns original file if set to == 0
   * @return  string  web file path
   */
  static public function file_url($file_id, $size=176)
  {
    $db = Fuuze::connect_db();
    $file = $db->query('SELECT f.id, f.file, f.original, u.username FROM files f '.
		       'INNER JOIN users u ON (f.uid = u.id) WHERE f.id=\''.(int) $file_id.'\'');
    $file = $file->fetch();
    
    if ( ! (bool) $file)
    {
      return '';
    }
    // see app/site/gallery.php on how file name is constructed
    $file_ext = explode('.', $file['original']);
    $file_ext = $file_ext[count($file_ext)-1];
    $file_name = $file['file'].'-'.$file['id'].'.'.strtolower($file_ext);
    
    if ( ! (bool) $size)
    {
      return '/static/gallery/'.strtolower($file['username']).'/'.$file_name;
    }
    else
    {
      return '/static/gallery/'.strtolower($file['username']).'/'.$size.'/'.$file_name;
    }
  }

  /**
   * Removes extension from file name
   *
   * @param   string  file name
   * @return  string  name without extension
   */
  static public function remove_name_ext($name)
  {
    $name = explode('.', $name);

    return implode('.', array_slice($name, 0, count($name)-1));
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