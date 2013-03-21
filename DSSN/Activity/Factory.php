<?php
/**
 * Factory for activities (depends on OntoWiki DSSN action addactivity)
 *
 * @author  {@link http://sebastian.tramp.name Sebastian Tramp}
 * @license http://sam.zoy.org/wtfpl/  Do What The Fuck You Want To Public License (WTFPL)
 */
class DSSN_Activity_Factory
{
    /**
     * the ontowiki app object
     */
    private $_ontowiki;

    /**
     * the erfurt app object
     */
    private $_erfurt;

    /**
     * the erfurt store object
     */
    private $_store;

    /**
     * the URI of the model to select
     */
    private $_modelUri;

    /**
     * Constructor for an Activity Factory.
     * @param $store OntoWiki|Erfurt_App An OntoWiki or Erfurt_App object from where the activities
     *        are taken.
     * @param $modelUri string with a URI for the model to select.
     *        Optional if $store is an OntoWiki, mandatory Erfurt_App.
     */
    public function __construct($store, $modelUri = null)
    {
        $this->_modelUri = $modelUri;

        if ($store instanceof OntoWiki) {
            $this->_ontowiki = $store;
        } else if ($store instanceof Erfurt_App) {
            if ($this->_modelUri !== null) {
                $this->_erfurt = $store;
            } else {
                throw new DSSN_Exception(
                    'developer error: Factory constructor needs a model URI if Erfurt_App object ' .
                    'is provided'
                );
            }
        } else if ($store instanceof Erfurt_Store) {
            if ($this->_modelUri !== null) {
                $this->_store = $store;
            } else {
                throw new DSSN_Exception(
                    'developer error: Factory constructor needs a model URI if Erfurt_Store ' .
                    'object is provided'
                );
            }
        } else {
            throw new DSSN_Exception(
                'developer error: Factory constructor needs an Erfurt_App, Erfurt_Store or ' .
                'OntoWiki object'
            );
        }
        //$store     = $this->_ontowiki->erfurt->getStore();
        //$model     = $this->_ontowiki->selectedModel;
        //$translate = $this->_ontowiki->translate;
    }

    /**
     * get a store object
     * @return Erfurt_Store
     */
    private function _getStore()
    {
        if (isset($this->_store) && $this->_store !== null) {
            return $this->_store;
        } else if (isset($this->_erfurt) && $this->_erfurt !== null) {
            return $this->_erfurt->getStore();
        } else if (isset($this->_ontowiki) && $this->_ontowiki !== null) {
            return $this->_ontowiki->erfurt->getStore();
        } else {
            throw new DSSN_Exception('Can\'t get store, there is neither an OntoWiki nor Erfurt');
        }
    }

    /**
     * get the currently sellected model or the default model
     */
    private function _getModel()
    {
        if ($this->_modelUri !== null) {
            $store = $this->_getStore();
            return $store->getModel($this->_modelUri);
        } else if (isset($this->_ontowiki) && $this->_ontowiki !== null) {
            return $this->_ontowiki->selectedModel;
        } else {
            throw new DSSN_Exception('Can\'t get model');
        }
    }

    /**
     * fetch a resource from the store
     */
    public function getFromStore($iri = null, $model = null)
    {
        if ($iri == null) {
            throw new DSSN_Exception('getFromStore needs an IRI string');
        }

        if ($model == null) {
            $model     = $this->_getModel();
        }
        if (!$model instanceof Erfurt_Rdf_Model){
            throw new DSSN_Exception('getFromStore needs a model');
        }

        // query for the activity (Q: restrict ?p here? actor can be big!)
        $query = <<<EndOfTemplate
            SELECT ?p ?o ?p2 ?o2
            WHERE {
                <$iri> ?p ?o.
                OPTIONAL {?o ?p2 ?o2}
            }
EndOfTemplate;
        $data = $model->sparqlQuery($query, array('result_format' => 'extended'));

        // fill the sparql result into an phprdf array / ARC2 index
        $index = new DSSN_Model();
        foreach ($data['results']['bindings'] as $key => $binding) {
            // the fake subject binding
            $s = array ( 'type' => 'uri', 'value'=> $iri);
            // add S P O (direct triple of the activity)
            $index->addStatementFromExtendedFormatArray(
                $s, $binding['p'], $binding['o']
            );
            // add O P2 O2 (triple of the activity objects (verb, actor, ...)
            if (isset($binding['p2'])) {
                $index->addStatementFromExtendedFormatArray(
                    $binding['o'], $binding['p2'], $binding['o2']
                );
            }
        }

        return $this->newFromModel($iri, $index);
    }

