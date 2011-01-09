<?php
interface PobCacheInterface {
  
  public function __construct(Evaluatable $evaluatable,
                                              PobCacheSpecificInterface $cache);

  public function storeCache ($output);

  public function fetchCache ();

  public function clearCache ();

  public function generateKey ();

}
