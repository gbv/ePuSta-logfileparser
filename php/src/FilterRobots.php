<?php

namespace epusta;

class FilterRobots
{

    public $robots = null;
    public $robotsFileName = __DIR__ . '/../../config/COUNTER_Robots_list.json';

    public function __construct()
    {
        $robotsFile = file_get_contents($this->robotsFileName);
        $this->robots = json_decode($robotsFile, true);
    }

    public function edit(& $epuStaLogline)
    {
        $agent = $epuStaLogline->urlLogline->userAgent;
        foreach ($this->robots as $robot) {
            $regex = '/' . $robot["pattern"] . '/';
            if (preg_match($regex, $agent, $treffer)) {
                $epuStaLogline->tags[] = "filter:robot";

                if (in_array("oas:content:counter", $epuStaLogline->tags)) {
                    if (($key = array_search("oas:content:counter", $epuStaLogline->tags)) !== false) {
                        unset($epuStaLogline->tags[$key]);
                    }

                    $epuStaLogline->tags = array_values($epuStaLogline->tags);
                    $epuStaLogline->tags[] = "oas:content:robots";
                }

                if (in_array("oas:content:counter_abstract", $epuStaLogline->tags)) {
                    if (($key = array_search(
                        "oas:content:counter_abstract",
                        $epuStaLogline->tags
                    )) !== false) {
                        unset($epuStaLogline->tags[$key]);
                    }

                    $epuStaLogline->tags = array_values($epuStaLogline->tags);
                    $epuStaLogline->tags[] = "oas:content:robots_abstract";
                }
            }
        }
    }
}
