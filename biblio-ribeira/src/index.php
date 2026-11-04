<?php
declare(strict_types=1);

// ---------------------------------------------------------------
// Catálogo das bibliotecas de Ribeira Alta
// Toda a configuración chega por variables de contorna.
// ---------------------------------------------------------------

function h(?string $texto): string
{
    return htmlspecialchars((string) $texto, ENT_QUOTES, 'UTF-8');
}

$contorno = getenv('CONTORNO') ?: 'sen definir';

$config = [
    'DB_HOST'     => getenv('DB_HOST'),
    'DB_NAME'     => getenv('DB_NAME'),
    'DB_USER'     => getenv('DB_USER'),
    'DB_PASSWORD' => getenv('DB_PASSWORD'),
];

// --- Contador de visitas: gárdase no volume montado en /var/www/datos ---
$ficheiroVisitas = '/var/www/datos/visitas.txt';
$visitas = is_file($ficheiroVisitas) ? (int) file_get_contents($ficheiroVisitas) + 1 : 1;
if (@file_put_contents($ficheiroVisitas, (string) $visitas, LOCK_EX) === false) {
    $visitas = null; // non se pode escribir no cartafol de datos
}

$erro         = null;
$libros       = [];
$bibliotecas  = [];
$filtro       = trim($_GET['biblioteca'] ?? '');
$consulta     = '';

$faltan = array_keys(array_filter($config, fn($valor) => $valor === false || $valor === ''));

if ($faltan) {
    $erro = 'Faltan variables de contorna: ' . implode(', ', $faltan);
} else {
    try {
        $pdo = new PDO(
            "mysql:host={$config['DB_HOST']};dbname={$config['DB_NAME']};charset=utf8mb4",
            $config['DB_USER'],
            $config['DB_PASSWORD'],
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_TIMEOUT => 3]
        );

        // --- Engadir un libro (formulario) ---
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $titulo     = trim($_POST['titulo'] ?? '');
            $autoria    = trim($_POST['autoria'] ?? '');
            $ano        = ($_POST['ano'] ?? '') === '' ? null : (int) $_POST['ano'];
            $biblioteca = trim($_POST['biblioteca'] ?? '');

            if ($titulo !== '' && $autoria !== '' && $biblioteca !== '') {
                $insercion = $pdo->prepare(
                    'INSERT INTO libros (titulo, autoria, ano, biblioteca) VALUES (?, ?, ?, ?)'
                );
                $insercion->execute([$titulo, $autoria, $ano, $biblioteca]);
            }
            header('Location: ./');
            exit;
        }

        // --- Consulta do catálogo ---
        if ($filtro !== '') {
            $consulta = 'SELECT titulo, autoria, ano, biblioteca FROM libros WHERE biblioteca = ? ORDER BY titulo';
            $sentenza = $pdo->prepare($consulta);
            $sentenza->execute([$filtro]);
        } else {
            $consulta = 'SELECT titulo, autoria, ano, biblioteca FROM libros ORDER BY titulo';
            $sentenza = $pdo->query($consulta);
        }
        $libros = $sentenza->fetchAll(PDO::FETCH_ASSOC);

        $bibliotecas = $pdo->query('SELECT DISTINCT biblioteca FROM libros ORDER BY biblioteca')
                           ->fetchAll(PDO::FETCH_COLUMN);
    } catch (PDOException $e) {
        $erro = 'Non se puido conectar coa base de datos: ' . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="gl">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Catálogo · Bibliotecas de Ribeira Alta</title>
  <style>
    body { font-family: system-ui, sans-serif; background: #f7f5ef; color: #14213d; margin: 0; }
    header { background: #22608f; color: #f7f5ef; padding: 1.5rem 2rem; }
    header p { margin: 0.3rem 0 0; }
    .contorno { display: inline-block; background: #fbe3c6; color: #14213d; padding: 0.2rem 0.7rem; border-radius: 1rem; font-weight: bold; }
    main { max-width: 900px; margin: 2rem auto; padding: 0 1rem; }
    table { width: 100%; border-collapse: collapse; background: #fff; }
    th, td { text-align: left; padding: 0.6rem; border-bottom: 1px solid #d5dce4; }
    th { background: #14213d; color: #f7f5ef; }
    .erro { background: #fdecea; border-left: 6px solid #c0392b; padding: 1rem; }
    code { background: #e9eef4; padding: 0.2rem 0.4rem; border-radius: 4px; }
    form.engadir { display: grid; grid-template-columns: 1fr 1fr; gap: 0.6rem; background: #fff; padding: 1rem; }
    form.engadir button { grid-column: span 2; }
    input, select, button { font: inherit; padding: 0.4rem; }
    footer { text-align: center; color: #5a6575; font-size: 0.9rem; margin: 3rem 0 1rem; }
  </style>
</head>
<body>
  <header>
    <h1>Catálogo das bibliotecas de Ribeira Alta</h1>
    <p>Contorno: <span class="contorno"><?= h($contorno) ?></span></p>
  </header>
  <main>
<?php if ($erro !== null): ?>
    <p class="erro"><?= h($erro) ?></p>
<?php else: ?>
    <form method="get">
      <label>Biblioteca:
        <select name="biblioteca" onchange="this.form.submit()">
          <option value="">Todas</option>
<?php foreach ($bibliotecas as $b): ?>
          <option value="<?= h($b) ?>" <?= $b === $filtro ? 'selected' : '' ?>><?= h($b) ?></option>
<?php endforeach; ?>
        </select>
      </label>
    </form>

    <p>Consulta executada: <code><?= h($consulta) ?></code></p>
    <p><?= count($libros) ?> libros atopados.</p>

    <table>
      <tr><th>Título</th><th>Autoría</th><th>Ano</th><th>Biblioteca</th></tr>
<?php foreach ($libros as $libro): ?>
      <tr>
        <td><?= h($libro['titulo']) ?></td>
        <td><?= h($libro['autoria']) ?></td>
        <td><?= h((string) $libro['ano']) ?></td>
        <td><?= h($libro['biblioteca']) ?></td>
      </tr>
<?php endforeach; ?>
    </table>

    <h2>Engadir un libro</h2>
    <form class="engadir" method="post">
      <input name="titulo" placeholder="Título" required>
      <input name="autoria" placeholder="Autoría" required>
      <input name="ano" type="number" placeholder="Ano">
      <input name="biblioteca" placeholder="Biblioteca" required>
      <button type="submit">Engadir</button>
    </form>
<?php endif; ?>
  </main>
  <footer>
    Servidor web: <?= h(gethostname() ?: '?') ?> ·
    Visitas: <?= $visitas === null ? 'non se pode gardar o contador' : $visitas ?> ·
    Versión 1
  </footer>
</body>
</html>
