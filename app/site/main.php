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
    $this->data['request'] = $request;
    $this->render('index.html', $this->data);
  }

}
