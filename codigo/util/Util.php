<?php

class Util
{
    /**
     * @param       $var
     * @param int   $nivel
     * @param bool  $full
     * @param array $desabilita
     * @return string
     */
    public static function printInTree ($var, $nivel = 0, $full = false, $desabilita = [])
    {
        $retorno = '';

        $espaco = '';
        $espacoMeio = '&#x251C;&#x2500;&#x2500; ';
        $espacoFim = '&#x2514;&#x2500;&#x2500; ';
        for ($i = 0; $i < $nivel - 1; $i++)
        {
            if (in_array($i, $desabilita))
            {
                $espaco .= '    ';
            }

            else
            {
                $espaco .= '&#x2502;   ';
            }
        }

        if (is_array($var))
        {
            $pos = 0;
            $count = count($var);
            foreach ($var as $key => $value)
            {
                $pos++;

                if ($count == $pos)
                {
                    $desabilita[] = $nivel - 1;
                }

                $retorno .= '<br>' . $espaco . ($nivel > 0 ? ($count == $pos ? $espacoFim : $espacoMeio) : '') . $key . ' ';
                $retorno .= self::printInTree($var[$key], $nivel + 1, $full, $desabilita);
            };

        }
        else
        {
            if (is_object($var))
            {
                $pos = 0;
                $count = count(get_object_vars($var));
                foreach (get_object_vars($var) as $key => $value)
                {
                    $pos++;
                    if (self::startsWith($key, 'field_') and $full)
                    {
                        if ($key == 'field_name')
                        {
                            continue;
                        }
                        else
                        {
                            if ($key == 'field_label')
                            {
                                $retorno .= $value;
                                continue;
                            }
                            else
                            {
                                if ($key == 'field_value' AND isset($value->value))
                                {
                                    $retorno .= ' => ' . $value->value;
                                    continue;
                                }
                            }
                        }
                    }

                    if ($count == $pos)
                    {
                        $desabilita[] = $nivel - 1;
                    }

                    $retorno .= "<br>" . $espaco . ($nivel > 0 ? ($count == $pos ? $espacoFim : $espacoMeio) : '') . $key;
                    $retorno .= self::printInTree($var->{$key}, $nivel + 1, $full, $desabilita);
                };
            }
            else
            {
                if (is_integer($var) and strlen($var) == 10)
                {
                    $var = self::trataData($var);
                }

                $retorno .= ' => (' . gettype($var) . ') ' . $var;
            }
        }

        return $retorno;
    }

    public static function DataSQLtoString ($data)
    {
        return isset ($data) ? date("d/m/Y", strtotime($data)) : "";
    }

    public static function SendReadyScript ($script)
    {
        return "<script>$().ready(function(){" . $script . "});</script>";
    }

    public static function SendScript ($script)
    {
        return "<script>{$script}</script>";
    }

    public static function inverterOrdem ($ordem)
    {
        if (strcasecmp($ordem, "DESC") == 0)
        {
            return "ASC";
        }

        return "DESC";
    }

    public static function numberToMoney ($number, $cifrao = false)
    {
        return ($cifrao ? "R$ " : "") . number_format($number, 2, ',', '.');
    }

    public static function numberToMoneyCor ($number, $cifrao = false)
    {
        return ($number < 0 ? "<span color='red'>" : "") . ($cifrao ? "R$ " : "") . number_format($number, 2, ',', '.') . ($number < 0 ? "</span>" : "");
    }

    public static function number ($number)
    {
        return number_format($number, 2, ',', '.');
    }

    public static function round ($number, $decimal)
    {
        return number_format($number, $decimal, ',', '.');
    }

    public static function isDiferente ($cmp1, $cmp2, $retorno = true)
    {
        if ($cmp1 != $cmp2)
        {
            return $retorno;
        }

        return null;
    }

    public static function startsWith ($fullText, $starts)
    {
        return (substr($fullText, 0, strlen($starts)) === $starts);
    }

    public static function endsWith ($fullText, $ends)
    {
        $length = strlen($ends);
        if ($length == 0)
        {
            return true;
        }

        return (substr($fullText, -$length) === $ends);
    }

    public static function PreencherFiltro (&$tpl, $campo, $coluna, $block, $query)
    {
        foreach (UtilDAO::getResult($query) as $row)
        {
            $tpl->{$campo} = $row [$coluna];
            $tpl->block($block);
        }
    }


