this.request({ action: 'getTypeFormatContents', });

addTypeFormatButton.addClickEvent(function() {
   self.addNewTypeFormat();
});

var x = {};

var y = {
   VarOne  : 'If you ask me, thats if you ask',
   VarTwo  : ['Alonzo played you', 'for a fool', 'esse'],
   VarThree: function(arg) {
       console.info(1);
   }
};

var z = {
   VarOne  : 'If you ask me, thats if you ask',
   VarTwo  : ['Alonzo played you', 'for a fool', 'esse'],
   VarThree: function(arg) {
       console.info(1);
   },
};

var x = function() {
   console.info(2);
};

AssetListingEditWidgetType.prototype = {
   init: function(data, assetid, editables)
   {
   }
};

AssetListingEditWidgetType.prototype = {
   init: function(data, assetid, editables)
   {
   },
};
