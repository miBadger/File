<?php

/**
 * This file is part of the miBadger package.
 *
 * @author Michael Webbers <michael@webbers.io>
 * @license http://opensource.org/licenses/Apache-2.0 Apache v2 License
 * @version 1.0.0
 */

namespace miBadger\File;

/**
 * The file class.
 *
 * @since 1.0.0
 */
class File implements \Countable
{
	const DIRECTORY_SEPARATOR = \DIRECTORY_SEPARATOR;

	/** @var string the file path. */
	private $path;

	/**
	 * Constructs a File object with the given path.
	 *
	 * @param string $path
	 */
	public function __construct($path)
	{
		if (substr($path, -1) === static::DIRECTORY_SEPARATOR) {
			$this->path = substr($path, 0, -1);
		} else {
			$this->path = $path;
		}
	}

	/**
	 * Returns the string representation of the File object.
	 *
	 * @return string the string representation of the File object.
	 */
	public function __toString()
	{
		return $this->getPath();
	}

	/**
	 * Returns the path of the file.
	 *
	 * @return string the path of the file.
	 */
	public function getPath()
	{
		return $this->path;
	}

	/**
	 * Returns the parent directory of the file.
	 *
	 * @return string the parent directory of the file.
	 */
	public function getDirectory()
	{
		return dirname($this->path);
	}

	/**
	 * Returns the name of the file.
	 *
	 * @return string the name of the file.
	 */
	public function getName()
	{
		return basename($this->path);
	}

	/**
	 * Returns true if the file exists.
	 *
	 * @return bool true if the file exists.
	 */
	public function exists()
	{
		return file_exists($this->path);
	}

	/**
	 * Returns true if you can execute the file.
	 *
	 * @return bool true if you can execute the file.
	 */
	public function canExecute()
	{
		return is_executable($this->path);
	}

	/**
	 * Returns true if you can read the file.
	 *
	 * @return bool true if you can read the file.
	 */
	public function canRead()
	{
		return is_readable($this->path);
	}

	/**
	 * Returns true if you can write the file.
	 *
	 * @return bool true if you can write the file.
	 */
	public function canWrite()
	{
		return is_writeable($this->path);
	}

	/**
	 * Returns true if the file is a file.
	 *
	 * @return bool true if the file is a file.
	 */
	public function isFile()
	{
		return is_file($this->path);
	}

	/**
	 * Returns true if the file is a directory.
	 *
	 * @return bool true if the file is a directory.
	 */
	public function isDirectory()
	{
		return is_dir($this->path);
	}

	/**
	 * Returns the numer of bytes in the file, or -1 on failure.
	 *
	 * @return int the number of bytes in the file, or -1 on failure.
	 */
	public function count()
	{
		// Check if the file exists
		if (!$this->exists()) {
			return -1;
		}

		// Return length
		return ($result = filesize($this->path)) !== false ? $result : -1;
	}

	/**
	 * Returns the time of the last modification as a unixtimestap, or -1 on failure.
	 *
	 * @return int the time of the last modification as a unixtimestap, or -1 on failure.
	 */
	public function lastModified()
	{
		// Check if the file exists
		if (!$this->exists()) {
			return -1;
		}

		// Return last modified timestamp
		return ($result = filemtime($this->path)) !== false ? $result : -1;
	}

	/**
	 * Returns an iterator with the files and directories in the current directory.
	 *
	 * @param bool $recursive = false
	 * @param bool $showHidden = false
	 * @return \ArrayIterator|\FilesystemIterator|\RecursiveIteratorIterator an iterator with the files and directories in the current directory.
	 */
	private function listAllIterator($recursive = false, $showHidden = false)
	{
		// Check file
		if (!$this->isDirectory()) {
			return new \ArrayIterator([]);
		}

		// Check flags
		$flags = \FilesystemIterator::KEY_AS_PATHNAME | \FilesystemIterator::CURRENT_AS_FILEINFO;

		if (!$showHidden) {
			$flags = $flags | \FilesystemIterator::SKIP_DOTS;
		}

		// Check recursive
		if ($recursive) {
			return new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($this->path, $flags), \RecursiveIteratorIterator::SELF_FIRST);
		}

