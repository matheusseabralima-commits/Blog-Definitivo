<?php $this->assign('title', 'Configurações da Conta'); ?>

<div class="container mt-4">
    <div class="row">
        
        <!-- COLUNA DA ESQUERDA: SOBRE O SISTEMA -->
        <div class="col-md-5 mb-4">
            <div class="card border-0 shadow-sm h-100 bg-dark text-white">
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <?php echo $this->Html->image('logo-dev.png', array('style' => 'width: 60px; height: auto;', 'class' => 'mb-3')); ?>
                        <h3 class="fw-bold">Blog do Dev</h3>
                        <span class="badge bg-light text-dark">Versão 1.0</span>
                    </div>
                    
                    <h5 class="border-bottom border-secondary pb-2 mb-3">Sobre a Plataforma</h5>
                    <p class="small text-white-50" style="line-height: 1.6; text-align: justify;">
                        O "Blog do Dev" apresenta-se como um painel administrativo de gerenciamento de conteúdo (CMS) voltado para a área de tecnologia, permitindo que múltiplos usuários criem, editem, filtrem e organizem postagens sobre desenvolvimento de software. A interface é funcional e colaborativa, destacando artigos sobre temas técnicos como PHP, APIs e bancos de dados.
                    </p>
                    <p class="small text-white-50" style="line-height: 1.6; text-align: justify;">
                        Quanto à provedora, a identidade visual e os elementos presentes indicam que não se trata de uma plataforma comercial de mercado, mas sim de um sistema próprio desenvolvido sob medida. O uso de nomes de perfil informais e o design específico sugerem que a "empresa" por trás do site é provavelmente um projeto de portfólio pessoal, um trabalho acadêmico ou uma comunidade independente.
                    </p>
                </div>
            </div>
        </div>

        <!-- COLUNA DA DIREITA: FORMULÁRIO -->
        <div class="col-md-7 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom-0 pt-4 px-4">
                    <h4 class="fw-bold text-primary mb-0">Meus Dados</h4>
                </div>
                <div class="card-body p-4">
                    <?php echo $this->Flash->render(); ?>
                    
                    <?php echo $this->Form->create('User'); ?>
                    <?php echo $this->Form->input('id', array('type' => 'hidden')); ?>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Nome de Usuário</label>
                        <?php echo $this->Form->input('username', array(
                            'label' => false,
                            'class' => 'form-control bg-light',
                            'readonly' => true, // Nome não muda aqui por segurança
                            'title' => 'O nome de usuário não pode ser alterado aqui.'
                        )); ?>
                        <div class="form-text">Para mudar o nome, contate um administrador.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">E-mail de Contato</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                            <?php echo $this->Form->input('email', array(
                                'label' => false,
                                'class' => 'form-control',
                                'placeholder' => 'seu@email.com',
                                'required' => true
                            )); ?>
                        </div>
                    </div>

                    <hr class="my-4">

                    <h5 class="fw-bold text-dark mb-3">Segurança</h5>
                    
                    <div class="mb-4">
                        <label class="form-label fw-bold">Nova Senha</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-key"></i></span>
                            <?php echo $this->Form->input('password', array(
                                'label' => false,
                                'class' => 'form-control',
                                'placeholder' => 'Deixe em branco para manter a atual',
                                'required' => false,
                                'value' => '',
                                'autocomplete' => 'new-password'
                            )); ?>
                        </div>
                    </div>

                    <div class="d-grid">
                        <?php echo $this->Form->button('Salvar Alterações', array('class' => 'btn btn-primary btn-lg')); ?>
                    </div>

                    <?php echo $this->Form->end(); ?>
                </div>
            </div>
        </div>

    </div>
</div>