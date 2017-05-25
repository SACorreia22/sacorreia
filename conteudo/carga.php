<?php
require_once(PAGINA_DEFAULT);

$tpl->CAMINHO_PAGINA = '';
$tpl->NOME_PAGINA = '';

$tpl->addFile('CONTEUDO_CENTRAL',  'conteudo/carga.html');

$cargas = [
//    [
//        'TIPO' => 'projeto',
//        'NOME' => 'Projeto',
//        'FUNC' => 'inserirDadosProjeto'
//    ],
//    [
//        'TIPO' => 'tracker',
//        'NOME' => 'Tracker',
//        'FUNC' => 'inserirDadosTracker'
//    ],
    [
        'TIPO' => 'artifacts',
        'NOME' => 'Artefato',
        'FUNC' => 'inserirDadosArtifacts'
    ],
    [
        'TIPO' => 'usuario',
        'NOME' => 'Usuario',
        'FUNC' => 'inserirDadosUsuario'
    ]
];

$saida = '';
$time_start = microtime(true);
$tuleap = new Tuleap($_SESSION ['TULEAP_USER'], $_SESSION ['TULEAP_PASS']);
foreach ($cargas as $index => $item)
{
    if (isset($_POST[$item['TIPO']]))
    {
        $saida = $tuleap->{$item['FUNC']}();
    }

    $tpl->TIPO = $item['TIPO'];
    $tpl->NOME = $item['NOME'];
    $tpl->block('BLOCK_CARGA');
}

$time = microtime(true) - $time_start;

$tpl->SAIDA_WS = "Process Time: {$time}<pre>{$saida}</pre>";

$tpl->show();