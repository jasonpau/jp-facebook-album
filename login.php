<?php

require_once __DIR__ . '/facebook-php-graph-sdk/src/Facebook/autoload.php'; // change path as needed

if(!session_id()) {
  session_start();
}

$fb = new Facebook\Facebook([
  'app_id' => '',
  'app_secret' => '',
  'default_graph_version' => 'v2.10',
  ]);

$helper = $fb->getRedirectLoginHelper();

$callback_url = 'https://example.com/callback.php';
$permissions = ['email']; // Optional permissions
$loginUrl = $helper->getLoginUrl($callback_url, $permissions);

echo '<a href="' . $loginUrl . '">Log in to authenticate</a>';