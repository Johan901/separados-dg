<?php
// imgbb_proxy.php
require_once 'vendor/autoload.php';

$sid = getenv('TWILIO_ACCOUNT_SID');
$token = getenv('TWILIO_AUTH_TOKEN');
$imgbb_key = getenv('IMGBB_API_KEY');

$twilio_url = $_GET['url'] ?? null;
if (!$twilio_url || strpos($twilio_url, 'twilio.com') === false) {
    http_response_code(400);
    echo "URL inválida";
    exit;
}

// Paso 1: Descargar imagen de Twilio
$ch = curl_init($twilio_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERPWD, "$sid:$token");
$image_data = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($http_code !== 200) {
    http_response_code($http_code);
    echo "No se pudo descargar la imagen de Twilio";
    exit;
}

// Paso 2: Subir imagen a imgbb
$encoded = base64_encode($image_data);
$post = http_build_query(['key' => $imgbb_key, 'image' => $encoded]);
$options = [
    'http' => [
        'method' => 'POST',
        'header' => 'Content-type: application/x-www-form-urlencoded',
        'content' => $post
    ]
];
$response = file_get_contents("https://api.imgbb.com/1/upload", false, stream_context_create($options));
$result = json_decode($response, true);

if (isset($result['data']['url'])) {
    header("Location: " . $result['data']['url']); // 🔁 Redirige a imgbb
    exit;
} else {
    http_response_code(500);
    echo "Error subiendo a imgbb";
}
