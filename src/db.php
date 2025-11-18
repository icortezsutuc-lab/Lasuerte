<?php
// src/db.php
date_default_timezone_set('America/Guatemala');

function getDB() {
    $db_path = __DIR__ . '/../database/lasuerte.db';
    $dir = dirname($db_path);
    if (!is_dir($dir)) mkdir($dir, 0777, true);
    if (!file_exists($db_path)) file_put_contents($db_path, '');

    $pdo = new PDO('sqlite:' . $db_path);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Tablas básicas
    $pdo->exec("
      CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        username TEXT UNIQUE,
        password TEXT,
        full_name TEXT,
        role TEXT
      );
    ");

    $pdo->exec("
      CREATE TABLE IF NOT EXISTS clientes (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        nombre TEXT,
        telefono TEXT,
        birth_date TEXT,
        fecha_registro TEXT DEFAULT (datetime('now'))
      );
    ");

    $pdo->exec("
      CREATE TABLE IF NOT EXISTS eventos (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        sorteo TEXT,
        event_date TEXT,       -- YYYY-MM-DD
        event_number INTEGER
      );
    ");

    $pdo->exec("
      CREATE TABLE IF NOT EXISTS apuestas (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        cliente_id INTEGER,
        numero TEXT,
        monto REAL,
        sorteo TEXT,
        evento_dia INTEGER,
        premio_estimado REAL,
        es_cumple INTEGER DEFAULT 0,
        fecha_registro TEXT DEFAULT (datetime('now')),
        fecha_limite TEXT,
        atendido_por TEXT,
        voucher_code TEXT,
        pagado INTEGER DEFAULT 0,
        fecha_pago TEXT,
        FOREIGN KEY(cliente_id) REFERENCES clientes(id)
      );
    ");

    $pdo->exec("
      CREATE TABLE IF NOT EXISTS raffle_results (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        sorteo TEXT,
        event_date TEXT,
        event_number INTEGER,
        winning_number TEXT,
        published_at TEXT DEFAULT (datetime('now'))
      );
    ");

    $pdo->exec("
      CREATE TABLE IF NOT EXISTS payments (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        apuesta_id INTEGER,
        paid_amount REAL,
        paid_at TEXT DEFAULT (datetime('now')),
        paid_by TEXT,
        FOREIGN KEY(apuesta_id) REFERENCES apuestas(id)
      );
    ");

    // Insertar usuarios por defecto si no existen
    $stmt = $pdo->query("SELECT COUNT(*) FROM users");
    if ($stmt->fetchColumn() == 0) {
        $pdo->prepare("INSERT INTO users (username,password,full_name,role) VALUES (?,?,?,?)")
            ->execute(['admin', password_hash('1234', PASSWORD_DEFAULT), 'Administrador', 'admin']);
        $pdo->prepare("INSERT INTO users (username,password,full_name,role) VALUES (?,?,?,?)")
            ->execute(['vendedor', password_hash('1234', PASSWORD_DEFAULT), 'Cajero', 'cajero']);
    }

    return $pdo;
}
