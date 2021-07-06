## 設定路徑

```
composer config repositories.line git git@gitlab.com:DevOpsKit/linekit.git
```


## 開始安裝
```
composer require ryan/linekit
```

## 執行單元測試
```
vendor/bin/phpunit .\tests\
```


## 安裝的套件
composer require --dev beyondcode/laravel-package-tools


## 若是開發模式請需
composer require laravel/chillbot:@dev --prefer-source --prefer-dist --dev