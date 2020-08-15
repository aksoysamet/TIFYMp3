<?php 
$urlc = $_POST['url'];
//$urlc = $_GET['url'];
$url = 'http://35.204.69.241/palmymp3/';
// use key 'http' even if you send the request to https://...
$data = http_build_query(
    array(
        'url' => $urlc
    )
);

$options = array(
    'http' => array(
        'header'  => "'Content-type: application/x-www-form-urlencoded'",
        'method'  => 'POST',
        'content' => $data
    ),
);
$context  = stream_context_create($options);
$result = @file_get_contents($url, false, $context) or
	die ("Error 1");
echo trim($result);
?> 