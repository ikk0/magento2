<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Test\Legacy;

/**
 * Temporary test
 * Test verifies obsolete usages in modules that were refactored to work with getConnection.
 */
class ObsoleteConnectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var array
     */
    protected $obsoleteMethods = [];

    /**
     * @var array
     */
    protected $obsoleteRegexp = [];

    /**
     * @var array
     */
    protected $filesBlackList = [];

    /**
     * @var string
     */
    protected $appPath;

    protected function setUp()
    {
        $this->appPath = \Magento\Framework\App\Utility\Files::init()->getPathToSource();
        $this->obsoleteMethods = [
            '_getReadConnection',
            '_getWriteConnection',
            '_getReadAdapter',
            '_getWriteAdapter',
            'getReadConnection',
            'getWriteConnection',
            'getReadAdapter',
            'getWriteAdapter',
            'getAdapter',
        ];

        $this->obsoleteRegexp = [
            'getConnection\\(\'\\w*_*(read|write)',
            '\\$_?(read|write)(Connection|Adapter)',
        //    '\\$write([A-Z]\\w*|\\s)',
        ];

        $this->filesBlackList = $this->getBlackList();
    }

    /**
     * Test verify that obsolete regexps do not appear in refactored folders
     */
    public function testObsoleteRegexp()
    {
        $invoker = new \Magento\Framework\App\Utility\AggregateInvoker($this);
        $invoker(
            function ($file) {
                $content = file_get_contents($file);
                foreach ($this->obsoleteRegexp as $regexp) {
                    $this->assertSame(
                        0,
                        preg_match('/' . $regexp . '/iS', $content),
                        "File: $file\nContains obsolete regexp: $regexp. "
                    );
                }
            },
            $this->modulesFilesDataProvider()
        );
    }

    /**
     * Test verify that obsolete methods do not appear in refactored folders
     */
    public function testObsoleteResponseMethods()
    {
        $invoker = new \Magento\Framework\App\Utility\AggregateInvoker($this);
        $invoker(
            function ($file) {
                $content = file_get_contents($file);
                foreach ($this->obsoleteMethods as $method) {
                    $quotedMethod = preg_quote($method, '/');
                    $this->assertSame(
                        0,
                        preg_match('/(?<=[a-z\\d_:]|->|function\\s)' . $quotedMethod . '\\s*\\(/iS', $content),
                        "File: $file\nContains obsolete method: $method . "
                    );
                }
            },
            $this->modulesFilesDataProvider()
        );
    }

    /**
     * Return refactored files
     *
     * @return array
     */
    public function modulesFilesDataProvider()
    {
        $filesList = [];

        foreach ($this->getFilesData('whitelist/refactored_modules*') as $refactoredFolder) {
            $files = \Magento\Framework\App\Utility\Files::init()->getFiles(
                [$this->appPath . $refactoredFolder],
                '*.php'
            );
            $filesList = array_merge($filesList, $files);
        }

        $result = array_map('realpath', $filesList);
        $result = array_diff($result, $this->filesBlackList);
        return \Magento\Framework\App\Utility\Files::composeDataSets($result);
    }

    /**
     * @return array
     */
    protected function getBlackList()
    {
        $blackListFiles = [];
        foreach ($this->getFilesData('blacklist/files_list*') as $file) {
            $blackListFiles[] = realpath($this->appPath . $file);
        }
        return $blackListFiles;
    }

    /**
     * @param string $filePattern
     * @return array
     */
    protected function getFilesData($filePattern)
    {
        $result = [];
        foreach (glob(__DIR__ . '/_files/connection/' . $filePattern) as $file) {
            $fileData = include $file;
            $result = array_merge($result, $fileData);
        }
        return $result;
    }
}
