-- 1. LIMPEZA (Remove tabelas antigas para evitar conflito)
DROP TABLE IF EXISTS votes CASCADE;
DROP TABLE IF EXISTS attachments CASCADE;
DROP TABLE IF EXISTS comments CASCADE;
DROP TABLE IF EXISTS posts CASCADE;
DROP TABLE IF EXISTS users CASCADE;

-- 2. ESTRUTURA (Cria as tabelas novamente)

CREATE TABLE public.users (
    id SERIAL PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(20) DEFAULT 'author',
    created TIMESTAMP,
    modified TIMESTAMP
);

CREATE TABLE public.posts (
    id SERIAL PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    body TEXT,
    status VARCHAR(20) DEFAULT 'rascunho',
    created TIMESTAMP,
    modified TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE public.comments (
    id SERIAL PRIMARY KEY,
    post_id INT NOT NULL,
    user_id INT NOT NULL,
    body TEXT NOT NULL,
    created TIMESTAMP,
    modified TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE public.attachments (
    id SERIAL PRIMARY KEY,
    post_id INT NOT NULL,
    filename VARCHAR(255) NOT NULL,
    path VARCHAR(255) NOT NULL,
    created TIMESTAMP,
    modified TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE
);

CREATE TABLE public.votes (
    id SERIAL PRIMARY KEY,
    post_id INT NOT NULL,
    user_id INT NOT NULL,
    vote_value INT DEFAULT 0,
    created TIMESTAMP,
    UNIQUE(post_id, user_id),
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- 3. DADOS (Usando INSERT para garantir que funcione)

-- Usuários
INSERT INTO public.users (id, username, password, role, created, modified) VALUES
(1, 'Madara Uchiha', '454329c5488f76a9cb32c5937b8e7d01a4231a27', 'admin', '2025-11-27 07:13:55', '2025-11-27 07:13:55'),
(2, 'Matheus Ivan', '454329c5488f76a9cb32c5937b8e7d01a4231a27', 'admin', '2025-11-27 07:15:17', '2025-11-27 07:15:17'),
(3, 'Ane Beatriz', '454329c5488f76a9cb32c5937b8e7d01a4231a27', 'author', '2025-11-27 07:15:51', '2025-11-27 07:15:51'),
(5, 'Miguel', '454329c5488f76a9cb32c5937b8e7d01a4231a27', 'author', '2025-11-27 07:45:18', '2025-11-27 07:45:18'),
(6, 'Teste', '25a438437c99af6c5ddb7f221e3cc31b7eab10b1', 'author', '2025-11-27 08:11:13', '2025-11-27 08:11:13'),
(7, 'Marcelo', '454329c5488f76a9cb32c5937b8e7d01a4231a27', 'author', '2025-11-27 09:29:49', '2025-11-27 09:29:49'),
(8, 'Teste1', '454329c5488f76a9cb32c5937b8e7d01a4231a27', 'author', '2025-11-27 09:33:55', '2025-11-27 09:33:55'),
(9, 'Teste2', '454329c5488f76a9cb32c5937b8e7d01a4231a27', 'author', '2025-11-27 09:34:12', '2025-11-27 09:34:12'),
(10, 'Teste3', '454329c5488f76a9cb32c5937b8e7d01a4231a27', 'author', '2025-11-27 09:34:28', '2025-11-27 09:34:28'),
(11, 'Teste4', '454329c5488f76a9cb32c5937b8e7d01a4231a27', 'author', '2025-11-27 10:17:44', '2025-11-27 10:17:44'),
(12, 'Test', '25a438437c99af6c5ddb7f221e3cc31b7eab10b1', 'author', '2025-11-27 11:10:22', '2025-11-27 11:10:22');

-- Posts
INSERT INTO public.posts (id, user_id, title, body, status, created, modified) VALUES
(1, 1, 'As Minha aplicações De PostgreSQL e Docker pra O Banco de Dados gc', 'rascunho', 'rascunho', '2025-11-27 07:14:05', '2025-11-27 07:14:05'),
(2, 1, 'Meu Código do Blog', 'dyc', 'publicado', '2025-11-27 07:14:51', '2025-11-27 07:14:51'),
(3, 2, 'Regras desse Blog', 'aeef', 'publicado', '2025-11-27 07:15:37', '2025-11-27 07:15:37'),
(4, 3, 'Estudo do MVC', 'jholj', 'publicado', '2025-11-27 07:17:41', '2025-11-27 07:17:41'),
(6, 1, 'Minhas Numéricas e Rubricas fdf', 'publicado', 'publicado', '2025-11-27 09:17:13', '2025-11-27 09:17:13'),
(7, 7, 'Estudos de PHP', 'st\r\n', 'rascunho', '2025-11-27 09:30:17', '2025-11-27 09:30:17'),
(8, 3, 'Código Bonitos', 'gvd', 'publicado', '2025-11-27 09:31:43', '2025-11-27 09:31:43'),
(9, 10, 'Estudo do MVC', 'd', 'publicado', '2025-11-27 09:38:12', '2025-11-27 09:38:12'),
(10, 8, 'Estudo de API', 'ST', 'publicado', '2025-11-27 09:39:02', '2025-11-27 09:39:02'),
(11, 9, 'Bugs no Controller', 'adv', 'publicado', '2025-11-27 09:40:21', '2025-11-27 09:40:21'),
(12, 5, 'As Minha aplicações De PostgreSQL e Docker pra O Banco de Dados acc', 'publicado', 'publicado', '2025-11-27 09:43:13', '2025-11-27 09:43:13'),
(13, 5, 'CakePHP hgklyjl', 'publicado', 'publicado', '2025-11-27 09:44:08', '2025-11-27 09:44:08');

-- 4. AJUSTES FINAIS (Corrige os contadores de ID para que novos posts não deem erro)

-- Ajusta a sequência para o maior ID + 1
SELECT pg_catalog.setval('public.users_id_seq', 13, true);
SELECT pg_catalog.setval('public.posts_id_seq', 14, true);
SELECT pg_catalog.setval('public.comments_id_seq', 1, false);
SELECT pg_catalog.setval('public.attachments_id_seq', 1, false);
SELECT pg_catalog.setval('public.votes_id_seq', 1, false);