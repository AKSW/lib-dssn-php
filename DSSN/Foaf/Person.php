<?php
/**
 * An activity user
 *
 * @author  Jonas Brekle <jonas.brekle@gmail.com>
 * @license http://sam.zoy.org/wtfpl/  Do What The Fuck You Want To Public License (WTFPL)
 */
class DSSN_Foaf_Person
{
    public $uri;
    
    protected $ow;
    protected $store;
    
    protected $props = array();

    const URI = 1;
    const BASIC = 2;
    const FULL = 3;
    const TRANSITIVE = 3;
    
    public function __construct($u, $isModel = false, $mode = self::URI, $transitivity = false, $depth = 1) {
        $this->ow = OntoWiki::getInstance();
        $this->store = $this->ow->erfurt->getStore();
        if($isModel){
            $model = $u;
            $res = $this->store->sparqlQuery('PREFIX foaf:<'.DSSN_FOAF_NS.'> SELECT ?me FROM <'.$model.'> WHERE {<'.$model.'> a foaf:PersonalProfileDocument . <'.$this->ow->selectedModel->getModelIri().'> foaf:primaryTopic ?me}');
            if(is_array($res) && !empty ($res)){
                $me = $res[0]['me'];
            } else {
                $res = $this->ow->sparqlAsk('PREFIX foaf:<'.DSSN_FOAF_NS.'> ASK FROM <'.$model.'> WHERE {<'.$this->ow->selectedModel->getModelIri().'> a foaf:Person}');
                if($res){
                    $me = $model;
                } else {
                    throw new OntoWiki_Exception("given uri is not a foaf profile or person");
                }
            }
            $this->uri = $me;
        } else $this->uri = $u;
        if($mode == self::BASIC){
            
        } //... build props
        if($transitivity != false){
            $this->props['friends'] = $this->getFriends($transitivity, $depth);
        }
    }
    
    /**
     *
     * @param type $mode 
     */
    public function getFriends($mode = self::URI, $depth = 1){
        $friends = array();
        if($depth == 0) return $friends;
        if($mode == self::URI){
            $res = $this->store->sparqlQuery('PREFIX foaf:<'.DSSN_FOAF_NS.'> SELECT * FROM <'.$ow->selectedModel.'> WHERE {<'.$this->uri.'> foaf:knows ?f}');
            foreach ($res as $ares){
               $friends[$ares['f']] = array('uri'=>$ares['f']); 
            }
        } else if ($mode == self::BASIC){
            $res = $this->store->sparqlQuery('PREFIX foaf:<'.DSSN_FOAF_NS.'> 
                SELECT * FROM <'.$this->ow->selectedModel.'> WHERE {
                    <'.$this->uri.'> foaf:knows ?f .
                        ?f foaf:name ?name .
                        ?f foaf:birthday ?birthday .
                        ?f foaf:depiction ?depiction
                    }');
            foreach ($res as $ares){
               $friends[$ares['f']] = array(
                   'uri'=>$ares['f'],
                   'name' => $ares['name'],
                   'birthday' => $ares['birthday'],
                   'depiction' => $ares['depiction']
               );
            }
        } else if ($mode == self::FULL){
            $res = $this->store->sparqlQuery('PREFIX foaf:<'.DSSN_FOAF_NS.'> SELECT * FROM <'.$ow->selectedModel.'> WHERE {
                <'.$this->uri.'> foaf:knows ?f
                    ?f ?p ?o}');
            foreach ($res as $ares){
               //TODO
            }
        } else if ($mode == self::TRANSITIVE){
            //TODO maybe use getTransitiveClosure method...
            $res = $this->store->sparqlQuery('PREFIX foaf:<'.DSSN_FOAF_NS.'> SELECT * FROM <'.$ow->selectedModel.'> WHERE {
                <'.$this->uri.'> foaf:knows ?f}');
            foreach ($res as $ares){
               $friends[] = new DSSN_Foaf_Person($ares['f'], false, $mode, self::TRANSITIVE, $depth -1);
            }
        }
        return $friends;
    }
}