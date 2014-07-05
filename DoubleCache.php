<?php
/**
 * @file
 */

/**
 * Class DoubleCache
 *
 * 2 level caching: static process level cache and permanent (attachable) cache.
 */
class DoubleCache {

  /**
   * Storage for temporary cached values.
   *
   * @var array
   */
  private static $staticStorage;

  /**
   * Storage for class instances.
   *
   * @var array
   */
  private static $instanceCache;

  // Permanent cache bin name.
  const DEFAULT_CACHE_BIN = 'cache';

  /**
   * Cache key.
   *
   * @var string
   */
  private $cid;

  /**
   * Cache bin name.
   *
   * @var string
   */
  private $bin;

  /**
   * Constructor.
   *
   * @param $cid
   *  Cache key string.
   * @param $bin
   *  Cache name string.
   */
  private function __construct($cid, $bin) {
    $this->cid = $cid;
    $this->bin = $bin;

    if (!$this->hasData()) {
      $permanent_cache = cache_get($this->cid, $this->bin);
      if ($permanent_cache) {
        self::$staticStorage[$this->bin][$this->cid] = $permanent_cache->data;
      }
    }
  }

  /**
   * Check if there is any data in cache.
   *
   * @return bool
   */
  public function hasData() {
    return isset(self::$staticStorage[$this->bin][$this->cid]);
  }

  /**
   * Set value to cache.
   *
   * @param mixed $value
   *  Value to cache.
   * @param int $expire
   *  Expiration timestamp. Use CACHE_PERMANENT for keeping it till the next cache flush.
   */
  public function set($value, $expire = CACHE_PERMANENT) {
    self::$staticStorage[$this->bin][$this->cid] = $value;
    cache_set($this->cid, $value, $this->bin, $expire);
  }

  /**
   * Get the stored value.
   *
   * @return mixed|NULL
   */
  public function value() {
    return $this->hasData() ? self::$staticStorage[$this->bin][$this->cid] : NULL;
  }

  /**
   * Obtain a class instance. This is the preferred usage instead of the constructor.
   *
   * @param $cid
   *  Cache ID string.
   * @param string $bin
   *  Cache bin name.
   * @return DoubleCache
   */
  public static function get($cid, $bin = self::DEFAULT_CACHE_BIN) {
    if (!isset(self::$instanceCache[$bin][$cid])) {
      self::$instanceCache[$bin][$cid] = new DoubleCache($cid, $bin);
    }

    return self::$instanceCache[$bin][$cid];
  }

}
