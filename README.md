# Communico — Plataforma de Mensagens Internas 💬

O **Communico** é uma plataforma moderna e segura de comunicação interna (DMs e grupos), desenvolvida como projeto académico, com foco em **usabilidade**, **organização** e **gestão** (administração de utilizadores e conteúdos).

> **Nota importante:** o projeto já tem fundações para *real-time* (ex.: Echo/Reverb) preparadas, mas **neste momento não é necessário iniciar nem configurar real-time** para executar a aplicação localmente. Este README **não inclui** comandos de execução de real-time.

---

## ✨ Funcionalidades (estado atual)

- Conversas **DM** e **Grupos**
- Gestão de grupos (inclui gestão de membros/roles conforme permissões)
- Pesquisa e filtros de conversas (recentes / grupos / favoritos)
- Fixar conversas (pin)
- Conversa de **Sistema** (ex.: menções `@sistema`)
- Upload e visualização de anexos (imagens e ficheiros)
- Autenticação e verificação de email (Laravel)
- Área de administração (gestão de utilizadores e componentes associados ao projeto)

---

## 🧰 Stack Tecnológica

- **Backend:** Laravel **12** (PHP **8.3**)
- **Frontend:** Livewire 3 + TailwindCSS + Vite
- **Base de Dados:** MySQL 8
- **Cache/Infra:** Redis
- **Email (local):** Mailpit (UI no browser)
- **Ambiente:** Docker Compose (Laravel Sail)

---

## ✅ Requisitos

### Windows
- **WSL2** ativo
- **Docker Desktop** com engine WSL2
- **Git**
- **VS Code** (recomendado)

### Linux/macOS
- Docker + Docker Compose
- Git

---

## 🚀 Instalação (do zero, com Docker)

> Todos os comandos devem ser executados **na raiz do projeto**.

### 1) Clonar o repositório
```powershell
git clone <REPOSITORY_URL> communico
cd communico
```

### 2) Instalar dependências do backend (Composer) **antes** de subir o Docker
Este passo é obrigatório porque o `docker-compose.yml` usa o runtime do Sail dentro de `vendor/`.

```powershell
docker run --rm `
  -v "${PWD}:/var/www/html" `
  -w /var/www/html `
  laravelsail/php83-composer:latest `
  composer install --ignore-platform-reqs
```

### 3) Criar `.env`
**Windows (PowerShell):**
```powershell
copy .env.example .env
```

**Linux/macOS:**
```bash
cp .env.example .env
```

#### 3.1) Ajustar variáveis essenciais para Docker (MySQL)
No ficheiro `.env`, garante pelo menos:

```env
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=communico
DB_USERNAME=sail
DB_PASSWORD=password
```

> Se o teu `.env` já vier preenchido no projeto (por exemplo num ZIP partilhado), valida apenas se os valores acima estão consistentes com o `docker-compose.yml`.

### 4) Subir serviços Docker
```powershell
docker compose up -d
```

### 5) Bootstrap da aplicação (dentro do container)
```powershell
docker compose exec laravel.test php artisan key:generate
docker compose exec laravel.test php artisan migrate:fresh --seed
docker compose exec laravel.test php artisan storage:link
docker compose exec laravel.test php artisan optimize:clear
```

### 6) Dependências do frontend + Vite (HMR)
```powershell
docker compose exec laravel.test npm install
docker compose exec laravel.test npm run dev -- --host 0.0.0.0 --port 5173
```

> Mantém o `npm run dev` a correr enquanto desenvolves (para CSS/JS/Livewire com hot reload).

---

## 🌐 Acessos Locais

- Aplicação:  
  `http://localhost`

- Mailpit (email local – caixa de entrada e preview):  
  `http://localhost:8025`

---

## 🔐 Credenciais de teste (seed)

Por omissão, os seeders criam um utilizador administrador:

- **Email:** `admin@communico.dev`  
- **Password:** `password`

> Se alterares os seeders, estas credenciais podem mudar.

---

## 🔁 Workflow diário (desenvolvimento)

### Subir containers
```powershell
docker compose up -d
```

### Correr Vite (hot reload)
```powershell
docker compose exec laravel.test npm run dev -- --host 0.0.0.0 --port 5173
```

---

## 🧪 Comandos úteis

### Reset total da BD (recriar tudo + seed)
```powershell
docker compose exec laravel.test php artisan migrate:fresh --seed
```

### Correr um seeder específico
> **PowerShell:** usa **uma** barra invertida no namespace.

```powershell
docker compose exec laravel.test php artisan db:seed --class="Database\Seeders\AutoReplySeeder"
```

### Limpar caches (quando algo “fica estranho”)
```powershell
docker compose exec laravel.test php artisan optimize:clear
```

### Parar serviços
```powershell
docker compose down
```

### Parar e limpar volumes (apaga BD local)
```powershell
docker compose down -v
```

---

## 🧯 Troubleshooting rápido

### Erro: `Target class [Database\\Seeders\\AutoReplySeeder] does not exist`
Isto acontece tipicamente quando é passado o namespace com **dupla barra** (`\\`) no PowerShell.

✅ Correto (PowerShell):
```powershell
docker compose exec laravel.test php artisan db:seed --class="Database\Seeders\AutoReplySeeder"
```

Se ainda assim falhar:
```powershell
docker compose exec laravel.test composer dump-autoload
docker compose exec laravel.test php artisan optimize:clear
```

### Build do Docker falha por falta de `vendor/`
Garante que fizeste o passo **2)** (`composer install`) **antes** de `docker compose up -d`.

---

## 📄 Licença / Contexto
Projeto académico. Uso interno e educativo.
