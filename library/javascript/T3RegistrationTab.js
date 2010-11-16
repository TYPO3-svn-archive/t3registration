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

T3Registration.Tab = function(conf){
    var defaultValues = {
            checks:{
            required: false,
            int: false,
            alfa: false,
            alfanum: false,
            trim:false,
            decimal: false
        },
        fields:{
            date: [false,''],
            regexp: [false,''],
            saveinflex: [false,''],
            minimum: [false,''],
            maximum: [false,''],
            hook: [false,'']
        }
    };
    Ext.apply(defaultValues.checks,conf.defaultValues.checks || {});
    Ext.apply(defaultValues.fields,conf.defaultValues.fields || {});
    config = {
        forceLayout: true, //permits to rearrange object inside
        values: defaultValues,
        items: [
                   {
                       xtype: 'checkboxgroup',
                       columns: 3,
                       vertical: true,
                       defaults:{
                           listeners:{
                               check: function(checkbox,checked){
                                    if (checked){
                                        checkbox.ownerCt.ownerCt.ownerCt.ownerCt.values.checks[checkbox.name.toLowerCase()] = true;
                                    }
                                    else {
                                        checkbox.ownerCt.ownerCt.ownerCt.ownerCt.values.checks[checkbox.name.toLowerCase()] = false;
                                    }
                               }
                            }
                       },
                       items:[
                                {
                                    name: 'required',
                                    boxLabel: translateObject.requiredTitle,
                                    checked: defaultValues.checks.required
                                },
                                {
                                    name: 'int',
                                    boxLabel: translateObject.intTitle,
                                    checked: defaultValues.checks.int
                                },{
                                    name: 'alfa',
                                    boxLabel: translateObject.alfaTitle,
                                    checked: defaultValues.checks.alfa
                                },
                                {
                                    name: 'alfanum',
                                    boxLabel: translateObject.alfanumTitle,
                                    checked: defaultValues.checks.alfanum
                                },{
                                    name: 'trim',
                                    boxLabel: translateObject.trimTitle,
                                    checked: defaultValues.checks.trim
                                },
                                {
                                    name: 'decimal',
                                    boxLabel: translateObject.decimalTitle,
                                    checked: defaultValues.checks.decimal
                                }
                              ]

            },
            {
                layout:'column',
                defaults:{
                    border: false
                },
                items:[{
                            columnWidth:.5,
                            items:[
                               new T3Registration.CheckboxWithField({name:'date', label:translateObject.dateTitle,defaultValues: defaultValues.fields.date,vtype:''}),
                               new T3Registration.CheckboxWithField({name:'regexp', label:translateObject.regularExpressionTitle,defaultValues: defaultValues.fields.regexp,vtype:''}),
                               new T3Registration.CheckboxWithField({name:'saveinflex', label:translateObject.saveInFlexTitle,defaultValues: defaultValues.fields.saveinflex,vtype:''})
                            ]
                        },{
                            columnWidth:.5,
                            items:[
                               new T3Registration.CheckboxWithField({name:'minimum', label:translateObject.minimumTitle,defaultValues: defaultValues.fields.minimum,vtype:''}),
                               new T3Registration.CheckboxWithField({name:'maximum', label:translateObject.maximumTitle,defaultValues: defaultValues.fields.maximum,vtype:''}),
                               new T3Registration.CheckboxWithField({name:'hook', label:translateObject.hookTitle,defaultValues: defaultValues.fields.hook,vtype:'hook'})
                            ]
                        }
                       ]
            }
        ]
    };
    Ext.apply(config, conf || {});
    T3Registration.Tab.superclass.constructor.call(this, config);
    this.doLayout();
}

Ext.extend(T3Registration.Tab,Ext.Panel,{
    closable: true,
    enableTabScroll:true,
    width: 600,
    resizeTabs:true,
    defaults: {
        labelWidth:50,
        bodyStyle:'padding:0px',
        border: false,
        autoScroll:true
    },
    listeners: {
        beforeClose: function(p){
        Ext.Msg.show({
            title:translateObject.msgboxClosePanelTitle,
            msg: translateObject.msgboxClosePanelText,
            buttons: Ext.Msg.YESNO,
            fn: function(btn){
            if (btn == 'yes')
                p.ownerCt.remove(p);
            },
            animEl: 'elId'
         });
        return false;
        }
    },
    isValid: function(){
        var bool = true;
        //console.log(this.items.items[1].items.items);
        for(var i = 0; i < this.items.items[1].items.items.length; i++){ //column 1,2
            for(var j=0; j< this.items.items[1].items.items[i].items.items.length; j++){
                bool = bool && this.items.items[1].items.items[i].items.items[j].isValid();
                //console.log(this.items.items[1].items.items[i].items[j]);
            }
        }
        return bool;
    }
});