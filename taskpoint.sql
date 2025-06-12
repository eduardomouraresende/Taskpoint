/* Lógico_Projeto: */
DROP DATABASE IF EXISTS taskpoint;
CREATE DATABASE taskpoint;
use taskpoint;

CREATE TABLE Usuario (
    ID_Usuario INT PRIMARY KEY AUTO_INCREMENT, -- Adicionado AUTO_INCREMENT
    Nome VARCHAR(255),
    Email VARCHAR(255),
    Senha VARCHAR(255)
);

CREATE TABLE Equipe (
    ID_Equipe INT PRIMARY KEY AUTO_INCREMENT, -- Adicionado AUTO_INCREMENT
    Nome VARCHAR(255),
    Dt_Criacao DATE
);

CREATE TABLE Quadro (
    ID_Quadro INT PRIMARY KEY AUTO_INCREMENT, -- Adicionado AUTO_INCREMENT
    Nome VARCHAR(255),
    Descricao VARCHAR(1000),
    Dt_Criacao DATE,
    fk_Usuario_ID_Usuario INT, -- Usuário que criou o quadro
    fk_Equipe_ID_Equipe INT
);

CREATE TABLE Lista (
    ID_Lista INT PRIMARY KEY AUTO_INCREMENT, -- Adicionado AUTO_INCREMENT
    Nome VARCHAR(255),
    Ordem INT,
    fk_Quadro_ID_Quadro INT
);

CREATE TABLE Cartao (
    ID_Cartao INT PRIMARY KEY AUTO_INCREMENT, -- Adicionado AUTO_INCREMENT
    Titulo VARCHAR(255),
    Descricao VARCHAR(1000),
    Dt_Criacao DATE,
    Dt_Vencimento DATE,
    fk_Lista_ID_Lista INT,
    fk_Usuario_ID_Usuario INT -- Usuário que criou o cartão
);

CREATE TABLE Etiqueta (
    ID_Etiqueta INT PRIMARY KEY AUTO_INCREMENT, -- Adicionado AUTO_INCREMENT
    Nome VARCHAR(255),
    Cor VARCHAR(7), -- Ex: #RRGGBB
    fk_Quadro_ID_Quadro INT -- Adicionando FK para vincular etiqueta a um quadro específico
);

CREATE TABLE Checklist (
    ID_Checklist INT PRIMARY KEY AUTO_INCREMENT, -- Adicionado AUTO_INCREMENT
    Nome VARCHAR(255),
    fk_Cartao_ID_Cartao INT
);

CREATE TABLE ItemChecklist (
    ID_ItemChecklist INT PRIMARY KEY AUTO_INCREMENT, -- Adicionado AUTO_INCREMENT
    Nome VARCHAR(255),
    Status VARCHAR(50), -- Ex: 'Pendente', 'Concluído'
    fk_Checklist_ID_Checklist INT
);

CREATE TABLE Anexo (
    ID_Anexo INT PRIMARY KEY AUTO_INCREMENT, -- Adicionado AUTO_INCREMENT
    Nome VARCHAR(255),
    URL VARCHAR(1000),
    fk_Cartao_ID_Cartao INT
);

CREATE TABLE Comentario (
    ID_Comentario INT PRIMARY KEY AUTO_INCREMENT, -- Adicionado AUTO_INCREMENT
    Conteudo VARCHAR(1000),
    Dt_Criacao DATE,
    fk_Usuario_ID_Usuario INT,
    fk_Cartao_ID_Cartao INT
);

CREATE TABLE MembroEquipe (
    fk_Usuario_ID_Usuario INT,
    fk_Equipe_ID_Equipe INT,
    Cargo VARCHAR(255),
    PRIMARY KEY (fk_Usuario_ID_Usuario, fk_Equipe_ID_Equipe)
);

CREATE TABLE Atribuicao (
    fk_Cartao_ID_Cartao INT,
    fk_Usuario_ID_Usuario INT,
    PRIMARY KEY (fk_Cartao_ID_Cartao, fk_Usuario_ID_Usuario)
);

CREATE TABLE CartaoEtiqueta (
    fk_Cartao_ID_Cartao INT,
    fk_Etiqueta_ID_Etiqueta INT,
    PRIMARY KEY (fk_Etiqueta_ID_Etiqueta, fk_Cartao_ID_Cartao)
);
 
ALTER TABLE Quadro ADD CONSTRAINT FK_Quadro_2
    FOREIGN KEY (fk_Usuario_ID_Usuario)
    REFERENCES Usuario (ID_Usuario)
    ON DELETE CASCADE;
 
ALTER TABLE Quadro ADD CONSTRAINT FK_Quadro_3
    FOREIGN KEY (fk_Equipe_ID_Equipe)
    REFERENCES Equipe (ID_Equipe)
    ON DELETE CASCADE;
 
ALTER TABLE Lista ADD CONSTRAINT FK_Lista_2
    FOREIGN KEY (fk_Quadro_ID_Quadro)
    REFERENCES Quadro (ID_Quadro)
    ON DELETE CASCADE;
 
ALTER TABLE Cartao ADD CONSTRAINT FK_Cartao_2
    FOREIGN KEY (fk_Lista_ID_Lista)
    REFERENCES Lista (ID_Lista)
    ON DELETE CASCADE;
 
ALTER TABLE Cartao ADD CONSTRAINT FK_Cartao_3
    FOREIGN KEY (fk_Usuario_ID_Usuario)
    REFERENCES Usuario (ID_Usuario)
    ON DELETE SET NULL; -- Mantendo SET NULL para não deletar cartão se o criador sair

ALTER TABLE Etiqueta ADD CONSTRAINT FK_Etiqueta_2
    FOREIGN KEY (fk_Quadro_ID_Quadro)
    REFERENCES Quadro (ID_Quadro)
    ON DELETE CASCADE; -- Se o quadro for excluído, as etiquetas associadas também são

