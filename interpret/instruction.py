from errors import *


class Instruction:
    def __init__(self, insXML):
        self.name = insXML.attrib['opcode']
        self.order = int(insXML.attrib['order'])
        self.arguments = []
        self.Instruction_args = {
            'MOVE': ["var", "symb"],
            'DEFVAR': ["var"],
            'CALL': ["label"],
            'RETURN': [],
            'PUSHS': ["symb"],
            'POPS': ["var"],
            'ADD': ["var", "symb", "symb"],
            'SUB': ["var", "symb", "symb"],
            'MUL': ["var", "symb", "symb"],
            'IDIV': ["var", "symb", "symb"],
            'DIV': ["var", "symb", "symb"],
            'LT': ["var", "symb", "symb"],
            'GT': ["var", "symb", "symb"],
            'OR': ["var", "symb", "symb"],
            'EQ': ["var", "symb", "symb"],
            'AND': ["var", "symb", "symb"],
            'GETCHAR': ["var", "symb", "symb"],
            'SETCHAR': ["var", "symb", "symb"],
            'CONCAT': ["var", "symb", "symb"],
            'NOT': ["var", "symb", "symb"],
            'STRLEN': ["var", "symb"],
            'INT2CHAR': ["var", "symb"],
            'STRI2INT': ["var", "symb", "symb"],
            'INT2FLOAT': [],
            'FLOAT2INT': [],
            'TYPE': ["var", "symb"],
            'LABEL': ["label"],
            'JUMP': ["label"],
            'JUMPIFEQ': ["label", "symb", "symb"],
            'JUMPIFNEQ': ["label", "symb", "symb"],
            'READ': ["var", "type"],
            'WRITE': ["symb"],
            'DPRINT': ["symb"],
            'BREAK': [],
            'CREATEFRAME': [],
            'PUSHFRAME': [],
            'POPFRAME': [],
            'EXIT': ["symb"],
            'CLEARS': [],
            'ADDS': [],
            'SUBS': [],
            'MULS': [],
            'IDIVS': [],
            'DIVS': [],
            'LTS': [],
            'GTS': [],
            'EQS': [],
            'ORS': [],
            'ANDS': [],
            'NOTS': [],
            'STRLENS': [],
            'INT2CHARS': [],
            'INT2FLOATS': [],
            'FLOAT2INTS': [],
            'JUMPIFEQS': [],
            'JUMPIFNEQS': [],
        }
        self.symb_type_list = ["var", "string", "int", "float", "nil", "bool"]
        self.typ = 0
        self.val = 1

        args = list(insXML)
        if args is not None:
            for aaa in range(0, self.arg_count(self.name)):
                arg = dict()
                arg['type'] = args[aaa].attrib['type']
                arg['value'] = args[aaa].text
                self.arguments.append(arg)
                # print(self.name,args[aaa].tag, args[aaa].text, args[aaa].attrib['type'])
        # print(self.order, self.name, self.arguments)
        pass

    """GET"""
    def argument_type(self, ins):
        return self.Instruction_args[ins]

    def arg_count(self, ins):
        return len(self.Instruction_args[ins])

    def is_var(self, idx):
        if self.arguments[idx]['type'] == "var":
            return True
        return False

    def is_var_exception(self, idx):
        if self.arguments[idx]['type'] != "var":
            raise OperandTypeException()

    """Verification"""
    def check_arg_count(self):
        """Checks whether number of arguments is correct for every instruction"""
        if len(self.arguments) == self.arg_count(self.name):
            return
        raise XMLStructureException()

    def check_arg_type(self):
        for idx in range(0, len(self.arguments)):
            if self.Instruction_args[self.name][idx] == self.arguments[idx]['type']:
                pass
            elif self.Instruction_args[self.name][idx] == "symb" and self.arguments[idx]['type'] in self.symb_type_list:
                pass
            else:
                print(self.order, self.arguments[idx]['type'])
                raise OperandTypeException()
            idx += 1

    def check_arguments(self):
        self.check_arg_count()
        self.check_arg_type()

    def get_value_from_frame(self, framestack):
        arg2 = self.arguments[1]['value'].split('@')
        value2 = arg2[self.val]
        type2 = arg2[self.typ]

        value_type_list = list()
        if type2 == "GF":
            value_type_list.append(framestack.GlobalFrame[value2][self.val])
            value_type_list.append(framestack.GlobalFrame[value2][self.typ])
        elif type2 == "LF":
            value_type_list.append(framestack.LocalFrame[value2][self.val])
            value_type_list.append(framestack.LocalFrame[value2][self.typ])
        elif type2 == "TF":
            value_type_list.append(framestack.TmpFrame[value2][self.val])
            value_type_list.append(framestack.TmpFrame[value2][self.typ])

        return value_type_list

    def save_to_frame(self, framestack):

        arg1 = self.arguments[0]['value'].split('@')
        value1 = arg1[self.val]
        frame_to_save = arg1[self.typ]

        arg2 = self.arguments[1]['value'].split('@')
        value2 = arg2[self.val]
        type2 = arg2[self.typ]

        """Zistí hodnotu premennej priamo z framestack-u"""
        if self.is_var(1): # 1 is index of second item in argument list
            value_to_save = list()
            value_to_save = self.get_value_from_frame(framestack)
            value2 = value_to_save[0]
            type2 = value_to_save[1]
            pass

        if frame_to_save == "GF":
            framestack.GlobalFrame[value1] = [value2, type2]
        elif frame_to_save == "LF":
            framestack.LocalFrame[value1] = [value2, type2]
        elif frame_to_save == "TF":
            framestack.TmpFrame[value1] = [value2, type2]
        else:
            raise XMLStructureException()  # $#$ - malo by to byt osetrenie chyby syntaktickej
        return
        """
        if value2[0] == "GF" or value2[0] == "LF" or value2[0] == "TF":
            value_to_save = list()
            value_to_save = get_value_from_frame(framestack)
            if frame_to_save == "GF":
                framestack.GlobalFrame[value1] = value_to_save
                pass
            elif frame_to_save == "LF":
                framestack.LocalFrame[value1] =  value_to_save
                pass
            elif frame_to_save == "TF":
                framestack.TmpFrame[value1] = [value_to_save
                pass
        """
    def run_instruction(self, framestack, ip_stack, labels):
        # sem pojde SWITCH s volanim na vykonanie instrukcie
        self.check_arguments()
        if self.name == "MOVE":
            self.save_to_variable(framestack)

        if self.name == "DEFVAR":
            if self.is_var(0):
                arg = self.arguments[0]['value'].split('@')
                value = arg[1]
                frame = arg[0]
                if frame == "GF":
                    framestack.GlobalFrame[value] = None
                elif frame == "LF":
                    framestack.LocalFrame[value] = None
                elif frame == "TF":
                    framestack.TmpFrame[value] = None
                else:
                    raise FrameNotDefinedException()
            else:
                raise OperandTypeException()
        if self.name == "CREATEFRAME":
            framestack.create_frame()
        if self.name == "PUSHFRAME":
            framestack.push_frame()
        if self.name == "POPFRAME":
            framestack.pop_frame()
        if self.name == "LABEL":
            if self.arguments[0]['value'] in labels:
                raise SemanticErrorLabel()
            labels[self.arguments[0]['value']] = self.order
        if self.name == "CALL":
            ip_stack.push_ip(self.order)
            print("VALUE", self.arguments[0]['value'])
            # ip_stack.ip = labels[self.arguments[0]['value']]
        if self.name == "RETURN":
            ip_stack.ip = ip_stack.pop_ip()
        # if self.name == "":
        # if self.name == "":

        if self.name in ["CREATEFRAME","PUSHFRAME", "POPFRAME",  "DEFVAR"] and 0:
            print(self.order, self.name)
            print("---------------------------------------------------")
            print("GF", framestack.GlobalFrame)
            print("LF", framestack.LocalFrame)
            print("TMPF", framestack.TmpFrame)
            print("framestack", framestack.frameStack)
            print("---------------------------------------------------")
