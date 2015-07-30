<?php

namespace Phat\Http;

use Phat\Core\Configure;
use Phat\Http\Exception\FileNotFoundException;
use Phat\Http\Exception\UnknownStatusException;

/**
 * Response is the object that is returned to the client. It handles common page retuning as well as file downloading
 * or redirections.
 */
class Response
{
    // TODO : output compresion

    /**
     * @var array HTTP Status codes and strings
     */
    protected $statusCodes = [
        100 => 'Continue',
        101 => 'Switching Protocols',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        307 => 'Temporary Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Time-out',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Large',
        415 => 'Unsupported Media Type',
        416 => 'Requested range not satisfiable',
        417 => 'Expectation Failed',
        429 => 'Too Many Requests',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Time-out',
        505 => 'Unsupported Version',
    ];

    /**
     * @var array Shortcut for mimetypes
     */
    protected $mimeTypes = [
        'html' => 'text/html',
        'json' => 'application/json',
        'xml' => 'application/xml',
        'rss' => 'application/rss+xml',
        'ai' => 'application/postscript',
        'bcpio' => 'application/x-bcpio',
        'bin' => 'application/octet-stream',
        'ccad' => 'application/clariscad',
        'cdf' => 'application/x-netcdf',
        'class' => 'application/octet-stream',
        'cpio' => 'application/x-cpio',
        'cpt' => 'application/mac-compactpro',
        'csh' => 'application/x-csh',
        'csv' => 'text/csv',
        'dcr' => 'application/x-director',
        'dir' => 'application/x-director',
        'dms' => 'application/octet-stream',
        'doc' => 'application/msword',
        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'drw' => 'application/drafting',
        'dvi' => 'application/x-dvi',
        'dwg' => 'application/acad',
        'dxf' => 'application/dxf',
        'dxr' => 'application/x-director',
        'eot' => 'application/vnd.ms-fontobject',
        'eps' => 'application/postscript',
        'exe' => 'application/octet-stream',
        'ez' => 'application/andrew-inset',
        'flv' => 'video/x-flv',
        'gtar' => 'application/x-gtar',
        'gz' => 'application/x-gzip',
        'bz2' => 'application/x-bzip',
        '7z' => 'application/x-7z-compressed',
        'hdf' => 'application/x-hdf',
        'hqx' => 'application/mac-binhex40',
        'ico' => 'image/x-icon',
        'ips' => 'application/x-ipscript',
        'ipx' => 'application/x-ipix',
        'js' => 'application/javascript',
        'latex' => 'application/x-latex',
        'lha' => 'application/octet-stream',
        'lsp' => 'application/x-lisp',
        'lzh' => 'application/octet-stream',
        'man' => 'application/x-troff-man',
        'me' => 'application/x-troff-me',
        'mif' => 'application/vnd.mif',
        'ms' => 'application/x-troff-ms',
        'nc' => 'application/x-netcdf',
        'oda' => 'application/oda',
        'otf' => 'font/otf',
        'pdf' => 'application/pdf',
        'pgn' => 'application/x-chess-pgn',
        'pot' => 'application/vnd.ms-powerpoint',
        'pps' => 'application/vnd.ms-powerpoint',
        'ppt' => 'application/vnd.ms-powerpoint',
        'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        'ppz' => 'application/vnd.ms-powerpoint',
        'pre' => 'application/x-freelance',
        'prt' => 'application/pro_eng',
        'ps' => 'application/postscript',
        'roff' => 'application/x-troff',
        'scm' => 'application/x-lotusscreencam',
        'set' => 'application/set',
        'sh' => 'application/x-sh',
        'shar' => 'application/x-shar',
        'sit' => 'application/x-stuffit',
        'skd' => 'application/x-koan',
        'skm' => 'application/x-koan',
        'skp' => 'application/x-koan',
        'skt' => 'application/x-koan',
        'smi' => 'application/smil',
        'smil' => 'application/smil',
        'sol' => 'application/solids',
        'spl' => 'application/x-futuresplash',
        'src' => 'application/x-wais-source',
        'step' => 'application/STEP',
        'stl' => 'application/SLA',
        'stp' => 'application/STEP',
        'sv4cpio' => 'application/x-sv4cpio',
        'sv4crc' => 'application/x-sv4crc',
        'svg' => 'image/svg+xml',
        'svgz' => 'image/svg+xml',
        'swf' => 'application/x-shockwave-flash',
        't' => 'application/x-troff',
        'tar' => 'application/x-tar',
        'tcl' => 'application/x-tcl',
        'tex' => 'application/x-tex',
        'texi' => 'application/x-texinfo',
        'texinfo' => 'application/x-texinfo',
        'tr' => 'application/x-troff',
        'tsp' => 'application/dsptype',
        'ttc' => 'font/ttf',
        'ttf' => 'font/ttf',
        'unv' => 'application/i-deas',
        'ustar' => 'application/x-ustar',
        'vcd' => 'application/x-cdlink',
        'vda' => 'application/vda',
        'xlc' => 'application/vnd.ms-excel',
        'xll' => 'application/vnd.ms-excel',
        'xlm' => 'application/vnd.ms-excel',
        'xls' => 'application/vnd.ms-excel',
        'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'xlw' => 'application/vnd.ms-excel',
        'zip' => 'application/zip',
        'aif' => 'audio/x-aiff',
        'aifc' => 'audio/x-aiff',
        'aiff' => 'audio/x-aiff',
        'au' => 'audio/basic',
        'kar' => 'audio/midi',
        'mid' => 'audio/midi',
        'midi' => 'audio/midi',
        'mp2' => 'audio/mpeg',
        'mp3' => 'audio/mpeg',
        'mpga' => 'audio/mpeg',
        'ogg' => 'audio/ogg',
        'oga' => 'audio/ogg',
        'spx' => 'audio/ogg',
        'ra' => 'audio/x-realaudio',
        'ram' => 'audio/x-pn-realaudio',
        'rm' => 'audio/x-pn-realaudio',
        'rpm' => 'audio/x-pn-realaudio-plugin',
        'snd' => 'audio/basic',
        'tsi' => 'audio/TSP-audio',
        'wav' => 'audio/x-wav',
        'aac' => 'audio/aac',
        'asc' => 'text/plain',
        'c' => 'text/plain',
        'cc' => 'text/plain',
        'css' => 'text/css',
        'etx' => 'text/x-setext',
        'f' => 'text/plain',
        'f90' => 'text/plain',
        'h' => 'text/plain',
        'hh' => 'text/plain',
        'htm' => ['text/html', '*/*'],
        'ics' => 'text/calendar',
        'm' => 'text/plain',
        'rtf' => 'text/rtf',
        'rtx' => 'text/richtext',
        'sgm' => 'text/sgml',
        'sgml' => 'text/sgml',
        'tsv' => 'text/tab-separated-values',
        'tpl' => 'text/template',
        'txt' => 'text/plain',
        'text' => 'text/plain',
        'avi' => 'video/x-msvideo',
        'fli' => 'video/x-fli',
        'mov' => 'video/quicktime',
        'movie' => 'video/x-sgi-movie',
        'mpe' => 'video/mpeg',
        'mpeg' => 'video/mpeg',
        'mpg' => 'video/mpeg',
        'qt' => 'video/quicktime',
        'viv' => 'video/vnd.vivo',
        'vivo' => 'video/vnd.vivo',
        'ogv' => 'video/ogg',
        'webm' => 'video/webm',
        'mp4' => 'video/mp4',
        'm4v' => 'video/mp4',
        'f4v' => 'video/mp4',
        'f4p' => 'video/mp4',
        'm4a' => 'audio/mp4',
        'f4a' => 'audio/mp4',
        'f4b' => 'audio/mp4',
        'gif' => 'image/gif',
        'ief' => 'image/ief',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'jpe' => 'image/jpeg',
        'pbm' => 'image/x-portable-bitmap',
        'pgm' => 'image/x-portable-graymap',
        'png' => 'image/png',
        'pnm' => 'image/x-portable-anymap',
        'ppm' => 'image/x-portable-pixmap',
        'ras' => 'image/cmu-raster',
        'rgb' => 'image/x-rgb',
        'tif' => 'image/tiff',
        'tiff' => 'image/tiff',
        'xbm' => 'image/x-xbitmap',
        'xpm' => 'image/x-xpixmap',
        'xwd' => 'image/x-xwindowdump',
        'ice' => 'x-conference/x-cooltalk',
        'iges' => 'model/iges',
        'igs' => 'model/iges',
        'mesh' => 'model/mesh',
        'msh' => 'model/mesh',
        'silo' => 'model/mesh',
        'vrml' => 'model/vrml',
        'wrl' => 'model/vrml',
        'mime' => 'www/mime',
        'pdb' => 'chemical/x-pdb',
        'xyz' => 'chemical/x-pdb',
        'javascript' => 'application/javascript',
        'form' => 'application/x-www-form-urlencoded',
        'file' => 'multipart/form-data',
        'xhtml' => ['application/xhtml+xml', 'application/xhtml', 'text/xhtml'],
        'xhtml-mobile' => 'application/vnd.wap.xhtml+xml',
        'atom' => 'application/atom+xml',
        'amf' => 'application/x-amf',
        'wap' => ['text/vnd.wap.wml', 'text/vnd.wap.wmlscript', 'image/vnd.wap.wbmp'],
        'wml' => 'text/vnd.wap.wml',
        'wmlscript' => 'text/vnd.wap.wmlscript',
        'wbmp' => 'image/vnd.wap.wbmp',
        'woff' => 'application/x-font-woff',
        'webp' => 'image/webp',
        'appcache' => 'text/cache-manifest',
        'manifest' => 'text/cache-manifest',
        'htc' => 'text/x-component',
        'rdf' => 'application/xml',
        'crx' => 'application/x-chrome-extension',
        'oex' => 'application/x-opera-extension',
        'xpi' => 'application/x-xpinstall',
        'safariextz' => 'application/octet-stream',
        'webapp' => 'application/x-web-app-manifest+json',
        'vcf' => 'text/x-vcard',
        'vtt' => 'text/vtt',
        'mkv' => 'video/x-matroska',
        'pkpass' => 'application/vnd.apple.pkpass',
        'ajax' => 'text/html',
    ];

