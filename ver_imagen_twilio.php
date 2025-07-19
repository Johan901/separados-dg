<?php
// ver_imagen_twilio.php

$media_url = $_GET['url'] ?? null;
if (!$media_url) {
    http_response_code(400);
    echo "Falta el parámetro 'url'";
    exit;
}

require_once 'vendor/autoload.php';

$sid = getenv('TWILIO_ACCOUNT_SID');
$token = getenv('TWILIO_AUTH_TOKEN');

// Inicializa la solicitud autenticada
$ch = curl_init($media_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERPWD, "$sid:$token");

$data = curl_exec($ch);
$info = curl_getinfo($ch);
$http_code = $info['http_code'];
$content_type = $info['content_type'];

curl_close($ch);

if ($http_code === 200) {
    header("Content-Type: $content_type");
    echo $data;
} else {
    http_response_code($http_code);
    echo "Error al cargar la imagen";
}
