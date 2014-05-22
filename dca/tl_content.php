<?php

/**
 * Add palettes to tl_content
 */
$GLOBALS['TL_DCA']['tl_content']['palettes']['qlink_nav'] = '{type_legend},type,headline;{link_legend},qlink_pages;{expert_legend:hide},qlink_item_key,qlink_keep_query;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space;{invisible_legend:hide},invisible,start,stop';


/**
 * Add fields to tl_content
 */
$GLOBALS['TL_DCA']['tl_content']['fields']['qlink_pages'] = array
(
    'label'                   => &$GLOBALS['TL_LANG']['tl_content']['qlink_pages'],
    'exclude'                 => false,
    'inputType'               => 'pageTree',
    'foreignKey'              => 'tl_page.title',
    'eval'                    => array('mandatory'=>true, 'multiple'=>true, 'fieldType'=>'checkbox', 'tl_class'=>'long clr'),
    'sql'                     => 'blob NULL',
    'relation'                => array('type'=>'hasMany', 'load'=>'lazy')
);

$GLOBALS['TL_DCA']['tl_content']['fields']['qlink_item_key'] = array
(
    'label'                   => &$GLOBALS['TL_LANG']['tl_content']['qlink_item_key'],
    'exclude'                 => true,
    'inputType'               => 'text',
    'eval'                    => array('mandatory'=>true, 'maxlength'=>255, 'tl_class'=>'w50'),
    'sql'                     => "varchar(255) NOT NULL default 'item'"
);

$GLOBALS['TL_DCA']['tl_content']['fields']['qlink_keep_query'] = array
(
    'label'                   => &$GLOBALS['TL_LANG']['tl_content']['qlink_keep_query'],
    'exclude'                 => true,
    'filter'                  => false,
    'flag'                    => 2,
    'inputType'               => 'checkbox',
    'eval'                    => array('isBoolean'=>true, 'tl_class'=>'w50 m12'),
    'sql'                     => "char(1) NOT NULL default '0'"
);
