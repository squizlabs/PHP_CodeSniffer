var x = {
    a: function () {
        alert('thats right')  ;
        x = (x?a:x) ;
    },
} ;

id = id.replace(/row\/:;/gi, '');

for (i=0   ; i<3 ; i++) {
   for (j=0; j<5 ; j++) {
      if (j==x)
         break ;
   }
}
alert('hi');