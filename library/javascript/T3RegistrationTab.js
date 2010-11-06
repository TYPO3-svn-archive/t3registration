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
    Ext.apply(defaultValues,conf.defaultValues || {});
    config = {
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
                                        checkbox.ownerCt.ownerCt.ownerCt.ownerCt.values.checks[checkbox.boxLabel.toLowerCase()] = true;
                                    }
                                    else {
                                        checkbox.ownerCt.ownerCt.ownerCt.ownerCt.values.checks[checkbox.boxLabel.toLowerCase()] = false;
                                    }
                               }
                            }
                       },
                       items:[
                                {
                                    boxLabel: 'Required',
                                    checked: defaultValues.checks.required
                                },
                                {
                                    boxLabel: 'Int',
                                    checked: defaultValues.checks.int
                                },{
                                    boxLabel: 'Alfa',
                                    checked: defaultValues.checks.alfa
                                },
                                {
                                    boxLabel: 'Alfanum',
                                    checked: defaultValues.checks.alfanum
                                },{
                                    boxLabel: 'Trim',
                                    checked: defaultValues.checks.trim
                                },
                                {
                                    boxLabel: 'Decimal',
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
                               new T3Registration.CheckboxWithField({
                                   label:'date',defaultValues: defaultValues.fields.date}),
                               new T3Registration.CheckboxWithField({label:'regExp',defaultValues: defaultValues.fields.regexp}),
                               new T3Registration.CheckboxWithField({label:'saveInFlex',defaultValues: defaultValues.fields.saveinflex})
                            ]
                        },{
                            columnWidth:.5,
                            items:[
                               new T3Registration.CheckboxWithField({
                                   label:'minimum',defaultValues: defaultValues.fields.minimum}),
                               new T3Registration.CheckboxWithField({label:'maximum',defaultValues: defaultValues.fields.maximum}),
                               new T3Registration.CheckboxWithField({label:'hook',defaultValues: defaultValues.fields.hook})
                            ]
                        }
                       ]
            }
        ]
    };
    Ext.apply(config, conf || {});
    T3Registration.Tab.superclass.constructor.call(this, config);
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
            title:p.ownerCt.closeTabTitle,
            msg: p.ownerCt.closeTabText,
            buttons: Ext.Msg.YESNO,
            fn: function(btn){
            if (btn == 'yes')
                p.ownerCt.remove(p);
            },
            animEl: 'elId'
         });
        return false;
        }
    }
});