<?php

namespace epusta;

class ePuStaErrors
{
    const ERRORS = [
        'E01' => 'Apachelog line could not be parsed',
        'E02' => 'ePuStalog line could not be parsed',
        'E03' => 'UUID could not be parsed from ePuSta log line',
        'E04' => 'Error array field could not be parsed from ePuSta log line',
        'E05' => 'Repository object could not be loaded (file not found or unreadable)',
    ];
}
