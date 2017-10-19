<f:if condition="{settings.ttAddressMapping}"><f:then>
<?php

if (!isset($GLOBALS['TCA']['tt_address']['ctrl']['type'])) {
    if (file_exists($GLOBALS['TCA']['tt_address']['ctrl']['dynamicConfigFile'])) {
        require_once($GLOBALS['TCA']['tt_address']['ctrl']['dynamicConfigFile']);
    }
}

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
    'tt_address',
    $GLOBALS['TCA']['tt_address']['ctrl']['type'],
    '',
    'after:' . $GLOBALS['TCA']['tt_address']['ctrl']['label']
);

$tmp_sic_address_columns = array(
     <f:for each="{properties}" as="field">
     <f:if condition="{field.external} == '0'">
     <f:format.htmlentitiesDecode>
    '{field.title}' => array(
        'exclude' => 1,
        'label' => 'LLL:EXT:sic_address/Resources/Private/Language/locallang_db.xlf:tx_sicaddress_domain_model_address.{field.title}',
        <f:if condition="{field.type.title} == 'rich'">'defaultExtras' => 'richtext[*]',</f:if>
        'config' => {field.config}
    ),
     </f:format.htmlentitiesDecode>
     </f:if>
    </f:for>
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('tt_address',$tmp_sic_address_columns);

/* inherit and extend the show items from the parent class */

if(isset($GLOBALS['TCA']['tt_address']['types']['0']['showitem'])) {
    $GLOBALS['TCA']['tt_address']['types']['Tx_SicAddress_Address']['showitem'] = $GLOBALS['TCA']['tt_address']['types']['0']['showitem'];
} elseif(is_array($GLOBALS['TCA']['tt_address']['types'])) {
    // use first entry in types array
    $tt_address_type_definition = reset($GLOBALS['TCA']['tt_address']['types']);
    $GLOBALS['TCA']['tt_address']['types']['Tx_SicAddress_Address']['showitem'] = $tt_address_type_definition['showitem'];
} else {
    $GLOBALS['TCA']['tt_address']['types']['Tx_SicAddress_Address']['showitem'] = '';
}
$GLOBALS['TCA']['tt_address']['types']['Tx_SicAddress_Address']['showitem'] .= ',--div--;LLL:EXT:sic_address/Resources/Private/Language/locallang_db.xlf:sic_address_tca_tab_label,';
$GLOBALS['TCA']['tt_address']['types']['Tx_SicAddress_Address']['showitem'] .= '<f:for each="{properties}" as="field"><f:if condition="{field.external} == '0'"> {field.title},</f:if></f:for>';

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr(
    '',
    'EXT:/Resources/Private/Language/locallang_csh_.xlf'
);

// Upgrade Description to an RTE field
$GLOBALS['TCA']['tt_address']['columns']['description']['defaultExtras'] = 'richtext[*]';
$GLOBALS['TCA']['tt_address']['columns']['description']['config']['wizards'] = array(
    'RTE' => array(
        'icon' => 'wizard_rte2.gif',
        'notNewRecords'=> 1,
        'RTEonly' => 1,
        'module' => array(
            'name' => 'wizard_rich_text_editor',
            'urlParameters' => array(
                'mode' => 'wizard',
                'act' => 'wizard_rte.php'
            )
        ),
        'title' => 'LLL:EXT:cms/locallang_ttc.xlf:bodytext.W.RTE',
        'type' => 'script'
    )
);

// Set company field as label, name and email as alternative label, ordering by company
$GLOBALS['TCA']['tt_address']['ctrl']['label'] = 'company';
$GLOBALS['TCA']['tt_address']['ctrl']['label_alt'] = 'name, email';
$GLOBALS['TCA']['tt_address']['ctrl']['default_sortby'] = 'ORDER BY company';
$GLOBALS['TCA']['tt_address']['ctrl']['searchFields'] = '{settings.searchFields}';

</f:then>
<f:else></f:else>
</f:if>
