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

T3Registration.CheckboxWithField = function(conf){
    var defaultValues = [false,''];
    Ext.apply(defaultValues,conf.defaultValues || []);
    var label = conf.label || '';
    var config = {
        layout: 'column',
        xtype: 'panel',
        anchor: '100%',
        border: false,
        items:[{
                layout: 'form',
                xtype: 'container',
                defaults: {
                    border: false,
                    fieldLabel: '',
                    hideLabel: true,
                },
                items:[
                       new Ext.form.Checkbox(
                       {
                           boxLabel: label,
                           checked: defaultValues[0],
                           listeners:{
                               check: function(checkbox,checked){
                                    if (checked){
                                        checkbox.ownerCt.ownerCt.ownerCt.ownerCt.ownerCt.values.fields[checkbox.boxLabel.toLowerCase()][0] = true;
                                        checkbox.ownerCt.ownerCt.items.items[1].items.items[0].enable();
                                    }
                                    else {
                                        checkbox.ownerCt.ownerCt.ownerCt.ownerCt.ownerCt.values.fields[checkbox.boxLabel.toLowerCase()][0] = false;
                                        checkbox.ownerCt.ownerCt.items.items[1].items.items[0].disable();
                                    }
                               }
                            }
                       })
                 ]
             },{
                layout: 'form',
                xtype: 'container',
                hideLabel: true,
                style: {padding: '0 0 0 10px'},
                defaults: {
                    fieldLabel: '',
                    hideLabel: true
                },
                items:[{
                        listeners:{
                        change: function(object){
                                 object.ownerCt.ownerCt.ownerCt.ownerCt.ownerCt.values.fields[label.toLowerCase()][1] = this.getValue();
                        }
                     },
                    disabled: !defaultValues[0],
                    xtype: 'textfield',
                    width: 150,
                    value: defaultValues[1]
                }]
        }]
    };
    Ext.apply(config, conf || {});
    T3Registration.CheckboxWithField.superclass.constructor.call(this, config);
}

Ext.extend(T3Registration.CheckboxWithField,Ext.Panel,{});