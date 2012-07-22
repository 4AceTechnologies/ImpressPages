<?php
/**
 * @package   ImpressPages
 * @copyright Copyright (C) 2011 ImpressPages LTD.
 * @license   GNU/GPL, see ip_license.html
 */

namespace Library\Php\Js;

/**
 * replaces special characters in a string
 * @package Library
 */
class Functions
{
    public static function htmlToString($html) {
        return str_replace('script',"scr' + 'ipt", str_replace("\r", "", str_replace("\n", "\\n' + \n '", str_replace("'", "\\'",str_replace("\\", "\\\\",$html)))));
    }
}