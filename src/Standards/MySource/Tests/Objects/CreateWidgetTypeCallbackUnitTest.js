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

        // Never good to return a value.
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

SampleWidgetType.prototype = {

    create: function(callback)
    {
        var something = callback;

    }

};

SampleWidgetType.prototype = {

    create: function(callback)
    {
        // Also valid because we are passing the callback to
        // someone else to call.
        if (y === 1) {
            this.something(callback);
            return;
        }

        this.init(callback);

    }

};

SampleWidgetType.prototype = {

    create: function(callback)
    {
        // Also valid because we are passing the callback to
        // someone else to call.
        if (y === 1) {
            this.something(callback);
        }

        this.init(callback);

    }

};

SampleWidgetType.prototype = {

    create: function(callback)
    {
        if (a === 1) {
            // This is ok because it is the last statement,
            // even though it is conditional.
            this.something(callback);
        }

    }

};


SampleWidgetType.prototype = {

    create: function(callback)
    {
        if (dfx.isFn(callback) === true) {
            callback.call(this, cont);
            return;
        }
    }

};


SampleWidgetType.prototype = {

    create: function(callback)
    {
        dfx.foreach(items, function(item) {
            return true;
        });

        if (dfx.isFn(callback) === true) {
            callback.call(this);
        }
    }

};

SampleWidgetType.prototype = {

    create: function(callback)
    {
        var self = this;
        this.createChildren(null, function() {
            callback.call(self, div);
            return;
        });
    }

};