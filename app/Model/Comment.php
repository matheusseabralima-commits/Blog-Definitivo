<?php
App::uses('AppModel', 'Model');

class Comment extends AppModel {

    public $name = 'Comment';

    // Para "ligar" o Containable, caso precisemos dele
    public $actsAs = array('Containable');

    // Regras de validação
    public $validate = array(
        'body' => array(
            'rule' => 'notBlank',
            'message' => 'O comentário não pode ficar em branco.'
        )
    );

    // Define a quem o Comentário pertence
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
}