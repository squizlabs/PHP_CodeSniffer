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