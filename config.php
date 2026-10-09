<?php
$host = "localhost";
$banco = "autoescola";
$usuario = "root";
$senha = "";

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$banco;charset=utf8mb4",
        $usuario,
        $senha,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
} catch (PDOException $e) {
    http_response_code(500);
    header("Content-Type: application/json; charset=UTF-8");

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Não foi possível conectar ao banco de dados."
    ], JSON_UNESCAPED_UNICODE);

    exit;
}