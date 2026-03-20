<?php

namespace epusta\mir;

use epusta\mir;

class MIRToolbox
{
    public $dbh = false;
    public $config = false;

    public $mycoreObjectFactory = null;
    public $mycoreDerivateFactory = null;


    /**
     * prepares database connection and instance variables
     * @param $config config to be used
     * @param $logger optional: set custom logger
     */
    public function __construct($config, $logger = false)
    {
        $this->config = $config;
        $this->logger = $logger;
        $this->cache = [];
        $this->lastDerivate = "";
        $this->mycoreDerivateFactory = new mir\DerivateFactory($config);
        $this->mycoreObjectFactory = new mir\MyCoReObjectFactory($config);
    }

    public function addIdentifier(& $epuStaLogline)
    {
        $path = $epuStaLogline->urlLogline->url;
        $referer = $epuStaLogline->urlLogline->referer;
        $mycoreIdPattern = (isset($this->config['mycoreIdPattern']) ? $this->config['mycoreIdPattern'] : '([^\/]+_[^\/]+_[0-9]{8})' );
        $derivateIdPattern = (isset($this->config['derivateIdPattern']) ? $this->config['derivateIdPattern'] : '([^\/]+_derivate_[0-9]{8})' );
        
        if (preg_match(
            '/\/rsc\/stat\/'.$mycoreIdPattern.'.css$/',
            $path,
            $match
        )) {
            // The Fake css download
            //fwrite(STDERR, "Match - fake css:".$path."\n");
            $object = $this->mycoreObjectFactory->create($match[1]);

            if ($object) {
                $epuStaLogline->documentIdentifier = $object->getAllIdentifier();
                $epuStaLogline->tags[] = "oas:content:counter_abstract";
                $epuStaLogline->tags = array_merge($object->getSubjects(), $epuStaLogline->tags);
                return true;
            } 
        } elseif (preg_match(
            '/\/receive\/'.$mycoreIdPattern.'(;jsessionid.+|$)/',
            $path,
            $match
        )) {
            // Metadatapage docportal and citelink
            //fwrite(STDERR, "Match - receive:".$path."\n");
            $object = $this->mycoreObjectFactory->create($match[1]);

	    if ($object) {
                $epuStaLogline->documentIdentifier = $object->getAllIdentifier();
                $epuStaLogline->tags[] = "oas:content:counter_abstract";
                $epuStaLogline->tags = array_merge($object->getSubjects(), $epuStaLogline->tags);
                return true;
            } 
        } elseif (preg_match(
            '/\/MCRFileNodeServlet\/'.$derivateIdPattern.'\/([^;?]+)(;jsessionid)?([?]view)?.*/',
            $path,
            $match
        )) {
            //Fulltext download
            //fwrite(STDERR, "Match - MCRFileNodeServlet:".$path."\n");
            //fwrite(STDERR, "Derivat (".$match[1].")");

            if (isset($match[3]) && ! (strpos($match[3], "?view") === false)) {  // If intern Dokviewer
                //fwrite(STDERR, "nur Ansicht.\n");

                return false;
            }
            if (strpos($referer, "pdf.worker.js") !== false
                || strpos($referer, "pdf.min.worker.js") !== false) {
                //fwrite(STDERR, "nur Ansicht(pdfWorker).\n");

                return false;
            }
            $derivateid = $match[1];
            if ($derivateid == $this->lastDerivate) {
                //fwrite(STDERR, "doppeltes Derivate).\n");

                return false;
            }
            $this->lastDerivate = $derivateid;
            $derivate = $this->mycoreDerivateFactory->create($derivateid);
            if ($derivate == null) {
                return false;
            }
            $maindoc = $derivate->maindoc;
            $filename = urldecode($match[2]);
            //fwrite(STDERR, $maindoc." - ".$filename."\n");

            if ($maindoc == $filename) {
                $epuStaLogline->tags[] = "oas:content:counter";
                $epuStaLogline->documentIdentifier[] = $derivateid;

                // Add objectid
                $objectid = $derivate->objectid;
                //fwrite(STDERR, " ObjectID: ".$objectid."\n");
                $object = $this->mycoreObjectFactory->create($objectid);
                $epuStaLogline->documentIdentifier = array_merge($object->getAllIdentifier(), $epuStaLogline->documentIdentifier);
                $epuStaLogline->tags = array_merge($object->getSubjects(), $epuStaLogline->tags);
                //Add URN
                $urn = $derivate->urn;
                if ($urn) {
                    $epuStaLogline->documentIdentifier[] = $urn;
                }

                return true;
            } else {
                //fwrite(STDERR, "nicht das Hauptdokument\n");
                return false;
            }
        } elseif (preg_match(
            '/\/MCRZipServlet\/'.$derivateIdPattern.'(;jsessionid)?.*/',
            $path,
            $match
        )) {
            //fwrite(STDERR, "Match - MCRZipServlet:".$path."\n");
            //fwrite(STDERR, "Derivate (".$match[1].")\n");
            $derivateid = $match[1];
            $epuStaLogline->tags[] = "oas:content:counter";
            $derivate = $this->mycoreDerivateFactory->create($derivateid);
            if ($derivate == null) {
                return false;
            }
            $epuStaLogline->documentIdentifier[] = $derivateid;
            $objectid = $derivate->objectid;
            //fwrite(STDERR, "Object:".$objectid."\n");
            $object = $this->mycoreObjectFactory->create($objectid);
            $epuStaLogline->documentIdentifier = array_merge($object->getAllIdentifier(), $epuStaLogline->documentIdentifier);
            $epuStaLogline->tags = array_merge($object->getSubjects(), $epuStaLogline->tags);
            //Add URN
            $urn = $derivate->urn;
            if ($urn) {
                $epuStaLogline->documentIdentifier = $urn;
            }
        } elseif (preg_match(
            '/\/rsc\/pdf\/'.$derivateIdPattern.'[?]pages=1-\d+$/',
            $path,
            $match
        )) {
            //fwrite(STDERR, "Match - PDF Download:".$path."\n");
            //fwrite(STDERR, "Derivate (".$match[1].")\n");
            $derivateid = $match[1];
            $epuStaLogline->tags[] = "oas:content:counter";
            $derivate = $this->mycoreDerivateFactory->create($derivateid);
            if ($derivate == null) {
                return false;
            }
            $epuStaLogline->documentIdentifier[] = $derivateid;
            $objectid = $derivate->objectid;
            $object = $this->mycoreObjectFactory->create($objectid);
            $epuStaLogline->documentIdentifier = array_merge($object->getAllIdentifier(), $epuStaLogline->documentIdentifier);
            $epuStaLogline->tags = array_merge($object->getSubjects(), $epuStaLogline->tags);
            //Add URN
            $urn = $derivate->urn;
            if ($urn) {
                $epuStaLogline->documentIdentifier[] = $urn;
            }
        } else {
            return false;
        }
        // TODO a return statement is missing here
    }
}
