# Hemodat - Sistema de Gestão de Banco de Sangue

Sistema web para controle de estoque de bolsas de sangue em hemocentros. Registra entrada e saída de bolsas, gerencia usuários com autenticação por sessão PHP e exibe relatório gráfico com exportação em PDF.

---

## Stack

| Camada | Tecnologia |
|--------|-----------|
| Frontend | HTML5, CSS3, Vanilla JS |
| Backend | PHP (PDO) |
| Banco de dados | MySQL |
| Ícones | Bootstrap Icons 1.11.3 (CDN) |
| Gráficos | Chart.js (CDN) |
| PDF Export | jsPDF 2.5.1 (CDN) |
| Servidor local | XAMPP |

---

## Estrutura de Arquivos

```
Hemodat/
├── index.html              # Landing + cadastro de usuário
├── login.html              # Login
├── home.html               # Dashboard (autenticado)
├── entrada.html            # Registro de entrada de bolsas
├── saida.html              # Registro de saída de bolsas
├── relatorio.html          # Gráfico de estoque + export PDF
├── forgot_password.html    # Recuperação de senha (step 1)
├── codigo.html             # Validação do código (step 2)
├── alterar_senha.html      # Nova senha (step 3)
│
├── php/
│   ├── login.php           # Auth → cria sessão
│   ├── logout.php          # Destrói sessão
│   ├── cadastro.php        # INSERT usuário (bcrypt)
│   ├── session.php         # Endpoint: verifica sessão ativa
│   ├── entrada.php         # INSERT bolsa_sangue
│   ├── saida.php           # INSERT saida_bolsas + valida estoque/validade
│   ├── buscar_tipo.php     # GET tipos sanguíneos (para select)
│   ├── buscar_total.php    # GET tipo + quantidade (para gráfico)
│   ├── recuperar_senha.php # Gera código 8 chars + envia email
│   ├── codigo.php          # Valida código → DELETE registro
│   └── alterar_senha.php   # UPDATE senha (bcrypt)
│
├── javascript/
│   ├── verificar_sessao.js # Fetch session.php → redireciona se não logado
│   ├── cadastro.js         # Submit cadastro via fetch
│   ├── login.js            # Submit login via fetch
│   ├── logout.js           # Submit logout
│   ├── entrada.js          # Submit entrada + mask de data
│   ├── saida.js            # Submit saída + popula select via buscar_tipo.php
│   ├── relatorio.js        # Renderiza Chart.js + exporta PDF
│   ├── recuperar_senha.js  # Submit forgot_password
│   ├── codigo.js           # Submit código de validação
│   ├── alterar_senha.js    # Submit nova senha
│   └── main.js             # Menu hamburguer (toggle nav)
│
├── sources/                # CSS por página
│   ├── index.css
│   ├── login.css
│   ├── home.css
│   ├── entrada.css
│   ├── saida.css
│   ├── relatorio.css
│   ├── alt_senha.css
│   ├── codigo.css
│   └── f_password.css
│
└── images/
    ├── logo.png
    ├── logo.ico
    ├── logo_reduzido.ico
    └── background.png
```

---

## Banco de Dados

### Tabelas (inferidas do código)

```sql
CREATE TABLE usuarios (
    id       INT AUTO_INCREMENT PRIMARY KEY,
    nome     VARCHAR(50)  NOT NULL,
    email    VARCHAR(255) NOT NULL UNIQUE,
    senha    VARCHAR(255) NOT NULL  -- bcrypt hash
);

CREATE TABLE bolsas_sangue (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    tipo_sanguineo  VARCHAR(5)     NOT NULL,
    quantidade      DECIMAL(10,2)  NOT NULL,
    data_coleta     DATE           NOT NULL,
    data_validade   DATE           NOT NULL
);

CREATE TABLE saida_bolsas_sangue (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    email           VARCHAR(255)   NOT NULL,
    tipo_sanguineo  VARCHAR(5)     NOT NULL,
    quantidade      DECIMAL(10,2)  NOT NULL,
    data_saida      DATE           NOT NULL
);

CREATE TABLE recuperar_senha (
    id      INT AUTO_INCREMENT PRIMARY KEY,
    usuario VARCHAR(50)  NOT NULL,
    email   VARCHAR(255) NOT NULL,
    codigo  VARCHAR(8)   NOT NULL
);
```

---

## Fluxo de Autenticação

```
index.html → [cadastro] → cadastro.php → usuarios
index.html → [entrar] → login.html → login.php → SESSION → home.html

home.html / entrada.html / saida.html / relatorio.html
    → verificar_sessao.js → session.php
    → não logado? → redirect login.html

forgot_password.html → recuperar_senha.php → email com código
    → codigo.html → codigo.php → valida → DELETE código
    → alterar_senha.html → alterar_senha.php → UPDATE senha → login.html
```

---

## Problemas Identificados

### 🔴 Crítico - Segurança

| # | Arquivo(s) | Problema | Impacto |
|---|-----------|----------|---------|
| 1 | Todos os `.php` | **Credenciais DB hardcoded** em cada arquivo | Vazamento de senha em qualquer diff/log/push |
| 2 | `recuperar_senha.php:48` | `str_shuffle()` não é criptograficamente seguro para código de recuperação | Código previsível |
| 3 | `recuperar_senha.php` | Código de recuperação **sem expiração por tempo** | Código válido indefinidamente até ser usado |
| 4 | Todos os forms | **Sem CSRF token** | Ataques cross-site request forgery |
| 5 | `saida.php` | Não verifica `$_SESSION['usuario_logado']`, só `usuario_email` | Bypass parcial de sessão |

