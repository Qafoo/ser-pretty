# ser-pretty - Pretty print serialized PHP data

ser-pretty allows you to pretty print serialized PHP data without unserializing
it. Therefore, you do not need to have the classes of serialized objects loaded.

## But, why?

It happens often that you have serialized PHP data lying around for example
from caching, session data or transmitting it through a queue. If you want to
inspect this data for debugging purposes, you typically write a tiny script
that loads your classes (for de-serialization of objects), fetch the data and
display a var dump.

ser-pretty deprecates this step by simply rendering a `var_dump()` like outut
from serialized data, without actually unserializing it.

## The CLI

ser-pretty comes with a very simple PHP CLI script, that reads serialized data
from STDIN and prints the formatted output to STDOUT. Just call:

    $ ser-pretty < dumped_data.txt

You can also pipe serialized data from another script to it.

## The Lib

You can also use ser-pretty as a library to integrate pretty printing of
serialized PHP data into your debugging/monitoring/… tools. Just look into the
CLI script to see what it does.

* The `Parser` parses serialized into a very simple AST
* A `Writer` turns an AST into a string representation

ser-pretty ships with a `var_dump()` style writer, the `SimpleTextWriter` which
you can use out of the box.

## Installation

Just include ser-pretty in your project via composer using `Qafoo/SerPretty`.
