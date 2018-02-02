<?php
namespace Linyuee\Cache;

use Doctrine\Common\Cache\Cache;


trait CacheTrait
{
    /**
     * Doctrine\Common\Cache\Cache.
     */
    protected $cache;
    /**
     * 设置缓存驱动.
     */
    protected function setCache(Cache $cache)
    {
        $this->cache = $cache;
    }
    /**
     * 获取缓存驱动.
     */
    protected function getCache()
    {
        return $this->cache;
    }
}