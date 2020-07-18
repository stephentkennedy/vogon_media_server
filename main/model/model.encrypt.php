<?php
$iv = random_bytes(16);
$key = base64_encode(SEED.$key);
$method = ENCRYPT;

$encrypted = openssl_encrypt($encrypt, $method, $key, 0, $iv);

return [
	'encrypted' => $encrypted,
	'iv' => $iv
];