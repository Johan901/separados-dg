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
        :root {
            --main-color: #800020;
            --hover-color: #a00030;
            --bg-light: #f8f8f8;
            --input-bg: #ffffff;
            --input-border: #ccc;
        }

        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background-color: var(--bg-light);
            display: flex;
        }

        .sidebar {
            background-color: var(--main-color);
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

        .sidebar a, .sidebar form button {
            display: block;
            width: 100%;
            padding: 15px 25px;
            color: #fff;
            background: none;
            border: none;
            text-align: left;
            font-size: 16px;
            text-decoration: none;
            cursor: pointer;
            transition: background 0.2s ease;
        }

        .sidebar a:hover,
        .sidebar a.active,
        .sidebar form button:hover {
            background-color: var(--hover-color);
        }

        .content {
            margin-left: 230px;
            padding: 30px;
            width: 100%;
        }

        /* Inputs y selects */
        input[type="text"],
        input[type="date"],
        input[type="file"],
        select,
        textarea {
            padding: 10px;
            border: 1px solid var(--input-border);
            border-radius: 6px;
            background-color: var(--input-bg);
            width: 100%;
            max-width: 400px;
            margin: 8px 0;
            font-size: 14px;
        }

        textarea {
            resize: vertical;
        }

        /* Botones */
        button {
            background-color: var(--main-color);
            color: #fff;
            border: none;
            border-radius: 6px;
            padding: 10px 20px;
            font-size: 14px;
            cursor: pointer;
            margin-top: 10px;
        }

        button:hover {
            background-color: var(--hover-color);
        }

        .success {
            color: green;
            font-weight: bold;
        }

        .error {
            color: red;
            font-weight: bold;
        }

        /* Responsive */
        @media screen and (max-width: 768px) {
            .sidebar { width: 100px; }
            .sidebar a, .sidebar form button { font-size: 12px; padding: 10px; }
            .sidebar h2 { font-size: 16px; }
            .content { margin-left: 100px; }
        }

        .content-section {
    max-width: 900px;
    margin: 0 auto;
}

.chat-filters {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: flex-start;
    gap: 20px;
    margin-bottom: 20px;
}

.input-date,
.input-select {
    padding: 8px 10px;
    border: 1px solid #ccc;
    border-radius: 8px;
    font-size: 15px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    transition: border 0.3s ease;
}

.input-date:focus,
.input-select:focus {
    border-color: #800020;
    outline: none;
}

.numero-form {
    display: flex;
    align-items: center;
    gap: 10px;
}

    </style>
    <div class="chat-filters">
    <h2>📢 Conversaciones del 
        <input type="date" name="fecha" value="<?php echo $fecha; ?>" onchange="window.location.href='?page=conversaciones&fecha='+this.value" class="input-date">
    </h2>
    <form method="get" class="numero-form">
        <input type="hidden" name="page" value="conversaciones">
        <input type="hidden" name="fecha" value="<?php echo $fecha; ?>">
        <label for="numero">Selecciona número:</label>
        <select name="numero" onchange="this.form.submit()" class="input-select">
            <?php foreach ($chats as $c): ?>
                <option value="<?php echo $c['phone_number']; ?>" <?php echo $numero === $c['phone_number'] ? 'selected' : ''; ?>>
                    <?php echo $c['phone_number']; ?>
                </option>
            <?php endforeach; ?>
        </select>
    </form>
</div>

</head>
<body>
    <div class="sidebar">
        <h2>🔮 Aurora</h2>
        <a href="?page=conversaciones" class="<?php echo $page == 'conversaciones' ? 'active' : ''; ?>">💬 Conversaciones</a>
        <a href="?page=alertas" class="<?php echo $page == 'alertas' ? 'active' : ''; ?>">📌 Alertas</a>
        <form method="get">
            <input type="hidden" name="page" value="<?php echo $page; ?>">
            <button type="submit">🔄 Refrescar</button>
        </form>
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
