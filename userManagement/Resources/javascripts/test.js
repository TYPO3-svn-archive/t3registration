
var usersStore = new Ext.data.JsonStore({
        url: '/typo3/ajax.php?ajaxID=tx_t3registration::getuser&folder=' + folder,
        root: 'data',
        fields: ['uid','username','password']
    });

var grid = new Ext.grid.GridPanel({
    region: 'center',
    title: 'Users',
    store: usersStore,
    columns: [
              {header: 'ID', width: 30, dataIndex: 'uid', sortable: true, hidden: true},
              { header: 'username', width: 130, dataIndex: 'username', sortable: true},
              {header: 'password', width: 130, dataIndex: 'password', sortable: true},
              ],
              // autoExpandColumn: 'username',
              width: 500,
              height: 300,
              loadMask: true
});

var fieldset = {
    columnWidth: 0.5,
    xtype: 'fieldset',
    labelWidth: 90,
    collapsible: true,
    title: 'Movie details',
    defaults: { width: 230 },
    defaultType: 'textfield',
    autoHeight: true,
    bodyStyle: Ext.isIE ? 'padding:0 0 5px 15px;' : 'padding:10px 15px;',
    items: [{ id: 'username', fieldLabel: 'username', name: 'username'
    }, {id: 'password', fieldLabel: 'password', name: 'password'}]
    };

var panel = new Ext.Panel({
        width: 900,
        height: 300,
        title: 'prova per T3Registration',
        collapsible: true,
        layout: 'column',
        // html: 'test'
            items: [
                    {columnWidth: 0.5,items:[grid]},
                    fieldset

]
    });



Ext.onReady(function() {
    Ext.QuickTips.init();

    usersStore.load();

    grid.getSelectionModel().on('rowselect', function(sm, rowIndex, record) {
        //fieldset.getForm().loadRecord(record);
        console.log(record);});


    panel.render(Ext.get('panel'));

     });