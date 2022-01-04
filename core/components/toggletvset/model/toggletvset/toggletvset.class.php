<?php
/**
 * ToggleTVSet
 *
 * Copyright 2015 by Patrick Percy Blank <info@pepebe.de>
 * Copyright 2015-2019 by Thomas Jakobi <thomas.jakobi@partout.info>
 * Modifications:
 * - Emmanuel Podvin - Intersel - 20190901 -
 *      add modx parsing on the input options values of the Toggle TV
 *      in order to prevent problem described in https://github.com/Jako/ToggleTVSet/issues/5
 * - Emmanuel Podvin - Intersel - 20210104
 *      Allow to have several toggleTVs in the same template/resource
 *
 * @package toggletvset
 * @subpackage classfile
 */

/**
 * Class ToggleTVSet
 */
class ToggleTVSet
{
    /**
     * A reference to the modX instance
     * @var modX $modx
     */
    public $modx;

    /**
     * The namespace
     * @var string $namespace
     */
    public $namespace = 'toggletvset';

    /**
     * The package name
     * @var string $packageName
     */
    public $packageName = 'ToggleTVSet';

    /**
     * The version
     * @var string $version
     */
    public $version = '1.2.6';

    /**
     * The class options
     * @var array $options
     */
    public $options = array();

		private $ready = false;

    /**
     * ToggleTVSet constructor
     *
     * @param modX $modx A reference to the modX instance.
     * @param array $options An array of options. Optional.
     */
    public function __construct(modX &$modx, $options = array())
    {
        $this->modx = &$modx;
        $this->namespace = $this->getOption('namespace', $options, $this->namespace);

        $corePath = $this->getOption('core_path', $options, $this->modx->getOption('core_path') . 'components/' . $this->namespace . '/');
        $assetsPath = $this->getOption('assets_path', $options, $this->modx->getOption('assets_path') . 'components/' . $this->namespace . '/');
        $assetsUrl = $this->getOption('assets_url', $options, $this->modx->getOption('assets_url') . 'components/' . $this->namespace . '/');

        // Load some default paths for easier management
        $this->options = array_merge(array(
            'namespace' => $this->namespace,
            'version' => $this->version,
            'corePath' => $corePath,
            'modelPath' => $corePath . 'model/',
            'vendorPath' => $corePath . 'vendor/',
            'chunksPath' => $corePath . 'elements/chunks/',
            'pagesPath' => $corePath . 'elements/pages/',
            'snippetsPath' => $corePath . 'elements/snippets/',
            'pluginsPath' => $corePath . 'elements/plugins/',
            'controllersPath' => $corePath . 'controllers/',
            'processorsPath' => $corePath . 'processors/',
            'templatesPath' => $corePath . 'templates/',
            'assetsPath' => $assetsPath,
            'assetsUrl' => $assetsUrl,
            'jsUrl' => $assetsUrl . 'js/',
            'cssUrl' => $assetsUrl . 'css/',
            'imagesUrl' => $assetsUrl . 'images/',
            'connectorUrl' => $assetsUrl . 'connector.php'
        ), $options);

        // Set default options
        $toggletvs = $this->getOption('toggletvs');
        $this->options = array_merge($this->options, array(
            'debug' => (boolean)$this->getOption('debug'),
            'toggletvs' => ($toggletvs) ? array_map('trim', explode(',', $toggletvs)) : array()
        ));

        $hidetvs = array();
        $showtvs = array();
				$showOptionTvs = array();

				if (empty($this->modx->resource)) {
					return;
				}

        foreach ($this->options['toggletvs'] as $toggletv) {
            $toggletv = intval($toggletv);
            $hidetvs[$toggletv] = array();
            $showtvs[$toggletv] = array();

            $tv = $this->modx->getObject('modTemplateVar', $toggletv);

						// Is our toggle tv is set for the template of our ressource?
						$tvt = $this->modx->getObject('modTemplateVarTemplate', array(
											'tmplvarid' => $toggletv,
											'templateid' => $this->modx->resource->get('template')
						));
						if (empty($tvt))
						{
							continue;//the toggle TV is not in the template of the ressource => should do nothing...
						}

						$this->ready = true;//ok

            if ($tv) {
                $elements = $tv->get('elements');
                $elements = explode('||', $elements);

                foreach ($elements as $element) {
		    						$tvlist="";
                    $element = explode('==', $element);
                    if (isset($element[1])) {
												$tvlist = $element[1];
												if (strpos($tvlist,'[[') !== false) //parse modx tags if any
												{
													$uniqid = uniqid();
													$chunk = $this->modx->newObject('modChunk', array('name' => "{tmp}-{$uniqid}"));
													$chunk->setCacheable(false);
													$tvlist = $chunk->process(array(), $tvlist);
													$this->modx->getParser();
													/*parse all non-cacheable tags and remove unprocessed tags - if you want to parse cacheable tags set 3 param as false*/
													$this->modx->parser->processElementTags('', $tvlist, true, true, '[[', ']]', array(), 0);
												}
                        if (!empty($tvlist))
                          $hidetvs[$toggletv] = array_merge($hidetvs[$toggletv], array_map('trim', explode(',', $tvlist)));
                    }
		    						$showOptionTvs[$toggletv][] = $tvlist;
                }
                //$hidetvs = array_values(array_unique($hidetv));
                $hidetvs[$toggletv] = array_values(array_unique($hidetvs[$toggletv]));
                if ($this->modx->resource) {

                    $tvr = $this->modx->getObject('modTemplateVarResource', array(
                        'tmplvarid' => $toggletv,
                        'contentid' => $this->modx->resource->get('id')
                    ));
                    if ($tvr) {
                        $tvvalue = $tvr->get('value');
                    } else {
                        $tv = $this->modx->getObject('modTemplateVar', $toggletv);
                        $tvvalue = ($tv) ? $tv->get('default_text') : '';
                    }
                    if ($tvvalue) {
                        if (strpos($tvvalue, '[[') !== false)
                        {
                                $uniqid = uniqid();
																/** @var modChunk $chunk */
                                $chunk = $this->modx->newObject('modChunk', array('name' => "{tmp}-{$uniqid}"));
                                $chunk->setCacheable(false);
                                $tvvalue = $chunk->process(array(), $tvvalue);
                                $parser = $this->modx->getParser();
                                /*parse all non-cacheable tags and remove unprocessed tags - if you want to parse cacheable tags set 3 param as false*/
                                $parser->processElementTags('', $tvvalue, true, true, '[[', ']]', array(), 0);
                        }

                        $showtvs[$toggletv] = array_merge($showtvs[$toggletv], array_map('trim', explode(',', $tvvalue)));
                    }
                }
                //$showtvs = array_values(array_unique($showtvs));
                $showtvs[$toggletv] = array_values(array_unique($showtvs[$toggletv]));
            }
        }

        $this->options['hidetvs'] = $hidetvs;
        $this->options['showtvs'] = $showtvs;
        $this->options['showOptionTvs'] = $showOptionTvs;

        $this->modx->lexicon->load($this->namespace . ':default');
    }