    /**
     * gets an ARC2 index / phprdf array and creates an activity from that
     */
    public function newFromModel($iri = null, DSSN_Model $model)
    {
        if ($iri == null) {
            throw new DSSN_Exception('getFromModel needs an IRI string');
        }
        $return = new DSSN_Activity($iri);
        $return->importLiterals($model);

        // check for actor, use factory and set actor to activity
        if ($model->countSP( $iri, DSSN_AAIR_activityActor) != 1) {
            throw new DSSN_Exception('need exactly ONE aair:activityActor statement');
        } else {
            $actorIri   = $model->getValue($iri, DSSN_AAIR_activityActor);
            if ($model->hasSP($actorIri, DSSN_RDF_type)) {
                $actor = DSSN_Resource::initFromType(
                    $model->getValues($actorIri, DSSN_RDF_type)
                );
                $actor->setIri($actorIri);
                $actor->fetchDirectImports($model);
                $return->setActor($actor);
            } else {
                throw new DSSN_Exception('need at least one rdf:type statement');
            }
        }

        // check for object, use factory and set object to activity
        if ($model->countSP( $iri, DSSN_AAIR_activityObject) != 1) {
            throw new DSSN_Exception('need exactly ONE aair:activityObject statement');
        } else {
            $objectIri   = $model->getValue($iri, DSSN_AAIR_activityObject);
            if ($model->hasSP($objectIri, DSSN_RDF_type)) {
                $object = DSSN_Resource::initFromType(
                    $model->getValue($objectIri, DSSN_RDF_type)
                );
                $object->setIri($objectIri);
                $object->fetchDirectImports($model);
                $return->setObject($object);
            } else {
                throw new DSSN_Exception('need at least one rdf:type statement for '.$objectIri);
            }
        }

        // check for target (optional), use factory and set context to activity
        if ($model->countSP($iri, DSSN_AAIR_activityContext) == 1) {
            $contextIri   = $model->getValue($iri, DSSN_AAIR_activityContext);
            if ($model->hasSP($contextIri, DSSN_RDF_type)) {
                $context = DSSN_Resource::initFromType(
                    $model->getValue($contextIri, DSSN_RDF_type)
                );
                $context->setIri($contextIri);
                $context->fetchDirectImports($model);
                $return->setObject($context);
            } else {
                throw new DSSN_Exception('need at least one rdf:type statement for '.$contextIri);
            }
        }

        // check for verb, use factory and set verb to activity
        if ($model->countSP( $iri, DSSN_AAIR_activityVerb) != 1) {
            throw new DSSN_Exception('need exactly ONE aair:activityVerb statement');
        } else {
            $verbIri   = $model->getValue($iri, DSSN_AAIR_activityVerb);
            $verb = DSSN_Resource::initFromType($verbIri);
            $verb->setIri($verbIri);
            $verb->fetchDirectImports($model);
            $return->setVerb($verb);
        }

        return $return;
    }

    /**
     * input is form request from the ShareitModule
     * this method is only available if the factory is initialized with and ontowiki
     * @throws DSSN_Exception
     */
    public function newFromShareItModule($request)
    {
        if (!isset($this->_ontowiki) || $this->_ontowiki === null) {
            throw new DSSN_Exception('No ontowiki was found');
        }

        $type = $request->getParam('activity-type');
        if ($type == '') {
            throw new DSSN_Exception('request error: no activity type parameter');
        } else {
            switch ($type) {
                case 'status':
                    $activity = $this->newStatus(
                        (string) $request->getParam('share-status'),
                        (string) $this->_ontowiki->user->getUri(),
                        (string) $this->_ontowiki->user->getUsername()
                    );
                    break;
                case 'link':
                    $activity = $this->newSharedLink(
                        (string) $request->getParam('share-link-url'),
                        (string) $request->getParam('share-link-name'),
                        (string) $this->_ontowiki->user->getUri(),
                        (string) $this->_ontowiki->user->getUsername()
                    );
                    break;
                default:
                    throw new DSSN_Exception('request error: unknown activity type '.$type.' given.');
                    break;
            }
        }
        return $activity;
    }

