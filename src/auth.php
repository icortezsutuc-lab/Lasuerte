<?php
// src/auth.php
require_once __DIR__ . '/db.php';
session_start();

function login($username, $password){
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM users WHERE username = ? LIMIT 1");
    $stmt->execute([$username]);
    $u = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($u && password_verify($password, $u['password'])) {
        $_SESSION['user'] = ['id'=>$u['id'],'username'=>$u['username'],'full_name'=>$u['full_name'],'role'=>$u['role']];
        return true;
    }
    return false;
}

function logout(){
    session_destroy();
}

function require_login(){
    if (!isset($_SESSION['user'])) {
        header('Location: login.php');
        exit;
    }
}

function current_user(){
    return $_SESSION['user'] ?? null;
}
