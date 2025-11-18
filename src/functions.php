<?php
// src/functions.php
require_once __DIR__ . '/db.php';

function calcularFactor($sorteo){
    $map = [
        'La Santa' => 25,
        'La Rifa'  => 70,
        'El Sorteo'=> 150
    ];
    return $map[$sorteo] ?? 0;
}

function calcularPremio($monto, $sorteo){
    return $monto * calcularFactor($sorteo);
}

function esCumple($birth_date, $fecha_evento){
    if (!$birth_date) return false;
    return date('m-d', strtotime($birth_date)) === date('m-d', strtotime($fecha_evento));
}

// sumar 5 días hábiles
function sumarDiasHabiles($fecha, $dias=5){
    $dt = new DateTime($fecha);
    $added = 0;
    while ($added < $dias) {
        $dt->modify('+1 day');
        $dow = (int)$dt->format('N'); // 1..7
        if ($dow < 6) $added++;
    }
    return $dt->format('Y-m-d');
}

function generarVoucher(){
    return strtoupper(uniqid('VCH'));
}

/* Funciones DB helpers */
function findClienteByName($nombre){
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM clientes WHERE nombre = ? LIMIT 1");
    $stmt->execute([$nombre]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function crearCliente($nombre, $telefono = null, $birth_date = null){
    $db = getDB();
    $stmt = $db->prepare("INSERT INTO clientes(nombre, telefono, birth_date) VALUES (?, ?, ?)");
    $stmt->execute([$nombre, $telefono, $birth_date]);
    return $db->lastInsertId();
}
