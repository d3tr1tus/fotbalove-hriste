<?php

/**
 * Description of AdminForm
 *
 * @author Renat Magadiev <magadievr@jelly.cz>
 */
class AdminForm extends Nette\Application\UI\Form {

  public function __construct($parent = NULL, $name = NULL) {
    parent::__construct($parent, $name);
    
    $this->getElementPrototype()->role = 'form';
    
    //RENDERER
    $renderer = $this->getRenderer();
    $renderer->wrappers['controls']['container'] = 'div class="form-body"';
    $renderer->wrappers['pair']['container'] = 'div class="form-group"';
    $renderer->wrappers['label']['container'] = NULL;
    $renderer->wrappers['control']['container'] = 'div class="input-group"';
    $renderer->wrappers['control']['.submit'] = 'btn primary';
    $renderer->wrappers['control']['.text'] = 'form-control';
    $renderer->wrappers['control']['.password'] = 'form-control';
    $renderer->wrappers['control']['.select'] = 'form-control';
    //dump($renderer->wrappers);exit;
  }

}
