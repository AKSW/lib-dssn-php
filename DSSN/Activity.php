<?php
/**
 * An activity
 *
 * @author  {@link http://sebastian.tramp.name Sebastian Tramp}
 * @license http://sam.zoy.org/wtfpl/  Do What The Fuck You Want To Public License (WTFPL)
 */
class DSSN_Activity extends DSSN_Resource
{
    private $actor     = null;
    private $verb      = null;
    private $object    = null;
    private $published = null;


    /*
     *
     */
    public function getTitle()
    {
        return "TODO: title";
    }

    /*
     * exports an activity as an atom entry
     */
    public function toAtomEntry()
    {
        $dom = new DOMDocument('1.0', 'UTF-8');
        $entry = $dom->createElement('entry');

        // entry->id
        $id = $dom->createElement('id', $this->getIri());
        $entry->appendChild($id);
        // entry->title
        $title = $dom->createElement('title', $this->getTitle());
        $entry->appendChild($title);
        // entry->published
        $published = $dom->createElement('published', $this->getPublished());
        $entry->appendChild($published);
        // entry->link
        $link = $dom->createElement('link');
        $link->setAttribute("rel", "alternate");
        $link->setAttribute("type", "text/html");
        $link->setAttribute("href", $this->getIri());
        $entry->appendChild($link);

        // entry->author
        $author = $this->getActor()->toDomElement();
        $entry->appendChild($dom->importNode($author, true));

        // entry->object
        $object = $this->getObject()->toDomElement();
        $entry->appendChild($dom->importNode($object, true));

        // entry->verb
        $verb = $this->getVerb()->toDomElement();
        $entry->appendChild($dom->importNode($verb, true));

        return $entry;
    }


    public function getSubResources()
    {
        return array(
            $this->getActor(),
            $this->getVerb(),
            $this->getObject()
        );
    }

    public function importLiterals(DSSN_Model $model) {
        $iri = $this->getIri();
        if ($model->countSP( $iri, DSSN_ATOM_published) != 1) {
            throw new Exception('need exactly ONE atom:published statement');
        } else {
            $published = $model->getValue($iri, DSSN_ATOM_published);
            $this->setPublished($published);
        }
    }

    public function getTurtleTemplate()
    {
        $now = date('c', time());
        $template  = <<<EndOfTemplate
            ?activityIri a aair:Activity ;
                atom:published "$now"^^xsd:dateTime ;
                aair:activityVerb   ?verbIri ;
                aair:activityActor  ?actorIri ;
                aair:activityObject ?objectIri .
EndOfTemplate;
        return $template;
    }

    public function getTurtleTemplateVars()
    {
        $vars                = array();
        $vars['activityIri'] = $this->getIri();
        $vars['verbIri']     = $this->getVerb()->getIri();
        $vars['actorIri']    = $this->getActor()->getIri();
        $vars['objectIri']   = $this->getObject()->getIri();
        return $vars;
    }

    /**
     * Get published.
     *
     * @return published.
     */
    public function getPublished()
    {
        /* set to current time if not set by now */
        if ($this->published == null) {
            $this->setPublished(date('c', time()));
        }
        return $this->published;
    }
    
    /**
     * Get published label as nice diff string.
     *
     * @return string
     */
    public function getPublishedLabel()
    {
        return OntoWiki_Utils::dateDifference($this->getPublished(), null, 3);
    }

    /**
     * Set published.
     *
     * @param published the value to set (as ISO 8601 dateTime string).
     */
    public function setPublished($published)
    {
        $this->published = $published;
    }

    /**
     * Get actor.
     *
     * @return actor.
     */
    public function getActor()
    {
        return $this->actor;
    }

    /**
     * Set actor.
     *
     * @note  if an IRI string is given a DSSN_Activity_Actor_User is created
     * @param actor is a DSSN_Activity_Actor object or an IRI string
     */
    public function setActor ($actor = null)
    {
        if (is_string($actor)) {
            $actor = new DSSN_Activity_Actor_User($actor);
        }
        if ($actor instanceof DSSN_Activity_Actor) {
            $this->actor = $actor;
        } else {
            throw Exception ('setActor needs an DSSN_Activity_Actor or an IRI string as parameter');
        }
    }

    /**
     * Get verb.
     *
     * @return verb.
     */
    public function getVerb()
    {
        return $this->verb;
    }

    /**
     * Set verb.
     *
     * @param verb the value to set.
     */
    public function setVerb(DSSN_Activity_Verb $verb)
    {
        $this->verb = $verb;
    }

    /**
     * Get object.
     *
     * @return object.
     */
    public function getObject()
    {
        return $this->object;
    }

    /**
     * Set object.
     *
     * @param object the value to set.
     */
    public function setObject(DSSN_Activity_Object $object)
    {
        $this->object = $object;
    }

}
