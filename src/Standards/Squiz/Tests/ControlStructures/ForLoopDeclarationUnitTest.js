// Valid.
for (var i = 0; i < 10; i++) {
}

// Invalid.
for ( i = 0; i < 10; i++ ) {
}

for (i = 0;  i < 10;  i++) {
}

for (var i = 0 ; i < 10 ; i++) {
}

for (i = 0;i < 10;i++) {
}

// The works.
for ( var i = 0 ;  i < 10 ;  i++ ) {
}

this.formats = {};
dfx.inherits('ContentFormat', 'Widget');

for (var widgetid in this.loadedContents) {
    if (dfx.isset(widget) === true) {
        widget.loadAutoSaveCWidgetStore.setData('activeScreen', null);widget.getContents(this.loadedContents[widgetid], function() {self.widgetLoaded(widget.id);});
    }
}

for (var i = 0; i < 10;) {
}
for (var i = 0; i < 10; ) {
}

for (var i = 0; ; i++) {
}
for (var i = 0;; i++) {
}

// phpcs:set Squiz.ControlStructures.ForLoopDeclaration requiredSpacesAfterOpen 1
// phpcs:set Squiz.ControlStructures.ForLoopDeclaration requiredSpacesBeforeClose 1
for (var i = 0; i < 10; i++) {}
for ( var i = 0; i < 10; i++ ) {}
for (  var i = 0; i < 10; i++  ) {}
// phpcs:set Squiz.ControlStructures.ForLoopDeclaration requiredSpacesAfterOpen 0
// phpcs:set Squiz.ControlStructures.ForLoopDeclaration requiredSpacesBeforeClose 0

for (      ; i < 10; i++) {}
for (; i < 10; i++) {}

// phpcs:set Squiz.ControlStructures.ForLoopDeclaration requiredSpacesAfterOpen 1
// phpcs:set Squiz.ControlStructures.ForLoopDeclaration requiredSpacesBeforeClose 1
for ( ; i < 10; i++ ) {}
for (         ; i < 10; i++ ) {}
for (; i < 10; i++ ) {}

for ( i = 0; i < 10; ) {}
for ( i = 0; i < 10;) {}
for ( i = 0; i < 10;     ) {}
// phpcs:set Squiz.ControlStructures.ForLoopDeclaration requiredSpacesAfterOpen 0
// phpcs:set Squiz.ControlStructures.ForLoopDeclaration requiredSpacesBeforeClose 0

// Test handling of comments and inline annotations.
for ( /*phpcs:enable*/ i = 0 /*start*/ ;    /*end*/i < 10/*comment*/; i++ /*comment*/   ) {}

// Test multi-line FOR control structure.
for (
    i = 0;
    i < 10;
    i++
) {}

// Test multi-line FOR control structure with comments and annotations.
for (
    i = 0; /* Start */
    i < 10; /* phpcs:ignore Standard.Category.SniffName -- for reasons. */
    i++ // comment

) {}

// Test fixing each error in one go. Note: lines 84 + 88 contain trailing whitespace on purpose.
for (
     

      i = 0

      ; 

      i < 10

      ;

      i++


) {}

// phpcs:set Squiz.ControlStructures.ForLoopDeclaration requiredSpacesAfterOpen 1
// phpcs:set Squiz.ControlStructures.ForLoopDeclaration requiredSpacesBeforeClose 1
for (



      i = 0

      ;

      i < 10

      ;

      i++


) {}
// phpcs:set Squiz.ControlStructures.ForLoopDeclaration requiredSpacesAfterOpen 0
// phpcs:set Squiz.ControlStructures.ForLoopDeclaration requiredSpacesBeforeClose 0

// Test with semi-colon not belonging to for.
for (i = function() {self.widgetLoaded(widget.id)  ;  }; i < function() {self.widgetLoaded(widget.id);}; i++) {}
for (i = function() {self.widgetLoaded(widget.id);}; i < function() {self.widgetLoaded(widget.id);}  ;   i++) {}

// This test has to be the last one in the file! Intentional parse error check.
for
