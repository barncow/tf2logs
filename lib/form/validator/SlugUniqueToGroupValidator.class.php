<?php
class SlugUniqueToGroupValidator extends sfValidatorBase {
 
  protected function configure($options = array(), $messages = array()) {
    $this->addMessage('unavailable', 'The server URL is already in use for this group.');
    $this->addOption('throw_global_error', false);
    $this->addOption('group_slug_value', false);
    $this->addOption('server_slug_key', false);
    $this->addOption('old_server_slug_value', false);
  }
 
  protected function doClean($values) {
    if (null === $values) {
      $values = array();
    }
    
    if (!is_array($values)) {
      throw new InvalidArgumentException('You must pass an array parameter to the clean() method');
    }

    if ($this->getOption('group_slug_value')) {
      $group_slug = $this->getOption('group_slug_value');
    } else if(isset($values['server_group_id'])) {
      $group_slug = Doctrine::getTable('ServerGroup')->getSlugByGroupId($values['server_group_id']);
    } else {
      $group_slug  = isset($values['slug']) ? $values['slug'] : null;
    }
    
    
    
    $server_slug_key = 'slug';
    if ($this->getOption('server_slug_key')) {
      $server_slug_key = $this->getOption('server_slug_key');
    }
    $server_slug = isset($values[$server_slug_key]) ? $values[$server_slug_key] : null; 
    
    $old_server_slug_value = false;
    if ($this->getOption('old_server_slug_value')) {
      $old_server_slug_value = $this->getOption('old_server_slug_value');
    }
    
    if ($group_slug && $server_slug && $server_slug != $old_server_slug_value && Doctrine::getTable('ServerGroup')->isServerSlugUsedInGroup($group_slug, $server_slug)) {
      $error = new sfValidatorError($this, 'unavailable', array(
        'slug'  => $group_slug,
        'server_slug' => $server_slug
      ));
      if ($this->getOption('throw_global_error')) {
        throw $error;
      }

      throw new sfValidatorErrorSchema($this, array('server_slug' => $error));
    }
    
    return $values;
  }
}
