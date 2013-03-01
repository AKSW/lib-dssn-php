<?php
/**
 * An activity object Photo
 *
 * Bookmark - pointer to some URL -- typically a web page
 *
 * @author  {@link http://sebastian.tramp.name Sebastian Tramp}
 * @license http://opensource.org/licenses/gpl-license.php GNU General Public License (GPL)
 * @seeAlso http://xmlns.notu.be/aair/#term_Bookmark
 */
class DSSN_Activity_Object_Photo extends DSSN_Activity_Object
{
    private $largerImage = '';
    private $thumbnail   = '';
    private $label       = '';

    public function getTurtleTemplate()
    {
        /* default template only a rdf:type statement */
        $template  = <<<EndOfTemplate
            ?resource rdf:type ?type ;
                aair:largerImage ?largerImage;
                aair:thumbnail ?thumbnail;
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
        $vars['largerImage'] = $this->getLargerImage();
        $vars['thumbnail'] = $this->getThumbnail();
        return $vars;
    }

    function getTypeLabel()
    {
        return 'photo';
    }

    /**
     * Get largerImage.
     *
     * @return largerImage.
     */
    function getLargerImage()
    {
        return $this->largerImage;
    }

    /**
     * Set largerImage.
     *
     * @param largerImage the value to set.
     */
    function setLargerImage($largerImage)
    {
        $this->largerImage = $largerImage;
    }

    /**
     * Get thumbnail.
     *
     * @return thumbnail.
     */
    function getThumbnail()
    {
        return $this->thumbnail;
    }

    /**
     * Set thumbnail.
     *
     * @param thumbnail the value to set.
     */
    function setThumbnail($thumbnail)
    {
        $this->thumbnail = $thumbnail;
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
        return 'photo';
    }
}
