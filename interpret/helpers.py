class Helpers:
    def __init__(self):
        self.arg1_f = 0
        self.arg2_f = 1
        self.arg3_f = 1

        self.one_arg = 1
        self.two_arg = 2
        self.three_arg = 3

    def increment(self, num):
        if num == 0:
            pass
        elif num == 1:
            self.arg1_f += 1
            pass
        elif num == 2:
            self.arg1_f += 1
            self.arg2_f += 1
            pass
        elif num == 3:
            self.arg1_f += 1
            self.arg2_f += 1
            self.arg3_f += 1
            pass
