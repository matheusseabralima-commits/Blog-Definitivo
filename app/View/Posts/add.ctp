<div class="row">
    <div class="col-md-10 offset-md-1 col-lg-8 offset-lg-2">

        <h2 class="mb-4">Adicionar Novo Post</h2>

        <?php 
        // Formulário simples (sem upload de arquivo)
        echo $this->Form->create('Post'); 
        ?>
        
        <fieldset class="p-3 card shadow-sm">
            <legend>Informações Principais</legend>

            <div class="mb-3">
                <?php 
                echo $this->Form->input('title', array(
                    'label' => array('text' => 'Título', 'class' => 'form-label fw-bold'),
                    'class' => 'form-control',
                    'placeholder' => 'Título do seu post'
                )); 
                ?>
            </div>

            <div class="mb-3">
                <?php 
                echo $this->Form->input('body', array(
                    'label' => array('text' => 'Conteúdo', 'class' => 'form-label fw-bold'),
                    'rows' => '8',
                    'class' => 'form-control',
                    'placeholder' => 'Escreva o conteúdo completo do seu post aqui...'
                )); 
                ?>
            </div>
            
            <div class="mb-3">
                <?php 
                $statusOptions = array(
                    'rascunho' => 'Rascunho',
                    'publicado' => 'Publicado'
                );
                echo $this->Form->input('status', array(
                    'label' => array('text' => 'Status da Publicação', 'class' => 'form-label fw-bold'),
                    'options' => $statusOptions,
                    'class' => 'form-select' // Classe correta para selects no Bootstrap
                )); 
                ?>
            </div>
        </fieldset>
        
        <div class="d-flex justify-content-between mt-4">
            <?php echo $this->Form->submit('Salvar Post', array('class' => 'btn btn-success')); ?>
            
            <?php echo $this->Html->link('Cancelar', array('action' => 'index'), array('class' => 'btn btn-secondary')); ?>
        </div>

        <?php echo $this->Form->end(); ?>
    </div>
</div>