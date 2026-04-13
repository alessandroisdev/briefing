<?php

declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

class DefaultBriefingTemplateSeeder extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * https://book.cakephp.org/phinx/0/en/seeding.html
     */
    public function run(): void
    {
        $schema = [
            ['label' => 'Descrição do Modelo de Negócio: O que sua empresa faz e como este projeto se encaixa na estratégia atual?', 'type' => 'textarea'],
            ['label' => 'O Problema Central: Qual é a principal "dor" que este sistema deve resolver?', 'type' => 'textarea'],
            ['label' => 'Público-Alvo e Stakeholders: Quem são as pessoas que utilizarão o sistema?', 'type' => 'textarea'],
            ['label' => 'Objetivos de Curto e Longo Prazo: O que o sistema precisa fazer hoje e o que ele deve estar preparado para fazer daqui a 2 anos?', 'type' => 'textarea'],
            ['label' => 'Jornada do Usuário: Descreva o passo a passo do processo principal', 'type' => 'textarea'],
            ['label' => 'Níveis de Acesso (ACL): Quais perfis de usuário existirão?', 'type' => 'textarea'],
            ['label' => 'Funcionalidades Críticas: Liste as 5 funcionalidades sem as quais o projeto não pode ser lançado (MVP)', 'type' => 'textarea'],
            ['label' => 'Gestão de Dados: O sistema precisa gerar relatórios? Quais métricas são vitais?', 'type' => 'textarea'],
            ['label' => 'Automações e Notificações: Descreva a necessidade de disparos de e-mail, SMS ou WhatsApp', 'type' => 'textarea'],
            ['label' => 'Ecossistema Existente: O sistema precisará se comunicar com softwares que você já utiliza?', 'type' => 'textarea'],
            ['label' => 'Volume de Dados Estimado: Qual a expectativa de acessos simultâneos e volume de registros?', 'type' => 'textarea'],
            ['label' => 'Dispositivos de Acesso: O foco é Desktop, Mobile ou híbrido?', 'type' => 'textarea'],
            ['label' => 'Integração de Hardware: Existe necessidade de comunicação com periféricos?', 'type' => 'textarea'],
            ['label' => 'Identidade Visual: Existe um manual de marca, paleta de cores ou logotipo definido?', 'type' => 'textarea'],
            ['label' => 'Referências de Mercado: Cite 2 ou 3 plataformas que possuem uma usabilidade que você admira', 'type' => 'textarea'],
            ['label' => 'Tom de Voz: A interface deve ser corporativa, moderna ou vibrante?', 'type' => 'text'],
            ['label' => 'Acessibilidade: Existe alguma necessidade específica para os usuários?', 'type' => 'textarea'],
            ['label' => 'LGPD: O sistema lidará com dados sensíveis de terceiros?', 'type' => 'textarea'],
            ['label' => 'Segurança de Acesso: Há necessidade de Autenticação 2FA ou logs de auditoria?', 'type' => 'textarea'],
            ['label' => 'Hospedagem e Servidores: Você já possui infraestrutura ou precisa de projetos de setup?', 'type' => 'textarea'],
            ['label' => 'Domínio: Já possui o endereço web registrado?', 'type' => 'text'],
            ['label' => 'Prazo de Lançamento: Qual a data ideal para o sistema entrar em produção?', 'type' => 'text'],
            ['label' => 'Fases do Projeto: Existe interesse em um lançamento faseado?', 'type' => 'textarea'],
            ['label' => 'Expectativa de Investimento: Qual a faixa de orçamento reservada para o desenvolvimento?', 'type' => 'text'],
        ];

        $description = '<h1>Questionário de Diagnóstico e Briefing de Projeto</h1>
<p><strong>Por favor, responda com o máximo de detalhes que possuir sobre sua infraestrutura e objetivos.</strong></p>
<p>As seções abordam desde a visão de negócios (Objetivos e Dores), passando pelas funcionalidades vitais até a infraestrutura e design esperado.</p>';

        $data = [
            [
                'title'       => 'Diagnóstico Completo de Software / SaaS',
                'description' => $description,
                'form_schema' => json_encode($schema, JSON_UNESCAPED_UNICODE),
                'status'      => 'active',
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ]
        ];

        $table = $this->table('briefing_templates');
        $table->insert($data)->saveData();
    }
}
