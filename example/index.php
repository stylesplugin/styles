<?php



// Have a look!
echo '<pre>';
print_r($variables);

// Now to save them back as an XML file
//$scaffold->extensions['XMLVariables']->save($variables,'vars.xml');

// Now when you load this XML file into the CSS, they will override the
// default values in the @variable blocks. You can build a UI
// just from declaring CSS @variable block within a theme file 