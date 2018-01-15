<?php

namespace Illuminate\Filesystem;

use Illuminate\Contracts\Cache\Repository;
use League\Flysystem\Cached\Storage\AbstractCache;

class Cache extends AbstractCache
{
    /**
     * 高速缓存存储库实现。
     *
     * @var \Illuminate\Contracts\Cache\Repository
     */
    protected $repository;

    /**
     * 缓存键
     *
     * @var string
     */
    protected $key;

    /**
     * 缓存过期时间
     *
     * @var int
     */
    protected $expire;

    /**
     * 创建一个新的缓存实例
     *
     * @param \Illuminate\Contracts\Cache\Repository  $repository
     * @param string  $key
     * @param int|null  $expire
     */
    public function __construct(Repository $repository, $key = 'flysystem', $expire = null)
    {
        $this->key = $key;
        $this->repository = $repository;

        if (! is_null($expire)) {
            $this->expire = (int) ceil($expire / 60);
        }
    }

    /**
     * 加载缓存
     *
     * @return void
     */
    public function load()
    {
        $contents = $this->repository->get($this->key);

        if (! is_null($contents)) {
            $this->setFromStorage($contents);
        }
    }

    /**
     * 保存缓存
     *
     * @return void
     */
    public function save()
    {
        $contents = $this->getForStorage();

        if (! is_null($this->expire)) {
            $this->repository->put($this->key, $contents, $this->expire);
        } else {
            $this->repository->forever($this->key, $contents);
        }
    }
}
