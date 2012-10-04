<?php
require_once PHPANVIL2_COMPONENT_PATH . 'anvilObject.abstract.php';

/**
 * Page BODY Class
 *
 * @copyright       Copyright (c) 2012 Nick Slevkoff (http://www.slevkoff.com)
 */
class anvilHTMLResponseBody extends anvilObjectAbstract
{
    //---- Render Properties -------------------------------------------------
    public $class = '';

    //---- Private Properties --------------------------------------------------

    public function __construct()
    {
//        $this->enableLog();
        parent::__construct();
    }

    public function render()
    {
//        $this->_logDebug($this);

//        $this->_logDebug('rendering anvilPageHead....');


        if (!empty($this->base)) {
            $this->html .= $this->prefix . '<base href="' . $this->base . '" />' . "\n";
        }

        if (!empty($this->title)) {
            $this->html .= $this->prefix . '<title>' . $this->title . '</title>' . "\n";
        }

        //---- HTTP ------------------------------------------------------------
        if (!empty($this->contentLanguage)) {
            $this->html .= $this->prefix . '<meta http-equiv="CONTENT-LANGUAGE" content="' . $this->contentLanguage . '" />' . "\n";
        }
        if (!empty($this->contentType)) {
            $this->html .= $this->prefix . '<meta http-equiv="CONTENT-TYPE" content="' . $this->contentType . '" />' . "\n";
        }
        if (!empty($this->cache)) {
            $this->html .= $this->prefix . '<meta http-equiv="CACHE-CONTROL" content="' . $this->cache . '" />' . "\n";
        }
        if (!empty($this->pragma)) {
            $this->html .= $this->prefix . '<meta http-equiv="PRAGMA" content="' . $this->pragma . '" />' . "\n";
        }
        if (!empty($this->expires)) {
            $this->html .= $this->prefix . '<meta http-equiv="EXPIRES" content="' . $this->expires . '" />' . "\n";
        }
        if (!empty($this->refresh)) {
            $this->html .= $this->prefix . '<meta http-equiv="REFRESH" content="' . $this->refresh . '" />' . "\n";
        }

        //---- Meta ------------------------------------------------------------
        if (!empty($this->description)) {
            $this->html .= $this->prefix . '<meta name="DESCRIPTION" content="' . $this->description . '" />' . "\n";
        }
        if (!empty($this->keywords)) {
            $this->html .= $this->prefix . '<meta name="KEYWORDS" content="' . $this->keywords . '" />' . "\n";
        }
        if (!empty($this->robots)) {
            $this->html .= $this->prefix . '<meta name="ROBOTS" content="' . $this->robots . '" />' . "\n";
        }

        if (!empty($this->author)) {
            $this->html .= $this->prefix . '<meta name="AUTHOR" content="' . $this->author . '" />' . "\n";
        }
        if (!empty($this->copyright)) {
            $this->html .= $this->prefix . '<meta name="COPYRIGHT" content="' . $this->copyright . '" />' . "\n";
        }
        if (!empty($this->generator)) {
            $this->html .= $this->prefix . '<meta name="GENERATOR" content="' . $this->generator . '" />' . "\n";
        }
        if (!empty($this->revised)) {
            $this->html .= $this->prefix . '<meta name="REVISED" content="' . $this->revised . '" />' . "\n";
        }
        $this->html .= $this->meta . "\n";


        //---- Icon Links ------------------------------------------------------
        if (!empty($this->icon)) {
            $this->html .= $this->prefix . '<link rel="icon" type="'. $this->iconType . '" href="' . $this->icon . '" />' . "\n";
        }

        if (!empty($this->shortcutIcon)) {
            $this->html .= $this->prefix . '<link rel="shortcut icon" type="'. $this->shortcutIconType . '" href="' . $this->shortcutIcon . '" />' . "\n";
        } elseif (!empty($this->icon)) {
            $this->html .= $this->prefix . '<link rel="shortcut icon" type="'. $this->iconType . '" href="' . $this->icon . '" />' . "\n";
        }

        if (!empty($this->meta)) {
            $this->html .= $this->meta . "\n\n";
        }
        if (!empty($this->stylesheets)) {
            $this->html .= $this->stylesheets . "\n\n";
        }
        if (!empty($this->styles)) {
//            $this->_logDebug('rendering styles....');

            $this->html .= '<style>' . "\n";
            $this->html .= $this->styles . "\n";
            $this->html .= '</style>' . "\n\n";
        }
        if (!empty($this->scripts)) {
            $this->html .= $this->scripts . "\n\n";
        }

        return $this->html;
    }
}

?>