    /**
     * creates a new shared link activity
     * @throws DSSN_Exception
     */
    public function newSharedLink($targetUrl = null, $targetName = null, $actorIri = null, $actorName = null)
    {
        if (!isset($this->_ontowiki) || $this->_ontowiki === null) {
            throw new DSSN_Exception('No ontowiki was found');
        }

        //throw new DSSN_Exception("debug: $targetUrl, $targetName, $actorIri, $actorName");
        if ($targetUrl == null) {
            throw new DSSN_Exception('request error: no target url given');
        } elseif ($targetName == null) {
            throw new DSSN_Exception('request error: no target name given');
        } else {
            $activity = new DSSN_Activity;
            $activity->setVerb(new DSSN_Activity_Verb_Share);

            $object = new DSSN_Activity_Object_Bookmark;
            $object->setIri($targetUrl);
            $object->setLabel($targetName);
            $object->setThumbnail('http://cligs.websnapr.com/?size=t&url='.$targetUrl);
            $activity->setObject($object);

            $actor = new DSSN_Activity_Actor_User;
            if ($actorIri == null) {
                $actorIri = (string) $this->_ontowiki->user->getUri();
            }
            if ($actorName == null) {
                $actorName = (string) $this->_ontowiki->user->getGetUsername();
            }
            $actor->setIri($actorIri);
            $actor->setName($actorName);
            $activity->setActor($actor);
            return $activity;
        }
    }

    /**
     * creates a new status note from the current ontowiki user
     * @throws DSSN_Exception
     */
    public function newStatus($content = null, $actorIri = null, $actorName = null)
    {
        if (!isset($this->_ontowiki) || $this->_ontowiki === null) {
            throw new DSSN_Exception('No ontowiki was found');
        }

        if ($content == null) {
            throw new DSSN_Exception('request error: no content given');
        } else {
            $activity = new DSSN_Activity;
            $activity->setVerb(new DSSN_Activity_Verb_Post);

            $object = new DSSN_Activity_Object_Note;
            $object->setContent($content);
            $activity->setObject($object);

            $actor = new DSSN_Activity_Actor_User;
            if ($actorIri == null) {
                $actorIri = (string) $this->_ontowiki->user->getUri();
            }
            if ($actorName == null) {
                $actorName = (string) $this->_ontowiki->user->getUsername();
            }
            $actor->setIri($actorIri);
            $actor->setName($actorName);
            $activity->setActor($actor);
            return $activity;
        }
    }

    /**
     * creates a static example
     */
    static public function newExample()
    {
        DSSN_Utils::setConstants();
        $activity = new DSSN_Activity;
        $verb  = new DSSN_Activity_Verb_Post;
        $activity->setVerb($verb);

        $actor = new DSSN_Activity_Actor_User('http://sebastian.tramp.name');
        $actor->setName('Sebastian Tramp');
        $activity->setActor($actor);

        $object = new DSSN_Activity_Object_Note;
        $object->setContent('my feelings today ...');
        $activity->setObject($object);

        //$context = new DSSN_Activity_Context_Time;
        //$context->setDate(time());
        //$activity->addContext($context);

        return $activity;
    }

    /**
     * new activity based on an atom:feed/atom:entry DOMElement
     *
     * TODO: this needs to be tweaked to conform to the standard (e.g
     * abbrevated syntax)
     */
    public static function newFromDomElement(DOMElement $element)
    {
        // create xpath environment
        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->appendChild($dom->importNode($element, true));
        $xpath = new DOMXPath($dom);
        $xpath->registerNamespace('atom', DSSN_ATOM_NS);
        $xpath->registerNamespace('activity', DSSN_ACTIVITIES_NS);

        // this is our new activity
        $activity = new DSSN_Activity();

        // fetch id
        $nodes = $xpath->query('/atom:entry/atom:id/text()');
        foreach ($nodes as $node) {
            $activity->setIri(strip_tags($node->wholeText));
        }

        // fetch title
        $nodes = $xpath->query('/atom:entry/atom:title/text()');
        foreach ($nodes as $node) {
            $activity->setTitle(strip_tags($node->wholeText));
        }

        // fetch published
        $nodes = $xpath->query('/atom:entry/atom:published/text()');
        foreach ($nodes as $node) {
            $activity->setPublished(strip_tags($node->wholeText));
        }

        // fetch verb
        $nodes = $xpath->query('/atom:entry/activity:verb/text()');
        foreach ($nodes as $node) {
            $verb = DSSN_Activity_Verb_Factory::newFromText(strip_tags($node->wholeText));
            $activity->setVerb($verb);
        }

        // fetch actor
        $nodes = $xpath->query('/atom:entry/atom:author');
        foreach ($nodes as $node) {
            $actor = DSSN_Activity_Actor_Factory::newFromDOMNode($node);
            $activity->setActor($actor);
        }

        // fetch object
        $nodes = $xpath->query('/atom:entry/activity:object');
        foreach ($nodes as $node) {
            $object = DSSN_Activity_Object_Factory::newFromDOMNode($node);
            $activity->setObject($object);
        }

        // fetch context
        $nodes = $xpath->query('/atom:entry/activity:target');
        foreach ($nodes as $node) {
            $context = DSSN_Activity_Context_Factory::newFromDOMNode($node);
            $activity->setContext($context);
        }

        return $activity;
    }

}
