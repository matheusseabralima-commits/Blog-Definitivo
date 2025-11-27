--
-- PostgreSQL database cluster dump
--

\restrict 53pNDDRxHMaOpLVzijwYTYUIbSKwwET9t69ikGvSlpuadNdolApcfhJe63dalX0

SET default_transaction_read_only = off;

SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;

--
-- Drop databases (except postgres and template1)
--

DROP DATABASE cake_blog_db;




--
-- Drop roles
--

DROP ROLE cake_user;


--
-- Roles
--

CREATE ROLE cake_user;
ALTER ROLE cake_user WITH SUPERUSER INHERIT CREATEROLE CREATEDB LOGIN REPLICATION BYPASSRLS PASSWORD 'md5e784c232ba02de27f665f082e4d41da3';






\unrestrict 53pNDDRxHMaOpLVzijwYTYUIbSKwwET9t69ikGvSlpuadNdolApcfhJe63dalX0

--
-- Databases
--

--
-- Database "template1" dump
--

--
-- PostgreSQL database dump
--

\restrict 6SzzXLvf9XA9EkTK2CQHw4A915PofGYR1fdwrTig1m6WYrfoQtbw75BZL2zdQl9

-- Dumped from database version 13.23 (Debian 13.23-1.pgdg13+1)
-- Dumped by pg_dump version 13.23 (Debian 13.23-1.pgdg13+1)

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

UPDATE pg_catalog.pg_database SET datistemplate = false WHERE datname = 'template1';
DROP DATABASE template1;
--
-- Name: template1; Type: DATABASE; Schema: -; Owner: cake_user
--

CREATE DATABASE template1 WITH TEMPLATE = template0 ENCODING = 'UTF8' LOCALE = 'en_US.utf8';


ALTER DATABASE template1 OWNER TO cake_user;

\unrestrict 6SzzXLvf9XA9EkTK2CQHw4A915PofGYR1fdwrTig1m6WYrfoQtbw75BZL2zdQl9
\connect template1
\restrict 6SzzXLvf9XA9EkTK2CQHw4A915PofGYR1fdwrTig1m6WYrfoQtbw75BZL2zdQl9

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- Name: DATABASE template1; Type: COMMENT; Schema: -; Owner: cake_user
--

COMMENT ON DATABASE template1 IS 'default template for new databases';


--
-- Name: template1; Type: DATABASE PROPERTIES; Schema: -; Owner: cake_user
--

ALTER DATABASE template1 IS_TEMPLATE = true;


\unrestrict 6SzzXLvf9XA9EkTK2CQHw4A915PofGYR1fdwrTig1m6WYrfoQtbw75BZL2zdQl9
\connect template1
\restrict 6SzzXLvf9XA9EkTK2CQHw4A915PofGYR1fdwrTig1m6WYrfoQtbw75BZL2zdQl9

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- Name: DATABASE template1; Type: ACL; Schema: -; Owner: cake_user
--

REVOKE CONNECT,TEMPORARY ON DATABASE template1 FROM PUBLIC;
GRANT CONNECT ON DATABASE template1 TO PUBLIC;


--
-- PostgreSQL database dump complete
--

\unrestrict 6SzzXLvf9XA9EkTK2CQHw4A915PofGYR1fdwrTig1m6WYrfoQtbw75BZL2zdQl9

--
-- Database "cake_blog_db" dump
--

--
-- PostgreSQL database dump
--

\restrict vvTH8yF2ApfWwGIkQnqj9X4O8WndKdSMtEfFLZLZYtLcN8EF5sLXFCI3br7Hb0H

-- Dumped from database version 13.23 (Debian 13.23-1.pgdg13+1)
-- Dumped by pg_dump version 13.23 (Debian 13.23-1.pgdg13+1)

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- Name: cake_blog_db; Type: DATABASE; Schema: -; Owner: cake_user
--

CREATE DATABASE cake_blog_db WITH TEMPLATE = template0 ENCODING = 'UTF8' LOCALE = 'en_US.utf8';


ALTER DATABASE cake_blog_db OWNER TO cake_user;

\unrestrict vvTH8yF2ApfWwGIkQnqj9X4O8WndKdSMtEfFLZLZYtLcN8EF5sLXFCI3br7Hb0H
\connect cake_blog_db
\restrict vvTH8yF2ApfWwGIkQnqj9X4O8WndKdSMtEfFLZLZYtLcN8EF5sLXFCI3br7Hb0H

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: attachments; Type: TABLE; Schema: public; Owner: cake_user
--

CREATE TABLE public.attachments (
    id integer NOT NULL,
    post_id integer NOT NULL,
    filename character varying(255) NOT NULL,
    path character varying(255) NOT NULL,
    created timestamp without time zone,
    modified timestamp without time zone
);


