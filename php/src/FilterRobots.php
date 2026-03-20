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

    public function edit(& $convertedLogline)
    {
        $agent = $convertedLogline->urlLogline->userAgent;
        foreach ($this->robots as $robot) {
            $regex = '/' . $robot["pattern"] . '/';
            if (preg_match($regex, $agent, $treffer)) {
                $convertedLogline->tags[] = "filter:robot";

                if (in_array("oas:content:counter", $convertedLogline->tags)) {
                    if (($key = array_search("oas:content:counter", $convertedLogline->tags)) !== false) {
                        unset($convertedLogline->tags[$key]);
                    }

                    $convertedLogline->tags = array_values($convertedLogline->tags);
                    $convertedLogline->tags[] = "oas:content:robots";
                }

                if (in_array("oas:content:counter_abstract", $convertedLogline->tags)) {
                    if (($key = array_search(
                        "oas:content:counter_abstract",
                        $convertedLogline->tags
                    )) !== false) {
                        unset($convertedLogline->tags[$key]);
                    }

                    $convertedLogline->tags = array_values($convertedLogline->tags);
                    $convertedLogline->tags[] = "oas:content:robots_abstract";
                }
            }
        }
    }
}
