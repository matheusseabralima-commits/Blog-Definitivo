<?php
App::uses('Controller', 'Controller');
// A nossa correção do "bug do inferno" (Plano B) está no UsersController e User Model,
// por isso este ficheiro pode ficar limpo.

class AppController extends Controller {
    
    // Carrega os componentes para TODOS os controllers
    public $components = array(
        'Flash',
        'Session',
        'Auth' => array(
            'loginRedirect' => array('controller' => 'posts', 'action' => 'index'),
            'logoutRedirect' => array('controller' => 'users', 'action' => 'login'), // Manda para o login
            'loginAction' => array('controller' => 'users', 'action' => 'login'),
            'authenticate' => array(
                'Form' => array(
                    // Usa o 'Plano B' (sha1) que já configurámos no UsersController
                    'fields' => array('username' => 'username', 'password' => 'password')
                )
            ),
            'authorize' => array('Controller') // Ativa a autorização
        )
    );

    // Função 'isAuthorized' genérica (pai)
    public function isAuthorized($user) {
        // Por defeito, nega o acesso
        return false;
    }

    // Corre ANTES de CADA controller
    public function beforeFilter() {
        // Permite que 'index' e 'view' dos Posts sejam vistos por todos (requisito do PDF)
        $this->Auth->allow('index', 'view');

        // Passa o utilizador logado para TODAS as views (requisito do PDF)
        $this->set('loggedInUser', $this->Auth->user());
    }
}