    /**
     * @param $time
     * @return false|string
     */
    public static function trataData ($time)
    {
        if (empty($time))
        {
            return '';
        }

        date_default_timezone_set('UTC');//'America/Sao_Paulo');

        return date("Y-m-d H:i:s", $time);
    }

    public static function criaPasta ($pasta)
    {
        if (!is_dir($pasta))
        {
            return mkdir($pasta, 0777, true);
        }

        return false;
    }

    public static function zipFile ($source, $destination, $remove = '')
    {
        if (!extension_loaded('zip') || !file_exists($source))
        {
            return false;
        }

        $zip = new ZipArchive();
        if (!$zip->open($destination, ZIPARCHIVE::CREATE))
        {
            return false;
        }

        $source = str_replace('\\', '/', realpath($source));

        if (is_dir($source) === true)
        {
            $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source), RecursiveIteratorIterator::SELF_FIRST);

            foreach ($files as $file)
            {
                $file = str_replace('\\', '/', $file);

                // Ignore "." and ".." folders
                if (in_array(substr($file, strrpos($file, '/') + 1), ['.', '..']) || "{$source}{$remove}" == $file)
                {
                    continue;
                }

                if (is_dir($file) === true)
                {
                    $zip->addEmptyDir(str_replace($source . '/', '', $file . '/'));
                }
                elseif (is_file($file) === true)
                {
                    $zip->addFromString(str_replace($source . '/', '', $file), file_get_contents(realpath($file)));
                }
            }
        }
        elseif (is_file($source) === true)
        {
            $zip->addFromString(basename($source), file_get_contents(realpath($source)));
        }

        return $zip->close();
    }


    public static function removeDir ($dir)
    {
        foreach (glob($dir . '/*') as $file)
        {
            if (is_dir($file))
            {
                self::removeDir($file);
            }
            else
            {
                unlink($file);
            }
        }
        rmdir($dir);
    }

    public static function preparaPopOver (string $glue, string $label, string $value, array $dados)
    {
        $aux = [];

        foreach ($dados as $item)
        {
            //$valor = str_replace("\n",'<br>',
            $valor = trim(
            //html_entity_decode(
                (trim($item->{$value}))
            //    )
            );
            //);

            $aux[] = htmlspecialchars("<div class='row par_impar'><div class='col-sm-4 text-right'>{$item->{$label}}</div><div class='col-sm-8 text-justify'>{$valor}</div></div>");
        }

        return (string) implode($glue, $aux);
    }

    public static function mimetype ($arquivo)
    {
        $mime = [
            '.3dm'       => 'x-world/x-3dmf',
            '.3dmf'      => 'x-world/x-3dmf',
            '.a'         => 'application/octet-stream',
            '.aab'       => 'application/x-authorware-bin',
            '.aam'       => 'application/x-authorware-map',
            '.aas'       => 'application/x-authorware-seg',
            '.abc'       => 'text/vnd.abc',
            '.acgi'      => 'text/html',
            '.afl'       => 'video/animaflex',
            '.ai'        => 'application/postscript',
            '.aif'       => 'audio/aiff',
            '.aif'       => 'audio/x-aiff',
            '.aifc'      => 'audio/aiff',
            '.aifc'      => 'audio/x-aiff',
            '.aiff'      => 'audio/aiff',
            '.aiff'      => 'audio/x-aiff',
            '.aim'       => 'application/x-aim',
            '.aip'       => 'text/x-audiosoft-intra',
            '.ani'       => 'application/x-navi-animation',
            '.aos'       => 'application/x-nokia-9000-communicator-add-on-software',
            '.aps'       => 'application/mime',
            '.arc'       => 'application/octet-stream',
            '.arj'       => 'application/arj',
            '.arj'       => 'application/octet-stream',
            '.art'       => 'image/x-jg',
            '.asf'       => 'video/x-ms-asf',
            '.asm'       => 'text/x-asm',
            '.asp'       => 'text/asp',
            '.asx'       => 'application/x-mplayer2',
            '.asx'       => 'video/x-ms-asf',
            '.asx'       => 'video/x-ms-asf-plugin',
            '.au'        => 'audio/basic',
            '.au'        => 'audio/x-au',
            '.avi'       => 'application/x-troff-msvideo',
            '.avi'       => 'video/avi',
            '.avi'       => 'video/msvideo',
            '.avi'       => 'video/x-msvideo',
            '.avs'       => 'video/avs-video',
            '.bcpio'     => 'application/x-bcpio',
            '.bin'       => 'application/mac-binary',
            '.bin'       => 'application/macbinary',
            '.bin'       => 'application/octet-stream',
            '.bin'       => 'application/x-binary',
            '.bin'       => 'application/x-macbinary',
            '.bm'        => 'image/bmp',
            '.bmp'       => 'image/bmp',
            '.bmp'       => 'image/x-windows-bmp',
            '.boo'       => 'application/book',
            '.book'      => 'application/book',
            '.boz'       => 'application/x-bzip2',
            '.bsh'       => 'application/x-bsh',
            '.bz'        => 'application/x-bzip',
            '.bz2'       => 'application/x-bzip2',
            '.c'         => 'text/plain',
            '.c'         => 'text/x-c',
            '.c++'       => 'text/plain',
            '.cat'       => 'application/vnd.ms-pki.seccat',
            '.cc'        => 'text/plain',
            '.cc'        => 'text/x-c',
            '.ccad'      => 'application/clariscad',
            '.cco'       => 'application/x-cocoa',
            '.cdf'       => 'application/cdf',
            '.cdf'       => 'application/x-cdf',
            '.cdf'       => 'application/x-netcdf',
            '.cer'       => 'application/pkix-cert',
            '.cer'       => 'application/x-x509-ca-cert',
            '.cha'       => 'application/x-chat',
            '.chat'      => 'application/x-chat',
            '.class'     => 'application/java',
            '.class'     => 'application/java-byte-code',
            '.class'     => 'application/x-java-class',
            '.com'       => 'application/octet-stream',
            '.com'       => 'text/plain',
            '.conf'      => 'text/plain',
            '.cpio'      => 'application/x-cpio',
            '.cpp'       => 'text/x-c',
            '.cpt'       => 'application/mac-compactpro',
            '.cpt'       => 'application/x-compactpro',
            '.cpt'       => 'application/x-cpt',
            '.crl'       => 'application/pkcs-crl',
            '.crl'       => 'application/pkix-crl',
            '.crt'       => 'application/pkix-cert',
            '.crt'       => 'application/x-x509-ca-cert',
            '.crt'       => 'application/x-x509-user-cert',
            '.csh'       => 'application/x-csh',
            '.csh'       => 'text/x-script.csh',
            '.css'       => 'application/x-pointplus',
            '.css'       => 'text/css',
            '.cxx'       => 'text/plain',
            '.dcr'       => 'application/x-director',
            '.deepv'     => 'application/x-deepv',
            '.def'       => 'text/plain',
            '.der'       => 'application/x-x509-ca-cert',
            '.dif'       => 'video/x-dv',
            '.dir'       => 'application/x-director',
            '.dl'        => 'video/dl',
            '.dl'        => 'video/x-dl',
            '.doc'       => 'application/msword',
            '.dot'       => 'application/msword',
            '.dp'        => 'application/commonground',
            '.drw'       => 'application/drafting',
            '.dump'      => 'application/octet-stream',
            '.dv'        => 'video/x-dv',
            '.dvi'       => 'application/x-dvi',
            '.dwf'       => 'drawing/x-dwf (old)',
            '.dwf'       => 'model/vnd.dwf',
            '.dwg'       => 'application/acad',
            '.dwg'       => 'image/vnd.dwg',
            '.dwg'       => 'image/x-dwg',
            '.dxf'       => 'application/dxf',
            '.dxf'       => 'image/vnd.dwg',
            '.dxf'       => 'image/x-dwg',
            '.dxr'       => 'application/x-director',
            '.el'        => 'text/x-script.elisp',
            '.elc'       => 'application/x-bytecode.elisp (compiled elisp)',
            '.elc'       => 'application/x-elc',
            '.env'       => 'application/x-envoy',
            '.eps'       => 'application/postscript',
            '.es'        => 'application/x-esrehber',
            '.etx'       => 'text/x-setext',
            '.evy'       => 'application/envoy',
            '.evy'       => 'application/x-envoy',
            '.exe'       => 'application/octet-stream',
            '.f'         => 'text/plain',
            '.f'         => 'text/x-fortran',
            '.f77'       => 'text/x-fortran',
            '.f90'       => 'text/plain',
            '.f90'       => 'text/x-fortran',
            '.fdf'       => 'application/vnd.fdf',
            '.fif'       => 'application/fractals',
            '.fif'       => 'image/fif',
            '.fli'       => 'video/fli',
            '.fli'       => 'video/x-fli',
            '.flo'       => 'image/florian',
            '.flx'       => 'text/vnd.fmi.flexstor',
            '.fmf'       => 'video/x-atomic3d-feature',
            '.for'       => 'text/plain',
            '.for'       => 'text/x-fortran',
            '.fpx'       => 'image/vnd.fpx',
            '.fpx'       => 'image/vnd.net-fpx',
            '.frl'       => 'application/freeloader',
            '.funk'      => 'audio/make',
            '.g'         => 'text/plain',
            '.g3'        => 'image/g3fax',
            '.gif'       => 'image/gif',
            '.gl'        => 'video/gl',
            '.gl'        => 'video/x-gl',
            '.gsd'       => 'audio/x-gsm',
            '.gsm'       => 'audio/x-gsm',
            '.gsp'       => 'application/x-gsp',
            '.gss'       => 'application/x-gss',
            '.gtar'      => 'application/x-gtar',
            '.gz'        => 'application/x-compressed',
            '.gz'        => 'application/x-gzip',
            '.gzip'      => 'application/x-gzip',
            '.gzip'      => 'multipart/x-gzip',
            '.h'         => 'text/plain',
            '.h'         => 'text/x-h',
            '.hdf'       => 'application/x-hdf',
            '.help'      => 'application/x-helpfile',
            '.hgl'       => 'application/vnd.hp-hpgl',
            '.hh'        => 'text/plain',
            '.hh'        => 'text/x-h',
            '.hlb'       => 'text/x-script',
            '.hlp'       => 'application/hlp',
            '.hlp'       => 'application/x-helpfile',
            '.hlp'       => 'application/x-winhelp',
            '.hpg'       => 'application/vnd.hp-hpgl',
            '.hpgl'      => 'application/vnd.hp-hpgl',
            '.hqx'       => 'application/binhex',
            '.hqx'       => 'application/binhex4',
            '.hqx'       => 'application/mac-binhex',
            '.hqx'       => 'application/mac-binhex40',
            '.hqx'       => 'application/x-binhex40',
            '.hqx'       => 'application/x-mac-binhex40',
            '.hta'       => 'application/hta',
            '.htc'       => 'text/x-component',
            '.htm'       => 'text/html',
            '.html'      => 'text/html',
            '.htmls'     => 'text/html',
            '.htt'       => 'text/webviewhtml',
            '.htx'       => 'text/html',
            '.ice'       => 'x-conference/x-cooltalk',
            '.ico'       => 'image/x-icon',
            '.idc'       => 'text/plain',
            '.ief'       => 'image/ief',
            '.iefs'      => 'image/ief',
            '.iges'      => 'application/iges',
            '.iges'      => 'model/iges',
            '.igs'       => 'application/iges',
            '.igs'       => 'model/iges',
            '.ima'       => 'application/x-ima',
            '.imap'      => 'application/x-httpd-imap',
            '.inf'       => 'application/inf',
            '.ins'       => 'application/x-internett-signup',
            '.ip'        => 'application/x-ip2',
            '.isu'       => 'video/x-isvideo',
            '.it'        => 'audio/it',
            '.iv'        => 'application/x-inventor',
            '.ivr'       => 'i-world/i-vrml',
            '.ivy'       => 'application/x-livescreen',
            '.jam'       => 'audio/x-jam',
            '.jav'       => 'text/plain',
            '.jav'       => 'text/x-java-source',
            '.java'      => 'text/plain',
            '.java'      => 'text/x-java-source',
            '.jcm'       => 'application/x-java-commerce',
            '.jfif'      => 'image/jpeg',
            '.jfif'      => 'image/pjpeg',
            '.jfif-tbnl' => 'image/jpeg',
            '.jpe'       => 'image/jpeg',
            '.jpe'       => 'image/pjpeg',
            '.jpeg'      => 'image/jpeg',
            '.jpeg'      => 'image/pjpeg',
            '.jpg'       => 'image/jpeg',
            '.jpg'       => 'image/pjpeg',
            '.jps'       => 'image/x-jps',
            '.js'        => 'application/x-javascript',
            '.js'        => 'application/javascript',
            '.js'        => 'application/ecmascript',
            '.js'        => 'text/javascript',
            '.js'        => 'text/ecmascript',
            '.jut'       => 'image/jutvision',
            '.kar'       => 'audio/midi',
            '.kar'       => 'music/x-karaoke',
            '.ksh'       => 'application/x-ksh',
            '.ksh'       => 'text/x-script.ksh',
            '.la'        => 'audio/nspaudio',
            '.la'        => 'audio/x-nspaudio',
            '.lam'       => 'audio/x-liveaudio',
            '.latex'     => 'application/x-latex',
            '.lha'       => 'application/lha',
            '.lha'       => 'application/octet-stream',
            '.lha'       => 'application/x-lha',
            '.lhx'       => 'application/octet-stream',
            '.list'      => 'text/plain',
            '.lma'       => 'audio/nspaudio',
            '.lma'       => 'audio/x-nspaudio',
            '.log'       => 'text/plain',
            '.lsp'       => 'application/x-lisp',
            '.lsp'       => 'text/x-script.lisp',
            '.lst'       => 'text/plain',
            '.lsx'       => 'text/x-la-asf',
            '.ltx'       => 'application/x-latex',
            '.lzh'       => 'application/octet-stream',
            '.lzh'       => 'application/x-lzh',
            '.lzx'       => 'application/lzx',
            '.lzx'       => 'application/octet-stream',
            '.lzx'       => 'application/x-lzx',
            '.m'         => 'text/plain',
            '.m'         => 'text/x-m',
            '.m1v'       => 'video/mpeg',
            '.m2a'       => 'audio/mpeg',
            '.m2v'       => 'video/mpeg',
            '.m3u'       => 'audio/x-mpequrl',
            '.man'       => 'application/x-troff-man',
            '.map'       => 'application/x-navimap',
            '.mar'       => 'text/plain',
            '.mbd'       => 'application/mbedlet',
            '.mc$'       => 'application/x-magic-cap-package-1.0',
            '.mcd'       => 'application/mcad',
            '.mcd'       => 'application/x-mathcad',
            '.mcf'       => 'image/vasa',
            '.mcf'       => 'text/mcf',
            '.mcp'       => 'application/netmc',
            '.me'        => 'application/x-troff-me',
            '.mht'       => 'message/rfc822',
            '.mhtml'     => 'message/rfc822',
            '.mid'       => 'application/x-midi',
            '.mid'       => 'audio/midi',
            '.mid'       => 'audio/x-mid',
            '.mid'       => 'audio/x-midi',
            '.mid'       => 'music/crescendo',
            '.mid'       => 'x-music/x-midi',
            '.midi'      => 'application/x-midi',
            '.midi'      => 'audio/midi',
            '.midi'      => 'audio/x-mid',
            '.midi'      => 'audio/x-midi',
            '.midi'      => 'music/crescendo',
            '.midi'      => 'x-music/x-midi',
            '.mif'       => 'application/x-frame',
            '.mif'       => 'application/x-mif',
            '.mime'      => 'message/rfc822',
            '.mime'      => 'www/mime',
            '.mjf'       => 'audio/x-vnd.audioexplosion.mjuicemediafile',
            '.mjpg'      => 'video/x-motion-jpeg',
            '.mm'        => 'application/base64',
            '.mm'        => 'application/x-meme',
            '.mme'       => 'application/base64',
            '.mod'       => 'audio/mod',
            '.mod'       => 'audio/x-mod',
            '.moov'      => 'video/quicktime',
            '.mov'       => 'video/quicktime',
            '.movie'     => 'video/x-sgi-movie',
            '.mp2'       => 'audio/mpeg',
            '.mp2'       => 'audio/x-mpeg',
            '.mp2'       => 'video/mpeg',
            '.mp2'       => 'video/x-mpeg',
            '.mp2'       => 'video/x-mpeq2a',
            '.mp3'       => 'audio/mpeg3',
            '.mp3'       => 'audio/x-mpeg-3',
            '.mp3'       => 'video/mpeg',
            '.mp3'       => 'video/x-mpeg',
            '.mpa'       => 'audio/mpeg',
            '.mpa'       => 'video/mpeg',
            '.mpc'       => 'application/x-project',
            '.mpe'       => 'video/mpeg',
            '.mpeg'      => 'video/mpeg',
            '.mpg'       => 'audio/mpeg',
            '.mpg'       => 'video/mpeg',
            '.mpga'      => 'audio/mpeg',
            '.mpp'       => 'application/vnd.ms-project',
            '.mpt'       => 'application/x-project',
            '.mpv'       => 'application/x-project',
            '.mpx'       => 'application/x-project',
            '.mrc'       => 'application/marc',
            '.ms'        => 'application/x-troff-ms',
            '.mv'        => 'video/x-sgi-movie',
            '.my'        => 'audio/make',
            '.mzz'       => 'application/x-vnd.audioexplosion.mzz',
            '.nap'       => 'image/naplps',
            '.naplps'    => 'image/naplps',
            '.nc'        => 'application/x-netcdf',
            '.ncm'       => 'application/vnd.nokia.configuration-message',
            '.nif'       => 'image/x-niff',
            '.niff'      => 'image/x-niff',
            '.nix'       => 'application/x-mix-transfer',
            '.nsc'       => 'application/x-conference',
            '.nvd'       => 'application/x-navidoc',
            '.o'         => 'application/octet-stream',
            '.oda'       => 'application/oda',
            '.omc'       => 'application/x-omc',
            '.omcd'      => 'application/x-omcdatamaker',
            '.omcr'      => 'application/x-omcregerator',
            '.p'         => 'text/x-pascal',
            '.p10'       => 'application/pkcs10',
            '.p10'       => 'application/x-pkcs10',
            '.p12'       => 'application/pkcs-12',
            '.p12'       => 'application/x-pkcs12',
            '.p7a'       => 'application/x-pkcs7-signature',
            '.p7c'       => 'application/pkcs7-mime',
            '.p7c'       => 'application/x-pkcs7-mime',
            '.p7m'       => 'application/pkcs7-mime',
            '.p7m'       => 'application/x-pkcs7-mime',
            '.p7r'       => 'application/x-pkcs7-certreqresp',
            '.p7s'       => 'application/pkcs7-signature',
            '.part'      => 'application/pro_eng',
            '.pas'       => 'text/pascal',
            '.pbm'       => 'image/x-portable-bitmap',
            '.pcl'       => 'application/vnd.hp-pcl',
            '.pcl'       => 'application/x-pcl',
            '.pct'       => 'image/x-pict',
            '.pcx'       => 'image/x-pcx',
            '.pdb'       => 'chemical/x-pdb',
            '.pdf'       => 'application/pdf',
            '.pfunk'     => 'audio/make',
            '.pfunk'     => 'audio/make.my.funk',
            '.pgm'       => 'image/x-portable-graymap',
            '.pgm'       => 'image/x-portable-greymap',
            '.pic'       => 'image/pict',
            '.pict'      => 'image/pict',
            '.pkg'       => 'application/x-newton-compatible-pkg',
            '.pko'       => 'application/vnd.ms-pki.pko',
            '.pl'        => 'text/plain',
            '.pl'        => 'text/x-script.perl',
            '.plx'       => 'application/x-pixclscript',
            '.pm'        => 'image/x-xpixmap',
            '.pm'        => 'text/x-script.perl-module',
            '.pm4'       => 'application/x-pagemaker',
            '.pm5'       => 'application/x-pagemaker',
            '.png'       => 'image/png',
            '.pnm'       => 'application/x-portable-anymap',
            '.pnm'       => 'image/x-portable-anymap',
            '.pot'       => 'application/mspowerpoint',
            '.pot'       => 'application/vnd.ms-powerpoint',
            '.pov'       => 'model/x-pov',
            '.ppa'       => 'application/vnd.ms-powerpoint',
            '.ppm'       => 'image/x-portable-pixmap',
            '.pps'       => 'application/mspowerpoint',
            '.pps'       => 'application/vnd.ms-powerpoint',
            '.ppt'       => 'application/mspowerpoint',
            '.ppt'       => 'application/powerpoint',
            '.ppt'       => 'application/vnd.ms-powerpoint',
            '.ppt'       => 'application/x-mspowerpoint',
            '.ppz'       => 'application/mspowerpoint',
            '.pre'       => 'application/x-freelance',
            '.prt'       => 'application/pro_eng',
            '.ps'        => 'application/postscript',
            '.psd'       => 'application/octet-stream',
            '.pvu'       => 'paleovu/x-pv',
            '.pwz'       => 'application/vnd.ms-powerpoint',
            '.py'        => 'text/x-script.phyton',
            '.pyc'       => 'application/x-bytecode.python',
            '.qcp'       => 'audio/vnd.qcelp',
            '.qd3'       => 'x-world/x-3dmf',
            '.qd3d'      => 'x-world/x-3dmf',
            '.qif'       => 'image/x-quicktime',
            '.qt'        => 'video/quicktime',
            '.qtc'       => 'video/x-qtc',
            '.qti'       => 'image/x-quicktime',
            '.qtif'      => 'image/x-quicktime',
            '.ra'        => 'audio/x-pn-realaudio',
            '.ram'       => 'audio/x-pn-realaudio',
            '.ras'       => 'application/x-cmu-raster',
            '.rast'      => 'image/cmu-raster',
            '.rexx'      => 'text/x-script.rexx',
            '.rf'        => 'image/vnd.rn-realflash',
            '.rgb'       => 'image/x-rgb',
            '.rm'        => 'application/vnd.rn-realmedia',
            '.rmi'       => 'audio/mid',
            '.rmm'       => 'audio/x-pn-realaudio',
            '.rmp'       => 'audio/x-pn-realaudio',
            '.rng'       => 'application/ringing-tones',
            '.rnx'       => 'application/vnd.rn-realplayer',
            '.roff'      => 'application/x-troff',
            '.rp'        => 'image/vnd.rn-realpix',
            '.rpm'       => 'audio/x-pn-realaudio-plugin',
            '.rt'        => 'text/richtext',
            '.rtf'       => 'application/rtf',
            '.rtx'       => 'application/rtf',
            '.rv'        => 'video/vnd.rn-realvideo',
            '.s'         => 'text/x-asm',
            '.s3m'       => 'audio/s3m',
            '.saveme'    => 'application/octet-stream',
            '.sbk'       => 'application/x-tbook',
            '.scm'       => 'application/x-lotusscreencam',
            '.sdml'      => 'text/plain',
            '.sdp'       => 'application/sdp',
            '.sdr'       => 'application/sounder',
            '.sea'       => 'application/sea',
            '.set'       => 'application/set',
            '.sgm'       => 'text/sgml',
            '.sgml'      => 'text/sgml',
            '.sh'        => 'application/x-bsh',
            '.shar'      => 'application/x-bsh',
            '.shtml'     => 'text/html',
            '.sid'       => 'audio/x-psid',
            '.sit'       => 'application/x-sit',
            '.skd'       => 'application/x-koan',
            '.skm'       => 'application/x-koan',
            '.skp'       => 'application/x-koan',
            '.skt'       => 'application/x-koan',
            '.sl'        => 'application/x-seelogo',
            '.smi'       => 'application/smil',
            '.smil'      => 'application/smil',
            '.snd'       => 'audio/basic',
            '.sol'       => 'application/solids',
            '.spc'       => 'application/x-pkcs7-certificates',
            '.spl'       => 'application/futuresplash',
            '.spr'       => 'application/x-sprite',
            '.sprite'    => 'application/x-sprite',
            '.src'       => 'application/x-wais-source',
            '.ssi'       => 'text/x-server-parsed-html',
            '.ssm'       => 'application/streamingmedia',
            '.sst'       => 'application/vnd.ms-pki.certstore',
            '.step'      => 'application/step',
            '.stl'       => 'application/sla',
            '.stp'       => 'application/step',
            '.sv4cpio'   => 'application/x-sv4cpio',
            '.sv4crc'    => 'application/x-sv4crc',
            '.svf'       => 'image/vnd.dwg',
            '.svr'       => 'application/x-world',
            '.swf'       => 'application/x-shockwave-flash',
            '.t'         => 'application/x-troff',
            '.talk'      => 'text/x-speech',
            '.tar'       => 'application/x-tar',
            '.tbk'       => 'application/toolbook',
            '.tcl'       => 'application/x-tcl',
            '.tcsh'      => 'text/x-script.tcsh',
            '.tex'       => 'application/x-tex',
            '.texi'      => 'application/x-texinfo',
            '.texinfo'   => 'application/x-texinfo',
            '.text'      => 'text/plain',
            '.tgz'       => 'application/gnutar',
            '.tif'       => 'image/tiff',
            '.tiff'      => 'image/tiff',
            '.tr'        => 'application/x-troff',
            '.tsi'       => 'audio/tsp-audio',
            '.tsp'       => 'application/dsptype',
            '.tsv'       => 'text/tab-separated-values',
            '.turbot'    => 'image/florian',
            '.txt'       => 'text/plain',
            '.uil'       => 'text/x-uil',
            '.uni'       => 'text/uri-list',
            '.unis'      => 'text/uri-list',
            '.unv'       => 'application/i-deas',
            '.uri'       => 'text/uri-list',
            '.uris'      => 'text/uri-list',
            '.ustar'     => 'application/x-ustar',
            '.uu'        => 'application/octet-stream',
            '.uue'       => 'text/x-uuencode',
            '.vcd'       => 'application/x-cdlink',
            '.vcs'       => 'text/x-vcalendar',
            '.vda'       => 'application/vda',
            '.vdo'       => 'video/vdo',
            '.vew'       => 'application/groupwise',
            '.viv'       => 'video/vivo',
            '.vivo'      => 'video/vivo',
            '.vmd'       => 'application/vocaltec-media-desc',
            '.vmf'       => 'application/vocaltec-media-file',
            '.voc'       => 'audio/voc',
            '.vos'       => 'video/vosaic',
            '.vox'       => 'audio/voxware',
            '.vqe'       => 'audio/x-twinvq-plugin',
            '.vqf'       => 'audio/x-twinvq',
            '.vql'       => 'audio/x-twinvq-plugin',
            '.vrml'      => 'application/x-vrml',
            '.vrt'       => 'x-world/x-vrt',
            '.vsd'       => 'application/x-visio',
            '.vst'       => 'application/x-visio',
            '.vsw'       => 'application/x-visio',
            '.w60'       => 'application/wordperfect6.0',
            '.w61'       => 'application/wordperfect6.1',
            '.w6w'       => 'application/msword',
            '.wav'       => 'audio/wav',
            '.wb1'       => 'application/x-qpro',
            '.wbmp'      => 'image/vnd.wap.wbmp',
            '.web'       => 'application/vnd.xara',
            '.wiz'       => 'application/msword',
            '.wk1'       => 'application/x-123',
            '.wmf'       => 'windows/metafile',
            '.wml'       => 'text/vnd.wap.wml',
            '.wmlc'      => 'application/vnd.wap.wmlc',
            '.wmls'      => 'text/vnd.wap.wmlscript',
            '.wmlsc'     => 'application/vnd.wap.wmlscriptc',
            '.word'      => 'application/msword',
            '.wp'        => 'application/wordperfect',
            '.wp5'       => 'application/wordperfect',
            '.wp6'       => 'application/wordperfect',
            '.wpd'       => 'application/wordperfect',
            '.wq1'       => 'application/x-lotus',
            '.wri'       => 'application/mswrite',
            '.wrl'       => 'application/x-world',
            '.wrz'       => 'model/vrml',
            '.wsc'       => 'text/scriplet',
            '.wsrc'      => 'application/x-wais-source',
            '.wtk'       => 'application/x-wintalk',
            '.xbm'       => 'image/x-xbitmap',
            '.xdr'       => 'video/x-amt-demorun',
            '.xgz'       => 'xgl/drawing',
            '.xif'       => 'image/vnd.xiff',
            '.xl'        => 'application/excel',
            '.xla'       => 'application/excel',
            '.xlb'       => 'application/excel',
            '.xlc'       => 'application/excel',
            '.xld'       => 'application/excel',
            '.xlk'       => 'application/excel',
            '.xll'       => 'application/excel',
            '.xlm'       => 'application/excel',
            '.xls'       => 'application/excel',
            '.xlt'       => 'application/excel',
            '.xlv'       => 'application/excel',
            '.xlw'       => 'application/excel',
            '.xm'        => 'audio/xm',
            '.xml'       => 'application/xml',
            '.xmz'       => 'xgl/movie',
            '.xpix'      => 'application/x-vnd.ls-xpix',
            '.xpm'       => 'image/x-xpixmap',
            '.x-png'     => 'image/png',
            '.xsr'       => 'video/x-amt-showrun',
            '.xwd'       => 'image/x-xwd',
            '.xyz'       => 'chemical/x-pdb',
            '.z'         => 'application/x-compress',
            '.zip'       => 'application/zip',
            '.zoo'       => 'application/octet-stream',
            '.zsh'       => 'text/x-script.zsh'
        ];
        
        return $mime[pathinfo($arquivo, PATHINFO_EXTENSION)];
    }
}