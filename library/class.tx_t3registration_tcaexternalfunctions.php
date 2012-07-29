<?php
/**
 * Created by JetBrains PhpStorm.
 * User: federico
 * Date: 18/07/12
 * Time: 11:37
 * To change this template use File | Settings | File Templates.
 */
class tx_t3registration_tcaexternalfunctions{

    public function getForeignTableData($field,$items = array()){
        $items = array();
        if ($field['config']['foreign_table']) {
            //to be implemented: error on BE_USER Object
            //$items = $this->foreignTable($items, $field, array(), $field['name']);
            if ($field['config']['neg_foreign_table']) {
                $items = $this->foreignTable($items, $field, array(), $field['name'], 1);
            }
        }
        $itemFromField = (is_array($field['config']['items']))?$field['config']['items']:array();
        return array_merge($itemFromField,$items);
    }

    /**
     * Fetches language label for key
     *
     * @param	string		Language label reference, eg. 'LLL:EXT:lang/locallang_core.php:labels.blablabla'
     * @return	string		The value of the label, fetched for the current backend language.
     */
    function sL($str) {
        return $GLOBALS['LANG']->sL($str);
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
        $res = t3lib_BEfunc::exec_foreign_table_where_query($fieldValue, $field, $TSconfig, $pF);

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

}
