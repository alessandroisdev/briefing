<?php

declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

class QuotationTemplatesSeeder extends AbstractSeed
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
        $templates = [
            [
                'title' => 'Landpage Premium de Alta Conversão',
                'description' => 'Criação de uma Landing Page focada em resultados, design moderno, responsiva e otimizada para SEO.',
                'base_items_json' => json_encode([
                    ['description' => 'UI/UX Design Exclusivo Premium (Figma)', 'quantity' => 1, 'unit_price' => 1200.00],
                    ['description' => 'Desenvolvimento Front-end Responsivo (HTML/CSS/JS)', 'quantity' => 1, 'unit_price' => 1800.00],
                    ['description' => 'Configuração de Servidor e Domínio', 'quantity' => 1, 'unit_price' => 300.00],
                ]),
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'title' => 'E-commerce Institucional Completo',
                'description' => 'Plataforma de loja virtual robusta com gestão de estoque, gateway de pagamentos e painel administrativo.',
                'base_items_json' => json_encode([
                    ['description' => 'Configuração de Plataforma E-commerce Mestre', 'quantity' => 1, 'unit_price' => 3500.00],
                    ['description' => 'Integração de Gateways de Pagamento (Asaas/Stripe)', 'quantity' => 1, 'unit_price' => 800.00],
                    ['description' => 'Cadastro Inicial de Produtos (Até 50 itens)', 'quantity' => 50, 'unit_price' => 10.00],
                    ['description' => 'Treinamento de Equipe / Documentação', 'quantity' => 1, 'unit_price' => 500.00],
                ]),
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'title' => 'Desenvolvimento de Sistema (SaaS / Web App Customizado)',
                'description' => 'Arquitetura e desenvolvimento de um sistema web modular escalável baseado em MVC.',
                'base_items_json' => json_encode([
                    ['description' => 'Levantamento de Requisitos e Arquitetura de Banco', 'quantity' => 1, 'unit_price' => 2000.00],
                    ['description' => 'Desenvolvimento Backend (API / Core Business, horas estimadas)', 'quantity' => 80, 'unit_price' => 120.00],
                    ['description' => 'Desenvolvimento Frontend (Painel Administrativo)', 'quantity' => 1, 'unit_price' => 4500.00],
                    ['description' => 'Testes e Homologação', 'quantity' => 1, 'unit_price' => 1500.00],
                ]),
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'title' => 'Integração de APIs / Refatoração Backend',
                'description' => 'Serviço avançado de engenharia de software para comunicar sistemas ou refatorar legados.',
                'base_items_json' => json_encode([
                    ['description' => 'Análise de Código e Planejamento', 'quantity' => 1, 'unit_price' => 1000.00],
                    ['description' => 'Construção de Webhooks e Consumo de API Externa', 'quantity' => 20, 'unit_price' => 150.00],
                ]),
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'title' => 'Aplicativo Mobile Nativo / PWA',
                'description' => 'Desenvolvimento de App publicado nas lojas com alta performance.',
                'base_items_json' => json_encode([
                    ['description' => 'Prototipação Mobile UI/UX', 'quantity' => 1, 'unit_price' => 2500.00],
                    ['description' => 'Desenvolvimento App iOS / Android (React Native)', 'quantity' => 1, 'unit_price' => 12000.00],
                    ['description' => 'Publicação nas Lojas (Apple e Play Store)', 'quantity' => 1, 'unit_price' => 800.00],
                ]),
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];

        $table = $this->table('quotation_templates');
        $table->insert($templates)->saveData();
    }
}
