from errors import FrameNotDefinedException


class FrameStack:
    def __init__(self):
        self.GF = 0
        self.active = -1

        self.GlobalFrame = {}
        self.frameStack = []
        self.TmpFrame = None

    def create_frame(self):
        self.TmpFrame = dict()
        # self.overwrite_tmp_frame()

    def push_frame(self):
        if self.TmpFrame is None:
            raise FrameNotDefinedException()
            pass
        self.frameStack.append(self.TmpFrame)
        # self.TmpFrame = None #$#

    def pop_frame(self):
        # treba pridat podmienku na 0
        if len(self.frameStack) == 0:
            raise FrameNotDefinedException()
        # vrcholovy LF frame presunut do TmpFrame
        self.TmpFrame = self.frameStack.pop()
