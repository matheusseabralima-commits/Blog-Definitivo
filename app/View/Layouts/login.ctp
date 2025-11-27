<!DOCTYPE html>
<html lang="pt-br">
<head>
    <?php echo $this->Html->charset(); ?>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Acesso - Manual do Dev</title>
    
    <!-- Bootstrap 5 e Ícones -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <style>
        /* --- CONFIGURAÇÃO GLOBAL --- */
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
        }

        body {
            /* Fundo Roxo com Imagem */
            background: linear-gradient(rgba(44, 27, 49, 0.95), rgba(44, 27, 49, 0.9)), url('/img/image.png');
            background-size: cover;
            background-attachment: fixed;
            background-position: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow-y: auto; /* Permite rolagem */
        }

        /* --- WRAPPER (CORREÇÃO DO SCROLL MOBILE) --- */
        .login-wrapper {
            min-height: 100%;
            display: flex;
            align-items: center;     /* Centraliza Verticalmente */
            justify-content: center; /* Centraliza Horizontalmente */
            padding: 30px 15px;      /* Espaço seguro nas bordas */
            box-sizing: border-box;
        }

        /* --- CARD --- */
        .card-login {
            width: 100%;
            max-width: 400px;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 15px 40px rgba(0,0,0,0.5);
            background-color: #fff;
            position: relative;
        }

        .login-header {
            background-color: #2c1b31;
            padding: 30px 20px;
            text-align: center;
            color: white;
            border-bottom: 4px solid #4a2c52;
        }

        /* --- ESTILO DOS INPUTS (Para unir com os ícones) --- */
        /* Ícone da esquerda */
        .input-group > .input-group-text:first-child { 
            border-radius: 10px 0 0 10px; 
            border-right: none;
        }
        
        /* Input do meio */
        .input-group > .form-control { 
            border-radius: 0; 
            border-left: none;
            border-right: none;
        }
        .input-group > .form-control:focus {
            box-shadow: none;
            border-color: #dee2e6;
        }

        /* Botão do olho (direita) e Ícones da direita */
        .input-group > .input-group-text:last-child,
        .btn-toggle-password {
            border-radius: 0 10px 10px 0;
            border: 1px solid #dee2e6;
            border-left: none;
            background-color: #f8f9fa;
        }

        /* Botão do Olho Específico */
        .btn-toggle-password {
            background-color: white;
            color: #6c757d;
            cursor: pointer;
            z-index: 5;
        }
        .btn-toggle-password:hover {
            background-color: #f1f1f1;
            color: #2c1b31;
        }
        
        /* Select do Perfil */
        .form-select {
            border-radius: 0 10px 10px 0;
        }

        /* --- MODO MOBILE (Destrava o centro se a tela for pequena) --- */
        @media (max-height: 600px) {
            .login-wrapper {
                align-items: flex-start;
                padding-top: 20px;
                padding-bottom: 20px;
            }
        }
    </style>
</head>
<body>
    
    <div class="login-wrapper">
        <?php echo $this->fetch('content'); ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- SCRIPT PARA MOSTRAR/ESCONDER SENHA -->
    <script>
        function togglePassword(inputId, iconId) {
            var input = document.getElementById(inputId);
            var icon = document.getElementById(iconId);

            if (input.type === "password") {
                input.type = "text";
                icon.classList.remove("bi-eye");
                icon.classList.add("bi-eye-slash");
            } else {
                input.type = "password";
                icon.classList.remove("bi-eye-slash");
                icon.classList.add("bi-eye");
            }
        }
    </script>
</body>
</html>