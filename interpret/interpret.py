import argparse
import xml.etree.ElementTree as ET
import sys
from errors import *
# from instruction import Instruction

from framestack import FrameStack


class ArgumentType:
    VAR = 1
    LABEL = 2
    STR = 3
    INT = 4
    FLOAT = 5
    TYPE = 6
    NIL = 7


class XMLParser:
    def __init__(self):
        self.order = 0

    def inc_order(self):
        self.order += 1

    def ParseXml(self, xml, reading_from):
        if reading_from == 2:  # String
            try:
                tree = ET.ElementTree(ET.fromstring(xml))
            except ET.ParseError:
                raise XMLStructureException()
        else:  # File
            tree = ET.parse(xml)

        # Check the root
        program = tree.getroot()
        if program.tag != "program" or len(program.attrib) > 3 or program.attrib.get('language') != 'IPPcode19':
            raise XMLFormatException()

        instructions = []
        for instruction in program:
            if instruction.tag == 'instruction':
                self.inc_order()
                if int(instruction.get("order")) == self.order:
                    instructions.append(self.make_instruction(instruction))
                else:
                    raise XMLFormatException()
                # print(self.order, int(instruction.get("order")), len(instructions))

        # print(instructions)
        return instructions

    def make_instruction(self, ins):
        ins_opcode = ins.attrib['opcode']
        ins_opcode = ins_opcode.upper()
        if ins_opcode == "MOVE":
            pass


def main():
    file_flag = 1
    string_flag = 2

    argument_parser = argparse.ArgumentParser()
    argument_parser.add_argument("--source", nargs=1, required=False)
    argument_parser.add_argument("--input", nargs=1, required=False)
    args = argument_parser.parse_args()

    if args.input is not None:
        input_file_name = args.input[0]
        try:
            input_file = open(input_file_name, "r")
        except IOError:
            print("Couldn't open the file", input_file_name, "\n")
            raise OpenInputFileException()
    else:
        """Here should be some flag to change where the input should come from"""
        pass

    if args.source is not None:
        source_file_name = args.source[0]
        try:
            source_file = open(source_file_name, "r")
            reading_from = file_flag
        except IOError:
            print("Couldn't open the file", source_file_name, "\n")
            raise OpenSourceFileException()
    else:
        source_file = sys.stdin.read()
        reading_from = string_flag

    xml_parser = XMLParser()
    instructions = xml_parser.ParseXml(source_file, reading_from)
    # print(instructions)


if __name__ == "__main__":
    error = Errors()
    try:
        main()
    except MissingParamException:
        sys.exit(error.missing_param)
    except OpenInputFileException:
        sys.exit(error.open_input_file_failed)
    except OpenSourceFileException:
        sys.exit(error.open_source_file_failed)
    except XMLFormatException:
        sys.exit(error.xml_wrong_format)
    except XMLStructureException:
        sys.exit(error.xml_lex_or_sem_err)
    except FrameNotDefinedException:
        sys.exit(error.frame_not_defined)
    except InternalErrorException:
        sys.exit(error.internal_error)
    except SystemExit as ex:
        if ex.code != 0:
            raise MissingParamException()
        sys.exit(ex.code)
    except BaseException:
        raise InternalErrorException()
    sys.exit(0)
    # ^^^ Go ^up^, just go ^up^, don't be so low. ^^^
