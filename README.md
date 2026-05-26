# Hemodat — Sistema de Gestão de Banco de Sangue

Sistema web para controle de estoque de bolsas de sangue em hemocentros. Registra entradas e saídas, gerencia usuários com autenticação por sessão PHP, exibe dashboard com gráficos e alertas, e envia e-mail de recuperação de senha.

---

## Stack

| Camada | Tecnologia |
|--------|------------|
| Frontend | HTML5, Bootstrap 5.3, Vanilla JS |
| Backend | PHP 8.2, PDO |
| Banco de dados | MySQL (InnoDB, utf8mb4_unicode_ci) |
| Ícones | Bootstrap Icons 1.11.3 (CDN) |
| Gráficos | Chart.js 4.4.3 (CDN) |
| PDF Export | jsPDF 2.5.1 (CDN) |
| E-mail | PHP `mail()` nativa (relay Hostinger em produção) |
| Servidor local | XAMPP (Apache + MySQL + PHP 8.2) |

---

## Estrutura de Arquivos

```
Hemodat/
├── .htaccess                   # Rewrite de URLs limpas, bloqueio de pastas, headers de segurança
├── composer.json               # Dependências PHP
│
├── home.php                    # Dashboard — stat cards, alertas, gráfico de estoque
├── entrada.php                 # Registro de entrada de bolsas
├── saida.php                   # Registro de saída de bolsas
├── relatorio.php               # Gráfico filtrado + exportação PDF
├── historico.php               # Histórico paginado de movimentações
├── admin.php                   # Painel admin: usuários + estoque mínimo
├── login.php                   # Autenticação
├── forgot_password.php         # Recuperação de senha — step 1
├── codigo.php                  # Validação do código — step 2
├── alterar_senha.php           # Nova senha — step 3
│
├── includes/
│   ├── actions/
│   │   ├── auth.php            # Login / logout
│   │   ├── bolsas.php          # CRUD bolsas (entrada, saída, totais, alertas, histórico)
│   │   ├── senha.php           # Recuperação e alteração de senha
│   │   └── admin.php           # Ações admin (usuários, estoque mínimo)
│   └── functions/
│       ├── config.php          # Configuração central: DB, URLs, constantes de negócio
│       ├── csrf.php            # Geração e validação de CSRF token
│       ├── session.php         # Verificação de sessão ativa (endpoint AJAX)
│       └── header.php          # Header HTML + assets globais + ob_start() minifier
│   └── other/
│       ├── sidebar.php         # Sidebar + topbar (layout de app)
│       └── footer.php          # Fecha app-shell
│
├── assets/
│   ├── css/
│   │   └── padrao.css          # Design system completo (tokens, dark mode, layout)
│   └── js/
│       ├── padrao/
│       │   ├── darkmode.js     # Toggle light/dark + sync localStorage
│       │   ├── security.js     # Console warning + bloqueia DevTools em produção
│       │   ├── logout.js       # Fetch logout → redirect /login
│       │   └── verificar_sessao.js  # Heartbeat de sessão → redirect /login
│       └── custom/
│           ├── login.js        # Submit login, toggle senha
│           ├── home.js         # Stat cards, alertas, Chart.js estoque
│           ├── entrada.js      # Blood chips, form entrada, estoque resumo
│           ├── saida.js        # Blood chips, form saída, últimas saídas
│           ├── relatorio.js    # Filtros, Chart.js, export PDF
│           └── historico.js    # Tabela paginada, filtros
│
└── imagens/
    ├── logo/                   # logo.png, logo-white.png, logo.svg, logo-white.svg,
    │                           # mark.svg, mark-white.svg
    └── favicon/                # favicon.svg, favicon-32.png
```

---

## URLs

Todas as páginas usam URLs sem `.php` via mod_rewrite:

| URL | Arquivo |
|-----|---------|
| `/login` | login.php |
| `/home` | home.php |
| `/entrada` | entrada.php |
| `/saida` | saida.php |
| `/relatorio` | relatorio.php |
| `/historico` | historico.php |
| `/admin` | admin.php |
| `/forgot_password` | forgot_password.php |
| `/codigo` | codigo.php |
| `/alterar_senha` | alterar_senha.php |

> **Endpoints de API** (fetch direto por JS) mantêm `.php`: `includes/actions/*.php`, `includes/functions/csrf.php`

---

## Banco de Dados

### Schema

