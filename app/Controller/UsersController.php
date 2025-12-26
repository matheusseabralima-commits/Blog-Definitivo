<?php
App::uses('AppController', 'Controller');
App::uses('AuthComponent', 'Controller/Component');

class UsersController extends AppController {

    /**
     * Configuração dos Componentes do CakePHP.
     */
    public $components = array(
        'Session',
        'Flash',
        'Auth' => array(
            'loginRedirect' => array('controller' => 'posts', 'action' => 'index'),
            'logoutRedirect' => array('controller' => 'users', 'action' => 'login'),
            'loginAction' => array('controller' => 'users', 'action' => 'login'),
            'authError' => 'Você precisa estar logado ou não tem permissão para acessar esta página.',
            'authenticate' => array(
                'Form' => array(
                    'fields' => array('username' => 'username', 'password' => 'password')
                )
            ),
            'authorize' => array('Controller') 
        )
    );

    public function beforeFilter() {
        parent::beforeFilter();
        
        // Permite que visitantes se registrem e façam login/logout
        $this->Auth->allow('add', 'login', 'logout');

        // Verifica permissões para ações administrativas
        $acao = $this->request->params['action'];
        $acoesAdmin = array('index', 'delete');

        if (in_array($acao, $acoesAdmin) && $this->Auth->user('role') != 'admin') {
            $this->Flash->error(__('Você não tem permissão para acessar esta área.'));
            return $this->redirect(array('controller' => 'posts', 'action' => 'index'));
        }
    }

    public function isAuthorized($user) {
        // Admins podem fazer tudo
        if (isset($user['role']) && $user['role'] === 'admin') {
            return true;
        }

        // Autores só podem fazer logout (edição de si mesmo é checada no edit)
        if (in_array($this->action, array('logout'))) {
            return true;
        }

        return false;
    }

    /*
     * LOGIN: Autenticação e definição de Layout
     */
    public function login() {
        // --- DEFINIÇÃO DO LAYOUT (Correção Visual) ---
        $this->layout = 'login'; 

        if ($this->request->is('post')) {
            if ($this->Auth->login()) {
                
                // Atualiza a sessão com os dados completos do usuário (incluindo role)
                $userId = $this->Auth->user('id'); 
                $this->User->recursive = -1;
                $usuarioCompleto = $this->User->findById($userId);

                if ($usuarioCompleto) {
                    $this->Session->write('Auth.User', $usuarioCompleto['User']);
                }
                return $this->redirect($this->Auth->redirectUrl());
                
            } else {
                $this->Flash->error(__('Usuário ou senha inválidos, tente novamente.'));
            }
        }
    }

    /*
     * LOGOUT
     */
    public function logout() {
        $this->Flash->success('Você saiu do sistema.');
        return $this->redirect($this->Auth->logout());
    }

    /*
     * INDEX: Listagem de usuários
     */
    public function index() {
        // Define a recursão para coloacfar o usuáriom e os dadoas necessários da tabela
        $this->User->recursive = 0;
        
        try {
            $users = $this->User->find('all', array(
                'order' => 'User.username ASC'
            ));
        } catch (Exception $e) {
            $this->Flash->error(__('Erro ao listar usuários: ') . $e->getMessage());
            $users = array();
        }
        
        $this->set('users', $users);
    }

    /*
     * ADD: Registro de novo usuário
     */
    public function add() {
        // --- DEFINIÇÃO DO LAYOUT (Correção Visual) ---
        $this->layout = 'login'; 

        if ($this->request->is('post')) {
            $this->User->create();
            
            if ($this->User->save($this->request->data)) {
                
                // Lógica de Auto-Login após cadastro
                $userId = $this->User->id;
                $this->User->recursive = -1;
                $usuarioCompleto = $this->User->findById($userId);

                if ($usuarioCompleto) {
                    if ($this->Auth->login($usuarioCompleto['User'])) {
                        $this->Session->write('Auth.User', $usuarioCompleto['User']);
                        $this->Flash->success(__('Usuário cadastrado e logado com sucesso.'));
                        return $this->redirect($this->Auth->redirectUrl());
                    }
                }

                $this->Flash->success(__('Usuário cadastrado. Por favor, faça o login.'));
                return $this->redirect(array('action' => 'login'));

            } else {
                $this->Flash->error(__('Erro ao cadastrar usuário. Verifique os dados e tente novamente.'));
            }
        }
    }
    
