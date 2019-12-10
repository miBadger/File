<?php

/**
 * This file is part of the miBadger package.
 *
 * @author Michael Webbers <michael@webbers.io>
 * @license http://opensource.org/licenses/Apache-2.0 Apache v2 License
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
	protected $path;

	/**
	 * Constructs a File object with the given path.
	 *
	 * @param string $path
	 */
	public function __construct($path)
	{
		if (mb_substr($path, -1) === static::DIRECTORY_SEPARATOR) {
			$this->path = mb_substr($path, 0, -1);
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
	 * Returns the extension of the file
	 * 
	 * @return string the file extension
	 */
	public function getExtension()
	{
		return pathinfo($this->path, PATHINFO_EXTENSION);
	}

	/**
	 * Returns the mime-type as determined by information from php's magic.mime file, null on failure
	 * 
	 * @return string|null the mime type
	 */
	public function getMimeType()
	{
		$mime = mime_content_type($this->path);
		if ($mime === false) {
			return null;
		}
		return $mime;
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
		return $this->length();
	}

	/**
	 * Returns the numer of bytes in the file, or -1 on failure.
	 *
	 * @return int the number of bytes in the file, or -1 on failure.
	 */
	public function size()
	{
		return $this->length();
	}

	/**
	 * Returns the numer of bytes in the file, or -1 on failure.
	 *
	 * @return int the number of bytes in the file, or -1 on failure.
	 */
	public function length()
	{
		if (!$this->exists()) {
			return -1;
		}

		return ($result = filesize($this->path)) !== false ? $result : -1;
	}

	/**
	 * Returns the time of the last modification as a unixtimestap, or -1 on failure.
	 *
	 * @return int the time of the last modification as a unixtimestap, or -1 on failure.
	 */
	public function lastModified()
	{
		if (!$this->exists()) {
			return -1;
		}

		return ($result = filemtime($this->path)) !== false ? $result : -1;
	}

	/**
	 * Returns an iterator with the files and directories in the current directory.
	 *
	 * @param bool $recursive = false
	 * @param bool $showHidden = false
	 * @return \ArrayIterator|\FilesystemIterator|\RecursiveIteratorIterator an iterator with the files and directories in the current directory.
	 */
	protected function listAllIterator($recursive = false, $showHidden = false)
	{
		if (!$this->isDirectory()) {
			return new \ArrayIterator([]);
		}

		$flags = \FilesystemIterator::KEY_AS_PATHNAME | \FilesystemIterator::CURRENT_AS_FILEINFO;

		if (!$showHidden) {
			$flags = $flags | \FilesystemIterator::SKIP_DOTS;
		}

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
		$result = [];

		foreach ($this->listAllIterator($recursive, $showHidden) as $element) {
			$result[] = $element->getFilename();
		}

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
		$result = [];

		foreach ($this->listAllIterator($recursive, $showHidden) as $element) {
			if ($element->isDir()) {
				$result[] = $element->getFilename();
			}
		}

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
		$result = [];

		foreach ($this->listAllIterator($recursive, $showHidden) as $element) {
			if ($element->isFile()) {
				$result[] = $element->getFilename();
			}
		}

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
		if ($this->exists()) {
			return false;
		}

		$old = umask(0777 - $permissions);
		$result = mkdir($this->path, $permissions, $recursive);
		umask($old);

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
		if (!$this->exists()) {
			return false;
		}

		$file = new File($path);

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
		if (!$recursive) {
			return rmdir($this->path);
		}

		foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($this->path, \FilesystemIterator::SKIP_DOTS), \RecursiveIteratorIterator::CHILD_FIRST) as $path) {
			$path->isFile() ? unlink($path->getPathname()) : rmdir($path->getPathname());
		}

		return true;
	}

	/**
	 * Returns true if the file is succesfully removed.
	 *
	 * @return bool true if the file is succesfully removed.
	 */
	public function removeFile()
	{
		if (!$this->isFile()) {
			return false;
		}

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
		$result = file_get_contents($this->path);

		if ($result === false) {
			throw new FileException('Can\'t read the content.');
		}

		return $result;
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
		if (file_put_contents($this->path, $content, \FILE_APPEND) === false) {
			throw new FileException('Can\'t append the given content.');
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
		if (file_put_contents($this->path, $content) === false) {
			throw new FileException('Can\'t write the given content.');
		}
	}
}
