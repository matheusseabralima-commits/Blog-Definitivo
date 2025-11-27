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

    public function beforeFilter() {
        parent::beforeFilter();
        // Permite que qualquer pessoa veja a listagem e o detalhe dos posts
        $this->Auth->allow('index', 'view');
    }

    public function isAuthorized($user) {
        // Admins podem fazer tudo
        if (isset($user['role']) && $user['role'] === 'admin') {
            return true;
        }

        // Autores: só podem adicionar
        if (in_array($this->action, array('add'))) {
            return true;
        }
        
        // IMPORTANTE: Liberamos o acesso às ações 'edit' e 'delete' aqui para tratar a lógica
        // de permissão personalizada (mensagem amarela) dentro das próprias funções.
        if (in_array($this->action, array('edit', 'delete'))) {
            return true;
        }

        // Nenhuma regra de acesso corresponde, nega o acesso
        return parent::isAuthorized($user);
    }

    /*
     * INDEX: Listagem com Filtros Blindados e Corrigidos
     */
    public function index() {
        // 1. LIMPEZA DE ID E MODELO (CRUCIAL)
        // Reseta o modelo para garantir que nenhum ID de save anterior interfira
        $this->Post->create(); 
        $this->Post->id = null; 
        $this->Post->recursive = 0;
        
        // 2. Inicializa condições
        $conditions = array();

        // 3. Pega dados da URL (GET)
        $filtroTitulo = $this->request->query('search_query');
        $filtroStatus = $this->request->query('filter_status');
        
        $ano = $this->request->query('year');
        $mes = $this->request->query('month');
        $dia = $this->request->query('day');

        // --- LÓGICA 1: Busca por Texto (Título OU Conteúdo) ---
        if (!empty($filtroTitulo)) {
            $termo = trim($filtroTitulo);
            $conditions['OR'] = array(
                'Post.title ILIKE' => '%' . $termo . '%',
                'Post.body ILIKE' => '%' . $termo . '%'
            );
        }

        // --- LÓGICA 2: Status (Texto) ---
        if (!empty($filtroStatus)) {
            $statusLimpo = strtolower(trim($filtroStatus));
            
            $mapaStatus = array(
                'publicado' => 'published',
                'rascunho' => 'draft',
                'arquivado' => 'archived'
            );
            
            $statusIngles = isset($mapaStatus[$statusLimpo]) ? $mapaStatus[$statusLimpo] : $statusLimpo;

            // Força um grupo OR explícito para evitar erros de sintaxe
            $conditions['AND'][] = array(
                'OR' => array(
                    array('Post.status ILIKE' => $statusLimpo),
                    array('Post.status ILIKE' => $statusIngles)
                )
            );
        }

        // --- LÓGICA 3: Filtro de Data (DATAS) ---
        if (!empty($ano)) {
            
            // Define Mês Início/Fim
            $mes_inicio = !empty($mes) ? str_pad($mes, 2, '0', STR_PAD_LEFT) : '01';
            $mes_fim    = !empty($mes) ? str_pad($mes, 2, '0', STR_PAD_LEFT) : '12';
            
            // Define Dia Início
            $dia_inicio = !empty($dia) ? str_pad($dia, 2, '0', STR_PAD_LEFT) : '01';
            
            // Define Dia Fim
            if (!empty($dia)) {
                $dia_fim = str_pad($dia, 2, '0', STR_PAD_LEFT);
            } else {
                // Se não tem dia, pega o último dia do mês selecionado
                $dia_fim = date('t', strtotime("$ano-$mes_fim-01"));
            }

            // Formata as datas para o PostgreSQL (YYYY-MM-DD HH:MM:SS)
            $data_inicio = "$ano-$mes_inicio-$dia_inicio 00:00:00";
            $data_fim    = "$ano-$mes_fim-$dia_fim 23:59:59";

            // ADICIONA AO ARRAY DE CONDIÇÕES DE FORMA SEGURA
            $conditions['Post.created >='] = $data_inicio;
            $conditions['Post.created <='] = $data_fim;
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
            // Se a paginação estourar, volta pra 1
            $this->request->query['page'] = 1;
            $this->redirect(array('action' => 'index', '?' => $this->request->query));
        }

        // Mantém o formulário preenchido na View
        $this->set('currentSearch', $filtroTitulo);
        $this->set('currentStatus', $filtroStatus);
        $this->set('currentYear', $ano);
        $this->set('currentMonth', $mes);
        $this->set('currentDay', $dia);
    }

    /*
     * VIEW: Exibe um post específico
     */
    public function view($id = null) {
        // Limpa qualquer estado anterior
        $this->Post->id = null;
        
        if (!$id) {
            throw new NotFoundException(__('Post inválido'));
        }

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
        
        $this->set('post', $post);
        $this->set('title_for_layout', $post['Post']['title']);
    }

    /*
     * ADD: Adicionar Post com Anexo
     */
    public function add() {
        if ($this->request->is('post')) {
            $this->Post->create(); // Garante um modelo limpo
            $this->request->data['Post']['user_id'] = $this->Auth->user('id'); 

            if ($this->Post->save($this->request->data)) {
                $postId = $this->Post->id;
                $postSalvo = true;
                $erroAnexo = false;

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
                    // Limpa o ID antes de redirecionar
                    $this->Post->id = null;
                    return $this->redirect(array('action' => 'index'));

                } elseif ($postSalvo && $erroAnexo) {
                    $this->Flash->warning(__('O post foi salvo, mas houve um erro ao processar o anexo.'));
                    return $this->redirect(array('action' => 'edit', $postId));
                }
            } else {
                $this->Flash->error(__('Não foi possível adicionar seu post. Verifique os erros.'));
            }
        }
        $this->set('title_for_layout', 'Adicionar Post');
    }

    /*
     * EDIT: Editar Post com Validação Personalizada (Mensagem Amarela)
     */
    public function edit($id = null) {
        if (!$id) {
            throw new NotFoundException(__('Post inválido.'));
        }

        $post = $this->Post->findById($id);
        if (!$post) {
            throw new NotFoundException(__('Post inválido.'));
        }
        
        // --- REGRA PERSONALIZADA DE PERMISSÃO ---
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
                $this->Flash->success(__('Seu post foi atualizado.'));
                
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
    
    /*
     * DELETE: Excluir Post com Validação Personalizada (Mensagem Amarela)
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
        
        // --- REGRA PERSONALIZADA DE PERMISSÃO PARA EXCLUSÃO ---
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
    
    public function isOwnedBy($post, $user) {
        return $this->Post->field('id', array('id' => $post, 'user_id' => $user)) !== false;
    }
}