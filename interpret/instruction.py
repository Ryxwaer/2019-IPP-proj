from interpret import ArgumentType


class Instruction:
    def __init__(self):
        self.name = ""
        self.order = -1
        self.arguments = []
        pass

    def make_instruction(self, ins):
        self.name = ins.attrib['opcode']
        self.order = ins.attrib['order']
        self.arguments = []
        args = list(ins)
        if args is not None:
            if len(args) > 0:
                print(args[1].text, args[0].tag, args[0].attrib['type'])

class MoveIns(Instruction):


