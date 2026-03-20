<?php

namespace epusta;

class FilterHttpMethod
{
    public function __construct()
    {
    }

    public function edit(& $epuStaLogline)
    {
        $httpMethod = $epuStaLogline->urlLogline->httpMethod;
        if ($httpMethod != 'GET') {
            $epuStaLogline->tags[] = "epusta:filter:httpMethod";
        }
    }
}
