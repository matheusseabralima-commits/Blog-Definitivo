<div class="card card-login"> 
     <!-- PÁGINA DE LOGIN DO USUÁRIO -->
    <div class="login-header">
        <h3 class="fw-bold mb-1">Bem-vindo</h3>
        <small class="opacity-75">Manual do Dev PHP</small>
    </div>

    <div class="card-body p-4 pt-5">
        
        <?php if ($this->Session->check('Message.auth')): ?>
            <div class="alert alert-danger d-flex align-items-center mb-4" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <div><?php echo $this->Session->flash('auth'); ?></div>
            </div>
        <?php endif; ?>
        
        <?php echo $this->Flash->render(); ?>

        <?php echo $this->Form->create('User', array('url' => array('action' => 'login'))); ?>

        <!-- USUÁRIO -->
        <div class="mb-3">
            <label class="form-label fw-bold small text-secondary">USUÁRIO</label>
            <div class="input-group">
                <span class="input-group-text bg-light"><i class="bi bi-person-fill"></i></span>
                <?php echo $this->Form->input('username', array(
                    'label' => false, 'div' => false,
                    'class' => 'form-control', 
                    'placeholder' => 'Digite seu usuário', 
                    'autofocus' => true
                )); ?>
            </div>
        </div>

        <!-- SENHA COM OLHO -->
        <div class="mb-4">
            <label class="form-label fw-bold small text-secondary">SENHA</label>
            <div class="input-group">
                <span class="input-group-text bg-light"><i class="bi bi-lock-fill"></i></span>
                <?php 
                    echo $this->Form->input('password', array(
                        'label' => false, 'div' => false,
                        'class' => 'form-control', 
                        'placeholder' => '••••••••', 
                        'type' => 'password',
                        'id' => 'UserPassword' // ID para o JS
                    )); 
                ?>
                <button type="button" class="btn btn-toggle-password" onclick="togglePassword('UserPassword', 'iconLogin')">
                    <i class="bi bi-eye" id="iconLogin"></i>
                </button>
            </div>
        </div>

        <!-- BOTÃO ACESSAR (ROXO FORÇADO) -->
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
                ACESSAR
            </button>
        </div>

        <?php echo $this->Form->end(); ?>

    </div>

    <div class="card-footer bg-light text-center p-3 border-top-0">
        <small class="text-muted">Não possui acesso?</small><br>
        <?php echo $this->Html->link('Criar nova conta', 
            array('action' => 'add'), 
            array('class' => 'text-decoration-none fw-bold', 'style' => 'color: #5e3573;')
        ); ?>
    </div>
</div>