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

    $value = $element[1];
    
	if (strpos($value,'[[') !== false)
	{
		$uniqid = uniqid();
		$chunk = $modx->newObject('modChunk', array('name' => "{tmp}-{$uniqid}"));
		$chunk->setCacheable(false);
		$value = $chunk->process(array(), $value);
		$modx->getParser();
		/*parse all non-cacheable tags and remove unprocessed tags - if you want to parse cacheable tags set 3 param as false*/
		$modx->parser->processElementTags('', $value, true, true, '[[', ']]', array(), 0);
	}


    if ($value == $input) {
        $output = $element[0];
        break;
    }
}

return $output;
