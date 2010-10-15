
var usersStore = new Ext.data.JsonStore({
        url: '/typo3/ajax.php?ajaxID=tx_t3registration::getuser',
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
              //autoExpandColumn: 'username',
              width: 600,
              height: 300,
              loadMask: true
});
var panel = new Ext.Panel({
        width: 600,
        height: 300,
        title: 'prova per T3Registration',
        collapsible: true,
        layout: 'border',
        //html: 'test'
            items: [grid,
                    new Ext.Panel({
                        title: 'User Data',
                        html: 'test',
                        region: 'east',
                        collapsible: true
                    })]
    });

Ext.onReady(function() {





    usersStore.load();
    panel.render(Ext.get('panel'));

     });