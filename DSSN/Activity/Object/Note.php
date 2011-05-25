<?php
/**
 * An activity object note
 *
 * @author  {@link http://sebastian.tramp.name Sebastian Tramp}
 * @license http://sam.zoy.org/wtfpl/  Do What The Fuck You Want To Public License (WTFPL)
 * @seeAlso http://xmlns.notu.be/aair/#term_Note
 */
class DSSN_Activity_Object_Note extends DSSN_Activity_Object
{
    /*
     * the content of the note is the status message
     */
    private $content = '';

    public function getDirectImports() {
        $myImports = array (
            DSSN_AAIR_content   => 'setContent',
        );
        $parentImports = parent::getDirectImports();
        return array_merge($myImports, $parentImports);
    }
    public function getTurtleTemplate()
    {
        /* default template only a rdf:type statement */
        $template  = <<<EndOfTemplate
            ?resource rdf:type ?type ;
                rdfs:label ?content ;
                aair:content ?content .
EndOfTemplate;
        return $template;
    }
    public function getTurtleTemplateVars()
    {
        $vars             = array();
        $vars['resource'] = $this->getIri();
        $vars['type']     = $this->getType();
        $vars['content']  = $this->getContent();
        return $vars;
    }
    /**
     * Get content.
     *
     * @return content.
     */
    function getContent()
    {
        return $this->content;
    }

    /**
     * Set content.
     *
     * @param content the value to set.
     */
    function setContent($content)
    {
        $this->content = $content;
    }

    function getTypeLabel()
    {
        return 'status / note';
    }

    function getFeedType()
    {
        return 'note';
    }
}
