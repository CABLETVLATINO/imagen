<?php
// Directorio base del explorador (toma la carpeta actual donde se ubica el script)
$baseDir = __DIR__;

// Obtener la ruta solicitada de manera segura para evitar ataques de salto de directorio
$subDir = isset($_GET['dir']) ? $_GET['dir'] : '';
$currentPath = realpath($baseDir . '/' . $subDir);

// Validación de seguridad para que no salgan de la carpeta raíz
if ($currentPath === false || strpos($currentPath, realpath($baseDir)) !== 0) {
    $currentPath = $baseDir;
    $subDir = '';
}

// Escanear elementos del directorio actual
$items = scandir($currentPath);
$folders = [];
$files = [];

foreach ($items as $item) {
    // Ocultar archivos de sistema y el propio script si se desea
    if ($item === '.' || $item === 'index.php') continue;

    if ($item === '..') {
        if ($subDir !== '') {
            $parentDir = dirname($subDir);
            $folders['.. (Volver atrás)'] = ($parentDir === '.' ? '' : $parentDir);
        }
        continue;
    }

    $fullPath = $currentPath . '/' . $item;
    $relativePath = ($subDir === '' ? '' : $subDir . '/') . $item;

    if (is_dir($fullPath)) {
        $folders[$item] = $relativePath;
    } else {
        $files[$item] = $relativePath;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Explorador de Archivos Personalizado</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #121212;
            color: #e0e0e0;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            background: #1e1e1e;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.5);
        }
        h2 {
            color: #4CAF50;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
            margin-top: 0;
            font-size: 1.2rem;
            word-break: break-all;
        }
        ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        li {
            padding: 10px 15px;
            margin: 6px 0;
            background: #252525;
            border-radius: 5px;
            display: flex;
            align-items: center;
            transition: background 0.2s;
        }
        li:hover {
            background: #2d2d2d;
        }
        a {
            color: #61afef;
            text-decoration: none;
            display: flex;
            align-items: center;
            width: 100%;
            font-size: 1rem;
        }
        a:hover {
            text-decoration: underline;
        }
        .folder {
            color: #e5c07b;
            font-weight: bold;
        }
        .file {
            color: #98c379;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>📁 Ruta actual: /<?php echo htmlspecialchars($subDir); ?></h2>
        <ul>
            <?php foreach ($folders as $name => $path): ?>
                <li>
                    <a href="?dir=<?php echo urlencode($path); ?>" class="folder">
                        📂 &nbsp; <?php echo htmlspecialchars($name); ?>
                    </a>
                </li>
            <?php endforeach; ?>

            <?php foreach ($files as $name => $path): ?>
                <li>
                    <a href="<?php echo htmlspecialchars($path); ?>" class="file" target="_blank">
                        📄 &nbsp; <?php echo htmlspecialchars($name); ?>
                    </a>
                </li>
            <?php endforeach; ?>
            
            <?php if (empty($folders) && empty($files)): ?>
                <li style="color: #888; justify-content: center;">Esta carpeta está vacía.</li>
            <?php endif; ?>
        </ul>
    </div>
</body>
</html>