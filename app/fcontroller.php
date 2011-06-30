<?php
/*
 * Add extra methods to fuuze
 *
 * @package     mobicms
 * @subpackage  app
 * @author      Jose Sibande <jbsibande@gmail.com>
 */

class FController extends Fuuze
{
  
  // template vars
  public $data = array();

  public function __construct()
  {
    parent::__construct();
    
    $this->user_object();
  }
  
  public function is_authenticated()
  {
    
  }
  public function user_object()
  {
    if ( ! array_key_exists('user_id', $_SESSION))
    {
      $_SESSION['user_id'] = NULL;
    }
    $this->data['user']['is_authenticated'] = ((bool) $_SESSION['user_id']);

    $user_id = $_SESSION['user_id'];
    $users = $this->db->prepare('SELECT id, username, email, udatetime, datetime '.
				'FROM users WHERE id = :id;');
    $users->execute(array(':id' => $user_id));
    $user = $users->fetchAll();

    $this->data['user']['object'] = $user;
  }
}