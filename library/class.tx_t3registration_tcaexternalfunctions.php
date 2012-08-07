<?php
/**
 * Created by JetBrains PhpStorm.
 * User: federico
 * Date: 18/07/12
 * Time: 11:37
 * To change this template use File | Settings | File Templates.
 */
class tx_t3registration_tcaexternalfunctions{

    /**
     * Fetches items from foreign table (if defined)
     * @param array $field field configuration array
     * @param array $items default items
     * @return array final items list
     */
    public function getForeignTableData($field,$items = array()){
        if ($field['config']['foreign_table']) {
            //to be implemented: error on BE_USER Object
            $items = $this->foreignTable($items, $field, array(), $field['name']);
            if ($field['config']['neg_foreign_table']) {
                $items = $this->foreignTable($items, $field, array(), $field['name'], 1);
            }
        }
        return $items;
    }

    public function getItemsProcFunc($field,$items = array()){
        if (isset($field['config']['itemsProcFunc'])) {
            $items = $this->procItems($items,array(),$field['config'],'fe_users',array(),$field['name']);
        }
        return $items;
    }

    /**
     * Perform user processing of the items arrays of checkboxes, selectorboxes and radio buttons.
     *
     * @param	array		The array of items (label,value,icon)
     * @param	array		The "itemsProcFunc." from fieldTSconfig of the field.
     * @param	array		The config array for the field.
     * @param	string		Table name
     * @param	array		Record row
     * @param	string		Field name
     * @return	array		The modified $items array
     */
    function procItems($items, $iArray, $config, $table, $row, $field) {
        $params = array();
        $params['items'] = &$items;
        $params['config'] = $config;
        $params['TSconfig'] = $iArray;
        $params['table'] = $table;
        $params['row'] = $row;
        $params['field'] = $field;

        t3lib_div::callUserFunction($config['itemsProcFunc'], $params, $this);
        return $items;
    }

    /**
     * Fetches language label for key
     *
     * @param	string		Language label reference, eg. 'LLL:EXT:lang/locallang_core.php:labels.blablabla'
     * @return	string		The value of the label, fetched for the current backend language.
     */
    function sL($str) {
        if(isset($GLOBALS['LANG'])){
            return $GLOBALS['LANG']->sL($str);
        }
        else{
            $language = t3lib_div::makeInstance('language');
            return $language->sL($str);
        }
    }



