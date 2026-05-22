<?php
// conexao.php (Ajustado para a sua Instância Local do MySQL Workbench)

$host = 'localhost';
$port = '3308'; // A porta padrão do MySQL80 geralmente é 3306
$db_name = 'sistema_chamados'; 
$username = 'root'; // Usuário padrão

// ⚠️ ATENÇÃO AQUI: O MySQL Workbench exige uma senha na instalação (ao contrário do XAMPP).
// Coloque dentro das aspas abaixo a senha que você digita para entrar no seu MySQL Workbench:
$password = 'P@ssw0rd'; // Substitua pela sua senha do MySQL Workbench

try {
    // Adicionamos o parâmetro port na string de conexão
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$db_name;charset=utf8", $username, $password);
    
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    die("Erro ao conectar ao banco MySQL: " . $e->getMessage());
}