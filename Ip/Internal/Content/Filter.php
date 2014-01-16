<?php


namespace Ip\Internal\Content;


class Filter
{
    public static function ipWidgets($widgets)
    {

        $widgets['Title'] = new \Ip\Internal\Content\Widget\Title\Controller('Title', 'Content', 1);
        $widgets['Text'] = new \Ip\Internal\Content\Widget\Text\Controller('Text', 'Content', 1);
        $widgets['Separator'] = new \Ip\Internal\Content\Widget\Separator\Controller('Separator', 'Content', 1);
        //$widgets['TextImage'] = new \Ip\Internal\Content\Widget\TextImage\Controller('TextImage', 'Content', 1);
        $widgets['Image'] = new \Ip\Internal\Content\Widget\Image\Controller('Image', 'Content', 1);
        $widgets['Gallery'] = new \Ip\Internal\Content\Widget\Gallery\Controller('Gallery', 'Content', 1);
        //$widgets['IpLogoGallery'] = new \Ip\Internal\Content\Widget\IpLogoGallery\Controller('IpLogoGallery', 'Content', 1);
        //$widgets['File'] = new \Ip\Internal\Content\Widget\File\Controller('File', 'Content', 1);
        $widgets['Html'] = new \Ip\Internal\Content\Widget\Html\Controller('Html', 'Content', 1);
        //$widgets['Faq'] = new \Ip\Internal\Content\Widget\Faq\Controller('Faq', 'Content', 1);
        $widgets['Columns'] = new \Ip\Internal\Content\Widget\Columns\Controller('Columns', 'Content', 1);
        $widgets['Form'] = new \Ip\Internal\Content\Widget\Form\Controller('Form', 'Content', 1);


        $widgetDirs = static::getPluginWidgetDirs();
        foreach ($widgetDirs as $widgetDirRecord) {
            $widgetKey = $widgetDirRecord['widgetKey'];
            $widgetClass = '\\Plugin\\' . $widgetDirRecord['module'] . '\\' . Model::WIDGET_DIR . '\\' . $widgetKey . '\\Controller';
            if (class_exists($widgetClass)) {
                $widget = new $widgetClass($widgetKey, $widgetDirRecord['module'], 0);
            } else {
                $widget = new \Ip\WidgetController($widgetKey, $widgetDirRecord['module'], 0);
            }
            $widgets[$widgetDirRecord['widgetKey']] = $widget;
        }
        return $widgets;
    }

    /**
     * Form widget
     * @param array $value
     */
    public static function ipWidgetFormFieldTypes($fieldTypes, $info = null)
    {

        $typeText = __('Text', 'ipAdmin', false);
        $typeEmail = __('Email', 'ipAdmin', false);
        $typeTextarea = __('Textarea', 'ipAdmin', false);
        $typeSelect = __('Select', 'ipAdmin', false);
        $typeCheckbox = __('Checkbox', 'ipAdmin', false);
        $typeRadio = __('Radio', 'ipAdmin', false);
        $typeCaptcha = __('Captcha', 'ipAdmin', false);
        $typeFile = __('File', 'ipAdmin', false);

        $fieldTypes['Text'] = new FieldType('Text', '\Ip\Form\Field\Text', $typeText);
        $fieldTypes['IpEmail'] = new FieldType('IpEmail', '\Ip\Form\Field\Email', $typeEmail);
        $fieldTypes['Textarea'] = new FieldType('Textarea', '\Ip\Form\Field\Textarea', $typeTextarea);
        $fieldTypes['IpSelect'] = new FieldType('IpSelect', '\Ip\Form\Field\Select', $typeSelect, 'ipWidgetForm_InitListOptions', 'ipWidgetForm_SaveListOptions', ipView(
            'view/form_field_options/list.php'
        )->render());
        $fieldTypes['IpCheckbox'] = new FieldType('IpCheckbox', '\Ip\Form\Field\Checkbox', $typeCheckbox, 'ipWidgetForm_InitWysiwygOptions', 'ipWidgetForm_SaveWysiwygOptions', ipView(
            'view/form_field_options/wysiwyg.php'
        )->render());
        $fieldTypes['IpRadio'] = new FieldType('IpRadio', '\Ip\Form\Field\Radio', $typeRadio, 'ipWidgetForm_InitListOptions', 'ipWidgetForm_SaveListOptions', ipView(
            'view/form_field_options/list.php'
        )->render());
        $fieldTypes['IpCaptcha'] = new FieldType('IpCaptcha', '\Ip\Form\Field\Captcha', $typeCaptcha);
        $fieldTypes['File'] = new FieldType('File', '\Ip\Form\Field\File', $typeFile);

        return $fieldTypes;
    }


    private static function getPluginWidgetDirs()
    {
        $answer = array();
        $plugins = \Ip\Internal\Plugins\Model::getActivePlugins();
        foreach ($plugins as $plugin) {
            $answer = array_merge($answer, static::findPluginWidgets($plugin));
        }
        return $answer;
    }

    private static function findPluginWidgets($moduleName)
    {
        $widgetDir = ipFile('Plugin/' . $moduleName . '/' . Model::WIDGET_DIR . '/');

        if (!is_dir($widgetDir)) {
            return array();
        }
        $widgetFolders = scandir($widgetDir);
        if ($widgetFolders === false) {
            return array();
        }

        $answer = array();
        //foreach all widget folders
        foreach ($widgetFolders as $widgetFolder) {
            //each directory is a widget
            if (!is_dir($widgetDir . $widgetFolder) || $widgetFolder == '.' || $widgetFolder == '..') {
                continue;
            }
            if (isset ($answer[(string)$widgetFolder])) {
                ipLog()->warning(
                    'Content.duplicateWidget: {widget}',
                    array('plugin' => 'Content', 'widget' => $widgetFolder)
                );
            }
            $answer[] = array(
                'module' => $moduleName,
                'dir' => $widgetDir . $widgetFolder . '/',
                'widgetKey' => $widgetFolder
            );
        }
        return $answer;
    }


}