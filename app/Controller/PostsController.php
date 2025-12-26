<?php
App::uses('AppController', 'Controller');

/**
 * Posts Controller
 *
 * @property Post $Post
 * @property FlashComponent $Flash
 * @property AuthComponent $Auth
 * @property PaginatorComponent $Paginator
 */
class PostsController extends AppController {

    public $helpers = array('Html', 'Form', 'Flash');
    public $components = array('Flash', 'Paginator');

    /**
     * beforeFilter callback
     *
     * @return void
     */
    public function beforeFilter() {
        parent::beforeFilter();
        // Permite que qualquer pessoa (mesmo não logada) acesse a lista (index) e a leitura (view)
        // A proteção de rascunhos será feita dentro dessas funções para filtrar o conteúdo.
        $this->Auth->allow('index', 'view');
    }

    /**
     * isAuthorized callback
     * Define permissões gerais baseadas no papel do usuário
     *
     * @param array $user
     * @return boolean
     */
    public function isAuthorized($user) {
        // Admins podem fazer tudo no sistema
        if (isset($user['role']) && $user['role'] === 'admin') {
            return true;
        }

        // Autores: só podem acessar a tela de adicionar
        if (in_array($this->action, array('add'))) {
            return true;
        }
        
        // IMPORTANTE: Liberamos o acesso às ações 'edit' e 'delete' aqui no isAuthorized
        // para tratar a lógica específica (dono do post vs outros) dentro das próprias funções.
        // Isso permite exibir aquela mensagem amarela personalizada em vez do erro padrão "Forbidden".
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
     * Listagem de posts com filtros avançados e proteção de visibilidade
     *
     * @return void
     */
    public function index() {
        // 1. LIMPEZA DE ID E MODELO (CRUCIAL)
        // Reseta o modelo para garantir que nenhum ID de save anterior interfira na busca
        $this->Post->create(); 
        $this->Post->id = null; 
        $this->Post->recursive = 0;
        
        // 2. Inicializa array de condições
        $conditions = array();

        // 3. Captura dados da URL (GET parameters) para filtros
        $filtroTitulo = $this->request->query('search_query');
        $filtroStatus = $this->request->query('filter_status');
        
        $ano = $this->request->query('year');
        $mes = $this->request->query('month');
        $dia = $this->request->query('day');

        // --- LÓGICA 1: Busca por Texto (Título OU Conteúdo) ---
        if (!empty($filtroTitulo)) {
            $termo = trim($filtroTitulo);
            // ILIKE é case-insensitive no PostgreSQL (LIKE no MySQL precisa de collation correta)
            $conditions['OR'] = array(
                'Post.title ILIKE' => '%' . $termo . '%',
                'Post.body ILIKE' => '%' . $termo . '%'
            );
        }

        // --- LÓGICA 2: Status (Texto) ---
        if (!empty($filtroStatus)) {
            $statusLimpo = strtolower(trim($filtroStatus));
            
            // Mapa para suportar termos em inglês se necessário
            $mapaStatus = array(
                'publicado' => 'published',
                'rascunho' => 'draft',
                'arquivado' => 'archived'
            );
            
            $statusIngles = isset($mapaStatus[$statusLimpo]) ? $mapaStatus[$statusLimpo] : $statusLimpo;

            // Força um grupo OR explícito para encontrar o status em PT ou EN
            $conditions['AND'][] = array(
                'OR' => array(
                    array('Post.status ILIKE' => $statusLimpo),
                    array('Post.status ILIKE' => $statusIngles)
                )
            );
        }

        // --- LÓGICA 3: Filtro de Data (DATAS) ---
        if (!empty($ano)) {
            
            // Define Mês Início/Fim (Padrão: ano inteiro se mês não for informado)
            $mes_inicio = !empty($mes) ? str_pad($mes, 2, '0', STR_PAD_LEFT) : '01';
            $mes_fim    = !empty($mes) ? str_pad($mes, 2, '0', STR_PAD_LEFT) : '12';
            
            // Define Dia Início
            $dia_inicio = !empty($dia) ? str_pad($dia, 2, '0', STR_PAD_LEFT) : '01';
            
            // Define Dia Fim
            if (!empty($dia)) {
                $dia_fim = str_pad($dia, 2, '0', STR_PAD_LEFT);
            } else {
                // Se não tem dia, pega o último dia do mês selecionado via função date
                $dia_fim = date('t', strtotime("$ano-$mes_fim-01"));
            }

            // Formata as datas para o PostgreSQL (YYYY-MM-DD HH:MM:SS)
            $data_inicio = "$ano-$mes_inicio-$dia_inicio 00:00:00";
            $data_fim    = "$ano-$mes_fim-$dia_fim 23:59:59";

            // Adiciona ranges de data às condições
            $conditions['Post.created >='] = $data_inicio;
            $conditions['Post.created <='] = $data_fim;
        }

        // --- LÓGICA 4: PROTEÇÃO DE VISIBILIDADE (RASCUNHOS) ---
        // Garante que rascunhos não vazem para quem não deve ver
        $user = $this->Auth->user();
        
        // Se NÃO for Admin, aplicamos restrições estritas
        if (!$user || (isset($user['role']) && $user['role'] !== 'admin')) {
            
            // Regra A: Visitantes (Não logados) -> Só veem PUBLICADO
            if (!$user) {
                // Força status publicado. Isso sobrescreve qualquer tentativa de buscar 'rascunho' na URL.
                $conditions['Post.status'] = 'publicado';
            } 
            // Regra B: Autores (Logados) -> Veem PUBLICADO + SEUS PRÓPRIOS RASCUNHOS
            else {
                // Cria uma condição isolada de escopo usando chaves numéricas para gerar um bloco ( ) no SQL
                $scopeCondition = array(
                    'OR' => array(
                        'Post.status' => 'publicado',
                        array(
                            'Post.status' => 'rascunho',
                            'Post.user_id' => $user['id'] // Apenas rascunhos DO USUÁRIO
                        )
                    )
                );
                
                // Adiciona ao array principal. O CakePHP une isso com AND às outras condições de busca.
                $conditions[] = $scopeCondition;
            }
        }

        // Configura a Paginação
        $this->Paginator->settings = array(
            'conditions' => $conditions,
            'limit' => 10, 
            'order' => array('Post.created' => 'desc')
        );

        try {
            $this->set('posts', $this->Paginator->paginate('Post'));
        } catch (NotFoundException $e) {
            // Se a paginação estourar (ex: deletou post da pág 5), volta pra pág 1
            $this->request->query['page'] = 1;
            $this->redirect(array('action' => 'index', '?' => $this->request->query));
        }

        // Mantém os filtros preenchidos na View (Formulário de Busca)
        $this->set('currentSearch', $filtroTitulo);
        $this->set('currentStatus', $filtroStatus);
        $this->set('currentYear', $ano);
        $this->set('currentMonth', $mes);
        $this->set('currentDay', $dia);
    }

    /**
     * view method
     * Exibe um post único
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function view($id = null) {
        // Limpa qualquer estado anterior
        $this->Post->id = null;
        
        if (!$id) {
            throw new NotFoundException(__('Post inválido'));
        }

        // Configurações da busca (trazendo dados relacionados: Autor, Anexos, Comentários)
        $options = array(
            'conditions' => array('Post.' . $this->Post->primaryKey => $id),
            'contain' => array(
                'User', 
                'Attachment', 
                'Comment' => array(
                    'User', 
                    'order' => 'Comment.created ASC' 
                ),
                'Vote' 
            )
        );
        
        $post = $this->Post->find('first', $options);

        if (!$post) {
            throw new NotFoundException(__('Post inválido'));
        }

        // --- PROTEÇÃO DE VISIBILIDADE (VIEW) ---
        // Se alguém tentar acessar a URL direta /posts/view/ID de um rascunho
        if (isset($post['Post']['status']) && $post['Post']['status'] === 'rascunho') {
            $user = $this->Auth->user();
            
            // 1. Visitante: Retorna 404 fingindo que o post não existe
            if (!$user) {
                throw new NotFoundException(__('Página não encontrada'));
            }
            
            // 2. Logado: Verifica se é o DONO ou ADMIN
            $isOwner = ($post['Post']['user_id'] == $user['id']);
            $isAdmin = (isset($user['role']) && $user['role'] === 'admin');
            
            if (!$isOwner && !$isAdmin) {
                // Redireciona com erro se tentar ver rascunho alheio
                $this->Flash->error(__('Você não tem permissão para visualizar este rascunho.'));
                return $this->redirect(array('action' => 'index'));
            }
        }
        
        $this->set('post', $post);
        $this->set('title_for_layout', $post['Post']['title']);
    }

    /**
     * add method
     * Adiciona novo post
     *
     * @return void
     */
    public function add() {
        if ($this->request->is('post')) {
            $this->Post->create(); // Garante um modelo limpo
            $this->request->data['Post']['user_id'] = $this->Auth->user('id'); // Associa ao usuário logado

            if ($this->Post->save($this->request->data)) {
                $postId = $this->Post->id;
                $postSalvo = true;
                $erroAnexo = false;

                // Lógica de Upload de Anexo
                $file = isset($this->request->data['Attachment']['file']) ? $this->request->data['Attachment']['file'] : null;
                
                if ($file && !empty($file['name']) && $file['error'] === UPLOAD_ERR_OK) {
                    
                    $filename = $this->Post->Attachment->saveFile($this->request->data['Attachment']);

                    if ($filename) {
                        $attachmentData = array(
                            'post_id' => $postId,
                            'filename' => $filename,
                            'path' => 'files/attachments/' . $filename 
                        );
                        $this->Post->Attachment->create();
                        if (!$this->Post->Attachment->save($attachmentData)) {
                            $erroAnexo = true;
                        }
                    } else {
                        $erroAnexo = true;
                    }
                }

                if ($postSalvo && !$erroAnexo) {
                    $this->Flash->success(__('Seu post (e anexo, se houver) foi salvo com sucesso.'));
                    // Limpa o ID antes de redirecionar para evitar conflitos
                    $this->Post->id = null;
                    return $this->redirect(array('action' => 'index'));

                } elseif ($postSalvo && $erroAnexo) {
                    $this->Flash->warning(__('O post foi salvo, mas houve um erro ao processar o anexo.'));
                    return $this->redirect(array('action' => 'edit', $postId));
                }
            } else {
                $this->Flash->error(__('Não foi possível adicionar seu post. Verifique os erros e tente novamente.'));
            }
        }
        $this->set('title_for_layout', 'Adicionar Post');
    }

    /**
     * edit method
     * Edição de posts existentes
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
        
        if ($this->Auth->user('role') != 'admin' && $post['Post']['user_id'] != $this->Auth->user('id')) {
            
            $this->Flash->set(
                'Você não tem permissão de Edição desse Post, mas pode criar um em Novo Post.',
                array(
                    'element' => 'default', 
                    'params' => array('class' => 'alert alert-warning') 
                )
            );
            
            return $this->redirect(array('action' => 'index'));
        }
        
        if ($this->request->is(array('post', 'put'))) {
            $this->Post->id = $id;
            
            if ($this->Post->save($this->request->data)) {
                $this->Flash->success(__('Seu post foi atualizado com sucesso.'));
                
                // LIMPA O ID ANTES DE REDIRECIONAR
                $this->Post->id = null;
                $this->Post->create(); // Reseta o modelo
                
                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Flash->error(__('Não foi possível atualizar seu post.'));
            }
        }
        
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
        if ($this->request->is('get')) {
            throw new MethodNotAllowedException();
        }
        
        $this->Post->id = $id;
        if (!$this->Post->exists()) {
             throw new NotFoundException(__('Post inválido'));
        }
        
        $post = $this->Post->findById($id);
        
        // --- REGRA PERSONALIZADA DE PERMISSÃO (DELETE) ---
        // Verifica se é dono ou admin. Se não for, exibe mensagem amarela.
        if ($this->Auth->user('role') != 'admin' && $post['Post']['user_id'] != $this->Auth->user('id')) {
            
            // Exibe mensagem AMARELA (Warning)
            $this->Flash->set(
                'Você não tem permissão de excluir esse Post.',
                array(
                    'element' => 'default', 
                    'params' => array('class' => 'alert alert-warning') 
                )
            );
            
            return $this->redirect(array('action' => 'index'));
        }
        
        if ($this->Post->delete($id)) {
            $this->Flash->success(__('O post com id: %s foi excluído.', h($id)));
        } else {
             $this->Flash->error(__('O post não pôde ser excluído.'));
        }
        
        // Limpa ID
        $this->Post->id = null;
        return $this->redirect(array('action' => 'index'));
    }
    
    /**
     * isOwnedBy helper
     * Auxiliar para verificar propriedade
     */
    public function isOwnedBy($post, $user) {
        return $this->Post->field('id', array('id' => $post, 'user_id' => $user)) !== false;
    }
}