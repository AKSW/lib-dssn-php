<?php
/**
 * Factory for feeds esp. for creating feeds from XML and feed URLs
 *
 * @author  {@link http://sebastian.tramp.name Sebastian Tramp}
 * @license http://sam.zoy.org/wtfpl/  Do What The Fuck You Want To Public License (WTFPL)
 */
class DSSN_Activity_Feed_Factory
{

    /*
     * creates a DSSN_Feed object from a URL string
     */
    public static function newFromUrl($url = null)
    {
        if (($url == null) || (!is_string($url))) {
            $message = 'Feed factory method newFromUrl needs an URL string';
            throw new DSSN_Exception($message);
        }

        // since DOMDocument::loadXML thows Warnings instead of Exceptions
        // hack the error handler (see $this->handleXmlError)
        $handler= array('DSSN_Activity_Feed_Factory', 'HandleXmlError');
        set_error_handler($handler);

        // try to parse the document and restore standard handler after that
        $dom = DOMDocument::load($url);
        restore_error_handler();

        // route to newFromDomDocument
        return DSSN_Activity_Feed_Factory::newFromDomDocument($dom);
    }


    /*
     * creates a DSSN_Feed object from a feed xml string
     */
    public static function newFromXml($xml = null)
    {
        if (($xml == null) || (!is_string($xml))) {
            $message = 'Feed factory method newFromXml needs an XML string';
            throw new DSSN_Exception($message);
        }

        // since DOMDocument::loadXML thows Warnings instead of Exceptions
        // hack the error handler (see $this->handleXmlError)
        $handler= array('DSSN_Activity_Feed_Factory', 'HandleXmlError');
        set_error_handler($handler);

        // try to parse the document and restore standard handler after that
        $dom = DOMDocument::loadXml($xml);
        restore_error_handler();

        // route to newFromDomDocument
        return DSSN_Activity_Feed_Factory::newFromDomDocument($dom);
    }

    /*
     * creates a DSSN_Feed object from a DOMDocument object
     */
    public static function newFromDomDocument(DOMDocument $dom)
    {
        $feed = new DSSN_Activity_Feed();

        // create xpath environment
        $xpath = new DOMXPath($dom);
        $xpath->registerNamespace('atom', DSSN_ATOM_NS);

        // fetch feed/title
        $nodes = $xpath->query('/atom:feed/atom:title/text()');
        foreach ($nodes as $titleNode) {
            $feed->setTitle(strip_tags($titleNode->wholeText));
        }

        // fetch feed/updated
        $nodes = $xpath->query('/atom:feed/atom:updated/text()');
        foreach ($nodes as $updatedNode) {
            $feed->setUpdated(strip_tags($updatedNode->wholeText));
        }

        // fetch feed/entries
        $nodes = $xpath->query('/atom:feed/atom:entry');
        foreach ($nodes as $entryNode) {
            $activity = DSSN_Activity_Factory::newFromDomElement($entryNode);
            $feed->addActivity($activity);
        }

        return $feed;
    }

    /*
     * since DOMDocument::loadXML thows Warnings instead of Exceptions
     * http://www.php.net/manual/en/domdocument.loadxml.php#69295
     */
    public static function handleXmlError($errno, $errstr, $errfile, $errline)
    {
        // test for specific string
        $substrCount = substr_count($errstr, "DOMDocument::loadXML()");
        // throw DSSN_Exception if there is a loadXML warning
        if (($errno==E_WARNING) && ($substrCount > 0)) {
            throw new DSSN_Exception($errstr);
        } else {
            return false;
        }
    }

}
