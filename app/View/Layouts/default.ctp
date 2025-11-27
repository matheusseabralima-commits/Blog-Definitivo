<!DOCTYPE html>
<html lang="pt-br">
<head>
    <?php echo $this->Html->charset(); ?>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>
        <?php echo "Manual do Dev PHP" . (isset($title_for_layout) ? " | " . $title_for_layout : ""); ?>
    </title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <!-- CSS do Cake -->
    <?php echo $this->Html->css('cake.generic'); ?>

    <?php
        echo $this->Html->meta('icon');
        echo $this->fetch('meta');
        echo $this->fetch('css');
        echo $this->fetch('script');
    ?>

    <style>
        /* Estilo do Botão de Menu na Barra Preta */
        .btn-menu-toggle {
            background: transparent;
            border: none;
            color: rgba(255, 255, 255, 0.9);
            font-size: 1.8rem; /* Tamanho do ícone */
            padding: 0 10px 0 0; /* Espaço entre o ícone e o título */
            line-height: 1;
            transition: color 0.2s;
            cursor: pointer;
        }
        .btn-menu-toggle:hover {
            color: #ffffff;
        }

        /* Estilo da Gaveta Lateral (Offcanvas) */
        .offcanvas-custom {
            border-right: none;
        }
        .offcanvas-header-custom {
            background-color: #2c1b31; /* Roxo Escuro */
            color: white;
        }
        
        /* Links do Menu */
        .menu-link {
            display: flex;
            align-items: center;
            padding: 15px 20px;
            color: #333;
            text-decoration: none;
            font-weight: 600;
            font-size: 1.1rem;
            border-bottom: 1px solid #f0f0f0;
            transition: all 0.2s;
        }
        .menu-link:hover {
            background-color: #f8f9fa;
            color: #5e3573; /* Roxo Destaque */
            padding-left: 25px; /* Efeito de deslizar levemente */
        }
        .menu-link i {
            font-size: 1.3rem;
            margin-right: 15px;
            color: #5e3573;
            width: 25px;
            text-align: center;
        }
        
        /* Link de Sair (Vermelho) */
        .menu-link.logout:hover {
            background-color: #fff5f5;
            color: #dc3545;
        }
        .menu-link.logout i {
            color: #dc3545;
        }
    </style>
