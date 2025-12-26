<?php
// Obtém o usuário logado da Sessão para verificação
$usuarioLogado = $this->Session->read('Auth.User');
// Verifica se é admin
$isAdmin = isset($usuarioLogado['role']) && $usuarioLogado['role'] === 'admin';
?>

<div class="users index container-fluid">
    
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="text-white border-bottom pb-2"><?php echo __('Gerenciar Usuários'); ?></h2>
        </div>
    </div>
    
    <div class="row g-3"> <?php foreach ($users as $user): ?>
            
            <div class="col-12 col-lg-6">
                <!-- Adiciona classe opacity-75 visual se estiver inativo -->
                <div class="user-card d-flex align-items-center position-relative <?php echo empty($user['User']['active']) ? 'bg-light text-muted' : ''; ?>">
                    
                    <div class="avatar-circle flex-shrink-0" style="<?php echo empty($user['User']['active']) ? 'background-color: #ccc;' : ''; ?>">
                        <?php echo strtoupper(substr($user['User']['username'], 0, 1)); ?>
                    </div>

                    <div class="flex-grow-1">
                        <h5 class="mb-0 font-weight-bold <?php echo empty($user['User']['active']) ? 'text-muted' : 'text-dark'; ?>">
                            <?php echo h($user['User']['username']); ?>
                        </h5>
                        
                        <div class="d-flex align-items-center mt-1">
                            <!-- Badge de Função -->
                            <span class="badge-role <?php echo ($user['User']['role'] == 'admin' ? 'admin' : ''); ?>">
                                <?php echo h(ucfirst($user['User']['role'])); ?>
                            </span>

                            <!-- STATUS -->
                            <?php if (!empty($user['User']['active'])): ?>
                                <span class="badge bg-success ms-1" style="font-size: 0.7em;">Ativo</span>
                            <?php else: ?>
                                <span class="badge bg-danger ms-1" style="font-size: 0.7em;">Inativo</span>
                            <?php endif; ?>

                            <small class="text-muted ms-2">ID: <?php echo h($user['User']['id']); ?></small>
                        </div>
                    </div>

                    <?php if ($isAdmin): ?>
                        <div class="ms-3">
                            <?php 
                                $isSelf = ($user['User']['id'] == $usuarioLogado['id']);
                                $isTargetAdmin = ($user['User']['role'] === 'admin');

                                if (!$isSelf && !$isTargetAdmin): 
                            ?>
                                <!-- BOTOES DESKTOP -->
                                <div class="d-none d-md-block">
                                    <?php echo $this->Html->link('Editar', 
                                        array('action' => 'edit', $user['User']['id']), 
                                        array('class' => 'btn btn-sm btn-outline-primary me-1')
                                    ); ?>
                                    
                                    <!-- LÓGICA DO BOTÃO DE AÇÃO -->
                                    <?php if (!empty($user['User']['active'])): ?>
                                        <!-- Se ATIVO -> Mostra DESATIVAR (Vermelho) -->
                                        <?php echo $this->Form->postLink('Desativar', 
                                            array('action' => 'delete', $user['User']['id']),
                                            array(
                                                'confirm' => 'Tem certeza que deseja DESATIVAR o acesso de: ' . $user['User']['username'] . '?', 
                                                'class' => 'btn btn-sm btn-outline-danger'
                                            )
                                        ); ?>
                                    <?php else: ?>
                                        <!-- Se INATIVO -> Mostra REATIVAR (Verde) -->
                                        <?php echo $this->Form->postLink('Reativar', 
                                            array('action' => 'activate', $user['User']['id']),
                                            array(
                                                'confirm' => 'Tem certeza que deseja REATIVAR o acesso de: ' . $user['User']['username'] . '?', 
                                                'class' => 'btn btn-sm btn-outline-success'
                                            )
                                        ); ?>
                                    <?php endif; ?>
                                </div>

                                <!-- MENU MOBILE -->
                                <div class="d-md-none dropdown">
                                    <button class="btn btn-sm btn-light" type="button" data-bs-toggle="dropdown">
                                        &#8942;
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li>
                                            <?php echo $this->Html->link('Editar', array('action' => 'edit', $user['User']['id']), array('class' => 'dropdown-item')); ?>
                                        </li>
                                        <li>
                                            <?php if (!empty($user['User']['active'])): ?>
                                                <?php echo $this->Form->postLink('Desativar', 
                                                    array('action' => 'delete', $user['User']['id']), 
                                                    array('class' => 'dropdown-item text-danger', 'confirm' => 'Desativar usuário?')
                                                ); ?>
                                            <?php else: ?>
                                                <?php echo $this->Form->postLink('Reativar', 
                                                    array('action' => 'activate', $user['User']['id']), 
                                                    array('class' => 'dropdown-item text-success', 'confirm' => 'Reativar usuário?')
                                                ); ?>
                                            <?php endif; ?>
                                        </li>
                                    </ul>
                                </div>

                            <?php else: ?>
                                <span class="text-muted small fst-italic d-none d-sm-inline">(Protegido)</span>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="paging mt-4 d-flex justify-content-center">
        <nav aria-label="Page navigation">
            <ul class="pagination">
                <?php
                    // Ajuste para classes do Bootstrap 5
                    echo $this->Paginator->prev('«', array('class' => 'page-item', 'tag' => 'li'), null, array('class' => 'page-item disabled', 'tag' => 'li', 'disabledTag' => 'span'));
                    echo $this->Paginator->numbers(array('class' => 'page-item', 'tag' => 'li', 'separator' => '', 'currentClass' => 'active', 'currentTag' => 'span'));
                    echo $this->Paginator->next('»', array('class' => 'page-item', 'tag' => 'li'), null, array('class' => 'page-item disabled', 'tag' => 'li', 'disabledTag' => 'span'));
                ?>
            </ul>
        </nav>
    </div>
    
    <style>
        .badge-role {
            padding: 3px 8px;
            border-radius: 12px;
            background-color: #e9ecef;
            color: #495057;
            font-size: 0.8rem;
            font-weight: 600;
        }
        .badge-role.admin {
            background-color: #e0cffc;
            color: #5e3573;
        }
        .pagination .page-item span, .pagination .page-item a {
            display: block;
            padding: 0.5rem 0.75rem;
            text-decoration: none;
            border: 1px solid #dee2e6;
            background-color: #fff;
            color: #5e3573;
        }
        .pagination .page-item.active span {
            background-color: #5e3573;
            color: white;
            border-color: #5e3573;
        }
    </style>
</div>