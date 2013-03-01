<?php
/**
 * An activity object Comment
 *
 * Bookmark - pointer to some URL -- typically a web page
 *
 * @author  {@link http://sebastian.tramp.name Sebastian Tramp}
 * @license http://opensource.org/licenses/gpl-license.php GNU General Public License (GPL)
 * @seeAlso http://xmlns.notu.be/aair/#term_Comment
 */
class DSSN_Activity_Object_Comment extends DSSN_Activity_Object
{
    private $commenter  = '';
    private $content  = '';
    private $label      = '';

    public function getTurtleTemplate()
    {
        /* default template only a rdf:type statement */
        $template  = <<<EndOfTemplate
            ?resource rdf:type ?type ;
                aair:commenter ?commenter;
                aair:content ?content;
                rdfs:label ?label.
EndOfTemplate;
        return $template;
    }
    public function getTurtleTemplateVars()
    {
        $vars              = array();
        $vars['resource']  = $this->getIri();
        $vars['type']      = $this->getType();
        $vars['label']     = $this->getLabel();
        $vars['commenter'] = $this->getCommenter();
        $vars['content']   = $this->getContent();
        return $vars;
    }

    function getTypeLabel()
    {
        return 'comment';
    }

    /**
     * Get content.
     *
     * @return thumbnail.
     */
    function getContent()
    {
        return $this->thumbnail;
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

    /**
     * Get content.
     *
     * @return thumbnail.
     */
    function getContent()
    {
        return $this->thumbnail;
    }

    /**
     * Set commenter.
     *
     * @param commenter the value to set.
     */
    function setCommenter($commenter)
    {
        $this->commenter = $commenter;
    }

    /**
     * Get label.
     *
     * @return label.
     */
    function getLabel()
    {
        return $this->label;
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

    function getFeedType()
    {
        return 'bookmark';
    }
}