ALTER TABLE Checklist ADD CONSTRAINT FK_Checklist_2
    FOREIGN KEY (fk_Cartao_ID_Cartao)
    REFERENCES Cartao (ID_Cartao)
    ON DELETE CASCADE;
 
ALTER TABLE ItemChecklist ADD CONSTRAINT FK_ItemChecklist_2
    FOREIGN KEY (fk_Checklist_ID_Checklist)
    REFERENCES Checklist (ID_Checklist)
    ON DELETE CASCADE;
 
ALTER TABLE Anexo ADD CONSTRAINT FK_Anexo_2
    FOREIGN KEY (fk_Cartao_ID_Cartao)
    REFERENCES Cartao (ID_Cartao)
    ON DELETE CASCADE;
 
ALTER TABLE Comentario ADD CONSTRAINT FK_Comentario_2
    FOREIGN KEY (fk_Usuario_ID_Usuario)
    REFERENCES Usuario (ID_Usuario)
    ON DELETE SET NULL; -- Comentário pode permanecer mesmo se o usuário for excluído
 
ALTER TABLE Comentario ADD CONSTRAINT FK_Comentario_3
    FOREIGN KEY (fk_Cartao_ID_Cartao)
    REFERENCES Cartao (ID_Cartao)
    ON DELETE CASCADE;
 
ALTER TABLE MembroEquipe ADD CONSTRAINT FK_MembroEquipe_1
    FOREIGN KEY (fk_Usuario_ID_Usuario)
    REFERENCES Usuario (ID_Usuario)
    ON DELETE CASCADE; -- Se o usuário for excluído, remove o membro da equipe
 
ALTER TABLE MembroEquipe ADD CONSTRAINT FK_MembroEquipe_2
    FOREIGN KEY (fk_Equipe_ID_Equipe)
    REFERENCES Equipe (ID_Equipe)
    ON DELETE CASCADE; -- Se a equipe for excluída, remove os membros da equipe
 
ALTER TABLE Atribuicao ADD CONSTRAINT FK_Atribuicao_1
    FOREIGN KEY (fk_Cartao_ID_Cartao)
    REFERENCES Cartao (ID_Cartao)
    ON DELETE CASCADE; -- Se o cartão for excluído, remove a atribuição
 
ALTER TABLE Atribuicao ADD CONSTRAINT FK_Atribuicao_2
    FOREIGN KEY (fk_Usuario_ID_Usuario)
    REFERENCES Usuario (ID_Usuario)
    ON DELETE CASCADE; -- Se o usuário for excluído, remove a atribuição
 
ALTER TABLE CartaoEtiqueta ADD CONSTRAINT FK_CartaoEtiqueta_1
    FOREIGN KEY (fk_Cartao_ID_Cartao)
    REFERENCES Cartao (ID_Cartao)
    ON DELETE CASCADE; -- Se o cartão for excluído, remove a etiqueta do cartão
 
ALTER TABLE CartaoEtiqueta ADD CONSTRAINT FK_CartaoEtiqueta_2
    FOREIGN KEY (fk_Etiqueta_ID_Etiqueta)
    REFERENCES Etiqueta (ID_Etiqueta)
    ON DELETE CASCADE; -- Se a etiqueta for excluída, remove a associação com o cartão
    
 -- Stored procedure
 
 DELIMITER //
CREATE PROCEDURE MoverCartaoEntreListas(
 IN p_ID_Cartao INT,
 IN p_ID_Lista_Destino INT
)
BEGIN
 DECLARE v_CartaoExiste BOOLEAN;
 DECLARE v_ListaDestinoExiste BOOLEAN;
 -- 1. Verificar se o cartão existe
 SELECT EXISTS (SELECT 1 FROM Cartao WHERE ID_Cartao = p_ID_Cartao) INTO v_CartaoExiste;
 IF NOT v_CartaoExiste THEN
 SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Erro: Cartão não encontrado.';
 END IF;
 -- 2. Verificar se a lista de destino existe
 SELECT EXISTS (SELECT 1 FROM Lista WHERE ID_Lista = p_ID_Lista_Destino) INTO
v_ListaDestinoExiste;
 IF NOT v_ListaDestinoExiste THEN
 SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Erro: Lista de destino não encontrada.';
 END IF;
 -- 3. Mover o cartão para a nova lista
 UPDATE Cartao
 SET fk_Lista_ID_Lista = p_ID_Lista_Destino
 WHERE ID_Cartao = p_ID_Cartao;
END //
DELIMITER ;
-- Como chamar a Stored Procedure (exemplo)
-- Para mover o Cartão com ID 1 (Implementar login) da Lista 1002 (A Fazer) para a Lista 1003(Em Progresso)
-- CALL MoverCartaoEntreListas(1, 1003);
-- Para testar:
-- SELECT ID_Cartao, Titulo, fk_Lista_ID_Lista FROM Cartao WHERE ID_Cartao = 1;







-- AUDITORIA 
CREATE TABLE IF NOT EXISTS Auditoria_Cartao (
 ID_Auditoria INT AUTO_INCREMENT PRIMARY KEY, -- AUTO_INCREMENT para MySQL
 ID_Cartao_Afetado INT,
 Coluna_Alterada VARCHAR(255),
 Valor_Antigo VARCHAR(1000),
 Valor_Novo VARCHAR(1000),
 Dt_Alteracao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 ID_Usuario_Alterador INT NULL,
 Acao VARCHAR(50) -- Ex: 'UPDATE'
);


