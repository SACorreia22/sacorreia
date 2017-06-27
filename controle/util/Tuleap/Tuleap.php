<?php

namespace Tuleap;
/**
 * Created by PhpStorm.
 * User: saulocorreia
 * Date: 1/11/2017
 * Time: 4:58 PM
 */
class Tuleap extends TuleapStatic
{
    const HOST = 'http://gestaoaplicacoes.mec.gov.br';
    const HOST_LOGIN = self::HOST . '/soap/?wsdl';
    const HOST_PROJECT = self::HOST . '/soap/project/?wsdl';
    const HOST_TRACKER = self::HOST . '/plugins/tracker/soap/?wsdl';

    private $client_login;
    private $client_project;
    private $client_tracker;

    private $user_info;

    private $usuario;
    private $senha;

    private $dados;

    private $users = [];

    function __construct ($usuario, $senha)
    {
        $this->usuario = $usuario;
        $this->senha = $senha;
    }


    public function getSessionHash ()
    {
        if (empty($this->user_info))
        {
            $this->init();
        }

        return $this->user_info->session_hash;
    }

    public function getUserInfo ()
    {
        if (empty($this->user_info))
        {
            $this->init();
        }

        return $this->client_login->getUserInfo($this->user_info->session_hash, $this->user_info->user_id);
    }

    /**
     * Busca no WS os dados de Group
     */
    public function buscaDadosProjeto ()
    {
        if (isset($this->dados))
        {
            return;
        }

        $this->init();

        set_time_limit(0);

        error_log('Buscando Projetos');
        $this->dados = $this->client_login->getMyProjects($this->getSessionHash());
        error_log('Buscando Projetos finalizados');
    }

    /**
     * Busca no WS os dados de Tracker
     */
    public function buscaDadosTracker ()
    {
        $this->buscaDadosProjeto();

        error_log('Buscando Trackers');
        foreach ($this->dados as $key => $value)
        {
            if (isset($this->dados[$key]->tracker))
            {
                continue;
            }

            $this->dados[$key]->tracker = $this->client_tracker->getTrackerList($this->getSessionHash(), $value->group_id);
        }
        error_log('Buscando Trackers finalizados');
    }

    /**
     * Busca no WS os dados de Artifacts e trata os valores
     * @return mixed
     */
    public function buscaDadosArtifacts ()
    {
        $this->buscaDadosTracker();

        error_log('Buscando Artifacts');

        foreach ($this->dados as $key => $value)
        {
            foreach ($this->dados[$key]->tracker as $key2 => $value2)
            {
                $this->dados[$key]->tracker[$key2]->artifacts = self::trataValoresArtifacts($this->client_tracker->getArtifacts($this->getSessionHash(), $value->group_id, $value2->tracker_id)->artifacts);
            }
        }
        error_log('Buscando Artifacts finalizado');

        return $this->dados;
    }

    /**
     * Busca no WS os dados de Usuario e trata os valores
     * @return mixed
     */
    public function buscaDadosUsuario ()
    {
        $this->buscaDadosProjeto();

        error_log('Buscando Usuario');
        $this->users = [];

        foreach ($this->dados as $key => $value)
        {
            foreach ($this->client_login->getProjectGroupsAndUsers($this->getSessionHash(), $value->group_id) as $projectGroupsAndUser)
            {
                if (!empty($projectGroupsAndUser->members))
                {
                    foreach ($projectGroupsAndUser->members as $member)
                    {
                        $this->users[$member->user_id] = $this->client_login->getUserInfo($this->getSessionHash(), $member->user_id);
                    }
                }
            }
        }

        error_log('Buscando Usuario finalizado');

        return $this->dados;
    }

    /**
     * Busca e insere os dados de projeto
     * @return string com arvore dos dados
     */
    public function inserirDadosProjeto ()
    {
        $this->buscaDadosProjeto();

        self::inserirProjeto($this->dados);

        return \Util::printInTree($this->dados);
    }

    /**
     * Busca e Insere os dados de Tracker
     * @return string
     */
    public function inserirDadosTracker ()
    {
        $this->buscaDadosTracker();

        self::inserirTracker($this->dados);

        return \Util::printInTree($this->dados);
    }

    /**
     * Busca e Insere os dados de Artefacts, Cross Reference e Values
     * @return string
     */
    public function inserirDadosArtifacts ()
    {
        $this->inserirDadosProjeto();

        $this->inserirDadosTracker();

        $this->buscaDadosArtifacts();

        foreach ($this->dados as $key => $value)
        {
            foreach ($this->dados[$key]->tracker as $key2 => $value2)
            {
                // ARTIFACTS
                self::inserirArtefato($value2->artifacts, $value2);

                // CROSS REFERENCES
                self::inserirCrossReferences($value2->artifacts);

                // VALUES
                self::inserirValues($value2->artifacts);
            }
        }

        \Portal\Cache::flushMemcache();

        return \Util::printInTree($this->dados);
    }

    public function inserirDadosUsuario ()
    {
        $this->buscaDadosUsuario();

        self::inserirUsuario($this->users);

        \Portal\Cache::flushMemcache();

        return \Util::printInTree($this->users);
    }

    /**
     * Inicializa os WSs e guarda o session_hash para demias conexões
     */
    private function init ()
    {
        if (!empty($this->user_info))
        {
            return;
        }

        error_log('Inicializando leitura dos WS');

        $SOAP_OPTION = [
            'cache_wsdl'     => WSDL_CACHE_NONE,
            'exceptions'     => 1,
            'trace'          => true,
            'encoding'       => 'UTF-8',
            'stream_context' => stream_context_create([
                'ssl' => [// set some SSL/TLS specific options
                    'verify_peer'       => false,
                    'verify_peer_name'  => false,
                    'allow_self_signed' => true
                ]
            ])
        ];

        $this->client_login = new \SoapClient(self::HOST_LOGIN, $SOAP_OPTION);

        $this->user_info = $this->client_login->login($this->usuario, $this->senha);

        $this->client_project = new \SoapClient(self::HOST_PROJECT, $SOAP_OPTION);
        $this->client_tracker = new \SoapClient(self::HOST_TRACKER, $SOAP_OPTION);

        //$this->printAllFunction();

        error_log('WS conectados');
    }

    private function printAllFunction ()
    {
        echo '<pre><b>Project</b><br>';
        foreach ($this->client_project->__getFunctions() as $function)
        {
            echo "$function<br>";
        }
        echo '<br><b>Tracker</b><br>';
        foreach ($this->client_tracker->__getFunctions() as $function)
        {
            echo "$function<br>";
        }
        echo '<br><b>Login</b><br>';
        foreach ($this->client_login->__getFunctions() as $function)
        {
            echo "$function<br>";
        }
        echo '</pre>';

    }
}