ALTER TABLE public.attachments OWNER TO cake_user;

--
-- Name: attachments_id_seq; Type: SEQUENCE; Schema: public; Owner: cake_user
--

CREATE SEQUENCE public.attachments_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.attachments_id_seq OWNER TO cake_user;

--
-- Name: attachments_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: cake_user
--

ALTER SEQUENCE public.attachments_id_seq OWNED BY public.attachments.id;


--
-- Name: comments; Type: TABLE; Schema: public; Owner: cake_user
--

CREATE TABLE public.comments (
    id integer NOT NULL,
    post_id integer NOT NULL,
    user_id integer NOT NULL,
    body text NOT NULL,
    created timestamp without time zone,
    modified timestamp without time zone
);


ALTER TABLE public.comments OWNER TO cake_user;

--
-- Name: comments_id_seq; Type: SEQUENCE; Schema: public; Owner: cake_user
--

CREATE SEQUENCE public.comments_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.comments_id_seq OWNER TO cake_user;

--
-- Name: comments_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: cake_user
--

ALTER SEQUENCE public.comments_id_seq OWNED BY public.comments.id;


--
-- Name: posts; Type: TABLE; Schema: public; Owner: cake_user
--

CREATE TABLE public.posts (
    id integer NOT NULL,
    user_id integer NOT NULL,
    title character varying(255) NOT NULL,
    body text,
    status character varying(20) DEFAULT 'rascunho'::character varying,
    created timestamp without time zone,
    modified timestamp without time zone
);


ALTER TABLE public.posts OWNER TO cake_user;

--
-- Name: posts_id_seq; Type: SEQUENCE; Schema: public; Owner: cake_user
--

CREATE SEQUENCE public.posts_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.posts_id_seq OWNER TO cake_user;

--
-- Name: posts_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: cake_user
--

ALTER SEQUENCE public.posts_id_seq OWNED BY public.posts.id;


--
-- Name: users; Type: TABLE; Schema: public; Owner: cake_user
--

CREATE TABLE public.users (
    id integer NOT NULL,
    username character varying(100) NOT NULL,
    password character varying(255) NOT NULL,
    role character varying(20) DEFAULT 'author'::character varying,
    created timestamp without time zone,
    modified timestamp without time zone
);


ALTER TABLE public.users OWNER TO cake_user;

--
-- Name: users_id_seq; Type: SEQUENCE; Schema: public; Owner: cake_user
--

CREATE SEQUENCE public.users_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.users_id_seq OWNER TO cake_user;

--
-- Name: users_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: cake_user
--

ALTER SEQUENCE public.users_id_seq OWNED BY public.users.id;


--
-- Name: votes; Type: TABLE; Schema: public; Owner: cake_user
--

CREATE TABLE public.votes (
    id integer NOT NULL,
    post_id integer NOT NULL,
    user_id integer NOT NULL,
    vote_value integer DEFAULT 0,
    created timestamp without time zone
);


ALTER TABLE public.votes OWNER TO cake_user;

--
-- Name: votes_id_seq; Type: SEQUENCE; Schema: public; Owner: cake_user
--

CREATE SEQUENCE public.votes_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.votes_id_seq OWNER TO cake_user;

--
-- Name: votes_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: cake_user
--

ALTER SEQUENCE public.votes_id_seq OWNED BY public.votes.id;


--
-- Name: attachments id; Type: DEFAULT; Schema: public; Owner: cake_user
--

ALTER TABLE ONLY public.attachments ALTER COLUMN id SET DEFAULT nextval('public.attachments_id_seq'::regclass);


--
-- Name: comments id; Type: DEFAULT; Schema: public; Owner: cake_user
--

ALTER TABLE ONLY public.comments ALTER COLUMN id SET DEFAULT nextval('public.comments_id_seq'::regclass);


--
-- Name: posts id; Type: DEFAULT; Schema: public; Owner: cake_user
--

ALTER TABLE ONLY public.posts ALTER COLUMN id SET DEFAULT nextval('public.posts_id_seq'::regclass);


--
-- Name: users id; Type: DEFAULT; Schema: public; Owner: cake_user
--

ALTER TABLE ONLY public.users ALTER COLUMN id SET DEFAULT nextval('public.users_id_seq'::regclass);


--
-- Name: votes id; Type: DEFAULT; Schema: public; Owner: cake_user
--

ALTER TABLE ONLY public.votes ALTER COLUMN id SET DEFAULT nextval('public.votes_id_seq'::regclass);


