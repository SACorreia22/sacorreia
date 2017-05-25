<?php
require_once($_SERVER ['DOCUMENT_ROOT'] . "/dashboard/codigo/portal/ConstantesPortal.php");

$arquivo = "{$_REQUEST['file']}";

// Registradores de erro
register_shutdown_function("check_for_fatal");
set_error_handler("log_error");
set_exception_handler("log_exception");
error_reporting(E_ALL);
ini_set("display_errors", "off");

// em caso de DEBUG
if (DEBUG)
{
    ini_set('display_errors', 1);
    error_log("CHAMADA => {$arquivo}");
}

// Registra o AutoLoad
spl_autoload_register('Init::autoload', true, true);

// Inicializa o Pool de Conexao
new PoolConexao();

// Carrega o arquivo pedido
require $arquivo;

class Init
{
    /**
     * Tratamento de erro padrão do portal
     *
     * @param string    $msg
     * @param Exception $ex
     * @throws Exception
     */
    public static function trataErro (string $msg, Exception $ex = null)
    {
        log_exception($ex, $msg);
    }

    public static function autoload ($className)
    {
        self::carregaClasseRecursiva($_SERVER ['DOCUMENT_ROOT'] . DIRETORIO_CODIGO, strtolower("$className.php"));
    }

    public static function generateCallTrace (Exception $e)
    {
        if (!DEBUG)
        {
            return;
        }

        $trace = explode("\n", $e->getTraceAsString());

        return implode("<br>", $trace);
    }

    public static function carregaClasseRecursiva ($directory, $classe)
    {
        foreach (scandir($directory) as $file)
        {
            if ($file == '.' || $file == '..')
            {
                continue;
            }

            if (is_dir($directory . DIRECTORY_SEPARATOR . $file))
            {
                if (self::carregaClasseRecursiva($directory . DIRECTORY_SEPARATOR . $file, $classe))
                {
                    return;
                }
            }
            elseif (strtolower($file) == $classe)
            {
                require_once($directory . DIRECTORY_SEPARATOR . $file);

                return true;
            }
        }

        return false;
    }
}


/**
 * Error handler, passes flow over the exception logger with new ErrorException.
 */
function log_error ($num, $str, $file, $line, $context = null)
{
    log_exception(new ErrorException($str, 0, $num, $file, $line));
}

/**
 * Uncaught exception handler.
 */
function log_exception (Exception $e, $message = null)
{
    if (!empty($message))
    {
        $message = "<br>$message";
    }

    $trace = Init::generateCallTrace($e);

    print "<div style='text-align: center;'>
    <h2 style='color: rgb(190, 50, 50);'>Ocorreu uma Exceção:</h2>
    <table style='width: 80%; display: inline-table;'>
    <tr style='background-color:rgb(230,230,230);'><th style='width: 80px;'>Type</th><td>" . get_class($e) . "</td></tr>
    <tr style='background-color:rgb(240,240,240);'><th>Message</th><td>{$e->getMessage()}{$message}</td></tr>
    <tr style='background-color:rgb(230,230,230);'><th>File</th><td>{$e->getFile()}</td></tr>
    <tr style='background-color:rgb(240,240,240);'><th>Line</th><td>{$e->getLine()}</td></tr>
    <tr style='background-color:rgb(230,230,230);'><th>Trace</th><td><pre>{$trace}</pre></td></tr>
    </table></div>";

    exit();
}

/**
 * Checks for a fatal error, work around for set_error_handler not working on fatal errors.
 */
function check_for_fatal ()
{
    $error = error_get_last();
    if ($error["type"] == E_ERROR)
    {
        log_error($error["type"], $error["message"], $error["file"], $error["line"]);
    }
}