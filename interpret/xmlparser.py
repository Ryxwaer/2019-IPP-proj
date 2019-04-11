import xml.etree.ElementTree as ET
from errors import *
from instruction import *
import operator

class Validate:
    def __init__(self):
        self._string_pattern = "^string@(?:(\\[0-9]{3})|[^\s\\#])*$"
        self._integer_pattern = "/^int@[+-]?[0-9]+$"
        self._bool_pattern = "/^bool@(true|false)$"

        self.INT = 120
        self.STRING = 125
        self.BOOL = 130

        prog = re.compile(pattern)

    def validate(self, to_validate, type):
        result = None
        pattern_to_use = ''
        if type == self.INT:
            pattern_to_use = self._integer_pattern
        elif type == self.STRING:
            pattern_to_use = self._string_pattern
        elif type == self.BOOL:
            pattern_to_use = self._bool_pattern
        else:
            raise InternalErrorException()

        result = re.match(pattern_to_use, to_validate)
        if not result:
            raise XMLStructureException()


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
            try:
                tree = ET.parse(xml)
            except ET.ParseError:
                raise XMLStructureException()

        # Check the root
        program = tree.getroot()
        if program.tag != "program" or len(program.attrib) > 3 or program.attrib.get('language') != 'IPPcode19':
            raise XMLFormatException()

        instructions = []
        for instruction in program:
            if instruction.tag == 'instruction':
                self.inc_order()
                # if int(instruction.get("order")) == self.order: # kontrola poradia
                instructions.append(self.make_instruction(instruction))
            else:
                raise XMLStructureException()
                # print(self.order, int(instruction.get("order")), len(instructions))

        # print(instructions)
        return sorted(instructions, key=operator.attrgetter('order'))  # lambda order: 'order'

    def make_instruction(self, ins):
        ins_opcode = ins.attrib['opcode']
        ins_opcode = ins_opcode.upper()
        return Instruction(ins)