    /**
     * Adds records from a foreign table (for selector boxes)
     *
     * @param	array		The array of items (label,value,icon)
     * @param	array		The 'columns' array for the field (from TCA)
     * @param	array		TSconfig for the table/row
     * @param	string		The fieldname
     * @param	boolean		If set, then we are fetching the 'neg_' foreign tables.
     * @return	array		The $items array modified.
     * @see addSelectOptionsToItemArray(), t3lib_BEfunc::exec_foreign_table_where_query()
     */
    function foreignTable($items, $fieldValue, $TSconfig, $field, $pFFlag = 0) {
        global $TCA;

        // Init:
        $pF = $pFFlag ? 'neg_' : '';
        $f_table = $fieldValue['config'][$pF . 'foreign_table'];
        $uidPre = $pFFlag ? '-' : '';

        // Exec query:
        $res = $this->exec_foreign_table_where_query($fieldValue, $field, $TSconfig, $pF);

        // Perform error test
        if ($GLOBALS['TYPO3_DB']->sql_error()) {
            $msg = htmlspecialchars($GLOBALS['TYPO3_DB']->sql_error());
            $msg .= '<br />' . LF;
            $msg .= $this->sL('LLL:EXT:lang/locallang_core.php:error.database_schema_mismatch');
            $msgTitle = $this->sL('LLL:EXT:lang/locallang_core.php:error.database_schema_mismatch_title');
            /** @var $flashMessage t3lib_FlashMessage */
            $flashMessage = t3lib_div::makeInstance(
                't3lib_FlashMessage',
                $msg,
                $msgTitle,
                t3lib_FlashMessage::ERROR,
                TRUE
            );
            t3lib_FlashMessageQueue::addMessage($flashMessage);

            return array();
        }

        // Get label prefix.
        $lPrefix = $this->sL($fieldValue['config'][$pF . 'foreign_table_prefix']);

        // Get icon field + path if any:
        $iField = $TCA[$f_table]['ctrl']['selicon_field'];
        $iPath = trim($TCA[$f_table]['ctrl']['selicon_field_path']);

        // Traverse the selected rows to add them:
        while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
            t3lib_BEfunc::workspaceOL($f_table, $row);

            if (is_array($row)) {
                // Prepare the icon if available:
                if ($iField && $iPath && $row[$iField]) {
                    $iParts = t3lib_div::trimExplode(',', $row[$iField], 1);
                    $icon = '../' . $iPath . '/' . trim($iParts[0]);
                } elseif (t3lib_div::inList('singlebox,checkbox', $fieldValue['config']['renderMode'])) {
                    $icon = t3lib_iconWorks::mapRecordTypeToSpriteIconName($f_table, $row);
                } else {
                    $icon = 'empty-empty';
                }

                // Add the item:
                $items[] = array(
                    $lPrefix . htmlspecialchars(t3lib_BEfunc::getRecordTitle($f_table, $row)),
                    $uidPre . $row['uid'],
                    $icon
                );
            }
        }
        return $items;
    }

    /**
     * Returns select statement for MM relations (as used by TCEFORMs etc)
     * Usage: 3
     *
     * @param	array		Configuration array for the field, taken from $TCA
     * @param	string		Field name
     * @param	array		TSconfig array from which to get further configuration settings for the field name
     * @param	string		Prefix string for the key "*foreign_table_where" from $fieldValue array
     * @return	string		Part of query
     * @internal
     * @see t3lib_transferData::renderRecord(), t3lib_TCEforms::foreignTable()
     */
    public static function exec_foreign_table_where_query($fieldValue, $field = '', $TSconfig = array(), $prefix = '') {
        global $TCA;

        $foreign_table = $fieldValue['config'][$prefix . 'foreign_table'];
        t3lib_div::loadTCA($foreign_table);
        $rootLevel = $TCA[$foreign_table]['ctrl']['rootLevel'];

        $fTWHERE = $fieldValue['config'][$prefix . 'foreign_table_where'];
        if (strstr($fTWHERE, '###REC_FIELD_')) {
            $fTWHERE_parts = explode('###REC_FIELD_', $fTWHERE);
            foreach ($fTWHERE_parts as $kk => $vv) {
                if ($kk) {
                    $fTWHERE_subpart = explode('###', $vv, 2);
                    if (substr($fTWHERE_parts[0], -1) === '\'' && $fTWHERE_subpart[1]{0} === '\'') {
                        $fTWHERE_parts[$kk] = $GLOBALS['TYPO3_DB']->quoteStr($TSconfig['_THIS_ROW'][$fTWHERE_subpart[0]], $foreign_table) . $fTWHERE_subpart[1];
                    } else {
                        $fTWHERE_parts[$kk] = $GLOBALS['TYPO3_DB']->fullQuoteStr($TSconfig['_THIS_ROW'][$fTWHERE_subpart[0]], $foreign_table) . $fTWHERE_subpart[1];
                    }
                }
            }
            $fTWHERE = implode('', $fTWHERE_parts);
        }

        $fTWHERE = str_replace('###CURRENT_PID###', intval($TSconfig['_CURRENT_PID']), $fTWHERE);
        $fTWHERE = str_replace('###THIS_UID###', intval($TSconfig['_THIS_UID']), $fTWHERE);
        $fTWHERE = str_replace('###THIS_CID###', intval($TSconfig['_THIS_CID']), $fTWHERE);
        $fTWHERE = str_replace('###STORAGE_PID###', intval($TSconfig['_STORAGE_PID']), $fTWHERE);
        $fTWHERE = str_replace('###SITEROOT###', intval($TSconfig['_SITEROOT']), $fTWHERE);
        $fTWHERE = str_replace('###PAGE_TSCONFIG_ID###', intval($TSconfig[$field]['PAGE_TSCONFIG_ID']), $fTWHERE);
        $fTWHERE = str_replace('###PAGE_TSCONFIG_IDLIST###', $GLOBALS['TYPO3_DB']->cleanIntList($TSconfig[$field]['PAGE_TSCONFIG_IDLIST']), $fTWHERE);
        $fTWHERE = str_replace('###PAGE_TSCONFIG_STR###', $GLOBALS['TYPO3_DB']->quoteStr($TSconfig[$field]['PAGE_TSCONFIG_STR'], $foreign_table), $fTWHERE);

        // rootLevel = -1 is not handled 'properly' here - it goes as if it was rootLevel = 1 (that is pid=0)
        $wgolParts = $GLOBALS['TYPO3_DB']->splitGroupOrderLimit($fTWHERE);
        if ($rootLevel) {
            $queryParts = array(
                'SELECT' => t3lib_BEfunc::getCommonSelectFields($foreign_table, $foreign_table . '.'),
                'FROM' => $foreign_table,
                'WHERE' => $foreign_table . '.pid=0 ' .
                    self::deleteClause($foreign_table) . ' ' .
                    $wgolParts['WHERE'],
                'GROUPBY' => $wgolParts['GROUPBY'],
                'ORDERBY' => $wgolParts['ORDERBY'],
                'LIMIT' => $wgolParts['LIMIT']
            );
        } else {
            if ($foreign_table != 'pages') {
                $queryParts = array(
                    'SELECT' => t3lib_BEfunc::getCommonSelectFields($foreign_table, $foreign_table . '.'),
                    'FROM' => $foreign_table . ', pages',
                    'WHERE' => 'pages.uid=' . $foreign_table . '.pid
								AND pages.deleted=0 ' .
                        t3lib_BEfunc::deleteClause($foreign_table) .
                        $wgolParts['WHERE'],
                    'GROUPBY' => $wgolParts['GROUPBY'],
                    'ORDERBY' => $wgolParts['ORDERBY'],
                    'LIMIT' => $wgolParts['LIMIT']
                );
            } else {
                $queryParts = array(
                    'SELECT' => t3lib_BEfunc::getCommonSelectFields($foreign_table, $foreign_table . '.'),
                    'FROM' => 'pages',
                    'WHERE' => 'pages.deleted=0
								AND ' . $pageClause . ' ' .
                        $wgolParts['WHERE'],
                    'GROUPBY' => $wgolParts['GROUPBY'],
                    'ORDERBY' => $wgolParts['ORDERBY'],
                    'LIMIT' => $wgolParts['LIMIT']
                );
            }
        }

        return $GLOBALS['TYPO3_DB']->exec_SELECT_queryArray($queryParts);
    }

}
