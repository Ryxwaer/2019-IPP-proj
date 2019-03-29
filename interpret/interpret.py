import argparse
import sys
from errors import *
from framestack import FrameStack
from xmlparser import XMLParser


class ArgumentType:
    VAR = 1
    LABEL = 2
    STR = 3
    INT = 4
    FLOAT = 5
    TYPE = 6
    NIL = 7


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
    for idx in range(0, 110):
        print(instructions[idx].order, instructions[idx].name, instructions[idx].arguments)


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
    except SemanticErrorLabel:
        sys.exit(error.semantic_error_nondefined_label)
    except OperandTypeException:
        sys.exit(error.wrong_operand_type)
    except NonExistingVariableException:
        sys.exit(error.missing_value)
    except NonExistingFrameException:
        sys.exit(error.frame_not_defined)
    except MissingValueException:
        sys.exit(error.missing_value)
    except ValueOperandException:
        sys.exit(error.missing_value)
    except StringException:
        sys.exit(error.working_with_string)
    except SystemExit as ex:
        if ex.code != 0:
            raise MissingParamException()
        sys.exit(ex.code)
    except BaseException:
        raise InternalErrorException()
    sys.exit(0)
    # ^^^ Go ^up^, just go ^up^, don't be so low. ^^^
