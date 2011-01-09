<?php
interface PobCacheInterface {

  public function __construct(AbstractPobCacheSpecific $cache);

  public function storeCache ($output);

  public function fetchCache ();

  public function clearCache ();

}
