<?php
//过滤错误
//http://tool.chacuo.net/cryptaes
error_reporting(E_ERROR | E_PARSE);

$message = '{"plateNo":"A12345"}';
$key = "F7A0B971B199FD2A1017CEC5";
$iv = "20160120";

$message_padded = $message;

$message_padded = 'DkTwRsUUza33A8/TvrocXI3r+Az1T7bt';
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