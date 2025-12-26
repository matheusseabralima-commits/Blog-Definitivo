<?php
App::uses('AppController', 'Controller');

/**
 * Posts Controller
 *
 * Responsável por gerenciar todas as ações relacionadas aos posts do blog:
 * Listagem, Visualização, Criação, Edição e Exclusão.
 *
 * @property Post $Post
 * @property FlashComponent $Flash
 * @property AuthComponent $Auth
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class PostsController extends AppController {

    /**
     * Helpers disponíveis na View
     */
    public $helpers = array('Html', 'Form', 'Flash', 'Text', 'Time');

    /**
     * Componentes carregados
     */
    public $components = array('Flash', 'Paginator', 'Session');

    /**
     * beforeFilter callback
     * Executado antes de qualquer ação do controller.
     *
     * @return void
     */
    public function beforeFilter() {
        parent::beforeFilter();
        // Permite que qualquer pessoa (mesmo não logada) acesse a lista (index) e a leitura (view)
        // A proteção de rascunhos é feita logicamente dentro dessas funções.
        $this->Auth->allow('index', 'view');
    }

    /**
     * isAuthorized callback
     * Define permissões gerais baseadas no papel do usuário
     *
     * @param array $user O usuário logado
     * @return boolean
     */
    public function isAuthorized($user) {
        // Regra 1: Admins podem fazer tudo no sistema
        if (isset($user['role']) && $user['role'] === 'admin') {
            return true;
        }

        // Regra 2: Autores podem acessar a tela de adicionar
        if (in_array($this->action, array('add'))) {
            return true;
        }
        
        // Regra 3: Liberamos 'edit' e 'delete' aqui para tratar a permissão
        // específica (dono vs outros) dentro das próprias funções, permitindo
        // mensagens de erro personalizadas (Flash Amarelo) em vez de erro 403 padrão.
        if (in_array($this->action, array('edit', 'delete'))) {
            return true;
        }

        // Para qualquer outra ação não listada, nega o acesso padrão
        return parent::isAuthorized($user);
    }

    /* -------------------------------------------------------------------------- */
    /* ACTIONS                                  */
    /* -------------------------------------------------------------------------- */

    /**
     * index method
     * Listagem de posts com filtros avançados e URL Limpa (Padrão PRG)
     *
     * Lógica:
     * 1. Se receber POST (formulário), salva filtros na Sessão e redireciona.
     * 2. Se receber GET, lê da Sessão e aplica os filtros.
     * Isso mantém a URL limpa (ex: /posts) sem parâmetros feios.
     *
     * @return void
     */
    public function index() {
        // 1. Limpeza preventiva do Model
        $this->Post->create(); 
        $this->Post->id = null; 
        $this->Post->recursive = 0;

        // -----------------------------------------------------------
        // LÓGICA DE FILTRO VIA SESSÃO (URL LIMPA)
        // -----------------------------------------------------------

        // A. Botão "Limpar Filtros" (?clear=1 na URL)
        if (isset($this->request->query['clear'])) {
            $this->Session->delete('Post.Filter');
            return $this->redirect(array('action' => 'index'));
        }

        // B. Processamento do Formulário (POST)
        if ($this->request->is('post')) {
            // Verifica se veio do formulário de busca
            if (!empty($this->request->data)) {
                // Salva os filtros na memória (Sessão)
                $this->Session->write('Post.Filter', $this->request->data);
            }
            // Redireciona para GET (Remove o POST do navegador e limpa URL)
            return $this->redirect(array('action' => 'index'));
        }

        // C. Leitura dos Filtros da Memória
        // Se não houver nada na sessão, retorna array vazio.
        $filters = $this->Session->read('Post.Filter');
        
        // Extrai variáveis para facilitar o uso na query e na View
        $searchQuery  = isset($filters['search_query']) ? $filters['search_query'] : '';
        $filterStatus = isset($filters['filter_status']) ? $filters['filter_status'] : '';
        $year         = isset($filters['year']) ? $filters['year'] : '';
        $month        = isset($filters['month']) ? $filters['month'] : '';
        $day          = isset($filters['day']) ? $filters['day'] : '';

        // -----------------------------------------------------------
        // MONTAGEM DAS CONDIÇÕES DE BUSCA (QUERY)
        // -----------------------------------------------------------
        
        $conditions = array();

        // Filtro 1: Busca Texto (Título ou Corpo)
        if (!empty($searchQuery)) {
            $termo = trim($searchQuery);
            // ILIKE é case-insensitive no PostgreSQL
            $conditions['OR'] = array(
                'Post.title ILIKE' => '%' . $termo . '%',
                'Post.body ILIKE' => '%' . $termo . '%'
            );
        }

        // Filtro 2: Status
        if (!empty($filterStatus)) {
            $statusLimpo = strtolower(trim($filterStatus));
            
            // Mapa para garantir compatibilidade se o banco usar inglês
            $mapaStatus = array(
                'publicado' => 'published',
                'rascunho' => 'draft',
                'arquivado' => 'archived'
            );
            $statusIngles = isset($mapaStatus[$statusLimpo]) ? $mapaStatus[$statusLimpo] : $statusLimpo;

            // Busca pelo termo em PT ou EN
            $conditions['AND'][] = array(
                'OR' => array(
                    array('Post.status ILIKE' => $statusLimpo),
                    array('Post.status ILIKE' => $statusIngles)
                )
            );
        }

        // Filtro 3: Datas Complexas
        if (!empty($year)) {
            // Define Mês (Padrão: ano inteiro se mês vazio)
            $m_ini = !empty($month) ? str_pad($month, 2, '0', STR_PAD_LEFT) : '01';
            $m_fim = !empty($month) ? str_pad($month, 2, '0', STR_PAD_LEFT) : '12';
            
            // Define Dia
            $d_ini = !empty($day) ? str_pad($day, 2, '0', STR_PAD_LEFT) : '01';
            
            if (!empty($day)) {
                $d_fim = str_pad($day, 2, '0', STR_PAD_LEFT);
            } else {
                // Último dia do mês (lógica robusta para anos bissextos etc)
                $d_fim = date('t', strtotime("$year-$m_fim-01"));
            }

            // Formata para timestamp do banco
            $data_inicio = "$year-$m_ini-$d_ini 00:00:00";
            $data_fim    = "$year-$m_fim-$d_fim 23:59:59";

            $conditions['Post.created >='] = $data_inicio;
            $conditions['Post.created <='] = $data_fim;
        }

        // -----------------------------------------------------------
        // PROTEÇÃO DE VISIBILIDADE (RASCUNHOS)
        // -----------------------------------------------------------
        $user = $this->Auth->user();
        
        // Se NÃO for Admin, aplica restrições
        if (!$user || (isset($user['role']) && $user['role'] !== 'admin')) {
            
            if (!$user) {
                // VISITANTE: Força apenas Publicados (ignora qualquer filtro de status tentando ver rascunho)
                $conditions['Post.status'] = 'publicado';
            } else {
                // AUTOR: Vê Publicados de todos + Seus próprios Rascunhos
                // Cria um grupo OR isolado para não conflitar com a busca de texto
                $scopeCondition = array(
                    'OR' => array(
                        'Post.status' => 'publicado',
                        array(
                            'Post.status' => 'rascunho',
                            'Post.user_id' => $user['id']
                        )
                    )
                );
                $conditions[] = $scopeCondition;
            }
        }

        // -----------------------------------------------------------
        // PAGINAÇÃO
        // -----------------------------------------------------------
        $this->Paginator->settings = array(
            'conditions' => $conditions,
            'limit' => 10, 
            'order' => array('Post.created' => 'desc')
        );

        try {
            $this->set('posts', $this->Paginator->paginate('Post'));
        } catch (NotFoundException $e) {
            // Se a paginação estourar (ex: estava na pág 5 e filtrou algo com 1 pág),
            // reseta os filtros para evitar ficar preso no erro.
            $this->Session->delete('Post.Filter');
            return $this->redirect(array('action' => 'index'));
        }

        // Envia variáveis para a View preencher o formulário (manter estado visual)
        $this->set(compact('searchQuery', 'filterStatus', 'year', 'month', 'day'));
    }

    /**
     * view method
     * Exibe um post único com detalhes
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function view($id = null) {
        // Limpa estado anterior
        $this->Post->id = null;
        
        if (!$id) {
            throw new NotFoundException(__('Post inválido'));
        }

        // Busca completa com associações (JOINs)
        // Removemos 'Attachment' daqui também se você não quiser nem exibir os antigos
        $options = array(
            'conditions' => array('Post.' . $this->Post->primaryKey => $id),
            'contain' => array(
                'User',          // Autor do post
                'Comment' => array(
                    'User',      // Autor do comentário
                    'order' => 'Comment.created ASC' 
                ),
                'Vote' 
            )
        );
        
        $post = $this->Post->find('first', $options);

        if (!$post) {
            throw new NotFoundException(__('Post inválido'));
        }

        // --- PROTEÇÃO DE RASCUNHOS (Acesso Direto via URL) ---
        if (isset($post['Post']['status']) && $post['Post']['status'] === 'rascunho') {
            $user = $this->Auth->user();
            
            // 1. Visitante: Retorna 404 (segurança por obscuridade)
            if (!$user) {
                throw new NotFoundException(__('Página não encontrada'));
            }
            
            // 2. Logado: Verifica propriedade
            $isOwner = ($post['Post']['user_id'] == $user['id']);
            $isAdmin = (isset($user['role']) && $user['role'] === 'admin');
            
            if (!$isOwner && !$isAdmin) {
                $this->Flash->error(__('Você não tem permissão para visualizar este rascunho.'));
                return $this->redirect(array('action' => 'index'));
            }
        }
        
        $this->set('post', $post);
        $this->set('title_for_layout', $post['Post']['title']);
    }

    /**
     * add method
     * Adiciona novo post (Simples, sem anexos)
     *
     * @return void
     */
    public function add() {
        if ($this->request->is('post')) {
            $this->Post->create(); // Limpa modelo
            
            // Força o ID do usuário logado (segurança)
            $this->request->data['Post']['user_id'] = $this->Auth->user('id'); 

            if ($this->Post->save($this->request->data)) {
                $this->Flash->success(__('Seu post foi salvo com sucesso.'));
                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Flash->error(__('Não foi possível adicionar seu post. Verifique os erros e tente novamente.'));
            }
        }
        $this->set('title_for_layout', 'Adicionar Post');
    }

    /**
     * edit method
     * Edição de posts existentes com validação de propriedade
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function edit($id = null) {
        if (!$id) {
            throw new NotFoundException(__('Post inválido.'));
        }

        $post = $this->Post->findById($id);
        if (!$post) {
            throw new NotFoundException(__('Post inválido.'));
        }
        
        // --- VALIDAÇÃO DE PERMISSÃO ---
        // Apenas Dono ou Admin podem editar
        if ($this->Auth->user('role') != 'admin' && $post['Post']['user_id'] != $this->Auth->user('id')) {
            $this->Flash->set(
                'Você não tem permissão de Edição desse Post.',
                array('element' => 'default', 'params' => array('class' => 'alert alert-warning'))
            );
            return $this->redirect(array('action' => 'index'));
        }
        
        if ($this->request->is(array('post', 'put'))) {
            $this->Post->id = $id;
            
            if ($this->Post->save($this->request->data)) {
                $this->Flash->success(__('Seu post foi atualizado com sucesso.'));
                
                // Limpa ID para evitar contaminação em operações futuras
                $this->Post->id = null;
                
                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Flash->error(__('Não foi possível atualizar seu post.'));
            }
        }
        
        // Preenche o formulário se não houve submissão
        if (!$this->request->data) {
            $this->request->data = $post;
        }
        $this->set('post', $post);
        $this->set('title_for_layout', 'Editar Post: ' . $post['Post']['title']);
    }
    
    /**
     * delete method
     * Exclusão de posts
     *
     * @throws NotFoundException
     * @throws MethodNotAllowedException
     * @param string $id
     * @return void
     */
    public function delete($id = null) {
        // Exige POST/DELETE para segurança contra links maliciosos
        if ($this->request->is('get')) {
            throw new MethodNotAllowedException();
        }
        
        $this->Post->id = $id;
        if (!$this->Post->exists()) {
             throw new NotFoundException(__('Post inválido'));
        }
        
        $post = $this->Post->findById($id);
        
        // --- VALIDAÇÃO DE PERMISSÃO ---
        if ($this->Auth->user('role') != 'admin' && $post['Post']['user_id'] != $this->Auth->user('id')) {
            $this->Flash->set(
                'Você não tem permissão de excluir esse Post.',
                array('element' => 'default', 'params' => array('class' => 'alert alert-warning'))
            );
            return $this->redirect(array('action' => 'index'));
        }
        
        if ($this->Post->delete($id)) {
            $this->Flash->success(__('O post foi excluído com sucesso.'));
        } else {
             $this->Flash->error(__('O post não pôde ser excluído.'));
        }
        
        return $this->redirect(array('action' => 'index'));
    }
    
    /**
     * isOwnedBy helper
     * Auxiliar para verificar propriedade de forma rápida
     */
    public function isOwnedBy($post, $user) {
        return $this->Post->field('id', array('id' => $post, 'user_id' => $user)) !== false;
    }
}