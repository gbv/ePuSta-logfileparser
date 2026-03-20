<?php

namespace epusta;

class FilterHttpStatus
{
    // TODO Why is this empty?
    public function __construct()
    {
    }

    public function edit(& $epuStaLogline)
    {
        $httpStatus = $epuStaLogline->urlLogline->httpStatusCode;
        if (! ($httpStatus == 200 || $httpStatus == 202 || $httpStatus == 206)) {
            $epuStaLogline->tags[] = "epusta:filter:httpStatus";
        }
    }
}
