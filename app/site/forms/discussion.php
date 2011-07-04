<?php

return array(
  // add topic form
  'topic' => array(
    'title' => array(
      'required' => array(),
      'length' => array('range' => array('min'=>4, 'max'=>250),),
      ),
    
    'body' => array(
      'required' => array(),
      'length' => array('range' => array('min'=>4, 'max'=>1000),),
      )
    ),
  // reply / comments form
  'reply' => array(
    'body' => array(
      'required' => array(),
      'length' => array('range' => array('min'=>2, 'max'=>1000),),
      ),
    ),
  );