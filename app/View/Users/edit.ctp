<div class="row">
    <div class="col-md-6 offset-md-3">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">Editar Usuário</h4>
            </div>
            <div class="card-body">
                <?php echo $this->Form->create('User'); ?>
                
                <fieldset>
                    <?php
                    // IMPORTANTE: O ID é obrigatório para o Cake saber que é um UPDATE e não um INSERT
                    echo $this->Form->input('id', array('type' => 'hidden'));
                    
                    echo $this->Form->input('username', array(
                        'label' => 'Nome de Usuário',
                        'class' => 'form-control mb-3',
                        'autocomplete' => 'off' // Evita sugestões erradas do navegador
                    ));
                    
                    // --- A Lógica da Senha ---
                    // Note que 'required' é false. 
                    // O Controller precisará tratar isso para não salvar senha vazia.
                    echo $this->Form->input('password', array(
                        'label' => 'Nova Senha (deixe em branco para manter a atual)',
                        'class' => 'form-control mb-3',
                        'required' => false,
                        'value' => '', // Força o campo a vir vazio, nunca mostre o hash!
                        'autocomplete' => 'new-password' // ESSENCIAL: Impede que o navegador cole a senha salva do admin aqui
                    ));
                    
                    // Apenas Admin pode mudar o papel de alguém e ver o status
                    $currentUser = $this->Session->read('Auth.User');
                    if (isset($currentUser['role']) && $currentUser['role'] === 'admin') {
                        
                        // --- NOVO: Mostra se o usuário está INATIVO ---
                        $isActive = isset($this->request->data['User']['active']) && $this->request->data['User']['active'] == 1;
                        if (!$isActive) {
                             echo '<div class="alert alert-danger py-2 mb-3">';
                             echo '<strong>⚠️ ATENÇÃO:</strong> Este usuário está <strong>INATIVO</strong> e não pode fazer login.';
                             echo '</div>';
                        }

                        echo $this->Form->input('role', array(
                            'label' => 'Função / Cargo',
                            'options' => array('admin' => 'Administrador', 'author' => 'Autor'),
                            'class' => 'form-control mb-4'
                        ));
                    }
                    ?>
                </fieldset>

                <div class="d-grid gap-2">
                    <?php echo $this->Form->submit('Salvar Alterações', array('class' => 'btn btn-success')); ?>
                    <?php echo $this->Html->link('Cancelar', array('action' => 'index'), array('class' => 'btn btn-secondary')); ?>
                </div>

                <?php echo $this->Form->end(); ?>
            </div>
        </div>
    </div>
</div>