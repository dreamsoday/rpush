# rpush
# 1，在composer.json中引用"lgy/console": “dev-master”
# 2，composer update
# 3，在app.php中的providers进行配置 Lgy\RPush\RPushServiceProvider::class
# 4，php artisan vendor:publish
# 5，在config文件夹中寻找rpush.php，对应参数
# 6，引用方式：RPushFacade::functin;（可安装laravel_ide）