```sql
-- Usuários do sistema
CREATE TABLE usuarios (
    id       INT AUTO_INCREMENT PRIMARY KEY,
    nome     VARCHAR(50)  NOT NULL,
    email    VARCHAR(255) NOT NULL UNIQUE,
    senha    VARCHAR(255) NOT NULL,           -- bcrypt
    papel    ENUM('admin','operador') NOT NULL DEFAULT 'operador',
    criado_em DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Estoque de bolsas (FIFO por data de validade)
CREATE TABLE bolsas_sangue (
    id             INT AUTO_INCREMENT PRIMARY KEY,
    tipo_sanguineo VARCHAR(5)    NOT NULL,
    quantidade     DECIMAL(10,2) NOT NULL,
    data_coleta    DATE          NOT NULL,
    data_validade  DATE          NOT NULL,
    criado_em      DATETIME      DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Log de entradas e saídas
CREATE TABLE historico_bolsas (
    id             INT AUTO_INCREMENT PRIMARY KEY,
    tipo_sanguineo VARCHAR(5)        NOT NULL,
    quantidade     DECIMAL(10,2)     NOT NULL,
    operacao       ENUM('entrada','saida') NOT NULL,
    usuario        VARCHAR(100),
    data_evento    DATETIME          DEFAULT CURRENT_TIMESTAMP,
    observacao     TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Estoque mínimo por tipo sanguíneo (alerta no dashboard)
CREATE TABLE estoque_minimo (
    tipo_sanguineo VARCHAR(5)    NOT NULL PRIMARY KEY,
    minimo         DECIMAL(10,2) NOT NULL DEFAULT 1.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tokens de recuperação de senha (TTL = 15 min)
CREATE TABLE recuperar_senha (
    id        INT AUTO_INCREMENT PRIMARY KEY,
    usuario   VARCHAR(50)  NOT NULL,
    email     VARCHAR(255) NOT NULL,
    codigo    VARCHAR(8)   NOT NULL,
    criado_em DATETIME     DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

## Fluxo de Autenticação

```
/login  →  POST auth.php?action=login  →  $_SESSION['usuario_logado'] = true  →  /home

Páginas protegidas:
    require_auth() em config.php  →  verifica sessão  →  redirect /login se inválida
    verificar_sessao.js  →  heartbeat AJAX  →  redirect /login se expirada

Recuperação de senha:
    /forgot_password  →  POST senha.php?action=recuperar
        → código 8 chars (random_bytes) salvo em recuperar_senha
        → e-mail HTML enviado via mail()
    /codigo           →  POST senha.php?action=validar   →  verifica TTL 15 min
    /alterar_senha    →  POST senha.php?action=alterar   →  bcrypt UPDATE  →  /login
```

---

## Segurança

| Item | Implementação |
|------|---------------|
| Senhas | bcrypt (`PASSWORD_BCRYPT`) |
| Sessão | `session_start()` em `config.php`, verificação server-side em cada página |
| CSRF | Token gerado em `csrf.php`, validado em todos os POSTs de formulário |
| Acesso direto a pastas | `.htaccess` bloqueia `includes/functions/` e `includes/other/` |
| Tipo sanguíneo | Whitelist `TIPOS_VALIDOS` em todas as entradas backend |
| Headers HTTP | X-Content-Type-Options, X-Frame-Options, X-XSS-Protection, Referrer-Policy, Permissions-Policy |
| DevTools | `security.js` bloqueia F12 / Ctrl+Shift+I/J/C / Ctrl+U / right-click em produção |
| Código de recuperação | `bin2hex(random_bytes(4))`, TTL 15 min, DELETE após uso |

---

## Dark Mode

Implementado via CSS custom properties com troca de `html[data-theme="dark"]`.

- Toggle: botão 🌙 na sidebar
- Persistência: `localStorage('hemodat_theme')`
- Anti-FOUC: `<script>` inline no `<head>` aplica o tema antes de qualquer render
- Sync entre abas: evento `storage` do localStorage

---

## Setup Local

### Pré-requisitos

- XAMPP com Apache + MySQL + PHP 8.2+
- Banco de dados: `efegduik_gphemodat` (ou ajustar em `config.php`)
- `mod_rewrite` habilitado no Apache

### Passos

```bash
# 1. Clonar no diretório correto
git clone <repo> C:/xampp/htdocs/_Pessoal/Hemodat

# 2. Criar banco e tabelas
# Via phpMyAdmin: importar o schema da seção "Banco de Dados"

# 3. (Opcional) Popular dados de exemplo
# Rodar seed via phpMyAdmin ou MySQL CLI

# 4. Iniciar XAMPP → Apache + MySQL

# 5. Acessar
http://localhost/_Pessoal/Hemodat/login
```

### Habilitar mod_rewrite no XAMPP

Em `C:/xampp/apache/conf/httpd.conf`, certifique-se que:
```apache
LoadModule rewrite_module modules/mod_rewrite.so
```
E no VirtualHost ou diretório do projeto:
```apache
AllowOverride All
```

---

## Deploy (Hostinger)

1. Fazer upload dos arquivos (exceto `vendor/`) via FTP ou painel
2. Em produção, `composer install --no-dev --optimize-autoloader` no servidor (se necessário)
3. Banco já configurado: `efegduik_gphemodat` com credenciais em `config.php`
4. URL limpa: domínio já aponta para raiz do projeto, `.htaccess` cuida do resto
5. E-mail: `mail()` do PHP funciona via relay nativo do Hostinger — sem configuração adicional

---

## Autores

Desenvolvido por **Thiago Ferraz** — projeto pessoal de gestão de hemocentro.
