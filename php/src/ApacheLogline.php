<?php

namespace epusta;

class ApacheLogline extends URLLogline
{
    public $remoteLogname;
    public $remoteUser;
    public $sizeOfResponse;
    private $format;

    // TODO Why is this empty?
    public function __construct()
    {
        $this->createFormatExpression();
    }

    public function __toString()
    {
        $str = $this->ip . " ";
        $str .= $this->remoteLogname . " ";
        $str .= $this->remoteUser . " ";
        $str .= '[' . $this->time . "] ";
        $str .= '"' . $this->httpMethod . " ";
        $str .= $this->url . " ";
        $str .= $this->httpProtokoll . '" ';
        $str .= $this->httpStatusCode . ' ';
        $str .= $this->sizeOfResponse . " ";
        $str .= '"' . $this->referer . '" ';
        $str .= '"' . $this->userAgent . '"';

        return $str;
    }

    /**
     * Anonymize the IP-Address. This is very important it the context of data-security
     */
    public function anonymizeIp()
    {

        if (filter_var($this->ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            $ipTemp = explode('.', $this->ip);
            $ipTemp[2] = 'XXX';
            $ipTemp[3] = 'XXX';
            $this->ip = implode('.', $ipTemp);
        } elseif (filter_var($this->ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            $ipTemp = explode(':', $this->ip);
            $ipTemp[6] = 'XXXX';
            $ipTemp[7] = 'XXXX';
            $this->ip = implode(':', $ipTemp);
        }
    }

    private function createFormatExpression()
    {
        // Regular expression for the apache logline created by format
        // %{X-Forwarded-For}i %l %u %t \"%r\" %>s %b \"%{Referer}i\" \"%{User-agent}i\"
        $regExp = '/^';
        // $regExp.='(-|\d+\.\d+\.\d+\.\d+|([A-F0-9]{1,4}:){7}[A-F0-9]{1,4}) ';      // IP Adress
        // $regExp.='(unknown|-|\d+\.\d+\.\d+\.\d+(, unknown)?|([A-Fa-f0-9]{1,4}:){7}[A-Fa-f0-9]{1,4}) ';      // IP Adress
        $regExp .= '(.*) ';      // IP Adress
        $regExp .= '(.*) ';                            // Remote logname
        $regExp .= '.* ';                              // Remote user
        $regExp .= '\[(.*)\] ';                        // Time the request was received
        $regExp .= '"(.*) (.*) HTTP\/[1,2]\.[0,1]" ';  // http Method, request URL,
        $regExp .= '(\d\d\d) ';                        // http Status Code
        $regExp .= '([0-9-]+) ';                       // Size of response in bytes
        $regExp .= '"(.*)" ';                          // Referer
        $regExp .= '"(.*)"';                           // User Agent
        $regExp .= '/';

        $this->format = $regExp;
    }

    public function checkFormat($line)
    {
        if (! preg_match($this->format, $line, $treffer)) {
            $logmsg = "Can't parse logline (wrong logformat?):\n";
            $logmsg .= "    " . $line . "\n";
            $logmsg .= "    " . $this->format . "";

            return $logmsg;
        }

        return true;
    }
}
