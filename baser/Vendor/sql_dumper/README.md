# Sql Dumper Plugin for CakePHP 1.2+

Dump schema to SQL.

## Features
 * Fetch tables form each datasource, and create SQL stetment (create, drop, insert).
 * Output file, each datasource.

## Installation

movo to APP/plugins/

    git clone http://github.com/nojimage/sql_dumper.git

and initialize object

    $sqlDumper = ClassRegistry::init('SqlDumper.SqlDumper', 'Vendor');

## Usage

### Dump to file, all datasources

    $sqlDumper->processAll($path_to_output);

### Dump to file, specified datasource, all tables

    $sqlDumper->process('datasouce_config_key', null, $path_to_output);

### Dump to file, specified datasource, all tables(Only created Model files)

    $sqlDumper->process('datasouce_config_key', null, $path_to_output, true);

### Dump to file, specified datasource and table

    $sqlDumper->process('datasouce_config_key', 'tablename', $path_to_output);

### get create stetment

    $sql = $sqlDumper->getCreateSql('datasouce_config_key', 'tablename');

### get drop stetment

    $sql = $sqlDumper->getDropSql('datasouce_config_key', 'tablename');

### get insert stetment

    $sql = $sqlDumper->getInsertSql('datasouce_config_key', 'tablename', false, true);
    

## License

Licensed under The MIT License.
Redistributions of files must retain the above copyright notice.


Copyright 2010, nojimage (http://php-tips.com/)

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.

