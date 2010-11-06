
Ext.ns('T3Registration');
translationArray = new array();

/*T3Registration.CheckboxWithField = function(conf){
    var defaultValues = [false,''];
    Ext.apply(defaultValues,conf.defaultValues || []);
    var label = conf.label || '';
    var config = {
        layout: 'column',
        xtype: 'panel',
        anchor: '100%',
        border: false,
        defaults: {
            border: false,
        },
        items:[{
                layout: 'form',
                xtype: 'container',
                style: {
                    padding: '0px',
                    margin: '0'
                },
                defaults: {
                    border: false,
                    fieldLabel: '',
                    hideLabel: true,
                },
                items:[
                       new Ext.form.Checkbox(
                       {
                           boxLabel: label,
                           name: label,
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
                //columnWidth:.5,
                layout: 'form',
                xtype: 'container',
                style: {padding: '0 0 0 10px'},
                defaults: {
                    fieldLabel: '',
                    hideLabel: true
                },
                items:[{
                        listeners:{
                        change: function(object){
                                 console.log(object.ownerCt.ownerCt.ownerCt.ownerCt.ownerCt);
                                 //console.log(this);
                                 object.ownerCt.ownerCt.ownerCt.ownerCt.ownerCt.values.fields[label.toLowerCase()][1] = this.getValue();
                        }
                     },
                    disabled: 'true',
                    xtype: 'textfield',
                    width: 150,
                    value: defaultValues[1]
                    //name: label + '1',
                    //id: label + '1'
                }]
        }]
    };
    Ext.apply(config, conf || {});
    T3Registration.CheckboxWithField.superclass.constructor.call(this, config);
}

Ext.extend(T3Registration.CheckboxWithField,Ext.Panel,{

});

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
                                        //console.log(checkbox.boxLabel.toLowerCase());
                                        //console.log(this);
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
                               new T3Registration.CheckboxWithField({label:'regExp',defaultValues: defaultValues.fields.regExp}),
                               new T3Registration.CheckboxWithField({label:'saveInFlex',defaultValues: defaultValues.fields.saveInFlex})
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
    resizeTabs:true, // turn on tab resizing
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
*/

Ext.onReady(function() {
    Ext.QuickTips.init();
    var fields = new array();
    //console.log(Ext.get('fieldsManagerHidden'));
    //Ext.get('fieldsManagerHidden').innerHTML='works';
    //alert(Ext.get('fieldsManagerHidden').getValue());
    Ext.get('fieldsManagerHidden').up('form').on('submit',function(e){
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
        Ext.get('fieldsManagerHidden').dom.setValue(Ext.util.JSON.encode(fields));
        console.log(Ext.util.JSON.decode(Ext.get('fieldsManagerHidden').getValue()));
        e.stopEvent();
    });
    // alert(document.form[0].name);
                panelContainer = new Ext.TabPanel( {
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
                    //plugins: new Ext.ux.TabCloseMenu(),
                    tbar: {
                        items: [ {
                            text: 'Add',
                            iconCls: 't3-icon-actions t3-icon-actions-document t3-icon-document-new',
                            handler: function(button,event) {
                         // Prompt for user data:
                            Ext.Msg.prompt('Name', 'Please enter your name:', function(btn, text){
                                if (btn == 'ok'){
                                    // process text value...
                                    button.ownerCt.ownerCt.add(new T3Registration.Tab({title: text,defaultValues:{checks:{
                                        required: true}}})
                                        //xtype: 'fieldset',#
                                        //autoScroll:true,
                                        //defaults:{anchor:'-20'},
                                                   /* { closable: true,
                                        title: text,
                                        enableTabScroll:true,
                                        resizeTabs:true, // turn on tab resizing
                                        defaults: {
                                            layout:'form',
                                            labelWidth:50,
                                            bodyStyle:'padding:0px',
                                            border: false,
                                            autoScroll:true
                                        },
                                        listeners: {
                                            beforeClose: function(p){
                                            Ext.Msg.show({
                                                title:'Save Changes?',
                                                msg: 'Your are closing a tab that has unsaved changes. Would you like to save your changes?',
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
                                        layout: 'column',
                                        items: [ {
                                            columnWidth:.2,
                                            layout: 'form',
                                            xtype: 'panel',
                                            items:[{
                                                xtype: 'checkbox',
                                                fieldLabel: 'Required',
                                                name: 'required'
                                            }
                                           ,{
                                               xtype: 'checkbox',
                                               fieldLabel: 'Int',
                                               name: 'int'
                                           }]
                                        },{
                                            columnWidth:.7,
                                            layout: 'column',
                                            xtype: 'panel',
                                            defaults: {
                                                layout:'form',
                                                bodyStyle:'padding:0px',
                                                border: false,
                                                autoScroll:true
                                            },
                                            items:[{
                                                columnWidth:.3,
                                                layout: 'form',
                                                xtype: 'panel',
                                                labelWidth:90,
                                                items:[{
                                                    xtype: 'checkbox',
                                                    fieldLabel: 'alfafdgdfsggddgs',
                                                    name: 'alfa',
                                                    listeners:{
                                                        check: function(e,checked){
                                                            if (checked){
                                                                Ext.getCmp(e.name + '1').enable();
                                                            }
                                                            else {
                                                                Ext.getCmp(e.name + '1').disable();
                                                            }
                                                        }
                                                    }
                                                }]},{
                                                columnWidth:.7,
                                                layout: 'form',
                                                xtype: 'panel',
                                                labelWidth:1,
                                                items:[{
                                                    disabled: 'true',
                                                    xtype: 'textfield',
                                                    width: 150,
                                                    name: 'alfa1',
                                                    id: 'alfa1'
                                                }]
                                            }]
                                            }
                                           ]}*/
                                    ).show();
                                    Ext.getCmp('PanelContainer').doLayout();
                                }
                            });

                            }
                        } ]
                    }
                });

                panelContainer.render('fieldsManagerPlaceHolder');

            });