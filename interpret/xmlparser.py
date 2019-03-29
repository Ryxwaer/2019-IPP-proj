import xml.etree.ElementTree as ET
from errors import *
from instruction import *
import operator

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
                # if int(instruction.get("order")) == self.order: # kontrola poradia
                instructions.append(self.make_instruction(instruction))
            else:
                raise XMLFormatException()
                # print(self.order, int(instruction.get("order")), len(instructions))

        # print(instructions)
        return sorted(instructions, key=operator.attrgetter('order'))  # lambda order: 'order'

    def make_instruction(self, ins):
        ins_opcode = ins.attrib['opcode']
        ins_opcode = ins_opcode.upper()
        return Instruction(ins)
