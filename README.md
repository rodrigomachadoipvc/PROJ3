Communico - Plataforma de Chat Interno 💬

Bem-vindo ao repositório do Communico. Este guia explica como configurar o projeto do zero no Windows.

📋 Pré-requisitos (Windows)

WSL 2 (Windows Subsystem for Linux):

Abra o PowerShell como Administrador e corra: wsl --install (e reinicie o PC).

Docker Desktop:

Instale e abra o Docker Desktop.

Vá a Settings > General e ative "Use the WSL 2 based engine".

Git e VS Code.

🚀 Instalação Passo a Passo

1. Clonar o Repositório

No PowerShell, navegue para a pasta desejada e clone o projeto:

git clone <URL_DO_GITHUB_REPO> communico
cd communico


⚠️ IMPORTANTE: Todos os comandos seguintes devem ser executados DENTRO da pasta communico.

2. Instalar Dependências (Backend)

Como não tens a pasta vendor (onde está o Laravel e o Sail), vamos usar o Docker para a criar:

docker run --rm -v "${PWD}:/var/www/html" -w /var/www/html laravelsail/php83-composer:latest composer install --ignore-platform-reqs


(Isto pode demorar alguns minutos).

3. Configurar o Ambiente

Copia o ficheiro de exemplo:

copy .env.example .env


Nota: Se precisares das chaves do Reverb para o chat, pede-as a um colega e cola-as no teu ficheiro .env.

4. Arrancar os Servidores

Em vez de usar o comando sail, usa o Docker Compose diretamente:

docker compose up -d


5. Configuração Final (Dentro do Contentor)

Com os servidores a correr, executa estes comandos para configurar a base de dados e a aplicação:

# 1. Gerar Chave de Encriptação
docker compose exec laravel.test php artisan key:generate

# 2. Criar Base de Dados e Admin
docker compose exec laravel.test php artisan migrate:fresh --seed

# 3. Instalar Frontend
docker compose exec laravel.test npm install


💻 Como Executar no Dia-a-Dia

Sempre que quiseres trabalhar no projeto, abre o VS Code na pasta communico e usa dois terminais:

Terminal 1: Ligar o Backend

docker compose up -d


Terminal 2: Ligar o Frontend (Vite)
Este comando deve ficar a correr para o site ter estilo (CSS/JS):

docker compose exec laravel.test npm run dev