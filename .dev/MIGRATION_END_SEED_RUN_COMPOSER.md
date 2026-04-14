# Composer

Para o ecossistema de Banco de Dados deste projeto, nós acoplamos a arquitetura robusta do **Phinx** (O ORM de migrações oficial adotado pelo CakePHP e muitos sistemas modernos). Ele já disponibiliza as ferramentas binárias prontas nativamente!

Para te dar aquela experiência deliciosa de ambiente maduro (parecido com o `artisan` do Laravel), eu acabei de turbinar o seu arquivo `composer.json` injetando **atalhos de Comando Customizados**. 

Agora você (ou os robôs na nuvem de produção) nunca mais vão precisar digitar caminhos longos! Ao invés de rodar binários complexos, você gerencia seu banco pelo terminal de forma muito simples:

### 1. Migrations (Evolução de Tabelas)
Para rodar suas tabelas que estão lá na pasta `database/migrations`:
* No seu Windows (Desenvolvimento Local):
  ```bash
  composer migrate
  ```
* Se estiver conectado lá no servidor HostGator (Produção):
  ```bash
  composer migrate:prod
  ```

### 2. Seeds (Plantio de Dados Mágicos)
Para rodar os dados fantasmas e as tabelas padrões que deixamos prontas nos `database/seeds`:
```bash
composer seed
```

### 3. Cenário "Terra Arrasada" (Reset Total)
*Atenção: Use apenas e estritamente no seu Windows (Desenvolvimento!)* 
Se você errar muitas colunas durante o dia de desenvolvimento e quiser implodir seu banco e recriá-lo fresco de uma vez só:
```bash
composer db:refresh
```
*(Esse comando roda o rollback em tudo, dispara as migrations do zero e automaticamente já chama suas seeds, deixando o banco novinho e populado em menos de 1 segundo!)*.
