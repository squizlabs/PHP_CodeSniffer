function FuncOne()
{
    // Code here.

}//end AdjustModalDialogWidgetType


Testing.prototype = {

    doSomething: function()
    {

        // Code here.

    },

    doSomethingElse: function()
    {
        // Code here.

    },

    start: function()
    {
        this.toolbarPlugin.addButton('Image', 'imageEditor', 'Insert/Edit Image', function () { self.editImage() });

    },
};

function FuncFour()
{


    // Code here.
}

AbstractAttributeEditorWidgetType.prototype = {
    isActive: function() {
        return this.active;

    },

    activate: function(data)
    {
        var x = {
            test: function () {
                alert('This is ok');
            }
        };

        this.active = true;

    }

};

function test() {
    var x = 1;
    var y = function()
    {
        alert(1);
    }

    return x;

}

var myFunc = function()
{
    var x = 1;

    blah(x, y, function()
    {
        alert(2);
    }, z);

    blah(function() { alert(2); });

    return x;

}

HelpWidgetType.prototype = {
    init: function() {
    var x = 1;
    var y = {
        test: function() {
            alert(3);
        }
    }
    return x;

    }
}

CustomFormEditWidgetType.prototype = {

    addQuestion: function()
    {
        var settings = {
            default: ''
        };

    },

    addQuestionRulesEvent: function()
    {
        var self = this;

    }

};