    /**
     * @var string HTTP Protocol
     */
    protected $protocol = 'HTTP/1.1';

    /**
     * @var int HTTP Status
     */
    protected $status = 200;

    /**
     * @var string HTTP Content-Type
     */
    protected $contentType = 'text/html';

    /**
     * @var array HTTP Headers
     */
    protected $headers = [];

    /**
     * @var string Response body (content)
     */
    protected $body = null;

    /**
     * @var string File to send back to the client (download)
     */
    protected $file = null;

    /**
     * @var string Charset
     */
    protected $charset = 'UTF-8';

    /**
     * @var array Client's cookies
     */
    protected $cookies = [];

    public function __construct(array $options = [])
    {
        if (isset($options['body'])) {
            $this->setBody($options['body']);
        }
        if (isset($options['status'])) {
            $this->setStatus($options['status']);
        }
        if (isset($options['contentType'])) {
            $this->setContentType($options['contentType']);
        }
        if (!isset($options['charset'])) {
            $config = Configure::read('App');
            $options['charset'] = $config['encoding'];
        }
        $this->setCharset($options['charset']);
    }

    public function send()
    {
        if (isset($this->headers['Location']) && $this->status === 200) {
            $this->setStatus(302);
        }
        $this->sendHeaders();

        if ($this->file) {
            $this->sendFile($this->file);
            $this->file = null;
        } else {
            $this->sendContent($this->body);
        }

        if (function_exists('fastcgi_finish_request')) {
            fastcgi_finish_request();
        }
    }

