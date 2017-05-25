<?php
$tpl = new Template ("index.html", false);

if (session_status() === PHP_SESSION_NONE)
{
    echo "<div style='background-color:white; font-weight:bold'>Favor configurar session.auto_start 
no php.ini<br>
	; Initialize session on request startup.<br>
	; http://php.net/session.auto-start<br>
	session.auto_start = 1</div>";
}
else
{
    session_destroy();
}

$tpl->NOME_SISTEMA = NOME_SISTEMA;
$tpl->CHAMADA_AJAX = CHAMADA_AJAX;
$tpl->DIRETORIO_RAIZ = ENDERECO_RAIZ;

$tpl->show();
