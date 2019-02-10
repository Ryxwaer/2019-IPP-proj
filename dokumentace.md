Popis
jazyk: anglictina
rozsah: každému skriptu patrí 1 strana A4
interpret.py max 2s
parse.php max 1s
test.php max 1s

--------------------------

pismo 10bodove, Times New Roman(text), Courier (kod), 

> !!! NEobsahuje uvod, obsah ani zaver !!!

obsah:
- celkovy popis filozofie návvrhu, sposob amoj specificky postup riesenia - hlavne veci, ktore som riesil inak ako zadanie albo neboli specifikovane v zadani.
- vyuzitie návrhových vzorov 
- nedokončené vlastnosti


Implementační dokumentace k %cislo%. úloze do IPP 2018/2019
Jméno a příjmení: Jakub Sencak
Login: xsenca00

Documentation of Project Implementation for IPP 2017/2018
Name and surname: Jakub Sencak
Login: xsenca00


Hodnoti sa aj okomentovanost kodu.




--------------------------------------------------------

Programova cast

errors
10 chybajuci parameter
11 otvaranie suboru - neexistuje/opravnenie
12 zapis do suboru - opravnenie
20-69 - script related
99 internal error

XML
v zdrojovom subore IPPcode19
21 - chybajuca hlavicka
22 - neznamy alebo chybny operacny kod 
23 -ina lexikalna albo syntakticka chyba


PYthon
31 - chybny XML format - well-formed
32 - neocakavana struktura XML/lexikalmna/syntakticka chyba

52 - semanticka kontrola vstupneho kodu v IPPcode19
53 - behova chyba interpretace ZLE TYPY operandov
54 - behova chyba interpretace pristup k neexistujucej premennej
55 - behova chyba interpretace ramec neexistuje
56 - behova chyba interpretace chybajuca hodnota
57 - behova chyba interpretace zla hodnota oeprandov
58 - behova chyba interpretace praca s retazcom




Vsade kodovat UTF-8

--help vypise ako sa ma scrpt pouzivat

3.1 XML
<?xml version="1.0" encoding="UTF 8"?>
<program language="IPPcode19">
	<instruction order="#" opcode="ADD">
		<arg1 type="int/bool/sting/nil/label/type/var"></arg1>
		<arg2 type=""></arg2>
		<arg3 type=""></arg3>
	</instruction>
</program>


escape sekvence pre znaky <, >, & &lt; &gt; &amp;

bool malymi pismenami true false

# 4 Interpret

interpret.py --source=file --input=file  {--help}

# 5 test.php

test.php {--help} --directory="path" --recursive --parse-script=file --int-script=file --parse-only


4 subory
.src - zdrojak
.in - ocakavany vstup
.out - ocakavany vystup
.rc - navratovy kod


pamatovy model
GF
LF
TF

















