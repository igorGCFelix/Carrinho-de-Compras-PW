<?php
session_start();

// Conectar ao banco de dados
$connect = mysqli_connect("127.0.0.1", "root", "", "loja_felix");
mysqli_set_charset($connect, "UTF8");

// Verifica se a conexão foi bem-sucedida
if (!$connect) {
    die("Conexão falhou: " . mysqli_connect_error());
}

// Verifica se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $usuario = $_POST['input_usuario'];
    $senha = $_POST['input_senha']; // Senha inserida pelo usuário (texto puro)

    // Consulta para obter a senha armazenada
    $query = mysqli_query($connect, "SELECT senha FROM usuario WHERE nome='$usuario'");

    // Verifica se houve erro na consulta
    if (!$query) {
        die("Erro na consulta: " . mysqli_error($connect));
    }

    // Verifica se encontrou o usuário
    if (mysqli_num_rows($query) > 0) {
        $row = mysqli_fetch_assoc($query);
        $senhaBanco = $row['senha']; // Senha armazenada (texto puro)

        // Compara a senha fornecida (convertida para MD5) com a senha armazenada
        if (md5($senha) === md5($senhaBanco)) {  // Corrigido a comparação da senha
            $_SESSION['logado'] = true;  // Modificado para usar a chave 'logado'
            $_SESSION['user'] = $usuario;
            header("Location: produtos.php");
            exit();
        }
    }

    // Caso as credenciais não sejam válidas
    $_SESSION['login_error'] = "Usuário ou senha inválidos.";
    header("Location: index.php");
    exit();
}

// Redireciona caso o acesso não seja por POST
header("Location: index.php");
exit();
?>
