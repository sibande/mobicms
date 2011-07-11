<?php
/**
 * Main site controller
 *
 * @package     mobicms
 * @subpackage  main
 * @author      Jose Sibande <jbsibande@gmail.com>
 */

class Site_Main extends FController
{
  public function index($request)
  {
    $featured = $this->db->query('SELECT u.username, u.datetime, p.profile_pic FROM users u '.
				 'LEFT OUTER JOIN profile p ON (p.uid=u.id) '.
				 'WHERE p.profile_pic IS NOT NULL ORDER BY u.datetime DESC LIMIT 2');
    
    $featured = $featured->fetchAll();

    $this->data['featured'] = $featured;
    $this->data['request'] = $request;
    $this->render('index.html', $this->data);
  }

}
