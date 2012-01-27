<?php
namespace framework\src\cache\cacheInvaludationProtection;

use POC\core\OptionAbleInterface;

use POC\core\OptionAble;

use POC\cache\tagging\driver\mysql\model\Cache;

class CIAProtector implements OptionAbleInterface
{
  const KEY_POSTFIX = "ci";
  const PARAM_CLIENT_UNIQUE = 'clinetUnique';
  /**
   * 
   * @var OptionAble
   */
  private $optionAble = null;
  
  /**
   * 
   * @var \POC\cache\cacheimplementation\Cache
   */
   private $cache = null;

   private $clientUnique;
   
   function fillDefaults (){
     /*$this->optionAble[self::PARAM_CLIENT_UNIQUE] = function(){
       return md5($_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_USER_AGENT'].$_SERVER['HTTP_ACCEPT'].
                  $_SERVER['HTTP_ACCEPT_LANGUAGE'].$_SERVER['HTTP_ACCEPT_ENCODING'].$_SERVER['HTTP_ACCEPT_CHARSET']);
     };*/
   }
   
  /**
   * 
   * @param  \POC\cache\cacheimplementation\Cache $cache
   */
  function __construct ($options = array())
  {
    $this->optionAble = new OptionAble($options, $this);
    //$this->clientUnique = $this->optionAble->getOption(self::PARAM_CLIENT_UNIQUE);
  }
  
  /**
   * 
   * @param Cache $cache
   */
  function setCache($cache){
    $this->cache = $cache;
  }
  
  public function setSentinel($cnt = 1){
    $this->cache->cacheSpecificStore($this->getKey(), $cnt);
  }
  
  public function getSentinel(){
    $sentiel = $this->cache->fetch($this->getKey());
	if(!$sentiel){
	  $sentiel = 0;
	}    
    if($this->cache->fetch($sentiel)){
      $this->setSentinel($sentiel + 1);
    }
    
    return ($sentiel);
  }
  
  private function getKey(){
    return $this->cache->getHasher()->getKey().self::KEY_POSTFIX;
  }
  
  public function deleteSentinel(){
    $this->cache->clearItem($this->getKey());
  }
  
  public function getRefreshPage(){
  	$servername = '';
  	if (isset($_SERVER["SERVER_NAME"]))
  	{
  		$servername = $_SERVER["SERVER_NAME"];
  	}
    $pageURL = 'http';
    if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {
    	$pageURL .= "s";
    }
    $pageURL .= "://";
    $ru = "";
    if(isset($_SERVER["REQUEST_URI"])){
      $ru = $_SERVER["REQUEST_URI"];
    }

    if (isset($_SERVER["SERVER_PORT"]) && $_SERVER["SERVER_PORT"] != "80") {
    	$pageURL .= $servername.":".$_SERVER["SERVER_PORT"].$ru;
    } else {
    	$pageURL .= $servername.$ru;
    }
     
    return '<HTML> 
    <HEAD>
    <META HTTP-EQUIV="refresh" content="1; url='.$pageURL.'">
    <TITLE>My new webpage</TITLE>
    </HEAD>
    <BODY>
    PLEASE WAIT!
    </BODY> 
    </HTML>';  
  }
  
  
  
}

?>
