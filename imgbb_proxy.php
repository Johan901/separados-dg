<?php
// imgbb_proxy.php
$sid = getenv('TWILIO_ACCOUNT_SID');
$token = getenv('TWILIO_AUTH_TOKEN');

$twilio_url = $_GET['url'] ?? null;
if (!$twilio_url || strpos($twilio_url, 'twilio.com') === false) {
    http_response_code(400);
    echo "URL inválida";
    exit;
}

// Paso 1: Descargar imagen desde Twilio con autenticación
$ch = curl_init($twilio_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
curl_setopt($ch, CURLOPT_USERPWD, "$sid:$token");
$image_data = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$content_type = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
curl_close($ch);

if ($http_code !== 200 || !$image_data) {
    http_response_code($http_code);
    echo "No se pudo descargar la imagen desde Twilio";
    exit;
}

// Paso 2: Mostrar la imagen directamente
header("Content-Type: $content_type");
echo $image_data;
exit;
