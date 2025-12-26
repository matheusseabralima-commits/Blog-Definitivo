<div class="container-fluid mt-4">
    
    <!-- MENU SUPERIOR ADAPTATIVO -->
    <div class="row align-items-center mb-4 border-bottom pb-3">
        <div class="col-auto">
            <div class="d-none d-md-flex gap-2">
                <span class="text-muted small fw-bold text-uppercase me-2 align-self-center">Navegação:</span>
                <?php echo $this->Html->link('<i class="bi bi-house-door-fill"></i> Dashboard', 
                    array('controller' => 'posts', 'action' => 'index'), 
                    array('class' => 'btn btn-sm btn-secondary', 'escape' => false)); 
                ?>
                <?php if ($this->Session->read('Auth.User')): ?>
                    <?php echo $this->Html->link('<i class="bi bi-plus-circle-fill"></i> Novo Post', 
                        array('action' => 'add'), 
                        array('class' => 'btn btn-sm btn-primary', 'escape' => false)); 
                    ?>
                <?php endif; ?>
            </div>
            <!-- Menu Mobile -->
            <div class="d-md-none">
                <div class="dropdown">
                    <button class="btn btn-light border" type="button" id="mobileMenuBtn" data-bs-toggle="dropdown"><i class="bi bi-list fs-4"></i></button>
                    <ul class="dropdown-menu shadow border-0">
                        <li><?php echo $this->Html->link('Dashboard', array('action' => 'index'), array('class' => 'dropdown-item')); ?></li>
                        <?php if ($this->Session->read('Auth.User')): ?>
                        <li><?php echo $this->Html->link('Novo Post', array('action' => 'add'), array('class' => 'dropdown-item')); ?></li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col d-flex justify-content-end align-items-center">
             <h1 class="h5 text-dark m-0 fw-bold">Gerenciar Posts</h1>
        </div>
    </div>

    <div class="row">
        <main role="main" class="col-12">

            <!-- --- FILTROS (URL Limpa + Mantém Valores) --- -->
            <div class="card mb-4 bg-light border-0 shadow-sm">
                <div class="card-body">
                    <!-- Method POST esconde os dados da URL -->
                    <form action="<?php echo $this->Html->url(array('action' => 'index')); ?>" method="post" class="row g-3">
                        
                        <!-- 1. Busca Texto -->
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-secondary">Título ou Conteúdo</label>
                            <!-- AQUI: Preenche o value com a variável $searchQuery vinda do Controller -->
                            <input type="text" name="search_query" class="form-control" placeholder="Digite para buscar..." 
                                   value="<?php echo isset($searchQuery) ? h($searchQuery) : ''; ?>">
                        </div>

                        <!-- 2. Status -->
                        <div class="col-md-2">
                            <label class="form-label fw-bold small text-secondary">Status</label>
                            <select name="filter_status" class="form-select">
                                <option value="">Todos</option>
                                <?php 
                                    $st = isset($filterStatus) ? $filterStatus : ''; 
                                ?>
                                <option value="publicado" <?php echo ($st == 'publicado') ? 'selected' : ''; ?>>Publicado</option>
                                <option value="rascunho" <?php echo ($st == 'rascunho') ? 'selected' : ''; ?>>Rascunho</option>
                                <option value="arquivado" <?php echo ($st == 'arquivado') ? 'selected' : ''; ?>>Arquivado</option>
                            </select>
                        </div>

                        <!-- 3. Data -->
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-secondary">Data de Criação</label>
                            <div class="input-group">
                                <?php 
                                    $curY = isset($year) ? $year : '';
                                    $curM = isset($month) ? $month : '';
                                    $curD = isset($day) ? $day : '';
                                ?>
                                <select name="year" id="filterYear" class="form-select" onchange="updateDays()">
                                    <option value="">Ano</option>
                                    <?php 
                                    $startYear = 2019;
                                    $endYear = date('Y'); 
                                    for ($i = $endYear; $i >= $startYear; $i--) {
                                        // Verifica se deve selecionar
                                        $sel = ($curY == $i) ? 'selected' : '';
                                        echo "<option value='$i' $sel>$i</option>";
                                    }
                                    ?>
                                </select>

                                <select name="month" id="filterMonth" class="form-select" onchange="updateDays()">
                                    <option value="">Mês</option>
                                    <?php 
                                    $meses = array(1 => 'Jan', 2 => 'Fev', 3 => 'Mar', 4 => 'Abr', 5 => 'Mai', 6 => 'Jun', 7 => 'Jul', 8 => 'Ago', 9 => 'Set', 10 => 'Out', 11 => 'Nov', 12 => 'Dez');
                                    foreach ($meses as $num => $nome) {
                                        $sel = ($curM == $num) ? 'selected' : '';
                                        echo "<option value='$num' $sel>$nome</option>";
                                    }
                                    ?>
                                </select>

                                <select name="day" id="filterDay" class="form-select">
                                    <option value="">Dia</option>
                                    <!-- Dias preenchidos via JS -->
                                </select>
                            </div>
                        </div>

                        <!-- Botões -->
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100 me-2"><i class="bi bi-search"></i></button>
                            <!-- Botão Limpar com parâmetro GET clear=1 para o controller limpar a sessão -->
                            <a href="<?php echo $this->Html->url(array('action' => 'index', '?' => array('clear' => 1))); ?>" class="btn btn-secondary" title="Limpar Filtros"><i class="bi bi-x-lg"></i></a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- --- LISTAGEM DE POSTS --- -->
            <?php echo $this->Flash->render(); ?>

            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                <?php foreach ($posts as $post): ?>
                    <div class="col">
                        <div class="card h-100 border-0 shadow-sm post-card">
                            <?php 
                                $statusColors = array(
                                    'publicado' => '#28a745',
                                    'rascunho' => '#6c757d',
                                    'arquivado' => '#ffc107'
                                );
                                $statusKey = strtolower($post['Post']['status']);
                                if ($statusKey == 'draft') $statusKey = 'rascunho';
                                if ($statusKey == 'published') $statusKey = 'publicado';
                                $statusColor = isset($statusColors[$statusKey]) ? $statusColors[$statusKey] : '#6c757d';
                                $statusLabel = ucfirst($statusKey);
                            ?>
                            <div style="height: 5px; background-color: <?php echo $statusColor; ?>; border-radius: 15px 15px 0 0;"></div>
                            
                            <div class="card-body d-flex flex-column">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <span class="badge rounded-pill bg-light text-dark border">
                                        #<?php echo h($post['Post']['id']); ?>
                                    </span>
                                    <span class="badge rounded-pill" style="background-color: <?php echo $statusColor; ?>; color: white;">
                                        <?php echo h($statusLabel); ?>
                                    </span>
                                </div>

                                <h5 class="card-title fw-bold text-dark mb-3">
                                    <?php echo $this->Text->truncate(h($post['Post']['title']), 40, array('ellipsis' => '...')); ?>
                                </h5>

                                <div class="text-muted small mb-3">
                                    <i class="bi bi-calendar-event"></i> <?php echo date('d/m/Y', strtotime($post['Post']['created'])); ?>
                                    <span class="mx-1">•</span>
                                    <i class="bi bi-person"></i> <?php echo h($post['User']['username']); ?>
                                </div>

                                <div class="mt-auto pt-3 border-top d-flex justify-content-between">
                                    <?php echo $this->Html->link('<i class="bi bi-eye"></i> Ler', 
                                        array('action' => 'view', $post['Post']['id']), 
                                        array('class' => 'btn btn-sm btn-outline-primary border-0', 'escape' => false)); 
                                    ?>
                                    
                                    <?php 
                                        $currentUser = $this->Session->read('Auth.User');
                                        if ($currentUser && ($currentUser['role'] === 'admin' || $currentUser['id'] == $post['Post']['user_id'])): 
                                    ?>
                                    <div>
                                        <?php echo $this->Html->link('<i class="bi bi-pencil-square"></i>', 
                                            array('action' => 'edit', $post['Post']['id']), 
                                            array('class' => 'btn btn-sm btn-light text-primary', 'escape' => false, 'title' => 'Editar')); 
                                        ?>
                                        <?php echo $this->Form->postLink('<i class="bi bi-trash"></i>', 
                                            array('action' => 'delete', $post['Post']['id']), 
                                            array('class' => 'btn btn-sm btn-light text-danger', 'escape' => false, 'confirm' => 'Tem certeza?')); 
                                        ?>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php if (empty($posts)): ?>
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                    <p>Nenhum post encontrado.</p>
                </div>
            <?php endif; ?>

            <!-- Paginação -->
            <nav class="mt-5 mb-4">
                <ul class="pagination justify-content-center">
                    <?php
                        echo $this->Paginator->prev('«', array('class' => 'page-item', 'tag' => 'li'), null, array('class' => 'page-item disabled', 'tag' => 'li', 'disabledTag' => 'span'));
                        echo $this->Paginator->numbers(array('class' => 'page-item', 'tag' => 'li', 'separator' => '', 'currentClass' => 'active', 'currentTag' => 'span'));
                        echo $this->Paginator->next('»', array('class' => 'page-item', 'tag' => 'li'), null, array('class' => 'page-item disabled', 'tag' => 'li', 'disabledTag' => 'span'));
                    ?>
                </ul>
            </nav>

        </main>
    </div>
