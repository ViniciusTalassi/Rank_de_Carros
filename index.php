<?php
function readData()
{
    $data = file_get_contents('usuarios.json');
    return json_decode($data, true) ?? [];
}

function writeData($data)
{
    file_put_contents('usuarios.json', json_encode($data, JSON_PRETTY_PRINT));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['action']) && $_POST['action'] == 'create') {
        $data = readData();
        $newUser = [
            'id' => uniqid(),
            'rank' => $_POST['rank'],
            'marca' => $_POST['marca'],
            'modelo' => $_POST['modelo'],
            'ano' => $_POST['ano'],
            'cor' => $_POST['cor'],
        ];
        $data[] = $newUser;
        writeData($data);
        echo "Novo registro criado com sucesso";
        header("Location: " . strtok($_SERVER["REQUEST_URI"], '?'));
        exit();
    } elseif (isset($_POST['action']) && $_POST['action'] == 'update') {
        $data = readData();
        foreach ($data as &$user) {
            if ($user['id'] == $_POST['id']) {
                $user['rank'] = $_POST['rank'];
                $user['marca'] = $_POST['marca'];
                $user['modelo'] = $_POST['modelo'];
                $user['ano'] = $_POST['ano'];
                $user['cor'] = $_POST['cor'];
                break;
            }
        }
        writeData($data);
        header("Location: " . strtok($_SERVER["REQUEST_URI"], '?'));
        exit();
    }
} elseif ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['delete'])) {
    $data = readData();
    $data = array_filter($data, function ($user) {
        return $user['id'] != $_GET['delete'];
    });
    writeData(array_values($data));
    // Redireciona após a exclusão
    header("Location: " . strtok($_SERVER["REQUEST_URI"], '?'));
    exit();
}

// HTML Atualizado //

?>
<!DOCTYPE html>
<html>

<head>
    <title>Rank de Carros</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="shortcut icon" href="imagens/favicon.ico" type="image/x-icon">
</head>

<body>
    <div class="navbar">
        <div class="nav-container">
            <div class="menu-icon">
                <i class="fas fa-bars"></i>
            </div>
            <div class="logo">
                <img src="imagens/logo.png" alt="Logo">
            </div>
            <div class="nav-icons">
                <i class="fas fa-search"><a href=""></a></i>
                <i class="fas fa-map-marker-alt"></i>
                <i class="fas fa-heart"></i>
            </div>
        </div>
    </div>
    <div class="container">
        <header>
            <h1>Lista de Ranking de Carros</h1>
        </header>
        <table>
            <tr>
                <th>Rank</th>
                <th>Marca</th>
                <th>Modelo</th>
                <th>Ano</th>
                <th>Cor</th>
                <th>Amostra</th>
                <th>Ações</th>
            </tr>
            <?php
            $data = readData();
            usort($data, function ($a, $b) {
                return $a['rank'] <=> $b['rank'];
            });
            foreach ($data as $user) {
                echo "<tr>
                        <td>{$user['rank']}</td>
                        <td>{$user['marca']}</td>
                        <td>{$user['modelo']}</td>
                        <td>{$user['ano']}</td>
                        <td>{$user['cor']}</td>
                        <td>
                        <div style='width: fill-parent; height: 20px; background-color:{$user['cor']}';></div>
                        </td>
                        <td>
                            <a href='?update={$user['id']}'>Editar</a>
                            <a href='?delete={$user['id']}'>Deletar</a>
                        </td>
                      </tr>";
            }
            ?>
        </table>
        <br><br />
        <?php if (isset($_GET['update'])):
            $data = readData();
            $user = array_filter($data, function ($u) {
                return $u['id'] == $_GET['update'];
            });
            $user = array_values($user)[0];
            ?>
            <form method="POST" action="">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
                Rank: <input type="number" name="rank" value="<?php echo $user['rank']; ?>" required><br>
                Marca: <input type="text" name="marca" value="<?php echo $user['marca']; ?>" required><br>
                Modelo: <input type="text" name="modelo" value="<?php echo $user['modelo']; ?>" required><br>
                Ano: <input type="number" name="ano" value="<?php echo $user['ano']; ?>" required><br>
                Cor: <input type="color" name="cor" value="<?php echo $user['cor']; ?>" required><br>
                <input type="submit" value="Atualizar">
            </form>
        <?php else: ?>
            <form method="POST" action="">
                <input type="hidden" name="action" value="create">
                Rank: <input type="number" name="rank" required><br>
                Marca: <input type="text" name="marca" required><br>
                Modelo: <input type="text" name="modelo" required><br>
                Ano: <input type="number" name="ano" required><br>
                Cor: <input type="color" name="cor" required><br>
                <input type="submit" value="Cadastrar">
            </form>
        <?php endif; ?>
    </div>
    <footer>
        Vinicius Talassi
    </footer>
</body>

</html>