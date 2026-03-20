<?php

namespace epusta;

class ConvertedLoglineParser
{
    public $regExp;

    public function __construct()
    {
        $this->regExp = '([^ ]*) ';           // UUID
        $this->regExp .= '([^ ]*) ';          // SessionID
        $this->regExp .= '(\[[^\]]*\]) ';     // DocumentIdentifier
        $this->regExp .= '(\[[^\]]*\]) ';     // AssociatedIdentifier
        $this->regExp .= '(\[[^\]]*\]) ';     // Tags
        $this->regExp .= '(\[[^\]]*\]) ';     // Errors
        $this->regExp .= '(.*)';              // CopyOfLogline
    }

    public function parse($line, & $logline)
    {
        $logline = new ConvertedLogline;
        $regExp2 = '/^' . $this->regExp . '/';

        if (! $logline) {
            $logline = new ApacheLogline();
            echo "Error keine  ApacheLogline\n";
        }

        if (preg_match($regExp2, $line, $treffer)) {
            $logline->uuid = trim($treffer[1]);
            $logline->sessionId = trim($treffer[2]);
            $logline->documentIdentifier = json_decode(trim($treffer[3]), true);
            $logline->associatedIdentifier = json_decode(trim($treffer[4]), true);
            $logline->tags = json_decode(trim($treffer[5]), true);
            $logline->errors = json_decode(trim($treffer[6]), true);
            $logline->rawLogline = trim($treffer[7]);

            return true;
        } else {
            fwrite(STDERR, "Error: can't parse ConvertedLogline:\n");
            fwrite(STDERR, "    " . $line . "\n");
            fwrite(STDERR, "    " . $regExp2 . "\n");

            return false;
        }
    }
}
