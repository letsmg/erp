<img src="https://raw.githubusercontent.com/letsmg/erp-vue/main/pacman-contribution-graph.svg" />

# 🌌 ERP Vue Laravel — Smart Business Management

> Sistema moderno de gestão empresarial (ERP) construído com **Laravel + Vue**, focado em **performance, segurança e experiência do desenvolvedor (DX)**.

![Laravel](https://img.shields.io/badge/Laravel-11-FF2D20?style=for-the-badge\&logo=laravel)
![Vue.js](https://img.shields.io/badge/Vue.js-3-4FC08D?style=for-the-badge\&logo=vue.js)
![PostgreSQL](https://img.shields.io/badge/PostgreSQL-16-4169E1?style=for-the-badge\&logo=postgresql)
![TailwindCSS](https://img.shields.io/badge/Tailwind_CSS-3.4-38B2AC?style=for-the-badge\&logo=tailwind-css)

---

## 👨‍💻 Autor

**Luiz Eduardo** 🔗 https://github.com/letsmg

---

# 🌎 Language / Idioma

* 🇧🇷 [Ver em Português](#-português)
* 🇺🇸 [Read in English](#-english)

---

# 🇧🇷 Português

# 📦 Visão Geral

ERP Vue Laravel é um ERP moderno projetado para entregar:

* ⚡ Alta performance
* 🔒 Segurança robusta e conformidade estrita com a LGPD
* 🧠 Excelente experiência para desenvolvedores (DX)
* 🧩 Arquitetura monolítica organizada estruturalmente em camadas (Controllers, Services, Requests, Models)
* 🚀 Desenvolvimento rápido usando Inertia.js

O projeto foca em **simplicidade sem perder escalabilidade**.

---

# 🧰 Tecnologias

| Camada      | Tecnologia              |
| ----------- | ----------------------- |
| Backend     | Laravel 11 (PHP 8.2+)   |
| Frontend    | Vue 3 (Composition API) |
| Build Tool  | Vite                    |
| Comunicação | Inertia.js              |
| Estilização | Tailwind CSS            |
| Icons       | Lucide Vue              |

---

# ⚡ Experiência do Desenvolvedor (DX)

Para acelerar desenvolvimento e testes, o sistema possui utilitários globais de formulário.
Para realizar testes localmente verifique a config no arquivo phpunit.xml

## Atalhos de Teclado

| Atalho   | Ação                                    |
| -------- | --------------------------------------- |
| ALT + 1  | Preenche formulário com dados fictícios |
| ALT + 2  | Limpa campos e erros de validação       |

TIP
Esses atalhos utilizam **Custom Events** disparados dentro do `AuthenticatedLayout.vue`, mantendo a lógica das páginas limpa.

---

# 🚀 Instalação

## 1. Clonar o repositório

```bash
git clone [https://github.com/letsmg/erp-vue.git](https://github.com/letsmg/erp-vue.git)
cd erp-vue
```

---

## 2. Instalar dependências

### PHP

```bash
composer install
```

### JavaScript

```bash
npm install
npm run dev
```

---

## 3. Configurar ambiente

```bash
cp .env.example .env
```

Configure o banco:

```
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=erp_vue_laravel
DB_USERNAME=postgres
DB_PASSWORD=123456
```

NOTE
Certifique-se de que as extensões **pdo_pgsql** e **pgsql** estão ativas no `php.ini`.

---

## 5. Configurar Redis (Opcional - Recomendado)

Para melhor performance de busca e cache, configure o Redis:

### Instalar Redis

**Windows:**
```bash
# Baixe e instale o Redis para Windows ou use WSL/Docker
```

**Linux/macOS:**
```bash
sudo apt-get install redis-server  # Ubuntu/Debian
brew install redis                   # macOS
```

### Configurar Ambiente

Adicione ao seu `.env`:

```env
# Redis Cache
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
REDIS_DB=0

# Cache Driver
CACHE_STORE=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
```

### Verificar Funcionamento

```bash
# Teste conexão Redis
php artisan tinker
> Redis::ping();  # Deve retornar "+PONG"
```

### Benefícios

- **Busca ultra rápida** (cache inteligente)
- **Sugestões automáticas** baseadas em buscas anteriores
- **Performance otimizada** para consultas frequentes
- **Persistência de dados** de cache

NOTE
Redis é opcional mas **altamente recomendado** para melhor performance. Sem Redis, o sistema funcionará normalmente com PostgreSQL.

---

## 4. Rodar migrações

```bash
php artisan migrate --seed
```

Isso criará a estrutura do banco e o **usuário administrador inicial**.

---

# ⚙️ Arquitetura

## Abordagem: Inertia.js vs API REST

Este projeto utiliza uma arquitetura baseada em **Inertia.js**, evitando a necessidade de uma API REST separada para consumo de páginas internas.

### Motivações da escolha

* Elimina a duplicação de lógica entre frontend e backend
* Reduz complexidade de autenticação (CSRF nativo)
* Permite desenvolvimento mais rápido
* Compartilhamento direto de estado entre backend e frontend

---

## Escalabilidade para API

A arquitetura foi pensada para permitir evolução futura para API REST sem retrabalho significativo:

* Regras de negócio centralizadas obrigatoriamente em **Services**
* Controllers podem ser adaptados para retornar JSON
* Autenticação pode ser feita via **Laravel Sanctum**
* Alto reaproveitamento de código

---

# 🔒 Segurança, Privacidade (LGPD) e Performance

## Autenticação e Privacidade

* Hash utilizando Argon2id (Memory cost: 64MB, Threads: 2) para maior resistência a ataques de força bruta.
* Estrutura de paridade para dados sensíveis (Nomes, CPF, CNPJ e Endereços) utilizando hashing (SHA-256) para buscas performáticas e criptografia em repouso (`Crypt::encryptString`) para exibição decodificada na interface.
* Validação rigorosa e armazenamento histórico jurídico de aceites de Termos de Uso e Política de Privacidade via tabela própria (`visitor_legal_consents`).

---

## Banco de Dados

* Utilização de PostgreSQL
* Uso de paginação com filtros
* Estrutura preparada para indexação em campos críticos e hashes de busca LGPD

---

# 🔍 SEO Dinâmico

O ecossistema possui um módulo e tabela dedicada ao gerenciamento global e dinâmico de SEO (`seo_metadatas`), tratando de forma isolada os seguintes dados:

- `meta_title`
- `meta_description`
- `meta_keywords`
- `h1_text`
- `h2_text`
- `schema_markup` (JSON-LD estruturado e injetado de forma segura usando a diretiva `@verbatim` nas views Blade do Laravel).

---

# ⚡ Experiência do Usuário

Funcionalidades implementadas:

* Busca em tempo real com debounce
* Filtros dinâmicos no módulo de fornecedores
* Interface reativa via Inertia.js com exceção controlada de Axios exclusivamente para consultas rápidas em background do PDV (como leituras de códigos de barra em tempo real).

---

# 🤖 Moderação de Imagens (Opcional)

Suporte à integração com Google Cloud Vision para análise automática de imagens durante o upload.

* Detecção de conteúdo impróprio
* Bloqueio automático de uploads inválidos

---

# 📦 Módulos do Sistema

## Implementados / Em Desenvolvimento Ativo

* Módulo de Consentimento de Termos de Uso, Políticas de Privacidade e Idiomas (`visitor_legal_consents`)
* Módulo Global de SEO Gerenciável (`seo_metadatas`)
* CRUD de Usuários com controle de acesso e Laravel Fortify
* CRUD de Fornecedores
* CRUD de Produtos (com histórico rígido de movimentações em `stock_movements`)
* CRUD de Clientes e Módulo de Vendas (PDV / Checkout E-commerce)
* Controle Financeiro Automático (`accounts_payable` e `accounts_receivable`)
* Upload e ordenação de imagens (drag and drop)
* Cobertura de Testes Automatizados com PHPUnit (Unit e Feature Tests)

---

# 🇺🇸 English

# 📦 Overview

ERP Vue Laravel is a modern ERP designed to deliver:

* ⚡ High performance
* 🔒 Robust security and strict LGPD privacy compliance
* 🧠 Excellent developer experience (DX)
* 🧩 Monolithic architecture structurally organized in layers (Controllers, Services, Requests, Models)
* 🚀 Rapid development and SPA behavior using Inertia.js

The project focuses on **simplicity without sacrificing scalability**.

---

# 🧰 Tech Stack

| Layer         | Technology              |
| ------------- | ----------------------- |
| Backend       | Laravel 11 (PHP 8.2+)   |
| Frontend      | Vue 3 (Composition API) |
| Build Tool    | Vite                    |
| Communication | Inertia.js              |
| Styling       | Tailwind CSS            |
| Icons         | Lucide Vue              |

---

# ⚡ Developer Experience (DX)

To accelerate development and testing, the system includes global form utilities.
To perform tests locally, check the configuration in the phpunit.xml file.

## Keyboard Shortcuts

| Shortcut | Action                                  |
| -------- | --------------------------------------- |
| ALT + 1  | Populate form with fake data            |
| ALT + 2  | Clear form fields and validation errors |

NOTE
These shortcuts use **Custom Events** triggered inside `AuthenticatedLayout.vue`, keeping page logic clean.

---

# 🚀 Installation

## 1. Clone repository

```bash
git clone [https://github.com/letsmg/erp-vue.git](https://github.com/letsmg/erp-vue.git)
cd erp-vue
```

---

## 2. Install dependencies

### PHP

```bash
composer install
```

### JavaScript

```bash
npm install
npm run dev
```

---

## 3. Configure environment

```bash
cp .env.example .env
```

Database example:

```
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=erp_vue_laravel
DB_USERNAME=postgres
DB_PASSWORD=123456
```

NOTE
Make sure **pdo_pgsql** and **pgsql** extensions are enabled in `php.ini`.

---

## 5. Configure Redis (Optional - Recommended)

For better search performance and caching, configure Redis:

### Install Redis

**Windows:**
```bash
# Download and install Redis for Windows or use WSL/Docker
```

**Linux/macOS:**
```bash
sudo apt-get install redis-server  # Ubuntu/Debian
brew install redis                   # macOS
```

### Configure Environment

Add to your `.env`:

```env
# Redis Cache
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
REDIS_DB=0

# Cache Driver
CACHE_STORE=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
```

### Verify Installation

```bash
# Test Redis connection
php artisan tinker
> Redis::ping();  # Should return "+PONG"
```

### Benefits

- **Ultra-fast search** (intelligent cache)
- **Automatic suggestions** based on previous searches
- **Optimized performance** for frequent queries
- **Cache data persistence**

NOTE
Redis is optional but **highly recommended** for better performance. Without Redis, the system will work normally with PostgreSQL.

---

## 4. Run migrations

```bash
php artisan migrate --seed
```

This will create the database structure and generate the **initial admin user**.

---

# ⚙️ Architecture

## Approach: Inertia.js vs REST API

This project uses an architecture based on **Inertia.js**, avoiding the need for a separate REST API for internal page routing.

### Why this approach?

* Eliminates duplicated logic between frontend and backend
* Reduces authentication complexity (native CSRF protection)
* Enables faster development
* Allows direct state sharing between backend and frontend

---

## API Scalability

The architecture is designed to support future API exposure with minimal refactoring:

* Business logic strictly centralized in **Services**
* Controllers can be adapted to return JSON responses
* Authentication can be handled via Laravel Sanctum
* High code reuse

---

# 🔒 Security, Privacy (LGPD) and Performance

## Authentication & Privacy

* Argon2id hashing (Memory cost: 64MB, Threads: 2) focused on resistance against brute-force attacks.
* Parity structure for sensitive data (Names, CPF, CNPJ, and Addresses) using SHA-256 hashes for fast database indexing and encryption at rest (`Crypt::encryptString`) for safe rendering.
* Strict legal compliance logging user consent history for Terms of Use and Privacy Policies via database records (`visitor_legal_consents`).

---

## Database

* Uses PostgreSQL
* Filter-based pagination
* Structure prepared for indexing on critical fields and privacy hashes

---

# 🔍 Dynamic SEO

The ecosystem includes a module and dedicated database table for global SEO management (`seo_metadatas`), processing the following records dynamically:

- `meta_title`
- `meta_description`
- `meta_keywords`
- `h1_text`
- `h2_text`
- `schema_markup` (Structured JSON-LD safely rendered using the Laravel `@verbatim` blade directive).

---

# ⚡ User Experience

Implemented features:

* Real-time search with debounce
* Dynamic filters in supplier module
* Reactive interface powered by Inertia.js with controlled Axios exceptions for real-time background actions in the POS interface (e.g., instant barcode scanning).

---

# 🤖 Image Moderation (Optional)

Supports integration with Google Cloud Vision for automatic image analysis during uploads.

* Detects inappropriate content
* Automatically blocks invalid uploads

---

# 📦 System Modules

## Implemented / Under Active Development

* Terms of Use, Privacy Policy & Language Preference Consent Module (`visitor_legal_consents`)
* Manageable Global SEO Module (`seo_metadatas`)
* User CRUD with role-based access control and Laravel Fortify
* Supplier CRUD
* Product CRUD (backed by absolute history tracking via `stock_movements`)
* Customer CRUD & Sales System (POS / E-commerce Checkout)
* Automated Financial Ledger (`accounts_payable` and `accounts_receivable`)
* Drag-and-drop image ordering
* Automated PHPUnit Suite (Unit and Feature Tests)

---

# 📄 License

MIT License

---

<p align="center">
<strong>ERP Vue Laravel — Technology for Smart Business</strong>
</p>

<p align="center">
© 2026 — Built with scalability in mind
</p>

<img src="https://raw.githubusercontent.com/letsmg/erp-vue/main/snake-dark.svg?palette=github-dark" />

Copyright (c) 2026 Luiz Eduardo