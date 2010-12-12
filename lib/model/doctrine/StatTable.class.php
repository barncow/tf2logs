<?php

/**
 * StatTable
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class StatTable extends Doctrine_Table
{
    /**
     * Returns an instance of this class.
     *
     * @return object StatTable
     */
    public static function getInstance()
    {
        return Doctrine_Core::getTable('Stat');
    }
    
    public function clearStats($log_id) {
      $this->createQuery('s')
        ->delete('Stat s')
        ->where('s.log_id = ?', $log_id)
        ->execute();
    }
}
