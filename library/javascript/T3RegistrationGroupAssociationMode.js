/***************************************************************
 * extJS for T3Registration
 *
 * $Id$
 *
 * Copyright notice
 *
 * (c) 2009-2010 Federico Bernardin <federico@bernardin.it>
 * All rights reserved
 *
 * This script is part of the TYPO3 project. The TYPO3 project is
 * free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * The GNU General Public License can be found at
 * http://www.gnu.org/copyleft/gpl.html.
 *
 * This script is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

var groupAssociationMode = new Ext.form.ComboBox({

    listeners: {
        'select': function(combo, record, index){
            alert(combo.getValue());
        }
    },
    store: new Ext.data.SimpleStore({
        id: 0,
        autoLoad: true,
        fields:['fieldId','fieldName'],
        data: [['nessuno','none'],['pippo','pippo'],['pluto','pluto']]
    }),
    triggerAction: 'all',
    valueField: 'fieldId',
    displayField: 'fieldName',
    mode: 'local',
    hiddenName: 'fieldId',
    editable: false,
    forceSelection: true,
    selectOnFocus: true,
    //resizable: true,
    emptyText: 'select Mode',
    typeAhead: true
});

function reloadCombo(){
    groupAssociationMode.store.removeAll();
    var dataArray = [];
    dataArray[0] = ['none','nessuno'];
    for(var i=0; i < panelContainer.items.items.length; i++){
        dataArray[i+1] = [panelContainer.items.items[i].title,panelContainer.items.items[i].title];
    }
    groupAssociationMode.store.loadData(dataArray);
    groupAssociationMode.updateBox();
}

Ext.onReady(function(){
    groupAssociationMode.render('T3RegistrationGroupAssociationModePlaceHolder');
    //reloadCombo();
    Ext.getCmp('T3RegistrationFieldsManager').on('createField',function(){
        groupAssociationMode.store.removeAll();
        var dataArray = [];
        dataArray[0] = ['none','nessuno'];
        for(var i=0; i < panelContainer.items.items.length; i++){
            dataArray[i+1] = [panelContainer.items.items[i].title,panelContainer.items.items[i].title];
        }
        groupAssociationMode.store.loadData(dataArray);
        groupAssociationMode.updateBox();
    });
});