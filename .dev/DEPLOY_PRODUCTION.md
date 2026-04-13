# Guia Definitivo: Deploy em Produção (Locaweb / HostGator / cPanel / VPS)

Este documento descreve o passo a passo exato para publicar o **BriefingApp** em uma hospedagem compartilhada padrão (que não utiliza Docker) ou um servidor VPS Linux puro.

---

## 1. Requisitos Críticos do Servidor
Sua hospedagem precisa oferecer:
*   **PHP 8.2 ou superior** (O sistema utiliza *Enums* e operadores nativos do PHP 8+).
*   **Banco de Dados MySQL** (ou MariaDB).
*   **Acesso SSH** (Terminal) liberado - *Essencial para rodar composer e migrações*.
*   **Servidor Redis** (Opcional, porém **obrigatório** se você quiser visualizar as Notificações e Chat fluindo em Tempo Real sem refresh de tela).
*   **Extensões do PHP:** `pdo_mysql`, `mbstring`, `json`, `dom`, `gd` ou `imagick` (para o DomPDF).

---

## 2. Preparando os Arquivos Locais
Antes de enviar os arquivos para a nuvem:
1.  **Limpe o ambiente:** Nunca envie a pasta `vendor` inteira via FTP se estiver em um sistema Windows (podem faltar arquivos ou dar quebra de permissões do Linux).
2.  Compacte todo o conteúdo da pasta `www/` (não a raiz com os arquivos do docker, **apenas o que está dentro do `www`**) em um arquivo `deploy.zip`.

---

## 3. Subindo para o Servidor (Estrutura de Pastas)
Para manter o sistema seguro, o código fonte jamais deve ficar exposto na internet. Se o domínio principal da sua hospedagem aponta para a pasta `public_html`:

### Opção A: Servidor VPS ou Hospedagem Avançada (Recomendado)
1. Especifique na configuração do NGINX ou Apache que a raiz (`DocumentRoot`) do seu site é a pasta `/public`.
2. Assim, você joga a pasta `www` para dentro `/var/www/seu-site/` inteira.

### Opção B: cPanel / Locaweb Comum (DocumentRoot Travado no public_html)
Se você **não** pode mudar para qual pasta o NGINX/Apache olha:
1. Suba todo o conteúdo do `www` (exceto `/public`) para *um nível acima* do `public_html` (ex: `/home/usuario/app_briefing/`).
2. Suba o conteúdo de `www/public/` para dentro da sua `public_html`.
3. Edite o `public_html/index.php` da internet para apontar para a nova pasta isolada:
   ```php
   // Exemplo: de __DIR__ . '/../vendor' para:
   require_once __DIR__ . '/../app_briefing/vendor/autoload.php';
   require_once __DIR__ . '/../app_briefing/app/Core/Helpers.php';
   ```

---

## 4. Configurando Variáveis de Ambiente (.env)
1. Crie ou edite o arquivo `.env` na raiz do sistema na nuvem.
2. Adicione os conectores reais da sua hospedagem:
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://seusite.com.br

# Geralmente as hospedagens compartilhas usam localhost para MySQL tbm.
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nome_do_banco_cpanel
DB_USERNAME=usuario_cpanel
DB_PASSWORD=senha_dificil

# Redis (Seu host precisará te fornecer as chaves, ou remova e instale local se for VPS)
REDIS_HOST=127.0.0.1
REDIS_PORT=6379 
REDIS_PASSWORD=null
```

---

## 5. Configurando o Banco de Dados (Gerando a Arquitetura)

1. Entre no **Painel da sua Hospedagem (cPanel)**.
2. Crie um "Banco de Dados MySQL" vazio.
3. Atribua o usuário criado ao banco e garanta "Todos os Privilégios".
4. Pelo **Terminal SSH** da sua hospedagem, navegue até a pasta do seu site e rode:
   ```bash
   composer install --no-dev --optimize-autoloader
   ```
5. Agora ordene a construção do banco pelo nosso ORM (Phinx):
   ```bash
   vendor/bin/phinx migrate -e development
   ```
   *(Nota: Por padrão nosso phinx.yml usa a tag "development" mapeando para as variáveis de ambiente, apenas rode o comando normal).*

6. Opcionalmente, popule os Modelos de Orçamento mágicos:
   ```bash
   vendor/bin/phinx seed:run
   ```

---

## 6. Corrigindo as Permissões Cruciais
Plataformas como Laravel, Blade e o próprio PHP precisam de permissões especiais de disco para re-desenhar (cache) os sites e os PDFs.
Se você pular esta etapa, o servidor dará tela branca (Erro 500) do `BladeOne`.

Rode no terminal da hospedagem (ou defina permissões manuais de Gravacão via FTP Manager nas seguintes pastas):
```bash
chmod -R 775 storage/
chmod -R 775 storage/cache/
chmod -R 775 storage/cache/views/
# Se existir upload de anexos de tickets
chmod -R 775 public/uploads/ 
```

---

## 7. Sobre o Servidor Redeis (Notificações ao Vivo / Chat War Room)

Nosso sistema utiliza `Server-Sent Events (SSE)` plugado debaixo dos panos através de uma conexão de **Memória RAM (Redis)**. O NGINX/Apache roda o PHP com limites de tempo restritos (geralmente 30s), por isso é essencial que o Redis atue rápido.

**Para a melhor experiência num servidor em Produção:**
* Se for um **VPS**, instale: `sudo apt install redis-server`.
* Se for hospedagem como **Locaweb/Hostgator**, geralmente o Redis não é ativado para os sub-usuários, e o chat ao vivo em tempo real dará `Connection Refused`. O cliente ainda conseguirá enviar a mensagem, mas talvez precise recarregar a tela para vê-la. Para servidores mais baratos que não deixam acionar Redis, desabilite ou comente as linhas do `RedisManager::publish` nos Controllers que causam timeouts lentos em instâncias faltantes.

---

## 8. Validando a Segurança da Instalação (.htaccess)

A raiz `/public` do projeto possui um `.htaccess` construído para despachar todas as rotas da internet pro arquivo oculto `index.php` do Bramus Router. Ele impede que pessoas acessem suas rotas brutalmente.

**Se usar NGINX puro:** o `.htaccess` é inútil. Adicione na configuração raiz do bloco `/` do seu NGINX a linha de re-escrita forçada:
```nginx
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
```

Acesse `https://seusite.com.br/admin/login` e pronto!
O **BriefingApp + Faturamento** estará operacional 100% na Nuvem.
