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
    $this->data['request'] = $request;
    
    $users = $this->db->prepare('SELECT id, username, email, udatetime, datetime '.
				'FROM users WHERE lower(username) = lower(:username);');
    $users->execute(array(':username' => $request['route']['username']));
    $profile_user = $users->fetchAll();
    if ( ! (bool) $profile_user )
    {
      return $this->render('404.html'); 
    }
    $profile_user = $profile_user[0];

    $this->data['profile_user'] = $profile_user;
    
    $this->render('profile/view.html', $this->data);
    
  }



}