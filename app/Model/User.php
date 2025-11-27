<?php
App::uses('AppModel', 'Model');
App::uses('AuthComponent', 'Controller/Component');

class User extends AppModel {
    
    public $validate = array(
        'username' => array(
            // REGRA 1: Não pode ser vazio
            'required' => array(
                'rule' => 'notBlank',
                'message' => 'Um nome de usuário é obrigatório'
            ),
            // REGRA 2: Verifica se já existe no banco (Adicionado Agora)
            'isUnique' => array(
                'rule' => 'isUnique',
                'message' => 'Já existe um Usuário com esse nome',
                'on' => 'create' // Opcional: valida principalmente na criação
            )
        ),
        'password' => array(
            'required' => array(
                'rule' => 'notBlank',
                'message' => 'Uma senha é obrigatória'
            )
        ),
        'role' => array(
            'valid' => array(
                'rule' => array('inList', array('admin', 'author')),
                'message' => 'Por favor, insira uma função válida',
                'allowEmpty' => false
            )
        )
    );

    // Hash da senha ANTES de salvar no banco de dados
    public function beforeSave($options = array()) {
        if (isset($this->data[$this->alias]['password'])) {
            $this->data[$this->alias]['password'] = AuthComponent::password($this->data[$this->alias]['password']);
        }
        return true;
    }

    // Garantir que apague os posts quando o usuário for deletado
    public $hasMany = array(
        'Post' => array(
            'className' => 'Post',
            'foreignKey' => 'user_id',
            'dependent' => true // Garante a limpeza em cascata
        ),
    );
}