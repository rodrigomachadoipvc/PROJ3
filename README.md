# Communico — Internal Messaging Platform 💬

Communico is a modern and secure internal chat platform built with **Laravel 11**, **Livewire**, **TailwindCSS**, and **Docker**.  
This guide explains how to set up the complete development environment on **Windows** using **Docker + WSL2**.

---

## 📦 Requirements (Windows)

Before starting, ensure the following tools are installed:

### ✔ WSL 2 (Windows Subsystem for Linux)
Open PowerShell **as Administrator** and run:
```powershell
wsl --install
```
Restart your computer when prompted.

### ✔ Docker Desktop
Install Docker Desktop and enable:
```
Settings → General → "Use the WSL 2 based engine"
```

### ✔ Git  
### ✔ Visual Studio Code

---

## 🚀 Installation Guide (From Zero)

All commands below must be executed **inside the project folder**.

---

### 1. Clone the Repository
```powershell
git clone <REPOSITORY_URL> communico
cd communico
```

---

### 2. Install Backend Dependencies (via Docker)

Because the `vendor/` folder is not included in the repository, install Laravel dependencies using a temporary container:

```powershell
docker run --rm -v "${PWD}:/var/www/html" -w /var/www/html laravelsail/php83-composer:latest composer install --ignore-platform-reqs
```

---

### 3. Environment Setup

Copy the example environment file:
```powershell
copy .env.example .env
```

If your team uses Laravel Reverb or other external services, add those keys to `.env`.

---

### 4. Start the Docker Services

```powershell
docker compose up -d
```

This starts:
- PHP 8.3  
- Nginx  
- MySQL  
- Redis (if enabled)

---

### 5. Application Bootstrap (Inside the Container)

Run the following commands:

#### 🔑 Generate Application Key
```powershell
docker compose exec laravel.test php artisan key:generate
```

#### 🗄 Run Migrations + Seeders (Creates all tables and default admin user)
```powershell
docker compose exec laravel.test php artisan migrate:fresh --seed
```

#### 📚 Install Frontend Dependencies (Node + Vite)
```powershell
docker compose exec laravel.test npm install
```

---

## 💻 Daily Development Workflow

When working on the project, you only need two terminals:

---

### **Terminal 1 — Start Backend Services**
```powershell
docker compose up -d
```

---

### **Terminal 2 — Start Frontend (Vite Hot Reload)**
```powershell
docker compose exec laravel.test npm run dev
```

This must remain running for CSS/JS and Livewire updates to work.

---

## 🎉 You're Ready to Develop

Access the app at:

```
http://localhost
```

Default seeded admin credentials:
```
Email: admin@admin.com
Password: password
```

---

## 🧰 Tech Stack

- **Backend:** Laravel 11 (PHP 8.3)
- **Frontend:** TailwindCSS + Livewire 3 + Vite
- **Database:** MySQL  
- **Environment:** Docker / Laravel Sail  
- **Authentication:** Email verification  
- **Messaging:** Real-time foundations prepared for future Laravel Reverb integration

---

## 👥 Contributing

Pull Requests and feature branches must follow your team's workflow.  
Please ensure you run the linter and test the environment before submitting changes.

---

## © Communico — Internal Chat Platform
Built for academic and enterprise collaboration.
