<div align="center">
  <h1>🚀 BriefingApp | Hub Criativo B2B</h1>
  <p><strong>Plataforma SaaS para Agências Criativas, com Sala de Guerra e Gestão de Propostas Comerciais.</strong></p>

  [![PHP Version](https://img.shields.io/badge/PHP-8.4+-777BB4?style=for-the-badge&logo=php&logoColor=white)]()
  [![Database](https://img.shields.io/badge/MySQL-Database-4479A1?style=for-the-badge&logo=mysql&logoColor=white)]()
  [![Redis](https://img.shields.io/badge/Redis-Real--Time-DC382D?style=for-the-badge&logo=redis&logoColor=white)]()
  [![Estilo](https://img.shields.io/badge/Design-Premium_Dark-0f172a?style=for-the-badge)]()
</div>

<br>

## 💡 Sobre o Projeto

O **BriefingApp** é um sistema ponta a ponta projetado do zero com uma arquitetura **Model-View-Controller (MVC) nativa super leve**, blindada e desenhada especificamente para empresas modernas. Ele substitui processos longos via e-mails frios e planilhas por uma plataforma online responsiva premium, permitindo fluxos ágeis desde o momento que o *Lead* solicita o orçamento, até as conversas da operação.

---

## ✨ Features Core (Principais Funcionalidades)

### 👥 Portal "Magic" do Cliente
- **Autenticação Descomplicada:** Fim do "Esqueci a Senha". O sistema utiliza infraestrutura de *Magic Links*, remetendo Tokens seguros nativos ao e-mail do cliente, blindando contra tentativas de força bruta.
- **Painel Central unificado (Dashboard dark):** Visualização imediata de Orçamentos, Status dos Projetos de Briefing e Tickets Abertos de Suporte em andamento.

### ⚔️ Project War Room (O Centro Nervoso)
- Espaço dedicado por Ordem de Produção / Escopo do cliente.
- **Chat em Tempo Real (SSE):** Comunicação entre cliente e Agência que pisca na tela e não requer atualização HTTP (`F5`) - energizado brutalmente por fluxos no Servidor `Redis`.
- **Cofre de Credenciais (Vault🔒):** Bloco ultra-isolado no projeto para trânsito criptografado de CPANEL, logons de redes sociais e chaves da AWS informados pelo cliente.
- **Templates Dinâmicos:** Acelere interações administrativas em 300% com banco ilimitado de Templates de Resposta e Fluxogramas já pré-cadastrados.

### 💵 Módulo Financeiro & Gerador de Faturas (Cotações)
- **Precificação por Matrizes Base:** Formulário do analista que constrói e calcula valores complexos instantaneamente.
- **Botão Real-Time (Sent):** A propostá salta pra tela do cliente. Ele analisa blocos estéticos responsivos.
- **Aceite Oficial / Modal de Contra-Proposta:** Funil validado quando o cliente pressiona *Aprovado*, ou envio de negociações amigáveis através de um modal customizado descrevendo um bloqueador.
- **Geração PDF "Server-Side":** Usamos `DomPDF` rodando cru dentro do Linux para compilar HTML formatado entregando Contratos limpos para anexação e backup (ao invés das velhas rotinas front-end).

### 🛠 Telemetria Global & Handlers Resilientes
Implementamos nativamente no `Bootstrap` os poderosos `Monolog/Monolog` combinados maravilhosamente a varredura e debugging em ambiente DEV através da tela de congelamento em tempo-real do `Filp/Whoops`. Sem logs expostos para clientes finais e 100% gravados em `storage/logs`.

---

## 🏗 Arquitetura & Tecnologias
* **Backend:** PHP 8.4+ 🐘 *(Tipado Rigorosamente)*
* **Padrão Relacional ORM:** Eloquent Base Layer Oculto & Query Builders.
* **Migrações e Seeds (Coração DB):** Phinx (CakePHP) mapeado via alias/comando customizado.
* **Roteamento Dinâmico:** Bramus Router Restful-Ready.
* **Segurança .htaccess Multi-Stage:** Blindagem global e secundária que previne exposição acidental se alocado sob `public_html`.
* **CI/CD:** Github Actions automatizado executando `composer` nativo via SCP + SSH Remoto em Hosts Compartilhadores e instâncias em nuvens VPS.
* **Queue Worker / Job Daemon:** Processos desamarrados pra envios assíncronos via script `php nortedev queue:work`.

---

## 🚦 Começando Rapidamente (Local Development)

### 1. Preparando o Solo
Instale as dependências.
```bash
cd www
composer install
```

Sincronize a Configuração: 
Copie o arquivo oculto `.env.example` e renomeie para `.env`.
Garanta a existência do comando `APP_ENV=local` e insira suas predefinições de MySQL e Redis.

### 2. Conjurando a Tabela do Banco através da CLI
Nós portamos o poder de *Consoles* famosas! Dentro da pasta `/www/` abra seu terminal local, inicialize as instâncias subjacentes e preencha dados fictícios de teste utilizando estes comandos elegantes:
```bash
php nortedev db:migrate
php nortedev db:seed
```

### 3. Rodando Fila Assíncrona 
As suas notificações e e-mails só chegam se o Agente Carteiro estiver acordado no fundo da tela em outra aba de terminal:
```bash
php nortedev queue:work
```

### 4. Ligando o Servidor de Páginas
Você está pronto, exponha o projeto apontando à pasta `public/`:
```bash
php -S localhost:8000 -t public/
```
Agora vá para [http://localhost:8000/admin/login](http://localhost:8000/admin/login) *A diversão começou.*

---

## 🚀 Deploys Oficiais em Produção

Deixamos um par robusto de documentações estritas na pasta raiz de Desenvolvimento (`/.dev/`), mapeando todo caminho sem solavancos caso queira subir essa aplicação para provedores de baixo/médio custo sem Containeres (Ex: Locaweb, Hostgator, Integrações cPanel). 

* 👉🏼 **[Guia Definitivo e Requisitos do Apache - clique aqui](./.dev/DEPLOY_PRODUCTION.md)**
* 👉🏼 **[Como usar nosso Pipeline de Continous Deployment SSH Automático - clique aqui](./.dev/GITHUB_DEPLOY.md)**

---

<div align="center">
  <b>Comercial & UX:</b> Desenvolvido com carinho para performar.
  <br>
  <i>"Faça com que seus clientes amem preencher contratos."</i>
</div>