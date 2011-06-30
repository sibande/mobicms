<?php
/**
 * Main site controller
 *
 * @package     mobicms
 * @subpackage  user
 * @author      JB Sibande <jbsibande@gmail.com>
 */

class Site_User extends FController
{
  
  public function login()
  {
    $data = array();
    $rules = require(dirname(__FILE__).'/forms/user.php');
    $form = new FForm_Form($rules['login']);
    
    if ( ! empty($_POST))
    {
      if ($form->validate())
      {
	$users = $this->db->prepare('SELECT id FROM users WHERE lower(username) = lower(:username);');
	$users->execute(array(':username' => $form->data['username']['value']));
	$user = $users->fetchAll();
	// set session user id
	$_SESSION['user_id'] = $user[0]['id'];
	
	header('Location: http://'.$_SERVER['HTTP_HOST'].'/');
      }
    }

    $data['form'] = $form;

    $this->render('user/login.html', $data);
  }
  
  public function join()
  {
    $rules = require(dirname(__FILE__).'/forms/user.php');
    $form = new FForm_Form($rules['join']);

    $data = array();

    if ( ! empty($_POST))
    {
      if ($form->validate())
      {
	$password = $this->hash($form->data['password']['value']);
	
	$user = $this->db->prepare('INSERT INTO users (username, email, password, udatetime, datetime) '.
			   'VALUES (:username, :email, :password, now(), now())');
	$user->execute(array(':username' => $form->data['username']['value'],
			     ':email' => $form->data['email']['value'],
			      ':password' => $password,
			 ));
	$users = $this->db->prepare('SELECT id FROM users WHERE lower(username) = lower(:username);');
	$users->execute(array(':username' => $form->data['username']['value']));
	$user = $users->fetchAll();
	// set session user id
	$_SESSION['user_id'] = $user[0]['id'];
	
	header('Location: http://'.$_SERVER['HTTP_HOST'].'/');
      }
    }
    
    $data['form'] = $form;

    $this->render('user/join.html', $data);
  }
  
  
  public function hash($str)
  {
    return hash_hmac('sha1', $str, $this->fconfig['hash_secret_key']);
  }

}
