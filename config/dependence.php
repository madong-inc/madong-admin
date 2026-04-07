<?php
/**
 * This file is part of webman.
 * Licensed under The MIT License
 * For full copyright and license information, please see the MIT-LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @author    walkor<walkor@workerman.net>
 * @copyright walkor<walkor@workerman.net>
 * @link      http://www.workerman.net/
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */

use Psr\Container\ContainerInterface;
use core\jwt\interfaces\TokenStorageInterface;
use core\jwt\interfaces\BlacklistStorageInterface;
use core\jwt\storage\RedisTokenStorage;
use core\jwt\storage\RedisBlacklistStorage;
use core\jwt\JwtToken;



return [

];