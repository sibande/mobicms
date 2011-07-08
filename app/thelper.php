<?php
/**
 * Template helpers
 *
 * @package     mobicms
 * @subpackage  app
 * @author      Jose Sibande <jbsibande@gmail.com>
 */

class THelper
{
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
      if ( ! (bool) $size)
      {
	$size = '240';
      }
      return '/static/images/nopic'.$size.'.jpg';
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
   * Counts gallery files
   *
   * @param   int  user id
   * @return  int  number of files
   */
  static public function gallery_file_count($uid)
  {
    $db = Fuuze::connect_db();

    $files = $db->prepare('SELECT COUNT(*) AS file_count FROM files WHERE uid = :uid');
    $files->execute(array(':uid'=>$uid));
    $files = $files->fetch();

    return $files['file_count'];
  }

  /**
   * Counts the number of new unread messages
   *
   * @param   int  user id
   * @param   int  sending user id
   * @return  int  number of new messages
   */
  static public function new_messages($uid, $from_uid=NULL)
  {
    $db = Fuuze::connect_db();

    if ( ! $from_uid)
    {
      $messages = $db->prepare('SELECT count(*) AS new_count FROM message WHERE receiver_id=:uid AND is_read=FALSE');
      $messages->execute(array(':uid'=>$uid));
    }
    else
    {
      $messages = $db->prepare('SELECT count(*) AS new_count FROM message '.
			       'WHERE receiver_id=:uid AND sender_id=:from_uid AND is_read=FALSE');
      $messages->execute(array(':uid'=>$uid, ':from_uid'=>$from_uid));
    }
    $messages = $messages->fetch();

    return $messages['new_count'];
  }
  
  /**
   * Mark message as has been read
   *
   * @param   int   message id
   * @param   int   receiver id
   * @return  void
   */
  static public function mark_as_read($message_id, $uid)
  {
    $db = Fuuze::connect_db();

    $message = $db->prepare('SELECT * FROM message WHERE id=:id AND receiver_id=:receiver_id AND is_read=FALSE');
    $message->execute(array(':id'=>$message_id, ':receiver_id'=>$uid));

    if ( (bool) $message->fetch())
    {

      $message = $db->prepare('UPDATE message SET is_read=TRUE WHERE id=:id AND receiver_id=:receiver_id');
      $message = $message->execute(array(':id'=>$message_id, ':receiver_id'=>$uid));
    }
    
    return ;
  }

}