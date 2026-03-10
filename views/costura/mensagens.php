<div class="conteudo flex">
    <?php require VIEWS_PATH . 'shared/sidebar.php'; ?>
    
    <div class="conteudo-tabela">
        <div class="flex" style="justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h2>Minhas Notificações</h2>
            <?php if (isset($_SESSION['sucesso'])): ?>
                <span class="success-message">
                    <i class="fas fa-check-circle"></i> <?= $_SESSION['sucesso'] ?>
                </span>
                <?php unset($_SESSION['sucesso']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['erro'])): ?>
                <span class="error-message">
                    <i class="fas fa-times-circle"></i> <?= $_SESSION['erro'] ?>
                </span>
                <?php unset($_SESSION['erro']); ?>
            <?php endif; ?>
        </div>

        <!-- Card de notificações -->
        <div class="formulario-cadastro" style="padding: 0; overflow: hidden;">
            <div class="card-header" style="padding: 15px 20px; background-color: #f8f9fa; border-bottom: 1px solid #dee2e6;">
                <div class="flex" style="justify-content: space-between; align-items: center;">
                    <h3 style="margin: 0;">Central de Notificações</h3>
                    <form id="deleteForm" method="POST" action="<?= BASE_URL ?>notificacoes/excluir">
                        <button type="submit" class="botao-remover" onclick="return confirmarExclusao()">
                            <i class="fas fa-trash-alt"></i> Excluir selecionadas
                        </button>
                </div>
            </div>

            <div class="card-body" style="padding: 20px;">
                <?php if (empty($notificacoesUsuario)): ?>
                    <div class="no-data" style="text-align: center; padding: 50px;">
                        <i class="fas fa-bell" style="font-size: 48px; color: #ccc; margin-bottom: 15px;"></i>
                        <p style="color: #6c757d; font-size: 1.1em;">Nenhuma notificação encontrada.</p>
                    </div>
                <?php else: ?>
                    <div class="tabela">
                        <table cellspacing='0' class="redondinho shadow" id="tabela-notificacoes">
                            <thead>
                                <tr>
                                    <th width="5%" class="ac">
                                        <input type="checkbox" id="selectAllCheckbox" style="cursor: pointer;">
                                    </th>
                                    <th width="20%" class="ae">Data</th>
                                    <th width="70%" class="ae">Mensagem</th>
                                    <th width="5%" class="ac">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($notificacoesUsuario as $notificacao): ?>
                                    <tr class="<?= empty($notificacao['visualizada']) ? 'notificacao-nao-lida' : '' ?>">
                                        <td class="ac">
                                            <input type="checkbox" class="notification-checkbox" 
                                                   name="notificacoes_ids[]" 
                                                   value="<?= $notificacao['id'] ?>" 
                                                   style="cursor: pointer;">
                                        </td>
                                        <td class="ae">
                                            <span class="data-notificacao">
                                                <?= date('d/m/Y H:i', strtotime($notificacao['data_criacao'])) ?>
                                            </span>
                                        </td>
                                        <td class="ae">
                                            <div class="mensagem-notificacao">
                                                <?= htmlspecialchars($notificacao['mensagem']) ?>
                                            </div>
                                        </td>
                                        <td class="ac">
                                            <?php if (empty($notificacao['visualizada'])): ?>
                                                <span class="status-badge in-progress" style="font-size: 0.8em;">Nova</span>
                                            <?php else: ?>
                                                <span class="status-badge completed" style="font-size: 0.8em;">Lida</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
function confirmarExclusao() {
    const checkboxes = document.querySelectorAll('.notification-checkbox:checked');
    
    if (checkboxes.length === 0) {
        alert('Selecione pelo menos uma notificação para excluir.');
        return false;
    }
    
    return confirm(`Tem certeza que deseja excluir as ${checkboxes.length} notificação(ões) selecionada(s)?`);
}

document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('selectAllCheckbox');
    const checkboxes = document.querySelectorAll('.notification-checkbox');
    
    if (selectAllCheckbox) {
        // Selecionar/deselecionar todos
        selectAllCheckbox.addEventListener('change', function() {
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });
        
        // Atualizar checkbox "selecionar todos" quando individuais mudam
        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const allChecked = Array.from(checkboxes).every(cb => cb.checked);
                selectAllCheckbox.checked = allChecked;
            });
        });
    }
});
</script>