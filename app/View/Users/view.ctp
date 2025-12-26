<?php 
// Define o título da aba do navegador
$this->assign('title', 'Perfil de ' . h($user['User']['username'])); 

// --- LÓGICA DE PERMISSÕES (Admin) ---
$usuarioLogado = $this->Session->read('Auth.User');
$isAdmin = isset($usuarioLogado['role']) && $usuarioLogado['role'] === 'admin';

// Verifica status do perfil visualizado
$isActive = !isset($user['User']['active']) || $user['User']['active'] == 1;
?>

<div class="container mt-4">
    <div class="row">
        
        <!-- COLUNA DA ESQUERDA: CARTÃO DE PERFIL -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow-sm border-0 text-center p-4">
                
                <!-- Avatar Grande -->
                <div class="mx-auto mb-3 bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 120px; height: 120px; font-size: 3rem; font-weight: bold;">
                    <?php echo strtoupper(substr($user['User']['username'], 0, 1)); ?>
                </div>

                <h2 class="card-title fw-bold mb-1">
                    <?php echo h($user['User']['username']); ?>
                </h2>
                
                <div class="mb-3">
                    <?php 
                        $roleClass = ($user['User']['role'] === 'admin') ? 'bg-danger' : 'bg-info text-dark';
                    ?>
                    <span class="badge rounded-pill <?php echo $roleClass; ?>">
                        <?php echo h(ucfirst($user['User']['role'])); ?>
                    </span>

                    <?php if (!$isActive): ?>
                        <div class="mt-2">
                            <span class="badge bg-warning text-dark border border-warning">
                                <i class="fas fa-ban"></i> CONTA INATIVA
                            </span>
                        </div>
                    <?php endif; ?>
                </div>

                <p class="text-muted small">
                    Membro desde <?php echo $this->Time->format('d/m/Y', $user['User']['created']); ?>
                </p>

                <!-- Estatística Rápida -->
                <div class="border-top pt-3 mt-3">
                    <h5 class="fw-bold mb-0"><?php echo count($user['Post']); ?></h5>
                    <small class="text-muted text-uppercase">Posts Publicados</small>
                </div>

                <!-- --- ÁREA EXCLUSIVA DE ADMINISTRAÇÃO --- -->
                <?php if ($isAdmin): ?>
                    <div class="mt-4 p-3 bg-light rounded border">
                        <h6 class="fw-bold text-dark mb-3">Administração</h6>
                        
                        <div class="d-grid gap-2">
                            <?php 
                            // 1. Botão Editar
                            echo $this->Html->link('<i class="bi bi-pencil-square"></i> Editar Perfil', 
                                array('controller' => 'users', 'action' => 'edit', $user['User']['slug']), 
                                array('class' => 'btn btn-outline-primary btn-sm', 'escape' => false)
                            ); 
                            ?>

                            <?php 
                            // 2. Botão Desativar/Reativar (Soft Delete)
                            // Regra: Admin não pode desativar a si mesmo nem outro Admin
                            $isSelf = ($user['User']['id'] == $usuarioLogado['id']);
                            $isTargetAdmin = ($user['User']['role'] === 'admin');
                            
                            if (!$isSelf && !$isTargetAdmin):
                                $btnLabel = $isActive ? '<i class="bi bi-person-x"></i> Desativar Usuário' : '<i class="bi bi-person-check"></i> Reativar Usuário';
                                $btnClass = $isActive ? 'btn-outline-danger' : 'btn-outline-success';
                                $confirmMsg = $isActive 
                                    ? 'ATENÇÃO: Desativar este usuário impedirá o login dele. Continuar?' 
                                    : 'Deseja REATIVAR o acesso deste usuário?';
                                
                                echo $this->Form->postLink($btnLabel, 
                                    array('controller' => 'users', 'action' => 'delete', $user['User']['slug']),
                                    array(
                                        'class' => 'btn btn-sm ' . $btnClass, 
                                        'escape' => false,
                                        'confirm' => $confirmMsg
                                    )
                                );
                            endif;
                            ?>
                        </div>
                    </div>
                <?php endif; ?>
                <!-- --------------------------------------- -->

                <!-- Botão Voltar -->
                <div class="mt-4">
                    <?php 
                    // Se veio da lista de usuários, volta pra lá. Senão, volta pros posts.
                    $referer = $this->request->referer();
                    $controllerDestino = (strpos($referer, 'users') !== false) ? 'users' : 'posts';
                    $textoVoltar = ($controllerDestino == 'users') ? 'Voltar para Usuários' : 'Voltar para Posts';

                    echo $this->Html->link('← ' . $textoVoltar, 
                        array('controller' => $controllerDestino, 'action' => 'index'), 
                        array('class' => 'btn btn-link text-secondary text-decoration-none btn-sm')
                    ); 
                    ?>
                </div>

            </div>
        </div>

        <!-- COLUNA DA DIREITA: LISTA DE POSTS DO AUTOR -->
        <div class="col-lg-8">
            <h3 class="border-bottom pb-2 mb-4">Publicações Recentes</h3>

            <?php if (!empty($user['Post'])): ?>
                <div class="list-group shadow-sm">
                    <?php foreach ($user['Post'] as $post): ?>
                        
                        <div class="list-group-item list-group-item-action p-4 border-0 border-bottom">
                            <div class="d-flex w-100 justify-content-between mb-2">
                                <h5 class="mb-1 fw-bold text-primary">
                                    <?php 
                                    // Link usando SLUG do post
                                    $slugPost = !empty($post['slug']) ? $post['slug'] : $post['id'];
                                    
                                    echo $this->Html->link($post['title'], 
                                        array('controller' => 'posts', 'action' => 'view', $slugPost),
                                        array('class' => 'text-decoration-none')
                                    ); 
                                    ?>
                                </h5>
                                <small class="text-muted">
                                    <?php echo $this->Time->timeAgoInWords($post['created']); ?>
                                </small>
                            </div>
                            
                            <p class="mb-1 text-secondary">
                                <?php echo $this->Text->truncate(h($post['body']), 150, array('ellipsis' => '...', 'exact' => false)); ?>
                            </p>
                            
                            <small>
                                <?php 
                                    // Status do Post
                                    $statusColor = ($post['status'] == 'publicado' || $post['status'] == 'published') ? 'text-success' : 'text-muted';
                                    echo "<span class='$statusColor fw-bold text-uppercase' style='font-size: 0.7rem;'>" . h($post['status']) . "</span>";
                                ?>
                            </small>
                        </div>

                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="alert alert-light text-center py-5 border">
                    <i class="bi bi-journal-x fs-1 text-muted mb-3 d-block"></i>
                    <p class="mb-0 text-muted">Este usuário ainda não publicou nada.</p>
                </div>
            <?php endif; ?>
        </div>

    </div>
</div>