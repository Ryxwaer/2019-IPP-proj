import sys
from errors import *


class FrameStack:
    def __init__(self):
        self.GF = 0
        self.active = -1
        self.frameStack = []

        self.GlobalFrame = {}
        self.LocalFrame = {}
        self.TmpFrame = None

        # self.frameStack.append({})  # set first frame

    def flush_local_frame(self):
        self.LocalFrame = dict()

    def get_global_frame(self):
        return self.frameStack[self.GF]

    def overwrite_tmp_frame(self):
        self.TmpFrame = dict()

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

