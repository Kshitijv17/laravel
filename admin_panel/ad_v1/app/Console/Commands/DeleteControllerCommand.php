<?php
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class DeleteControllerCommand extends Command
{
    protected $signature = 'delete:controller {controllerName : The name of the controller you want to delete}';

    protected $description = 'Delete a controller by name';

    public function handle()
    {
        $name = $this-argument('controllerName');
        $path = app_path("Http/Controllers/{$name}.php");

        if (File::exists($path)) {
            File::delete($path);
            $this->info("{$name} deleted");
        } else {
            $this->error("{$name} does not exist in Http/Controllers/");
        }
    }
}