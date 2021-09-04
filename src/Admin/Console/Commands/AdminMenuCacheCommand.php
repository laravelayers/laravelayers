<?php

namespace Laravelayers\Admin\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

class AdminMenuCacheCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'admin:menu-cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update cache file of admin menu bar.';

    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * Create a new admin command instance.
     *
     * @param  \Illuminate\Filesystem\Filesystem  $files
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct();

        $this->files = $files;
    }

    /**
     * Execute the console command.
     *
     * @return void
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function handle()
    {
        $this->files->delete($this->laravel->bootstrapPath() . $this->getCachedAdminFile());

        $menuItems = $this->getAdminMenuItems();

        $this->files->put(
            $this->laravel->bootstrapPath() . $this->getCachedAdminFile(), $this->buildAdminCacheFile($menuItems)
        );

        $this->info('Admin menu bar cache file updated successfully!');
    }

    /**
     * Get the path to the admin cache file.
     *
     * @return string
     */
    public static function getCachedAdminFile()
    {
        return '/cache/adminMenu.php';
    }

    /**
     * Get the admin menu items.
     *
     * @return $this|array|static
     */
    public function getAdminMenuItems()
    {
        $items = [];
        $i = 0;

        foreach ($this->getAdminClasses() as $key => $class)
        {
            if (method_exists($class, 'getMenuItem')) {
                if ($item = resolve($class)->getMenuItem()) {
                    $i++;
                    $items[$i] = $item;

                    if (!empty($item['item'])) {
                        unset($items[$i]['item']);

                        $i++;
                        $items[$i] = $item['item'];
                    }
                }
            }
        }

        array_multisort(array_map(function ($item) {
            return $item['sorting'];
        }, $items), SORT_ASC, SORT_REGULAR, $items);

        foreach($items as $key => $item) {
            $items[$key]['sorting'] = preg_match('/^[0-9]+$/', $item['sorting']) ? $item['sorting'] : $key + 1;
        }

        return $items;
    }

    /**
     * Get administration classes.
     *
     * Based on <http://qaru.site/questions/182186/php-get-all-class-names-inside-a-particular-namespace>.
     *
     * @return array
     */
    protected function getAdminClasses()
    {
        $finder = new Finder();

        if (file_exists(app_path('Http/Controllers/Admin'))) {
            $finder->files()->in(app_path('Http/Controllers/Admin'));
        }

        $finder->files()->in(dirname(__DIR__, 2) . '/Controllers');

        $fqcns = [];
        
        foreach ($finder as $file) {
            if ($file->getExtension() == 'php') {
                $content = file_get_contents($file->getRealPath());
                $tokens = token_get_all($content);
                $namespace = '';

                for ($index = 0; isset($tokens[$index]); $index++) {
                    if (!isset($tokens[$index][0])) {
                        continue;
                    }
                    if (T_NAMESPACE === $tokens[$index][0]) {
                        $index += 2; // Skip namespace keyword and whitespace
                        while (isset($tokens[$index]) && is_array($tokens[$index])) {
                            $namespace .= $tokens[$index++][1];
                        }
                    }
                    if (T_CLASS === $tokens[$index][0]) {
                        $index += 2; // Skip class keyword and whitespace
                        if (!is_string($tokens[$index])) {
                            $fqcns[] = $namespace . '\\' . $tokens[$index][1];
                        }
                    }
                }
            }
        }

        return $fqcns;
    }

    /**
     * Build the admin cache file.
     *
     * @param $menuItems
     * @return mixed
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function buildAdminCacheFile($menuItems)
    {
        $stub = $this->files->get(__DIR__.'/stubs/admin.menu.stub');

        $str = '';
        foreach ($menuItems as $key => $node) {
            $str .= "\t'{$key}' => [\n";

            foreach ($node as $name => $value) {
                $str .= "\t\t'{$name}' => '{$value}',\n";
            }

            $str .= "\t],\n";
        }

        return str_replace('{{menu}}', "return [\n{$str}];", $stub);
    }
}
