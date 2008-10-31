SampleWidgetType.prototype = {

    create: function(callback)
    {
        if (x === 1) {
            return;
        }

        if (y === 1) {
            callback.call(this);
            // A comment here to explain the return is okay.
            return;
        }

        if (a === 1) {
            // Cant return value even after calling callback.
            callback.call(this);
            return something;
        }

        if (a === 1) {
            // Need to pass self or this to callback function.
            callback.call(a);
        }

        callback.call(self);

        var self = this;
        this.createChildren(null, function() {
            callback.call(self, div);
        });

        // Never good to return a vaue.
        return something;

        callback.call(self);
    }

};

AnotherSampleWidgetType.prototype = {

    create: function(input)
    {
        return;
    }

    getSomething: function(input)
    {
        return 1;
    }

};


NoCreateWidgetType.prototype = {

    getSomething: function(input)
    {
        return;
    }

};


SomeRandom.prototype = {

    create: function(input)
    {
        return;
    }

};

SampleWidgetType.prototype = {

    create: function(callback)
    {
        if (a === 1) {
            // This is ok because it is the last statement,
            // even though it is conditional.
            callback.call(self);
        }

    }

};