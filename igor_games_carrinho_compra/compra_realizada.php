<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit();
}

$connect = mysqli_connect("127.0.0.1", "root", "", "loja_felix");
mysqli_set_charset($connect, "UTF8");

// Limpar o carrinho após a compra
setcookie('carrinho', '', time() - 10800, '/');
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/compras.css">
    <title>Compra Realizada</title>
</head>

<body>
    <div class="background-carrinho">
        <h3 style="color:white;text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.5);">Agradecemos a sua compra!</h3>
        <p style="color:white;text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.5);">Seu pedido está sendo processado. Em breve
            você receberá informações sobre o envio.</p>
        <button onclick="window.location.href='produtos.php'"
            style="cursor: pointer;margin-top:20px;font-size:15px;background-color:purple;padding:8px;padding-inline:15px;color:white;border:none;border-radius:5px">Voltar
            para a loja</button>
    </div>

    <div class="container">
        <h3>Resumo da Compra</h3>
        <div class="produto-grid">
            <?php
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
                    $total = $produtoPreco * $quantidade;
                    echo "
                    <div class='produto'>
                        <img src='$produtoImagem' alt='Produto $produtoId'>
                        <label>Controle $produtoMarca $produtoNome</label>
                        <label>R$$produtoPreco</label>
                        <label>Quantidade: $quantidade</label>
                        <label>Total: R$$total</label>
                    </div>";
                }
            }
            ?>
        </div>
    </div>


</body>

</html>