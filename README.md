# File

[![Build Status](https://scrutinizer-ci.com/g/miBadger/miBadger.File/badges/build.png?b=master)](https://scrutinizer-ci.com/g/miBadger/miBadger.File/build-status/master)
[![Code Coverage](https://scrutinizer-ci.com/g/miBadger/miBadger.File/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/miBadger/miBadger.File/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/miBadger/miBadger.File/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/miBadger/miBadger.File/?branch=master)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/3bb457ee-5eaa-476e-a8b4-aec2f029d535/mini.png)](https://insight.sensiolabs.com/projects/3bb457ee-5eaa-476e-a8b4-aec2f029d535)

The File Component.

## Example

```php
<?php

use miBadger\File\File;

/**
 * Constructs a File object with the given path.
 */
$file = new File($path);

/**
 * Returns the path of the file.
 */
$file->getPath();

/**
 * Returns the parent directory of the file.
 */
$file->getDirectory();

/**
 * Returns the name of the file.
 */
$file->getName();

/**
 * Returns true if the file exists.
 */
$file->exists();

/**
 * Returns true if you can execute the file.
 */
$file->canExecute();

/**
 * Returns true if you can read the file.
 */
$file->canRead();

/**
 * Returns true if you can write the file.
 */
$file->canWrite();

/**
 * Returns true if the file is a file.
 */
$file->isFile();

/**
 * Returns true if the file is a directory.
 */
$file->isDirectory();

/**
 * Returns the numer of bytes in the file, or -1 on failure.
 */
$file->count();

/**
 * Returns the numer of bytes in the file, or -1 on failure.
 */
$file->length();

/**
 * Returns the time of the last modification as a unixtimestap, or -1 on failure.
 */
$file->lastModified();

/**
 * Returns an array with the files and directories in the current directory.
 */
$file->listAll($recursive = false, $showHidden = false);

/**
 * Returns an array with the directories in the current directory.
 */
$file->listDirectories($recursive = false, $showHidden = false);

/**
 * Returns an array with the files in the current directory.
 */
$file->listFiles($recursive = false, $showHidden = false);

/**
 * Returns true if the file has been created.
 */
$file->makeFile($override = false);

/**
 * Returns true if the directory has been created.
 */
$file->makeDirectory($recursive = false, $permissions = 0775);

/**
 * Returns true if the file is succesfully moved.
 */
$file->move($path, $override = false);

/**
 * Returns true if the file is succesfully renamed.
 */
$file->rename($file, $override = false);

/**
 * Returns true if the directory is succesfully removed.
 */
$file->removeDirectory($recursive = false);

/**
 * Returns true if the file is succesfully removed
 */
$file->removeFile();

/**
 * Returns the content of the file.
 */
$file->read();

/**
 * Append the given content.
 */
$file->append($content);

/**
 * Write the given content.
 */
$file->write($content);
```
