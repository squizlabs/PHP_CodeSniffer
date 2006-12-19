<?php


//  Correct declarations.
class CorrectClassDeclaration
{

}

class CorrectClassDeclarationWithExtends extends correctClassDeclaration
{

}

class CorrectClassDeclarationWithImplements implements correctClassDeclaration
{

}


// Incorrect placement of opening/closing braces, including indent.
class IncorrectBracePlacement {}
class IncorrectBracePlacementWithExtends extends correctClassDeclaration {}
class IncorrectBracePlacementWithImplements implements correctClassDeclaration {}


    class IncorrectIndentedClass
    {

    }


// Incorrect code placement for opening/closing brace.
class IncorrectCodeAfterOpeningBrace
{ echo phpinfo();

}

class IncorrectCodeAfterClosingBrace
{

} echo phpinfo();


class IncorrectCodeBeforeClosingBrace
{

echo phpinfo(); }

    class IncorrectIndentedClass
{

}

class ClassOne extends ClassTwo, ClassThree
{
}

class ClassOne extends ClassTwo,ClassThree
{
}

?>