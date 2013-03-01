<?php
/**
 * Activity context factory
 *
 * @author  {@link http://sebastian.tramp.name Sebastian Tramp}
 * @author Norman Radtke <norman.radtke@gmail.com>
 * @license http://opensource.org/licenses/gpl-license.php GNU General Public License (GPL)
 */
class DSSN_Activity_Context_Factory
{
    /*
     * uses the content of //activity:context/activity:contextType/text()
     * and returns an appropriate object
     */
    public static function newFromContextTypeString($text = null)
    {
        if ($text == null) {
            $message = 'newFromContextTypeString needs an input text';
            throw new DSSN_Exception($message);
        }

        switch ($text) {
            case 'note':
            case 'http://activitystrea.ms/schema/1.0/note':
                return new DSSN_Activity_Object_Note();
                break;

            case 'bookmark':
            case 'http://activitystrea.ms/schema/1.0/bookmark':
                return new DSSN_Activity_Object_Bookmark();
                break;

            default:
                $message = 'newFromFeedElementText: Unknown object ' . $text;
                throw new DSSN_Exception($message);
                break;
        }
    }

    /*
     * uses the content of //activity:object and returns an appropriate
     * verb object
     */
    public static function newFromDOMNode(DOMNode $node)
    {
        // create xpath environment
        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->appendChild($dom->importNode($node, true));
        $xpath = new DOMXPath($dom);
        $xpath->registerNamespace('atom', DSSN_ATOM_NS);
        $xpath->registerNamespace('activity', DSSN_ACTIVITIES_NS);

        // fetch context-type and create object of that
        $nodes = $xpath->query('/activity:target/activity:object-type/text()');
        $object = null;
        foreach($nodes as $node) {
            $object = DSSN_Activity_Context_Factory::newFromContextTypeString(strip_tags($node->wholeText));
        }

        // fetch name
        $nodes = $xpath->query('/activity:target/atom:title/text()');
        foreach ($nodes as $node) {
            $context->setName(strip_tags($node->wholeText));

            if ($context instanceof DSSN_Activity_Context_Note) {
                $context->setContent(strip_tags($node->wholeText));
            }
        }

        // fetch iri
        $nodes = $xpath->query('/activity:target/atom:id/text()');
        foreach ($nodes as $node) {
            $context->setIri(strip_tags($node->wholeText));
        }

        return $context;
    }

}

