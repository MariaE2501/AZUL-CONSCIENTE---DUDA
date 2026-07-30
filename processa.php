<?php
header('Content-Type: application/json');

// NÃO mostrar erro na tela (evita quebrar JSON)
ini_set('display_errors', 0);

// CONFIG BANCO
$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'guardioes_oceano';

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch(PDOException $e) {
    echo json_encode([
        "success" => false,
        "message" => "Erro ao conectar com banco"
    ]);
    exit;
}

// RECEBE DADOS
$nome = trim($_POST['nome'] ?? '');
$email = strtolower(trim($_POST['email'] ?? ''));
$educacao = trim($_POST['educacao_descoberta'] ?? '');
$aprendizado = trim($_POST['exp_aprendizado'] ?? '');
$multiplicador = trim($_POST['educacao_multiplicador'] ?? '');
$pergunta3 = trim($_POST['pergunta3'] ?? '');

// VALIDAÇÃO COMPLETA
$erros = [];

if (strlen($nome) < 3) $erros[] = "Nome inválido";
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $erros[] = "Email inválido";
if (strlen($pergunta3) < 10) $erros[] = "Resposta muito curta";

if (empty($educacao)) $erros[] = "Selecione educação";
if (empty($aprendizado)) $erros[] = "Selecione aprendizado";
if (empty($multiplicador)) $erros[] = "Selecione multiplicador";

if (!empty($erros)) {
    echo json_encode([
        "success" => false,
        "errors" => $erros
    ]);
    exit;
}

// VERIFICA DUPLICATA
$stmt = $pdo->prepare("SELECT id FROM respostas_questionario WHERE email = ?");
$stmt->execute([$email]);

if ($stmt->fetch()) {
    echo json_encode([
        "success" => false,
        "message" => "Este email já respondeu!"
    ]);
    exit;
}

// INSERE
$stmt = $pdo->prepare("
    INSERT INTO respostas_questionario 
    (nome, email, educacao_descoberta, exp_aprendizado, educacao_multiplicador, pergunta3) 
    VALUES (?, ?, ?, ?, ?, ?)
");

try {
    $stmt->execute([
        $nome,
        $email,
        $educacao,
        $aprendizado,
        $multiplicador,
        $pergunta3
    ]);

    echo json_encode([
        "success" => true,
        "message" => "Dados salvos com sucesso!",
        "redirect" => "inicio.html"
    ]);

} catch(PDOException $e) {
    echo json_encode([
        "success" => false,
        "message" => "Erro ao salvar no banco"
    ]);
}
