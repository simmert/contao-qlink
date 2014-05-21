<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2014 Leo Feyer
 *
 * @package Qlink
 * @link    https://contao.org
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */


/**
 * Register the namespaces
 */
ClassLoader::addNamespaces(array
(
	'QLink',
));


/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
	// Elements
	'QLink\ContentQLink' => 'system/modules/qlink/elements/ContentQLink.php',
));


/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
	'ce_qlink' => 'system/modules/qlink/templates/elements',
));
