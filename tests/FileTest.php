<?php

/**
 * This file is part of the miBadger package.
 *
 * @author Michael Webbers <michael@webbers.io>
 * @license http://opensource.org/licenses/Apache-2.0 Apache v2 License
 * @version 1.0.0
 */

namespace miBadger\File;

use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\vfsStreamFile;
use org\bovigo\vfs\vfsStreamWrapper;

/**
 * The file test class.
 *
 * @since 1.0.0
 */
class FileTest extends \PHPUnit_Framework_TestCase
{
	/** @var File The directory. */
	private $directory;

	/** @var File The file. */
	private $file;

	/** @var File The fake file. */
	private $fake;

	public function setUp()
	{
		vfsStreamWrapper::register();
		vfsStreamWrapper::setRoot(new vfsStreamDirectory('test'));
		vfsStreamWrapper::getRoot()->addChild(new vfsStreamDirectory('directory'));
		vfsStreamWrapper::getRoot()->addChild(new vfsStreamFile('file.txt'));

		$this->directory = new File(vfsStream::url('test'));
		$this->file = new File(vfsStream::url('test/file.txt'));
		$this->fake = new File(vfsStream::url('test/fake.txt'));
	}

	public function tearDown()
	{
		vfsStreamWrapper::unregister();
	}

	public function test__construct()
	{
		$directory = new File(vfsStream::url('test/'));
		$this->assertEquals($this->directory->__toString(), $directory->__toString());
	}

	public function test__toString()
	{
		$this->assertEquals(vfsStream::url('test'), $this->directory->__toString());
		$this->assertEquals(vfsStream::url('test/file.txt'), $this->file->__toString());
		$this->assertEquals(vfsStream::url('test/fake.txt'), $this->fake->__toString());
	}

	public function testGetPath()
	{
		$this->assertEquals(vfsStream::url('test'), $this->directory->getPath());
		$this->assertEquals(vfsStream::url('test/file.txt'), $this->file->getPath());
		$this->assertEquals(vfsStream::url('test/fake.txt'), $this->fake->getPath());
	}

	public function testGetDirectory()
	{
		$this->assertEquals('vfs:', $this->directory->getDirectory());
		$this->assertEquals(vfsStream::url('test'), $this->file->getDirectory());
		$this->assertEquals(vfsStream::url('test'), $this->fake->getDirectory());
	}

	public function testGetFile()
	{
		$this->assertEquals('test', $this->directory->getName());
		$this->assertEquals('file.txt', $this->file->getName());
		$this->assertEquals('fake.txt', $this->fake->getName());
	}

	public function testExists()
	{
		$this->assertTrue($this->directory->exists());
		$this->assertTrue($this->file->exists());
		$this->assertFalse($this->fake->exists());
	}

	public function testCanExecute()
	{
		$this->assertFalse($this->directory->canExecute());
		$this->assertFalse($this->file->canExecute());
		$this->assertFalse($this->fake->canExecute());
	}

	public function testCanRead()
	{
		$this->assertTrue($this->directory->canRead());
		$this->assertTrue($this->file->canRead());
		$this->assertFalse($this->fake->canRead());
	}

	public function testCanWrite()
	{
		$this->assertTrue($this->directory->canWrite());
		$this->assertTrue($this->file->canWrite());
		$this->assertFalse($this->fake->canWrite());
	}

	public function testIsFile()
	{
		$this->assertFalse($this->directory->isFile());
		$this->assertTrue($this->file->isFile());
		$this->assertFalse($this->fake->isFile());
	}

	public function testIsDirectory()
	{
		$this->assertTrue($this->directory->isDirectory());
		$this->assertFalse($this->file->isDirectory());
		$this->assertFalse($this->fake->isDirectory());
	}

	/**
	 * @depends testExists
	 */
	public function testCount()
	{
		$this->assertNotEquals(-1, $this->directory->count());
		$this->assertNotEquals(-1, $this->file->count());
		$this->assertEquals(-1, $this->fake->count());
	}

	/**
	 * @depends testExists
	 */
	public function testLength()
	{
		$this->assertNotEquals(-1, $this->directory->length());
		$this->assertNotEquals(-1, $this->file->length());
		$this->assertEquals(-1, $this->fake->length());
	}

	/**
	 * @depends testExists
	 */
	public function testLastModified()
	{
		$this->assertNotEquals(-1, $this->directory->lastModified());
		$this->assertNotEquals(-1, $this->file->lastModified());
		$this->assertEquals(-1, $this->fake->lastModified());
	}

