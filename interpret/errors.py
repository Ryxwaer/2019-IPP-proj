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


class MissingParamException(Exception):  # 10
    pass


class OpenInputFileException(Exception):  # 11
    pass


class OpenSourceFileException(Exception):  # 12
    pass


class XMLFormatException(Exception):  # 31
    pass


class XMLStructureException(Exception):  # 32
    pass


class SemanticErrorLabel(Exception):  # 52
    pass


class OperandTypeException(Exception):  # 53
    pass


class NonExistingVariableException(Exception):  # 54
    pass


class FrameNotDefinedException(Exception):  # 55
    pass


class MissingValueException(Exception):  # 56
    pass


class ValueOperandException(Exception):  # 57
    pass


class StringException(Exception):  # 58
    pass


class InternalErrorException(Exception):  # 99
    pass
