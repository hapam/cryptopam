<?php
function smarty_modifier_print($string)
{
    echo '<div align="left"><pre>';
    var_dump($string);
    echo '</pre></div>';
}

?>
