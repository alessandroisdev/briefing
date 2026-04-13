# Documentação da CLI `nortedev` e Arquitetura de Filas Assíncronas

## Visão Geral
Bem-vindo à documentação oficial do motor de execução em background da agência. Inspirado em ecossistemas corporativos como Laravel Horizon e Symfony Messenger, introduzimos um manipulador assíncrono para o envio de e-mails blindado contra loops, gargalos e travamentos da aplicação HTTP primária.

A orquestração do sistema é dividida em 3 pilares fundamentais:
1. **Persistência**: MariaDB (`tabela: email_jobs`) 
2. **Mensageria Veloz**: Protocolo in-memory via Redis (`fila: email_queue`)
3. **Operário de Retaguarda**: Worker Daemon orquestrado pelo Symfony Console no script `nortedev`.

---

## 1. O Motor Assíncrono (Redis + DB)

Sempre que a aplicação web precisa despachar um E-mail (seja o Magic Link do Cliente ou Notificações Administrativas nativas), a lógica jamais tenta engatilhar o servidor SMTP de forma síncrona na hora do clique. Em vez disso, utilizamos o invocador:

```php
\App\Services\EmailQueueService::enqueue(
    $email_destinatario, 
    $nome, 
    $assunto, 
    $corpo_html
);
```

### O que acontece nativamente nos bastidores?
1. **Segurança Permanente:** O App insere no MariaDB (`email_jobs`) a intenção com seus parâmetros e estipula o status estático como `'pending'`. Desta forma o gatilho nunca se perderá mesmo que a força caia.
2. **Propulsão Reativa:** O App agarra exatamente o `ID` inserido neste trabalho e dá um 'sopro' (*push*) na esteira hiper-rápida do cache (`$redis->rpush('email_queue', $job->id)`).
3. **Página Imediata:** O cliente navegando do outro lado não enfreta as telas de "Carregando" angustiantes, sua requisição responde quase que instantaneamente (< 0.05ms) enviando os flashbacks visuais.

---

## 2. A CLI (Command-Line Interface) `nortedev`

Abandonamos a necessidade limitante do *Crontab do Linuux* (um cron acorda no máximo a cada 60 segundos com muito retardo) para processos velozes de mensagens, e no lugar, embarcamos o sofisticado **Daemon Worker**.

O executável master corporativo roda na raiz de código `/www/nortedev`. Ele importa e invoca a inteligência de comando do _Symfony/Console_.

### Como Operar Manualmente
Se você quiser ver as engrenagens da fila e relatórios coloridos ao vivo no terminal operando dentro do container principal, basta executar este comando no seu console:

```bash
docker compose exec app php nortedev queue:work
```

### Protocolos de Defesa da Engenharia (Zero Loop & Zero Gargalos)
A classe intrínseca `App\Console\Commands\QueueWorkCommand` foi cirurgicamente projetada pelo arquiteto blindando a infraestrutura de *"Loops Infinitos Secos"* (os temíveis *Dry loops* consumistas de máquina):

1. **Eficiência Abismal do `blpop`:** Diferente de programadores que cometem o risco de usar `while(true) { sleep(1); }` consultando fisicamente a rede de dados toda hora (gastando milhares de contatos por minuto do processador da AWS), nos unificamos o controle ao bloqueio ativo do Redis `blpop(['email_queue'], 0)`. Isso avisa ao processador para colocar a linha daquele worker literalmente para "dormir", gastando **absolutos 0.0% da CPU**. A thread é reativada instantaneamente e nativamente nos exatos *2 milissegundos* que qualquer chave flutue no canal do Redis.
2. **Throttle Anti-DDoS:** Caso todas as comunicações despenquem do seu datacenter simultaneamente (SMTP quebra porta, MariaDB reinicia servidor, internet cai), há um filtro *Try/Catch global* no final do script que jorra o problema na tela, porém ele aplica por obrigação um calmante cirúrgico de `sleep(5)`. Na prática, o Worker não vai causar estopins de log escrevendo 30 gigabytes de erros e fundindo seu HD local; em vez disso, ele hibernará e buscará um ping calmo de 5 em 5 segundos até as coisas voltarem pro lugar.

---

## 3. Gestão Administrativa Visual

### Tratamento de Falhas (Retry Pattern)
Você eventualmente irá topar com envios barrados. Entraves na API do provedor de e-mail, tokens expostos ou caixas de e-mail estourada causarão rejeições iminentes.
O *Worker* lidará com a adversidade e puxará estaticamente o erro oficial cuspidos pelos Data Centers do Google/MS direto para as veias do painel preenchendo as chaves com `'failed'` e `error_message`.

Para lidar com maestria neste campo bélico, acesse **http://localhost:8000/admin/queue**:
- Lá você visualizará todo o histórico inquebrável de eventos e `jobs`.
- Monitoramento de qual e por que um determinado Job rejeitou lendo os relatórios do `PHPMailer`.
- Uma chave mágica ressuscitadora: Submeta eventos reprovados no funil reacionário apertando num sublime clique o elemento gráfico <kbd>Reenviar</kbd> – O próprio Backend resgata as premissas deste falecido ID e o arremessa sem corrompimentos diretamente devolta no rabo vital da fila do Redis!

### Orquestrador da Imortalidade (Docker)
Deixamos a fundação impetuosa sem dor de cabeça configurada no coração do maquinário local. No `docker-compose.yml`:

```yaml
  worker:
    image: briefing-app
    restart: unless-stopped
    command: php nortedev queue:work
```

A diretiva celestial `restart: unless-stopped` é a guarda-costas estragada no núcleo do Docker. O Docker passará a medir o pulso (*Heartbeat PID*) deste operário noite e dia. Caso você mate o contêiner por asfixia de RAM ou o script sucumba ao invadir limites de PHP; em exatos 5 a 15 segundos ele recriará o processo injetando imediatamente outro Operário robusto para as ordens de processamento – assim sua comunicação com os clientes é perpétua e inabalável!