		return new \FilesystemIterator($this->path, $flags);
	}

	/**
	 * Returns an array with the files and directories in the current directory.
	 *
	 * @param bool $recursive = false
	 * @param bool $showHidden = false
	 * @return string[] an array with the files and directories in the current directory.
	 */
	public function listAll($recursive = false, $showHidden = false)
	{
		// Init
		$result = [];

		// List all
		foreach ($this->listAllIterator($recursive, $showHidden) as $element) {
			$result[] = $element->getFilename();
		}

		// Return all
		return $result;
	}

	/**
	 * Returns an array with the directories in the current directory.
	 *
	 * @param bool $recursive = false
	 * @param bool $showHidden = false
	 * @return string[] an array with the directories in the current directory.
	 */
	public function listDirectories($recursive = false, $showHidden = false)
	{
		// Init
		$result = [];

		// List directories
		foreach ($this->listAllIterator($recursive, $showHidden) as $element) {
			if ($element->isDir()) {
				$result[] = $element->getFilename();
			}
		}

		// Return directories
		return $result;
	}

	/**
	 * Returns an array with the files in the current directory.
	 *
	 * @param bool $recursive = false
	 * @param bool $showHidden = false
	 * @return string[] an array with the files in the current directory.
	 */
	public function listFiles($recursive = false, $showHidden = false)
	{
		// Init
		$result = [];

		// List files
		foreach ($this->listAllIterator($recursive, $showHidden) as $element) {
			if ($element->isFile()) {
				$result[] = $element->getFilename();
			}
		}

		// Return files
		return $result;
	}

	/**
	 * Returns true if the file has been created.
	 *
	 * @param bool $override = false
	 * @return bool true if the file has been created.
	 */
	public function makeFile($override = false)
	{
		// Check if the file exists
		if ($this->exists() && !$override) {
			return false;
		}

		return file_put_contents($this->path, '') !== false;
	}

	/**
	 * Returns true if the directory has been created.
	 *
	 * @param bool $recursive = false
	 * @param int $permissions = 0755
	 * @return bool true if the directory has been created.
	 */
	public function makeDirectory($recursive = false, $permissions = 0775)
	{
		// Check if the directory exists
		if ($this->exists()) {
			return false;
		}

		// Make directory
		$old = umask(0777 - $permissions);
		$result = mkdir($this->path, $permissions, $recursive);
		umask($old);

		// Return result
		return $result;
	}

	/**
	 * Returns true if the file is succesfully moved.
	 *
	 * @param string $path
	 * @param bool $override = false
	 * @return bool true if the file is succesfully moved.
	 */
	public function move($path, $override = false)
	{
		// Check if the old file exists
		if (!$this->exists()) {
			return false;
		}

		// Create file
		$file = new File($path);

		// Check if the new file exists
		if (($file->exists() && !$override) || !rename($this->path, $file->getPath())) {
			return false;
		}

		$this->path = $file->getPath();

		return true;
	}

	/**
	 * Returns true if the file is succesfully renamed.
	 *
	 * @param string $file
	 * @param bool $override = false
	 * @return bool true if the file is succesfully renamed.
	 */
	public function rename($file, $override = false)
	{
		return $this->move($this->getDirectory() . static::DIRECTORY_SEPARATOR . basename($file), $override);
	}

	/**
	 * Returns true if the directory is succesfully removed.
	 *
	 * @param bool $recursive = false
	 * @return bool true if the directory is succesfully removed.
	 */
	public function removeDirectory($recursive = false)
	{
		// Check if the directory has to be removed recursive
		if (!$recursive) {
			return rmdir($this->path);
		}

		// Loop trough the directory
		foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($this->path, \FilesystemIterator::SKIP_DOTS), \RecursiveIteratorIterator::CHILD_FIRST) as $path) {
			$path->isFile() ? unlink($path->getPathname()) : rmdir($path->getPathname());
		}

		// Return true
		return true;
	}

	/**
	 * Returns true if the file is succesfully removed.
	 *
	 * @return bool true if the file is succesfully removed.
	 */
	public function removeFile()
	{
		// Check if the file exists
		if (!$this->isFile()) {
			return false;
		}

		// Remove file
		return unlink($this->path);
	}

	/**
	 * Returns the content of the file.
	 *
	 * @return string the content of the file.
	 * @throws FileException on failure.
	 */
	public function read()
	{
		try {
			if (($result = file_get_contents($this->path)) === false) {
				throw new \Exception('Failed');
			}

			return $result;
		} catch (\Exception $e) {
			throw new FileException('Can\'t read the content.', $e->getCode(), $e);
		}
	}

	/**
	 * Append the given content.
	 *
	 * @param string $content
	 * @return null
	 * @throws FileException on failure.
	 */
	public function append($content)
	{
		try {
			if (file_put_contents($this->path, $content, \FILE_APPEND) === false) {
				throw new \Exception('Failed');
			}
		} catch (\Exception $e) {
			throw new FileException('Can\'t append the given content.', $e->getCode(), $e);
		}
	}

	/**
	 * Write the given content.
	 *
	 * @param string $content
	 * @return null
	 * @throws FileException on failure.
	 */
	public function write($content)
	{
		try {
			if (file_put_contents($this->path, $content) === false) {
				throw new \Exception('Failed');
			}
		} catch (\Exception $e) {
			throw new FileException('Can\'t write the given content.', $e->getCode(), $e);
		}
	}
}
