<?php

    namespace PHPPgAdmin\XHtml;

    /**
     *  XHtmlSimpleElement
     *
     *  Used to generate Xhtml-Code for simple xhtml elements
     *  (i.e. elements, that can't contain child elements)
     *
     *
     * @author    Felix Meinhold
     *
     */
    class XHtmlSimpleElement
    {
        public $_element;
        public $_siblings   = [];
        public $_htmlcode;
        public $_attributes = [];

        /**
         * Constructor
         *
         * @param    string    The element's name. Defaults to name of the
         *                     derived class
         *
         */
        public function __construct($element = null)
        {

            $this->_element = $this->is_element();
        }

        public function is_element()
        {
            return
                str_replace('phppgadmin\xhtml\xhtml_', '', strtolower(get_class($this)));
        }

        public function set_style($style)
        {
            $this->set_attribute('style', $style);
        }

        public function set_attribute($attr, $value)
        {
            $this->_attributes[$attr] = $value;
        }

        public function set_class($class)
        {
            $this->set_attribute('class', $class);
        }

        /**
         * Echoes xhtml
         *
         */
        public function show()
        {
            echo $this->fetch();
        }

        /**
         * Returns xhtml code
         *
         */
        public function fetch()
        {
            return $this->_html();
        }

        /**
         * Private function generates xhtml
         *
         * @access private
         */
        public function _html()
        {
            $this->_htmlcode = '<';
            foreach ($this->_attributeCollection as $attribute => $value) {
                if (!empty($value)) {
                    $this->_htmlcode .= " {$attribute}=\"{$value}\"";
                }
            }
            $this->_htmlcode .= '/>';

            return $this->_htmlcode;
        }

    }