### 🟠 Alto - Bug Funcional

| # | Arquivo(s) | Problema |
|---|-----------|----------|
| 6 | `saida.php` | **Não decrementa `bolsas_sangue.quantidade`** após saída registrada - estoque nunca diminui |
| 7 | `buscar_total.php` | Retorna todas as linhas de `bolsas_sangue` sem `SUM()`/`GROUP BY` - se houver múltiplas entradas do mesmo tipo, gráfico mostra duplicatas em vez de total |
| 8 | `buscar_tipo.php` | Mesmo problema - busca todas as linhas e deduplica em PHP com `array_unique()` em vez de `SELECT DISTINCT` |

### 🟡 Médio - Inconsistência / UX

| # | Arquivo(s) | Problema |
|---|-----------|----------|
| 9 | `index.html:39` | `maxlength="9"` mas texto diz "8 dígitos + 1 especial". `alterar_senha.php:42` valida `strlen < 8`. Regra de senha inconsistente entre frontend e backend |
| 10 | `index.html:41` | Tag `<fontsize>` não existe em HTML5 |
| 11 | Todos os JS | `alert()` nativo para feedback - bloqueia UI, UX ruim |
| 12 | `entrada.js` / `saida.js` | `formatDate()` duplicada nos dois arquivos |
| 13 | `relatorio.html:59` | `chart.js` importado duas vezes (head + fim do body) |
| 14 | `saida.php:41` | `SELECT quantidade, data_validade FROM bolsas_sangue WHERE tipo_sanguineo = :tipo` retorna apenas o primeiro registro - não considera múltiplas entradas do mesmo tipo |

### 🔵 Baixo - Qualidade de Código

| # | Problema |
|---|----------|
| 15 | Config de DB repetida em 9 arquivos PHP - sem arquivo central `config.php` |
| 16 | Sem `.env` ou separação de ambiente (dev/prod) |
| 17 | Sem validação de tipo sanguíneo no backend (aceita qualquer string) |
| 18 | `logout.php` não referenciado - `logout.js` pode estar chamando endpoint diferente |

---

## Melhorias Prioritárias

### P0 - Antes de qualquer deploy

1. **Centralizar config DB** → criar `php/config.php` com credenciais, incluir via `require_once`
2. **Corrigir decremento de estoque** → `saida.php` deve fazer `UPDATE bolsas_sangue SET quantidade = quantidade - :qtd WHERE tipo_sanguineo = :tipo` após INSERT
3. **Corrigir queries agregadas** → `buscar_total.php` e `buscar_tipo.php` usar `GROUP BY` e `SUM()` / `SELECT DISTINCT`
4. **Expiração de código** → adicionar coluna `criado_em TIMESTAMP` em `recuperar_senha`, rejeitar códigos > 15 min
5. **Código seguro** → trocar `str_shuffle` por `bin2hex(random_bytes(4))`

### P1 - Qualidade

6. **Validação de tipo sanguíneo** → whitelist `[A+, A-, B+, B-, AB+, AB-, O+, O-]` no backend
7. **Unificar regra de senha** → decidir entre 8 ou 9 chars, aplicar igual em HTML + PHP
8. **Toast/modal** → substituir `alert()` por feedback visual não-bloqueante
9. **Deduplicar `formatDate()`** → mover para `main.js`
10. **CSRF tokens** → implementar em todos os forms POST

### P2 - Funcionalidades Futuras

- Alerta de bolsas próximas do vencimento (dashboard)
- Histórico paginado de entradas e saídas
- Controle de estoque mínimo por tipo sanguíneo
- Perfil de usuário (nome exibido no dashboard)
- Relatório com filtro por período e tipo sanguíneo
- Roles: admin vs. operador

---

## Setup Local

### Pré-requisitos
- XAMPP (Apache + MySQL + PHP 8+)
- Banco criado: `efegduik_gphemodat`

### Passos

```bash
# 1. Clonar no diretório correto
git clone <repo> C:/xampp/htdocs/_Pessoal/Hemodat

# 2. Importar schema no MySQL
# Criar tabelas conforme seção "Banco de Dados" acima

# 3. Configurar credenciais
# Editar host/dbname/username/password nos arquivos php/*.php
# (ver P0 #1: mover para php/config.php)

# 4. Iniciar XAMPP → Apache + MySQL

# 5. Acessar
http://localhost/_Pessoal/Hemodat/
```

---

## Segurança - Estado Atual vs. Alvo

| Item | Atual | Alvo |
|------|-------|------|
| Senha | bcrypt ✅ | - |
| Config DB | hardcoded em cada arquivo ❌ | `config.php` centralizado |
| Sessão | PHP session básica ⚠️ | + session regeneration + timeout |
| Recuperação senha | código sem expiração ❌ | TTL 15 min + `random_bytes` |
| CSRF | sem proteção ❌ | tokens em todos os forms |
| Validação backend | mínima ⚠️ | whitelist + sanitização completa |
| HTTPS | depende do servidor | obrigatório em produção |

---

## Autores

Desenvolvido por **Thiago Ferraz** como projeto pessoal de gestão de hemocentro.