    public function outputCompressed()
    {
        return strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== false && (ini_get('zlib.output_compression') === '1' || in_array('ob_gzhandler', ob_list_handlers()));
    }

    private function sendHeaders()
    {
        if (headers_sent()) {
            return;
        }

        $codeMessage = $this->statusCodes[$this->status];
        $this->sendCookies();
        $this->sendHeader("{$this->protocol} {$this->status} {$codeMessage}");
        $this->sendContentType();
        foreach ($this->headers as $name => $value) {
            $this->sendHeader($name, $value);
        }
    }

    private function sendHeader($name, $value = null)
    {
        if (!$value) {
            header($name);
        } else {
            header("{$name}: {$value}");
        }
    }

    private function sendCookies()
    {
        foreach ($this->cookies as $name => $cookie) {
            setcookie(
                $name,
                $cookie['value'],
                $cookie['expire'],
                $cookie['path'],
                $cookie['domain'],
                $cookie['secure'],
                $cookie['httpOnly']
            );
        }
    }

    private function sendContentType()
    {
        if ($this->charset) {
            $this->sendHeader('Content-Type', "{$this->contentType}; charset={$this->charset}");
        } else {
            $this->sendHeader('Content-Type', "{$this->contentType}");
        }
    }

    private function sendContent($content)
    {
        if (!is_string($content) && is_callable($content)) {
            $content = $content();
        }

        echo $content;
    }

