<?php
/**
 * Output filter that retrieves the names of TVs from a list of TV IDs
 *
 * @package toggletvset
 * @subpackage snippet
 *
 * @var modX $modx
 * @var string $input
 */

$tvIds = explode(',', $input);

$tvNames = array();
foreach ($tvIds as $tvId) {
    $tv = $modx->getObject('modTemplateVar', $tvId);
    if (!empty($tv)) {
        $tvNames[] = $tv->get('name');
    }
}

return implode(',', $tvNames);
