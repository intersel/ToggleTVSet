<?php
/**
 * Output filter that retrieves the label of a corresponding TV value
 *
 * @package toggletvset
 * @subpackage snippet
 *
 * @var modX $modx
 * @var string $input
 * @var string $options
 */

// Output filter options
$name = (!empty($options)) ? str_replace($options, '', $name) : $name;
$tv = $modx->getObject('modTemplateVar', array('name' => $name));

$elements = (!empty($tv)) ? explode('||', $tv->get('elements')) : array();

$output = '';
foreach ($elements as $key => $element) {
    $element = explode('==', $element);

    if ($element[1] == $input) {
        $output = $element[0];
        break;
    }
}

return $output;
