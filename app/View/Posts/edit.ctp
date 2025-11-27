<h1>Editar Post</h1>
<?php
    // Inicia o formulário
    echo $this->Form->create('Post');

    // 1. Campo Título
    echo '<div class="form-group">';
    echo $this->Form->input('title', array(
        'label' => 'Título',
        'class' => 'form-control' // Deixa o campo bonito e esticado
    ));
    echo '</div><br>';

    // 2. Campo Conteúdo
    echo '<div class="form-group">';
    echo $this->Form->input('body', array(
        'rows' => '5', // Aumentei um pouco para ficar melhor de escrever
        'label' => 'Conteúdo',
        'class' => 'form-control'
    ));
    echo '</div><br>';

    // 3. NOVO CAMPO: Status (Rascunho ou Publicado)
    echo '<div class="form-group">';
    echo $this->Form->input('status', array(
        'label' => 'Status da Publicação',
        'options' => array(
            'published' => 'Publicado',
            'draft'     => 'Rascunho'
        ),
        'class' => 'form-control',
        'empty' => false // Impede que fique uma opção vazia selecionada
    )); 
    echo '</div><br>';
    
    // 4. Campo 'id' oculto (Essencial para edição)
    echo $this->Form->input('id', array('type' => 'hidden')); 
    
    // 5. Botão Salvar
    // Troquei para o helper de button para podermos por estilo nele também
    echo $this->Form->button('Salvar Alterações', array(
        'type' => 'submit', 
        'class' => 'btn btn-primary' // Botão azul padrão
    ));
    
    echo $this->Form->end();
?>