<?php
/**
 * Main site controller
 *
 * @package     mobicms
 * @subpackage  main
 * @author      Jose Sibande
 */

class Site_Main extends Fuuze
{
  public function index()
  {
    $this->render('index.html');
  }

}
