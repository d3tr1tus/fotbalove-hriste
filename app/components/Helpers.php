<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Helpers
 *
 * @author jerabekd
 */
class Helpers {

  public static function hDate($date) {
    return Nette\Templating\Helpers::date($date, 'j. n. Y');
  }

  public static function hTime($date) {
    return Nette\Templating\Helpers::date($date, 'H:i');
  }

  public static function hDateTime($date) {
    return Nette\Templating\Helpers::date($date, 'j. n. Y  H:i');
  }

}
