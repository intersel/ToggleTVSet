<?php
/**
 * ToggleTVSet Runtime Hooks
 *
 * @package toggletvset
 * @subpackage plugin
 *
 * @event OnDocFormPreRender
 *
 * @var modX $modx
 */

$eventName = $modx->event->name;

$corePath = $modx->getOption('toggletvset.core_path', null, $modx->getOption('core_path') . 'components/toggletvset/');
/** @var ToggleTVSet $toggletvset */
$toggletvset = $modx->getService('toggletvset', 'ToggleTVSet', $corePath . 'model/toggletvset/', array(
    'core_path' => $corePath
));

switch ($eventName) {
    case 'OnDocFormPrerender':
        $toggletvset->includeScriptAssets();
        break;
}
