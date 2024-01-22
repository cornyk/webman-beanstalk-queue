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

namespace Cornyk\Webman\BeanstalkQueue;


/**
 * Interface Consumer
 * @package Cornyk\Webman\BeanstalkQueue
 */
interface Consumer
{
    public function consume($job);
}