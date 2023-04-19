## Windows Service For laravel Queue

### Features:
- Laravel Queue as Windows native service with gracefully stop and restart.
- Gracefully exit on Ctrl+C event if running in console.
- Stop and restart services individually.
- Create a service for all queues or a specific queue.
- Create multiple Windows services.

### Install instructions:
- `composer require litiano/laravel-queue-for-windows`
- `php artisan windows:service:queue:create {WINDOWS_SERVICE_NAME}"` * see examples.
- Run `bin/{WINDOWS_SERVICE_NAME}/LaravelQueueService.exe` as administrator and click on `Install` button to create Windows service.
- Open Windows service manager and start your new service.

### Examples:
#### Create a service for all queues:
`php artisan windows:service:queue:create LaravelAllQueue`

#### Create a service for queue "orders_queue"
`php artisan windows:service:queue:create LaravelOrdersQueue --queue=orders_queue`

#### Create a service for queue "invoices_queue" and "shipments_queue"
`php artisan windows:service:queue:create LaravelInvoicesAndShipmentsQueues --queue=orders_queue,shipments_queue`

### Uninstall instructions:
- Run `bin/{WINDOWS_SERVICE_NAME}/LaravelQueueService.exe` as administrator and click on `Uninstall` button to remove Windows service.

### Commands:
- `windows:service:queue:create {WINDOWS_SERVICE_NAME}` - Create new configuration.
- `windows:service:queue:restart --windowsServiceName={WINDOWS_SERVICE_NAME}` - Gracefully exit
- `windows:service:queue:work --windowsServiceName={WINDOWS_SERVICE_NAME}` - Start service
- Use `--help` option for display details.

### Windows service project:
https://github.com/Litiano/Windows-service-for-Laravel-queue
