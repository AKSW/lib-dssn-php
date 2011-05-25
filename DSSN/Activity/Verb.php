<?php
/**
 * An activity verb
 *
 * @author  {@link http://sebastian.tramp.name Sebastian Tramp}
 * @license http://sam.zoy.org/wtfpl/  Do What The Fuck You Want To Public License (WTFPL)
 */
class DSSN_Activity_Verb extends DSSN_Resource
{
    public function getTurtleTemplate()
    {
        /* default template only a rdf:type statement */
        $template  = <<<EndOfTemplate
            ?resource rdf:type ?type ;
                rdfs:label ?label .
EndOfTemplate;
        return $template;
    }
    public function getTurtleTemplateVars()
    {
        $vars             = array();
        $vars['resource'] = $this->getIri();
        $vars['type']     = $this->getType();
        $vars['label']    = $this->getLabel();
        return $vars;
    }

    /*
     * returns a DOMElement object for the usage in an atom feed
     */
    public function toDomElement() {
        $dom = new DOMDocument('1.0', 'UTF-8');
        $verb = $dom->createElementNS('http://activitystrea.ms/spec/1.0/', 'activity:verb', $this->getAtomIri());

        $dom->appendChild($verb);
        //var_dump($dom->saveXML());
        return $verb;
    }

     /**
     * Get label.
     * a label in past form (user LABEL the following)
     */
    function getLabel()
    {
        return 'was active with';
    }

    /**
     * Set label.
     *
     * @param label the value to set.
     */
    function setLabel($label)
    {
        $this->label = $label;
    }
}
