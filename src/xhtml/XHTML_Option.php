<?php

    namespace PHPPgAdmin\XHtml;

    class XHTML_Option extends XHtmlElement
    {

        public function __construct($text, $value = null)
        {

            parent::__construct(null);

            $this->set_text($text);
        }
    }
