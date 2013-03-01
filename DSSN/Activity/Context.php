<?php
/**
 * An activity context
 *
 * @author  {@link http://sebastian.tramp.name Sebastian Tramp}
 * @license http://opensource.org/licenses/gpl-license.php GNU General Public License (GPL)
 */
class DSSN_Activity_Context extends DSSN_Resource
{
    private $name        = null;
    private $description = null;

    public function getDirectImports() {
        $myImports = array (
            DSSN_AAIR_name   => 'setName',
            DSSN_RDFS_label  => 'setName'
        );
        //return $myImports;
        $parentImports = parent::getDirectImports();
        return array_merge($myImports, $parentImports);
    }

    /*
     * returns a DOMElement object for the usage in an atom feed
     */
    public function toDomElement() {
        $dom = new DOMDocument('1.0', 'UTF-8');
        $target = $dom->createElementNS('http://activitystrea.ms/spec/1.0/', 'activity:target');

        /* context->id
         * The id of an context construct is an IRI that uniquely identifies the
         * object. Note that the definition of "IRI" excludes relative
         * references. An Object construct SHOULD have an ID value, and MUST
         * NOT have more than one.
         *
         * If an object construct does not have an ID value consumers MAY use
         * the Permalink URL as a weaker identifier, but must in this case
         * allow for the fact that Permalink URL is not defined to be unique
         * across all objects and be prepared to handle duplicates
         */
        $id = $dom->createElement('id', $this->getIri());
        $target->appendChild($id);

        /* context->name
         * This string value provides a human readable display name for the
         * object, if the object has a name. An Object construct MAY have
         * a name, but MUST NOT have more than one.
         */
        $name = $dom->createElement('title', $this->getName());
        $target->appendChild($name);

        /* context->object-type
         * An IRI reference that identifies the type of object. This value MUST
         * be an absolute IRI, or a IRI relative to the base IRI of
         * http://activitystrea.ms/schema/1.0/. An Object construct MAY have
         * a type, but MUST NOT have more than one.
         *
         * If no object type is present, the object has no specific type.
         * Consumers SHOULD refer to such objects only by their names. For
         * example, when forming an activity sentence a consumer might say
         * "Johan posted 'My Cat'" rather than "Johan posted a photo: 'My
         * Cat'".
         */
        $targetType = $dom->createElementNS('http://activitystrea.ms/spec/1.0/', 'object-type', $this->getFeedType());
        $target->appendChild($targetType);

        $dom->appendChild($target);
        return $target;
    }

    /**
     * Get name.
     *
     * @return name.
     */
    function getName()
    {
        return $this->name;
    }

    /**
     * Set name.
     *
     * @param name the value to set.
     */
    function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Get description.
     *
     * @return description.
     */
    function getDescription()
    {
        return $this->description;
    }

    /**
     * Set description.
     *
     * @param description the value to set.
     */
    function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * Get typeLabel.
     *
     * @return typeLabel.
     */
    function getTypeLabel()
    {
        return 'context';
    }
}