-- trigger
DELIMITER //
CREATE TRIGGER tr_auditar_cartao_update
AFTER UPDATE ON Cartao
FOR EACH ROW
BEGIN
 -- verifica se o titulo foi alterado
 IF NOT (OLD.Titulo <=> NEW.Titulo) THEN 
 INSERT INTO Auditoria_Cartao (ID_Cartao_Afetado, Coluna_Alterada, Valor_Antigo, Valor_Novo,
ID_Usuario_Alterador, Acao)
 VALUES (OLD.ID_Cartao, 'Titulo', COALESCE(OLD.Titulo, 'NULL'), COALESCE(NEW.Titulo, 'NULL'),
NULL, 'UPDATE');
 END IF;
 
 IF NOT (OLD.Descricao <=> NEW.Descricao) THEN
 INSERT INTO Auditoria_Cartao (ID_Cartao_Afetado, Coluna_Alterada, Valor_Antigo, Valor_Novo,
ID_Usuario_Alterador, Acao)
 VALUES (OLD.ID_Cartao, 'Descricao', COALESCE(OLD.Descricao, 'NULL'), COALESCE(NEW.Descricao,
'NULL'), NULL, 'UPDATE');
 END IF;
 -- Verifica se a Data de Vencimento foi alterada
 IF NOT (OLD.Dt_Vencimento <=> NEW.Dt_Vencimento) THEN
 INSERT INTO Auditoria_Cartao (ID_Cartao_Afetado, Coluna_Alterada, Valor_Antigo, Valor_Novo,
ID_Usuario_Alterador, Acao)
 VALUES (OLD.ID_Cartao, 'Dt_Vencimento', COALESCE(OLD.Dt_Vencimento, 'NULL'),
COALESCE(NEW.Dt_Vencimento, 'NULL'), NULL, 'UPDATE');
 END IF;
 -- Verifica se a Lista (fk_Lista_ID_Lista) foi alterada
 IF NOT (OLD.fk_Lista_ID_Lista <=> NEW.fk_Lista_ID_Lista) THEN
 INSERT INTO Auditoria_Cartao (ID_Cartao_Afetado, Coluna_Alterada, Valor_Antigo, Valor_Novo,
ID_Usuario_Alterador, Acao)
 VALUES (OLD.ID_Cartao, 'fk_Lista_ID_Lista', COALESCE(OLD.fk_Lista_ID_Lista, 'NULL'),
COALESCE(NEW.fk_Lista_ID_Lista, 'NULL'), NULL, 'UPDATE');
 END IF;
 -- Verifica se o Usuário Criador (fk_Usuario_ID_Usuario) foi alterado
 IF NOT (OLD.fk_Usuario_ID_Usuario <=> NEW.fk_Usuario_ID_Usuario) THEN
 INSERT INTO Auditoria_Cartao (ID_Cartao_Afetado, Coluna_Alterada, Valor_Antigo, Valor_Novo,
ID_Usuario_Alterador, Acao)
 VALUES (OLD.ID_Cartao, 'fk_Usuario_ID_Usuario', COALESCE(OLD.fk_Usuario_ID_Usuario, 'NULL'),
COALESCE(NEW.fk_Usuario_ID_Usuario, 'NULL'), NULL, 'UPDATE');
 END IF;
END //
DELIMITER ;
-- CALL MoverCartaoEntreListas(1, 1004); -- SP que dispara o trigger
-- UPDATE Cartao SET Titulo = 'Titulo do Cartao 1 (Atualizado)', Descricao = 'Descricao modificada parateste de auditoria.' WHERE ID_Cartao = 1;
-- SELECT * FROM Auditoria_Cartao ORDER BY Dt_Alteracao DESC;

    
    
    
    
    
    
    
    
    

    

-- Inserções para a tabela Usuario (15 registros)
INSERT INTO Usuario (ID_Usuario, Nome, Email, Senha) VALUES
(1, 'Alice Smith', 'alice.s@email.com', 'senha123'),
(2, 'Bob Johnson', 'bob.j@email.com', 'senha456'),
(3, 'Charlie Brown', 'charlie.b@email.com', 'senha789'),
(4, 'Diana Prince', 'diana.p@email.com', 'senhaabc'),
(5, 'Eve Davis', 'eve.d@email.com', 'senhaefg'),
(6, 'Frank White', 'frank.w@email.com', 'senhahiJ'),
(7, 'Grace Black', 'grace.b@email.com', 'senhaklm'),
(8, 'Henry Green', 'henry.g@email.com', 'senhanop'),
(9, 'Ivy Blue', 'ivy.bl@email.com', 'senhaqrs'),
(10, 'Jack Orange', 'jack.o@email.com', 'senhatuv'),
(11, 'Karen Violet', 'karen.v@email.com', 'senhawxy'),
(12, 'Liam Gold', 'liam.g@email.com', 'senha1234'),
(13, 'Mia Silver', 'mia.s@email.com', 'senha5678'),
(14, 'Noah Bronze', 'noah.b@email.com', 'senha9012'),
(15, 'Olivia Platinum', 'olivia.p@email.com', 'senha3456');

-- Inserções para a tabela Equipe (15 registros)
INSERT INTO Equipe (ID_Equipe, Nome, Dt_Criacao) VALUES
(101, 'Equipe Alpha', '2023-01-15'),
(102, 'Equipe Beta', '2023-02-20'),
(103, 'Equipe Gamma', '2023-03-01'),
(104, 'Equipe Delta', '2023-04-10'),
(105, 'Equipe Epsilon', '2023-05-05'),
(106, 'Equipe Zeta', '2023-06-12'),
(107, 'Equipe Eta', '2023-07-01'),
(108, 'Equipe Theta', '2023-08-18'),
(109, 'Equipe Iota', '2023-09-25'),
(110, 'Equipe Kappa', '2023-10-30'),
(111, 'Equipe Lambda', '2024-01-01'),
(112, 'Equipe Mu', '2024-02-14'),
(113, 'Equipe Nu', '2024-03-22'),
(114, 'Equipe Xi', '2024-04-05'),
(115, 'Equipe Omicron', '2024-05-19');


