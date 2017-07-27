<?php
function smarty_modifier_t($string)
{
    $args = func_get_args();
    $arrInput = array($string) + $args;
    if($string != ''){
        return call_user_func_array(array('Language', 'trans'), $arrInput);
    }
    return $string;
}

?>
