<?php
/**
 * An activity verb post
 *
 * @category   OntoWiki
 * @package    OntoWiki_extensions_components_dssn
 * @copyright  Copyright (c) 2011, {@link http://aksw.org AKSW}
 * @license    http://opensource.org/licenses/gpl-license.php GNU General Public License (GPL)
 */
class DSSN_Activity_Verb_Post extends DSSN_Activity_Verb
{
    public function __construct() {
        $this->setIri(DSSN_AAIR_NS . 'Post');
    }
}

