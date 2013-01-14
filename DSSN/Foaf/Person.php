<?php
/**
 * This file is part of the {@link https://github.com/AKSW/lib-dssn-php/ lib-dssn-php} project.
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU General Public License (GPL)
 */

/**
 * This Class represents a foaf:Person
 *
 * @author  Jonas Brekle <jonas.brekle@gmail.com>
 * @author  Natanael Arndt <arndtn@gmail.com>
 */
class DSSN_Foaf_Person
{
    public $uri;

    protected $ow = null;
    protected $store = null;

    protected $props = array();

    const URI = 1;
    const BASIC = 2;
    const FULL = 3;
    const TRANSITIVE = 3;

    public function __construct($u, $isModel = false, $mode = self::URI, $transitivity = false, $depth = 1)
    {
        if ($isModel) {
            $this->ow = OntoWiki::getInstance();
            $this->store = $this->ow->erfurt->getStore();

            $model = $u;

            $query = 'PREFIX foaf:<'.DSSN_FOAF_NS.'>' . PHP_EOL;
            $query.= 'SELECT ?me' . PHP_EOL;
            $query.= 'FROM <'.$model.'>' . PHP_EOL;
            $query.= 'WHERE {' . PHP_EOL;
            $query.= '  <'.$model.'> a foaf:PersonalProfileDocument .' . PHP_EOL;
            $query.= '  <'.$this->ow->selectedModel->getModelIri().'> foaf:primaryTopic ?me' . PHP_EOL;
            $query.= '}' . PHP_EOL;

            $res = $this->store->sparqlQuery($query);

            if (is_array($res) && !empty ($res)) {
                $me = $res[0]['me'];
            } else {

                $query = 'PREFIX foaf:<'.DSSN_FOAF_NS.'>' . PHP_EOL;
                $query.= 'ASK FROM <'.$model.'>' . PHP_EOL;
                $query.= 'WHERE {' . PHP_EOL;
                $query.= '  <'.$this->ow->selectedModel->getModelIri().'> a foaf:Person' . PHP_EOL;
                $query.= '}' . PHP_EOL;

                $res = $this->store->sparqlAsk($query);

                if($res){
                    $me = $model;
                } else {
                    throw new DSSN_Exception("given uri is not a foaf profile or person");
                }
            }

            $this->uri = $me;

        } else {
            $this->uri = $u;
        }

        if ($mode == self::BASIC) {

        } //... build props

        if ($transitivity != false) {
            $this->props['friends'] = $this->getFriends($transitivity, $depth);
        }
    }

    /**
     *
     * @param type $mode 
     */
    public function getFriends($mode = self::URI, $depth = 1)
    {
        if ($this->ow === null) {
            $this->ow = OntoWiki::getInstance();
        }

        if ($this->store === null) {
            $this->store = $this->ow->erfurt->getStore();
        }

        $friends = array();

        if ($depth == 0) {
            return $friends;
        }

        if ($mode == self::URI) {
            $query = 'PREFIX foaf:<'.DSSN_FOAF_NS.'>' . PHP_EOL;
            $query.= 'SELECT *' . PHP_EOL;
            $query.= 'FROM <'.$ow->selectedModel.'>' . PHP_EOL;
            $query.= 'WHERE {' . PHP_EOL;
            $query.= '  <'.$this->uri.'> foaf:knows ?f' . PHP_EOL;
            $query.= '}' . PHP_EOL;

            $res = $this->store->sparqlQuery($query);

            foreach ($res as $ares) {
               $friends[$ares['f']] = array('uri'=>$ares['f']);
            }

        } else if ($mode == self::BASIC) {

            $query = 'PREFIX foaf:<'.DSSN_FOAF_NS.'>' . PHP_EOL;
            $query.= 'SELECT * FROM <'.$this->ow->selectedModel.'> WHERE {' . PHP_EOL;
            $query.= '  <'.$this->uri.'> foaf:knows ?f .' . PHP_EOL;
            $query.= '  ?f foaf:name ?name ;' . PHP_EOL;
            $query.= '     foaf:birthday ?birthday ;' . PHP_EOL;
            $query.= '     foaf:depiction ?depiction' . PHP_EOL;
            $query.= '}' . PHP_EOL;

            $res = $this->store->sparqlQuery($query);

            foreach ($res as $ares) {
               $friends[$ares['f']] = array(
                   'uri' => $ares['f'],
                   'name' => $ares['name'],
                   'birthday' => $ares['birthday'],
                   'depiction' => $ares['depiction']
               );
            }
        } else if ($mode == self::FULL) {

            $query = 'PREFIX foaf:<'.DSSN_FOAF_NS.'>' . PHP_EOL;
            $query.= 'SELECT *' . PHP_EOL;
            $query.= 'FROM <'.$ow->selectedModel.'>' . PHP_EOL;
            $query.= 'WHERE {' . PHP_EOL;
            $query.= '  <'.$this->uri.'> foaf:knows ?f.' . PHP_EOL;
            $query.= '  ?f ?p ?o.' . PHP_EOL;
            $query.= '}' . PHP_EOL;

            $res = $this->store->sparqlQuery($query);

            foreach ($res as $ares) {
               //TODO
            }
        } else if ($mode == self::TRANSITIVE) {
            //TODO maybe use getTransitiveClosure method...
            $query = 'PREFIX foaf:<'.DSSN_FOAF_NS.'>' . PHP_EOL;
            $query.= 'SELECT *' . PHP_EOL;
            $query.= 'FROM <'.$ow->selectedModel.'>' . PHP_EOL;
            $query.= 'WHERE {' . PHP_EOL;
            $query.= '  <'.$this->uri.'> foaf:knows ?f' . PHP_EOL;
            $query.= '}' . PHP_EOL;

            $res = $this->store->sparqlQuery($query);

            foreach ($res as $ares) {
               $friends[] = new DSSN_Foaf_Person($ares['f'], false, $mode, self::TRANSITIVE, $depth -1);
            }
        }
        return $friends;
    }
}
