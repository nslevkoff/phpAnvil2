<?php
require_once PHPANVIL2_COMPONENT_PATH . 'anvilObject.abstract.php';

/**
 * Page HEAD Class
 *
 * @copyright       Copyright (c) 2012 Nick Slevkoff (http://www.slevkoff.com)
 */
class anvilHTMLResponseHead extends anvilObjectAbstract
{
    //---- General Properties --------------------------------------------------
    public $base;
    public $icon;
    public $iconType = 'image/x-icon';
    public $shortcutIcon;
    public $shortcutIconType = 'image/x-icon';
    public $title;

    //---- HTTP Properties -----------------------------------------------------
    public $cache;
    public $contentLanguage = 'en-US';
    public $contentType = 'text/html; charset=UTF-8';
    public $expires;
    public $pragma;
    public $refresh;

    //---- Meta Properties -----------------------------------------------------
    public $author;
    public $copyright;
    public $description;
    public $generator = 'phpAnvil';
    public $keywords;
    public $meta;
    public $revised;
    public $robots;

    //---- Render Properties -------------------------------------------------
    public $html = '';
    public $prefix = "\t";
    public $scripts = '';
    public $styles = '';
    public $stylesheets = '';

    //---- Private Properties --------------------------------------------------

    public function __construct()
    {
        $this->enableLog();
        parent::__construct();
    }

    public function addScript($source = '', $script = '', $type = 'text/javascript')
    {
        $this->scripts .= '<script type="' . $type . '"';

        if (!empty($source)) {
            $this->scripts .= ' src="' . $source . '"';
        }
        $this->scripts .= '>';

        if (!empty($script)) {
            $this->scripts .=  "\n" . $script . "\n";
        }
        $this->scripts .= '</script>' . "\n";
    }


    public function addStyle($style)
    {
        $this->styles .= $style . "\n";
    }

    public function addStylesheet($stylesheet, $media = '')
    {
        $this->stylesheets .= '<link rel="stylesheet" type="text/css" href="' . $stylesheet . '"';

        if (!empty($media)) {
            $this->stylesheets .= ' media="' . $media . '"';
        }

        $this->stylesheets .= ' />' . "\n";
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