<?php
/**
 * A semantic ping
 *
 * @author  {@link http://natanael.comiles.eu/webid Natanael Arndt}
 * @license http://opensource.org/licenses/gpl-license.php GNU General Public License (GPL)
 */
class DSSN_Ping extends DSSN_Resource
{
    private $_sourceIri         = null;
    private $_targetIri         = null;
    private $_commentLiteral    = null;

    /*
     * generates a non-markuped single string title e.g. for feed usage
     */
    public function generateTitle()
    {
        $title  = "";
//        $title .= $this->_source->getName() . ' ';
        $title .= $this->_sourceIri . ' ';
        $title .= 'pinged' . ' ';
//        $title .= $this->_target->getName() . ': ';
        $title .= $this->_targetIri . ': ';

        $title .= $this->_commentLiteral;

        return $title;
    }

    /*
     * exports a detailed HTML snippet
     *
     * TODO: do
     */
    public function toHtml()
    {
    }

    public function getTurtleTemplate()
    {
        // needs pingback Namespace
        $now = date('c', time());
        $template  = <<<EndOfTemplate
            ?pingIri a ping:Itme ;
                ping:source ?sourceIri ;
                ping:target ?targetIri ;
                ping:comment ?commentLiteral .
EndOfTemplate;
        return $template;
    }

    public function getTurtleTemplateVars()
    {
        $vars                   = array();
        $vars['pingIri']        = $this->getIri();
        $vars['sourceIri']      = $this->getSourceIri();
        $vars['targetIri']      = $this->getTargetIri();
        $vars['commentLiteral'] = $this->getCommentLiteral();
        return $vars;
    }

    /**
     * Get source.
     *
     * @return source.
     */
    public function getSourceIri()
    {
        return $this->_sourceIri;
    }

    /**
     * Get source.
     *
     * @return source.
     */
    public function setSourceIri($sourceIri)
    {
        $this->_sourceIri = $sourceIri;
    }

    /**
     * Get target.
     *
     * @return target.
     */
    public function getTargetIri()
    {
        return $this->_targetIri;
    }

    /**
     * Get target.
     *
     * @return target.
     */
    public function getTargetIri($targetIri)
    {
        $this->_targetIri = $targetIri;
    }

    /**
     * Get comment.
     *
     * @return comment.
     */
    public function getCommentLiteral()
    {
        return $this->_commentLiteral;
    }

    /**
     * Get comment.
     *
     * @return comment.
     */
    public function getCommentLiteral($commentLiteral)
    {
        $this->_commentLiteral = $commentLiteral;
    }

}
