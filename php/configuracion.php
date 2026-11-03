<?php

function getHandelAPI()
{
  $host = $_SERVER['HTTP_HOST'] ?? '';
  if (strpos($host, 'localhost') !== false || strpos($host, '127.0.0.1') !== false)
    return "http://localhost:7500/handel_api";
  return "https://api.apps-handel.com";
}