-- Inserções para a tabela MembroEquipe (mais de 1 usuário por equipe)
-- Equipe Alpha (ID: 101)
INSERT INTO MembroEquipe (fk_Usuario_ID_Usuario, fk_Equipe_ID_Equipe, Cargo) VALUES
(1, 101, 'Gerente de Projeto'),
(2, 101, 'Desenvolvedor Senior'),
(3, 101, 'Analista de QA'),
(4, 101, 'Designer UI/UX');

-- Equipe Beta (ID: 102)
INSERT INTO MembroEquipe (fk_Usuario_ID_Usuario, fk_Equipe_ID_Equipe, Cargo) VALUES
(5, 102, 'Líder Técnico'),
(6, 102, 'Desenvolvedor'),
(7, 102, 'Analista de Negócios');

-- Equipe Gamma (ID: 103)
INSERT INTO MembroEquipe (fk_Usuario_ID_Usuario, fk_Equipe_ID_Equipe, Cargo) VALUES
(8, 103, 'Product Owner'),
(9, 103, 'Scrum Master');

-- Equipe Delta (ID: 104)
INSERT INTO MembroEquipe (fk_Usuario_ID_Usuario, fk_Equipe_ID_Equipe, Cargo) VALUES
(10, 104, 'Consultor'),
(11, 104, 'Analista de Dados');

-- Equipe Epsilon (ID: 105)
INSERT INTO MembroEquipe (fk_Usuario_ID_Usuario, fk_Equipe_ID_Equipe, Cargo) VALUES
(12, 105, 'Arquiteto de Software'),
(13, 105, 'Engenheiro de DevOps');

-- Equipe Zeta (ID: 106)
INSERT INTO MembroEquipe (fk_Usuario_ID_Usuario, fk_Equipe_ID_Equipe, Cargo) VALUES
(14, 106, 'Especialista em Suporte'),
(15, 106, 'Coordenador de Atendimento');

-- Mais alguns membros para outras equipes para ter mais de 15 registros no total
INSERT INTO MembroEquipe (fk_Usuario_ID_Usuario, fk_Equipe_ID_Equipe, Cargo) VALUES
(1, 107, 'Consultor Externo'),
(2, 108, 'Especialista em Segurança'),
(3, 109, 'Desenvolvedor Fullstack'),
(4, 110, 'Tester de Automação'),
(5, 111, 'Gerente de Contas'),
(6, 112, 'Especialista em BI'),
(7, 113, 'Cientista de Dados'),
(8, 114, 'Estrategista de Produto'),
(9, 115, 'Engenheiro de Machine Learning'); -- Total: 27 registros


-- Inserções para a tabela Quadro (mais de um quadro para cada equipe)
INSERT INTO Quadro (ID_Quadro, Nome, Descricao, Dt_Criacao, fk_Usuario_ID_Usuario, fk_Equipe_ID_Equipe) VALUES
(1, 'Projeto Web XYZ', 'Quadro para o desenvolvimento do site XYZ', '2024-01-20', 1, 101),
(2, 'Marketing Digital Q1', 'Planejamento e execução de campanhas de marketing para o Q1', '2024-02-01', 1, 101), -- Equipe Alpha, Quadro 2
(3, 'Novos Recursos App', 'Ideias e implementação de novos recursos para o aplicativo', '2024-03-10', 5, 102),
(4, 'Manutenção Infra', 'Tarefas de manutenção e upgrades de infraestrutura', '2024-04-05', 6, 102), -- Equipe Beta, Quadro 2
(5, 'Recrutamento RH', 'Processo de recrutamento e seleção para novas vagas', '2024-05-15', 8, 103),
(6, 'Treinamento Equipe', 'Materiais e cronograma para o treinamento da equipe', '2024-06-01', 9, 103), -- Equipe Gamma, Quadro 2
(7, 'Pesquisa de Mercado', 'Estudo de viabilidade para novos produtos', '2024-07-10', 10, 104),
(8, 'Controle Financeiro', 'Acompanhamento de despesas e receitas', '2024-08-01', 11, 104), -- Equipe Delta, Quadro 2
(9, 'Suporte ao Cliente', 'Gestão de tickets e melhorias no atendimento', '2024-09-01', 12, 105), -- Correção: Usuário 12 pertence à equipe 105
(10, 'Onboarding de Clientes', 'Fluxo para integração de novos clientes', '2024-10-01', 13, 105), -- Correção: Usuário 13 pertence à equipe 105
(11, 'DevOps Pipeline', 'Melhorias no CI/CD e automação', '2024-11-01', 14, 106), -- Correção: Usuário 14 pertence à equipe 106
(12, 'Gestão de Conteúdo', 'Criação e curadoria de conteúdo para o blog', '2024-12-01', 15, 106), -- Correção: Usuário 15 pertence à equipe 106
(13, 'Eventos Internos', 'Organização de eventos para a equipe', '2025-01-01', 1, 107),
(14, 'Design System', 'Desenvolvimento e manutenção do sistema de design', '2025-02-01', 2, 108),
(15, 'Auditoria Interna', 'Preparação para a auditoria anual', '2025-03-01', 3, 109),
(16, 'Planejamento Estratégico', 'Planejamento de longo prazo', '2024-01-10', 1, 101), -- Mais um quadro para Equipe Alpha
(17, 'OKR Q2', 'Definição e acompanhamento de OKRs do segundo trimestre', '2024-04-01', 5, 102), -- Mais um quadro para Equipe Beta
(18, 'Gestão de Projetos Internos', 'Quadro para projetos de melhoria interna da RH', '2024-06-05', 8, 103); -- Mais um quadro para Equipe Gamma
-- Total: 18 registros