--
-- Data for Name: attachments; Type: TABLE DATA; Schema: public; Owner: cake_user
--

COPY public.attachments (id, post_id, filename, path, created, modified) FROM stdin;
\.


--
-- Data for Name: comments; Type: TABLE DATA; Schema: public; Owner: cake_user
--

COPY public.comments (id, post_id, user_id, body, created, modified) FROM stdin;
\.


--
-- Data for Name: posts; Type: TABLE DATA; Schema: public; Owner: cake_user
--

COPY public.posts (id, user_id, title, body, status, created, modified) FROM stdin;
1	1	As Minha aplicações De PostgreSQL e Docker pra O Banco de Dados	gc	rascunho	2025-11-27 07:14:05	2025-11-27 07:14:05
2	1	Meu Código do Blog	dyc	publicado	2025-11-27 07:14:51	2025-11-27 07:14:51
3	2	Regras desse Blog	aeef	publicado	2025-11-27 07:15:37	2025-11-27 07:15:37
4	3	Estudo do MVC	jholj	publicado	2025-11-27 07:17:41	2025-11-27 07:17:41
6	1	Minhas Numéricas e Rubricas	fdf	publicado	2025-11-27 09:17:13	2025-11-27 09:17:13
7	7	Estudos de PHP	st\r\n	rascunho	2025-11-27 09:30:17	2025-11-27 09:30:17
8	3	Código Bonitos	gvd	publicado	2025-11-27 09:31:43	2025-11-27 09:31:43
9	10	Estudo do MVC	d	publicado	2025-11-27 09:38:12	2025-11-27 09:38:12
10	8	Estudo de API	ST	publicado	2025-11-27 09:39:02	2025-11-27 09:39:02
11	9	Bugs no Controller	adv	publicado	2025-11-27 09:40:21	2025-11-27 09:40:21
12	5	As Minha aplicações De PostgreSQL e Docker pra O Banco de Dados	acc	publicado	2025-11-27 09:43:13	2025-11-27 09:43:13
13	5	CakePHP	hgklyjl	publicado	2025-11-27 09:44:08	2025-11-27 09:44:08
\.


--
-- Data for Name: users; Type: TABLE DATA; Schema: public; Owner: cake_user
--

COPY public.users (id, username, password, role, created, modified) FROM stdin;
1	Madara Uchiha	454329c5488f76a9cb32c5937b8e7d01a4231a27	admin	2025-11-27 07:13:55	2025-11-27 07:13:55
2	Matheus Ivan	454329c5488f76a9cb32c5937b8e7d01a4231a27	admin	2025-11-27 07:15:17	2025-11-27 07:15:17
3	Ane Beatriz	454329c5488f76a9cb32c5937b8e7d01a4231a27	author	2025-11-27 07:15:51	2025-11-27 07:15:51
5	Miguel	454329c5488f76a9cb32c5937b8e7d01a4231a27	author	2025-11-27 07:45:18	2025-11-27 07:45:18
6	Teste	25a438437c99af6c5ddb7f221e3cc31b7eab10b1	author	2025-11-27 08:11:13	2025-11-27 08:11:13
7	Marcelo	454329c5488f76a9cb32c5937b8e7d01a4231a27	author	2025-11-27 09:29:49	2025-11-27 09:29:49
8	Teste1	454329c5488f76a9cb32c5937b8e7d01a4231a27	author	2025-11-27 09:33:55	2025-11-27 09:33:55
9	Teste2	454329c5488f76a9cb32c5937b8e7d01a4231a27	author	2025-11-27 09:34:12	2025-11-27 09:34:12
10	Teste3	454329c5488f76a9cb32c5937b8e7d01a4231a27	author	2025-11-27 09:34:28	2025-11-27 09:34:28
\.


--
-- Data for Name: votes; Type: TABLE DATA; Schema: public; Owner: cake_user
--

COPY public.votes (id, post_id, user_id, vote_value, created) FROM stdin;
\.


--
-- Name: attachments_id_seq; Type: SEQUENCE SET; Schema: public; Owner: cake_user
--

SELECT pg_catalog.setval('public.attachments_id_seq', 1, false);


--
-- Name: comments_id_seq; Type: SEQUENCE SET; Schema: public; Owner: cake_user
--

SELECT pg_catalog.setval('public.comments_id_seq', 1, false);


--
-- Name: posts_id_seq; Type: SEQUENCE SET; Schema: public; Owner: cake_user
--

SELECT pg_catalog.setval('public.posts_id_seq', 13, true);


--
-- Name: users_id_seq; Type: SEQUENCE SET; Schema: public; Owner: cake_user
--

SELECT pg_catalog.setval('public.users_id_seq', 10, true);


