<?php
App::uses('AppModel', 'Model');

class Post extends AppModel {

    public $name = 'Post';

    /**
     * CORREÇÃO 1: "Ligar" o Containable
     * Isso faz o Model "obedecer" o array 'contain' que você definiu no Controller.
     * Sem isso, o 'contain' é ignorado, e $post['Comment'] nunca existirá.
     */
    public $actsAs = array('Containable');

    /**
     * CORREÇÃO 2: Regras de Validação
     * Um model completo deve ter regras de validação para
     * garantir que os dados sejam salvos corretamente.
     */
    public $validate = array(
        'title' => array(
            'rule' => 'notBlank', // 'notEmpty' no Cake 2.x mais antigo
            'message' => 'Um título é obrigatório.'
        ),
        'body' => array(
            'rule' => 'notBlank',
            'message' => 'O corpo do post não pode ficar em branco.'
        )
    );

    // Associa o Post ao User (um Post pertence a um User)
    // (Esta parte já estava correta)
    public $belongsTo = array(
        'User' => array(
            'className' => 'User',
            'foreignKey' => 'user_id'
        )
    );

    /**
     * CORREÇÃO 3: Associações 'hasMany' (O Post POSSUI Muitos...)
     * Você precisa definir que um Post "tem muitos" Comentários, Votos e Anexos.
     * Isso é essencial para o 'contain' funcionar.
     */
    public $hasMany = array(
        'Comment' => array(
            'className' => 'Comment',
            'foreignKey' => 'post_id',
            'dependent' => true // Deleta comentários se o post for deletado
        ),
        'Attachment' => array(
            'className' => 'Attachment',
            'foreignKey' => 'post_id',
            'dependent' => true // Deleta anexos se o post for deletado
        ),
        'Vote' => array(
            'className' => 'Vote',
            'foreignKey' => 'post_id',
            'dependent' => true // Deleta votos se o post for deletado
        )
    );

    /**
     * CORREÇÃO 4: Erro de Sintaxe (Faltava 'function')
     * Métodos de classe em PHP precisam da palavra-chave 'function'.
     */
    // Método de segurança: Verifica se o post pertence ao usuário
    public function isOwnedBy($post, $user) {
        return $this->field('id', array('id' => $post, 'user_id' => $user)) !== false;
    }

    /**
     * CORREÇÃO 5: Erro de Sintaxe (Faltava 'function')
     */
    // Método para montar as condições de filtro
    public function filterConditions($data) {
        $conditions = array();

        // 1. FILTRO POR TÍTULO OU CONTEÚDO
        if (!empty($data['Post']['TituloOuConteudo'])) {
            $searchTerm = '%' . $data['Post']['TituloOuConteudo'] . '%';
            $conditions['OR'] = array(
                'Post.title LIKE' => $searchTerm,
                'Post.body LIKE' => $searchTerm
            );
        }

        // 2. FILTRO POR STATUS
        if (!empty($data['Post']['Status'])) {
            $conditions['Post.status'] = $data['Post']['Status'];
        }

        // 3. FILTRO POR DATA 'DE'
        if (
            !empty($data['Post']['De']['year']) && 
            !empty($data['Post']['De']['month']) && 
            !empty($data['Post']['De']['day'])
        ) {
            // Monta a data inicial (início do dia)
            $dateFrom = $data['Post']['De']['year'] . '-' . $data['Post']['De']['month'] . '-' . $data['Post']['De']['day'] . ' 00:00:00';
            $conditions['Post.created >='] = $dateFrom;
        }

        // 4. FILTRO POR DATA 'ATÉ'
        if (
            !empty($data['Post']['Ate']['year']) && 
            !empty($data['Post']['Ate']['month']) && 
            !empty($data['Post']['Ate']['day'])
        ) {
            // Monta a data final (final do dia)
            $dateTo = $data['Post']['Ate']['year'] . '-' . $data['Post']['Ate']['month'] . '-' . $data['Post']['Ate']['day'] . ' 23:59:59';
            $conditions['Post.created <='] = $dateTo;
        }

        return $conditions;
    }
}