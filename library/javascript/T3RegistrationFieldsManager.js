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

Ext.ns('T3Registration');

T3Registration.FieldsManager = function(conf){
    config = {
        title: 'FieldsList',
        id: 'PanelContainer',
        closeTabTitle: 'Chiusura',
        closeTabText: 'Siete sicuri di voler elimanre il campo?',
        resizeTabs:true, // turn on tab resizing
        minTabWidth: 115,
        tabWidth:135,
        enableTabScroll:true,
        width:600,
        height:300,
        defaults: {autoScroll:true},
        tbar: {
            items: [ {
                text: 'Add',
                iconCls: 't3-icon-actions t3-icon-actions-document t3-icon-document-new',
                handler: function(button,event) {
             // Prompt for user data:
                Ext.Msg.prompt('Name', 'Please enter your name:', function(btn, text){
                    if (btn == 'ok'){
                        // process text value...
                        button.ownerCt.ownerCt.add(new T3Registration.Tab({title: text})).show();
                        Ext.getCmp('PanelContainer').doLayout();
                    }
                });

                }
            } ]
        }
    };


    Ext.apply(config, conf || {});
    T3Registration.FieldsManager.superclass.constructor.call(this, config);
}

Ext.extend(T3Registration.FieldsManager,Ext.TabPanel,{
    defaultValues: {},
    init: function(){
        console.log(this);
        if (this.initialConfiguration){
            var defaultValues = {};
            for(key in this.initialConfiguration){
                defaultValues[key] = {
                    checks:{},
                    fields:{}
                };
                for(var i = 0; i < this.initialConfiguration[key].length; i++){
                    var elementArray = this.initialConfiguration[key][i].split(';');
                    if (elementArray.length > 1){
                        //console.log(elementArray);
                        defaultValues[key].fields[elementArray[0]] = [];
                        defaultValues[key].fields[elementArray[0]][0] = true;
                        defaultValues[key].fields[elementArray[0]][1] = elementArray[1];
                    }
                    else{
                        defaultValues[key].checks[elementArray[0]] = true;
                    }
                }
                console.log(defaultValues[key]);
                lastTab = this.add(new T3Registration.Tab({title: key,defaultValues:defaultValues[key]}));
            }
            this.activate(lastTab);
            this.doLayout();
        }
    }
});

Ext.onReady(function() {
    Ext.QuickTips.init();
    var fields = new array();
    Ext.get('T3RegistrationFieldsManagerHidden').up('form').on('submit',function(e){
        for(var i=0; i < Ext.getCmp('PanelContainer').items.items.length; i++){
            var name = Ext.getCmp('PanelContainer').items.items[i].title;
            var tab = Ext.getCmp('PanelContainer').items.items[i];
            fields[name] = [];
            //console.log(field[name]);
            var j=0;
            for(key in tab.values.checks){
                if (tab.values.checks[key]){
                    fields[name][j] = key;
                    j++
                }
            }
            for(key in tab.values.fields){
                //console.log(tab.values.fields[key]);
                if (tab.values.fields[key][0]){
                    fields[name][j] = key + ';' + tab.values.fields[key][1];
                    j++
                }
            }
        }
        console.log(fields);
        Ext.get('T3RegistrationFieldsManagerHidden').dom.setValue(Ext.util.JSON.encode(fields));
        console.log(Ext.util.JSON.decode(Ext.get('T3RegistrationFieldsManagerHidden').getValue()));
        //e.stopEvent();
    });

                var fieldsValue = {};
                if (Ext.get('T3RegistrationFieldsManagerHidden').getValue().length > 0){
                    fieldsValue = Ext.util.JSON.decode(Ext.get('T3RegistrationFieldsManagerHidden').getValue());
                }
                console.log(fieldsValue);
                panelContainer = new T3Registration.FieldsManager({initialConfiguration: fieldsValue});
                panelContainer.init();
                panelContainer.render('T3RegistrationFieldsManagerPlaceHolder');

            });