<?php
/**
 * MailBox
 *
 * @package     mobicms
 * @subpackage  main
 * @author      Jose Sibande <jbsibande@gmail.com>
 */

class Site_MailBox extends FController
{
  public function index()
  {
    $this->login_required();
    
    $rules = require(dirname(__FILE__).'/forms/mailbox.php');
    $form = new FForm_Form($rules);
    
    $exchanges = $this->db->prepare('SELECT uo.id AS user_one_id, uo.username AS username_one, ut.id AS user_two_id, ut.username AS username_two, uc.id '.
				    'FROM uconnection uc INNER JOIN users uo ON (uc.user_one_id=uo.id) '.
				    'INNER JOIN users ut ON (uc.user_two_id=ut.id) '.
				    'WHERE uc.user_one_id=:user_id OR uc.user_two_id=:user_id ORDER BY uc.datetime DESC');
    $exchanges->execute(array(':user_id'=>$this->data['user']['data']['id']));
    $exchanges = $exchanges->fetchAll();
    
    $this->data['exchanges'] = $exchanges;
    $this->data['form'] = $form;
    $this->render('mailbox/index.html', $this->data);
  }
  
  public function messages($request)
  {
    $this->login_required();
    
    $rules = require(dirname(__FILE__).'/forms/mailbox.php');
    $form = new FForm_Form($rules);
    
    $ucon = $this->db->prepare('SELECT uo.username AS username_one, uc.user_one_id, ut.username AS username_two, uc.user_two_id, uc.id '.
			       'FROM uconnection uc INNER JOIN users uo ON (uc.user_one_id=uo.id) '.
			       'INNER JOIN users ut ON (uc.user_two_id=ut.id) '.
			       'WHERE uc.id=:id');
    $ucon->execute(array(':id'=>$request['route']['ucon_id']));
    $ucon = $ucon->fetch();

    if ( ! $ucon)
    {
      return $this->render('404.html', $this->data);
    }
    $messages = $this->db->query('SELECT u.username, m.id, m.sender_id, m.receiver_id, m.message, m.is_read, m.datetime '.
				 'FROM message m INNER JOIN users u ON (m.sender_id=u.id) '.
				 'WHERE m.ucon_id=\''.$ucon['id'].'\' ORDER BY m.datetime DESC');
    $messages = $messages->fetchAll();

    $this->data['messages'] = $messages;
    $this->data['ucon'] = $ucon;
    $this->data['form'] = $form;
    $this->render('mailbox/thread.html', $this->data);
  }
  
  public function send($request)
  {
    $this->login_required();
    
    if (empty($_POST))
    {
      return $this->render('500.html', $this->data);
    }
    $receiver = $this->db->prepare('SELECT * FROM users WHERE id=:id');
    $receiver->execute(array(':id'=>$request['route']['receiver_id']));
    $receiver = $receiver->fetch();
    
    if ( ! $receiver)
    {
      return $this->render('404.html', $this->data);
    }
    
    $ucon = $this->get_user_connection($receiver['id'], $this->data['user']['data']['id']);
    
    if ( ! $ucon)
    {
      return $this->render('500.html', $this->data);
    }
    
    $rules = require(dirname(__FILE__).'/forms/mailbox.php');
    $form = new FForm_Form($rules);
    
    if ($form->validate())
    {
      $message = $this->db->prepare('INSERT INTO message (sender_id, receiver_id, message, ucon_id) '.
				    'VALUES (:sender_id, :receiver_id, :message, :ucon_id)');
      $message->execute(array(':sender_id'=>$this->data['user']['data']['id'],
			      ':receiver_id'=>$receiver['id'],
			      ':message'=>$form->data['message']['value'],
			      ':ucon_id'=>$ucon['id']));
      FHelper::set_flash('send_feedback', 'Message successfully sent.');
    }
    else
    {
      FHelper::set_flash('send_feedback', $form->data['message']['errors'][0]);
    }
    header('Location: http://'.$_SERVER['HTTP_HOST'].$_GET['next']);
  }
  
  public function get_user_connection($user_id, $other_user_id)
  {
    function select_ucon($user_id, $other_user_id, $that)
    {
      $ucon = $that->db->prepare('SELECT * FROM uconnection  WHERE '.
				 '(user_one_id=:user_id AND user_two_id=:other_user_id) OR '.
				 '(user_one_id=:other_user_id AND user_two_id=:user_id)');
      $ucon->execute(array(':user_id'=>$user_id, ':other_user_id'=>$other_user_id));
      return $ucon->fetch();
    }

    if ( ! select_ucon($user_id, $other_user_id, $this))
    {
      $ucon = $this->db->prepare('INSERT INTO uconnection (user_one_id, user_two_id) '.
				 ' VALUES (:user_one_id, :user_two_id)');
      $ucon->execute(array(':user_one_id'=>$user_id, ':user_two_id'=>$other_user_id));
    }
    return select_ucon($user_id, $other_user_id, $this);
  }

}
