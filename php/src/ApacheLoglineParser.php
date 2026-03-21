<?php

namespace epusta;

class ApacheLoglineParser extends URLLoglineParser
{
    private $regExp;

    public function __construct()
    {
        $this->regExp = '(.*) ';                                    // IP Address
        $this->regExp .= '(.*) ';                                   // Remote logname
        $this->regExp .= '.* ';                                     // Remote user
        $this->regExp .= '\[(.*)\] ';                               // Time
        $this->regExp .= '"([^ ]*) ([^ ]*) HTTP\/[1,2]\.[0,1]" ';  // Method, URL
        $this->regExp .= '(\d\d\d) ';                               // HTTP Status Code
        $this->regExp .= '([0-9-]+) ';                              // Response size
        $this->regExp .= '"(.*)" ';                                 // Referer
        $this->regExp .= '"(.*)"';                                  // User Agent
    }

    public function parse(string $rawLogline, URLLogline &$logline, array &$errors): bool
    {
        $logline = new ApacheLogline();
        $regExp2 = '/^' . $this->regExp . '/';

        if (!preg_match($regExp2, $rawLogline, $treffer)) {
            $errors[] = 'E01';
            return false;
        }

        $logline->ip = trim($treffer[1]);
        $logline->remoteLogname = trim($treffer[2]);
        $logline->time = trim($treffer[3]);
        $logline->httpMethod = trim($treffer[4]);
        $logline->url = trim($treffer[5]);
        $logline->httpStatusCode = trim($treffer[6]);
        $logline->sizeOfResponse = trim($treffer[7]);
        $logline->referer = trim($treffer[8]);
        $logline->userAgent = trim($treffer[9]);

        return true;
    }
}
