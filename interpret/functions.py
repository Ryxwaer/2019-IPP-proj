from Debug import *

###########################
# arg_count()
## Brief: counts number of arguments that instruction uses
## HDTDI:
#### Looks for and instuction in dictionary of instructions
#### and gets number of arguments necessary

"""
def arg_count(ins):
    Instruction_arg = {
        'MOVE': 2,
        'DEFVAR': 1,
        'CALL': 1,
        'RETURN': 0,
        'PUSHS': 1,
        'POPS': 1,
        'ADD': 3,
        'SUB': 3,
        'MUL': 3,
        'IDIV': 3,
        'DIV': 3,
        'LT': 3,
        'GT': 3,
        'OR': 3,
        'EQ': 3,
        'AND': 3,
        'GETCHAR': 3,
        'SETCHAR': 3,
        'CONCAT': 3,
        'NOT': 2,
        'STRLEN': 2,
        'INT2CHAR': 2,
        'STRI2INT': 3,
        'INT2FLOAT': 2,
        'FLOAT2INT': 2,
        'TYPE': 2,
        'LABEL': 1,
        'JUMP': 1,
        'JUMPIFEQ': 3,
        'JUMPIFNEQ': 3,
        'READ': 2,
        'WRITE': 1,
        'DPRINT': 1,
        'BREAK': 0,
        'CREATEFRAME': 0,
        'PUSHFRAME': 0,
        'POPFRAME': 0,
        'EXIT': 1,
        'CLEARS': 0,
        'ADDS': 0,
        'SUBS': 0,
        'MULS': 0,
        'IDIVS': 0,
        'DIVS': 0,
        'LTS': 0,
        'GTS': 0,
        'EQS': 0,
        'ORS': 0,
        'ANDS': 0,
        'NOTS': 0,
        'STRLENS': 2,
        'INT2CHARS': 2,
        'INT2FLOATS': 0,
        'FLOAT2INTS': 0,
        'JUMPIFEQS': 3,
        'JUMPIFNEQS': 3,
    }
    return Instruction_arg[ins]
"""

def argument_type(ins):
    return

def get_instructions(whole_program):
    instructions_array = [""]
    n = 0
    for instruction in whole_program:
        instructions_array.append(instruction.attrib)
        # print(instruction.tag, instruction.attrib)
        if debug:
            print(instructions_array[n])
        n += 1
    return instructions_array


def get_arguments(whole_program):
    arg = ["arg1", "arg2", "arg3"]
    arguments_array = []
    for a in arg:
        path = "instruction/" + a
        for argument in whole_program.findall(path):
            arguments_array.append(argument)
            # if debug:
                # print(argument.text, argument.attrib['type'])

    if debug:
        for x in arguments_array:
            if x == "":
                continue

            if debug:
                print(x.tag, x.attrib, x.text)

    return arguments_array

