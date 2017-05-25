<?php

class Conexao
{
    public $Conexao;

    /**
     * Conexao constructor.
     */
    public function __construct ()
    {
        try
        {
            $this->Conexao = pg_connect(BANCO_URL);
            $this->beginTransaction();

            if (DEBUG)
            {
                pg_query(Querys::CREATE_TABLE);
                $this->commit();
            }
        } catch (Exception $e)
        {
            Init::trataErro("Falha ao abrir conexão", $e);
        }
    }

//    function __destruct ()
//    {
//        pg_close($this->Conexao);
//    }

    public function close ()
    {
        //return parent::close();
    }

    public function commit ()
    {
        return pg_query($this->Conexao, 'COMMIT');
    }

    public function rollback ()
    {
        return pg_query($this->Conexao, 'ROLLBACK');
    }

    public function beginTransaction ()
    {
        return pg_query($this->Conexao, 'BEGIN');
    }

    public function query ($query)
    {
        return pg_query($this->Conexao, $query);
    }

    public function prepare ($query, $parameters = [])
    {
        try
        {
            return $this->prepare_execute($query, $parameters, true);
        } catch (Exception $e)
        {
            throw $e;
        }
    }

    public function execute ($query, $parameters = [])
    {
        $this->prepare_execute($query, $parameters, false);
    }

    private function prepare_execute ($query, $parameters = [], $result)
    {
        try
        {
            $sqlName = 'select' . date('D, d M Y H:i:s');

            pg_prepare($this->Conexao, $sqlName, $query);

            $rs = pg_execute($this->Conexao, $sqlName, $parameters);

            $retorno = [];
            if ($result)
            {
                $retorno = pg_fetch_all($rs, PGSQL_BOTH);
            }

            $sql = sprintf(
                'DEALLOCATE "%s"',
                pg_escape_string($this->Conexao, $sqlName)
            );
            if (!$this->query($sql))
            {
                die("Can't query '{$sql}': " . pg_last_error());
            }

            $this->commit();

            if ($result)
            {
                return $retorno;
            }
        } catch (ErrorException $e)
        {
            error_log("entrei");
            Init::trataErro(SqlFormatter::format(forward_static_call_array([
                    'self',
                    'MontarQuery'
                ], array_merge([
                    $query
                ], $parameters))) . "<br>" . str_replace("\n", "<br>", $e));
        }
    }
}
