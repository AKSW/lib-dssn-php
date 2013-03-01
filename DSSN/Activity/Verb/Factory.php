<?php
/**
 * Activity verb factory
 *
 * @author  {@link http://sebastian.tramp.name Sebastian Tramp}
 * @license http://sam.zoy.org/wtfpl/  Do What The Fuck You Want To Public License (WTFPL)
 */
class DSSN_Activity_Verb_Factory
{
    /*
     * uses the content of //activity:verb/text() and returns an appropriate
     * verb object
     */
    public static function newFromText($text = null)
    {
        if ($text == null) {
            $message = 'newFromFeedElementText needs an input text';
            throw new DSSN_Exception($message);
        }

        switch ($text) {
            case 'share':
            case 'http://activitystrea.ms/schema/1.0/share':
                return new DSSN_Activity_Verb_Share();
                break;

            case 'post':
            case 'http://activitystrea.ms/schema/1.0/post':
                return new DSSN_Activity_Verb_Post();
                break;

            default:
                $verb = new DSSN_Activity_Verb();
                $verb->setIri($text);
                return $verb;
        }
    }

}

