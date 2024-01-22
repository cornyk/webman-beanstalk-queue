<?php

/**
 * This file is part of webman.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the MIT-LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @author    cornyk<corneliuskahn@gmail.com>
 * @copyright cornyk<corneliuskahn@gmail.com>
 * @link      https://github.com/cornyk/webman-beanstalk-queue
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */

namespace Cornyk\Webman\BeanstalkQueue\Process;

use support\Container;
use Cornyk\Webman\BeanstalkQueue\Client;

/**
 * Class Consumer
 * @package process
 */
class Consumer
{
    /**
     * @var string
     */
    protected $_consumerDir = '';

    /**
     * constructor.
     * @param string $consumer_dir
     */
    public function __construct($consumer_dir = '')
    {
        $this->_consumerDir = $consumer_dir;
    }

    /**
     * onWorkerStart.
     */
    public function onWorkerStart()
    {
        if (!is_dir($this->_consumerDir)) {
            echo "Beanstalk Consumer directory {$this->_consumerDir} not exists\r\n";
            return;
        }
        $dir_iterator = new \RecursiveDirectoryIterator($this->_consumerDir);
        $iterator = new \RecursiveIteratorIterator($dir_iterator);
        foreach ($iterator as $file) {
            if (is_dir($file)) {
                continue;
            }
            $fileinfo = new \SplFileInfo($file);
            $ext = $fileinfo->getExtension();
            if ($ext === 'php') {
                $class = str_replace('/', "\\", substr(substr($file, strlen(base_path())), 0, -4));
                if (is_a($class, 'Cornyk\Webman\BeanstalkQueue\Consumer', true)) {
                    $consumer = Container::get($class);
                    $connection_name = $consumer->connection ?? 'default';
                    $tube = $consumer->tube ?? 'default';
                    $reserve_timeout = $consumer->reserve_timeout ?? 10;
                    Client::watch($tube);
                    while(!($consumer->quit ?? false)) {
                        $job = Client::reserve_with_timeout($reserve_timeout, $connection_name);
                        if (is_null($job)) {
                            continue;
                        }
                        $consumer->consume($job);
                    } // while
                }
            }
        }
    }
}
