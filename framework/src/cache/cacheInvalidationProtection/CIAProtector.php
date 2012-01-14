<?php
namespace framework\src\cache\cacheInvaludationProtection;

use POC\core\OptionAbleInterface;

use POC\core\OptionAble;

use POC\cache\tagging\driver\mysql\model\Cache;

class CIAProtector implements OptionAbleInterface
{
  const KEY_POSTFIX = "ci";
  const PARAM_CLIENTUNIQUE = 'clinetUnique';
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
     $this->optionAble[self::PARAM_CLIENTUNIQUE] = function(){
       return md5($_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_USER_AGENT'].$_SERVER['HTTP_ACCEPT'].$_SERVER['HTTP_ACCEPT_LANGUAGE'].$_SERVER['HTTP_ACCEPT_ENCODING'].$_SERVER['HTTP_ACCEPT_CHARSET']);
     };
   }
   
  /**
   * 
   * @param  \POC\cache\cacheimplementation\Cache $cache
   */
  function __construct ($options = array())
  {
    $this->optionAble = new OptionAble($options, $this);
    $this->clientUnique = $this->optionAble->getOption(self::PARAM_CLIENTUNIQUE);
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
    $pageURL = 'http';
    if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {
    	$pageURL .= "s";
    }
    $pageURL .= "://";
    if ($_SERVER["SERVER_PORT"] != "80") {
    	$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
    } else {
    	$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
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