    /**
     * Get a local configuration option or a namespaced system setting by key.
     *
     * @param string $key The option key to search for.
     * @param array $options An array of options that override local options.
     * @param mixed $default The default value returned if the option is not found locally or as a
     * namespaced system setting; by default this value is null.
     * @return mixed The option value or the default value specified.
     */
    public function getOption($key, $options = array(), $default = null)
    {
        $option = $default;
        if (!empty($key) && is_string($key)) {
            if ($options != null && array_key_exists($key, $options)) {
                $option = $options[$key];
            } elseif (array_key_exists($key, $this->options)) {
                $option = $this->options[$key];
            } elseif (array_key_exists("{$this->namespace}.{$key}", $this->modx->config)) {
                $option = $this->modx->getOption("{$this->namespace}.{$key}");
            }
        }
        return $option;
    }

    /**
     * Gets a context-aware setting through $this->getOption, and casts the value to a true boolean automatically,
     * including strings "false" and "no" which are sometimes set that way by ExtJS.
     *
     * @param string $name
     * @param array $options
     * @param bool $default
     * @return bool
     */
    public function getBooleanOption($name, array $options = null, $default = null)
    {
        $option = $this->getOption($name, $options, $default);
        return $this->castValueToBool($option);
    }

    /**
     * Turns a value into a boolean. This checks for "false" and "no" strings, as well as anything PHP can automatically
     * cast to a boolean value.
     *
     * @param $value
     * @return bool
     */
    public function castValueToBool($value)
    {
        if (in_array(strtolower($value), array('false', 'no'))) {
            return false;
        }
        return (bool)$value;
    }

/**
 * returns if we can include the script...
 * @return boolean if true it's ok to install the script
 */
		public function ready()
		{
			return $this->ready;
		}

    /**
     * Register javascripts in the controller
     */
    public function includeScriptAssets()
    {
        $assetsUrl = $this->getOption('assetsUrl');
        $jsUrl = $this->getOption('jsUrl') . 'mgr/';
        $jsSourceUrl = $assetsUrl . '../../../source/js/mgr/';

        if ($this->getOption('debug') && $assetsUrl != MODX_ASSETS_URL . 'components/toggletvset/') {
            $this->modx->controller->addLastJavascript($jsSourceUrl . 'toggletvset.js?v=v' . $this->version);
        } else {
            $this->modx->controller->addLastJavascript($jsUrl . 'toggletvset.js?v=v' . $this->version);
        }
				$this->modx->controller->addHtml('<script type="text/javascript">' .
            'var ToggleTVSet = ' . json_encode(array('options' => array(
                'debug' => $this->getOption('debug'),
                'toggleTVs' => $this->getOption('toggletvs'),
                'toggleTVsClearHidden' => $this->getBooleanOption('toggletvs_clearhidden'),
                'hideTVs' => $this->getOption('hidetvs'),
                'showTVs' => $this->getOption('showtvs'),
								'showOptionTvs' => $this->getOption('showOptionTvs')
            )), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . ';' . '</script>');
    }
}
