Documentation of Project Implementation for IPP 2017/2018  
Name and surname: Jakub Sencak  
Login: xsenca00  

--------

## About

I have implemented extension STATP.  
I have not implemented short options.  
This documentation was created in Markdown so keep that in mind.   

--------

## Dependencies

Using **PHP v7.3**

There has to be few dependencies installed to run this script:

- XMLWriter [1]  

- SimpleXML

- libxml

--------

## Files

#### parse.php

Contains the ```main``` part of the script. Everything starts here.

#### debug.php
 
Contains one value ```$debug``` which sets a lot of stuff to be printed on *stdout*. It may be helpful.

#### stat.php

Contains definition of ```class Statistics```. It has 4 private parameters which are set to 0 at the beginning and 4 enable parameters which act as flags.  

```public function setEnable ($params_array)```  Checks whether there is a parameter in array of parameters and sets the *enable flags*.

There are public functions that are used for incrementing values of private parameters as well as methods for getting the values.

#### functions.php

This file was made to make the parse.php file a little bit lightweight.


```function err_out($ret_val)``` Returns return value to the *stderr*      

These functions generate XML:  
```function generate_instruction_start($xw, &$line_counter, $word)```  
```function generate_arg($xw, $type, $arg, $attr)```  
```function generate_symb($xw, $order, $word_a)```  
```function generate_instruction_end($xw)```  

Check string for lexical errors:  
```function symb_regex($symb_string)```  
```function var_regex($var_string)```  


#### Errors.php

Takes care of errors. Unfortunately there is no method for exiting the script. Maybe in the future.   

--------

## How does it work

1. Take care of the arguments  
2. Open files
3. Process one line of source file - using regexes to check lexical errors
4. Output to XML - using XMLWriter functions
5. Loop 3. and 4.
6. Close files 
7. Write statistics
8. Close files   

--------

## Testing

Testing had been done using IFJ class project. No automation till today (12. 03. 2019).   

--------

## Literature 

[1] http://php.net/manual/en/book.xmlwriter.php

> Have a nice day