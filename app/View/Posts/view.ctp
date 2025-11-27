<?php 
// Define um título melhor para a aba do navegador
$this->assign('title', h($post['Post']['title'])); 
?>

<div class="row">
    <!-- Coluna Principal do Post -->
    <div class="col-lg-8">
        
        <!-- Post -->
        <article class="card shadow-sm mb-4">
            <div class="card-body">
                <!-- Título do Post -->
                <h1 class="card-title"><?php echo h($post['Post']['title']); ?></h1>

                <!-- Informações (Autor e Data) -->
                <p class="text-muted">
                    Escrito por 
                    <strong><?php echo h($post['User']['username']); ?></strong>
                    em <?php echo $this->Time->format('d/m/Y \à\s H:i', $post['Post']['created']); ?>
                </p>

                <!-- Anexo (Imagem), se existir -->
                <?php if (!empty($post['Attachment'])): ?>
                    <div class="mb-3 text-center">
                        <?php 
                        // Pega o primeiro anexo
                        $attachment = $post['Attachment'][0];
                        // Exibe a imagem usando o caminho salvo
                        echo $this->Html->image($attachment['path'], array(
                            'alt' => h($post['Post']['title']),
                            'class' => 'img-fluid rounded'
                        ));
                        ?>
                    </div>
                <?php endif; ?>

                <!-- Corpo do Post -->
                <div class="post-body mt-4">
                    <?php 
                    // Usamos 'nl2br' para preservar as quebras de linha (Enter)
                    echo nl2br(h($post['Post']['body'])); 
                    ?>
                </div>
            </div>
        </article>

        <!-- Seção de Comentários -->
        <section class="card shadow-sm mb-4">
            <div class="card-body">
                <h3 class="card-title">Comentários (<?php echo count($post['Comment']); ?>)</h3>
                
                <hr>

                <!-- 1. Lista os comentários que já existem (Estilo Cartão) -->
                <?php if (empty($post['Comment'])): ?>
                    <p class="text-muted">Nenhum comentário ainda. Seja o primeiro a comentar!</p>
                <?php else: ?>
                    <?php foreach ($post['Comment'] as $comment): ?>
                        <div class="card mb-3"> <!-- Usando card -->
                            <div class="card-body">
                                <!-- Adicionado nl2br para quebrar linhas -->
                                <p class="card-text"><?php echo nl2br(h($comment['body'])); ?></p>
                                <footer class="blockquote-footer"> <!-- Usando blockquote-footer -->
                                    Por 
                                    <strong><?php echo h($comment['User']['username']); ?></strong>
                                    <!-- Usando o Time Helper para formatar -->
                                    em <?php echo $this->Time->format('d/m/Y H:i', $comment['created']); ?>
                                </footer>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>

                <!-- 2. Formulário para Adicionar Comentário -->
                <?php if ($this->Session->read('Auth.User')): // Só mostra o form se estiver logado ?>
                    <div class="mt-4">
                        <h4>Adicionar Comentário</h4>
                        <?php 
                        // Corrigido: Removido 'action' duplicado. Apenas 'url' é necessário.
                        echo $this->Form->create('Comment', array(
                            'url' => array('controller' => 'comments', 'action' => 'add')
                        )); 
                        ?>
                        
                        <?php
                        // Campo oculto para enviar o post_id
                        echo $this->Form->hidden('post_id', array('value' => $post['Post']['id']));
                        
                        echo $this->Form->input('body', array(
                            'label' => 'Seu Comentário',
                            'class' => 'form-control',
                            'rows' => 3
                        ));
                        ?>
                        <div class="mt-2">
                            <?php 
                            // Atalho para fechar o form e criar o botão
                            echo $this->Form->end(array('label' => 'Comentar', 'class' => 'btn btn-primary')); 
                            ?>
                        </div>
                    </div>
                <?php else: // Se não estiver logado ?>
                    <p class="mt-4">
                        <?php 
                        // Corrigido: Usando o Html Helper para o link de login
                        echo $this->Html->link('Faça login', array('controller' => 'users', 'action' => 'login')); 
                        ?>
                         para comentar.
                    </p>
                <?php endif; ?>

            </div>
        </section>

    </div>

    <!-- Barra Lateral (se houver) -->
    <div class="col-lg-4">
        <!-- (Você pode adicionar um 'Element' de sidebar aqui) -->
    </div>
</div>