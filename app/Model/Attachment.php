<?php
App::uses('AppModel', 'Model');

class Attachment extends AppModel {

    public $name = 'Attachment';

    public $belongsTo = array(
        'Post' => array(
            'className' => 'Post',
            'foreignKey' => 'post_id'
        )
    );

    // Validação básica de arquivo
    public $validate = array(
        'file' => array(
            'uploadError' => array(
                'rule' => 'uploadError',
                'message' => 'Erro ao fazer upload do arquivo.',
                'required' => false
            ),
            'extension' => array(
                'rule' => array('extension', array('jpg', 'jpeg', 'png', 'gif')),
                'message' => 'Apenas arquivos JPG, PNG e GIF são permitidos.'
            ),
            'size' => array(
                'rule' => array('fileSize', '<=', '5MB'), // Limite de 5MB
                'message' => 'O tamanho máximo do arquivo é 5MB.'
            )
        )
    );

    // Método para processar o upload do arquivo (salva o arquivo e retorna o nome)
    public function saveFile($data) {
        if (!empty($data['file']['name'])) {
            $filename = time() . '_' . sanitize_filename($data['file']['name']);
            $uploadPath = WWW_ROOT . 'files' . DS . 'attachments' . DS;

            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }

            if (move_uploaded_file($data['file']['tmp_name'], $uploadPath . $filename)) {
                return $filename;
            }
        }
        return false;
    }
}

// Função auxiliar para sanitizar o nome do arquivo
function sanitize_filename($filename) {
    $info = pathinfo($filename);
    $name = preg_replace('/[^a-zA-Z0-9_-]/', '', str_replace(' ', '_', $info['filename']));
    return $name . '.' . $info['extension'];
}