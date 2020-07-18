<?php
$key = base64_encode(SEED.$key);
$method = ENCRYPT;
$decrypted = openssl_decrypt($decrypt, $method, $key, 0, $iv);
return $decrypted;