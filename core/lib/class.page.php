<?php
class Page
{
var $vars = array();
    function set($var, $value)
    {
        $this->vars[$var] = $value;
    }
    
    function add($var, $value)
    {
        $this->vars[$var] .= $value;
    }

    function get($var)
    {
        return $this->vars[$var];
    }
}
?>