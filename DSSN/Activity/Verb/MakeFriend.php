<?php
/**
 * An activity verb makeFriend
 *
 * @author  {@link http://sebastian.tramp.name Sebastian Tramp}
 * @license http://opensource.org/licenses/gpl-license.php GNU General Public License (GPL)
 */
class DSSN_Activity_Verb_MakeFriend extends DSSN_Activity_Verb
{
    public function __construct() {
        $this->setIri(DSSN_AAIR_NS . 'MakeFriend');
    }

    /**
     * Get label.
     * a label in past form (user LABEL the following)
     */
    function getLabel()
    {
        return 'friended';
    }

    /**
     * Get AtomIri
     *
     * http://activitystrea.ms/head/atom-activity.html#activity.verb
     * An IRI reference that identifies the action of the activity. This value
     * MUST be an absolute IRI, or a IRI relative to the base IRI of
     * http://activitystrea.ms/schema/1.0/. An Activity construct MUST have
     * exactly one verb.
     */
    function getAtomIri()
    {
        return 'makefriend';
    }
}

