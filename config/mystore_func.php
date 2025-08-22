<?php

function decryptStr($str_txt,$ENCRYPT_METHOD,$WSECRET_KEY,$WSECRET_IV){
	$user_key = hash('sha256', $WSECRET_KEY);  
	$user_iv = substr(hash('sha256', $WSECRET_IV), 0, 16);
	$file_name = openssl_decrypt(base64_decode($str_txt), $ENCRYPT_METHOD, $user_key, 0, $user_iv);
	return $file_name;
}

function encryptStoreStr($str_txt,$ENCRYPT_METHOD,$WSECRET_KEY,$WSECRET_IV){
	  $encrypt_method = "AES-256-CBC";
	  $secret_key = 'KDU enroll key n';
	  $secret_iv = 'KDU key iv';
	  
	  $key = hash('sha256', $WSECRET_KEY);  
	  $iv = substr(hash('sha256', $WSECRET_IV), 0, 16);
	  $encrypt_nic = openssl_encrypt($str_txt, $ENCRYPT_METHOD, $key, 0, $iv);
	  $encrypt_nic = base64_encode($encrypt_nic);
	  return $encrypt_nic;
}
?>