<?php

namespace Laravelayers\Admin\Console\Commands;

use Laravelayers\Foundation\Console\Commands\StubPublishCommand;

class AdminStubPublishCommand extends StubPublishCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:stub-publish {--force : Overwrite any existing files}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish all admin stubs that are available for customization';

    /**
     * The directory of the file.
     *
     * @var string
     */
    protected $dir = __DIR__;

    /**
     * The directory from which the files will be copied.
     *
     * @var string
     */
    protected $to = '/admin';

    /**
     * String as information output.
     *
     * @var string
     */
    protected $info = 'Admin stubs published successfully.';
}
