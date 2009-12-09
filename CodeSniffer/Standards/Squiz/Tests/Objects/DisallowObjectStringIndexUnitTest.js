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

function test() {
    this.errors['step_' + step] = errors;
    this.errors['test'] = x;
    this.errors['test' + 10] = x;
    this.errors['test' + y] = x;
    this.errors['test' + 'blah'] = x;
    this.errors[y] = x;
    this.errors[y + z] = x;
    this.permissions['workflow.cancel'] = x;
}

if (child.prototype) {
    above.prototype['constructor'] = parent;
    child.prototype['super']       = new above();
}
