<?php

/**
 * Created by PhpStorm.
 * User: saulocorreia
 * Date: 18/05/2017
 * Time: 15:54
 */
class TabelasDAO
{
    public static function buscarTabelas ()
    {
        $resultado = [];

        $tabelas = explode("\r\n", $_REQUEST['tabelas']);
        foreach ($tabelas as $index => $tabela)
        {
            $tabela = strtolower(trim($tabela));

            if (empty($tabela))
            {
                continue;
            }

            $dados = explode('.', $tabela);
            $result = UtilDAO::getResult(Querys::SELECT_TABELA_BY_SCHEMA_TABELA, $dados[0], $dados[1]);

            if (empty($result))
            {
                $resultado[] = (object) [
                    'nome'       => $tabela,
                    'comentario' => '',
                    'id_tabela'  => ''
                ];
            }
            else
            {
                $resultado[] = $result[0];
            }
        }

        Ajax::RespostaGenerica('', '', false, $resultado);
    }

    public static function salvarTabela ()
    {
        $tabela = key($_REQUEST['comentario']);
        $id = key($_REQUEST['comentario'][$tabela]);

        $schemaETabela = explode('.', $tabela);

        try
        {
            if (empty($id))
            {
                UtilDAO::executeQueryParam(Querys::INSERT_TABELA_SUGESTAO, $schemaETabela[0], $schemaETabela[1], $_REQUEST['comentario'][$tabela][$id]);
            }
            else
            {
                UtilDAO::executeQueryParam(Querys::UPDATE_TABELA_SUGESTAO, $schemaETabela[0], $schemaETabela[1], $_REQUEST['comentario'][$tabela][$id]);
            }

            Ajax::RespostaSucesso("Comentário da tabela {$tabela} salva com sucesso.", false);
        } catch (Exception $e)
        {
            Ajax::RespostaErro("Falha ao salvar comentário da tabela {$tabela}.", $e);
        }
    }
}