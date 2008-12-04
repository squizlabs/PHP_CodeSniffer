

// Valid SWITCH statement.
switch (something) {
    case '1':
        myvar = '1';
    break;

    case '2':
    case '3':
        myvar = '5';
    break;

    case '4':
        myvar = '4';
    break;

    default:
        myvar = null;
    break;
}

// Alignment wrong.
switch (something) {
    case '1':
        myvar = '1';
        break;

case '2':
    case '3':
        myvar = '5';
    break;

case '4':
    myvar = '4';
break;

    default:
        myvar = null;
    break;
}

// Closing brace wrong.
switch (something) {
    case '1':
        myvar = '1';
    break;
    }

// PEAR style.
switch (something) {
case '1':
    myvar = '1';
    break;
case '2':
case '3':
    myvar = '5';
    break;
case '4':
    myvar = '4';
    break;
default:
    myvar = null;
    break;
}

// Valid, but missing BREAKS.
switch (something) {
    case '1':
        myvar = '1';

    case '2':
    case '3':
        myvar = '5';

    case '4':
        myvar = '4';

    default:
        myvar = null;
}

// Invalid, and missing BREAKS.
switch (something) {
    Case '1' :
        myvar = '1';

case  '2':
    case  '3' :
        myvar = '5';

    case'4':
        myvar = '4';

    Default :
        myvar = null;
        something = 'hello';
        other = 'hi';
    }

// Valid
switch (condition) {
    case 'string':
        varStr = 'test';

    default:
        // Ignore the default.
    break;
}

// No default comment
switch (condition) {
    case 'string':
        varStr = 'test';

    default:
    break;
}

// Break problems
switch (condition) {
    case 'string':


        varStr = 'test';

    break;


    case 'bool':
        varStr = 'test';


    break;
    default:

        varStr = 'test';
    break;

}

switch (var) {
    case 'one':
    case 'two':
    break;

    case 'three':
        // Nothing to do.
    break;

    case 'four':
        echo hi;
    break;

    default:
        // No default.
    break;
}

switch (var) {
    case 'one':
        if (blah) {
        }

    break;

    default:
        // No default.
    break;
}

switch (name) {
    case "1":
        switch (name2) {
            case "1":
                return true;
            break;

            case "2":
                return true;
            break;

            default:
                // No default.
            break;
        }
    break;

    case "2":
switch (name2) {
    case "1":
        return true;
    break;

    case "2":
        return true;
    break;

    default:
        // No default.
    break;
}
    break;
}

switch (name) {
    case "1":
        switch (name2) {
            case "1":
                return true;
            break;

            default:
                // No default.
            break;
        }
    break;

    default:
        // No default.
    break;
}

switch (name2) {
    default:
        // No default.
    break;
}