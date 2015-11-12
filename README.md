# Desktop Notifications

## Creating new Notification Tasks

* Create a class implementing Kyoushu\DesktopNotifications\Task
* Add an instance of the class to the array returned by Kyoushu\DesktopNotifications\TaskManager::getTasks()

## Running the Notification Processor

Add a cron job which runs the following command

    php path/to/root/dir/process.php