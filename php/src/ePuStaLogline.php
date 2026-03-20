<?php

namespace epusta;

class ePuStaLogline
{
    public $uuid;
    public $sessionId;
    public $documentIdentifier;
    public $associatedIdentifier;
    public $tags;
    public $errors;
    public $rawLogline;
    public $urlLogline;

    public function __toString()
    {
        $str = $this->uuid . " ";
        $str .= $this->sessionId . " ";
        $str .= json_encode($this->documentIdentifier) . " ";
        $str .= json_encode($this->associatedIdentifier) . " ";
        $str .= json_encode($this->tags) . " ";
        $str .= json_encode($this->errors) . " ";
        $str .= $this->rawLogline;

        return $str;
    }

    public function convertLogline($line)
    {
        $out = $this->uuid . " ";
        $out .= "- ";       // SessionID
        $out .= "[] ";      // DocumentIdentifier
        $out .= "[] ";      // AssociatedIdentifier
        $out .= "[] ";      // Tags
        $out .= "[] ";      // Errors
        $out .= $line;      // Copy of the Original line

        return $out;
    }
}
