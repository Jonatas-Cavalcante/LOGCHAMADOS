<?php
require_once 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Dados do Formulário - index.php
    $usuario = trim($_POST['usuario_auxiliado'] ?? '');
    $usuario_setor = trim($_POST['usuario_setor'] ?? ''); 
    $problema_relatado = trim($_POST['problema_relatado'] ?? '');
    $solucao_problema = trim($_POST['solucao_problema'] ?? '');


    $realizado = isset($_POST['realizado']) ? $_POST['realizado'] : '0'; 

    if (!empty($usuario) && !empty($usuario_setor) && !empty($problema_relatado) && !empty($solucao_problema)) {
        
        try {
            $sql = "INSERT INTO chamados (usuario_auxiliado, setor, problema_relatado, solucao_problema, realizado) 
                    VALUES (:usuario, :setor, :problema_relatado, :solucao_problema, :realizado)";

            $stmt = $pdo->prepare($sql);
            
            $stmt->bindParam(':usuario', $usuario);
            $stmt->bindParam(':setor', $usuario_setor); 
            $stmt->bindParam(':problema_relatado', $problema_relatado);
            $stmt->bindParam(':solucao_problema', $solucao_problema);
         
            
            $stmt->bindValue(':realizado', (int)$realizado, PDO::PARAM_INT);
            
            $stmt->execute();
            
        } catch (PDOException $e) {
            die("Erro ao salvar o chamado: " . $e->getMessage());
        }
    }
}

header("Location: index.php");
exit;