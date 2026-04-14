# Automatação de Deploy: GitHub Actions ➔ HostGator 🚀

Este repositório está configurado com um fluxo contínuo (CI/CD) de código. A automação processará os pacotes PHP da aplicação e enviará exatamente e apenas o conteúdo da pasta `www/` compilada diretamente para a sua hospedagem na Locaweb, HostGator ou cPanel **quando você criar uma nova Release**.

Isso resolve o problema de ter que enviar milhares de arquivos da pasta `vendor` via FileZilla (o GitHub fará o download lá e mandará já comprimido pelo protocolo).

---

## 1. Configurando os Segredos (Secrets) no GitHub

Para que o GitHub Actions tenha permissão de acessar sua hospedagem, você precisa cadastrar as **Actions Secrets**.

1. Acesse seu repositório no **GitHub**.
2. Clique na engrenagem **Settings** (Configurações) ➔ **Secrets and variables** ➔ **Actions**.
3. Clique no botão verde **New repository secret** e adicione exatamente as seguintes chaves uma por uma:

| Nome da Secret | Valor (Exemplo) | Onde conseguir? |
| :--- | :--- | :--- |
| `FTP_HOSTGATOR_SERVER` | `ftp.seudominio.com.br` | Endereço FTP ou IP liberado no Painel de Hospedagem (cPanel). |
| `FTP_HOSTGATOR_USERNAME` | `usuario@seudominio.com.br` | O usuário principal do FTP ou um específico criado no cPanel para esse App. |
| `FTP_HOSTGATOR_PASSWORD` | `SuaSenhaForte123!` | A senha do FTP correspondente. |
| `FTP_HOSTGATOR_TARGET_DIR` | `/app_briefing/` ou `/public_html/` | A ***pasta de destino***. Onde o conteúdo de `www/` deve ser guardado. *Lembre-se de colocar as "/" no fim*. |

---

## 2. Como Disparar o Deploy?

O fluxo está amarrado ao evento de `Release (Published)`. Isso garante que o servidor só receba versões estáveis e aprovadas do seu sistema, ignorando commits em andamento.

> **Importante:** A esteira de automação só dispara as modificações relativas daquela Release se ela for criada na branch principal (geralmente `main`).

Para colocar no ar:
1. Trabalhe normalmente e faça commit/push das alterações para sua branch `main` no GitHub.
2. Acesse a página inicial do repositório no GitHub.
3. No lado direito, em **Releases**, clique em **Create a new release** (ou Draft a new release).
4. Em **Choose a tag**, digite a versão apontando para a `main` (ex: `v1.0.1`) e clique em *Create new tag*.
5. Dê um título (ex: _Release v1.0.1 - Módulo Financeiro_).
6. Pressione o botão verde **Publish release**.

Imediatamente após publicar, acesse a aba **Actions** na parte superior. Você verá o robô `Deploy para HostGator (Produção)` executando:
1. Ele criará um contêiner Linux no GitHub.
2. Baixará o PHP 8.4 e o Composer.
3. Executará os builds automáticos instalando as bibliotecas no painel do Github.
4. Conectará no seu HostGator silenciosamente e transferirá **apenas os arquivos com diferenças**, economizando banda.

---

## 3. O que essa Automação ignora propositalmente?

A automação do GitHub usará a regra estrita do campo de *exclude*. Arquivos que **não** serão enviados e injetados na sua hospedagem em nenhuma hipótese:
- O arquivo de banco primário local `www/.env` (você precisa configurar o .env do Hostgator manualmente pelo Painel da HostGator apenas uma vez, e o Github nunca o subscreverá acidentalmente).
- Bibliotecas do front-end da sua máquina como a pasta `node_modules`.
- Código inútil online da pasta de documentações `.dev/` e infraestrutura do repositório `.git/`.

---

## 4. Banco de Dados / Migrações Pós-Deploy

O GitHub Actions fará o upload dos arquivos modificados. **Porém, ele não tem acesso seguro direto e cego ao Banco de Dados da Hospedagem** para reescrever tabelas.

Sempre que a sua nova Versão/Release incluir modificações de banco (alteração de tabelas e `.php` de Migrations), logue no Shell SSH da sua HostGator após o deploy e dispare manualmente o comando de evolução arquitetural:

```bash
cd /pasta_da_sua_aplicacao/
vendor/bin/phinx migrate -e development
```

*Sucesso na Jornada!* Mantenha seu ambiente focado e sua ramificação (branch) limpa. Tendo o `deploy.yml` na pasta oficial do Github, não precisará se preocupar com envio via WebFTP.
