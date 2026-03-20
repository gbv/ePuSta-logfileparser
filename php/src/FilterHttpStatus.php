<?php

namespace epusta;

class FilterHttpStatus
{
    // TODO Why is this empty?
    public function __construct()
    {
    }

    public function edit(& $convertedLogline)
    {
        $httpStatus = $convertedLogline->urlLogline->httpStatusCode;
        if (! ($httpStatus == 200 || $httpStatus == 202 || $httpStatus == 206)) {
            $convertedLogline->tags[] = "epusta:filter:httpStatus";
        }
    }
}
