# ser-pretty

[![Build Status](https://travis-ci.org/Qafoo/ser-pretty.svg?branch=master)](https://travis-ci.org/Qafoo/ser-pretty)

**ser-pretty prints serialized PHP in the style of `var_dump()` without
sourcing the required classes.**

    $ src/bin/ser-pretty test.txt 
      class stdClass (1) {
        private $bar =>
        int(223)
      }

[Download ser-pretty as a PHAR](https://github.com/Qafoo/ser-pretty/releases)
or require `qafoo/ser-pretty` via composer.

## But, why?

It happens often that you have serialized PHP data lying around, e.g. from
caching, session data or transmitting it through a queue. If you want to
inspect this data for debugging purposes you typically write a tiny script that
loads your classes (which are needed for de-serialization), get the data and
display a var dump.

ser-pretty deprecates the script-step by simply rendering a `var_dump()` outut
from serialized data, without needing to source the class source.

## The CLI

ser-pretty comes with a very simple PHP CLI script, that reads serialized data
from STDIN or a given file and prints the formatted output to STDOUT. Just
call:

    $ ser-pretty.phar dumped_data.txt

This even works for [Doctrine](http://docs.doctrine-project.org/) annotation
cache files since ser-pretty 0.2.0.

You can also pipe serialized data from another script to it:

    $ some_script.php | ser-pretty.phar


## The Lib

You can also use ser-pretty as a library to integrate pretty printing of
serialized PHP data into your debugging/monitoring/… tools. Just look into the
CLI script to see what it does.

* The `Parser` parses serialized into a very simple AST
* A `Writer` turns an AST into a string representation

ser-pretty ships with a `var_dump()` style writer, the `SimpleTextWriter` which
you can use out of the box. You can implement your own writer by deriving the
`Writer` class.

## Installation

Just include ser-pretty in your project via composer using 
or [download the latest PHAR](https://github.com/Qafoo/ser-pretty/releases).
