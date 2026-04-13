# Criando Comandos na CLI `nortedev` (Guia do Desenvolvedor)

A infraestrutura da CLI corporativa baseia-se fortemente no componente **Symfony Console**. O executável `www/nortedev` é a porta de entrada que carrega todo o ecossistema (Variáveis de Ambiente `.env`, Banco de Dados, e Autoload do Composer).

Este guia cobre passo a passo como você e sua equipe podem estender a ferramenta para automatizar minerações de dados, disparo de cronjobs, relatórios e limpezas regulares.

---

## 1. Passo a Passo: Criando o seu Primeiro Comando

Todos os comandos devem viver estruturalmente no diretório:
**`www/app/Console/Commands/`**

### Exemplo Básico de Esqueleto: `SayHelloCommand.php`
Crie um arquivo PHP e estenda a classe fundamental `Symfony\Component\Console\Command\Command`:

```php
<?php

namespace App\Console\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Attribute\AsCommand;

// O atributo PHP 8 \AsCommand define a assinatura de chamada terminal (nome) e a descrição legível
#[AsCommand(name: 'app:hello', description: 'Imprime uma mensagem global amigável no terminal.')]
class SayHelloCommand extends Command
{
    // A inteligência mecânica vai residir na função execute()
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('<info>Olá, mundo! A NorteDev CLI está funcionando!</info>');

        // Sempre retorne Command::SUCCESS (0) indicando integridade.
        return Command::SUCCESS; 
    }
}
```

### Registrando o Comando no Kernel
Diferente de frameworks monolíticos (como Laravel que escaneiam pastas auto-magicamente causando peso enorme no boot), nossa infraestrutura é enxuta. Vá até o coração da ferramenta em `www/nortedev` e registre a classe para que ela exista no menu de `list`:

Abra `www/nortedev` e abaixo do registro do Worker, adicione:
```php
$application->addCommand(new \App\Console\Commands\SayHelloCommand());
```

A partir de agora, digitando `php nortedev app:hello` no terminal do Docker, o sistema executará seu script!

---

## 2. Lidando com Argumentos e Opções (Inputs)

Você raramente vai rodar comandos mudos. Você geralmente quer passar variáveis, como `php nortedev relatorio:gerar --mes=04` ou `php nortedev user:delete 15`.

### Utilizando `configure()`
Adicione o método `configure()` antes de seu `execute()` para forçar a exigência estatutária dessas variáveis.

```php
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

protected function configure(): void
{
    $this
        // Argumentos requeridos/obrigatórios (O que?) no comando
        ->addArgument('user_id', InputArgument::REQUIRED, 'O ID do usuário a ser banido.')
        ->addArgument('motivo', InputArgument::OPTIONAL, 'Motivo do banimento.', 'Motivo não especificado')
        
        // Opções (Flags --nome=valor) parametrizadas
        ->addOption('force', 'f', InputOption::VALUE_NONE, 'Força o apagamento irrecuperável do banco');
}
```

### Lendo os Inputs no `execute()`
```php
protected function execute(InputInterface $input, OutputInterface $output): int
{
    $userId = $input->getArgument('user_id');
    $motivo = $input->getArgument('motivo');
    $forced = $input->getOption('force'); // Retornará booleano caso seja InputOption::VALUE_NONE

    $output->writeln("Iniciando ação no usuário ID: <comment>{$userId}</comment>");
    
    if($forced) {
        $output->writeln("<error>Modo FORCED ativado! Não há volta.</error>");
    }

    return Command::SUCCESS;
}
```
**Como você usaria no Terminal?**
`php nortedev app:ban-user 55 "Violação de Termos" --force` ou `php nortedev app:ban-user 55 -f`

---

## 3. Saídas, Cores e Formatações (Output)

Utilizar `echo` no CLI é uma abominação; ele não lida com quebra de fluxos entre terminais Linux, Window ou MacOS. Confie no objeto `$output->writeln()`.

### As 4 Cores Universais:
* `<info>Verde</info>`: Usado para sucessos, aprovações, e dados seguros (`writeln('<info>Processamento concluído</info>')`).
* `<comment>Amarelo</comment>`: Usado para variáveis, alertas visuais chamativos e parâmetros.
* `<error>Fundo Vermelho</error>`: Apenas para exceções cataclísmicas que romperam o script.
* `<question>Fundo Ciano/Azul</question>`: Geralmente para Inputs iterativos ou perguntas diretas.

### Interações Visuais (Symfony Style)
Se você quer um visual altamente corporativo (com caixas de textos preenchidas, tabelas, e barras de progresso), você deve criar o Objeto *SymfonyStyle* logo na primeira linha do *execute*:

```php
use Symfony\Component\Console\Style\SymfonyStyle;

protected function execute(InputInterface $input, OutputInterface $output): int
{
    $io = new SymfonyStyle($input, $output);
    
    $io->title('Gerador de Faturas NorteDev');
    $io->success('Conexão ao ERP iniciada.');
    
    // Mostrando uma Barra de Progresso elegante que atualiza ao vivo na mesma div (Sem quebrar a linha):
    $io->progressStart(100);
    for ($i = 0; $i < 100; $i++) {
        // Gerar Fatura Lógica Aqui
        usleep(100000);
        $io->progressAdvance();
    }
    $io->progressFinish();

    return Command::SUCCESS;
}
```

---

## 4. Arquitetura e Precauções Extremamente Importantes

### 4.1 Loops Infinitos Acidentais no Banco de Dados
Se o script processa grande quantia de dados brutos (`Model::all()`) ele fará a memória RAM do terminal sangrar e crachar (Fatal Error: Allowed memory size exhausted). O `PHP CLI` por padrão não tem tempo limite e consome memória ilimitada!

**Solução (Paginação de Memória)**:
Em vez de buscar 20 mil clientes de uma vez:
```php
// RUIM: Vai Estourar RAM do Servidor
$clients = Client::all(); 
foreach($clients as $c) { }

// EXCELENTE: Eloquent Chunking busca e processa 100 usuários limpos por vez destruindo lixo da memória.
Client::chunk(100, function ($clients) {
    foreach ($clients as $client) {
        // processar $client;
    }
});
```

### 4.2 Lixo Resgatado (Garbage Collection Manual)
Se o processamento for um Daemon vitalício que nunca morre (Exemplo: o NOSSO `queue:work`):
Sempre cuide para destruir e esvaziar variáveis massivas utilizando `unset($dataArray)` se carregar relatórios muito longos. O daemon sobrevive meses dormindo e a menor alocação estática vira uma bola de neve. 

### 4.3 Lidando com Dependências Temporais
Use sem medo a variável mágica injetada no topo do `nortedev`. O App instancia o `.env`, o Database global (`App\Core\Database.php`) e os arquivos do Composer. Então você não precisa se conectar, dar 'Session Starts' ou dar Include em rotas. Apenas invoque os Modelos Eloquent (`User::find(1)`) normalmente nas lógicas do comando!

> [!WARNING]
> Fique Atento a Modos Incompletos: 
> Um `Command` deve por convenção **SEMPRE** retornar um inteiro correspondente do Shell POSIX em seu final final (Tentar dar `return true` vai gerar Fatal Error). No sucesso estipe `return Command::SUCCESS`, ou na falha técnica fatal chame `return Command::FAILURE`. O Docker usará esse estande final para identificar se ele precisa reiniciar sua máquina subitamente.
