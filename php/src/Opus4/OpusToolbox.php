<?php

namespace epusta\Opus4;

/**
* Ruleset for the enrichment of logfiles of OPUS4.
*/
class OpusToolbox
{
    public function addIdentifier(& $convertedLogline, $praefix = null)
    {
        $path = $convertedLogline->urlLogline->url;

        /**
         * Add functionality for OPUS4.
         *
         * There are two cases:
         * The first is a frontdoor access. This is tagged with content:counter_abstract and is represented by .../frontdoor/index/...
         * The second is a download. This is tagged with content:counter and represented by .../frontdoor/deliver/... or .../files/...
         * In the logfile there are also other layout-accesses. We decided to not tag this kind of access,
         * because this is not an access of interest.
         */

            $method_names = preg_grep('/^rule/', get_class_methods($this));
        foreach ($method_names as $value) {
            $this->$value($path, $convertedLogline, $praefix);
            $convertedLogline->documentIdentifier = array_unique($convertedLogline->documentIdentifier);
            if ($convertedLogline->urlLogline->httpMethod == 'GET') {
                $convertedLogline->tags = array_unique($convertedLogline->tags);
            } elseif ($convertedLogline->urlLogline->httpMethod == 'HEAD') {
                $convertedLogline->tags = ["oas:content:counter_head"];
            }
        }
    }

    /**
    * Tag a file-download with oas:content:counter. File-download is a URL with "/frontdoor/deliver/..."
    */
    public function ruleDownload($path, & $convertedLogline, $praefix = null)
    {
        if ($praefix == null) {
            if (preg_match("|/([^/]+)/frontdoor/deliver/index/docId/([0-9]+)/file/([A-Za-z0-9.]+)|", $path, $match)) {
                $convertedLogline->tags[] = "oas:content:counter";
                $convertedLogline->documentIdentifier[] = $match[1] . "-" . $match[2];
            }
        } else {
            if (preg_match("|/frontdoor/deliver/index/docId/([0-9]+)/file/([A-Za-z0-9.]+)|", $path, $match)) {
                $convertedLogline->tags[] = "oas:content:counter";
                $convertedLogline->documentIdentifier[] = $praefix . "-" . $match[1];
            }
        }
    }

    /**
    * Tag a frontdoor-access with oas:content:counter_abstract. Frontdoor-access is a URL with "/frontdoor/index/..."
    */
    public function ruleFrontdoorAccess($path, & $convertedLogline, $praefix = null)
    {
        if ($praefix == null) {
            if (preg_match("|/([^/]+)/frontdoor/index/.*/docId/([0-9]+)|", $path, $match)) {
                $convertedLogline->tags[] = "oas:content:counter_abstract";
                $convertedLogline->documentIdentifier[] = $match[1] . "-" . $match[2];
            }
        } else {
            if (preg_match("|/frontdoor/index/.*/docId/([0-9]+)|", $path, $match)) {
                $convertedLogline->tags[] = "oas:content:counter_abstract";
                $convertedLogline->documentIdentifier[] = $praefix . "-" . $match[1];
            }
        }
    }

    /**
    * Tag an asset-access with oas:content:counter_layout. This should tag all URLs with ".../assets/..."
    */
    public function ruleAssetsAccess($path, & $convertedLogline, $praefix = null)
    {
        if ($praefix == null) {
            if (preg_match("|/([^/]+)/assets/([A-Za-z0-9.]+)|", $path, $match)) {
                $convertedLogline->tags[] = "oas:content:counter_layout";
                $convertedLogline->documentIdentifier[] = $match[1];
            }
        } else {
            if (preg_match("|/assets/([A-Za-z0-9.]+)|", $path, $match)) {
                $convertedLogline->tags[] = "oas:content:counter_layout";
                $convertedLogline->documentIdentifier[] = $praefix;
            }
        }
    }

    /**
    * Tag an iamge-access with oas:content:counter_layout. This should tag all URLs with ".../img/..."
    */
    public function ruleImagetAccess($path, & $convertedLogline, $praefix = null)
    {
        if ($praefix == null) {
            if (preg_match("|/([^/]+)/img/([A-Za-z0-9.]+)|", $path, $match)) {
                $convertedLogline->tags[] = "oas:content:counter_layout";
                $convertedLogline->documentIdentifier[] = $match[1];
            }
        } else {
            if (preg_match("|/img/([A-Za-z0-9.]+)|", $path, $match)) {
                $convertedLogline->tags[] = "oas:content:counter_layout";
                $convertedLogline->documentIdentifier[] = $praefix;
            }
        }
    }

    /**
    * Tag any other layout-access with oas:content:counter_layout. This should tag all URLs with ".../layouts/..."
    */
    public function ruleLayoutAccess($path, & $convertedLogline, $praefix = null)
    {
        if ($praefix == null) {
            if (preg_match("|/([^/]+)/layouts/([A-Za-z0-9.]+)|", $path, $match)) {
                $convertedLogline->tags[] = "oas:content:counter_layout";
                $convertedLogline->documentIdentifier[] = $match[1];
            }
        } else {
            if (preg_match("|/layouts/([A-Za-z0-9.]+)|", $path, $match)) {
                $convertedLogline->tags[] = "oas:content:counter_layout";
                $convertedLogline->documentIdentifier[] = $praefix;
            }
        }
    }
}
