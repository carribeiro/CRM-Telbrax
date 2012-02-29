<?php
require 'spider.php';

$s = new Spider();

$url = 'http://localhost:8080/payment';

$data = array(
	'signed_request' => 'GmEgMC0TaRedBRp9Ebf7VO4kGOd77tsWV626b1-Rwgc.eyJhbGdvcml0aG0iOiJITUFDLVNIQTI1NiIsImV4cGlyZXMiOjEzMTIzOTQ0MDAsImlzc3VlZF9hdCI6MTMxMjM4OTc5Mywib2F1dGhfdG9rZW4iOiIxNTY0MTEwNDc3MzA4MDl8Mi5BUURMdl9CMjI5UmhMcWpuLjM2MDAuMTMxMjM5NDQwMC4xLTEwMDAwMTg1NzU1MTA0MnxnMzAxWE5UXzQwdDZzcUtIVkc3bXBMMVpfczQiLCJ1c2VyIjp7ImNvdW50cnkiOiJiciIsImxvY2FsZSI6InB0X0JSIiwiYWdlIjp7Im1pbiI6MjF9fSwidXNlcl9pZCI6IjEwMDAwMTg1NzU1MTA0MiJ9',
	'method' => 'payments_get_items'
);

$s->debug(0);

$result = $s->doPost($url, $data);

echo $result;
?>