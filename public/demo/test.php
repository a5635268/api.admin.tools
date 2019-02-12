<?php
//过滤错误
error_reporting(E_ERROR | E_PARSE);

$message = "123456";
$key = "1111";
$iv = "11111111";

$message_padded = $message;

$message_padded = 'g7bCR3CogC8=';
$message_padded = base64_decode($message_padded);

if (strlen($message_padded) % 8) {
    $message_padded = str_pad($message_padded,
                              strlen($message_padded) + 8 - strlen($message_padded) % 8, "\0");
}

//$encrypted_openssl = openssl_encrypt($message_padded, "DES-EDE3-CBC",
//                                     $key, OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING, $iv);
//
//printf("%s => %s\n", bin2hex($message_padded), base64_encode($encrypted_openssl));


$encrypted_openssl = openssl_decrypt($message_padded, "DES-EDE3-CBC",
                                     $key, OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING, $iv);

echo $encrypted_openssl;