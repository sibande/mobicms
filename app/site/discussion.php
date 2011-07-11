<?php
/**
 * Chat
 *
 * @package     mobicms
 * @subpackage  discussion
 * @author      Jose Sibande <jbsibande@gmail.com>
 */

class Site_Discussion extends FController
{
  public function index()
  {
    $this->process_discussion_request(NULL);
    $forums = $this->db->query('SELECT * FROM content_group WHERE typeid = \''.$this->discussion['id'].'\';');

    $forums = $forums->fetchAll();
    if ( ! (bool) $forums)
    {
      return $this->render('500.html');
    }
    
    $this->data['forums'] = $forums;
    
    $this->render('discussion/index.html', $this->data);
    
  }
  
  public function topic($request)
  {
    if ( ! $this->process_discussion_request())
    {
      return $this->render('500.html');
    }
    
    $rules = require(dirname(__FILE__).'/forms/discussion.php');
    $form = new FForm_Form($rules['reply']);

    $topic = $this->db->prepare('SELECT c.label AS forum_label, u.username, d.id, d.topic, d.body, d.udatetime, d.datetime '.
				'FROM discussion d INNER JOIN users u ON (d.uid = u.id) '.
				'INNER JOIN content_group c ON (d.groupid = c.id) WHERE d.id = :id LIMIT 1');
    $topic->execute(array(':id' => $request['route']['topic_id']));
    $topic = $topic->fetch();
    if ( ! $topic)
    {
      return $this->render('500.html');
    }

    if ( ! empty($_POST))
    {
      $this->login_required();

      if ($form->validate())
      {
	$reply = $this->db->prepare('INSERT INTO comments (uid, body, item_id, typeid) '.
				    'VALUES (:uid, :body, :item_id, :typeid);');
	$reply->execute(array(':uid'=>$this->data['user']['data']['id'],
			      ':body'=>$form->data['body']['value'],
			      ':item_id'=>$request['route']['topic_id'],
			      ':typeid'=>$this->discussion['id']));
	header('Location: http://'.$_SERVER['HTTP_HOST'].'/discussion/topic/'.$request['route']['topic_id']);
      }
    }
    
    $replies = $this->db->query('SELECT c.id, c.body, c.datetime, u.username FROM comments c INNER JOIN users u ON (c.uid = u.id) '.
				'WHERE c.item_id = '.((int) $request['route']['topic_id']).' '.
				'AND c.typeid = '.$this->discussion['id'].' ORDER BY c.datetime DESC');

    $replies = new Lib_Paginator($replies->fetchAll(), FUUZE_DATA_PER_PAGE, FHelper::get_page_id($request),
			    '/discussion/topic/'.$request['route']['topic_id']);

    $this->data['replies'] = $replies;
    $this->data['form'] = $form;
    $this->data['topic'] = $topic;
    
    $this->render('discussion/topic.html', $this->data);
  }
  
  public function add($request)
  {
    $this->login_required();

    //sets $this->discussion and $this->forum
    if ( ! $this->process_discussion_request($request))
    {
       return $this->render('500.html');
    }
    $rules = require(dirname(__FILE__).'/forms/discussion.php');
    $form = new FForm_Form($rules['topic']);

    if ( ! empty($_POST))
    {
      if ($form->validate())
      {
	$post = $this->db->prepare('INSERT INTO discussion (uid, topic, body, groupid) '.
			       'VALUES (:uid, :topic, :body, :groupid);');
	$post->execute(array(':uid'=>$this->data['user']['data']['id'],
			     ':topic'=>$form->data['title']['value'],
			     ':body'=>$form->data['body']['value'],
			     ':groupid'=>$this->forum['id'],
			 )
	  );
	header('Location: http://'.$_SERVER['HTTP_HOST'].'/discussion/'.strtolower($this->forum['label']));
      }
    }    
    
    $this->data['form'] = $form;
    $this->data['discussion'] = $this->discussion;
    $this->data['forum'] = $this->forum;
    
    $this->render('discussion/add.html', $this->data);
  }
  public function discussion($request)
  {
    if ( ! $this->process_discussion_request($request))
    {
      return $this->render('500.html');
    }
    
    if ( ! empty($_POST))
    {
    }

    $topics = $this->db->query('SELECT d.id, d.topic, d.body, d.datetime, d.udatetime, u.username '.
				    'FROM discussion d INNER JOIN users u ON (d.uid = u.id) '.
				    'WHERE d.groupid = \''.$this->forum['id'].'\' AND d.is_active=true '.
				    'ORDER BY d.id DESC');

      
    $topics = new Lib_Paginator($topics->fetchAll(), FUUZE_DATA_PER_PAGE, FHelper::get_page_id($request),
				'/discussion/'.$request['route']['forum']);

    $this->data['topics'] = $topics;
    $this->data['discussion'] = $this->discussion;
    $this->data['forum'] = $this->forum;
    $this->render('discussion/discussion.html', $this->data);

  }

  public function process_discussion_request($request=NULL)
  {
    $discussion = $this->db->query('SELECT id FROM content_type WHERE table_id = \'discussion\';');
    $discussion = $discussion->fetch();
    if ( ! (bool) $discussion)
    {
      return FALSE;
    }
    // set content type
    $this->discussion = $discussion;
    
    if ( (bool) $request)
    {
      $forum = $this->db->prepare('SELECT * FROM content_group WHERE typeid = \''.$this->discussion['id'].
				  '\' AND lower(label) = lower(:label);');
      $forum->execute(array(':label'=>$request['route']['forum']));
      $forum = $forum->fetch();
      if ( ! (bool) $forum)
      {
	return FALSE;
      }
      // section type data
      $this->forum = $forum;
    }
    return TRUE;
  }

}