</div>

<script>
    // Recupera o dia selecionado vindo do PHP
    var selectedDay = "<?php echo isset($curD) ? $curD : ''; ?>";

    function updateDays() {
        var yearSelect = document.getElementById("filterYear");
        var monthSelect = document.getElementById("filterMonth");
        var daySelect = document.getElementById("filterDay");
        
        var year = yearSelect.value;
        var month = monthSelect.value;

        // Se o usuário selecionou algo manualmente agora, usa isso. 
        // Se não, usa o que veio do banco (selectedDay).
        var currentVal = daySelect.value;
        if(!currentVal && selectedDay) currentVal = selectedDay;

        daySelect.innerHTML = '<option value="">Dia</option>';

        if (month !== "") {
            if (year === "") year = new Date().getFullYear();
            var daysInMonth = new Date(year, month, 0).getDate();

            for (var i = 1; i <= daysInMonth; i++) {
                var option = document.createElement("option");
                option.value = i;
                option.text = i;
                if (i == currentVal) option.selected = true;
                daySelect.appendChild(option);
            }
        }
    }

    // Executa ao carregar para repopular os dias
    window.onload = function() {
        updateDays();
    };
</script>

<style>
    /* Estilos Adicionais */
    .post-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        border-radius: 15px;
    }
    .post-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
    }
    .page-item.active span {
        background-color: #5e3573;
        border-color: #5e3573;
        color: white;
    }
    .page-item a {
        color: #5e3573;
    }
    .page-item a:hover {
        color: #4a2a5a;
        background-color: #e9ecef;
    }
    .dropdown-item:active, .dropdown-item:hover {
        background-color: #f8f9fa;
        color: #5e3573;
    }
</style>