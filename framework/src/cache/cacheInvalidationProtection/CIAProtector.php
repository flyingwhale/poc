<?php
namespace framework\src\cache\cacheInvaludationProtection;

use POC\core\OptionAbleInterface;

use POC\core\OptionAble;

use POC\cache\tagging\driver\mysql\model\Cache;

/**
 * This calss name comes form the "Cache Invalidation Attack Protection" name.
 * This integrates transpanently to the framework.
 * 
 * The basic idea was to implement a subsytem to the framework that protects
 * the system that uses against the high load if the caches are invalidated, or
 * just cannot afford long TTL's for the cache, so the pages are generated offten.
 * 
 * Alought it can be used in many scenatrios. For instance if you have a page
 * thats generation takes a long time you can use cache with even very sort ttl 
 * the cache. If there are lot of concurrent request waits for the page that 
 * generates for a long time this system can reduce the load from your server 
 * effectively as well by forcing the clients to wait while the first user's
 * output is generated. If it is done the clients in the queue will receive the
 * result. Also we can set up how much client can wait for the results with the 
 * sleep php method per page, if there are more requests are coming the clients
 * will be informed about the heavy losad and their client will try to reconnect
 * to the resource within a second again. 
 * 
 * @author Imre Toth
 *
 */
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