-- Inserções para a tabela Lista (15+ registros)
INSERT INTO Lista (ID_Lista, Nome, Ordem, fk_Quadro_ID_Quadro) VALUES
(1001, 'Backlog', 1, 1),
(1002, 'A Fazer', 2, 1),
(1003, 'Em Progresso', 3, 1),
(1004, 'Revisão', 4, 1),
(1005, 'Concluído', 5, 1); -- 5 listas para Quadro 1

INSERT INTO Lista (ID_Lista, Nome, Ordem, fk_Quadro_ID_Quadro) VALUES
(1006, 'Ideias', 1, 2),
(1007, 'Planejamento', 2, 2),
(1008, 'Execução', 3, 2),
(1009, 'Monitoramento', 4, 2); -- 4 listas para Quadro 2

INSERT INTO Lista (ID_Lista, Nome, Ordem, fk_Quadro_ID_Quadro) VALUES
(1010, 'Pendências', 1, 3),
(1011, 'Desenvolvimento', 2, 3),
(1012, 'Testes', 3, 3); -- 3 listas para Quadro 3

INSERT INTO Lista (ID_Lista, Nome, Ordem, fk_Quadro_ID_Quadro) VALUES
(1013, 'Prioridade Alta', 1, 4),
(1014, 'Prioridade Média', 2, 4),
(1015, 'Prioridade Baixa', 3, 4); -- 3 listas para Quadro 4

INSERT INTO Lista (ID_Lista, Nome, Ordem, fk_Quadro_ID_Quadro) VALUES
(1016, 'Aguardando', 1, 5),
(1017, 'Em Análise', 2, 5); -- 2 listas para Quadro 5

INSERT INTO Lista (ID_Lista, Nome, Ordem, fk_Quadro_ID_Quadro) VALUES
(1018, 'Etapa 1', 1, 6),
(1019, 'Etapa 2', 2, 6); -- 2 listas para Quadro 6

INSERT INTO Lista (ID_Lista, Nome, Ordem, fk_Quadro_ID_Quadro) VALUES
(1020, 'Coleta de Dados', 1, 7); -- 1 lista para Quadro 7

INSERT INTO Lista (ID_Lista, Nome, Ordem, fk_Quadro_ID_Quadro) VALUES
(1021, 'Entrada', 1, 8),
(1022, 'Saída', 2, 8); -- 2 listas para Quadro 8

INSERT INTO Lista (ID_Lista, Nome, Ordem, fk_Quadro_ID_Quadro) VALUES
(1023, 'Fase 1', 1, 16),
(1024, 'Fase 2', 2, 16); -- Listas para Quadro 16

INSERT INTO Lista (ID_Lista, Nome, Ordem, fk_Quadro_ID_Quadro) VALUES
(1025, 'Objetivos', 1, 17),
(1026, 'Resultados Chave', 2, 17); -- Listas para Quadro 17

INSERT INTO Lista (ID_Lista, Nome, Ordem, fk_Quadro_ID_Quadro) VALUES
(1027, 'Planejamento Inicial', 1, 18),
(1028, 'Execução Projetos', 2, 18); -- Listas para Quadro 18
-- Total: 32 registros


-- Inserções para a tabela Cartao (15+ registros)
INSERT INTO Cartao (ID_Cartao, Titulo, Descricao, Dt_Criacao, Dt_Vencimento, fk_Lista_ID_Lista, fk_Usuario_ID_Usuario) VALUES
(1, 'Implementar login', 'Desenvolver sistema de autenticação de usuários', '2024-01-22', '2024-02-05', 1002, 2),
(2, 'Criar página inicial', 'Design e desenvolvimento da homepage do site', '2024-01-25', '2024-02-10', 1003, 4),
(3, 'Configurar banco de dados', 'Setup inicial do PostgreSQL', '2024-01-20', '2024-01-28', 1001, 2),
(4, 'Campanha de Páscoa', 'Planejamento e execução de emails e ads', '2024-02-05', '2024-03-20', 1007, 1),
(5, 'Análise de concorrência', 'Estudo de mercado para novos recursos', '2024-03-12', '2024-03-25', 1010, 8),
(6, 'Migrar servidor', 'Migração de dados para novo servidor AWS', '2024-04-01', '2024-04-15', 1013, 6),
(7, 'Entrevistar candidatos', 'Agendar e conduzir entrevistas para vaga de Desenvolvedor', '2024-05-16', '2024-05-30', 1016, 8),
(8, 'Preparar material de treinamento', 'Slides e exercícios para o novo módulo', '2024-06-02', '2024-06-15', 1018, 9),
(9, 'Análise SWOT', 'Análise de forças, fraquezas, oportunidades e ameaças', '2024-07-12', '2024-07-25', 1020, 10),
(10, 'Relatório mensal financeiro', 'Compilação de dados e apresentação', '2024-08-05', '2024-08-10', 1021, 11),
(11, 'Responder tickets urgentes', 'Priorizar e resolver problemas de clientes', '2024-09-02', '2024-09-05', 1009, 14), -- Corrigido para ID_Lista 1009
(12, 'Criar fluxo de onboarding', 'Documentar e implementar o processo para novos clientes', '2024-10-02', '2024-10-15', 1010, 15), -- Corrigido para ID_Lista 1010
(13, 'Implementar Git Hooks', 'Automação de checks de código no commit', '2024-11-05', '2024-11-12', 1011, 12), -- Corrigido para ID_Lista 1011
(14, 'Escrever post sobre IA', 'Artigo para o blog sobre inteligência artificial', '2024-12-05', '2024-12-15', 1012, 13), -- Corrigido para ID_Lista 1012
(15, 'Agendar reunião com stakeholders', 'Definir pauta e convidar participantes', '2025-01-05', '2025-01-10', 1013, 1), -- Corrigido para ID_Lista 1013
(16, 'Definir componentes base', 'Primeira etapa do design system', '2025-02-02', '2025-02-10', 1014, 2), -- Corrigido para ID_Lista 1014
(17, 'Revisar documentos de conformidade', 'Verificação de adherence às normas', '2025-03-02', '2025-03-10', 1015, 3), -- Corrigido para ID_Lista 1015
(18, 'Auditoria Interna - Fase 2', 'Revisão final dos relatórios', '2025-03-12', '2025-03-20', 1015, 3),
(19, 'Acompanhamento KPI marketing', 'Monitorar desempenho de campanhas', '2024-02-10', '2024-02-25', 1008, 1),
(20, 'Desenvolver API de produtos', 'Criar endpoints para gerenciar produtos', '2024-03-15', '2024-04-01', 1011, 5),
(21, 'Revisar OKRs do Q2', 'Verificar progresso dos OKRs', '2024-04-05', '2024-04-15', 1025, 5), -- Novo cartão para o Quadro 17
(22, 'Planejar próximo sprint', 'Definir escopo e backlog para a próxima sprint', '2024-01-25', '2024-02-01', 1001, 2), -- Novo cartão para o Quadro 1 (Backlog)
(23, 'Criação de artigos para blog', 'Desenvolver artigos sobre IA', '2024-12-01', '2024-12-20', 1012, 13); -- Novo cartão para o Quadro 3 (Testes)
-- Total: 23 registros


