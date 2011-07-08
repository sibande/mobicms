<?php
/**
 * Chat
 *
 * @package     mobicms
 * @subpackage  chat
 * @author      Jose Sibande <jbsibande@gmail.com>
 */

class Site_Chat extends FController
{


  public function index()
  {
    $chat = $this->select_chat();
    $chatrooms = $this->db->query('SELECT * FROM content_group WHERE typeid = \''.$chat['id'].'\';');
    $chatrooms = $chatrooms->fetchAll();
    if ( ! (bool) $chatrooms)
    {
      return $this->render('500.html');
    }

    $this->data['chatrooms'] = $chatrooms;

    $this->render('chat/index.html', $this->data);
  }
  
  public function chat($request)
  {
    $this->login_required();
    
    $chat = $this->select_chat();
    $chatroom = $this->db->prepare('SELECT * FROM content_group WHERE typeid = \''.$chat['id'].
				 '\' AND lower(label) = lower(:label);');
    $chatroom->execute(array(':label'=>$request['route']['chatroom']));
    $chatroom = $chatroom->fetch();
    if ( ! (bool) $chatroom)
    {
      return $this->render('500.html');
    }

    $rules = require(dirname(__FILE__).'/forms/chat.php');
    $form = new FForm_Form($rules);
    
    if ( ! empty($_POST))
    {
      if ($form->validate())
      {
	$message = $this->db->prepare('INSERT INTO chat (uid, message, room_id, datetime) '.
				      'VALUES (:uid, :message, :room_id, now());');
	$message->execute(array(':uid'=>$this->data['user']['data']['id'],
				':message'=>$form->data['message']['value'],
				':room_id'=>$chatroom['id'],));
	header('Location: http://'.$_SERVER['HTTP_HOST'].'/chat/'.strtolower($chatroom['label']));
      }
    }
    
    $chat_data = $this->db->query('SELECT c.message, c.datetime, u.username FROM chat c INNER JOIN users u ON (c.uid = u.id)'.
				  'WHERE c.room_id = \''.$chatroom['id'].'\' ORDER BY c.id DESC LIMIT '.FUUZE_DATA_PER_PAGE.';');
    $this->data['chat_data'] = $chat_data->fetchAll();
    /* echo '<pre>';var_dump($this->data['chat_data']);echo '</pre>'; */
    $this->data['form'] = $form;
    $this->data['chatroom'] = $chatroom;
    $this->render('chat/chat.html', $this->data);
  }
  public function select_chat()
  {

    $chat = $this->db->query('SELECT id FROM content_type WHERE table_id = \'chat\';');
    $chat = $chat->fetch();
    if ( ! (bool) $chat)
    {
      return $this->render('500.html');
    }
    return $chat;
  }
}
