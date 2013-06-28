<?php
/**
 * @package ImpressPages
 *
 *
 */

namespace Modules\community\newsletter;
if (!defined('BACKEND')) exit;


require_once (__DIR__.'/db.php');
require_once (BASE_DIR.MODULE_DIR.'administrator/email_queue/module.php');

class BackendWorker {

    function work() {
        if(isset($_GET['action']) && $_GET['action'] == 'preview' && isset($_GET['record_id'])) {
            $this->preview($_GET['record_id']);
        }

        if(isset($_GET['action']) && $_GET['action'] == 'send' && isset($_GET['record_id']) ) {
            $this->send($_GET['record_id']);
        }

        if(isset($_GET['action']) && $_GET['action'] == 'test' && isset($_GET['record_id'])) {
            $this->testSend($_GET['record_id'], $_GET['email']);
        }

        if(isset($_GET['action']) && $_GET['action'] == 'get_template' && isset($_GET['record_id'])) {
            $this->getTemplate($_GET['record_id']);
        }

        if(isset($_GET['action']) && $_GET['action'] == 'test_form' && isset($_GET['record_id'])) {
            $this->testForm($_GET['record_id']);
        }
    }

    function preview($recordId) {
        global $parametersMod;
        global $cms;
        $record = Db::getRecord($recordId);
        header('Content-Type: text/html; charset='.CHARSET.'');
        $html = '
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<link rel="SHORTCUT ICON" href="favicon.ico" />
</head>
<frameset rows="64px,*" framespacing="0" border="0">
 <frame name="header" noresize="noresize" frameborder=0 scrolling="no" src="'.$cms->generateWorkerUrl(null, '&action=test_form&record_id='.$recordId).'">
 <frame id="frameContent" name="content" frameborder=0 src="'.$cms->generateWorkerUrl(null, '&action=get_template&record_id='.$recordId).'">
 <noframes>
  <body>Your browser don\'t support frames!</body>
 </noframes>
</frameset>
</html>		
';


        echo $html;

    }

    function testForm($recordId) {
        global $parametersMod;
        global $cms;
        $record = Db::getRecord($recordId);
        $html = '
		<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">
			<head>
				<title>'.htmlspecialchars($record['subject']).'</title>
				<meta http-equiv="Content-Type" content="text/html; charset='.CHARSET.'" />
			</head>
			<body style="margin:0; padding: 0;">
				<div style="padding: 20px; background-color: white;">
					<form action="" onsubmit="return false;"><div style="font-family: Verdana; font-size: 12px; color: black;">
						'.htmlspecialchars($parametersMod->getValue('community', 'newsletter', 'admin_translations', 'where_to_send')).' <input id="input_email" style="border: 1px solid silver;" type="text" /> 
						<span style="cursor: pointer; padding: 2px; border: 1px solid silver; background-color: #eeeeee;"
						onclick="send_test_email(document.getElementById(\'input_email\').value)"
						>'.htmlspecialchars($parametersMod->getValue('community', 'newsletter', 'admin_translations', 'send')).'</span></div></form>
				</div>
				<script type="text/javascript" src="'.BASE_URL.LIBRARY_DIR.'js/default.js"></script>
				<script type="text/javascript">
					function send_test_email(email){
						if(email == \'\')
							return;
            LibDefault.ajaxMessage(\''.$cms->generateWorkerUrl($_GET['module_id'], 'record_id='.$_GET['record_id'].'&action=test&email=\' + email ').', \'\');
						
					}
				</script>
			</body>
		</html>';	
        echo $html;
    }

    function getTemplate($recordId) { //email message preview
        global $parametersMod;
        global $cms;
        global $site;

        $record = Db::getRecord($recordId);
        echo $this->prepareMessage($record, '', '');
    }

    function send($recordId) {
        global $parametersMod;
        global $site;

        $queue = new \Modules\administrator\email_queue\Module();
        $record = Db::getRecord($recordId);


        $subscribers = Db::getSubscribers($record['language_id']);
        $newsletterZone = $site->getZoneByModule('community', 'newsletter');

        if($subscribers) {
            foreach($subscribers as $key => $subscriber) {
                $unsubscriptionLink = $site->generateUrl($record['language_id'], $newsletterZone->getName(), null, array('action' => 'cancel', 'email' => $subscriber['email'], 'id' => $subscriber['id'], 'code' => $subscriber['verification_code']));
                $email = $this->prepareMessage($record, $subscriber['email'], $unsubscriptionLink);
                $queue->addEmail($parametersMod->getValue('standard', 'configuration', 'main_parameters', 'email', $record['language_id']), $parametersMod->getValue('standard', 'configuration', 'main_parameters', 'name', $record['language_id']), $subscriber['email'], '', $record['subject'], $email, false, true);
            }
        }
        $queue->send();

        echo "alert('".$parametersMod->getValue('community', 'newsletter', 'admin_translations', 'was_sent')."');";
    }

    function testSend($recordId, $testEmail) {
        global $parametersMod;
        global $site;

        $queue = new \Modules\administrator\email_queue\Module();
        $record = Db::getRecord($recordId);

        $email = $this->prepareMessage($record, $testEmail, $site->generateUrl($record['language_id']));

        $queue->addEmail($parametersMod->getValue('standard', 'configuration', 'main_parameters', 'email', $record['language_id']), $parametersMod->getValue('standard', 'configuration', 'main_parameters', 'name', $record['language_id']), $testEmail, '', $record['subject'], $email, true, true);
        $queue->send();

        echo "alert('".$parametersMod->getValue('community', 'newsletter', 'admin_translations', 'test_email_send')."');";
    }

    function prepareMessage($record, $receiverEmail, $unsubscriptionLink) {
        global $parametersMod;
        global $site;
        global $cms;


        $site->requireTemplate('community/newsletter/template.php');
        $email = Template::newsletterTemplate($record['language_id'], $record['text'], $unsubscriptionLink);

        return $email;
    }

}



