<?php

class UsuarioDAO
{
    const SENHA_PADRAO = "123456";

    public static function cadastrarUsuario ()
    {
        try
        {
            UtilDAO::executeQueryParam(Querys::INSERT_USUARIO, $_REQUEST ["nome"], $_REQUEST ["email"], md5(self::SENHA_PADRAO), $_REQUEST ["perfil"]);

            Ajax::RespostaSucesso("Usuário cadastrado com sucesso.", true, Ajax::TIPO_SUCCESS);
        } catch (Exception $e)
        {
            Ajax::RespostaErro("Falha ao salvar perfil.", $e);
        }
    }

    public static function modificarUsuario ()
    {
        try
        {
            UtilDAO::executeQueryParam(Querys::UPDATE_USUARIO_PERFIL, $_REQUEST ["nome"], $_REQUEST ["email"], $_REQUEST ["perfil"], $_REQUEST ["id_usuario"]);

            Ajax::RespostaSucesso("Usuário modificado com sucesso.", true, Ajax::TIPO_SUCCESS);
        } catch (Exception $e)
        {
            Ajax::RespostaErro("Falha ao salvar perfil.", $e);
        }
    }

    public static function desativarUsuario ()
    {
        try
        {
            UtilDAO::executeQueryParam(Querys::UPDATE_USUARIO_ATIVO, ($_REQUEST["ativo"] == "S" ? "N" : "S"), $_REQUEST ["id_usuario"]);

            Ajax::RespostaSucesso("Usuário modificado com sucesso.", true, Ajax::TIPO_SUCCESS);
        } catch (Exception $e)
        {
            Ajax::RespostaErro("Falha ao salvar perfil.", $e);
        }

    }

    public static function login ()
    {
        try
        {
            $retorno = UtilDAO::getResult(Querys::SELECT_USUARIO_BY_USUARIO_ATIVO, $_REQUEST ["login"]);
            if (count($retorno) == 1 && empty($retorno[0]->senha))
            {
                $_REQUEST ["id_usuario"] = $retorno[0]->usuario_id;
                self::ResetSenha();
            }

            $retorno = UtilDAO::getResult(Querys::SELECT_LOGIN, $_REQUEST ["login"], md5($_REQUEST ["senha"]));
            if (count($retorno) == 0)
            {
                Ajax::RespostaErro("Usuário e/ou Senha incorretos.");
            }

            $_SESSION ['NOME_USUARIO'] = $retorno [0]->nome;
            $_SESSION ['ID_USUARIO'] = $retorno [0]->usuario_id;
            $_SESSION ['USUARIO'] = $retorno [0]->usuario;
            $_SESSION ['PERFIL'] = $retorno [0]->perfil;
            $_SESSION ['TULEAP_USER'] = $retorno [0]->tuleap_user;
            $_SESSION ['TULEAP_PASS'] = $retorno [0]->tuleap_pass;

            Ajax::RespostaSucesso("", true, Ajax::TIPO_SUCCESS);
        } catch (Exception $e)
        {
            Ajax::RespostaErro("Falha ao logar.", $e);
        }
    }

    public static function ResetSenha ()
    {
        try
        {
            UtilDAO::executeQueryParam(Querys::UPDATE_USUARIO_RESET_SENHA, md5(self::SENHA_PADRAO), $_REQUEST ["id_usuario"]);

            Ajax::RespostaSucesso("Resetado senha com sucesso.", true, Ajax::TIPO_SUCCESS);
        } catch (Exception $e)
        {
            Ajax::RespostaErro("Falha ao resetar senha.", $e);
        }
    }

    public static function salvarPerfil ()
    {
        try
        {
            // Se for trocar senha
            if (isset ($_REQUEST ["senha_atual"]) && !empty ($_REQUEST ["senha_atual"]))
            {
                $retorno = UtilDAO::getResult(Querys::SELECT_LOGIN, $_REQUEST ["email"], md5($_REQUEST ["senha_atual"]));
                if (count($retorno) == 0)
                {
                    Ajax::RespostaErro("Senha incorreta.");
                }

                UtilDAO::executeQueryParam(Querys::UPDATE_USUARIO_SENHA, $_REQUEST ["nome"], $_REQUEST ["email"], md5($_REQUEST ["nova_senha"]), $_REQUEST ["tuleap_user"], $_REQUEST ["tuleap_senha"], $_REQUEST ["id_usuario"]);
                $_REQUEST ["login"] = $_REQUEST ["email"];
                $_REQUEST ["senha"] = $_REQUEST ["nova_senha"];
                self::login();
            }
            else
            {
                UtilDAO::executeQueryParam(Querys::UPDATE_USUARIO, $_REQUEST ["nome"], $_REQUEST ["email"], $_REQUEST ["tuleap_user"], $_REQUEST ["tuleap_senha"], $_REQUEST ["id_usuario"]);

                $_SESSION ['NOME_USUARIO'] = $_REQUEST ["nome"];
                $_SESSION ['USUARIO'] = $_REQUEST ["email"];
                $_SESSION ['TULEAP_USER'] = $_REQUEST ["tuleap_user"];
                $_SESSION ['TULEAP_PASS'] = $_REQUEST ["tuleap_senha"];
            }

            Ajax::RespostaSucesso("Usuário modificado com sucesso.", true, Ajax::TIPO_SUCCESS);
        } catch (Exception $e)
        {
            Ajax::RespostaErro("Falha ao salvar perfil.", $e);
        }
    }
}