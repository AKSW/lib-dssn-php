<?php
/**
 * An activity verb share
 *
 * @author  {@link http://sebastian.tramp.name Sebastian Tramp}
 * @license http://sam.zoy.org/wtfpl/  Do What The Fuck You Want To Public License (WTFPL)
 */
class DSSN_Activity_Verb_Share extends DSSN_Activity_Verb
{
    public function __construct() {
        $this->setIri(DSSN_AAIR_NS . 'Share');
    }
    
    /**
     * Get label.
     * a label in past form (user LABEL the following)
     */
    function getLabel()
    {
        return 'shared';
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
        return 'share';
    }
}

