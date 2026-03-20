<?php

namespace epusta;

class ePuStaLoglineParser
{
    public $regExp;
    private $apacheRegExp;

    public function __construct()
    {
        $this->regExp = '([^ ]*) ';           // UUID
        $this->regExp .= '([^ ]*) ';          // SessionID
        $this->regExp .= '(\[[^\]]*\]) ';     // DocumentIdentifier
        $this->regExp .= '(\[[^\]]*\]) ';     // AssociatedIdentifier
        $this->regExp .= '(\[[^\]]*\]) ';     // Tags
        $this->regExp .= '(\[[^\]]*\]) ';     // Errors
        $this->regExp .= '(.*)';              // CopyOfLogline

        $this->apacheRegExp = '(.*) ';                                    // IP Address
        $this->apacheRegExp .= '(.*) ';                                   // Remote logname
        $this->apacheRegExp .= '.* ';                                     // Remote user
        $this->apacheRegExp .= '\[(.*)\] ';                               // Time
        $this->apacheRegExp .= '"([^ ]*) ([^ ]*) HTTP\/[1,2]\.[0,1]" ';  // Method, URL
        $this->apacheRegExp .= '(\d\d\d) ';                               // HTTP Status Code
        $this->apacheRegExp .= '([0-9-]+) ';                              // Response size
        $this->apacheRegExp .= '"(.*)" ';                                 // Referer
        $this->apacheRegExp .= '"(.*)"';                                  // User Agent
    }

    public function parse($line, & $logline)
    {
        $logline = new ePuStaLogline;
        $regExp2 = '/^' . $this->regExp . '/';

        if (preg_match($regExp2, $line, $treffer)) {
            $logline->uuid = trim($treffer[1]);
            $logline->sessionId = trim($treffer[2]);
            $logline->documentIdentifier = json_decode(trim($treffer[3]), true);
            $logline->associatedIdentifier = json_decode(trim($treffer[4]), true);
            $logline->tags = json_decode(trim($treffer[5]), true);
            $logline->errors = json_decode(trim($treffer[6]), true);
            $logline->rawLogline = trim($treffer[7]);

            $logline->urlLogline = $this->parseApacheLogline($logline->rawLogline);

            return true;
        } else {
            fwrite(STDERR, "Error: can't parse ePuStaLogline:\n");
            fwrite(STDERR, "    " . $line . "\n");
            fwrite(STDERR, "    " . $regExp2 . "\n");

            return false;
        }
    }

    private function parseApacheLogline($rawLogline)
    {
        $apacheLogline = new ApacheLogline();
        $regExp2 = '/^' . $this->apacheRegExp . '/';

        if (preg_match($regExp2, $rawLogline, $treffer)) {
            $apacheLogline->ip = trim($treffer[1]);
            $apacheLogline->remoteLogname = trim($treffer[2]);
            $apacheLogline->time = trim($treffer[3]);
            $apacheLogline->httpMethod = trim($treffer[4]);
            $apacheLogline->url = trim($treffer[5]);
            $apacheLogline->httpStatusCode = trim($treffer[6]);
            $apacheLogline->sizeOfResponse = trim($treffer[7]);
            $apacheLogline->referer = trim($treffer[8]);
            $apacheLogline->userAgent = trim($treffer[9]);
        }

        return $apacheLogline;
    }
}
