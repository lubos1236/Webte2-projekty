import sys
import traceback
import sympy
from sympy.parsing.latex import parse_latex

def is_same(x, y):
    try:
        expected = parse_latex(x)
        actual = parse_latex(y)
    except Exception as e:
        print("Error parsing LaTeX:", e)
        traceback.print_exc()
        exit(1)

    if (type(expected) == sympy.Eq):
        expected = expected.args[1]
    if (type(actual) == sympy.Eq):
        actual = actual.args[1]

    # print("Expected:", expected)
    # print("Actual:", actual)

    a = sympy.nsimplify(expected, tolerance=0.001, rational=True)
    b = sympy.nsimplify(actual, tolerance=0.001, rational=True)

    # print("expected (after nsimplify):", a)
    # print("actual (after nsimplify):", b)

    a = sympy.simplify(a)
    b = sympy.simplify(b)

    # print("expected (after simplify):", a)
    # print("actual (after simplify):", b)

    c = sympy.Eq(a, b)

    # print("Equality:", c)
    # print("Equality:", c.doit())

    return c

if len(sys.argv) != 3:
    print("Usage: python validate.py <expected> <actual>")
    exit(1)

print(is_same(sys.argv[1], sys.argv[2]))