    /*
     * EDIT: Edição de usuário com regras de segurança
     */
    public function edit($id = null) {
        if (!$id) {
             throw new NotFoundException(__('Usuário inválido'));
        }

        $usuarioSendoEditado = $this->User->read(null, $id);
        if (!$usuarioSendoEditado) {
             throw new NotFoundException(__('Usuário não encontrado'));
        }

        $adminLogado = $this->Auth->user();
        $this->User->id = $id; 

        // REGRA: Autor só pode editar a si mesmo
        if ($adminLogado['role'] == 'author' && $adminLogado['id'] != $id) {
             $this->Flash->error(__('Você não tem permissão para editar este usuário.'));
             return $this->redirect(array('action' => 'index'));
        }

        if ($this->request->is('post') || $this->request->is('put')) {
            
            $formData = $this->request->data;
            $currentRole = $usuarioSendoEditado['User']['role'];
            $newRole = isset($formData['User']['role']) ? $formData['User']['role'] : $currentRole;

            // Regras para Admin
            if ($adminLogado['role'] === 'admin') {
                // REGRA: Admin não pode se rebaixar
                if ($adminLogado['id'] == $id && $newRole !== 'admin') {
                    $this->Flash->error('AÇÃO PROIBIDA: Um Admin não pode alterar seu próprio perfil para Autor.');
                    $formData['User']['role'] = 'admin'; 
                    $this->request->data['User']['role'] = 'admin'; 
                } 
                // REGRA: Admin não pode alterar perfil de outro Admin
                else if ($adminLogado['id'] != $id && $currentRole === 'admin' && $newRole !== 'admin') {
                    $this->Flash->error('AÇÃO PROIBIDA: Você não pode alterar o perfil de outro Administrador.');
                    $formData['User']['role'] = 'admin'; 
                    $this->request->data['User']['role'] = 'admin'; 
                }
            }

            // Remove a senha do array se estiver vazia (para não salvar senha em branco), mantendo a senha antiga
            if (empty($formData['User']['password'])) {
                unset($formData['User']['password']);
            }

            if ($this->User->save($formData)) {
                $this->Flash->success(__('Usuário atualizado com sucesso.'));
                
                // Se editou a si mesmo, atualiza a sessão
                if ($adminLogado['id'] == $id) {
                     $this->Session->write('Auth.User', $this->User->read(null, $id)['User']);
                }
                
                return $this->redirect(array('action' => 'index'));
            }
            
            $this->Flash->error(__('Não foi possível atualizar o usuário. Verifique os erros abaixo.'));
            
        } else {
            $this->request->data = $usuarioSendoEditado;
            unset($this->request->data['User']['password']); // Não envia a senha hash para o formulário
        }
    }
    
     // --- LÓGICA DE SOFT DELETE (Desativar) ---
    public function delete($id = null) {
        // Verifica se a requisição é POST (segurança)
        if (!$this->request->is('post')) {
            throw new MethodNotAllowedException();
        }

        $this->User->id = $id;
        
        // Verifica se o usuário existe no banco
        if (!$this->User->exists()) {
            $this->Flash->error(__('Usuário inválido.'));
            return $this->redirect(array('action' => 'index'));
        }
        
        $adminLogado = $this->Auth->user();
        $userToDelete = $this->User->findById($id);

        // REGRA 1: Admin não pode se desativar (segurança para não trancar o sistema)
        if ($adminLogado['id'] == $id) {
             $this->Flash->error('AÇÃO PROIBIDA: Você não pode desativar sua própria conta.');
             return $this->redirect(array('action' => 'index'));
        }

        // REGRA 2: Admin não pode desativar outro Admin (hierarquia)
        if ($userToDelete['User']['role'] === 'admin') {
            $this->Flash->error('AÇÃO PROIBIDA: Você não pode desativar outro usuário Administrador.');
            return $this->redirect(array('action' => 'index'));
        }
        
        // MUDANÇA: Update 'active' para 0 em vez de deletar
        if ($this->User->saveField('active', 0)) {
            $this->Flash->success(__('Usuário desativado com sucesso. (Ele permanece no banco de dados)'));
        } else {
            $this->Flash->error(__('O usuário não pôde ser desativado.'));
        }
        
        return $this->redirect(array('action' => 'index'));
    }

    // --- NOVA FUNÇÃO: REATIVAR USUÁRIO ---
    public function activate($id = null) {
        if (!$this->request->is('post')) {
            throw new MethodNotAllowedException();
        }

        $this->User->id = $id;

        if (!$this->User->exists()) {
            $this->Flash->error(__('Usuário inválido.'));
            return $this->redirect(array('action' => 'index'));
        }

        // Reativa o usuário setando 'active' para 1
        if ($this->User->saveField('active', 1)) {
            $this->Flash->success(__('Usuário reativado com sucesso! Agora ele pode fazer login novamente.'));
        } else {
            $this->Flash->error(__('Não foi possível reativar o usuário.'));
        }

        return $this->redirect(array('action' => 'index'));
    }
}