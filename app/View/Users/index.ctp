<?php
// Obtém o usuário logado da Sessão para verificação
$usuarioLogado = $this->Session->read('Auth.User');
// Verifica se é admin
$isAdmin = isset($usuarioLogado['role']) && $usuarioLogado['role'] === 'admin';
?>

<div class="users index container-fluid">
    
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="text-white border-bottom pb-2"><?php echo __('Usuários'); ?></h2>
        </div>
    </div>
    
    <div class="row g-3"> <?php foreach ($users as $user): ?>
            
            <div class="col-12 col-lg-6">
                <div class="user-card d-flex align-items-center position-relative">
                    
                    <div class="avatar-circle flex-shrink-0">
                        <?php echo strtoupper(substr($user['User']['username'], 0, 1)); ?>
                    </div>

                    <div class="flex-grow-1">
                        <h5 class="mb-0 text-dark font-weight-bold">
                            <?php echo h($user['User']['username']); ?>
                        </h5>
                        
                        <div class="d-flex align-items-center mt-1">
                            <span class="badge-role <?php echo ($user['User']['role'] == 'admin' ? 'admin' : ''); ?>">
                                <?php echo h(ucfirst($user['User']['role'])); ?>
                            </span>
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
                                <div class="d-none d-md-block">
                                    <?php echo $this->Html->link('Editar', 
                                        array('action' => 'edit', $user['User']['id']), 
                                        array('class' => 'btn btn-sm btn-outline-primary me-1')
                                    ); ?>
                                    
                                    <?php echo $this->Form->postLink('Excluir', 
                                        array('action' => 'delete', $user['User']['id']),
                                        array(
                                            'confirm' => 'Tem certeza que deseja excluir: ' . $user['User']['username'] . '?', 
                                            'class' => 'btn btn-sm btn-outline-danger'
                                        )
                                    ); ?>
                                </div>

                                <div class="d-md-none dropdown">
                                    <button class="btn btn-sm btn-light" type="button" data-bs-toggle="dropdown">
                                        &#8942;
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li>
                                            <?php echo $this->Html->link('Editar', array('action' => 'edit', $user['User']['id']), array('class' => 'dropdown-item')); ?>
                                        </li>
                                        <li>
                                            <?php echo $this->Form->postLink('Excluir', array('action' => 'delete', $user['User']['id']), array('class' => 'dropdown-item text-danger', 'confirm' => 'Confirma?')); ?>
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