<?php
// salvar.php (Versão Corrigida para MySQL)
require_once 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Pegando os dados exatamente como vêm do formulário (names do HTML)
    $usuario = trim($_POST['usuario_auxiliado'] ?? '');
    $usuario_setor = trim($_POST['usuario_setor'] ?? ''); 
    $problema_relatado = trim($_POST['problema_relatado'] ?? '');
    $solucao_problema = trim($_POST['solucao_problema'] ?? '');

    // Verificando se os campos obrigatórios não estão vazios
    if (!empty($usuario) && !empty($usuario_setor) && !empty($problema_relatado) && !empty($solucao_problema)) {
        
        try {
            // A data_atendimento foi removida daqui porque o MySQL preenche sozinho!
            $sql = "INSERT INTO chamados (usuario_auxiliado, setor, problema_relatado, solucao_problema) 
                    VALUES (:usuario, :setor, :problema_relatado, :solucao_problema)";
            
            $stmt = $pdo->prepare($sql);
            
            $stmt->bindParam(':usuario', $usuario);
            $stmt->bindParam(':setor', $usuario_setor); 
            $stmt->bindParam(':problema_relatado', $problema_relatado);
            $stmt->bindParam(':solucao_problema', $solucao_problema);
            
            $stmt->execute();
            
        } catch (PDOException $e) {
            die("Erro ao salvar o chamado: " . $e->getMessage());
        }
    }
}

// Redireciona de volta para a página principal
header("Location: index.php");
exit;