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
    <title>Sistema deee Chamados</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <header class="topo-sistema">
        <div class="logo">🖥️ Sistema de Chamados</div>
        <div class="usuario-logado">👤Admin</div>
    </header>

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
                        <select id="usuario_setor" name="usuario_setor" required style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 6px; background-color: #f8fafc; font-size: 14px; color: #334155; outline: none;">
                            <option value="Escritório">Escritório</option>
                            <option value="Serra">Serra</option>
                            <option value="Estoque">Estoque</option>
                            <option value="Plano de corte">Plano de corte</option>
                            <option value="Equipamentos">Equipamentos</option>
                            <option value="Iluminação">Iluminação</option>
                            <option value="Caixa">Caixa</option>
                            
                            
                        </select>
                    </div>
                    

                    

                    <div class="campo">
                        <label for="problema">Qual era o problema?</label>
                        <textarea id="problema" name="problema_relatado" placeholder="Ex: Computador não liga..." required></textarea>
                    </div>

                      
                    <div class="campo">
                        <label for="realizado">Problema Resolvido?</label>
                        <select id="realizado" name="realizado" onchange="ajustarCampoSolucao()" required style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 6px; background-color: #f8fafc; font-size: 14px; color: #334155; outline: none;">
                            <option value="1">Sim</option>
                            <option value="0" selected>Não</option>
                        </select>
                    </div>

                    <div class="campo">
                        <label for="solucao" id="label-solucao">Observações / Próximos Passos</label>
                        <textarea id="solucao" name="solucao_problema" placeholder="Ex: Detalhe o motivo de não ter sido resolvido ou o que precisa ser feito..." required></textarea>
                    </div>


                    <button type="submit" class="botao-enviar">Registrar</button>
                </form>
            </div>
        </section>

        <section id="aba-historico" class="conteudo-aba">
            <div class="card-historico">
                
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
                            <th>Solução / Observação</th>
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
                <h3 style="margin-top: 0; color: #1e293b; border-bottom: 1px solid #e2e8f0; padding-bottom: 12px; margin-bottom: 16px;">🖥️ Detalhes da Solução / Observação</h3>
                <div id="textoSolucaoModal" style="color: #475569; line-height: 1.6; white-space: pre-wrap; font-size: 14px;"></div>
            </div>
        </div>

    </main>

    <script>
    function mudarAba(evento, idAba) {
        var conteudos = document.getElementsByClassName("conteudo-aba");
        for (var i = 0; i < conteudos.length; i++) {
            conteudos[i].style.display = "none";
        }

        var botoes = document.getElementsByClassName("btn-link-aba");
        for (var i = 0; i < botoes.length; i++) {
            botoes[i].classList.remove("active");
        }

        document.getElementById(idAba).style.display = "block";
        evento.currentTarget.classList.add("active");
    }

    function abrirMinhaSolucao(botao) {
        var textoCompleto = botao.getAttribute('data-solucao');
        document.getElementById('textoSolucaoModal').textContent = textoCompleto;
        document.getElementById('janelaModalSolucao').style.display = 'block';
    }

    function ajustarCampoSolucao() {
    var selectRealizado = document.getElementById("realizado");
    var labelSolucao = document.getElementById("label-solucao");
    var textareaSolucao = document.getElementById("solucao");

    if (selectRealizado.value === "1") {
        // Se mudou para "Sim"
        labelSolucao.textContent = "Como você resolveu?";
        textareaSolucao.placeholder = "Ex: Troquei o cabo de força...";
    } else {
        // Se mudou para "Não"
        labelSolucao.textContent = "Observações / Próximos Passos";
        textareaSolucao.placeholder = "Ex: Detalhe o motivo de não ter sido resolvido ou o que precisa ser feito...";
    }
}

    function fecharMinhaSolucao() {
        document.getElementById('janelaModalSolucao').style.display = 'none';
    }

    window.onclick = function(event) {
        var modal = document.getElementById('janelaModalSolucao');
        if (event.target == modal) { 
            modal.style.display = 'none'; 
        }
    }

    window.addEventListener("DOMContentLoaded", function() {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('busca') && urlParams.get('busca') !== '') {
            const botaoHistorico = document.querySelector(".btn-link-aba:nth-child(2)");
            if (botaoHistorico) {
                mudarAba({ currentTarget: botaoHistorico }, 'aba-historico');
            }
        }
    });

    
    </script>

</body>
</html>