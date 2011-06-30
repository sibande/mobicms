<?php

function check_value($key, $args)
{
  $db = $args['db'];
  $that = $args['that'];
  $users = $db->prepare('SELECT * FROM users WHERE lower('.$key.') = lower(:key)');
  $users->execute(array(':key'=>$that->data[$key]['value']));
  if ( (bool) $users->rowCount())
  {
    $that->data[$key]['errors'][] = $args['error'];
    return FALSE;
  }
  return TRUE;
}

function check_name($key, $args)
{
  $db = $args['db'];
  $site_user = $args['site_user'];
  $that = $args['that'];
  $users = $db->prepare('SELECT username, password FROM users WHERE lower('.$key.') = lower(:key)');
  $users->execute(array(':key'=>$that->data[$key]['value']));
  $user = $users->fetchAll();
  if ( ! isset($user[0]['username']))
  {
    $that->data[$key]['errors'][] = $args['error'];
    return FALSE;
  }
  else
  {
    if ($site_user->hash($that->data['password']['value']) != $user[0]['password'])
    {
      $that->data[$key]['errors'][] = $args['error'];
      return FALSE;
    }
    return TRUE;
  }
}

return 
array('join' => array(
	'username' => array(
	  'required' => array('error' => 'Name required.'),
	  'regex' => array('pattern' => '/^\w+$/',
			   'error' => 'Name must contain alphanumerals, _ or - only.'),
	  'length' => array('range' => array('min'=>2, 'max'=>20),
			    'error' => array('min'=>'Name too small, must be > 2 characters.',
					     'max'=>'Name too long, must be < 20 characters.')),
	  'custom'=> array('callback'=>'check_value',
			   'args' => array('db'=>$this->db,
					   'error'=>'Name already exists, choose another one.',
			     )),
	  ),
	'email' => array(
	  'required' => array('error' => 'E-Mail required.'),
	  'email' => array('error' => 'Invalid E-Mail entered.'),
	  'custom'=> array('callback'=>'check_value',
			   'args' => array('db'=>$this->db,
					   'error'=>'E-Mail already exists, are you sure you don\'t have an account.',
			     )),
	  ),
	'password' => array(
	  'required' => array('error' => 'Password required.'),
	  ),
	'password_verify' => array(
	  'required' => array('error' => 'Password required.'),
	  'compare' => array('other_value' => 'password',
			     'error'=>'Passwords don\'t match.'),
	  ),
	),
      
      'login' => array(
	'username' => array(
	  'required' => array('error' => 'Name required.'),
	  'regex' => array('pattern' => '/^\w+$/',
			   'error' => 'Name must contain alphanumerals, _ or - only.'),
	  'length' => array('range' => array('min'=>2, 'max'=>20),
			    'error' => array('min'=>'Name too small, must be > 2 characters.',
					     'max'=>'Name too long, must be < 20 characters.')),
	  'custom'=> array('callback'=>'check_name',
			   'args' => array('db'=>$this->db,
					   'site_user'=>$this,
					   'error'=>'Name doesn\'t exists.',
			     )),
	  ),
	'password' => array(
	  'required' => array('error' => 'Password required.'),
	  ),
	
	),
  );