</head>
<body class="bg-light"> 
    
    <!-- BARRA FIXA NO TOPO -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-black fixed-top shadow-sm" style="background-color: #2c1b31;">
        <div class="container-fluid px-3">
            
            <!-- LADO ESQUERDO: Botão Menu + Título -->
            <div class="d-flex align-items-center">
                
                <!-- 1. O BOTÃO QUE ABRE TUDO -->
                <!-- data-bs-toggle="offcanvas" faz a mágica acontecer -->
                <button class="btn-menu-toggle" type="button" data-bs-toggle="offcanvas" data-bs-target="#menuGeral" aria-controls="menuGeral">
                    <i class="bi bi-list"></i>
                </button>

                <!-- Título do Site -->
                <?php echo $this->Html->link('Manual do Dev PHP', '/', array('class' => 'navbar-brand fw-bold m-0', 'style' => 'font-size: 1.1rem;')); ?>
            </div>
            
            <!-- LADO DIREITO: Apenas Sair (Visível apenas no Desktop para não poluir mobile) -->
            <div class="d-none d-md-flex align-items-center ms-auto"> 
               <?php 
                $usuarioLogado = $this->Session->read('Auth.User');
                if ($usuarioLogado): 
               ?>
                   <div class="text-white me-3 small opacity-75">
                       <i class="bi bi-person-circle me-1"></i> <?php echo h($usuarioLogado['username']); ?>
                   </div>
                   <?php echo $this->Html->link(
                       'Sair', 
                       array('controller' => 'users', 'action' => 'logout'), 
                       array('class' => 'btn btn-sm btn-outline-light rounded-pill px-3', 'escape' => false)
                   ); ?>
               <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- MENU LATERAL (A GAVETA QUE ABRE) -->
    <div class="offcanvas offcanvas-start offcanvas-custom" tabindex="-1" id="menuGeral" aria-labelledby="menuGeralLabel" style="width: 85%; max-width: 320px;">
        
        <!-- Cabeçalho do Menu -->
        <div class="offcanvas-header offcanvas-header-custom">
            <h5 class="offcanvas-title fw-bold" id="menuGeralLabel">
                <i class="bi bi-grid-1x2-fill me-2"></i> Menu
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Fechar"></button>
        </div>
        
        <!-- Corpo do Menu -->
        <div class="offcanvas-body p-0">
            
            <!-- Perfil do Usuário (Se logado) -->
            <?php if ($usuarioLogado): ?>
                <div class="p-4 bg-light border-bottom text-center">
                    <div class="rounded-circle bg-white d-inline-flex align-items-center justify-content-center shadow-sm mb-2 text-custom-purple fw-bold" 
                         style="width: 70px; height: 70px; font-size: 2rem; color: #5e3573; border: 2px solid #e9ecef;">
                        <?php echo strtoupper(substr($usuarioLogado['username'], 0, 1)); ?>
                    </div>
                    <h5 class="m-0 fw-bold text-dark mt-2"><?php echo h($usuarioLogado['username']); ?></h5>
                    <span class="badge bg-secondary mt-1 px-3 rounded-pill">
                        <?php echo h(ucfirst($usuarioLogado['role'])); ?>
                    </span>
                </div>
            <?php else: ?>
                <div class="p-4 bg-light border-bottom text-center">
                    <h6 class="text-muted m-0">Bem-vindo, Visitante!</h6>
                </div>
            <?php endif; ?>

            <!-- Lista de Opções -->
            <nav class="nav flex-column">
                
                <!-- Início -->
                <?php echo $this->Html->link('<i class="bi bi-house-door-fill"></i> Início', 
                    '/', 
                    array('class' => 'menu-link', 'escape' => false)); 
                ?>
                
                <!-- Dashboard -->
                <?php echo $this->Html->link('<i class="bi bi-grid-fill"></i> Dashboard (Posts)', 
                    array('controller' => 'posts', 'action' => 'index'), 
                    array('class' => 'menu-link', 'escape' => false)); 
                ?>
                
                <!-- Novo Post (Só Logado) -->
                <?php if ($usuarioLogado): ?>
                    <?php echo $this->Html->link('<i class="bi bi-plus-circle-fill"></i> Criar Novo Post', 
                        array('controller' => 'posts', 'action' => 'add'), 
                        array('class' => 'menu-link', 'escape' => false)); 
                    ?>
                <?php endif; ?>

                <!-- Usuários (Só Admin) -->
                <?php if ($usuarioLogado && $usuarioLogado['role'] == 'admin'): ?>
                    <?php echo $this->Html->link('<i class="bi bi-people-fill"></i> Gerenciar Usuários', 
                        array('controller' => 'users', 'action' => 'index'), 
                        array('class' => 'menu-link', 'escape' => false)); 
                    ?>
                <?php endif; ?>

                <!-- Login / Sair -->
                <?php if ($usuarioLogado): ?>
                    <div class="border-top mt-2"></div>
                    <?php echo $this->Html->link('<i class="bi bi-box-arrow-right"></i> Sair do Sistema', 
                        array('controller' => 'users', 'action' => 'logout'), 
                        array('class' => 'menu-link logout', 'escape' => false)); 
                    ?>
                <?php else: ?>
                    <div class="border-top mt-2"></div>
                    <?php echo $this->Html->link('<i class="bi bi-box-arrow-in-right"></i> Login / Cadastrar', 
                        array('controller' => 'users', 'action' => 'login'), 
                        array('class' => 'menu-link', 'escape' => false)); 
                    ?>
                <?php endif; ?>
            </nav>
        </div>
    </div>

    <!-- CONTEÚDO PRINCIPAL -->
    <main class="container-fluid px-0">
        <!-- Espaço para compensar a barra fixa -->
        <div style="padding-top: 60px;">
            <div class="container px-3 pt-3">
                <?php echo $this->Session->flash(); ?>
            </div>
            
            <!-- Aqui carrega o conteúdo das views (index, add, etc) -->
            <?php echo $this->fetch('content'); ?>
        </div>
    </main>

    <!-- JS Bootstrap (Necessário para o menu funcionar) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>