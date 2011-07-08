<?php
/**
 * User profiles
 *
 * @package     mobicms
 * @subpackage  profile
 * @author      JB Sibande <jbsibande@gmail.com>
 */

class Site_Profile extends FController
{
  public function view($request)
  {
    $this->login_required();

    $this->data['request'] = $request;
    
    $object_user = $this->db->prepare('SELECT u.id, u.username, u.email, u.udatetime, u.datetime, '.
				      'p.birthdate, p.sex, p.country, p.city, p.profile_pic AS profile_pic_id '.
				      'FROM users u LEFT OUTER JOIN profile p ON (u.id=p.uid) '.
				      'WHERE lower(u.username) = lower(:username)');
    $object_user->execute(array(':username' => $request['route']['username']));
    $object_user = $object_user->fetch();
    if ( ! (bool) $object_user )
    {
      return $this->render('404.html'); 
    }

    $rules = require(dirname(__FILE__).'/forms/mailbox.php');
    $form = new FForm_Form($rules);

    //echo '<pre>';var_dump($form); echo '</pre>';
    $this->data['object_user'] = $object_user;
    $this->data['form'] = $form;

    $this->render('profile/view.html', $this->data);
    
  }



}