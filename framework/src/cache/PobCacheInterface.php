<?php
interface PobCacheInterface {
  
  public storeCache ($conditions);
  
  public fetchCache ($conditions);
  
  public clearCache ($conditions);
  
}
