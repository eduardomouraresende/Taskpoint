<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TaskPoint</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <a class="navbar-brand" href="#">TaskPoint</a>
    </nav>

    <div class="container-fluid mt-3">
        <!-- Seção de Lista de Quadros -->
        <div id="boards-section" class="row">
            <div class="col-12">
                <h2 class="mb-3">Meus Quadros</h2>
                <button class="btn btn-primary mb-3" id="create-board-btn">
                    <i class="fas fa-plus"></i> Criar Novo Quadro
                </button>
                <div id="boards-list" class="row">
                    <!-- Quadros serão carregados aqui via jQuery -->
                </div>
            </div>
        </div>

        <!-- Seção de Detalhes do Quadro (inicialmente oculta) -->
        <div id="board-detail-section" class="row mt-4" style="display: none;">
            <div class="col-12">
                <button class="btn btn-secondary mb-3" id="back-to-boards-btn">
                    <i class="fas fa-arrow-left"></i> Voltar para Quadros
                </button>
                <h2 id="board-name" class="mb-2"></h2>
                <p id="board-description" class="text-muted"></p>
                <button class="btn btn-success mb-3" id="create-list-btn">
                    <i class="fas fa-plus"></i> Adicionar Nova Lista
                </button>
                <div id="lists-container" class="d-flex overflow-auto pb-3">
                    <!-- Listas e Cartões serão carregados aqui via jQuery -->
                </div>
            </div>
        </div>
    </div>

    <!-- Modais -->

    <!-- Modal para Criar/Editar Quadro -->
    <div class="modal fade" id="boardModal" tabindex="-1" role="dialog" aria-labelledby="boardModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="boardModalLabel">Criar/Editar Quadro</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="boardForm">
                        <input type="hidden" id="board-id">
                        <div class="form-group">
                            <label for="board-name-input">Nome do Quadro</label>
                            <input type="text" class="form-control" id="board-name-input" required>
                        </div>
                        <div class="form-group">
                            <label for="board-description-input">Descrição</label>
                            <textarea class="form-control" id="board-description-input" rows="3"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="board-user-input">Criado por (Usuário)</label>
                            <select class="form-control" id="board-user-input" required>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="board-team-input">Equipe</label>
                            <select class="form-control" id="board-team-input" required>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="board-date-input">Data de Criação</label>
                            <input type="date" class="form-control" id="board-date-input" value="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="save-board-btn">Salvar Quadro</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Criar/Editar Lista -->
    <div class="modal fade" id="listModal" tabindex="-1" role="dialog" aria-labelledby="listModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="listModalLabel">Criar/Editar Lista</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="listForm">
                        <input type="hidden" id="list-id">
                        <input type="hidden" id="list-board-id"> 
                        <div class="form-group">
                            <label for="list-name-input">Nome da Lista</label>
                            <input type="text" class="form-control" id="list-name-input" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="save-list-btn">Salvar Lista</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Criar/Editar Cartão -->
    <div class="modal fade" id="cardModal" tabindex="-1" role="dialog" aria-labelledby="cardModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cardModalLabel">Criar/Editar Cartão</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="cardForm">
                        <input type="hidden" id="card-id">
                        <input type="hidden" id="card-list-id">
                        <div class="form-group">
                            <label for="card-title-input">Título do Cartão</label>
                            <input type="text" class="form-control" id="card-title-input" required>
                        </div>
                        <div class="form-group">
                            <label for="card-description-input">Descrição</label>
                            <textarea class="form-control" id="card-description-input" rows="3"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="card-due-date-input">Data de Vencimento</label>
                            <input type="date" class="form-control" id="card-due-date-input">
                        </div>
                        <div class="form-group">
                            <label for="card-creator-input">Criado por (Usuário)</label>
                            <select class="form-control" id="card-creator-input" required>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="save-card-btn">Salvar Cartão</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="cardDetailsModal" tabindex="-1" role="dialog" aria-labelledby="cardDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cardDetailsModalLabel">Detalhes do Cartão</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <h4 id="card-detail-title" class="mb-2"></h4>
                    <p id="card-detail-description" class="text-muted"></p>
                    <p><strong>Vencimento:</strong> <span id="card-detail-due-date"></span></p>
                    <p><strong>Criado por:</strong> <span id="card-detail-creator"></span></p>

                    <hr>
                    <h5>Etiquetas</h5>
                    <div id="card-detail-labels" class="mb-3"></div>

                    <h5>Membros Atribuídos</h5>
                    <div id="card-detail-assigned-members" class="mb-3"></div>

                    <hr>
                    <h5>Checklists</h5>
                    <div id="card-detail-checklists" class="mb-3">
                        <button class="btn btn-sm btn-outline-secondary add-checklist-btn" data-card-id="">Adicionar Checklist</button>
                    </div>

                    <hr>
                    <h5>Anexos</h5>
                    <div id="card-detail-attachments" class="mb-3">
                        <button class="btn btn-sm btn-outline-secondary add-attachment-btn" data-card-id="">Adicionar Anexo</button>
                    </div>

                    <hr>
                    <h5>Comentários</h5>
                    <div id="card-detail-comments" class="mb-3">
                        <div class="form-group">
                            <textarea class="form-control" id="new-comment-content" rows="2" placeholder="Escreva um comentário..."></textarea>
                        </div>
                        <button class="btn btn-primary btn-sm" id="post-comment-btn" data-card-id="">Postar Comentário</button>
                        <div id="comments-list" class="mt-3"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dragula/3.7.2/dragula.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dragula/3.7.2/dragula.min.css" />
    <script src="js/script.js"></script>
</body>
</html>
