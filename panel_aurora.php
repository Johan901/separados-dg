<?php
// panel_aurora.php
$page = $_GET['page'] ?? 'conversaciones';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel Aurora</title>
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background-color: #f8f8f8;
            display: flex;
        }
        .sidebar {
            background-color: #800020;
            color: #fff;
            width: 230px;
            height: 100vh;
            padding-top: 20px;
            position: fixed;
        }
        .sidebar h2 {
            text-align: center;
            margin-bottom: 30px;
            font-size: 22px;
        }
        .sidebar a {
            display: block;
            padding: 15px 25px;
            color: #fff;
            text-decoration: none;
            font-size: 16px;
            transition: background 0.2s ease;
        }
        .sidebar a:hover, .sidebar a.active {
            background-color: #a00030;
        }
        .content {
            margin-left: 230px;
            padding: 30px;
            width: 100%;
        }
        iframe {
            width: 100%;
            height: 100vh;
            border: none;
        }
        @media screen and (max-width: 768px) {
            .sidebar { width: 100px; }
            .sidebar a { font-size: 12px; padding: 10px; }
            .sidebar h2 { font-size: 16px; }
            .content { margin-left: 100px; }
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>🔮 Aurora</h2>
        <a href="?page=conversaciones" class="<?php echo $page == 'conversaciones' ? 'active' : ''; ?>">💬 Conversaciones</a>
        <a href="?page=alertas" class="<?php echo $page == 'alertas' ? 'active' : ''; ?>">📌 Alertas</a>
    </div>
    <div class="content">
        <?php
            if ($page === 'alertas') {
                include 'panel_alertas.php';
            } else {
                include 'panel_conversaciones.php';
            }
        ?>
    </div>
</body>
</html>
