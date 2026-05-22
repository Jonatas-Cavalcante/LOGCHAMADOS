# 🖥️ Sistema de Registro de Chamados de TI

Este é um sistema web desenvolvido para organizar, registrar e pesquisar os atendimentos diários realizados pelo suporte de TI. Ele funciona como uma base de conhecimento prática para agilizar a resolução de problemas repetidos na empresa.

## 🚀 Funcionalidades
* **Registro de Atendimentos:** Cadastro de Usuário, Setor, Problema, Solução e Status (Se foi realizado ou não).
* **Barra de Pesquisa Inteligente:** Filtro rápido por palavra-chave para buscar históricos antigos.
* **Interface Moderna:** Visual limpo e responsivo baseado na identidade visual corporativa.

## 🛠️ Tecnologias Utilizadas
* **Backend:** PHP (PDO)
* **Banco de Dados:** MySQL
* **Frontend:** HTML5, CSS3

## 📦 Como Rodar o Projeto Localmente
1. Clone este repositório ou baixe os arquivos na pasta `htdocs` do seu XAMPP.
2. Certifique-se de que o Apache e o MySQL estão ativos no XAMPP.
3. Importe a estrutura do banco de dados (o script está disponível abaixo) no seu MySQL Workbench ou phpMyAdmin.
4. Acesse `http://localhost/LogChamados/index.php` no seu navegador.

## 🗄️ Estrutura do Banco de Dados
```sql

create database sistema_chamados;

Use sistema_chamados;

create table chamados(
	id INT PRIMARY KEY AUTO_INCREMENT,
	 usuario_auxiliado VARCHAR(255) NOT NULL, 
	 setor VARCHAR(255) NOT null, 
	 problema_relatado TEXT NOT NULL,
	 solucao_problema TEXT NOT NULL,
	 realizado TINYINT(1) DEFAULT 0,
	 data_atendimento DATETIME DEFAULT CURRENT_TIMESTAMP
 );
 
 Use sistema_chamados;
 SELECT * FROM chamados;
 
 
 SET sql_safe_updates = 0;
 
 Use sistema_chamados;
 UPDATE chamados SET realizado = 0 WHERE realizado IS NULL;
 
  SET sql_safe_updates = 1;