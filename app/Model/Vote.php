<?php
App::uses('AppModel', 'Model');

class Vote extends AppModel {

    public $name = 'Vote';

    public $belongsTo = array(
        'Post' => array(
            'className' => 'Post',
            'foreignKey' => 'post_id'
        ),
        'User' => array(
            'className' => 'User',
            'foreignKey' => 'user_id'
        )
    );

    // Garante que um usuário só pode votar uma vez por post
    public $validate = array(
        'user_id' => array(
            'unique' => array(
                'rule' => array('validateUniqueVote'),
                'message' => 'Você já votou neste post.',
                'on' => 'create' // Aplica apenas na criação
            )
        )
    );
    
    // Regra de validação customizada para evitar votos duplicados
    public function validateUniqueVote($check) {
        $existingVote = $this->find('count', array(
            'conditions' => array(
                'Vote.user_id' => $this->data['Vote']['user_id'],
                'Vote.post_id' => $this->data['Vote']['post_id']
            )
        ));
        return $existingVote == 0;
    }
}