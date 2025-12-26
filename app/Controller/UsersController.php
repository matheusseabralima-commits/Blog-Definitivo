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
        'Paginator', // Necessário para a paginação funcionar
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

        // Verifica permissões para ações administrativas (index e delete restritos a Admins)
        $acao = $this->request->params['action'];
        $acoesAdmin = array('index', 'delete', 'activate');

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
     * LOGIN: Autenticação
     */
    public function login() {
        $this->layout = 'login'; 

        if ($this->request->is('post')) {
            if ($this->Auth->login()) {
                
                // Atualiza a sessão com os dados completos do usuário
                $userId = $this->Auth->user('id'); 
                $this->User->recursive = -1;
                $usuarioCompleto = $this->User->findById($userId);

                if ($usuarioCompleto) {
                    // Verifica se está ativo (Soft Delete)
                    if (isset($usuarioCompleto['User']['active']) && $usuarioCompleto['User']['active'] == 0) {
                        $this->Flash->error(__('Sua conta está desativada. Entre em contato com o administrador.'));
                        return $this->redirect($this->Auth->logout());
                    }
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
     * INDEX: Listagem de usuários com Filtros e Paginação
     */
    public function index() {
        $this->User->recursive = 0;
        
        // 1. Inicializa condições de busca
        $conditions = array();

        // 2. Captura dados da URL (GET)
        $filtroTexto = $this->request->query('search_query');
        $filtroRole = $this->request->query('filter_role');
        $filtroStatus = $this->request->query('filter_status');

        // --- Filtro por Nome (Username) ---
        if (!empty($filtroTexto)) {
            $termo = trim($filtroTexto);
            $conditions['User.username ILIKE'] = '%' . $termo . '%';
        }

        // --- Filtro por Função (Role) ---
        if (!empty($filtroRole)) {
            $conditions['User.role'] = $filtroRole;
        }

        // --- Filtro por Status (Ativo/Inativo) ---
        // Verifica se não é nulo e nem string vazia (pois '0' é falso em PHP)
        if ($filtroStatus !== null && $filtroStatus !== '') {
            $conditions['User.active'] = $filtroStatus;
        }

        // Configura Paginação
        $this->Paginator->settings = array(
            'conditions' => $conditions,
            'limit' => 10,
            'order' => array('User.username' => 'ASC')
        );
        
        try {
            // Usa o Paginator em vez de find('all')
            $this->set('users', $this->Paginator->paginate('User'));
        } catch (Exception $e) {
            // Se a paginação falhar (ex: página inexistente), volta para a primeira
            $this->request->query['page'] = 1;
            $this->redirect(array('action' => 'index', '?' => $this->request->query));
        }

        // Passa os valores atuais para a View manter o formulário preenchido
        $this->set('currentSearch', $filtroTexto);
        $this->set('currentRole', $filtroRole);
        $this->set('currentStatus', $filtroStatus);
    }

    /*
     * ADD: Registro de novo usuário
     */
    public function add() {
        $this->layout = 'login'; 

        if ($this->request->is('post')) {
            $this->User->create();
            // Define como ativo por padrão
            $this->request->data['User']['active'] = 1;
            
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
     * EDIT: Edição de usuário
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

            // Regras de Proteção para Admin
            if ($adminLogado['role'] === 'admin') {
                // Admin não pode se rebaixar
                if ($adminLogado['id'] == $id && $newRole !== 'admin') {
                    $this->Flash->error('AÇÃO PROIBIDA: Um Admin não pode alterar seu próprio perfil para Autor.');
                    $formData['User']['role'] = 'admin'; 
                    $this->request->data['User']['role'] = 'admin'; 
                } 
                // Admin não pode alterar outro Admin
                else if ($adminLogado['id'] != $id && $currentRole === 'admin' && $newRole !== 'admin') {
                    $this->Flash->error('AÇÃO PROIBIDA: Você não pode alterar o perfil de outro Administrador.');
                    $formData['User']['role'] = 'admin'; 
                    $this->request->data['User']['role'] = 'admin'; 
                }
            }

            // Não altera a senha se o campo estiver vazio
            if (empty($formData['User']['password'])) {
                unset($formData['User']['password']);
            }

            if ($this->User->save($formData)) {
                $this->Flash->success(__('Usuário atualizado com sucesso.'));
                
                // Se editou a si mesmo, atualiza a sessão para refletir mudanças (nome, etc)
                if ($adminLogado['id'] == $id) {
                     $this->Session->write('Auth.User', $this->User->read(null, $id)['User']);
                }
                
                return $this->redirect(array('action' => 'index'));
            }
            
            $this->Flash->error(__('Não foi possível atualizar o usuário. Verifique os erros abaixo.'));
            
        } else {
            $this->request->data = $usuarioSendoEditado;
            unset($this->request->data['User']['password']); 
        }
    }
    
    /*
     * DELETE: Desativar Usuário (Soft Delete)
     */
    public function delete($id = null) {
        if (!$this->request->is('post')) {
            throw new MethodNotAllowedException();
        }

        $this->User->id = $id;
        
        if (!$this->User->exists()) {
            $this->Flash->error(__('Usuário inválido.'));
            return $this->redirect(array('action' => 'index'));
        }
        
        $adminLogado = $this->Auth->user();
        $userToDelete = $this->User->findById($id);

        // Regras de Proteção
        if ($adminLogado['id'] == $id) {
             $this->Flash->error('AÇÃO PROIBIDA: Você não pode desativar sua própria conta.');
             return $this->redirect(array('action' => 'index'));
        }
        if ($userToDelete['User']['role'] === 'admin') {
            $this->Flash->error('AÇÃO PROIBIDA: Você não pode desativar outro usuário Administrador.');
            return $this->redirect(array('action' => 'index'));
        }
        
        // MUDANÇA: Update 'active' para 0 em vez de deletar fisicamente
        if ($this->User->saveField('active', 0)) {
            $this->Flash->success(__('Usuário desativado com sucesso. (Ele permanece no banco de dados)'));
        } else {
            $this->Flash->error(__('O usuário não pôde ser desativado.'));
        }
        
        return $this->redirect(array('action' => 'index'));
    }

    /*
     * ACTIVATE: Reativar Usuário
     */
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