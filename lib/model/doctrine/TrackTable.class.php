<?php

/**
 * TrackTable
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class TrackTable extends Doctrine_Table
{
    /**
     * Returns an instance of this class.
     *
     * @return object TrackTable
     */
    public static function getInstance()
    {
        return Doctrine_Core::getTable('Track');
    }
    
    public function incrementUrl($url, $increment = 1) {
      $numrows = Doctrine_Query::create()
        ->update('Track')
        ->set('hits', 'hits + ?', $increment)
        ->where('url = ?', $url)
        ->execute();
      if($numrows == 0) {
        //the above update did nothing, therefore we need to create a new record.
        $t = new Track();
        $t->setUrl($url);
        $t->setHits($increment);
        $t->save();
      }
    }
    
    public function getHitsByUrl($url) {
      $h = Doctrine_Query::create()
      ->select('t.hits as hits')
      ->from('Track t')
      ->where('t.url = ?', $url)
      ->setHydrationMode(Doctrine_Core::HYDRATE_SINGLE_SCALAR)
      ->execute();
      
      if(!$h) $h = 0; //in case not yet created
      
      return $h;
    }
}
