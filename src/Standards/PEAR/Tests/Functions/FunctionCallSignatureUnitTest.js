test(
);
test();
test(arg, arg2);
test ();
test( );
test() ;
test( arg);
test( arg );
test ( arg );

if (foo(arg) === true) {

}

var something = get(arg1, arg2);
var something = get(arg1, arg2) ;
var something = get(arg1, arg2)   ;

make_foo(string/*the string*/, true/*test*/);
make_foo(string/*the string*/, true/*test*/ );
make_foo(string /*the string*/, true /*test*/);
make_foo(/*the string*/string, /*test*/true);
make_foo( /*the string*/string, /*test*/true);

// phpcs:set PEAR.Functions.FunctionCallSignature requiredSpacesAfterOpen 1
// phpcs:set PEAR.Functions.FunctionCallSignature requiredSpacesBeforeClose 1
test(arg, arg2);
test( arg, arg2 );
test(  arg, arg2  );
// phpcs:set PEAR.Functions.FunctionCallSignature requiredSpacesAfterOpen 0
// phpcs:set PEAR.Functions.FunctionCallSignature requiredSpacesBeforeClose 0

this.init = function(data) {
    a.b('').a(function(itemid, target) {
        b(
            itemid,
            target,
            {
                reviewData: _reviewData,
                pageid: itemid
            },
            '',
            function() {
                var _showAspectItems = function(itemid) {
                    a.a(a.c(''), '');
                    a.b(a.c('-' + itemid), '');
                };
                a.foo(function(itemid, target) {
                    _foo(itemid);
                });
            }
        );
    });
};

a.prototype = {

    a: function()
    {
        this.addItem(
            {
            /**
             * @return void
             */
            a: function()
            {

            },
        /**
 * @return void
             */
            a: function()
            {

            },
            }
        );
    }
};
