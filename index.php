<?php
require_once 'conexao.php';

$busca = trim($_GET['busca'] ?? '');

try {
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
        
        <main class="conteudo-principal">
        
        <div class="abas-titulos">
            <button class="btn-link-aba active" onclick="mudarAba(event, 'aba-registro')">Registrar Chamado</button>
            <button class="btn-link-aba" onclick="mudarAba(event, 'aba-historico')">Histórico de Atendimentos</button>
        </div>

        <section id="aba-registro" class="conteudo-aba active">
            <div class="card-formulario">
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
            </div>
        </section>

 
                
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
                                <td colspan="6" style="text-align: center;">Nenhum chamado registrado ainda.</td>
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
                                    <td>
                                        <button type="button" class="btn-ver-solucao" data-solucao="<?= htmlspecialchars($chamado['solucao_problema']) ?>" onclick="abrirMinhaSolucao(this)">
                                            📄 Ver Resolução
                                        </button>
                                    </td>
                                    <td class="col-data"><?= date('d/m/Y H:i', strtotime($chamado['data_atendimento'])) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <div id="janelaModalSolucao" class="modal">
            <div class="modal-conteudo">
                <span class="botao-fechar" onclick="fecharMinhaSolucao()">&times;</span>
                <h3 style="margin-top: 0; color: #1e293b; border-bottom: 1px solid #e2e8f0; padding-bottom: 12px; margin-bottom: 16px;">🖥️ Detalhes da Solução Aplicada</h3>
                <div id="textoSolucaoModal" style="color: #475569; line-height: 1.6; white-space: pre-wrap; font-size: 14px;"></div>
            </div>
        </div>

    </main>

    </main>

    <script>
    function mudarAba(evento, idAba) {
        // 1. Esconde absolutamente todas as abas
        var conteudos = document.getElementsByClassName("conteudo-aba");
        for (var i = 0; i < conteudos.length; i++) {
            conteudos[i].style.display = "none";
        }

        // 2. Remove a linha azul de todos os títulos
        var botoes = document.getElementsByClassName("btn-link-aba");
        for (var i = 0; i < botoes.length; i++) {
            botoes[i].classList.remove("active");
        }

        // 3. Mostra apenas a aba clicada e bota a linha azul nela
        document.getElementById(idAba).style.display = "block";
        evento.currentTarget.classList.add("active");
    }

    // Funções do Modal (Mantenha aqui para o botão "Ver Resolução" funcionar)
    function abrirMinhaSolucao(botao) {
        var textoCompleto = botao.getAttribute('data-solucao');
        document.getElementById('textoSolucaoModal').textContent = textoCompleto;
        document.getElementById('janelaModalSolucao').style.display = 'block';
    }
    function fecharMinhaSolucao() {
        document.getElementById('janelaModalSolucao').style.display = 'none';
    }
    window.onclick = function(event) {
        var modal = document.getElementById('janelaModalSolucao');
        if (event.target == modal) { modal.style.display = 'none'; }
    }

    // Esse código roda automático assim que a página termina de carregar
window.addEventListener("DOMContentLoaded", function() {
    // 1. Verifica se existe o termo "?busca=" na URL do navegador
    const urlParams = new URLSearchParams(window.location.search);
    
    if (urlParams.has('busca') && urlParams.get('busca') !== '') {
        // 2. Cria um evento falso para o JavaScript entender o clique
        const eventoFalso = { currentTarget: document.querySelector(".btn-link-aba:nth-child(2)") };
        
        // 3. Executa a sua função mudarAba jogando direto para o histórico
        mudarAba(eventoFalso, 'aba-historico');
    }
});
</script>

</body>
</html>