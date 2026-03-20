<?php

namespace epusta;

class Counter3Filter30sek
{

    public $lastHits = [];

    // TODO Why is this empty?
    public function __construct()
    {
    }

    public function edit(& $epuStaLogline)
    {
        $uuid = $epuStaLogline->uuid;
        $ip = $epuStaLogline->urlLogline->ip;
        $path = $epuStaLogline->urlLogline->url;
        $time = $epuStaLogline->urlLogline->time;
        $identifier = $epuStaLogline->documentIdentifier;
        $unixtime = strtotime($time);

        // delete old entrys
        while (count($this->lastHits) > 0 && intval($unixtime) - intval(key($this->lastHits)) > 30) {
            //array_shift($this->lastHits);
            unset($this->lastHits[key($this->lastHits)]);
        }
        // Find duplicate entry
        foreach ($this->lastHits as $lastHitsForSec) {
            foreach ($lastHitsForSec as $lastHit) {
                if ($lastHit['ip'] == $ip &&
                    ($lastHit['path'] == $path ||
                        (count($identifier) > 0 && count(array_diff($identifier, $lastHit['identifier'])) == 0)
                    )
                ) {
                    if (! in_array("filter:30sek:counter3",$epuStaLogline->tags)) {
                        $epuStaLogline->tags[] = "filter:30sek:counter3";
                    }
                }
            }
        }

        if (! isset($this->lastHits[$unixtime])) {
            $this->lastHits[$unixtime] = [];
        }
        $thisHit = [];
        $thisHit['ip'] = $ip;
        $thisHit['path'] = $path;
        $thisHit['identifier'] = $identifier;
        $this->lastHits[$unixtime][] = $thisHit;
    }
}
