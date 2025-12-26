<div class="container py-5">
    <div class="row justify-content-center align-items-center" style="min-height: 80vh;">
        <div class="col-md-6 col-lg-4">
            
            <!-- Cabeçalho -->
            <div class="text-center mb-4">
                <div class="bg-white p-3 rounded-circle shadow-sm d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                    <?php echo $this->Html->image('logo-dev.png', array('style' => 'width: 50px; height: auto;', 'alt' => 'Logo')); ?>
                </div>
                <h3 class="fw-bold text-dark">Recuperar Acesso</h3>
                <p class="text-muted small">Informe seu e-mail para continuar.</p>
            </div>

            <!-- Cartão de Recuperação -->
            <div class="card shadow-lg border-0 rounded-4">
                <div class="card-body p-4">
                    
                    <?php echo $this->Flash->render(); ?>

                    <?php echo $this->Form->create('User'); ?>
                        
                        <div class="mb-4">
                            <label class="form-label fw-bold small text-uppercase text-secondary">E-mail Cadastrado</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-envelope-fill"></i></span>
                                <?php echo $this->Form->input('email', array(
                                    'label' => false,
                                    'class' => 'form-control bg-light border-start-0',
                                    'placeholder' => 'seu@email.com',
                                    'type' => 'email',
                                    'required' => true,
                                    'autofocus' => true
                                )); ?>
                            </div>
                        </div>

                        <div class="d-grid gap-2 mb-3">
                            <?php echo $this->Form->button('Enviar Link de Recuperação', array('class' => 'btn btn-primary btn-lg fw-bold shadow-sm')); ?>
                        </div>

                    <?php echo $this->Form->end(); ?>
                </div>
                
                <div class="card-footer bg-light border-0 text-center py-3 rounded-bottom-4">
                    <p class="mb-0 text-muted small">Lembrou a senha? 
                        <?php echo $this->Html->link('Voltar para o Login', array('controller' => 'users', 'action' => 'login'), array('class' => 'text-primary fw-bold text-decoration-none')); ?>
                    </p>
                </div>
            </div>

        </div>
    </div>
</div>

<style>
    /* Mantendo a identidade visual roxa */
    .btn-primary { background-color: #5e3573; border-color: #5e3573; }
    .btn-primary:hover { background-color: #4a2a5a; border-color: #4a2a5a; }
    .form-control:focus { border-color: #5e3573; box-shadow: 0 0 0 0.25rem rgba(94, 53, 115, 0.25); }
    .input-group-text { border-color: #ced4da; }
    .input-group:focus-within .input-group-text, .input-group:focus-within .btn-light { border-color: #5e3573; }
</style>