<?php
require_once(PAGINA_DEFAULT);

$tpl->CAMINHO_PAGINA = '';
$tpl->NOME_PAGINA = '';

$tpl->addFile('CONTEUDO_CENTRAL',  'default.html');

$result = UtilDAO::getResult(Querys::SELECT_DASHBOARD);
if (isset($result[0]))
{
    $ultimo = $result[0]->group_name;

    $tpl->GROUP_NAME = $result[0]->group_name;
    $tpl->GROUP_ID = $result[0]->group_id;
}
foreach ($result as $row)
{
    if ($ultimo != $row->group_name)
    {
        $tpl->block('BLOCK_GROUP');

        $tpl->GROUP_NAME = $row->group_name;
        $tpl->GROUP_ID = $row->group_id;

        $ultimo = $row->group_name;
    }

    $tpl->TYPE = $row->type;
    $tpl->QTD = $row->qtd_aberto;
    $tpl->TOTAL = $row->qtd;
    $tpl->block('BLOCK_STATUS');
}

$tpl->block('BLOCK_GROUP');

$tpl->show();