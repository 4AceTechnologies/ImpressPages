<?php
/**
 * @package	ImpressPages
 * @copyright	Copyright (C) 2012 ImpressPages LTD.
 * @license see ip_license.html
 */


if (!defined('CMS')) exit;


class Scripts {
  private $scripts;
  const destinationVersion = '2.4';
  
  public function __construct(){
    $this->scripts = array();
    $this->scripts[] = array("from" => "2.0rc1", "to" => "2.0rc2", "script" => "update_2_0_rc1_to_2_0_rc2");
    $this->scripts[] = array("from" => "2.0rc2", "to" => "2.0", "script" => "update_2_0_rc2_to_2_0");
    $this->scripts[] = array("from" => "2.0", "to" => "2.1", "script" => "update_2_0_to_2_1");
    $this->scripts[] = array("from" => "2.1", "to" => "2.2", "script" => "update_2_1_to_2_2");
    $this->scripts[] = array("from" => "2.2", "to" => "2.3", "script" => "update_2_2_to_2_3");
    $this->scripts[] = array("from" => "2.3", "to" => "2.4", "script" => "update_2_3_to_2_4");
  }

  public function getScripts($fromVersion = "2.0rc1"){
    $answer = array();
    
    $currentScript = false;
    while($currentScript = $this->getScript($fromVersion)){
      $answer[] = $currentScript;
      $fromVersion = $currentScript['to'];
    }
    
    return $answer;
  }

  public function getScript($fromVersion){
    $answer = false;
    
    foreach ($this->scripts as $script) {
      if ($script['from'] == $fromVersion){
        $answer = $script;
      }
    } 
    
    return $answer;
  }
  
  public function nextVersin($version){
  
  }
  
  public function updateClass($fromVersion, $toVersion){
  
  }
  
    
}





