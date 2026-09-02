<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

ob_start();

require_once __DIR__ . "/../src/conexao.php";

global $conexao;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Recebe os dados do formulário
    $nome = trim($_POST['nome'] ?? '');
    $categoria = trim($_POST['categoria'] ?? '');
    $descricao = trim($_POST['descricao']??'');
    $logradouro = trim($_POST['logradouro'] ?? '');
    $numero = trim($_POST['numero'] ?? '');
    $bairro = trim($_POST['bairro'] ?? '');
    $horario_visitacao = trim($_POST['horario_visitacao'] ?? '');
    $mapa = trim($POST['mapa'] ?? '');
    $indicacao_acessibilidade = trim($POST['indicacao_acessibilidade']??'');
    $telefone = trim($_POST['telefone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $imagem = '';

    // Upload da imagem
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {

        $pasta = __DIR__ . "/../assets/img/img/Turismo/";

        if (!is_dir($pasta)) {
            mkdir($pasta, 0777, true);
        }

        $extensao = strtolower(
            pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION)
        );

        // Permite somente imagens
        $extensoesPermitidas = ['jpg', 'jpeg', 'png', 'webp'];

        if (!in_array($extensao, $extensoesPermitidas)) {
            die("Formato de imagem não permitido.");
        }

        $nomeArquivo = pathinfo(
            $_FILES['foto']['name'],
            PATHINFO_FILENAME
        );

        // Remove caracteres especiais
        $nomeArquivo = preg_replace(
            '/[^a-zA-Z0-9_-]/',
            '',
            $nomeArquivo
        );

        // Se o nome ficar vazio
        if (empty($nomeArquivo)) {
            $nomeArquivo = 'ponto_turistico';
        }

        $nomeArquivo = $nomeArquivo . "." . $extensao;

        $destino = $pasta . $nomeArquivo;

        if (move_uploaded_file($_FILES['foto']['tmp_name'], $destino)) {

            $imagem = "/assets/img/img/Turismo/" . $nomeArquivo;

        } else {

            die("Erro ao salvar a imagem.");
        }
    }

    // SQL para cadastrar o ponto turístico
    $sql = "INSERT INTO pontosturisticos
    (
        nome,
        categoria,
        descricao,
        logradouro,
        numero,
        bairro,
        horario_visitacao,
        mapa,
        indicacao_acessibilidade,
        telefone,
        email,
        imagem,
    )
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($conexao, $sql);

    if ($stmt) {

        mysqli_stmt_bind_param(
            $stmt,
            "ssssssssss",
            $nome,
            $categoria,
            $descricao,
            $logradouro,
            $numero,
            $bairro,
            $horario_visitacao,
            $mapa,
            $indicacao_acessibilidade,
            $telefone,
            $email,
            $imagem
        );

        if (mysqli_stmt_execute($stmt)) {

            header("Location: turismo.php");
            exit;

        } else {

            echo "Erro ao cadastrar ponto turístico: "
                . mysqli_stmt_error($stmt);
        }

        mysqli_stmt_close($stmt);

    } else {

        echo "Erro na preparação da query: "
            . mysqli_error($conexao);
    }

} else {

    echo "Acesso inválido.";
}

mysqli_close($conexao);
?>