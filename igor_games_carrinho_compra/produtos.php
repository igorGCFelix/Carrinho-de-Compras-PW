<?php
session_start();  // Inicia a sessão

// Verifica se o usuário está logado
if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    header("Location: login.php");
    exit();
}

// Conexão com o banco de dados
$connect = mysqli_connect("127.0.0.1", "root", "", "loja_felix");
mysqli_set_charset($connect, "UTF8");

// Verifica se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Verifica se foi enviado um produto
    if (isset($_POST['produtos'])) {
        $produtosSelecionados = $_POST['produtos']; // Array com os IDs dos produtos selecionados
        $carrinho = isset($_COOKIE['carrinho']) ? json_decode($_COOKIE['carrinho'], true) : [];

        foreach ($produtosSelecionados as $produtoId) {
            // Verifica se o produto foi selecionado e obtém a quantidade especificada
            if (isset($_POST['quantidade_' . $produtoId])) {
                $quantidade = (int)$_POST['quantidade_' . $produtoId];
                if ($quantidade > 0) {
                    // Se o produto já estiver no carrinho, soma a quantidade
                    if (isset($carrinho[$produtoId])) {
                        $carrinho[$produtoId] += $quantidade;
                    } else {
                        // Se o produto não estiver no carrinho, adiciona com a quantidade
                        $carrinho[$produtoId] = $quantidade;
                    }
                }
            }
        }

        // Salva o carrinho atualizado no cookie
        setcookie('carrinho', json_encode($carrinho), time() + 3600, "/");

        // Redireciona para a página de compras
        header("Location: produtos.php");
        exit();
    }
}

// Excluir carrinho
if (isset($_GET['excluir'])) {
    $produtoExcluir = $_GET['excluir'];
    $carrinho = isset($_COOKIE['carrinho']) ? json_decode($_COOKIE['carrinho'], true) : [];
    if (isset($carrinho[$produtoExcluir])) {
        unset($carrinho[$produtoExcluir]);
        setcookie('carrinho', json_encode($carrinho), time() + 3600, "/");
    }
    header("Location: produtos.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/compras.css">
    <title>Carrinho de Compras</title>
</head>
<body>
    <div class="banner">
        <h1 style="color:orange">Bem-vindo ao Felix Games!</h1>
        <p>Confira os melhores produtos e aproveite as promoções exclusivas.</p>
        <a href="logout.php" style="color: cyan">Sair</a>
    </div>

    <div class="container">
        <form action="produtos.php" method="POST">
            <h3>Controles disponíveis</h3>
            <div class="produto-grid">
                <?php
                $query = mysqli_query($connect, "SELECT id_produto, marca, nome, preco, imagem FROM produtos");
                while ($row = mysqli_fetch_assoc($query)) {
                    $produtoId = $row['id_produto'];
                    $produtoMarca = $row['marca'];
                    $produtoNome = $row['nome'];
                    $produtoPreco = $row['preco'];
                    $produtoImagem = $row['imagem'];
                    echo "
            <div class='produto'>
                <img src='$produtoImagem' alt='Produto $produtoId'>
                <label>Controle $produtoMarca $produtoNome</label>
                <label>R$$produtoPreco</label>
                <input type='number' name='quantidade_$produtoId' min='1' value='1'>
                <input type='checkbox' name='produtos[]' value='$produtoId'>
            </div>";
                }
                ?>
            </div>
            <button type="submit" id="btn_adicionar_carrinho">Adicionar ao Carrinho</button>
        </form>
    </div>

    <div class="background-carrinho">
        <h3 style="color:white;text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.5);">Seu Carrinho</h3>
        <div class="carrinho-container">
            <?php
            $temProduto = false;
            $carrinho = isset($_COOKIE['carrinho']) ? json_decode($_COOKIE['carrinho'], true) : [];
            if (!empty($carrinho)) {
                $ids = implode(',', array_keys($carrinho));
                $query = mysqli_query($connect, "SELECT id_produto, marca, nome, preco, imagem FROM produtos WHERE id_produto IN ($ids)");
                while ($row = mysqli_fetch_assoc($query)) {
                    $produtoId = $row['id_produto'];
                    $produtoMarca = $row['marca'];
                    $produtoNome = $row['nome'];
                    $produtoPreco = $row['preco'];
                    $produtoImagem = $row['imagem'];
                    $quantidade = $carrinho[$produtoId];
                    echo "
                <div class='carrinho-item'>
                    <img src='$produtoImagem' alt='Produto $produtoId'>
                    <span>Controle $produtoMarca $produtoNome</span>
                    <span>R$$produtoPreco</span>
                    <span>Quantidade: $quantidade</span>
                    <a href='?excluir=$produtoId'>Remover</a>
                </div>";
                    $temProduto = true;
                }
                echo "<div class='finalizar-compra-container'>
                        <button id='btn_finalizar_compra' onclick='window.location.href=\"compra_realizada.php\"'>Finalizar Compra</button>
                    </div>";
            }
            if (!$temProduto) {
                echo "<p>Carrinho vazio</p>";
            }
            ?>
        </div>
    </div>
</body>
</html>
