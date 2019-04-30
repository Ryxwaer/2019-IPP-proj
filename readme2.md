Documentation of Project Implementation for IPP 2018/2019  
Name and surname: Jakub Sencak  
Login: xsenca00  

Interpret
=========

## Description

This is documentation of project written in python3.6.x, which was given to me as mission from heaven. I was given to do an interpret of IPPcode19.

## Used libraries

sys, argparse, xml and operator

## Usage

```python3 interpret.py <<parameters>>```  
Parameters are --source="file.xml" and --help. If there is no source file given, input from stdin is used.

## Files

### interpret.py

This file contains ```main()```. Every exception that has been raised is caught here and special value is returned. 

### errors.py

Contains ```Errors``` class that has all return codes for the interpret. It also containes all other classes that inherit from ```Exception``` class. Those are used to create and catch exceptions.

### xmlparser.py

Contains two classes ```XMLParser``` and ```Validate```.  
XMLParser class uses ```xml``` library to parse source file. For every processed instruction, a new instance from class ```Instruction``` is created.

Validate class is used for checking instructions and arguments using regular expressions.

### framestack.py

```Framestack``` class contains a dictionary for the global frame, list for local frames that acts like a stack and a dictionary for the temporary frame. It also declares methods for creating, pushing and popping frames.

### instruction.py

```Instruction``` class does almost all the magic behind the interpreter. During parsing the XML file all instructions are appended into instruction list one by one and sorted. 
Instruction contains:
* name 
* order number
* list of all instructions and their arguments
* list of arguments, where each argument is dictionary with keys 'type' and 'value'
* methods that:
	* make an instruction
	* run an instructions

### Missing features

I have not implemented --input="" parameter.