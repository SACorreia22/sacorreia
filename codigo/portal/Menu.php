<?php

class Menu
{
    const MENU = [
        [
            'fa'         => 'fa-link',
            'text'       => 'Carga',
            'permission' => PERFIL_1_ESCRITA,
            'url'        => DIRETORIO_CONTEUDO . '/carga',
        ],
        [
            'fa'       => 'fa-file-text-o',
            'text'     => 'Documentação',
            'url'      => '#',
            'children' => [
                [
                    'fa'   => 'fa-file-text-o',
                    'text' => 'Gerar Documentação',
                    'url'  => DIRETORIO_CONTEUDO . '/documentacao',
                ],
                [
                    'fa'   => 'fa-table',
                    'text' => 'Buscar Tabelas',
                    'url'  => DIRETORIO_CONTEUDO . '/tabela',
                ]
            ]
        ],
        [
            'fa'         => 'fa-cogs',
            'text'       => 'Configurações',
            'url'        => '#',
            'permission' => PERFIL_1_ESCRITA,
            'children'   => [
//                array(
//                    'fa'       => 'fa-building',
//                    'text'     => 'Cadastrar Empresas',
//                    'url'      => DIRETORIO_CONTEUDO . '/addEmpresa',
//                    'children' => null
//                ),
                [
                    'fa'       => 'fa-user',
                    'text'     => 'Usuário',
                    'url'      => '#',
                    'children' => [
                        [
                            'fa'   => 'fa-user-plus',
                            'text' => 'Cadastrar',
                            'url'  => DIRETORIO_CONTEUDO . '/addUsuario'
                        ],
                        [
                            'fa'   => 'fa-search',
                            'text' => 'Pesquisar',
                            'url'  => DIRETORIO_CONTEUDO . '/viewUsuario'
                        ]
                    ]
                ]

            ]
        ],
        /*
         *         array(
                    'fa'       => 'fa-line-chart',
                    'text'     => 'Relatório',
                    'url'      => '#',
                    'children' => array(
                        array(
                            'fa'       => 'fa-bar-chart',
                            'text'     => 'Acompanhamento de Datas',
                            'url'      => DIRETORIO_CONTEUDO . '/slaDatas',
                            'children' => null
                            ),
                            array (
                                    'fa' => 'fa-bar-chart',
                                    'text' => 'Alterações por Período',
                                    'url' => DIRETORIO_CONTEUDO . '/#',
                                    'children' => NULL
                            
                ),
                array(
                    'fa'       => 'fa-history',
                    'text'     => 'Histórico do Contrato',
                    'url'      => DIRETORIO_CONTEUDO . '/historico',
                    'children' => null
                )
            )
        )
    */
    ];

    public static function getMenu ()
    {
        return self::MENU;
    }
}