-- Inserções para a tabela Etiqueta (15 registros)
INSERT INTO Etiqueta (ID_Etiqueta, Nome, Cor, fk_Quadro_ID_Quadro) VALUES
(1, 'Urgente', '#FF0000', 1),
(2, 'Backend', '#0000FF', 1),
(3, 'Frontend', '#00FF00', 1),
(4, 'Marketing', '#FFFF00', 2),
(5, 'Bug', '#FF4500', 3),
(6, 'Melhoria', '#ADFF2F', 3),
(7, 'Infraestrutura', '#808080', 4),
(8, 'RH', '#FF69B4', 5),
(9, 'Treinamento', '#4682B4', 6),
(10, 'Pesquisa', '#8A2BE2', 7),
(11, 'Financeiro', '#20B2AA', 8),
(12, 'Suporte', '#FFD700', 9),
(13, 'Onboarding', '#F08080', 10),
(14, 'DevOps', '#DDA0DD', 11),
(15, 'Conteúdo', '#9ACD32', 12),
(16, 'Design', '#4B0082', 14),
(17, 'Estratégico', '#8B0000', 16),
(18, 'OKR', '#008080', 17),
(19, 'Projetos Internos', '#FFA500', 18);
-- Total: 19 registros


-- Inserções para a tabela CartaoEtiqueta (15+ registros)
INSERT INTO CartaoEtiqueta (fk_Cartao_ID_Cartao, fk_Etiqueta_ID_Etiqueta) VALUES
(1, 2), -- Implementar login - Backend
(1, 1), -- Implementar login - Urgente
(2, 3), -- Criar página inicial - Frontend
(2, 6), -- Criar página inicial - Melhoria
(3, 2), -- Configurar banco de dados - Backend
(4, 4), -- Campanha de Páscoa - Marketing
(5, 6), -- Análise de concorrência - Melhoria
(6, 7), -- Migrar servidor - Infraestrutura
(7, 8), -- Entrevistar candidatos - RH
(8, 9), -- Preparar material de treinamento - Treinamento
(9, 10), -- Análise SWOT - Pesquisa
(10, 11), -- Relatório mensal financeiro - Financeiro
(11, 12), -- Responder tickets urgentes - Suporte
(12, 13), -- Criar fluxo de onboarding - Onboarding
(13, 14), -- Implementar Git Hooks - DevOps
(14, 15), -- Escrever post sobre IA - Conteúdo
(16, 16), -- Definir componentes base - Design
(17, 1), -- Revisar documentos de conformidade - Urgente
(18, 17), -- Auditoria Interna - Estratégico
(19, 4), -- Acompanhamento KPI marketing - Marketing
(20, 2), -- Desenvolver API de produtos - Backend
(21, 18), -- Revisar OKRs do Q2 - OKR
(22, 1), -- Planejar próximo sprint - Urgente
(23, 15); -- Criação de artigos para blog - Conteúdo
-- Total: 24 registros


-- Inserções para a tabela Checklist (15 registros)
INSERT INTO Checklist (ID_Checklist, Nome, fk_Cartao_ID_Cartao) VALUES
(1, 'Login - Tarefas', 1),
(2, 'Homepage - Elementos', 2),
(3, 'BD - Configuração', 3),
(4, 'Campanha - Etapas', 4),
(5, 'Análise - Pontos', 5),
(6, 'Migração - Passos', 6),
(7, 'Entrevista - Roteiro', 7),
(8, 'Treinamento - Módulos', 8),
(9, 'SWOT - Itens', 9),
(10, 'Financeiro - Relatório', 10),
(11, 'Suporte - Ações', 11),
(12, 'Onboarding - Checklist', 12),
(13, 'DevOps - Melhorias', 13),
(14, 'Blog Post - Estrutura', 14),
(15, 'Reunião - Pauta', 15),
(16, 'Design System - Components', 16),
(17, 'Auditoria - Tarefas', 17),
(18, 'Auditoria - Fase 2', 18),
(19, 'KPI - Acompanhamento', 19),
(20, 'API - Desenvolvimento', 20),
(21, 'OKR - Detalhes', 21),
(22, 'Sprint - Planejamento', 22),
(23, 'Blog - Artigos', 23);
-- Total: 23 registros


-- Inserções para a tabela ItemChecklist (mais de 1 item em cada checklist)
INSERT INTO ItemChecklist (ID_ItemChecklist, Nome, Status, fk_Checklist_ID_Checklist) VALUES
(1, 'Criar tabela de usuários', 'Concluído', 1),
(2, 'Desenvolver endpoint de login', 'Concluído', 1),
(3, 'Implementar autenticação JWT', 'Pendente', 1), -- 3 itens para Checklist 1

