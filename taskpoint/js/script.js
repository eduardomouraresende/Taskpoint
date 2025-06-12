$(document).ready(function() {
    let currentBoardId = null;
    let dragulaInstance = null; // Para a instância do Dragula

    // Função para mostrar mensagens de alerta amigáveis (substitui alert())
    function showMessage(message, type = 'info') {
        // Implementar um modal ou um toast de notificação aqui
        // Por enquanto, usaremos um alert simples para fins de demonstração
        alert(message);
    }

    // Função para carregar usuários para dropdowns
    function loadUsers(selector, selectedId = null) {
        console.log("LOG API CALL: Buscando usuários para dropdown. URL: api/usuarios.php, Método: GET"); // Log da chamada de API
        $.ajax({
            url: 'api/usuarios.php',
            method: 'GET',
            dataType: 'json',
            success: function(users) {
                $(selector).empty();
                $(selector).append('<option value="">Selecione um Usuário</option>');
                $.each(users, function(index, user) {
                    const selected = (selectedId && user.ID_Usuario == selectedId) ? 'selected' : '';
                    $(selector).append(`<option value="${user.ID_Usuario}" ${selected}>${user.Nome}</option>`);
                });
            },
            error: function(xhr, status, error) {
                console.error("Erro ao carregar usuários:", error);
                showMessage("Erro ao carregar usuários.", 'danger');
            }
        });
    }

    // Função para carregar equipes para dropdowns
    function loadTeams(selector, selectedId = null) {
        console.log("LOG API CALL: Buscando equipes para dropdown. URL: api/equipes.php, Método: GET"); // Log da chamada de API
        $.ajax({
            url: 'api/equipes.php',
            method: 'GET',
            dataType: 'json',
            success: function(teams) {
                $(selector).empty();
                $(selector).append('<option value="">Selecione uma Equipe</option>');
                $.each(teams, function(index, team) {
                    const selected = (selectedId && team.ID_Equipe == selectedId) ? 'selected' : '';
                    $(selector).append(`<option value="${team.ID_Equipe}" ${selected}>${team.Nome}</option>`);
                });
            },
            error: function(xhr, status, error) {
                console.error("Erro ao carregar equipes:", error);
                showMessage("Erro ao carregar equipes.", 'danger');
            }
        });
    }

    // Função para carregar todos os quadros
    function loadBoards() {
        console.log("LOG API CALL: Buscando todos os quadros. URL: api/quadros.php, Método: GET"); // Log da chamada de API
        $.ajax({
            url: 'api/quadros.php',
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                $('#boards-list').empty();
                if (data.length > 0) {
                    $.each(data, function(index, board) {
                        $('#boards-list').append(
                            `<div class="col-md-3">
                                <div class="board-card rounded" data-id="${board.ID_Quadro}">
                                    <h5>${board.Nome}</h5>
                                    <p>${board.Descricao || 'Sem descrição'}</p>
                                    <small>Criado em: ${board.Dt_Criacao}</small><br>
                                    <button class="btn btn-sm btn-info edit-board-btn mt-2 mr-2" data-id="${board.ID_Quadro}">
                                        <i class="fas fa-edit"></i> Editar
                                    </button>
                                    <button class="btn btn-sm btn-danger delete-board-btn mt-2" data-id="${board.ID_Quadro}">
                                        <i class="fas fa-trash-alt"></i> Excluir
                                    </button>
                                </div>
                            </div>`
                        );
                    });
                } else {
                    $('#boards-list').append('<p class="col-12 text-center">Nenhum quadro encontrado. Crie um novo!</p>');
                }
            },
            error: function(xhr, status, error) {
                console.error("Erro ao carregar quadros:", error);
                showMessage("Erro ao carregar quadros.", 'danger');
            }
        });
    }

    // Função para carregar detalhes de um quadro (listas e cartões)
    function loadBoardDetails(boardId) {
        currentBoardId = boardId;
        console.log("DEBUG: currentBoardId foi definido como:", currentBoardId); // Linha adicionada para depuração
        $('#boards-section').hide();
        $('#board-detail-section').show();

        // Limpa containers antes de carregar novos dados
        $('#lists-container').empty();
        if (dragulaInstance) {
            dragulaInstance.destroy(); // Destruir instância anterior do Dragula
        }

        // Busca detalhes do quadro
        console.log(`LOG API CALL: Buscando detalhes do quadro. URL: api/quadros.php?id=${boardId}, Método: GET`); // Log da chamada de API
        $.ajax({
            url: `api/quadros.php?id=${boardId}`,
            method: 'GET',
            dataType: 'json',
            success: function(board) {
                $('#board-name').text(board.Nome);
                $('#board-description').text(board.Descricao || 'Sem descrição');

                // Busca listas para este quadro
                console.log(`LOG API CALL: Buscando listas para o quadro ${boardId}. URL: api/listas.php?board_id=${boardId}, Método: GET`); // Log da chamada de API
                $.ajax({
                    url: `api/listas.php?board_id=${boardId}`,
                    method: 'GET',
                    dataType: 'json',
                    success: function(lists) {
                        let containersForDragula = []; // Array para containers do Dragula

                        if (lists.length > 0) {
                            $.each(lists, function(index, list) {
                                let listHtml = `
                                    <div class="list-container rounded" data-list-id="${list.ID_Lista}">
                                        <div class="list-header">
                                            <span>${list.Nome}</span>
                                            <div class="list-actions">
                                                <i class="fas fa-edit edit-list-btn text-info mr-2" data-list-id="${list.ID_Lista}" data-list-name="${list.Nome}" title="Editar Lista"></i>
                                                <i class="fas fa-trash-alt delete-list-btn ml-2" data-list-id="${list.ID_Lista}" title="Excluir Lista"></i>
                                            </div>
                                        </div>
                                        <div class="cards-in-list" id="list-${list.ID_Lista}-cards">
                                            <!-- Cartões serão carregados aqui -->
                                        </div>
                                        <button class="btn btn-sm btn-light add-card-btn rounded mt-3" data-list-id="${list.ID_Lista}">
                                            <i class="fas fa-plus"></i> Adicionar Cartão
                                        </button>
                                    </div>`;
                                $('#lists-container').append(listHtml);
                                containersForDragula.push(document.getElementById(`list-${list.ID_Lista}-cards`));
                                loadCardsForList(list.ID_Lista); // Carrega cartões para cada lista
                            });
                        } else {
                            $('#lists-container').append('<p class="text-center w-100">Nenhuma lista encontrada. Adicione uma!</p>');
                        }

                        // Inicializa Dragula após carregar todas as listas
                        if (containersForDragula.length > 0) {
                            dragulaInstance = dragula(containersForDragula);

                            dragulaInstance.on('drop', function(el, target, source, sibling) {
                                const cardId = $(el).data('card-id');
                                const newlistId = $(target).closest('.list-container').data('list-id');
                                const oldListId = $(source).closest('.list-container').data('list-id');

                                // Atualiza o FK_Lista_ID_Lista no banco de dados
                                if (cardId && newlistId) {
                                    const moveData = { ID_Cartao: cardId, ID_Lista_Destino: newlistId };
                                    console.log("LOG API CALL: Movendo cartão entre listas. URL: api/cartoes_move.php, Método: POST, Dados:", moveData); // Log da chamada de API
                                    $.ajax({
                                        url: 'api/cartoes_move.php', // Endpoint para a stored procedure
                                        method: 'POST',
                                        contentType: 'application/json',
                                        data: JSON.stringify(moveData),
                                        success: function(response) {
                                            console.log(response.message);
                                            // O Dragula já moveu visualmente, então não precisa recarregar tudo
                                            // No entanto, é bom recarregar apenas a lista de origem e destino para
                                            // garantir consistência se houver outras interações complexas.
                                            // loadCardsForList(oldListId);
                                            // loadCardsForList(newlistId);
                                        },
                                        error: function(xhr, status, error) {
                                            console.error("Erro ao mover cartão:", error);
                                            showMessage("Erro ao mover cartão. Por favor, recarregue a página.", 'danger');
                                            // Reverte a ação visual se a API falhar
                                            dragulaInstance.cancel(true);
                                        }
                                    });
                                }
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Erro ao carregar listas:", error);
                        showMessage("Erro ao carregar listas.", 'danger');
                    }
                });
            },
            error: function(xhr, status, error) {
                console.error("Erro ao carregar detalhes do quadro:", error);
                showMessage("Erro ao carregar detalhes do quadro.", 'danger');
            }
        });
    }

    // Função para carregar cartões para uma lista específica
    function loadCardsForList(listId) {
        console.log(`LOG API CALL: Buscando cartões para a lista ${listId}. URL: api/cartoes.php?list_id=${listId}, Método: GET`); // Log da chamada de API
        $.ajax({
            url: `api/cartoes.php?list_id=${listId}`,
            method: 'GET',
            dataType: 'json',
            success: function(cards) {
                $(`#list-${listId}-cards`).empty();
                if (cards.length > 0) {
                    $.each(cards, function(index, card) {
                        const dueDate = card.Dt_Vencimento ? new Date(card.Dt_Vencimento).toLocaleDateString('pt-BR') : 'N/A';
                        $(`#list-${listId}-cards`).append(
                            `<div class="card-item rounded" data-card-id="${card.ID_Cartao}">
                                <h6>${card.Titulo}</h6>
                                <p><small>${card.Descricao || 'Sem descrição'}</small></p>
                                <small>Vencimento: ${dueDate}</small><br>
                                <div class="card-actions mt-2">
                                    <button class="btn btn-sm btn-info edit-card-btn mr-1" data-id="${card.ID_Cartao}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger delete-card-btn mr-1" data-id="${card.ID_Cartao}">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                    <button class="btn btn-sm btn-secondary view-card-details-btn" data-id="${card.ID_Cartao}">
                                        <i class="fas fa-info-circle"></i> Detalhes
                                    </button>
                                </div>
                            </div>`
                        );
                    });
                } else {
                    $(`#list-${listId}-cards`).append('<p class="text-center"><small>Nenhum cartão nesta lista.</small></p>');
                }
            },
            error: function(xhr, status, error) {
                console.error(`Erro ao carregar cartões para a lista ${listId}:`, error);
                showMessage(`Erro ao carregar cartões para a lista ${listId}.`, 'danger');
            }
        });
    }

    // Função para carregar detalhes completos de um cartão no modal
    function loadCardDetailsInModal(cardId) {
        // Limpa o conteúdo anterior
        $('#card-detail-title').text('');
        $('#card-detail-description').text('');
        $('#card-detail-due-date').text('');
        $('#card-detail-creator').text('');
        $('#card-detail-labels').empty();
        $('#card-detail-assigned-members').empty();
        $('#card-detail-checklists').empty().append('<button class="btn btn-sm btn-outline-secondary add-checklist-btn" data-card-id="' + cardId + '">Adicionar Checklist</button>');
        $('#card-detail-attachments').empty().append('<button class="btn btn-sm btn-outline-secondary add-attachment-btn" data-card-id="' + cardId + '">Adicionar Anexo</button>');
        $('#comments-list').empty();
        $('#new-comment-content').val('');
        $('#post-comment-btn').data('card-id', cardId);
        $('.add-checklist-btn').data('card-id', cardId);
        $('.add-attachment-btn').data('card-id', cardId);


        // 1. Fetch Card Details
        console.log(`LOG API CALL: Buscando detalhes do cartão. URL: api/cartoes.php?id=${cardId}, Método: GET`); // Log da chamada de API
        $.ajax({
            url: `api/cartoes.php?id=${cardId}`,
            method: 'GET',
            dataType: 'json',
            success: function(card) {
                if (card) {
                    $('#card-detail-title').text(card.Titulo);
                    $('#card-detail-description').text(card.Descricao || 'Sem descrição');
                    const dueDate = card.Dt_Vencimento ? new Date(card.Dt_Vencimento).toLocaleDateString('pt-BR') : 'N/A';
                    $('#card-detail-due-date').text(dueDate);

                    // Fetch creator name
                    console.log(`LOG API CALL: Buscando nome do criador do cartão. URL: api/usuarios.php?id=${card.fk_Usuario_ID_Usuario}, Método: GET`); // Log da chamada de API
                    $.ajax({
                        url: `api/usuarios.php?id=${card.fk_Usuario_ID_Usuario}`,
                        method: 'GET',
                        dataType: 'json',
                        success: function(user) {
                            $('#card-detail-creator').text(user ? user.Nome : 'Desconhecido');
                        }
                    });

                    // 2. Fetch Labels for this card
                    console.log(`LOG API CALL: Buscando etiquetas do cartão. URL: api/cartaoetiqueta.php?card_id=${cardId}, Método: GET`); // Log da chamada de API
                    $.ajax({
                        url: `api/cartaoetiqueta.php?card_id=${cardId}`,
                        method: 'GET',
                        dataType: 'json',
                        success: function(cardLabels) {
                            if (cardLabels.length > 0) {
                                $.each(cardLabels, function(index, cl) {
                                    // Fetch label details
                                    console.log(`LOG API CALL: Buscando detalhes da etiqueta ${cl.fk_Etiqueta_ID_Etiqueta}. URL: api/etiquetas.php?id=${cl.fk_Etiqueta_ID_Etiqueta}, Método: GET`); // Log da chamada de API
                                    $.ajax({
                                        url: `api/etiquetas.php?id=${cl.fk_Etiqueta_ID_Etiqueta}`,
                                        method: 'GET',
                                        dataType: 'json',
                                        success: function(label) {
                                            if (label) {
                                                $('#card-detail-labels').append(
                                                    `<span class="badge" style="background-color: ${label.Cor}; color: white;">${label.Nome}</span>`
                                                );
                                            }
                                        }
                                    });
                                });
                            } else {
                                $('#card-detail-labels').append('<p><small>Nenhuma etiqueta.</small></p>');
                            }
                        }
                    });

                    // 3. Fetch Assigned Members for this card
                    console.log(`LOG API CALL: Buscando membros atribuídos ao cartão. URL: api/atribuicoes.php?card_id=${cardId}, Método: GET`); // Log da chamada de API
                    $.ajax({
                        url: `api/atribuicoes.php?card_id=${cardId}`,
                        method: 'GET',
                        dataType: 'json',
                        success: function(assignments) {
                            if (assignments.length > 0) {
                                $.each(assignments, function(index, assignment) {
                                    console.log(`LOG API CALL: Buscando detalhes do usuário atribuído ${assignment.fk_Usuario_ID_Usuario}. URL: api/usuarios.php?id=${assignment.fk_Usuario_ID_Usuario}, Método: GET`); // Log da chamada de API
                                    $.ajax({
                                        url: `api/usuarios.php?id=${assignment.fk_Usuario_ID_Usuario}`,
                                        method: 'GET',
                                        dataType: 'json',
                                        success: function(user) {
                                            if (user) {
                                                $('#card-detail-assigned-members').append(
                                                    `<span class="badge badge-secondary mr-2">${user.Nome}</span>`
                                                );
                                            }
                                        }
                                    });
                                });
                            } else {
                                $('#card-detail-assigned-members').append('<p><small>Nenhum membro atribuído.</small></p>');
                            }
                        }
                    });

                    // 4. Fetch Checklists and their Items
                    console.log(`LOG API CALL: Buscando checklists do cartão. URL: api/checklists.php?card_id=${cardId}, Método: GET`); // Log da chamada de API
                    $.ajax({
                        url: `api/checklists.php?card_id=${cardId}`,
                        method: 'GET',
                        dataType: 'json',
                        success: function(checklists) {
                            if (checklists.length > 0) {
                                $.each(checklists, function(index, checklist) {
                                    let checklistHtml = `
                                        <div class="card p-2 mb-2 rounded" data-checklist-id="${checklist.ID_Checklist}">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <h6>${checklist.Nome}</h6>
                                                <div>
                                                    <i class="fas fa-edit edit-checklist-btn text-info mr-2" data-checklist-id="${checklist.ID_Checklist}" data-checklist-name="${checklist.Nome}"></i>
                                                    <i class="fas fa-trash-alt delete-checklist-btn text-danger" data-checklist-id="${checklist.ID_Checklist}"></i>
                                                </div>
                                            </div>
                                            <div id="checklist-${checklist.ID_Checklist}-items"></div>
                                            <button class="btn btn-sm btn-outline-success add-item-checklist-btn mt-2" data-checklist-id="${checklist.ID_Checklist}">
                                                <i class="fas fa-plus"></i> Adicionar Item
                                            </button>
                                        </div>`;
                                    $('#card-detail-checklists').append(checklistHtml);
                                    loadChecklistItems(checklist.ID_Checklist);
                                });
                            } else {
                                $('#card-detail-checklists').append('<p><small>Nenhum checklist.</small></p>');
                            }
                        }
                    });

                    // 5. Fetch Attachments
                    console.log(`LOG API CALL: Buscando anexos do cartão. URL: api/anexos.php?card_id=${cardId}, Método: GET`); // Log da chamada de API
                    $.ajax({
                        url: `api/anexos.php?card_id=${cardId}`,
                        method: 'GET',
                        dataType: 'json',
                        success: function(attachments) {
                            if (attachments.length > 0) {
                                $.each(attachments, function(index, attachment) {
                                    $('#card-detail-attachments').append(
                                        `<div class="attachment-item d-flex justify-content-between align-items-center">
                                            <span><i class="fas fa-paperclip mr-1"></i> <a href="${attachment.URL}" target="_blank">${attachment.Nome}</a></span>
                                            <button class="btn btn-sm btn-danger delete-attachment-btn" data-id="${attachment.ID_Anexo}"><i class="fas fa-trash-alt"></i></button>
                                        </div>`
                                    );
                                });
                            } else {
                                $('#card-detail-attachments').append('<p><small>Nenhum anexo.</small></p>');
                            }
                        }
                    });

                    // 6. Fetch Comments
                    console.log(`LOG API CALL: Buscando comentários do cartão. URL: api/comentarios.php?card_id=${cardId}, Método: GET`); // Log da chamada de API
                    $.ajax({
                        url: `api/comentarios.php?card_id=${cardId}`,
                        method: 'GET',
                        dataType: 'json',
                        success: function(comments) {
                            if (comments.length > 0) {
                                $.each(comments, function(index, comment) {
                                    // Fetch user name for each comment
                                    console.log(`LOG API CALL: Buscando nome do autor do comentário ${comment.ID_Comentario}. URL: api/usuarios.php?id=${comment.fk_Usuario_ID_Usuario}, Método: GET`); // Log da chamada de API
                                    $.ajax({
                                        url: `api/usuarios.php?id=${comment.fk_Usuario_ID_Usuario}`,
                                        method: 'GET',
                                        dataType: 'json',
                                        success: function(user) {
                                            const userName = user ? user.Nome : 'Usuário Desconhecido';
                                            $('#comments-list').append(
                                                `<div class="comment-item">
                                                    <span class="comment-author">${userName}</span>
                                                    <span class="comment-date">${new Date(comment.Dt_Criacao).toLocaleString('pt-BR')}</span>
                                                    <p class="comment-content">${comment.Conteudo}</p>
                                                    <button class="btn btn-sm btn-danger delete-comment-btn" data-id="${comment.ID_Comentario}"><i class="fas fa-trash-alt"></i></button>
                                                </div>`
                                            );
                                        }
                                    });
                                });
                            } else {
                                $('#comments-list').append('<p><small>Nenhum comentário.</small></p>');
                            }
                        }
                    });

                    $('#cardDetailsModal').modal('show');
                } else {
                    showMessage("Cartão não encontrado.", 'warning');
                }
            },
            error: function(xhr, status, error) {
                console.error("Erro ao carregar detalhes do cartão:", error);
                showMessage("Erro ao carregar detalhes do cartão.", 'danger');
            }
        });
    }

    // Função para carregar itens de um checklist
    function loadChecklistItems(checklistId) {
        console.log(`LOG API CALL: Buscando itens da checklist ${checklistId}. URL: api/itemchecklists.php?checklist_id=${checklistId}, Método: GET`); // Log da chamada de API
        $.ajax({
            url: `api/itemchecklists.php?checklist_id=${checklistId}`,
            method: 'GET',
            dataType: 'json',
            success: function(items) {
                const $container = $(`#checklist-${checklistId}-items`);
                $container.empty();
                if (items.length > 0) {
                    $.each(items, function(index, item) {
                        const isChecked = item.Status === 'Concluído' ? 'checked' : '';
                        const textClass = item.Status === 'Concluído' ? 'completed' : '';
                        $container.append(
                            `<div class="checklist-item ${textClass}" data-item-id="${item.ID_ItemChecklist}" data-status="${item.Status}">
                                <input type="checkbox" class="checklist-item-checkbox" ${isChecked}>
                                <span>${item.Nome}</span>
                                <i class="fas fa-trash-alt delete-item-checklist-btn text-danger ml-auto" data-item-id="${item.ID_ItemChecklist}" title="Excluir Item"></i>
                            </div>`
                        );
                    });
                } else {
                    $container.append('<p><small>Nenhum item nesta checklist.</small></p>');
                }
            },
            error: function(xhr, status, error) {
                console.error(`Erro ao carregar itens da checklist ${checklistId}:`, error);
            }
        });
    }


    // --- Event Listeners ---

    // Carregamento inicial dos quadros
    loadBoards();

    // Evento: Abrir detalhes do quadro
    $(document).on('click', '.board-card', function() {
        const boardId = $(this).data('id');
        loadBoardDetails(boardId);
    });

    // Evento: Voltar para a lista de quadros
    $('#back-to-boards-btn').on('click', function() {
        $('#board-detail-section').hide();
        $('#boards-section').show();
        loadBoards(); // Recarrega quadros para refletir possíveis mudanças
    });

    // Evento: Abrir modal de criação de quadro
    $('#create-board-btn').on('click', function() {
        $('#boardModalLabel').text('Criar Novo Quadro');
        $('#boardForm')[0].reset(); // Limpa o formulário
        $('#board-id').val(''); // Limpa o ID para nova criação
        $('#board-date-input').val(new Date().toISOString().slice(0, 10)); // Data atual
        loadUsers('#board-user-input');
        loadTeams('#board-team-input');
        $('#boardModal').modal('show');
    });

    // Evento: Salvar Quadro (Criar/Editar)
    $('#save-board-btn').on('click', function() {
        const boardId = $('#board-id').val();
        const boardName = $('#board-name-input').val();
        const boardDescription = $('#board-description-input').val();
        const boardUser = $('#board-user-input').val();
        const boardTeam = $('#board-team-input').val();
        const boardDate = $('#board-date-input').val();

        if (!boardName || !boardUser || !boardTeam || !boardDate) {
            showMessage("Por favor, preencha todos os campos obrigatórios do quadro.", 'warning');
            return;
        }

        const boardData = {
            Nome: boardName,
            Descricao: boardDescription,
            Dt_Criacao: boardDate,
            fk_Usuario_ID_Usuario: parseInt(boardUser),
            fk_Equipe_ID_Equipe: parseInt(boardTeam)
        };

        let url = 'api/quadros.php';
        let method = 'POST';

        if (boardId) { // Se o ID existe, é uma atualização (PUT)
            method = 'PUT';
            boardData.ID_Quadro = parseInt(boardId);
        }

        console.log(`LOG API CALL: Salvando quadro. URL: ${url}, Método: ${method}, Dados:`, boardData); // Log da chamada de API
        $.ajax({
            url: url,
            method: method,
            contentType: 'application/json',
            data: JSON.stringify(boardData),
            success: function(response) {
                showMessage(response.message, 'success');
                $('#boardModal').modal('hide');
                loadBoards(); // Recarrega a lista de quadros
            },
            error: function(xhr, status, error) {
                console.error("Erro ao salvar quadro:", error);
                let errorMessage = "Erro desconhecido ao salvar quadro.";
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.responseText) {
                    // Tentativa de parsear o erro se não for JSON válido
                    try {
                        const parsedError = JSON.parse(xhr.responseText);
                        errorMessage = parsedError.message || xhr.responseText;
                    } catch (e) {
                        errorMessage = xhr.responseText;
                    }
                }
                showMessage("Erro ao salvar quadro. Detalhes: " + errorMessage, 'danger');
            }
        });
    });

    // Evento: Editar Quadro - Popula o modal
    $(document).on('click', '.edit-board-btn', function(e) {
        e.stopPropagation(); // Previne a abertura dos detalhes do quadro
        const boardId = $(this).data('id');

        console.log(`LOG API CALL: Buscando quadro para edição. URL: api/quadros.php?id=${boardId}, Método: GET`); // Log da chamada de API
        $.ajax({
            url: `api/quadros.php?id=${boardId}`,
            method: 'GET',
            dataType: 'json',
            success: function(board) {
                if (board) {
                    $('#boardModalLabel').text('Editar Quadro');
                    $('#board-id').val(board.ID_Quadro);
                    $('#board-name-input').val(board.Nome);
                    $('#board-description-input').val(board.Descricao);
                    $('#board-date-input').val(board.Dt_Criacao);
                    loadUsers('#board-user-input', board.fk_Usuario_ID_Usuario);
                    loadTeams('#board-team-input', board.fk_Equipe_ID_Equipe);
                    $('#boardModal').modal('show');
                } else {
                    showMessage("Quadro não encontrado para edição.", 'warning');
                }
            },
            error: function(xhr, status, error) {
                console.error("Erro ao buscar quadro para edição:", error);
                showMessage("Erro ao buscar quadro para edição.", 'danger');
            }
        });
    });

    // Evento: Deletar Quadro
    $(document).on('click', '.delete-board-btn', function(e) {
        e.stopPropagation();
        const boardId = $(this).data('id');
        if (confirm('Tem certeza que deseja excluir este quadro? Todas as listas, cartões e itens associados serão removidos.')) {
            const deleteData = { ID_Quadro: boardId };
            console.log("LOG API CALL: Deletando quadro. URL: api/quadros.php, Método: DELETE, Dados:", deleteData); // Log da chamada de API
            $.ajax({
                url: 'api/quadros.php',
                method: 'DELETE',
                contentType: 'application/json',
                data: JSON.stringify(deleteData),
                success: function(response) {
                    showMessage(response.message, 'success');
                    loadBoards();
                },
                error: function(xhr, status, error) {
                    console.error("Erro ao excluir quadro:", error);
                    showMessage("Erro ao excluir quadro. Detalhes: " + (xhr.responseJSON ? xhr.responseJSON.message : error), 'danger');
                }
            });
        }
    });

    // Evento: Abrir modal de criação de Lista
    $('#create-list-btn').on('click', function() {
        console.log("DEBUG: Clique no botão 'Adicionar Nova Lista'. currentBoardId atual:", currentBoardId); // Linha adicionada para depuração
        if (!currentBoardId) {
            showMessage("Por favor, selecione um quadro primeiro para adicionar uma lista.", 'warning');
            return; // Impede que o modal abra se não houver quadro selecionado
        }
        $('#listModalLabel').text('Criar Nova Lista');
        $('#listForm')[0].reset();
        $('#list-id').val(''); // Limpa o ID para nova criação
        $('#list-board-id').val(currentBoardId); // Associa ao quadro atual
        console.log("DEBUG: Valor atribuído a #list-board-id (campo oculto):", $('#list-board-id').val()); // Linha adicionada para depuração
        $('#listModal').modal('show');
    });

    // Evento: Salvar Lista (Criar/Editar)
    $('#save-list-btn').on('click', function() {
        const listId = $('#list-id').val();
        const listName = $('#list-name-input').val();
        const boardId = $('#list-board-id').val();

        if (!listName) {
            showMessage("O nome da lista não pode estar vazio.", 'warning');
            return;
        }

        const listData = {
            Nome: listName,
            fk_Quadro_ID_Quadro: parseInt(boardId)
        };

        let url = 'api/listas.php';
        let method = 'POST';

        if (listId) {
            method = 'PUT';
            listData.ID_Lista = parseInt(listId);
            // Ao editar, a ordem e o fk_Quadro_ID_Quadro não devem ser alterados diretamente
            // Para simplicidade, apenas o nome será editável no modal.
        } else {
            // Para novas listas, defina uma ordem inicial (pode ser ajustada depois)
            // Buscar a maior ordem existente para o quadro ou um valor alto.
            // Para manter a simplicidade, vamos usar um valor alto para que a nova lista vá para o final.
            listData.Ordem = 9999; // Valor alto para aparecer no final
        }

        console.log(`LOG API CALL: Salvando lista. URL: ${url}, Método: ${method}, Dados:`, listData); // Log da chamada de API
        $.ajax({
            url: url,
            method: method,
            contentType: 'application/json',
            data: JSON.stringify(listData),
            success: function(response) {
                showMessage(response.message, 'success');
                $('#listModal').modal('hide');
                loadBoardDetails(currentBoardId); // Recarrega listas e cartões do quadro atual
            },
            error: function(xhr, status, error) {
                console.error("Erro ao salvar lista:", error);
                let errorMessage = "Erro desconhecido ao salvar lista.";
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.responseText) {
                    // Tentativa de parsear o erro se não for JSON válido
                    try {
                        const parsedError = JSON.parse(xhr.responseText);
                        errorMessage = parsedError.message || xhr.responseText;
                    } catch (e) {
                        errorMessage = xhr.responseText;
                    }
                }
                showMessage("Erro ao salvar lista. Detalhes: " + errorMessage, 'danger');
            }
        });
    });

    // Evento: Editar Lista
    $(document).on('click', '.edit-list-btn', function(e) {
        e.stopPropagation();
        const listId = $(this).data('list-id');
        const listName = $(this).data('list-name');

        console.log(`LOG API CALL: Buscando lista para edição. URL: api/listas.php?id=${listId}, Método: GET`); // Log da chamada de API
        $.ajax({
            url: `api/listas.php?id=${listId}`,
            method: 'GET',
            dataType: 'json',
            success: function(list) {
                if (list) {
                    $('#listModalLabel').text('Editar Lista');
                    $('#list-id').val(list.ID_Lista);
                    $('#list-name-input').val(list.Nome);
                    $('#list-board-id').val(currentBoardId);
                    $('#listModal').modal('show');
                } else {
                    showMessage("Lista não encontrada para edição.", 'warning');
                }
            },
            error: function(xhr, status, error) {
                console.error("Erro ao buscar lista para edição:", error);
                showMessage("Erro ao buscar lista para edição.", 'danger');
            }
        });
    });

    // Evento: Deletar Lista
    $(document).on('click', '.delete-list-btn', function() {
        const listId = $(this).data('list-id');
        if (confirm('Tem certeza que deseja excluir esta lista? Todos os cartões associados serão removidos.')) {
            const deleteData = { ID_Lista: listId };
            console.log("LOG API CALL: Deletando lista. URL: api/listas.php, Método: DELETE, Dados:", deleteData); // Log da chamada de API
            $.ajax({
                url: 'api/listas.php',
                method: 'DELETE',
                contentType: 'application/json',
                data: JSON.stringify(deleteData),
                success: function(response) {
                    showMessage(response.message, 'success');
                    loadBoardDetails(currentBoardId); // Recarrega listas e cartões do quadro atual
                },
                error: function(xhr, status, error) {
                    console.error("Erro ao excluir lista:", error);
                    showMessage("Erro ao excluir lista. Detalhes: " + (xhr.responseJSON ? xhr.responseJSON.message : error), 'danger');
                }
            });
        }
    });

    // Evento: Abrir modal de criação de Cartão
    $(document).on('click', '.add-card-btn', function() {
        const listId = $(this).data('list-id');
        $('#cardModalLabel').text('Criar Novo Cartão');
        $('#cardForm')[0].reset();
        $('#card-id').val('');
        $('#card-list-id').val(listId);
        $('#card-due-date-input').val(''); // Limpa a data de vencimento
        $('#card-creator-input').val(''); // Limpa o criador
        loadUsers('#card-creator-input'); // Carrega usuários para o criador
        $('#cardModal').modal('show');
    });

    // Evento: Salvar Cartão (Criar/Editar)
    $('#save-card-btn').on('click', function() {
        const cardId = $('#card-id').val();
        const cardTitle = $('#card-title-input').val();
        const cardDescription = $('#card-description-input').val();
        const cardDueDate = $('#card-due-date-input').val();
        const cardCreator = $('#card-creator-input').val();
        const listId = $('#card-list-id').val();

        if (!cardTitle || !cardCreator) {
            showMessage("Título e criador do cartão são obrigatórios.", 'warning');
            return;
        }

        const cardData = {
            Titulo: cardTitle,
            Descricao: cardDescription,
            Dt_Criacao: new Date().toISOString().slice(0, 10), // Data atual de criação
            Dt_Vencimento: cardDueDate || null, // Pode ser null
            fk_Lista_ID_Lista: parseInt(listId),
            fk_Usuario_ID_Usuario: parseInt(cardCreator)
        };

        let url = 'api/cartoes.php';
        let method = 'POST';

        if (cardId) {
            method = 'PUT';
            cardData.ID_Cartao = parseInt(cardId);
            // Ao editar, a data de criação, lista e usuário criador não devem ser alterados diretamente
            delete cardData.Dt_Criacao;
            delete cardData.fk_Lista_ID_Lista;
        }

        console.log(`LOG API CALL: Salvando cartão. URL: ${url}, Método: ${method}, Dados:`, cardData); // Log da chamada de API
        $.ajax({
            url: url,
            method: method,
            contentType: 'application/json',
            data: JSON.stringify(cardData),
            success: function(response) {
                showMessage(response.message, 'success');
                $('#cardModal').modal('hide');
                loadCardsForList(listId); // Recarrega cartões da lista atual
            },
            error: function(xhr, status, error) {
                console.error("Erro ao salvar cartão:", error);
                showMessage("Erro ao salvar cartão. Detalhes: " + (xhr.responseJSON ? xhr.responseJSON.message : error), 'danger');
            }
        });
    });

    // Evento: Editar Cartão - Popula o modal
    $(document).on('click', '.edit-card-btn', function(e) {
        e.stopPropagation();
        const cardId = $(this).data('id');
        const listId = $(this).closest('.list-container').data('list-id');

        console.log(`LOG API CALL: Buscando cartão para edição. URL: api/cartoes.php?id=${cardId}, Método: GET`); // Log da chamada de API
        $.ajax({
            url: `api/cartoes.php?id=${cardId}`,
            method: 'GET',
            dataType: 'json',
            success: function(card) {
                if (card) {
                    $('#cardModalLabel').text('Editar Cartão');
                    $('#card-id').val(card.ID_Cartao);
                    $('#card-list-id').val(listId); // Mantém a lista atual
                    $('#card-title-input').val(card.Titulo);
                    $('#card-description-input').val(card.Descricao);
                    $('#card-due-date-input').val(card.Dt_Vencimento);
                    loadUsers('#card-creator-input', card.fk_Usuario_ID_Usuario);
                    $('#cardModal').modal('show');
                } else {
                    showMessage("Cartão não encontrado para edição.", 'warning');
                }
            },
            error: function(xhr, status, error) {
                console.error("Erro ao buscar cartão para edição:", error);
                showMessage("Erro ao buscar cartão para edição.", 'danger');
            }
        });
    });

    // Evento: Deletar Cartão
    $(document).on('click', '.delete-card-btn', function(e) {
        e.stopPropagation();
        const cardId = $(this).data('id');
        const listId = $(this).closest('.list-container').data('list-id');
        if (confirm('Tem certeza que deseja excluir este cartão?')) {
            const deleteData = { ID_Cartao: cardId };
            console.log("LOG API CALL: Deletando cartão. URL: api/cartoes.php, Método: DELETE, Dados:", deleteData); // Log da chamada de API
            $.ajax({
                url: 'api/cartoes.php',
                method: 'DELETE',
                contentType: 'application/json',
                data: JSON.stringify(deleteData),
                success: function(response) {
                    showMessage(response.message, 'success');
                    loadCardsForList(listId); // Recarrega cartões da lista atual
                },
                error: function(xhr, status, error) {
                    console.error("Erro ao excluir cartão:", error);
                    showMessage("Erro ao excluir cartão. Detalhes: " + (xhr.responseJSON ? xhr.responseJSON.message : error), 'danger');
                }
            });
        }
    });

    // Evento: Abrir Modal de Detalhes do Cartão
    $(document).on('click', '.view-card-details-btn', function(e) {
        e.stopPropagation();
        const cardId = $(this).data('id');
        loadCardDetailsInModal(cardId);
    });

    // Evento: Adicionar Checklist
    $(document).on('click', '.add-checklist-btn', function() {
        const cardId = $(this).data('card-id');
        const checklistName = prompt('Nome da nova Checklist:');
        if (checklistName) {
            const checklistData = { Nome: checklistName, fk_Cartao_ID_Cartao: cardId };
            console.log("LOG API CALL: Adicionando checklist. URL: api/checklists.php, Método: POST, Dados:", checklistData); // Log da chamada de API
            $.ajax({
                url: 'api/checklists.php',
                method: 'POST',
                contentType: 'application/json',
                data: JSON.stringify(checklistData),
                success: function(response) {
                    showMessage(response.message, 'success');
                    loadCardDetailsInModal(cardId); // Recarrega os detalhes do cartão para mostrar a nova checklist
                },
                error: function(xhr, status, error) {
                    console.error("Erro ao adicionar checklist:", error);
                    showMessage("Erro ao adicionar checklist.", 'danger');
                }
            });
        }
    });

    // Evento: Excluir Checklist
    $(document).on('click', '.delete-checklist-btn', function() {
        const checklistId = $(this).data('checklist-id');
        const cardId = $(this).closest('.modal-body').find('#card-detail-title').closest('.modal-body').find('.add-checklist-btn').data('card-id'); // Gambiarra para pegar o cardId
        if (confirm('Tem certeza que deseja excluir esta checklist e todos os seus itens?')) {
            const deleteData = { ID_Checklist: checklistId };
            console.log("LOG API CALL: Excluindo checklist. URL: api/checklists.php, Método: DELETE, Dados:", deleteData); // Log da chamada de API
            $.ajax({
                url: 'api/checklists.php',
                method: 'DELETE',
                contentType: 'application/json',
                data: JSON.stringify(deleteData),
                success: function(response) {
                    showMessage(response.message, 'success');
                    loadCardDetailsInModal(cardId);
                },
                error: function(xhr, status, error) {
                    console.error("Erro ao excluir checklist:", error);
                    showMessage("Erro ao excluir checklist.", 'danger');
                }
            });
        }
    });

    // Evento: Adicionar Item de Checklist
    $(document).on('click', '.add-item-checklist-btn', function() {
        const checklistId = $(this).data('checklist-id');
        const cardId = $(this).closest('.modal-body').find('#card-detail-title').closest('.modal-body').find('.add-checklist-btn').data('card-id');
        const itemName = prompt('Nome do novo item da Checklist:');
        if (itemName) {
            const itemData = { Nome: itemName, Status: 'Pendente', fk_Checklist_ID_Checklist: checklistId };
            console.log("LOG API CALL: Adicionando item de checklist. URL: api/itemchecklists.php, Método: POST, Dados:", itemData); // Log da chamada de API
            $.ajax({
                url: 'api/itemchecklists.php',
                method: 'POST',
                contentType: 'application/json',
                data: JSON.stringify(itemData),
                success: function(response) {
                    showMessage(response.message, 'success');
                    loadChecklistItems(checklistId);
                },
                error: function(xhr, status, error) {
                    console.error("Erro ao adicionar item de checklist:", error);
                    showMessage("Erro ao adicionar item de checklist.", 'danger');
                }
            });
        }
    });

    // Evento: Mudar Status do Item de Checklist
    $(document).on('change', '.checklist-item-checkbox', function() {
        const itemId = $(this).closest('.checklist-item').data('item-id');
        const currentStatus = $(this).is(':checked') ? 'Concluído' : 'Pendente';
        const $itemElement = $(this).closest('.checklist-item');
        const checklistId = $(this).closest('[data-checklist-id]').data('checklist-id');

        const updateData = { ID_ItemChecklist: itemId, Status: currentStatus };
        console.log("LOG API CALL: Atualizando status do item de checklist. URL: api/itemchecklists.php, Método: PUT, Dados:", updateData); // Log da chamada de API
        $.ajax({
            url: 'api/itemchecklists.php',
            method: 'PUT',
            contentType: 'application/json',
            data: JSON.stringify(updateData),
            success: function(response) {
                showMessage(response.message, 'success');
                loadChecklistItems(checklistId); // Recarrega itens para atualizar visualmente
            },
            error: function(xhr, status, error) {
                console.error("Erro ao atualizar status do item:", error);
                showMessage("Erro ao atualizar status do item.", 'danger');
            }
        });
    });

    // Evento: Excluir Item de Checklist
    $(document).on('click', '.delete-item-checklist-btn', function() {
        const itemId = $(this).data('item-id');
        const checklistId = $(this).closest('[data-checklist-id]').data('checklist-id');
        if (confirm('Tem certeza que deseja excluir este item?')) {
            const deleteData = { ID_ItemChecklist: itemId };
            console.log("LOG API CALL: Excluindo item de checklist. URL: api/itemchecklists.php, Método: DELETE, Dados:", deleteData); // Log da chamada de API
            $.ajax({
                url: 'api/itemchecklists.php',
                method: 'DELETE',
                contentType: 'application/json',
                data: JSON.stringify(deleteData),
                success: function(response) {
                    showMessage(response.message, 'success');
                    loadChecklistItems(checklistId);
                },
                error: function(xhr, status, error) {
                    console.error("Erro ao excluir item de checklist:", error);
                    showMessage("Erro ao excluir item de checklist.", 'danger');
                }
            });
        }
    });

    // Evento: Adicionar Anexo
    $(document).on('click', '.add-attachment-btn', function() {
        const cardId = $(this).data('card-id');
        const attachmentName = prompt('Nome do Anexo:');
        const attachmentUrl = prompt('URL do Anexo:');
        if (attachmentName && attachmentUrl) {
            const attachmentData = { Nome: attachmentName, URL: attachmentUrl, fk_Cartao_ID_Cartao: cardId };
            console.log("LOG API CALL: Adicionando anexo. URL: api/anexos.php, Método: POST, Dados:", attachmentData); // Log da chamada de API
            $.ajax({
                url: 'api/anexos.php',
                method: 'POST',
                contentType: 'application/json',
                data: JSON.stringify(attachmentData),
                success: function(response) {
                    showMessage(response.message, 'success');
                    loadCardDetailsInModal(cardId);
                },
                error: function(xhr, status, error) {
                    console.error("Erro ao adicionar anexo:", error);
                    showMessage("Erro ao adicionar anexo.", 'danger');
                }
            });
        }
    });

    // Evento: Excluir Anexo
    $(document).on('click', '.delete-attachment-btn', function() {
        const attachmentId = $(this).data('id');
        const cardId = $(this).closest('.modal-body').find('#card-detail-title').closest('.modal-body').find('.add-checklist-btn').data('card-id'); // Gambiarra para pegar o cardId
        if (confirm('Tem certeza que deseja excluir este anexo?')) {
            const deleteData = { ID_Anexo: attachmentId };
            console.log("LOG API CALL: Excluindo anexo. URL: api/anexos.php, Método: DELETE, Dados:", deleteData); // Log da chamada de API
            $.ajax({
                url: 'api/anexos.php',
                method: 'DELETE',
                contentType: 'application/json',
                data: JSON.stringify(deleteData),
                success: function(response) {
                    showMessage(response.message, 'success');
                    loadCardDetailsInModal(cardId);
                },
                error: function(xhr, status, error) {
                    console.error("Erro ao excluir anexo:", error);
                    showMessage("Erro ao excluir anexo.", 'danger');
                }
            });
        }
    });

    // Evento: Postar Comentário
    $(document).on('click', '#post-comment-btn', function() {
        const cardId = $(this).data('card-id');
        const commentContent = $('#new-comment-content').val();
        // Para fins de teste, usaremos um fk_Usuario_ID_Usuario fixo (ID 1 - Alice Smith)
        // Em um app real, isso viria da sessão do usuário logado.
        const userId = 1;

        if (commentContent) {
            const commentData = {
                Conteudo: commentContent,
                Dt_Criacao: new Date().toISOString().slice(0, 19).replace('T', ' '), // Formato YYYY-MM-DD HH:MM:SS
                fk_Usuario_ID_Usuario: userId,
                fk_Cartao_ID_Cartao: cardId
            };
            console.log("LOG API CALL: Postando comentário. URL: api/comentarios.php, Método: POST, Dados:", commentData); // Log da chamada de API
            $.ajax({
                url: 'api/comentarios.php',
                method: 'POST',
                contentType: 'application/json',
                data: JSON.stringify(commentData),
                success: function(response) {
                    showMessage(response.message, 'success');
                    $('#new-comment-content').val('');
                    loadCardDetailsInModal(cardId); // Recarrega para mostrar o novo comentário
                },
                error: function(xhr, status, error) {
                    console.error("Erro ao postar comentário:", error);
                    showMessage("Erro ao postar comentário.", 'danger');
                }
            });
        } else {
            showMessage("O comentário não pode estar vazio.", 'warning');
        }
    });

    // Evento: Excluir Comentário
    $(document).on('click', '.delete-comment-btn', function() {
        const commentId = $(this).data('id');
        const cardId = $(this).closest('.modal-body').find('#card-detail-title').closest('.modal-body').find('.add-checklist-btn').data('card-id'); // Gambiarra para pegar o cardId

        if (confirm('Tem certeza que deseja excluir este comentário?')) {
            const deleteData = { ID_Comentario: commentId };
            console.log("LOG API CALL: Excluindo comentário. URL: api/comentarios.php, Método: DELETE, Dados:", deleteData); // Log da chamada de API
            $.ajax({
                url: 'api/comentarios.php',
                method: 'DELETE',
                contentType: 'application/json',
                data: JSON.stringify(deleteData),
                success: function(response) {
                    showMessage(response.message, 'success');
                    loadCardDetailsInModal(cardId);
                },
                error: function(xhr, status, error) {
                    console.error("Erro ao excluir comentário:", error);
                    showMessage("Erro ao excluir comentário.", 'danger');
                }
            });
        }
    });

});