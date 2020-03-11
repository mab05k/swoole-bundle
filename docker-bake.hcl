target "releaser" {
  cache-from = ["type=registry,ref=k911/swoole-bundle-cache:releaser"]
  cache-to   = ["type=registry,ref=k911/swoole-bundle-cache:releaser,mode=max"]
  output     = ["type=registry"]
}

target "cli" {
  cache-from = ["type=registry,ref=k911/swoole-bundle-cache:cli"]
  cache-to   = ["type=registry,ref=k911/swoole-bundle-cache:cli,mode=max"]
  output     = ["type=registry"]
}

target "composer" {
  cache-from = ["type=registry,ref=k911/swoole-bundle-cache:composer"]
  cache-to   = ["type=registry,ref=k911/swoole-bundle-cache:composer,mode=max"]
  output     = ["type=registry"]
}

target "coverage-xdebug" {
#   cache-from = ["type=registry,ref=k911/swoole-bundle-cache:coverage-xdebug"]
  cache-to   = ["type=registry,ref=k911/swoole-bundle-cache:coverage-xdebug,mode=max"]
  output     = ["type=registry"]
}

target "coverage-pcov" {
#   cache-from = ["type=registry,ref=k911/swoole-bundle-cache:coverage-pcov"]
  cache-to   = ["type=registry,ref=k911/swoole-bundle-cache:coverage-pcov,mode=max"]
  output     = ["type=registry"]
}
