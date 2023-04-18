## Windows Service For laravel Queue

### Instructions:
- `composer require litiano/laravel-queue-for-windows`
- `php artisan windows:service:queue:create WINDOWS_SERVICE_NAME"`
- Run `bin/WINDOWS_SERVICE_NAME/LaravelQueueService.exe` as administrator and click on `Install` button.
- Open Windows service manager and start your new service.

### Windows service project:
https://github.com/Litiano/Windows-service-for-Laravel-queue
