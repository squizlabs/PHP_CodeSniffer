/* testNonUnaryPlus */
result = 1 + 2;

/* testNonUnaryMinus */
result = 1-2;

/* testUnaryMinusColon */
$.localScroll({offset: {top: -32}});

switch (result) {
	/* testUnaryMinusCase */
	case -1:
		break;
}

/* testUnaryMinusInlineIf */
result = x?-y:z;

/* testUnaryPlusInlineThen */
result = x ? y : +z;

/* testUnaryMinusInlineLogical */
if (true || -1 == b) {}