--
-- Name: votes_id_seq; Type: SEQUENCE SET; Schema: public; Owner: cake_user
--

SELECT pg_catalog.setval('public.votes_id_seq', 1, false);


--
-- Name: attachments attachments_pkey; Type: CONSTRAINT; Schema: public; Owner: cake_user
--

ALTER TABLE ONLY public.attachments
    ADD CONSTRAINT attachments_pkey PRIMARY KEY (id);


--
-- Name: comments comments_pkey; Type: CONSTRAINT; Schema: public; Owner: cake_user
--

ALTER TABLE ONLY public.comments
    ADD CONSTRAINT comments_pkey PRIMARY KEY (id);


--
-- Name: posts posts_pkey; Type: CONSTRAINT; Schema: public; Owner: cake_user
--

ALTER TABLE ONLY public.posts
    ADD CONSTRAINT posts_pkey PRIMARY KEY (id);


--
-- Name: users users_pkey; Type: CONSTRAINT; Schema: public; Owner: cake_user
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);


--
-- Name: users users_username_key; Type: CONSTRAINT; Schema: public; Owner: cake_user
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_username_key UNIQUE (username);


--
-- Name: votes votes_pkey; Type: CONSTRAINT; Schema: public; Owner: cake_user
--

ALTER TABLE ONLY public.votes
    ADD CONSTRAINT votes_pkey PRIMARY KEY (id);


--
-- Name: votes votes_post_id_user_id_key; Type: CONSTRAINT; Schema: public; Owner: cake_user
--

ALTER TABLE ONLY public.votes
    ADD CONSTRAINT votes_post_id_user_id_key UNIQUE (post_id, user_id);


--
-- Name: attachments attachments_post_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: cake_user
--

ALTER TABLE ONLY public.attachments
    ADD CONSTRAINT attachments_post_id_fkey FOREIGN KEY (post_id) REFERENCES public.posts(id) ON DELETE CASCADE;


--
-- Name: comments comments_post_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: cake_user
--

ALTER TABLE ONLY public.comments
    ADD CONSTRAINT comments_post_id_fkey FOREIGN KEY (post_id) REFERENCES public.posts(id) ON DELETE CASCADE;


--
-- Name: comments comments_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: cake_user
--

ALTER TABLE ONLY public.comments
    ADD CONSTRAINT comments_user_id_fkey FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: posts posts_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: cake_user
--

ALTER TABLE ONLY public.posts
    ADD CONSTRAINT posts_user_id_fkey FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: votes votes_post_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: cake_user
--

ALTER TABLE ONLY public.votes
    ADD CONSTRAINT votes_post_id_fkey FOREIGN KEY (post_id) REFERENCES public.posts(id) ON DELETE CASCADE;


--
-- Name: votes votes_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: cake_user
--

ALTER TABLE ONLY public.votes
    ADD CONSTRAINT votes_user_id_fkey FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- PostgreSQL database dump complete
--

\unrestrict vvTH8yF2ApfWwGIkQnqj9X4O8WndKdSMtEfFLZLZYtLcN8EF5sLXFCI3br7Hb0H

--
-- Database "postgres" dump
--

--
-- PostgreSQL database dump
--

\restrict cI5LMLHef5x6qtHfXVIeDe5SM64kg5UqNvhPjI8DHJjc4ZgnobpHNTROmCbYfYA

-- Dumped from database version 13.23 (Debian 13.23-1.pgdg13+1)
-- Dumped by pg_dump version 13.23 (Debian 13.23-1.pgdg13+1)

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

DROP DATABASE postgres;
--
-- Name: postgres; Type: DATABASE; Schema: -; Owner: cake_user
--

CREATE DATABASE postgres WITH TEMPLATE = template0 ENCODING = 'UTF8' LOCALE = 'en_US.utf8';


ALTER DATABASE postgres OWNER TO cake_user;

\unrestrict cI5LMLHef5x6qtHfXVIeDe5SM64kg5UqNvhPjI8DHJjc4ZgnobpHNTROmCbYfYA
\connect postgres
\restrict cI5LMLHef5x6qtHfXVIeDe5SM64kg5UqNvhPjI8DHJjc4ZgnobpHNTROmCbYfYA

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- Name: DATABASE postgres; Type: COMMENT; Schema: -; Owner: cake_user
--

COMMENT ON DATABASE postgres IS 'default administrative connection database';


--
-- PostgreSQL database dump complete
--

\unrestrict cI5LMLHef5x6qtHfXVIeDe5SM64kg5UqNvhPjI8DHJjc4ZgnobpHNTROmCbYfYA

--
-- PostgreSQL database cluster dump complete
--