(4, 'Design da landing page', 'Concluído', 2),
(5, 'Desenvolver HTML/CSS', 'Em Progresso', 2),
(6, 'Integrar com API', 'Pendente', 2), -- 3 itens para Checklist 2

(7, 'Configurar servidor DB', 'Concluído', 3),
(8, 'Criar schema e tabelas', 'Concluído', 3), -- 2 itens para Checklist 3

(9, 'Definir público-alvo', 'Concluído', 4),
(10, 'Criar copy para emails', 'Em Progresso', 4),
(11, 'Configurar automação de envio', 'Pendente', 4), -- 3 itens para Checklist 4

(12, 'Pesquisar concorrentes diretos', 'Concluído', 5),
(13, 'Analisar pontos fortes/fracos', 'Pendente', 5), -- 2 itens para Checklist 5

(14, 'Backup dos dados', 'Concluído', 6),
(15, 'Configurar novo servidor', 'Em Progresso', 6),
(16, 'Testar conectividade', 'Pendente', 6), -- 3 itens para Checklist 6

(17, 'Definir critérios de avaliação', 'Concluído', 7),
(18, 'Agendar com RH', 'Pendente', 7), -- 2 itens para Checklist 7

(19, 'Preparar slides', 'Concluído', 8),
(20, 'Gravar vídeos', 'Pendente', 8),
(21, 'Disponibilizar material online', 'Pendente', 8), -- 3 itens para Checklist 8

(22, 'Analisar Forças', 'Concluído', 9),
(23, 'Analisar Fraquezas', 'Em Progresso', 9),
(24, 'Analisar Oportunidades', 'Pendente', 9),
(25, 'Analisar Ameaças', 'Pendente', 9), -- 4 itens para Checklist 9

(26, 'Coletar dados de despesas', 'Concluído', 10),
(27, 'Compilar receitas', 'Em Progresso', 10), -- 2 itens para Checklist 10

(28, 'Priorizar tickets', 'Concluído', 11),
(29, 'Resolver tickets urgentes', 'Em Progresso', 11), -- 2 itens para Checklist 11

(30, 'Documentar fluxo', 'Concluído', 12),
(31, 'Testar com novos usuários', 'Pendente', 12), -- 2 itens para Checklist 12

(32, 'Implementar CI/CD', 'Em Progresso', 13),
(33, 'Automatizar testes', 'Pendente', 13), -- 2 itens para Checklist 13

(34, 'Escrever rascunho', 'Concluído', 14),
(35, 'Revisar conteúdo', 'Pendente', 14), -- 2 itens para Checklist 14

(36, 'Definir participantes', 'Concluído', 15),
(37, 'Enviar convites', 'Pendente', 15), -- 2 itens para Checklist 15

(38, 'Mapear componentes existentes', 'Concluído', 16),
(39, 'Criar novos componentes', 'Pendente', 16), -- 2 itens para Checklist 16

(40, 'Reunir documentos contábeis', 'Concluído', 17),
(41, 'Verificar conformidade legal', 'Em Progresso', 17), -- 2 itens para Checklist 17

(42, 'Revisar relatórios financeiros', 'Pendente', 18),
(43, 'Preparar apresentação', 'Pendente', 18), -- 2 itens para Checklist 18

(44, 'Coletar dados de vendas', 'Concluído', 19),
(45, 'Analisar tendências', 'Em Progresso', 19), -- 2 itens para Checklist 19

(46, 'Definir requisitos da API', 'Concluído', 20),
(47, 'Codificar endpoints', 'Em Progresso', 20), -- 2 itens para Checklist 20

(48, 'Definir KR1', 'Pendente', 21),
(49, 'Definir KR2', 'Pendente', 21), -- 2 itens para Checklist 21

(50, 'Revisar Backlog', 'Concluído', 22),
(51, 'Estimar tarefas', 'Em Progresso', 22), -- 2 itens para Checklist 22

(52, 'Pesquisar palavras-chave', 'Concluído', 23),
(53, 'Escrever primeira versão', 'Pendente', 23); -- 2 itens para Checklist 23
-- Total: 53 registros


-- Inserções para a tabela Anexo (15 registros)
INSERT INTO Anexo (ID_Anexo, Nome, URL, fk_Cartao_ID_Cartao) VALUES
(1, 'Wireframe_Login.png', 'http://link.com/wireframe_login.png', 1),
(2, 'Mockup_Homepage.fig', 'http://link.com/mockup_homepage.fig', 2),
(3, 'DB_Schema.pdf', 'http://link.com/db_schema.pdf', 3),
(4, 'Email_Marketing_Layout.html', 'http://link.com/email_marketing.html', 4),
(5, 'Relatorio_Mercado.xlsx', 'http://link.com/relatorio_mercado.xlsx', 5),
(6, 'Checklist_Migracao.pdf', 'http://link.com/checklist_migracao.pdf', 6),
(7, 'Entrevistas_Roteiro.docx', 'http://link.com/entrevistas_roteiro.docx', 7),
(8, 'Material_Treinamento.zip', 'http://link.com/material_treinamento.zip', 8),
(9, 'Analise_SWOT.pptx', 'http://link.com/analise_swot.pptx', 9),
(10, 'Relatorio_Financeiro_2024_08.pdf', 'http://link.com/relatorio_financeiro.pdf', 10),
(11, 'FAQ_Suporte.docx', 'http://link.com/faq_suporte.docx', 11),
(12, 'Fluxo_Onboarding.drawio', 'http://link.com/fluxo_onboarding.drawio', 12),
(13, 'Diagrama_CI_CD.png', 'http://link.com/diagrama_ci_cd.png', 13),
(14, 'Esboco_Blog_Post.txt', 'http://link.com/esboco_blog_post.txt', 14),
(15, 'Ata_Reuniao_Exemplo.pdf', 'http://link.com/ata_reuniao.pdf', 15),
(16, 'Design_System_Guidelines.pdf', 'http://link.com/design_system_guidelines.pdf', 16),
(17, 'Documentos_Auditoria.zip', 'http://link.com/documentos_auditoria.zip', 17),
(18, 'Plano_Campanha_Q2.pdf', 'http://link.com/plano_campanha_q2.pdf', 19),
(19, 'Relatorio_OKR_Q2.pdf', 'http://link.com/relatorio_okr_q2.pdf', 21),
(20, 'Diagrama_Sprint_Backlog.png', 'http://link.com/diagrama_sprint_backlog.png', 22);
-- Total: 20 registros


