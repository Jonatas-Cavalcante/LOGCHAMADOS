<?php
// index.php (Versão Final Organizada)
require_once 'conexao.php';

$busca = trim($_GET['busca'] ?? '');

try {
    // Lógica para filtrar se houver busca, ou trazer tudo se estiver vazio
    if (!empty($busca)) {
        $sql = "SELECT * FROM chamados 
                WHERE usuario_auxiliado LIKE :busca 
                   OR setor LIKE :busca 
                   OR problema_relatado LIKE :busca 
                ORDER BY data_atendimento DESC";
        
        $stmt = $pdo->prepare($sql);
        $termo = "%" . $busca . "%";
        $stmt->bindParam(':busca', $termo);
        $stmt->execute();
        
        $chamados = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $stmt = $pdo->query("SELECT * FROM chamados ORDER BY data_atendimento DESC");
        $chamados = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) {
    die("Erro ao buscar chamados: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Chamados</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <header class="topo-sistema">
        <div class="logo">🖥️ Sistema de Chamados</div>
        <div class="usuario-logado">👤 Auxiliar de TI</div>
    </header>

    <main class="conteudo-principal">
        
        <section class="card-formulario">
            <h2>Registrar Chamado</h2>

            <form action="salvar.php" method="POST">
                
                <div class="campo">
                    <label for="usuario_auxiliado">Pessoa Auxiliada</label>
                    <input type="text" id="usuario_auxiliado" name="usuario_auxiliado" placeholder="Ex: Maria..." required>
                </div>
                
                <div class="campo">
                    <label for="setor">Qual era o Setor?</label>
                    <input type="text" id="usuario_setor" name="usuario_setor" placeholder="Ex: Escritório..." required>
                </div>

                <div class="campo">
                    <label for="problema">Qual era o problema?</label>
                    <textarea id="problema" name="problema_relatado" placeholder="Ex: Computador não liga..." required></textarea>
                </div>

                <div class="campo">
                    <label for="realizado">Problema Resolvido?</label>
                        <select id="realizado" name="realizado" required style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 6px; background-color: #f8fafc; font-size: 14px; color: #334155; outline: none;">
                            <option value="1">Sim</option>
                            <option value="0" selected>Não</option>
                        </select>
                </div>

                <div class="campo">
                    <label for="solucao">Como você resolveu?</label>
                    <textarea id="solucao" name="solucao_problema" placeholder="Ex: Troquei o cabo de força..." required></textarea>
                </div>

                <button type="submit" class="botao-enviar">Registrar</button>
            </form>
        </section>

        <section class="card-historico">
            <h2>Histórico de Atendimentos</h2>

            <form method="GET" action="index.php" style="margin-bottom: 20px; display: flex; gap: 10px;">
                <input type="text" name="busca" placeholder="Pesquisar por usuário, setor ou problema..." 
                    value="<?= htmlspecialchars($busca) ?>" 
                    style="flex: 1; margin-bottom: 0;">
                <button type="submit" class="botao-enviar" style="background-color: #3b82f6;">Buscar</button>
                <?php if (!empty($busca)): ?>
                    <a href="index.php" class="botao-enviar" style="background-color: #64748b; text-decoration: none; text-align: center;">Limpar</a>
                <?php endif; ?>
            </form>
            
            <table>
                <thead>
                    <tr>
                        <th>Usuário</th>
                        <th>Setor</th>
                        <th>Problema</th>
                        <th>Realizado?</th>
                        <th>Solução</th>
                        <th>Data</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($chamados)): ?>
                        <tr>
                            <td colspan="5" style="text-align: center;">Nenhum chamado registrado ainda.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($chamados as $chamado): ?>
                            <tr>
                                <td class="col-usuario"><strong><?= htmlspecialchars($chamado['usuario_auxiliado']) ?></strong></td>
                                <td><?= htmlspecialchars($chamado['setor'] ?? '') ?></td>
                                <td><?= nl2br(htmlspecialchars($chamado['problema_relatado'])) ?></td>
                                <td>
                                <?= $chamado['realizado'] == 1 ? '<span style="color: #10b981; font-weight: bold;">Sim</span>' : '<span style="color: #ef4444; font-weight: bold;">Não</span>' ?>
                                </td>
                                <td><?= nl2br(htmlspecialchars($chamado['solucao_problema'])) ?></td>
                                <td class="col-data"><?= date('d/m/Y H:i', strtotime($chamado['data_atendimento'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>

    </main>

</body>
</html>