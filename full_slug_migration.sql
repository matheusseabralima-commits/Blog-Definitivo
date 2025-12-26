-- 1. POSTS: Cria coluna, preenche e trava
ALTER TABLE posts ADD COLUMN IF NOT EXISTS slug VARCHAR(255);

-- Preenche posts antigos: "Meu Título" vira "meu-titulo-1" (ID garante unicidade)
UPDATE posts 
SET slug = lower(replace(title, ' ', '-')) || '-' || id 
WHERE slug IS NULL;

ALTER TABLE posts ALTER COLUMN slug SET NOT NULL;
-- Remove constraint se existir para evitar erro ao rodar 2x, depois recria
ALTER TABLE posts DROP CONSTRAINT IF EXISTS posts_slug_unique;
ALTER TABLE posts ADD CONSTRAINT posts_slug_unique UNIQUE (slug);


-- 2. USERS: Cria coluna, preenche e trava
ALTER TABLE users ADD COLUMN IF NOT EXISTS slug VARCHAR(255);

-- Preenche users antigos: "Miguel Silva" vira "miguel-silva-5"
UPDATE users 
SET slug = lower(replace(username, ' ', '-')) || '-' || id 
WHERE slug IS NULL;

ALTER TABLE users ALTER COLUMN slug SET NOT NULL;
-- Remove constraint se existir para evitar erro ao rodar 2x, depois recria
ALTER TABLE users DROP CONSTRAINT IF EXISTS users_slug_unique;
ALTER TABLE users ADD CONSTRAINT users_slug_unique UNIQUE (slug);