-- Inserções para a tabela Comentario (15+ registros)
INSERT INTO Comentario (ID_Comentario, Conteudo, Dt_Criacao, fk_Usuario_ID_Usuario, fk_Cartao_ID_Cartao) VALUES
(1, 'Precisamos definir os requisitos de segurança para o login.', '2024-01-23', 1, 1),
(2, 'O mockup da homepage está aprovado. Podemos iniciar o desenvolvimento.', '2024-01-26', 4, 2),
(3, 'Consegui configurar o banco de dados localmente.', '2024-01-21', 2, 3),
(4, 'A campanha de Páscoa está no prazo. Aguardando a aprovação final do texto.', '2024-03-15', 1, 4),
(5, 'Ainda preciso de mais dados sobre o concorrente X.', '2024-03-18', 8, 5),
(6, 'A migração do servidor está progredindo bem. Quase finalizando a fase de testes.', '2024-04-10', 6, 6),
(7, 'Recebi 5 currículos promissores para a vaga.', '2024-05-20', 8, 7),
(8, 'Os slides para o treinamento estão prontos para revisão.', '2024-06-10', 9, 8),
(9, 'A análise SWOT inicial aponta oportunidades no mercado asiático.', '2024-07-20', 10, 9),
(10, 'O relatório financeiro de agosto será enviado até o final do dia.', '2024-08-08', 11, 10),
(11, 'Cliente Y está satisfeito com a resolução do problema.', '2024-09-03', 14, 11),
(12, 'O novo fluxo de onboarding deve reduzir a taxa de abandono em 10%.', '2024-10-10', 15, 12),
(13, 'Os Git Hooks estão funcionando, melhorando a qualidade do código.', '2024-11-10', 12, 13),
(14, 'O rascunho do post sobre IA está pronto. Precisa de revisão.', '2024-12-10', 13, 14),
(15, 'A pauta da reunião foi enviada a todos os stakeholders.', '2025-01-08', 1, 15),
(16, 'Definimos os princípios do Design System na última reunião.', '2025-02-05', 2, 16),
(17, 'Documentos da auditoria estão sendo compilados.', '2025-03-05', 3, 17),
(18, 'Fase 2 da auditoria iniciada com sucesso.', '2025-03-13', 3, 18),
(19, 'KPIs de marketing apresentaram melhora no último mês.', '2024-02-20', 1, 19),
(20, 'API de produtos está quase pronta para homologação.', '2024-03-28', 5, 20),
(21, 'Precisamos agendar uma reunião para revisar os KRs.', '2024-04-08', 5, 21),
(22, 'Backlog da sprint atualizado. Próxima reunião na quarta.', '2024-01-26', 2, 22),
(23, 'Sugiro adicionar mais exemplos práticos ao artigo.', '2024-12-15', 13, 23);
-- Total: 23 registros


-- Inserções para a tabela Atribuicao (15+ registros para demonstrar relacionamentos)
INSERT INTO Atribuicao (fk_Cartao_ID_Cartao, fk_Usuario_ID_Usuario) VALUES
(1, 2), -- Implementar login atribuído ao Bob
(1, 3), -- Implementar login atribuído ao Charlie (mais de 1 por cartão)
(2, 4), -- Criar página inicial atribuído à Diana
(2, 1), -- Criar página inicial atribuído à Alice (mais de 1 por cartão)
(3, 2), -- Configurar banco de dados atribuído ao Bob
(4, 1), -- Campanha de Páscoa atribuído à Alice
(4, 4), -- Campanha de Páscoa atribuído à Diana
(5, 8), -- Análise de concorrência atribuído ao Henry
(6, 6), -- Migrar servidor atribuído ao Frank
(6, 7), -- Migrar servidor atribuído à Grace
(7, 8), -- Entrevistar candidatos atribuído ao Henry
(7, 9), -- Entrevistar candidatos atribuído à Ivy
(8, 9), -- Preparar material de treinamento atribuído à Ivy
(8, 10), -- Preparar material de treinamento atribuído ao Jack
(9, 10), -- Análise SWOT atribuído ao Jack
(10, 11), -- Relatório mensal financeiro atribuído à Karen
(11, 14), -- Responder tickets urgentes atribuído ao Noah
(12, 15), -- Criar fluxo de onboarding atribuído à Olivia
(13, 12), -- Implementar Git Hooks atribuído ao Liam
(14, 13), -- Escrever post sobre IA atribuído à Mia
(15, 1), -- Agendar reunião com stakeholders atribuído à Alice
(16, 2), -- Definir componentes base atribuído ao Bob
(17, 3), -- Revisar documentos de conformidade atribuído ao Charlie
(18, 3), -- Auditoria Interna - Fase 2 atribuído ao Charlie
(19, 1), -- Acompanhamento KPI marketing atribuído à Alice
(20, 5), -- Desenvolver API de produtos atribuído à Eve
(21, 5), -- Revisar OKRs do Q2 atribuído à Eve
(22, 2), -- Planejar próximo sprint atribuído ao Bob
(23, 13); -- Criação de artigos para blog atribuído à Mia
