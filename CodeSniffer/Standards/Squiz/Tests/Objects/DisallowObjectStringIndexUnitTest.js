function test(id)
{

    this.id = id;

}

test.prototype = {
    init: function()
    {
        var x      = {};
        x.name     = 'test';
        x['phone'] = 123124324;
        var t      = ['test', 'this'].join('');
        var y      = ['test'].join('');
        var a      = x[0];
        var z      = x[x['name']];
        var p      = x[x.name];
    }

};
