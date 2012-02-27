<?php
namespace Poc\PocEvents;

interface PocEventNames {
  const BEFORE_OUTPUT_SENT_TO_CLIENT_AFTER_OUTPUT_STORED = 'bcostc';
  const BEFORE_STORE_OUTPUT = 'bsto';
  const BEFORE_OUTPUT_SENT_TO_CLIENT_NO_CACHING_PROCESS_INVLOVED = 'bostcnci';
  const BEFORE_OUTPUT_SENT_TO_CLIENT_FETCHED_FROM_CACHE = 'BOSTCGFC';
  
  //const BEFORE_OUTPUT_SENT_TO_CLIENT = 'bcostc';
}

?>