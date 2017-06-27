<?php

class UsuarioDAO
{
    public static function cadastrarUsuario ()
    {
        try
        {
            \Portal\Cache::flushMemcache();

            UtilDAO::executeQueryParam(Querys::INSERT_USUARIO, $_REQUEST ['nome'], $_REQUEST ['email'], $_REQUEST ['perfil']);

            Portal\Ajax::RespostaSucesso('Usuário cadastrado com sucesso.', true, Portal\Ajax::TIPO_SUCCESS);
        } catch (Exception $e)
        {
            Portal\Ajax::RespostaErro('Falha ao salvar perfil.', $e);
        }
    }

    public static function modificarUsuario ()
    {
        try
        {
            \Portal\Cache::flushMemcache();

            UtilDAO::executeQueryParam(Querys::UPDATE_USUARIO_PERFIL, $_REQUEST ['nome'], $_REQUEST ['email'], $_REQUEST ['perfil'], $_REQUEST ['id_usuario']);

            Portal\Ajax::RespostaSucesso('Usuário modificado com sucesso.', true, Portal\Ajax::TIPO_SUCCESS);
        } catch (Exception $e)
        {
            Portal\Ajax::RespostaErro('Falha ao salvar perfil.', $e);
        }
    }

    public static function desativarUsuario ()
    {
        try
        {
            \Portal\Cache::flushMemcache();

            UtilDAO::executeQueryParam(Querys::UPDATE_USUARIO_ATIVO, ($_REQUEST['ativo'] == 'S' ? 'N' : 'S'), $_REQUEST ['id_usuario']);

            Portal\Ajax::RespostaSucesso('Usuário modificado com sucesso.', true, Portal\Ajax::TIPO_SUCCESS);
        } catch (Exception $e)
        {
            Portal\Ajax::RespostaErro('Falha ao salvar perfil.', $e);
        }

    }

    public static function login ()
    {
        try
        {
            $retorno = UtilDAO::getResult(Querys::SELECT_LOGIN, $_REQUEST ['login']);
            if (count($retorno) == 1)
            {
                if (  $retorno[0]->ativo == 'N')
                {
                    Portal\Ajax::RespostaErro('Usuário bloqueado.');
                }
            }
            else
            {
                try
                {
                    $user = (new Tuleap\Tuleap($_REQUEST ['login'], $_REQUEST ['senha']))->getUserInfo();
                    if (count($user) == 1)
                    {
                        UtilDAO::executeQueryParam(Querys::INSERT_USUARIO_TULEAP,
                            $user->id,
                            $user->real_name,
                            $user->email,
                            PERFIL_1_ESCRITA,
                            $user->username
                        );
                        \Portal\Cache::flushMemcache();
                    }
                } catch (Exception $e)
                {
                    Portal\Ajax::RespostaErro('Usuário e/ou senha incorretos.');
                }
            }
            

            $_SESSION ['NOME_USUARIO'] = $retorno [0]->nome;
            $_SESSION ['ID_USUARIO'] = $retorno [0]->usuario_id;
            $_SESSION ['EMAIL'] = $retorno [0]->email;
            $_SESSION ['PERFIL'] = $retorno [0]->perfil;
            $_SESSION ['TULEAP_USER'] = $_REQUEST ['login'];
            $_SESSION ['TULEAP_PASS'] = $_REQUEST ['senha'];

            Portal\Ajax::RespostaSucesso('', true, Portal\Ajax::TIPO_SUCCESS);
        } catch (Exception $e)
        {
            Portal\Ajax::RespostaErro('Falha ao logar.', $e);
        }
}

public
static function salvarPerfil ()
{
    try
    {
        \Portal\Cache::flushMemcache();

        UtilDAO::executeQueryParam(Querys::UPDATE_USUARIO, $_REQUEST ['nome'], $_REQUEST ['email'], $_REQUEST ['tuleap_user'], $_REQUEST ['tuleap_senha'], $_REQUEST ['id_usuario']);

        $_SESSION ['NOME_USUARIO'] = $_REQUEST ['nome'];
        $_SESSION ['EMAIL'] = $_REQUEST ['email'];
        $_SESSION ['TULEAP_USER'] = $_REQUEST ['tuleap_user'];
        $_SESSION ['TULEAP_PASS'] = $_REQUEST ['tuleap_senha'];

        Portal\Ajax::RespostaSucesso('Usuário modificado com sucesso.', true, Portal\Ajax::TIPO_SUCCESS);
    } catch (Exception $e)
    {
        Portal\Ajax::RespostaErro('Falha ao salvar perfil.', $e);
    }
}
}