<?php

namespace epusta\mir;

abstract class AbstractFactory
{
    abstract protected function __construct($config);

    abstract protected function create($id);

    protected function getFilePathById($id)
    {
        if (preg_match('/([^\/]+)_([^\/]+)_([0-9]{1,4})([0-9]{2})[0-9]{2}$/', $id, $match)) {
            $project = $match[1];
            $type = $match[2];
            $dig4 = $match[3];
            $dig2 = $match[4];
            return $project . "/" . $type . "/" . $dig4 . "/" . $dig2;
        } else if (preg_match('/([^\/]+)_([^\/]+)_([0-9]{1,2})[0-9]{2}$/', $id, $match)) {
            $project = $match[1];
            $type = $match[2];
            $dig2 = $match[3];
            return $project . "/" . $type . "/" . $dig2;
        } else {
            return null;
        }
    }


    private function _percent_encode($c) {
        $c_bytes = mb_convert_encoding($c, 'UTF-8');
        $s = '';
        for ($i=0; $i < strlen($c_bytes);$i++) {
                $b = $c_bytes[$i];
                $s .= '%' . bin2hex($b);
        }
        return $s;
    }

    private function _get_encapsulation_directory($object_id, $digest) {
        $d = '';
        for ($i=0; $i < strlen($object_id);$i++) {
                $c = $object_id[$i];
                if (preg_match('/[A-Za-z0-9-_]{1}/', $c)) {
                        $d .= $c;
                } else {
                        $d .= $this->_percent_encode($c);
                };
                if (strlen($d) > 100) return substr($d,0,100)."-".$digest;
        }
        return $d;
    }

    private function ocfl_path($object_id, $algorithm='sha256', $tuple_size=3, $number_of_tuples=3){
        $object_id_utf8 = mb_convert_encoding($object_id, 'UTF-8');
        $digest = hash($algorithm,$object_id_utf8);
        $digest = strtolower($digest);
        $path = '';
        for ($i=0; $i < $number_of_tuples; $i++) {
                $part = substr($digest,$i*$tuple_size,$tuple_size);
                $path .= $part."/";
        }
        $encapsulation_directory = $this->_get_encapsulation_directory($object_id, $digest);
        $path .= $encapsulation_directory;
        return $path;
    }

    protected function getOcflFilePathById($id)
    {
        // config from extensions/0003-hash-and-id-n-tuple-storage-layout/config.json
	$digestAlgorithm = "sha256";
        $tupleSize = 3;
        $numberOfTuples = 3;
	$extensionName = "0003-hash-and-id-n-tuple-storage-layout";
	
	$path = "ocfl-root-neu/".$this->ocfl_path($id,$digestAlgorithm,$tupleSize,$numberOfTuples);
	$dirs = scandir('/mcr/dfi/docker-data/mir-data/'.$path);
	$vdir='';
	foreach ($dirs as $dir) {
		if (substr($dir,0,1) == 'v'  ) $vdir=$dir;
	}
	return $path."/".$vdir."/content/metadata";
    }

    protected function getDOMByURL($url)
    {
        $doc = new \DOMDocument();
        $count = 0;
	// TODO Why is this developed in this way?
	//libxml_set_external_entity_loader(LIBXML_NOENT);
	$xml = @file_get_contents($url);
        if ($xml === false) {
            return null;
        }
	$load = $doc->loadXML($xml);
        //$load = $doc->load($url,LIBXML_NOWARNING );
        //while ($count < 10 && ! $load) {
            //fwrite(STDERR, "Error: unable to get Data from ".$url.". Try to reconnect(".$count.")\n");
        //    usleep(2000);
        //    @$load = $doc->load($url, LIBXML_NOWARNING);
        //    $count++;
        //}
        if (! $load) {
            //throw new Exception("Error: unable to get Data from ".$this->config['url_prefix']);

            return null;
        }
        return $doc;
    }
}
