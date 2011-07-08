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

    //purge flash data
    FHelper::flash();
  }
  
  public function _extra_twig_options()
  {
    //Functions
    $this->_twig_env->addFunction('remove_name_ext', new Twig_Function_Function('THelper::remove_name_ext'));
    $this->_twig_env->addFunction('get_file_url', new Twig_Function_Function('THelper::file_url'));
    $this->_twig_env->addFunction('get_flash', new Twig_Function_Function('FHelper::get_flash'));
    $this->_twig_env->addFunction('gallery_file_count', new Twig_Function_Function('THelper::gallery_file_count'));

    //Filters
    $this->_twig_env->addFilter('new_messages', new Twig_Filter_Function('THelper::new_messages'));
    $this->_twig_env->addFilter('mark_as_read', new Twig_Filter_Function('THelper::mark_as_read'));
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
    $users = $this->db->prepare('SELECT u.id, u.username, u.email, u.udatetime, u.datetime, '.
				'p.birthdate, p.sex, p.country, p.city, p.profile_pic AS profile_pic_id '.
				'FROM users u LEFT OUTER JOIN profile p ON (u.id=p.uid) WHERE u.id = :id LIMIT 1;');
    $users->execute(array(':id' => $user_id));
    $user = $users->fetch();
    $this->data['user']['data'] = $user;
  }
  
  public function login_required($url='/user/login', $next=NULL)
  {
    if ( ! (bool) $next)
    {
      $next = $_SERVER['REQUEST_URI'];
    }
    if ( ! (bool) $this->data['user']['is_authenticated'])
    {
      header('Location: http://'.$_SERVER['HTTP_HOST'].$url.'?next='.$next);
    }
  }

}