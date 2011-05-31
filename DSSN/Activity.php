<?php
/**
 * An activity
 *
 * @author  {@link http://sebastian.tramp.name Sebastian Tramp}
 * @license http://sam.zoy.org/wtfpl/  Do What The Fuck You Want To Public License (WTFPL)
 */
class DSSN_Activity extends DSSN_Resource
{
    private $_actor     = null;
    private $_verb      = null;
    private $_object    = null;
    private $_published = null;


    /*
     * generates a non-markuped single string title e.g. for feed usage
     */
    public function getTitle()
    {
        $title  = "";
        $title .= $this->_actor->getName() . ' ';
        $title .= $this->_verb->getLabel() . ' ';
        $title .= 'the following' . ' ';
        $title .= $this->_object->getTypeLabel() . ': ';

        if (method_exists($this->_object, 'getContent')) {
            $title .= $this->_object->getContent();
        } else {
            $title .= $this->_object->getIri();
        }
        return $title;
    }

    /*
     * exports a detailed HTML snippet
     *
     * TODO: use some template engine engine for this
     * TODO: return instead of echo
     */
    public function toHtml()
    {
        $actor    = $this->_actor;
        $object   = $this->_object;
        $verb     = $this->_verb;
        $activity = $this;
    ?>
<div class="lib-dssn-php-activity">
    <span class="subject">
        <img class="avatar" src="<?php echo $actor->getAvatar(); ?>" />
        <a href="<?php echo $actor->getIri() ?>">
            <?php echo $actor->getName() ?>
        </a>
    </span>
    <span class="verb">
        <?php echo $verb->getLabel() ?> the following
        <a href="<?php echo $object->getIri() ?>"><?php echo $object->getTypeLabel() ?></a>
    </span>
    <span title="<?php echo $activity->getPublished() ?>" class="published"><?php echo $activity->getPublishedLabel() ?></span>:
    <span class="object">
<?php if ($object instanceof DSSN_Activity_Object_Note || $object instanceof DSSN_Activity_Object_Site) : ?>
        <span class="content"><?php echo $object->getContent() ?></span>
<?php else : ?>
    <?php /*
        <a href="<?php echo $item['objectUri'] ?>"><?php echo $item['objectUri'] ?></a>
        <?php if(isset($item['objectThumbnail'])) :?>
        <a href="<?php echo $item['objectUri'] ?>">
            <img class="thumbnail" src="<?php echo $item['objectThumbnail'] ?>" />
        </a>
        <?php endif; ?>
        <?php if(isset($item['objectContent'])) :?>
        <span class="content"><?php echo $item['objectContent'] ?></span>
        <?php endif; ?>
*/ ?>
<?php endif ?>
    </span>
</div>
    <?php
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
        if ($model->countSP($iri, DSSN_ATOM_published) != 1) {
            throw new DSSN_Exception('need exactly ONE atom:published statement');
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
        if ($this->_published == null) {
            $this->setPublished(date('c', time()));
        }
        return $this->_published;
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
        $this->_published = $published;
    }

    /**
     * Get actor.
     *
     * @return actor.
     */
    public function getActor()
    {
        return $this->_actor;
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
            $this->_actor = $actor;
        } else {
            throw DSSN_Exception('setActor needs an DSSN_Activity_Actor'.
                'or an IRI string as parameter');
        }
    }

    /**
     * Get verb.
     *
     * @return verb.
     */
    public function getVerb()
    {
        return $this->_verb;
    }

    /**
     * Set verb.
     *
     * @param verb the value to set.
     */
    public function setVerb(DSSN_Activity_Verb $verb)
    {
        $this->_verb = $verb;
    }

    /**
     * Get object.
     *
     * @return object.
     */
    public function getObject()
    {
        return $this->_object;
    }

    /**
     * Set object.
     *
     * @param object the value to set.
     */
    public function setObject(DSSN_Activity_Object $object)
    {
        $this->_object = $object;
    }

}
