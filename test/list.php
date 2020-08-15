<?php 
$urlc = $_GET['url'];
$url = 'http://panel.turkibiza.net/palmymp3/list/';

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
