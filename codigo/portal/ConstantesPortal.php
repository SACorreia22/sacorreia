<?php

// DEBUG
const DEBUG = false;

// Configuracoes do Portal
const TITULO_PAGINA = "Dashboard";
const NOME_SISTEMA = "Dashboard";
const EMPRESA_SISTEMA = "Cast Group Inc.";
const ANO_SISTEMA = "2017";
const FAV_ICON = "";

const DIRETORIO_RAIZ = '/dashboard';
const DIRETORIO_CONTEUDO = DIRETORIO_RAIZ . '/conteudo';
const DIRETORIO_CODIGO = DIRETORIO_RAIZ . '/codigo';

const CHAMADA_AJAX = DIRETORIO_CODIGO . '/controle/JSON';
const PAGINA_PRINCIPAL = DIRETORIO_RAIZ . '/default';

define('PAGINA_DEFAULT', "{$_SERVER ['DOCUMENT_ROOT']}/dashboard/template/pagina.php");
define("DIRETORIO_IMPORT", "{$_SERVER ['DOCUMENT_ROOT']}/dashboard/");
define('ENDERECO_RAIZ', "http://{$_SERVER['HTTP_HOST']}/dashboard/");

// Configuracoes de Banco
//define("BANCO_URL", $_SERVER ['DOCUMENT_ROOT'] . "/dashboard/db/banco.db");
const BANCO_URL = 'host=localhost port=5432 dbname=postgres user=postgres password=qwe123';
const BANCO_USUARIO = 'postgres';
const BANCO_SENHA = 'qwe123';

// Configuracao de Perfil
const PERFIL_0_ADMIN = "0";
const PERFIL_1_ESCRITA = "1";
const PERFIL_2_CONSULTA = "2";

// Constantes dos arquivos de template 
define('TEMPLATE_ROTEIRO', "{$_SERVER ['DOCUMENT_ROOT']}/dashboard/arquivos/roteiro");
define('TEMPLATE_TERMO_ENTREGA', "{$_SERVER ['DOCUMENT_ROOT']}/dashboard/arquivos/termo");
define('TEMPLATE_HISTORIA', "{$_SERVER ['DOCUMENT_ROOT']}/dashboard/arquivos/AnaliseFuncionalidades");