from functions import arg_count


class Instruction:
    def __init__(self, insXML):
        self.name = insXML.attrib['opcode']
        self.order = int(insXML.attrib['order'])
        self.arguments = []
        args = list(insXML)
        arg = {}
        if args is not None:
            if len(args) == arg_count(self.name):
                for i in range(0, arg_count(self.name)):
                    arg['type'] = args[0].attrib['type']
                    arg['value'] = args[0].text
                    self.arguments.append(arg)
                    # print(args[0].text, args[0].tag, args[0].attrib['type'])
            else:
                raise

        # print(self.name, self.order, self.arguments)
        pass

    """"def make_instruction(self, ins):
        self.name = ins.attrib['opcode']
        self.order = ins.attrib['order']
        self.arguments = []
        args = list(ins)
        if args is not None:
            if len(args) > 0:
                print(args[1].text, args[0].tag, args[0].attrib['type'])

    """

