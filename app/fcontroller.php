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
    
    $this->_extra_twig_options();
    
    $this->user_object();
  }
  
  public function _extra_twig_options()
  {
    $this->_twig_env->addFunction('remove_name_ext', new Twig_Function_Function('FHelper::remove_name_ext'));
    $this->_twig_env->addFunction('get_file_url', new Twig_Function_Function('FHelper::file_url'));
  }

  public function user_object()
  {
    if ( ! array_key_exists('user_id', $_SESSION))
    {
      $_SESSION['user_id'] = NULL;
      $user = array(array());
    }
    $this->data['user']['is_authenticated'] = ((bool) $_SESSION['user_id']);

    $user_id = $_SESSION['user_id'];
    $users = $this->db->prepare('SELECT id, username, email, udatetime, datetime '.
				'FROM users WHERE id = :id LIMIT 1;');
    $users->execute(array(':id' => $user_id));
    $user = $users->fetch();
    $this->data['user']['object'] = $user;
  }
  
}