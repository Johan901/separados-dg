<?php
// panel_conversaciones.php
require_once 'config.php';
require_once 'vendor/autoload.php';
use Twilio\Rest\Client;
date_default_timezone_set('America/Bogota');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$fecha = $_GET['fecha'] ?? date('Y-m-d');
$numero = $_GET['numero'] ?? null;
$chats = [];

$stmt = $conn->prepare("SELECT DISTINCT ON (phone_number) phone_number, message, timestamp FROM chat_history WHERE DATE(timestamp AT TIME ZONE 'America/Bogota') = :fecha ORDER BY phone_number, timestamp DESC LIMIT 100");
$stmt->execute([':fecha' => $fecha]);
$chats = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['numero']) && isset($_POST['respuesta'])) {
    $numero = $_POST['numero'];
    $respuesta = $_POST['respuesta'];
    $sid = getenv('TWILIO_ACCOUNT_SID');
    $token = getenv('TWILIO_AUTH_TOKEN');
    $from = 'whatsapp:' . getenv('TWILIO_NUMBER');
    $client = new Client($sid, $token);

    try {
        $client->messages->create($numero, [
            'from' => $from,
            'body' => $respuesta
        ]);
        $stmt = $conn->prepare("INSERT INTO chat_history (phone_number, role, message, timestamp) VALUES (:numero, 'assistant', :msg, NOW())");
        $stmt->execute([':numero' => $numero, ':msg' => $respuesta]);
        $success = "Mensaje enviado";
    } catch (Exception $e) {
        $error = "Error al enviar: " . $e->getMessage();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['imagen']) && isset($_POST['numero_imagen'])) {
    $numero = $_POST['numero_imagen'];
    $mensaje = $_POST['mensaje_imagen'] ?? '';
    $api_key = getenv('IMGBB_API_KEY');

    if ($_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $image_data = base64_encode(file_get_contents($_FILES['imagen']['tmp_name']));

        $response = file_get_contents("https://api.imgbb.com/1/upload?key=$api_key", false, stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => 'Content-Type: application/x-www-form-urlencoded',
                'content' => http_build_query(['image' => $image_data])
            ]
        ]));

        $data = json_decode($response, true);
        if (isset($data['data']['url'])) {
            $media_url = $data['data']['url'];
            $sid = getenv('TWILIO_ACCOUNT_SID');
            $token = getenv('TWILIO_AUTH_TOKEN');
            $from = 'whatsapp:' . getenv('TWILIO_NUMBER');
            $client = new Client($sid, $token);

            try {
                $client->messages->create($numero, [
                    'from' => $from,
                    'body' => $mensaje,
                    'mediaUrl' => [$media_url]
                ]);

                $stmt = $conn->prepare("INSERT INTO chat_history (phone_number, role, message, media_url, timestamp) VALUES (:numero, 'assistant', :msg, :url, NOW())");
                $stmt->execute([':numero' => $numero, ':msg' => $mensaje, ':url' => $media_url]);
                $success = "Imagen enviada correctamente";
            } catch (Exception $e) {
                $error = "Error al enviar imagen: " . $e->getMessage();
            }
        } else {
            $error = "No se pudo subir la imagen a imgbb";
        }
    } else {
        $error = "Error en la carga del archivo";
    }
}
?>
<div class="content-section">
    <h2>📢 Conversaciones del <input type="date" name="fecha" value="<?php echo $fecha; ?>" onchange="window.location.href='?page=conversaciones&fecha='+this.value">

    </h2>
    <?php if ($chats): ?>
        <form method="get">
            <input type="hidden" name="page" value="conversaciones">
            <input type="hidden" name="fecha" value="<?php echo $fecha; ?>">
            <label>Selecciona número:</label>
            <select name="numero" onchange="this.form.submit()">
                <?php foreach ($chats as $c): ?>
                    <option value="<?php echo $c['phone_number']; ?>" <?php echo $numero === $c['phone_number'] ? 'selected' : ''; ?>>
                        <?php echo $c['phone_number']; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>

        <?php if ($numero):
            $stmt = $conn->prepare("SELECT id AS sid, role, message, timestamp, media_url, quoted_sid FROM chat_history WHERE phone_number = :num ORDER BY timestamp ASC");
            $stmt->execute([':num' => $numero]);
            $mensajes = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $refs = [];
            foreach ($mensajes as $m) $refs[$m['sid']] = $m;
        ?>
            <div class="chat-box">
                <h3>Conversación con <?php echo $numero; ?></h3>
                <?php foreach ($mensajes as $m): ?>
                    <div class="msg-<?php echo $m['role']; ?>">
                        <small><?php echo $m['timestamp']; ?></small><br>
                        <?php if ($m['quoted_sid'] && isset($refs[$m['quoted_sid']])): ?>
                            <blockquote>↪ <i><?php echo htmlspecialchars($refs[$m['quoted_sid']]['message']); ?></i></blockquote>
                        <?php endif; ?>
                        <?php echo nl2br(htmlspecialchars($m['message'])); ?>
                        <?php if ($m['media_url']): ?>
                            <?php
                                $url = $m['media_url'];
                                $es_twilio = strpos($url, 'twilio.com') !== false;

                                if ($es_twilio) {
                                    // Llama al endpoint Flask que convierte la URL Twilio en ImgBB
                                    $encoded = urlencode($url);
                                    $imgbb_response = @file_get_contents("http://localhost:5000/convert?url=$encoded");

                                    if ($imgbb_response !== false) {
                                        $json = json_decode($imgbb_response, true);
                                        $imagen_final = $json['imgbb_url'] ?? $url; // fallback
                                    } else {
                                        $imagen_final = $url;
                                    }
                                } else {
                                    $imagen_final = $url;
                                }
                            ?>
                            <div>
                                <img class="msg-image" src="<?php echo htmlspecialchars($imagen_final); ?>" alt="Imagen recibida">
                            </div>

                        <?php endif; ?>

                        <hr>
                    </div>
                <?php endforeach; ?>

                <form method="post">
                    <input type="hidden" name="numero" value="<?php echo $numero; ?>">
                    <textarea name="respuesta" rows="3" cols="50" placeholder="Escribe tu respuesta..."></textarea><br>
                    <button type="submit">Enviar respuesta</button>
                </form>

                <form method="post" enctype="multipart/form-data">
                    <input type="hidden" name="numero_imagen" value="<?php echo $numero; ?>">
                    <input type="file" name="imagen" accept="image/*" required><br>
                    <input type="text" name="mensaje_imagen" placeholder="Mensaje opcional"><br>
                    <button type="submit">📷 Enviar imagen</button>
                </form>

                <?php if (isset($success)) echo "<p class='success'>$success</p>"; ?>
                <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
            </div>
        <?php endif; ?>
    <?php else: ?>
        <p>No hay chats.</p>
    <?php endif; ?>
</div>
