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
    //define the configuration data
    config = {
        resizeTabs:true,
        minTabWidth: 115,
        tabWidth:135,
        enableTabScroll:true,
        width:600,
        height:300,
        defaults: {autoScroll:true},
        tbar: { //toolbar for adding a new tab
            items: [ {
                text: translateObject.addButtonTitle, //title for the tab
                handler: function(button,event) {
                    Ext.Msg.prompt(translateObject.msgboxAddPanelTitle, translateObject.msgboxAddPanelTitle, function(btn, text){
                        if (btn == 'ok'){
                            var tabsList = button.ownerCt.ownerCt.items.items;
                            var errorText = '';
                            for(var i=0; i < tabsList.length; i++){
                                if (tabsList[i].title == text){
                                    errorText = translateObject.errorDuplicateTitle;
                                    break;
                                }
                            }
                            if (text.length == 0){
                                errorText = translateObject.errorEmptyPanelTitle;
                            }
                            if (errorText.length > 0){

                                Ext.Msg.show({title: translateObject.errorTitle,msg: errorText,buttons: Ext.MessageBox.OK,icon: Ext.MessageBox.ERROR});
                            }
                            else {
                                //create a new tab
                                button.ownerCt.ownerCt.add(new T3Registration.Tab({title: text})).show();
                                button.ownerCt.ownerCt.doLayout();
                                button.ownerCt.ownerCt.fireEvent('createField');
                            }
                        }
                    });
                }
            } ]
        }
    };

    //Merge the config and conf array
    Ext.apply(config, conf || {});
    //call the superclass constructor
    T3Registration.FieldsManager.superclass.constructor.call(this, config);
}


//extend the tab panel
Ext.extend(T3Registration.FieldsManager,Ext.TabPanel,{
    //default values object containig values for create the initialization data
    defaultValues: {},

    /**
     * This function initializes the configuration data like checks and check with fields
     *
     */
    init: function(){
        this.addEvents('createField');
        var lastTab = {};
        //if you create a default configuration tab, you can set some check and fields with predefined values
        if (this.initialConfiguration){
            var defaultValues = {};
            for(key in this.initialConfiguration){
                defaultValues[key] = {
                    checks:{}, //list of checks
                    fields:{} //list of checks with fields
                };
                for(var i = 0; i < this.initialConfiguration[key].length; i++){
                    var elementArray = this.initialConfiguration[key][i].split(';');
                    if (elementArray.length > 1){ //if saved object is separated by semicolon the first parameter is true and the second one is the value
                        defaultValues[key].fields[elementArray[0]] = [];
                        defaultValues[key].fields[elementArray[0]][0] = true;
                        defaultValues[key].fields[elementArray[0]][1] = elementArray[1];
                    }
                    else{ //if array is length 1 the value is check
                        defaultValues[key].checks[elementArray[0]] = true;
                    }
                }
                //save the last tab is activated
                lastTab = this.add(new T3Registration.Tab({title: key,defaultValues:defaultValues[key]}));
            }
           this.doLayout();
        }
    }
});



Ext.onReady(function() {
    Ext.QuickTips.init();

    Ext.apply(Ext.form.VTypes,{
        hook: function(value, field)
        {
            return value.match(/w*->w*/);
        },
        hookText: translateObject.hookValidationErrorTitle
    });
    var fields = new array();
    Ext.get('T3RegistrationFieldsManagerHidden').up('form').on('submit',function(e){
        var validate = true;
        //console.log(panelContainer.items.items.length);
        for(var i=0; i < panelContainer.items.items.length; i++){
            //console.log(panelContainer.items.items[i].isValid());
            //console.log('iiiii');
            var name = panelContainer.items.items[i].title;
            var tab = panelContainer.items.items[i];
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
                console.log(tab.values.fields[key]);
                if (tab.values.fields[key][0]){
                    fields[name][j] = key + ';' + tab.values.fields[key][1];
                    j++
                }
            }

            if (!panelContainer.items.items[i].isValid()){
                 validate = false;
             }
        }
        if(validate){
            Ext.get('T3RegistrationFieldsManagerHidden').dom.setValue(Ext.util.JSON.encode(fields));
        }
        else {
            console.log('blocco');
            e.stopEvent();
        }


    });

                var fieldsValue = {};
                if (Ext.get('T3RegistrationFieldsManagerHidden').getValue().length > 0){
                    fieldsValue = Ext.util.JSON.decode(Ext.get('T3RegistrationFieldsManagerHidden').getValue());
                }
                var panelContainer = new T3Registration.FieldsManager({initialConfiguration: fieldsValue});
                panelContainer.init();
                panelContainer.render('T3RegistrationFieldsManagerPlaceHolder');


            });