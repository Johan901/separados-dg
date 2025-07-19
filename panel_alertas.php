<?php
// panel_alertas.php
require_once 'config.php';
date_default_timezone_set('America/Bogota');

$fecha = $_GET['fecha'] ?? date('Y-m-d');

$stmt = $conn->prepare("SELECT id, phone_number, nombre, mensaje, fecha FROM alertas_pendientes WHERE respondido = FALSE AND DATE(fecha AT TIME ZONE 'America/Bogota') = :fecha ORDER BY fecha DESC");
$stmt->execute([':fecha' => $fecha]);
$alertas = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['marcar_respondido'])) {
    $id_alerta = $_POST['marcar_respondido'];
    $stmt = $conn->prepare("UPDATE alertas_pendientes SET respondido = TRUE WHERE id = :id");
    $stmt->execute([':id' => $id_alerta]);
    header("Location: panel_aurora.php?page=alertas&fecha=$fecha");
    exit();
}
?>
<div class="content-section">
    <h2>📌 Alertas del <input type="date" name="fecha" value="<?php echo $fecha; ?>" onchange="window.location.href='?page=alertas&fecha='+this.value">
    </h2>
    <?php foreach ($alertas as $a): ?>
        <div class="chat-box">
            <p><strong>📞 <?php echo $a['phone_number']; ?> – <?php echo $a['nombre'] ?: 'Sin nombre'; ?></strong></p>
            <p><small>⏰ <?php echo $a['fecha']; ?></small></p>
            <p>💬 <?php echo $a['mensaje']; ?></p>
            <form method="get">
                <input type="hidden" name="tab" value="chats">
                <input type="hidden" name="numero" value="<?php echo $a['phone_number']; ?>">
                <input type="hidden" name="fecha" value="<?php echo $fecha; ?>">
                <button type="submit">🛰 Ir a la conversación</button>
            </form>
            <form method="post">
                <input type="hidden" name="marcar_respondido" value="<?php echo $a['id']; ?>">
                <button type="submit">✅ Marcar como respondida</button>
            </form>
        </div>
    <?php endforeach; ?>
</div>