    private function sendFile($filepath)
    {
        if ($fd = @fopen($filepath, 'r')) {
            $fsize = filesize($filepath);
            $this->sendHeader("Content-length: $fsize");
            $this->sendHeader('Expires: 0');
            $this->sendHeader('Cache-control: must-revalidate');
            $this->sendHeader('Pragma: public');
            set_time_limit(0);
            $bufferSize = 8192;
            while (!feof($fd)) {
                echo fread($fd, $bufferSize);
            }
            fclose($fd);

            return true;
        }

        throw new FileNotFoundException("The file $filepath can not be opened");
    }

    /**
     * @return string
     */
    public function getProtocol()
    {
        return $this->protocol;
    }

    /**
     * @param string $protocol
     *
     * @return Response
     */
    public function setProtocol($protocol)
    {
        $this->protocol = $protocol;

        return $this;
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param int $status
     *
     * @return Response
     *
     * @throws UnknownStatusException
     */
    public function setStatus($status)
    {
        if (empty($this->statusCodes[$status])) {
            throw new UnknownStatusException("Unknown HTTP status '$status'");
        }
        $this->status = $status;

        return $this;
    }

    /**
     * @return string
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * @param string $contentType
     *
     * @return Response
     */
    public function setContentType($contentType)
    {
        $this->contentType = $this->mimeTypes[$contentType];

        return $this;
    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @param $name
     * @param $value
     *
     * @return Response
     */
    public function addHeader($name, $value)
    {
        $this->headers[$name] = $value;

        return $this;
    }

    /**
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param null $body
     *
     * @return Response
     */
    public function setBody($body)
    {
        $this->body = $body;

        return $this;
    }

    /**
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param string $filepath
     *
     * @return Response
     */
    public function setFile($filepath)
    {
        $this->file = $filepath;

        return $this;
    }

    /**
     * @return string
     */
    public function getCharset()
    {
        return $this->charset;
    }

    /**
     * @param string $charset
     *
     * @return Response
     */
    public function setCharset($charset)
    {
        $this->charset = $charset;

        return $this;
    }

    /**
     * @return array
     */
    public function getCookies()
    {
        return $this->cookies;
    }

    /**
     * @param array $cookies
     *
     * @return Response
     */
    public function setCookies($cookies)
    {
        $this->cookies = $cookies;

        return $this;
    }
}
