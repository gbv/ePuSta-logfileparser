<?php

namespace epusta;

class ePuStaLoglineParser
{
    public $regExp;
    private $urlLoglineParser;
    private $debug;

    public function __construct(string $urlLoglineParserClass = ApacheLoglineParser::class, bool $debug = false)
    {
        $this->debug = $debug;
        $this->regExp = '([^ ]*) ';           // UUID
        $this->regExp .= '([^ ]*) ';          // SessionID
        $this->regExp .= '(\[[^\]]*\]) ';     // DocumentIdentifier
        $this->regExp .= '(\[[^\]]*\]) ';     // AssociatedIdentifier
        $this->regExp .= '(\[[^\]]*\]) ';     // Tags
        $this->regExp .= '(\[[^\]]*\]) ';     // Errors
        $this->regExp .= '(.*)';              // CopyOfLogline

        $this->urlLoglineParser = new $urlLoglineParserClass();
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
            $logline->errors = json_decode(trim($treffer[6]), true) ?? [];
            $logline->rawLogline = trim($treffer[7]);

            $urlLogline = new URLLogline();
            $urlErrors = [];
            $this->urlLoglineParser->parse($logline->rawLogline, $urlLogline, $urlErrors);
            $logline->urlLogline = $urlLogline;

            if (!empty($urlErrors)) {
                $logline->errors = array_merge($logline->errors, $urlErrors);
            }

            return true;
        } else {
            $logline->errors = ['E02'];
            if ($this->debug) {
                fwrite(STDERR, "Error: can't parse ePuStaLogline:\n");
                fwrite(STDERR, "    " . $line . "\n");
                fwrite(STDERR, "    " . $regExp2 . "\n");
            }

            return false;
        }
    }
}
