
<?php
header("Content-Type: application/json; charset=UTF-8");
require_once "config.php";

$rota = $_GET["rota"] ?? "";
$metodo = $_SERVER["REQUEST_METHOD"];

function listar($pdo, $tabela) {
    $sql = "SELECT * FROM `$tabela`";
    $stmt = $pdo->query($sql);

    echo json_encode(
        $stmt->fetchAll(PDO::FETCH_ASSOC),
        JSON_UNESCAPED_UNICODE
    );
    exit;
}

function criarAluno($pdo) {
    $dados = json_decode(file_get_contents("php://input"), true);

    if (!$dados) {
        $dados = $_POST;
    }

    $campos = ["nome", "email", "telefone", "data_nascimento"];

    foreach ($campos as $campo) {
        if (empty($dados[$campo])) {
            http_response_code(400);
            echo json_encode([
                "mensagem" => "Preencha o campo $campo."
            ]);
            exit;
        }
    }

    try {
        $sql = "INSERT INTO Alunos
                (nome, email, telefone, data_nascimento)
                VALUES (:nome, :email, :telefone, :data_nascimento)";

        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            ":nome" => $dados["nome"],
            ":email" => $dados["email"],
            ":telefone" => $dados["telefone"],
            ":data_nascimento" => $dados["data_nascimento"]
        ]);

        http_response_code(201);
        echo json_encode([
            "mensagem" => "Aluno cadastrado com sucesso!"
        ], JSON_UNESCAPED_UNICODE);

    } catch (PDOException $e) {
        if ($e->getCode() == "23000") {
            http_response_code(409);
            echo json_encode([
                "mensagem" => "Este e-mail já está cadastrado ou existe um dado duplicado."
            ], JSON_UNESCAPED_UNICODE);
        } else {
            http_response_code(500);
            echo json_encode([
                "mensagem" => "Erro ao cadastrar aluno."
            ], JSON_UNESCAPED_UNICODE);
        }
    }

    exit;
}

try {
    switch ($rota) {
        case "alunos":
            if ($metodo === "GET") {
                listar($pdo, "Alunos");
            }
            break;

        case "instrutores":
            if ($metodo === "GET") {
                listar($pdo, "Instrutores");
            }
            break;

        case "alunos/create":
            if ($metodo === "POST") {
                criarAluno($pdo);
            }
            break;

        default:
            http_response_code(404);
            echo json_encode(["mensagem" => "Rota não encontrada."]);
            exit;
    }

    http_response_code(405);
    echo json_encode(["mensagem" => "Método não permitido."]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "mensagem" => "Erro ao consultar o banco. Confira as tabelas."
    ], JSON_UNESCAPED_UNICODE);
}