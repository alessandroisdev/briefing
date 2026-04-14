# Automatação de Deploy: GitHub Actions ➔ HostGator 🚀 (Via SSH Profissional)

O fluxo de implantação foi totalmente modernizado! Esqueça o protocolo FTP ultrapassado. Agora, utilizamos conexões Criptografadas SSH e SCP diretamente no terminal da sua hospedagem.

**Vantagens esmagadoras sobre o método antigo (FTP):**
- A transferência inicial leva **segundos** em vez de minutos, pois não enviamos as dezenas de milhares de arquivos pesados contidos na pasta `vendor`. Mandamos apenas os textos do Seu Código Oficial (Controller, HTML, CSS).
- Um robô acessará secretamente o servidor da Hostgator e montará o quebra-cabeça (Composer Install) **diretamente pela memória da máquina deles**. Maior estabilidade e garantia imaculada de que os arquivos do framework batem perfeitamente.
- O Banco de Dados executa as migrações automáticas pendentes num piscar de olhos logo após o código cair. Sem trabalho manual após Release!

---

## 1. Configurando os Segredos (Secrets) no GitHub

Para as Actions funcionarem, seu sistema base (HostGator/cPanel) **deve suportar e estar com o Acesso SSH liberado** nas configurações de segurança do painel.

No GitHub vá em **Settings (Engrenagem)** ➔ **Secrets and variables** ➔ **Actions**.
Cadastre estas chaves:

| Nome da Secret | Valor (Exemplo) | Onde conseguir? |
| :--- | :--- | :--- |
| `SSH_HOST` | `br00.hostgator.com.br` | O IP ou domínio do seu acesso cPanel/Servidor. |
| `SSH_USERNAME` | `usuario_cpanel` | Seu usuário ROOT do cPanel ou do Jail. |
| `SSH_PASSWORD` | `SuaSenhaForte123!` | *Atenção: Na hostgator, é melhor usar Password pura no começo. Em provedores Premium (AWS/DigitalOcean), se usaria SSH_KEY com a chave RSA.* |
| `DEPLOY_PATH` | `/home/usuario_cpanel/public_html/` | O **caminho absoluto (Full Path)** da hospedagem. |

> **Detalhe da Porta SSH:** Se a porta SSH do seu Hostgator **não for 22** e sim a clássica **2222**, crie a porta nos segredos ou apenas tire o comentário do campo `port: 2222` diretamente dentro do seu arquivo `.github/workflows/deploy.yml`.

---

## 2. O Processo Exato de Deploy (O que acontece por trás dos panos?)

Quando você gerar uma **Nova Release (Published)** a partir do menu `Tags / Releases` na branch main:

1. **Empacotamento Magro**: O Robô apaga os lixos de teste da sua aplicação. Ignora o `.env` (que já deve existir vivo no servidor HostGator para não explodir em senhas locais vazadas) e recusa também toda a volumosa pasta `vendor`.
2. **Transferência Relâmpago (SCP)**: Em poucos segundos o pacote base com o código purificado do `BriefingApp` cai na pasta do HostGator designada no `DEPLOY_PATH`.
3. **Pilar Esterno (O Terminal)**: O Github abre um **SSH In-Browser** fantasma com o servidor:
   * **Composer Install (`--optimize-autoloader`)**: Ele obriga o PHP da HostGator a baixar todos pacotes nativos mais recentes e mapeá-los nativamente pro disco, eliminando os erros 500 do Blade/Autoload gaguejante.
   * **Teste de Sanidade (`composer check-platform-reqs`)**: Dispara um alarme caso alguma dependência no Hostgator esteja desatualizada.
   * **Database Sync Automático**: Roda o seu console customizado `php nortedev db:migrate --env=production`. Tudo que você criar localmente (novas tabelas / migrações do Phinx) magicamente brotará na nuvem HostGator para você!

---

## 3. Resolução de Problemas Comuns (Avisos Importantes na Hostgator)

1. **Caminho do PHP Específico:** Para quem tem múltiplos PHPs instalados no cPanel (ex: Select PHP Version), as vezes só digitar a palavra `composer` nos Scripts do GitHub Action pode cair num PHP 7 jurássico. Se o Action falhar, edite o seu `deploy.yml` e mude as chamadas para a rota bruta da sua engine HostGator desejada, como `/opt/cpanel/ea-php84/root/usr/bin/php nortedev ...`
2. **O .env**: Não canso de reforçar para equipes de DevOps iniciantes: A automação NUNCA tocará no `.env` global. A responsabilidade de ir no cPanel e preencher a senha do Banco e do Redis no arquivo `.env` uma única vez é primária!
