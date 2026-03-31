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
        $str .= json_encode($this->errors) . " ";
        $str .= $this->sessionId . " ";
        $str .= json_encode($this->documentIdentifier) . " ";
        $str .= json_encode($this->associatedIdentifier) . " ";
        $str .= json_encode($this->tags) . " ";
        $str .= $this->rawLogline;

        return $str;
    }

    public function hasErrors(): bool
    {
        return !empty($this->errors);
    }

    public function convertLogline($line)
    {
        $out = $this->uuid . " ";
        $out .= "[] ";      // Errors
        $out .= "- ";       // SessionID
        $out .= "[] ";      // DocumentIdentifier
        $out .= "[] ";      // AssociatedIdentifier
        $out .= "[] ";      // Tags
        $out .= $line;      // Copy of the Original line

        return $out;
    }
}
