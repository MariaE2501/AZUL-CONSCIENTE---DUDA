<?php
$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'guardioes_oceano';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    echo "✅ Conectado com sucesso!";
} catch(PDOException $e) {
    echo "❌ Erro: " . $e->getMessage();
}
