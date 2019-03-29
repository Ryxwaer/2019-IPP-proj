# Error definition
class Errors:
    def __init__(self):
        self.missing_param = 10
        self.open_input_file_failed = 11
        self.open_source_file_failed = 12

        self.xml_wrong_format = 31
        self.xml_lex_or_sem_err = 32

        self.semantic_error_nondefined_label = 52
        self.wrong_operand_type = 53
        self.non_existing_variable = 54
        self.frame_not_defined = 55
        self.missing_value = 56
        self.value_operand = 57
        self.working_with_string = 58

        self.internal_error = 99


class MissingParamException(Exception):
    pass


class OpenInputFileException(Exception):
    pass


class OpenSourceFileException(Exception):
    pass


class XMLFormatException(Exception):
    pass


class XMLStructureException(Exception):
    pass


class FrameNotDefinedException(Exception):
    pass


class InternalErrorException(Exception):
    pass


class SemanticErrorLabel(Exception):
    pass


class OperandTypeException(Exception):
    pass


class NonExistingVariableException(Exception):
    pass


class NonExistingFrameException(Exception):
    pass


class MissingValueException(Exception):
    pass


class ValueOperandException(Exception):
    pass


class StringException(Exception):
    pass
