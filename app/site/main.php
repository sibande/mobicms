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
  public function index()
  {
    $this->render('index.html', array('data'=>$this->data));
  }

}
