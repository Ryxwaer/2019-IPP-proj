import sys
from errors import Errors


class FrameStack:
    def __init__(self):
        self.eee = Errors()
        self.GF = 0
        self.active = 0
        self.frameStack = []

        self.GlobalFrame = {}
        self.LocalFrame = {}
        self.TmpFrame = {}

        self.frameStack.append({})  # set first frame

    def get_global_frame(self):
        return self.frameStack[self.GF]

    def overwrite_tmp_frame(self):
        self.TmpFrame = {}

    def create_frame(self):
        # vynulovat TmpFrame a vlozit ho na zasobnik
        self.overwrite_tmp_frame()
        # self.active += 1
        self.frameStack.append(self.TmpFrame)

    def push_frame(self):
        if self.TmpFrame is None:
            pass
            sys.exit(self.eee.frame_not_defined)
        self.frameStack.append(self.TmpFrame)
        self.TmpFrame = None

    def pop_frame(self):
        # treba pridat podmienku na 0
        # vrcholovy LF frame presunut do TmpFrame
        self.TmpFrame = self.frameStack.pop()
        if self.TmpFrame is None:
            pass
            sys.exit(self.eee.frame_not_defined)

        # if self.active != 0:
        #    self.active -= 1

    # def get_stack_frame(self):
    #    return self.frameStack

    # def get_active_frame(self):
    #    return self.frameStack[self.active]

    # def get_active_frame_pointer(self):
    #    return self.active

    # def get_global_frame_pointer(self):
    #    return self.GF

    # def set_active_frame(self, new_active):
    #    self.active = new_active

