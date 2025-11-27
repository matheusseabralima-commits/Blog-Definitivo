<div class="card card-login"> 
    <!--
  esse a página é para adicionar novos usuários
-->   
    <div class="login-header">
        <h3 class="fw-bold mb-1">Criar Conta</h3>
        <small class="opacity-75">Junte-se ao Manual do Dev PHP</small>
    </div>

    <div class="card-body p-4 pt-4">
        
        <?php echo $this->Flash->render(); ?>

        <?php echo $this->Form->create('User', array(
            'url' => array('action' => 'add'),
            'class' => 'needs-validation'
        )); ?>

        <!-- USUÁRIO -->
        <div class="mb-3">
            <label class="form-label fw-bold small text-secondary">USUÁRIO</label>
            <div class="input-group">
                <span class="input-group-text bg-light"><i class="bi bi-person-plus-fill"></i></span>
                <?php echo $this->Form->input('username', array(
                    'label' => false, 'div' => false,
                    'class' => 'form-control', 
                    'placeholder' => 'Escolha um usuário', 
                    'autofocus' => true
                )); ?>
            </div>
        </div>

        <!-- SENHA COM OLHO -->
        <div class="mb-3">
            <label class="form-label fw-bold small text-secondary">SENHA</label>
            <div class="input-group">
                <span class="input-group-text bg-light"><i class="bi bi-lock-fill"></i></span>
                <?php 
                    echo $this->Form->input('password', array(
                        'label' => false, 'div' => false,
                        'class' => 'form-control', 
                        'placeholder' => 'Defina sua senha', 
                        'type' => 'password',
                        'id' => 'UserPasswordReg' // ID diferente do login
                    )); 
                ?>
                <button type="button" class="btn btn-toggle-password" onclick="togglePassword('UserPasswordReg', 'iconAdd')">
                    <i class="bi bi-eye" id="iconAdd"></i>
                </button>
            </div>
        </div>

        <!-- PERFIL -->
        <div class="mb-4">
            <label class="form-label fw-bold small text-secondary">PERFIL</label>
            <div class="input-group">
                <span class="input-group-text bg-light"><i class="bi bi-shield-check"></i></span>
                <?php echo $this->Form->input('role', array(
                    'label' => false, 'div' => false,
                    'class' => 'form-select',
                    'options' => array('author' => 'Autor', 'admin' => 'Admin')
                )); ?>
            </div>
        </div>

        <!-- BOTÃO CADASTRAR (ROXO FORÇADO) -->
        <div class="d-grid gap-2 mb-3 mt-4">
            <button type="submit" class="btn" style="
                background-color: #5e3573 !important; 
                color: #ffffff !important; 
                border-radius: 50px !important; 
                padding: 12px 0; 
                font-weight: bold; 
                text-transform: uppercase; 
                width: 100%;
                border: none;
                box-shadow: 0 4px 10px rgba(94, 53, 115, 0.4);
            ">
                CADASTRAR
            </button>
        </div>

        <?php echo $this->Form->end(); ?>

    </div>

    <div class="card-footer bg-light text-center p-3 border-top-0">
        <small class="text-muted d-block mb-2">Já possui uma conta?</small>
        
        <?php 
            echo $this->Html->link(
                '<i class="bi bi-arrow-left"></i> Voltar para o Login', 
                array('action' => 'login'), 
                array(
                    'class' => 'btn btn-outline-secondary btn-sm w-100 fw-bold', 
                    'style' => 'border-radius: 20px;', 
                    'escape' => false
                )
            ); 
        ?>
    </div>
</div>