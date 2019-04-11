from errors import *
import sys
import re


class Instruction:
    def __init__(self, insXML):
        self.name = insXML.attrib['opcode'].upper()
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
        self.symb_type_list = ["string", "int", "float", "nil", "bool", "GF", "LF", "TF"]
        self.var_type_list = ["GF", "LF", "TF"]
        self.type_type_list = ["type"]
        self.typ = 0
        self.val = 1
        self.symb_idx0 = 0
        self.symb_idx1 = 1
        self.symb_idx2 = 2

        args = list(insXML)
        if args is not None:
            for aaa in range(0, self.arg_count(self.name)):
                arg = dict()
                arg['type'] = args[aaa].attrib['type']
                arg['value'] = args[aaa].text
                if arg['type'] == "var":
                    arg['type'] = args[aaa].text.split('@')[0]
                    arg['value'] = args[aaa].text.split('@')[1]
                self.arguments.append(arg)
                # print(self.name,args[aaa].tag, args[aaa].text, args[aaa].attrib['type'])
        # print(self.order, self.name, self.arguments)
        pass

    """GET"""

    def arg_count(self, ins):
        return len(self.Instruction_args[ins])

    def is_var(self, idx):
        if self.arguments[idx]['type'] in self.var_type_list:
            return True
        return False

    """Verification"""

    def check_arg_count(self):
        """Checks whether number of arguments is correct for every instruction"""
        if len(self.arguments) == self.arg_count(self.name):
            return
        raise XMLFormatException()

    def check_arg_type(self):
        for idx in range(0, len(self.arguments)):
            if self.Instruction_args[self.name][idx] == self.arguments[idx]['type']:
                pass
            elif self.Instruction_args[self.name][idx] == "var" and self.arguments[idx]['type'] in self.var_type_list:
                pass
            elif self.Instruction_args[self.name][idx] == "symb" and self.arguments[idx]['type'] in self.symb_type_list:
                pass
            elif self.Instruction_args[self.name][idx] == "type" and self.arguments[idx]['type'] in self.type_type_list:
                pass
            else:
                print(self.order, self.arguments[idx]['type'])
                raise OperandTypeException()
            idx += 1

    def check_arguments(self):
        self.check_arg_count()
        self.check_arg_type()

    def get_symb_value(self, idx, framestack):
        symb1_value = self.arguments[idx]['value']
        symb1_type = self.arguments[idx]['type']

        if self.is_var(idx):  # 1 is index of second item in argument list
            symb1 = self.get_value_from_frame(framestack, idx)
            symb1_value = symb1[0]
            symb1_type = symb1[1]
        return symb1_value, symb1_type

    def get_value_from_frame(self, framestack, idx):
        variable = self.arguments[idx]['value']
        frame = self.arguments[idx]['type']

        value_type_list = list()
        if frame == "GF":
            if variable in framestack.GlobalFrame:
                value_type_list.append(framestack.GlobalFrame[variable])
                # value_type_list.append(framestack.GlobalFrame[variable])
            else:
                raise NonExistingVariableException()
        elif frame == "LF":
            if variable in framestack.frameStack[framestack.active]:
                value_type_list.append(framestack.frameStack[framestack.active][variable])
                # value_type_list.append(framestack.LocalFrame[variable])
            else:
                raise NonExistingVariableException()
        elif frame == "TF":
            if variable in framestack.TmpFrame:
                value_type_list.append(framestack.TmpFrame[variable])
                # value_type_list.append(framestack.TmpFrame[variable])
            else:
                raise NonExistingVariableException()
        return value_type_list

    def save_value(self, framestack, variable_name, frame_to_save, value_to_save):
        if frame_to_save == "GF":
            framestack.GlobalFrame[variable_name] = value_to_save
        elif frame_to_save == "LF":
            if framestack.frameStack:
                framestack.frameStack[framestack.active][variable_name] = value_to_save
        elif frame_to_save == "TF":
            framestack.TmpFrame[variable_name] = value_to_save
        else:
            raise XMLStructureException()  # $#$ - malo by to byt osetrenie chyby syntaktickej

    def save_to_variable(self, framestack):
        value1 = self.arguments[0]['value']
        frame_to_save = self.arguments[0]['type']

        # value2 = self.arguments[1]['value']
        # type2 = self.arguments[1]['type']
        value_to_save = []
        """Zistí hodnotu premennej priamo z framestack-u"""
        if self.is_var(self.symb_idx1):  # 1 is index of second item in argument list
            value_to_save = self.get_value_from_frame(framestack, self.symb_idx1)
            pass

        self.save_value(framestack, value1, frame_to_save, value_to_save)

        """
        if frame_to_save == "GF":
            framestack.GlobalFrame[value1] = [value2, type2]
        elif frame_to_save == "LF":
            framestack.LocalFrame[value1] = [value2, type2]
        elif frame_to_save == "TF":
            framestack.TmpFrame[value1] = [value2, type2]
        else:
            raise XMLStructureException()  # $#$ - malo by to byt osetrenie chyby syntaktickej
        """
        return

    def must_be(self, expected, given):
        if expected != given:
            raise OperandTypeException()
        return

    def arithmetic_operation(self):
        pass

    def run_instruction(self, framestack, ip_stack, labels, data_stack):
        """sem pojde SWITCH s volanim na vykonanie instrukcie"""
        self.check_arguments()
        if self.name == "MOVE":
            self.save_to_variable(framestack)

        if self.name == "DEFVAR":
            if self.is_var(0):
                value = self.arguments[0]['value']
                frame = self.arguments[0]['type']

                if frame == "GF":
                    framestack.GlobalFrame[value] = None
                elif frame == "LF":
                    if framestack.frameStack:
                        framestack.frameStack[framestack.active][value] = None
                    else:
                        raise FrameNotDefinedException()
                elif frame == "TF":
                    if framestack.TmpFrame:
                        framestack.TmpFrame[value] = None
                    else:
                        raise FrameNotDefinedException()
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
            """Vytvori zaznam o navesti"""
            label_name = self.arguments[0]['value']
            if label_name in labels:
                raise SemanticErrorLabel()
            labels[label_name] = self.order + 1  # because I need the next one to be jumped to
            print(labels)

        if self.name == "CALL":
            ip_stack.push_ip(self.order)
            if self.arguments[0]['value'] in labels:
                ip_stack.ip = labels[self.arguments[0]['value']]
            print("VALUE", self.arguments[0]['value'], ip_stack.ip)
        if self.name == "RETURN":
            ip_stack.ip = ip_stack.pop_ip()

        if self.name == "PUSHS":
            data_stack.pushs([self.arguments[0]['type'], self.arguments[0]['value']])
            pass
        if self.name == "POPS":
            value = self.arguments[0]['value']
            frame_to_save = self.arguments[0]['type']
            self.save_value(framestack, value, frame_to_save, data_stack.pops())
            pass
        if self.name in ["ADD", "SUB", "MUL", "IDIV", "AND", "OR", "CONCAT", "GETCHAR", "SETCHAR"]:
            # self.arithmetic_operation()
            variable = self.arguments[0]['value']
            frame_to_save = self.arguments[0]['type']

            symb1_value = self.arguments[self.symb_idx1]['value']
            symb1_type = self.arguments[self.symb_idx1]['type']

            if self.is_var(self.symb_idx1):  # 1 is index of second item in argument list
                symb1 = self.get_value_from_frame(framestack, self.symb_idx1)
                symb1_value = symb1[0]
                symb1_type = symb1[1]

            symb2_value = self.arguments[self.symb_idx2]['value']
            symb2_type = self.arguments[self.symb_idx2]['type']

            if self.is_var(self.symb_idx2):
                symb2 = self.get_value_from_frame(framestack, self.symb_idx2)
                symb2_value = symb2[0]
                symb2_type = symb2[1]

            self.must_be("int", symb1_type)
            self.must_be("int", symb2_type)

            value = 0
            val_type = "int"
            if self.name == "ADD":
                value = symb1_value + symb2_value
            elif self.name == "SUB":
                value = symb1_value - symb2_value
            elif self.name == "MUL":
                value = symb1_value * symb2_value
            elif self.name == "IDIV":
                if symb2_value == 0:
                    raise ValueOperandException()
                value = symb1_value / symb2_value
            elif self.name == "AND":
                value = symb1_value and symb2_value
                val_type = "bool"
            elif self.name == "OR":
                value = symb1_value or symb2_value
                val_type = "bool"
            elif self.name == "CONCAT":
                value = symb1_value + symb2_value
                val_type = "string"
            elif self.name == "GETCHAR":
                if symb2_value > len(symb1_value):
                    raise StringException()
                value = symb1_value[symb2_value]
                val_type = "string"
            elif self.name == "SETCHAR":
                var = self.get_value_from_frame(framestack, self.symb_idx0)
                var_value = var[0]
                var_type = var[1]
                if symb1_value > len(var_value):  # indexace mimo string
                    raise StringException()
                string_list = list(var_value)
                string_list[symb1_value] = symb2_value
                value = ''.join(string_list)
                val_type = "string"
            elif self.name == "STR2INT":
                if symb2_value > len(symb1_value):
                    raise StringException()
                value = ord(symb1_value[symb2_value])
                pass
            self.save_value(framestack, variable, frame_to_save, [value, val_type])
            pass
        if self.name == "NOT":
            variable = self.arguments[0]['value']
            frame_to_save = self.arguments[0]['type']

            symb1_value = self.arguments[self.symb_idx1]['value']
            symb1_type = self.arguments[self.symb_idx1]['type']
            if self.is_var(self.symb_idx1):  # 1 is index of second item in argument list
                symb1 = self.get_value_from_frame(framestack, self.symb_idx1)
                symb1_value = symb1[0]
                symb1_type = symb1[1]
            value = not symb1_value
            self.save_value(framestack, variable, frame_to_save, [value, "bool"])
            pass
        if self.name == "READ":
            """Doplnit read z INPUT suboru"""
            variable = self.arguments[0]['value']
            frame_to_save = self.arguments[0]['type']

            value_type = self.arguments[1]['value']
            value = input()
            if value_type == "int":
                try:
                    value += 0
                except TypeError:
                    value = 0
            if value_type == "string":
                # value = "" # kedy moze byt vstup od uzivatela nie stringoveho typu????
                pass
            elif value_type == "bool":
                if value in ["True", "true", 1]:
                    value = "true"
                elif value_type in ["False", "false", 0]:
                    value = "false"
                else:
                    value = "false"  # dafault value

            self.save_value(framestack, variable, frame_to_save, [value, value_type])
            pass
        if self.name == "WRITE":
            symb = []
            value = self.arguments[0]['value']
            arg_type = self.arguments[0]['type']
            if self.is_var(self.symb_idx0):
                symb = self.get_value_from_frame(framestack, self.symb_idx0)
                value = symb[0]

            if arg_type == "int":
                print(value)
            if arg_type == "float":
                print(value)
            if arg_type == "bool":
                print(bool(value))  # co sa ma vypisat pri bool????
        if self.name == "STRLEN":
            variable = self.arguments[0]['value']
            frame_to_save = self.arguments[0]['type']

            symb1_value = self.arguments[self.symb_idx1]['value']
            symb1_type = self.arguments[self.symb_idx1]['type']

            if self.is_var(self.symb_idx1):  # 1 is index of second item in argument list
                symb1 = self.get_value_from_frame(framestack, self.symb_idx1)
                symb1_value = symb1[0]
                symb1_type = symb1[1]

            value = len(symb1_value)
            value_type = "int"

            self.save_value(framestack, variable, frame_to_save, [value, value_type])
            pass

        if self.name == "INT2CHAR":
            variable = self.arguments[0]['value']
            frame_to_save = self.arguments[0]['type']

            # symb1_value, symb1_type = self.get_symb_value(self.symb_idx1, framestack)

            symb1_value = self.arguments[self.symb_idx1]['value']
            symb1_type = self.arguments[self.symb_idx1]['type']

            if self.is_var(self.symb_idx1):  # 1 is index of second item in argument list
                symb1 = self.get_value_from_frame(framestack, self.symb_idx1)
                symb1_value = symb1[0]
                symb1_type = symb1[1]

            try:
                chr(symb1_value)
            except ValueError:
                raise StringException()

            value = chr(symb1_value)
            value_type = "string"
            self.save_value(framestack, variable, frame_to_save, [value, value_type])

            pass
        # if self.name == "":
        # if self.name == "":
        # if self.name == "":
        # if self.name == "":
        # if self.name == "":
        # if self.name == "":
        if self.name == "TYPE":
            variable = self.arguments[0]['value']
            frame_to_save = self.arguments[0]['type']

            symb1_value = self.arguments[self.symb_idx1]['value']
            symb1_type = self.arguments[self.symb_idx1]['type']
            if self.is_var(self.symb_idx1):  # 1 is index of second item in argument list
                symb1 = self.get_value_from_frame(framestack, self.symb_idx1)
                symb1_value = symb1[0]
                symb1_type = symb1[1]

            if symb1_type is None:
                val_type = ""
            else:
                val_type = symb1_type
            self.save_value(framestack, variable, frame_to_save, [val_type, "string"])
            pass
        if self.name == "JUMP":
            pass
        if self.name in ["JUMPIFEQ", "JUMPIFNEQ"]:
            pass
        if self.name == "EXIT":
            value = int(self.arguments[0]['value'])
            arg_type = self.arguments[0]['type']
            if self.is_var(self.symb_idx0):
                symb = self.get_value_from_frame(framestack, self.symb_idx0)
                value = int(symb[0])
                arg_type = symb[1]

            self.must_be("int", arg_type)

            if 0 <= value <= 49:
                sys.exit(value)
            else:
                raise ValueOperandException()
        if self.name == "DPRINT":
            value = self.arguments[0]['value']
            if self.is_var(self.symb_idx0):
                symb = self.get_value_from_frame(framestack, self.symb_idx0)
                value = symb[0]
            print(value, file=sys.stderr)

        if self.name == "BREAK":
            print(self.order, file=sys.stderr)
            print("GF:", framestack.GlobalFrame, file=sys.stderr)
            print("Active LocalFrame:", framestack.frameStack, file=sys.stderr)
            print("Temporary Frame", framestack.TmpFrame, file=sys.stderr)
            pass


        """
        if self.name in ["CREATEFRAME","PUSHFRAME", "POPFRAME",  "DEFVAR"] and 0:
            print(self.order, self.name)
            print("---------------------------------------------------")
            print("GF", framestack.GlobalFrame)
            print("LF", framestack.LocalFrame)
            print("TMPF", framestack.TmpFrame)
            print("framestack", framestack.frameStack)
            print("---------------------------------------------------")
        """
