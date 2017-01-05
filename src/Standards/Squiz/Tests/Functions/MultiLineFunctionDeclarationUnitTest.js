
function someFunctionWithAVeryLongName(firstParameter='something',
   secondParameter='booooo',
   third=null, fourthParameter=false,
     fifthParameter=123.12,
    sixthParam=true
){
}

function someFunctionWithAVeryLongName2(
firstParameter='something',
secondParameter='booooo',
)  {
}

function blah() {
}

function blah()
{
}

var object =
{

    someFunctionWithAVeryLongName: function(
        firstParameter='something',
        secondParameter='booooo',
        third=null,
        fourthParameter=false,
        fifthParameter=123.12,
        sixthParam=true
    ) /** w00t */ {
    }

    someFunctionWithAVeryLongName2: function (firstParameter='something',
        secondParameter='booooo',
        third=null
    ) {
    }

     someFunctionWithAVeryLongName3: function (
         firstParameter, secondParameter, third=null
     ) {
     }

     someFunctionWithAVeryLongName4: function (
         firstParameter, secondParameter
     ) {
     }

     someFunctionWithAVeryLongName5: function (
         firstParameter,
         secondParameter=array(1,2,3),
         third=null
     ) {
     }

}

var a = Function('return 1+1');

class test
{
    myFunction() {
       return false;
    }

    myFunction2()
    {
       return false;
    }
}
