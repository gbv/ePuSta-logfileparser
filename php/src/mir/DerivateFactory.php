<?php

namespace epusta\mir;

class DerivateFactory extends AbstractFactory
{

    private $config = null;
    private $cache = [];
    private $debug = false;

    public function __construct($config, bool $debug = false)
    {
        $this->config = $config;
        $this->debug = $debug;
    }

    public function create($derivateid)
    {
        if (isset($this->cache[$derivateid])) {
            return $this->cache[$derivateid];
        }
        // TODO doc-var is not used -> did we need this?
        $doc = new \DOMDocument();
        //$doc->load($this->config['url_prefix']."/receive/".$derivateid."?XSL.Style=xml");

        if ($this->config['getmethod'] == 'file') {
            if (! is_dir($this->config['datadir'])) {
                // Temp - print a notice for user. Replace after intruducing a correct error handling.
                fwrite(STDERR, "MyCoReObjectFactory - Configured directory don't exists. ".$this->config['datadir']."\n");
            }
            $path = $this->config['datadir'] . '/' . $this->getFilePathById($derivateid) . '/' . $derivateid . '.xml';
	} else if ($this->config['getmethod'] == 'file-ocfl') {
	    if (! is_dir($this->config['datadir'])) {
                // Temp - print a notice for user. Replace after intruducing a correct error handling.
                fwrite(STDERR, "MyCoReObjectFactory - Configured directory don't exists. ".$this->config['datadir']."\n");
            }
            $path = $this->config['datadir'] . '/' . $this->getOcflFilePathById('mcrderivate:' . $derivateid, $this->config['datadir']) . '/' . $derivateid . '.xml';
	} else {
            $path = $this->config['url_prefix'] . "/receive/" . $derivateid . "?XSL.Style=xml";
        }

        $doc = $this->getDOMByURL($path);
        if ($doc == null) {
            return null;
        }

        $xpath = new \DOMXpath($doc);
        $elements = $xpath->query("/mycorederivate/derivate/internals[@class='MCRMetaIFS']/internal");
        $element = $elements->item(0);
        $maindoc = $element->getAttribute("maindoc");

        $elements = $xpath->query("/mycorederivate/derivate/linkmetas[@class='MCRMetaLinkID']/linkmeta");
        if ($elements->length > 1) {
            if ($this->debug) fwrite(STDERR, "Warning - (" . $derivateid . ") more then one parent.\n");
        }
        $element = $elements->item(0);
        $objectid = $element->getAttribute("xlink:href");

        $elements = $xpath->query("/mycorederivate/derivate/fileset");
        $element = $elements->item(0);
        $urn = ($element) ? $urn = $element->getAttribute("urn") : null;
        $this->cache[$derivateid] = new Derivate($derivateid, $objectid, $maindoc, $urn);

        return $this->cache[$derivateid];
    }
}