	public function testListAll()
	{
		$this->assertEquals(['directory', 'file.txt'], $this->directory->listAll());
		$this->assertEquals([], $this->file->listAll());
		$this->assertEquals([], $this->fake->listAll());
	}

	public function testListAllRecursive()
	{
		$this->assertEquals(['directory', 'file.txt'], $this->directory->listAll(true));
		$this->assertEquals([], $this->file->listAll(true));
		$this->assertEquals([], $this->fake->listAll(true));
	}

	public function testListDirectories()
	{
		$this->assertEquals(['directory'], $this->directory->listDirectories());
		$this->assertEquals([], $this->file->listDirectories());
		$this->assertEquals([], $this->fake->listDirectories());
	}

	public function testListFiles()
	{
		$this->assertEquals(['file.txt'], $this->directory->listFiles());
		$this->assertEquals([], $this->file->listFiles());
		$this->assertEquals([], $this->fake->listFiles());
	}

	public function testMakeFile()
	{
		$this->assertFalse($this->directory->makeFile());
		$this->assertFalse($this->file->makeFile());
		$this->assertTrue($this->fake->makeFile());
	}

	public function testMakeDirectory()
	{
		$this->assertFalse($this->directory->makeDirectory());
		$this->assertFalse($this->file->makeDirectory());
		$this->assertTrue($this->fake->makeDirectory());
	}

	public function testMove()
	{
		/* rename() is not supported by vfsStream root directories
		$this->assertTrue($this->directory->move(vfsStream::url('test2'))); */

		$this->assertFalse($this->file->move(vfsStream::url('test/directory')));
		$this->assertTrue($this->file->move(vfsStream::url('test/file2.txt')));
		$file = new File(vfsStream::url('test/file2.txt'));
		$this->assertTrue($file->exists());

		$this->assertFalse($this->fake->move(vfsStream::url('test/fake2.txt')));
		$file = new File(vfsStream::url('test/fake2.txt'));
		$this->assertFalse($file->exists());
	}

	public function testRename()
	{
		/* rename() is not supported by vfsStream root directories
		$this->assertTrue($this->directory->rename(vfsStream::url('test2'))); */

		$this->assertTrue($this->file->rename(vfsStream::url('file2.txt')));
		$file = new File(vfsStream::url('test/file.txt'));
		$this->assertFalse($file->exists());

		$this->assertFalse($this->fake->rename(vfsStream::url('fake2.txt')));
		$file = new File(vfsStream::url('test/fake2.txt'));
		$this->assertFalse($file->exists());
	}

	public function testRemoveDirectory()
	{
		$this->assertFalse($this->directory->removeDirectory());
		$this->assertTrue($this->directory->removeDirectory(true));
		$this->assertFalse($this->file->removeDirectory());
		$this->assertFalse($this->fake->removeDirectory());
	}

	public function testRemoveFile()
	{
		$this->assertFalse($this->directory->removeFile());
		$this->assertTrue($this->file->removeFile());
		$this->assertFalse($this->fake->removeFile());
	}

	/**
	 * @expectedException miBadger\File\FileException
	 * @expectedExceptionMessage Can't read the content.
	 */
	public function testReadDirectory()
	{
		@$this->directory->read();
	}

	public function testReadFile()
	{
		$this->assertEquals('', $this->file->read());
	}

	/**
	 * @expectedException miBadger\File\FileException
	 * @expectedExceptionMessage Can't read the content.
	 */
	public function testReadFake()
	{
		@$this->fake->read();
	}

	/**
	 * @expectedException miBadger\File\FileException
	 * @expectedExceptionMessage Can't append the given content.
	 */
	public function testAppendDirectory()
	{
		@$this->directory->append('test');
	}

	public function testAppendFile()
	{
		$this->assertNull($this->file->append('test'));
		$this->assertEquals('test', $this->file->read());
	}

	public function testAppendFake()
	{
		$this->assertNull($this->fake->append('test'));
		$this->assertEquals('test', $this->fake->read());
	}

	/**
	 * @expectedException miBadger\File\FileException
	 * @expectedExceptionMessage Can't write the given content.
	 */
	public function testWriteDirectory()
	{
		@$this->directory->write('test');
	}

	public function testWriteFile()
	{
		$this->assertNull($this->file->write('test'));
		$this->assertEquals('test', $this->file->read());
	}

	public function testWriteFake()
	{
		$this->assertNull($this->fake->write('test'));
		$this->assertEquals('test', $this->fake->read());
	}
}
