<?php
require_once(PAGINA_DEFAULT);

$tpl->addFile('CONTEUDO_CENTRAL', 'conteudo/viewUsuario.html');

$tpl->CHAMADA_AJAX = CHAMADA_AJAX;
$tpl->NOME_PAGINA = 'Pesquisar Usuário';
$tpl->DESCRICAO_PAGINA = '';

if (!empty ($_POST))
{
    $where = '';
    $parametro = [];
    
    $pos = count($parametro) + 1;
    if (!empty ($_POST ['nome']))
    {
        $tpl->VALUE_NOME = $_POST ['nome'];

        $where .= " AND nome ~ \${$pos}";
        $parametro[] = $_POST ['nome'];
    }
    if (!empty ($_POST ['email']))
    {
        $tpl->VALUE_EMAIL = $_POST ['email'];

        $where .= " AND usuario ~ \${$pos}";
        $parametro [] = $_POST ['email'];
    }
    if (isset ($_POST ['perfil']) and ($_POST ['perfil'] == '0' or !empty ($_POST ['perfil'])))
    {
        $tpl->SCRIPT_PERFIL = Util::SendReadyScript('$("#perfil").val("' . $_POST ['perfil'] . '");');

        $where .= " AND perfil = \${$pos}";
        $parametro [] = $_POST ['perfil'];
    }
    foreach (UtilDAO::getResultArrayParam(Querys::SELECT_USUARIO . $where, $parametro) as $row)
    {
        $tpl->ID_USUARIO = $row->usuario_id;
        $tpl->NOME = $row->nome;
        $tpl->EMAIL = $row->usuario;

        switch ($row->perfil)
        {
            case PERFIL_0_ADMIN :
                $tpl->PERFIL = 'Administrador';
                break;
            case PERFIL_1_ESCRITA :
                $tpl->PERFIL = 'Escrita';
                break;
            case PERFIL_2_CONSULTA :
                $tpl->PERFIL = 'Consulta';
                break;
        }

        $tpl->ATIVO = ($row->ativo == 'S' ? 'Ativo' : 'Inativo');

        $tpl->block('BLOCK_VALORES');
    }
}

$tpl->show();