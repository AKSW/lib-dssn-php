<?php
/**
 * Activity actor factory
 *
 * @author  {@link http://sebastian.tramp.name Sebastian Tramp}
 * @license http://sam.zoy.org/wtfpl/  Do What The Fuck You Want To Public License (WTFPL)
 */
class DSSN_Activity_Actor_Factory
{
    /*
     * uses the content of //atom:author and returns an appropriate
     * actor object
     */
    public static function newFromDOMNode(DOMNode $node)
    {
        // create xpath environment
        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->appendChild($dom->importNode($node, true));
        $xpath = new DOMXPath($dom);
        $xpath->registerNamespace('atom', DSSN_ATOM_NS);
        $xpath->registerNamespace('activity', DSSN_ACTIVITIES_NS);

        // this is the actor we want
        $actor = new DSSN_Activity_Actor_User();

        // fetch name
        $nodes = $xpath->query('/atom:author/atom:name/text()');
        foreach ($nodes as $node) {
            $actor->setName(strip_tags($node->wholeText));
        }

        // fetch iri
        $nodes = $xpath->query('/atom:author/atom:uri/text()');
        foreach ($nodes as $node) {
            $actor->setIri(strip_tags($node->wholeText));
        }

        return $actor;
    }

}

