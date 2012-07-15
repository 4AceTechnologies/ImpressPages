<?php
/**
 * @package ImpressPages
 * @copyright   Copyright (C) 2011 ImpressPages LTD.
 * @license see ip_license.html
 */

namespace Modules\community\newsletter;

if (!defined('FRONTEND')&&!defined('BACKEND')) exit;

require_once (__DIR__.'/db.php');
require_once (BASE_DIR.MODULE_DIR.'administrator/email_queue/module.php');


class Actions {

  public static function makeActions($zoneName) {
    global $site;
    global $parametersMod;
    global $log;

    $newsletterZone = $site->getZoneByModule('community', 'newsletter');

    if(!$newsletterZone)
      return;

    if (isset($_REQUEST['action'])) {
      switch ($_REQUEST['action']) {
     
        case 'cancel': //unsubscribe through e-mail link
          if (isset($_REQUEST['id']) && isset($_REQUEST['code'])) {
            $record = DB::getSubscriber($_REQUEST['id']);
            $log->log('community/newsletter', 'Unsubscribe (e-mail link)', $record['email']);

            Db::unsubscribe($_REQUEST['email'], $site->currentLanguage['id'], $_REQUEST['id'], $_REQUEST['code']);
            header('location: '.$site->generateUrl(null, $newsletterZone->getName(), array("unsubscribed"), array()));

            \Db::disconnect();
            exit;
          }
          break;
        case 'conf':
          if (isset($_GET['id']) && isset($_GET['code'])) {

            if(Db::confirm($_GET['id'],  $_GET['code'], $site->currentLanguage['id'])) {
              header('location: '.$site->generateUrl(null, $newsletterZone->getName(), array("subscribed"), array()));
              $record = DB::getSubscriber($_GET['id']);
              $log->log('community/newsletter', 'Confirm subscribtion', $record['email']);
            } else {
              header('location: '.$site->generateUrl(null, $newsletterZone->getName(), array("error_confirmation"), array()));
              $log->log('community/newsletter', 'Incorrect confirmation link', $_GET['id'].' '.$_GET['code']);
            }
          }
          break;
        case 'get_link':
          if (isset($_REQUEST['page'])) {
            switch ($_REQUEST['page']) {
              case 'error_confirmation':
                echo $site->generateUrl(null, $zoneName, array("error_confirmation"));
                break;
              case 'email_confirmation':
                echo $site->generateUrl(null, $zoneName, array("email_confirmation"));
                break;
              case 'subscribed':
                echo $site->generateUrl(null, $zoneName, array("subscribed"));
                break;
              case 'incorrect_email':
                echo $site->generateUrl(null, $zoneName, array("incorrect_email"));
                break;
              case 'unsubscribed':
                echo $site->generateUrl(null, $zoneName, array("unsubscribed"));
                break;
            }
          }
          \Db::disconnect();
          exit;
          break;
      }

    }
  }
}



