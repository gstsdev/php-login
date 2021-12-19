<?php
// Initialize the session
session_start();

// Check if the user is logged in, if not then redirect him to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Bem-vindo</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/styles.css" />
    <link rel="stylesheet" href="assets/css/welcome.css" />
</head>

<body>
    <h1 class="my-5">Olá, <b><?php echo htmlspecialchars($_SESSION["username"]); ?></b>. Bem-vindo à <i>sua</i> página.</h1>
    <p>
        <a href="reset-password.php" class="btn btn-warning">Redefina Sua Senha</a>
        <a href="logout.php" class="btn btn-danger ml-3">Sair de Sua Conta</a>
    </p>

    <div class="d-flex flex-column justify-content-center align-items-center h-50">
        <span class="my-5">Selecione um elemento:</span>

        <div id="element-settings"></div>

        <button id="select-btn" class="btn btn-secondary" onclick="selectElement()">Selecionar</button>
    </div>

    <script type="text/javascript" src="assets/js/main.js"></script>
</body>

</html>