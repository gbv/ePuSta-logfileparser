<?php

namespace epusta;

class FilterHttpMethod
{
    public function __construct()
    {
    }

    public function edit(& $convertedLogline)
    {
        $httpMethod = $convertedLogline->urlLogline->httpMethod;
        if ($httpMethod != 'GET') {
            $convertedLogline->tags[] = "epusta:filter:httpMethod";
        }
    }
}
