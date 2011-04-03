<?php
class SlugUniqueToGroupValidator extends sfValidatorBase {
 
  protected function configure($options = array(), $messages = array()) {
    $this->addMessage('unavailable', 'The server URL is already in use for this group.');
    $this->addOption('throw_global_error', false);
  }
 
  protected function doClean($values) {
    if (null === $values) {
      $values = array();
    }
    
    if (!is_array($values)) {
      throw new InvalidArgumentException('You must pass an array parameter to the clean() method');
    }

    $group_slug  = isset($values['slug']) ? $values['slug'] : null;
    $server_slug = isset($values['server_slug']) ? $values['server_slug'] : null;    

    if ($group_slug && $server_slug && Doctrine::getTable('ServerGroup')->isServerSlugUsedInGroup($group_slug, $server_slug)) {
      $error = new sfValidatorError($this, 'unavailable', array(
        'slug'  => $slug,
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
