<div class="row">
    <div class="col-md-10 offset-md-1 col-lg-8 offset-lg-2">

        <h2 class="mb-4">Adicionar Novo Post</h2>

        <?php 
        // Habilita o formulário para aceitar upload de arquivos (type => file)
        echo $this->Form->create('Post', array('type' => 'file')); 
        ?>
        
        <fieldset class="p-3 card shadow-sm">
            <legend>Informações Principais</legend>

            <?php 
            echo $this->Form->input('title', array(
                'label' => 'Título',
                'class' => 'form-control',
                'placeholder' => 'Título do seu post'
            )); 
            ?>

            <?php 
            echo $this->Form->input('body', array(
                'label' => 'Conteúdo',
                'rows' => '8',
                'class' => 'form-control',
                'placeholder' => 'Escreva o conteúdo completo do seu post aqui...'
            )); 
            ?>
            
            <?php 
            $statusOptions = array(
                'rascunho' => 'Rascunho',
                'publicado' => 'Publicado'
            );
            echo $this->Form->input('status', array(
                'label' => 'Status da Publicação',
                'options' => $statusOptions,
                'class' => 'form-control'
            )); 
            ?>
        </fieldset>
        
        <fieldset class="mt-4 p-3 card shadow-sm">
            <legend>Anexo (Opcional)</legend>
            <div class="form-group">
                <label for="AttachmentFile">Imagem Principal (Max 5MB)</label>
                <?php 
                // Campo de upload de arquivo
                echo $this->Form->input('Attachment.file', array(
                    'type' => 'file',
                    'label' => false,
                    'div' => false,
                    'class' => 'form-control-file',
                    
                    // --- CORREÇÃO AQUI ---
                    // Isso remove a validação "required" do HTML5 no navegador.
                    'required' => false 
                )); 
                ?>
                <small class="form-text text-muted">Apenas JPG, PNG e GIF são aceitos. A imagem será anexada ao post.</small>
            </div>
        </fieldset>

        <div class="d-flex justify-content-between mt-4">
            <?php echo $this->Form->submit('Salvar Post', array('class' => 'btn btn-success')); ?>
            
            <?php echo $this->Html->link('Cancelar', array('action' => 'index'), array('class' => 'btn btn-secondary')); ?>
        </div>

        <?php echo $this->Form->end(); ?>
    </div>
</div>