<?php
/**
 * @package ImpressPages
 *
 */

namespace Modules\administrator\theme;

if (!defined('BACKEND')) exit;

require_once(__DIR__.'/template.php');
require_once(__DIR__.'/model.php');

class Manager{
    function __construct(){

    }
    function manage(){
        global $parametersMod;
        $error = '';
        $message = '';
        if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'changeTheme' && isset($_REQUEST['themeName'])) {
            try {
                Model::installTheme($_REQUEST['themeName']);
            } catch (\Exception $e) {
                $error = $e->getMessage();
            }
            
            if (!$error) {
                $message = $parametersMod->getValue('administrator', 'theme', 'admin_translations', 'successful_install');
            }
        }
        
        
        $themes = Model::getAvailableThemes();
        
        $answer = Template::header();
        $answer.= Template::title();
        if ($error) {
            $answer.= Template::error($error);
        }
        if ($message) {
            $answer .= Template::message($message);
        }
        $answer.= Template::themes($themes);
        $answer.= Template::footer();
        
        return $answer;
         
    }


}
