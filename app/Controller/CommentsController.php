<?php
App::uses('AppController', 'Controller');

class CommentsController extends AppController {

    public $helpers = array('Html', 'Form', 'Flash');
    public $components = array('Flash');

    // Apenas usuários logados podem comentar e deletar
    public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->deny('add', 'delete'); // Nega por padrão
    }

    // Define quem pode fazer o quê
    public function isAuthorized($user) {
        // Admins podem tudo
        if (isset($user['role']) && $user['role'] === 'admin') {
            return true;
        }

        // Usuários logados (qualquer role) podem adicionar comentários
        if ($this->action === 'add') {
            return true;
        }

        // Apenas o dono do comentário (ou admin) pode deletar
        if ($this->action === 'delete') {
            $commentId = (int) $this->request->params['pass'][0];
            if ($this->Comment->isOwnedBy($commentId, $user['id'])) {
                return true;
            }
        }

        return parent::isAuthorized($user);
    }

    /**
     * add: Adiciona um novo comentário a um post.
     */
    public function add() {
        if ($this->request->is('post')) {
            $this->Comment->create();
            
            // Pega o ID do usuário logado e o ID do post (que deve vir do formulário)
            $this->request->data['Comment']['user_id'] = $this->Auth->user('id');
            
            // Pega o post_id dos dados enviados pelo formulário
            $postId = $this->request->data['Comment']['post_id'];

            if (!$postId) {
                 $this->Flash->error(__('ID do post inválido.'));
                 return $this->redirect($this->referer()); // Volta de onde veio
            }

            if ($this->Comment->save($this->request->data)) {
                $this->Flash->success(__('Comentário adicionado com sucesso.'));
            } else {
                $this->Flash->error(__('Não foi possível adicionar seu comentário. Verifique os erros.'));
            }
            
            // Redireciona de volta para a página do post
            return $this->redirect(array('controller' => 'posts', 'action' => 'view', $postId));
        }
        
        // Se não for POST, apenas redireciona para o início
        return $this->redirect(array('controller' => 'posts', 'action' => 'index'));
    }

    /**
     * delete: Deleta um comentário.
     */
    public function delete($id = null) {
        if (!$id) {
            throw new NotFoundException(__('Comentário inválido'));
        }

        // Pega o comentário antes de deletar, para saber para qual post voltar
        $comment = $this->Comment->findById($id);
        if (!$comment) {
            throw new NotFoundException(__('Comentário inválido'));
        }
        
        $postId = $comment['Comment']['post_id'];

        // A checagem isAuthorized() garante que só o dono/admin pode deletar

        if ($this->Comment->delete($id)) {
            $this->Flash->success(__('Comentário excluído.'));
        } else {
            $this->Flash->error(__('O comentário não pôde ser excluído.'));
        }

        // Redireciona de volta para a página do post
        return $this->redirect(array('controller' => 'posts', 'action' => 'view', $postId));
    }
}