var x = {
  abc: 1,
  zyz: 2,
  abc: 5,
  mno: {
      abc: 4
  },
  abc: 5
  
  this.request({
    action: 'getSubmissions'
  });

  this.request({
    action: 'deleteSubmission'
  });
}


LinkingEditScreenWidgetType.prototype = {

    _addDeleteButtonEvent: function(parentid)
    {
        var params = {
            screen: 'LinkingEditScreenWidget',
            assetid: self.assetid,
            parentid: parentid,
            assetid: parentid,
            op: 'deleteLink'
        };

    },

    saveDesignEdit: function()
    {
        var params = {
            screen: [this.id, 'Widget'].join(''),
            assetid: this.assetid,
            changes: dfx.jsonEncode(this.currnetLinksWdgt.getChanges()),
            op: 'saveLinkEdit'
        };

    }

};