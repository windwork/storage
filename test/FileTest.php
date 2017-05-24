<?php
require_once '../lib/AStorage.php';
require_once '../lib/Exception.php';
require_once '../lib/strategy/File.php';

use \wf\storage\strategy\File;

/**
 * File test case.
 */
class FileTest extends PHPUnit_Framework_TestCase 
{
    private $storage;
    
    /**
     * Prepares the environment before running a test.
     */
    protected function setUp() 
    {
        parent::setUp ();
        $cfg = array(
            'storageUrl'      => '',//http://stor.demo.com/upload/storage_dir/',
            'storageDir'      => '/storage_dir/',
            'baseUrl'         => 'http://stor.demo.com/upload/index.php?',
            'hostInfo'        => 'http://stor.demo.com',
            'basePath'        => '/upload/',
        );
        $this->storage = new \wf\storage\strategy\File($cfg);
    }
    
    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown() 
    {
        parent::tearDown ();
    }
    
    /**
     * Constructs the test case.
     */
    public function __construct() 
    {
        // TODO Auto-generated constructor
    }
    
    /**
     * Tests File->remove()
     */
    public function testRemove()
    {
        // TODO Auto-generated FileTest->testRemove()
        $this->markTestIncomplete ( "remove test not implemented" );
        
        $this->file->remove(/* parameters */);
    }

    /**
     * Tests File->removeThumb()
     */
    public function testThumb()
    {
        $path = 'aa/bb.txt';
        $stor = clone $this->storage;
        $thumbPath = $stor->getThumbPath($path, 50, 50);
        $thumbUrl = $stor->getThumbUrl($path, 200, 100);
        $this->assertEquals('http://stor.demo.com/upload/storage_dir/thumb/8d/f0/YWEvYmIudHh0$MjAweDEwMA==.jpg', $thumbUrl);
        

        $cfg = array(
            'storageDir'      => '/storage_dir/',
            'baseUrl'         => 'http://stor.demo.com/upload/index.php?',
            'hostInfo'        => 'http://stor.demo.com',
            'basePath'        => '/upload/',
            'siteUrl' => 'http://stor.demo.com/upload/storage_dir/',
        );
        $stor = new \wf\storage\strategy\File($cfg);
        $thumbUrl = $stor->getThumbUrl($path, 200, 100);
        $this->assertEquals('http://stor.demo.com/upload/storage_dir/thumb/8d/f0/YWEvYmIudHh0$MjAweDEwMA==.jpg', $thumbUrl);
    }
    
    /**
     * Tests File->removeThumb()
     */
    public function testRemoveThumb() 
    {
        // TODO Auto-generated FileTest->testRemoveThumb()
        $this->markTestIncomplete ( "removeThumb test not implemented" );
    }
    
    /**
     * Tests File->clearThumb()
     */
    public function testClearThumb() 
    {
        // TODO Auto-generated FileTest->testClearThumb()
        $this->markTestIncomplete ( "clearThumb test not implemented" );
    }
    
    /**
     * Tests File->getContent()
     */
    public function testGetContent()
    {
        // TODO Auto-generated FileTest->testGetContent()
        $this->markTestIncomplete ( "getContent test not implemented" );
        
        $this->file->getContent(/* parameters */);
    }
    
    /**
     * Tests File->save()
     */
    public function testSave()
    {
        // TODO Auto-generated FileTest->testSave()
        $this->markTestIncomplete ( "save test not implemented" );
        
        $this->file->save(/* parameters */);
    }
    
    /**
     * Tests File->upload()
     */
    public function testUpload()
    {
        $this->markTestIncomplete ( "save test not implemented" );
    }
    
    /**
     * Tests File->copy()
     */
    public function testCopy()
    {
        
        $stor = clone $this->storage;
        $path = 'test/a.txt';
        
        $stor->save($path, 'test');
        $this->assertEquals('test', $stor->getContent($path));
        
        $pathB = 'test/b.txt';
        $stor->remove($pathB);
        $stor->copy($path, $pathB);
        
        $this->assertEquals('test', $stor->getContent($pathB));

        $stor->remove($path);
        $stor->remove($pathB);
    }
    
    /**
     * Tests File->isExist()
     */
    public function testIsExist()
    {
        $stor = clone $this->storage;
        $path = 'my/test.tmp';
        
        $stor->remove($path);
        $this->assertFalse($stor->isExist($path));
        
        $stor->save($path, 'text');
        $this->assertTrue($stor->isExist($path));
        
        $stor->remove($path);
    }
    
    /**
     * Tests File->getRealPath()
     */
    public function testGetRealPath()
    {
        $path = 'a/b/c/11/22/';
        $stor = clone $this->storage;
        $realPath = $stor->getRealPath($path);
        $this->assertEquals(__DIR__ . DIRECTORY_SEPARATOR . 'storage_dir/a/b/c/11/22/', $realPath);
    }
    
    public function testGetPathFromUrl()
    {
        $url = 'http://stor.demo.com/upload/storage_dir/thumb/8d/f0/YWEvYmIudHh0$MjAweDEwMA==.jpg';

        $stor = clone $this->storage;
        $path = $stor->getPathFromUrl($url);
        
    }
}

