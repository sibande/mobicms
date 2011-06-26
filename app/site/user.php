<?php
/**
 * Main site controller
 *
 * @package     mobicms
 * @subpackage  user
 * @author      JB Sibande
 */

class Site_User extends Fuuze
{
  public function join()
  {
    if ( ! empty($_POST))
    {
      $rules = require(dirname(__FILE__).'/forms/user.php');
      $form = new FForm_Form($rules);
      $form = $form->validate();
      
      var_dump($_POST);
    }
    
    $this->render('user/join.html');
  }

}
