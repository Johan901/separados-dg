<?php
// panel_aurora.php
require_once 'config.php';
require_once 'vendor/autoload.php';
use Twilio\Rest\Client;
date_default_timezone_set('America/Bogota');
$hoy = date('Y-m-d');
$alertas = [];
$chats = [];

// Obtener alertas pendientes
$stmt = $conn->prepare("SELECT id, phone_number, nombre, mensaje, fecha FROM alertas_pendientes WHERE respondido = FALSE ORDER BY fecha DESC");
$stmt->execute();
$alertas = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener chats del día
$stmt = $conn->prepare("SELECT DISTINCT ON (phone_number) phone_number, message, timestamp FROM chat_history WHERE DATE(timestamp AT TIME ZONE 'America/Bogota') = :hoy ORDER BY phone_number, timestamp DESC LIMIT 100");
$stmt->execute([':hoy' => $hoy]);
$chats = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Manejar respuestas de texto
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['numero']) && isset($_POST['respuesta'])) {
    $numero = $_POST['numero'];
    $respuesta = $_POST['respuesta'];
    $sid = getenv('TWILIO_ACCOUNT_SID');
    $token = getenv('TWILIO_AUTH_TOKEN');
    $from = 'whatsapp:' . getenv('TWILIO_NUMBER');
    $client = new Client($sid, $token);

    try {
        $message = $client->messages->create('whatsapp:' . $numero, [
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

// Manejar envío de imagen
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
                $client->messages->create('whatsapp:' . $numero, [
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

// Marcar alerta como respondida
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['marcar_respondido'])) {
    $id_alerta = $_POST['marcar_respondido'];
    $stmt = $conn->prepare("UPDATE alertas_pendientes SET respondido = TRUE WHERE id = :id");
    $stmt->execute([':id' => $id_alerta]);
    header("Location: panel_aurora.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Panel Aurora</title>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; }
        .chat-box { border: 1px solid #ccc; padding: 10px; margin-bottom: 20px; }
        .msg-user { color: #000; }
        .msg-assistant { color: green; text-align: right; }
        .msg-image { max-width: 300px; }
        hr { border: 1px dashed #ccc; }
    </style>
</head>
<body>
    <h1>📬 Conversaciones del <?php echo $hoy; ?></h1>
    <form method="get"><button type="submit">🔄 Refrescar</button></form>
    <?php if ($chats): ?>
        <form method="get">
            <label>Selecciona número:</label>
            <select name="numero" onchange="this.form.submit()">
                <?php foreach ($chats as $c): ?>
                    <option value="<?php echo $c['phone_number']; ?>" <?php echo isset($_GET['numero']) && $_GET['numero'] == $c['phone_number'] ? 'selected' : ''; ?>>
                        <?php echo $c['phone_number']; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>

        <?php if (isset($_GET['numero'])):
            $numero = $_GET['numero'];
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
                            <blockquote style="border-left: 2px solid #888; padding-left: 10px;">
                                ↪ <i><?php echo htmlspecialchars($refs[$m['quoted_sid']]['message']); ?></i>
                            </blockquote>
                        <?php endif; ?>
                        <?php echo nl2br(htmlspecialchars($m['message'])); ?>
                        <?php if ($m['media_url']): ?>
                            <div><img class="msg-image" src="<?php echo $m['media_url']; ?>"></div>
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

                <?php if (isset($success)) echo "<p style='color:green'>$success</p>"; ?>
                <?php if (isset($error)) echo "<p style='color:red'>$error</p>"; ?>
            </div>
        <?php endif; ?>
    <?php else: ?>
        <p>No hay chats para hoy.</p>
    <?php endif; ?>

    <h2>📌 Alertas de pedidos pendientes</h2>
    <?php foreach ($alertas as $a): ?>
        <div class="chat-box">
            <p><strong>📞 <?php echo $a['phone_number']; ?> – <?php echo $a['nombre'] ?: 'Sin nombre'; ?></strong></p>
            <p><small>🕒 <?php echo $a['fecha']; ?></small></p>
            <p>💬 <?php echo $a['mensaje']; ?></p>
            <form method="get">
                <input type="hidden" name="numero" value="<?php echo $a['phone_number']; ?>">
                <button type="submit">🗨️ Ir a la conversación</button>
            </form>
            <form method="post">
                <input type="hidden" name="marcar_respondido" value="<?php echo $a['id']; ?>">
                <button type="submit">✅ Marcar como respondida</button>
            </form>
        </div>
    <?php endforeach; ?>
</body>
</html>
