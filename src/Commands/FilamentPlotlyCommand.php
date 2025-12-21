<?php

namespace Asharif88\FilamentPlotly\Commands;

use Filament\Facades\Filament;
use Filament\Panel;
use Filament\Resources\Resource;
use Filament\Support\Commands\Concerns\CanManipulateFiles;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;

use function Laravel\Prompts\select;
use function Laravel\Prompts\text;

class FilamentPlotlyCommand extends Command
{
    use CanManipulateFiles;

    public $signature = 'make:filament-plotly {name?}';

    public $description = 'Creates a Filament Plotly Widget class.';

    /**
     * Filesystem instance
     */
    protected Filesystem $files;

    /**
     * Chart options
     */
    // private array $chartOptions;

    /**
     * Create a new command instance.
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct();

        $this->files = $files;
    }

    public function handle(): int
    {
        $widget = (string) str($this->argument('name') ?? text(
            label: 'What is the chart name?',
            placeholder: 'BlogPostsChart',
            required: true,
        ))
            ->trim('/')
            ->trim('\\')
            ->trim(' ')
            ->replace('/', '\\');

        $resource = null;

        if (class_exists(Resource::class)) {
            $resourceInput = text(
                label: 'What is the resource you would like to create this in?',
                placeholder: '[Optional] BlogPostResource',
            );

            if (filled($resourceInput)) {
                $resource = (string) str($resourceInput)
                    ->studly()
                    ->trim('/')
                    ->trim('\\')
                    ->trim(' ')
                    ->replace('/', '\\');

                if (! str($resource)->endsWith('Resource')) {
                    $resource .= 'Resource';
                }
            }
        }

        $panel = null;

        if (class_exists(Panel::class)) {

            $panels = Filament::getPanels();
            $namespace = config('livewire.class_namespace');

            /** @var ?Panel $panel */
            $panel = $panels[select(
                label: 'Where would you like to create this?',
                options: array_unique([
                    ...array_map(
                        fn (Panel $panel): string => "The [{$panel->getId()}] panel",
                        $panels,
                    ),
                    $namespace => "[{$namespace}] alongside other Livewire components",
                ])
            )] ?? null;
        }

        $path = null;
        $namespace = null;
        $resourcePath = null;
        $resourceNamespace = null;

        if (! $panel) {
            $namespace = config('livewire.class_namespace');
            $path = app_path((string) str($namespace)->after('App\\')->replace('\\', '/'));
        } elseif ($resource === null) {
            $widgetDirectories = $panel->getWidgetDirectories();
            $widgetNamespaces = $panel->getWidgetNamespaces();

            $namespace = (count($widgetNamespaces) > 1) ?
                select(
                    label: 'Which namespace would you like to create this in?',
                    options: $widgetNamespaces,
                ) :
                (Arr::first($widgetNamespaces) ?? 'App\\Filament\\Widgets');
            $path = (count($widgetDirectories) > 1) ?
                $widgetDirectories[array_search($namespace, $widgetNamespaces)] :
                (Arr::first($widgetDirectories) ?? app_path('Filament/Widgets/'));
        } else {
            $resourceDirectories = $panel->getResourceDirectories();
            $resourceNamespaces = $panel->getResourceNamespaces();

            $resourceNamespace = (count($resourceNamespaces) > 1) ?
                select(
                    label: 'Which namespace would you like to create this in?',
                    options: $resourceNamespaces,
                ) :
                (Arr::first($resourceNamespaces) ?? 'App\\Filament\\Resources');
            $resourcePath = (count($resourceDirectories) > 1) ?
                $resourceDirectories[array_search($resourceNamespace, $resourceNamespaces)] :
                (Arr::first($resourceDirectories) ?? app_path('Filament/Resources/'));
        }

        if ($path) {
            $this->makeDirectory($path);
            $contents = $this->getSourceFile($namespace, $widget);
            $file = $path . '/' . $widget . '.php';
            if ($this->files->exists($file)) {
                $this->error("File : {$file} already exits!");
                exit();
            }

            $fileCount = count($this->files->files(dirname($file)));

            $this->files->put($file, $contents);

            $this->info("Successfully created {$widget}!");

            if ($fileCount === 0) {
                $this->welcomeMessage();
            }
        } elseif ($resourcePath) {

            $resourceFqn = str($resource)->contains('\\') ? $resource : ($resourceNamespace ? $resourceNamespace . '\\' . $resource : $resource);

            if (class_exists($resourceFqn)) {
                $reflection = new \ReflectionClass($resourceFqn);

                $pluralResourceBasenameBeforeResource = (string) str($resourceFqn)
                    ->classBasename()
                    ->beforeLast('Resource')
                    ->plural();

                $resourceNamespacePartBeforeBasename = (string) str($resourceFqn)
                    ->beforeLast('\\')
                    ->classBasename();

                if ($pluralResourceBasenameBeforeResource === $resourceNamespacePartBeforeBasename) {
                    $widgetsNamespace = (string) str($resourceFqn)->beforeLast('\\')->append('\\Widgets');
                    $widgetsDirectory = (string) str($reflection->getFileName())->beforeLast(DIRECTORY_SEPARATOR)->append('/Widgets');
                } else {
                    $widgetsNamespace = $resourceFqn . '\\Widgets';
                    $widgetsDirectory = (string) str($reflection->getFileName())->beforeLast('.')->append('/Widgets');
                }
            } else {
                // Guess standard layout: Resources/{PluralResourceName}/Widgets
                $base = (string) str($resource)->afterLast('\\')->beforeLast('Resource');
                $folder = (string) Str::of($base)->plural();

                $widgetsNamespace = rtrim($resourceNamespace, '\\') . '\\' . $folder . '\\Widgets';
                $widgetsDirectory = rtrim($resourcePath, '/\\') . DIRECTORY_SEPARATOR . $folder . DIRECTORY_SEPARATOR . 'Widgets';
            }

            // Ensure the directory exists and write the widget class into it
            $this->makeDirectory($widgetsDirectory);

            $contents = $this->getSourceFile($widgetsNamespace, $widget);

            $file = rtrim($widgetsDirectory, '/\\') . DIRECTORY_SEPARATOR . $widget . '.php';

            if ($this->files->exists($file)) {
                $this->error("File : {$file} already exits!");
                exit();
            }

            $fileCount = count($this->files->files(dirname($file)));

            $this->files->put($file, $contents);

            $resourceClassName = (string) str($resource)->afterLast('\\');

            $this->info("Successfully created {$resourceClassName} widget! Make sure to register the widget in `{$resourceClassName}::getWidgets()`, and then again in `getHeaderWidgets()` or `getFooterWidgets()` of any `{$resourceClassName}` page.");

            if ($fileCount === 0) {
                $this->welcomeMessage();
            }
        }

        return self::SUCCESS;
    }

    /**
     * Build the directory for the class if necessary.
     */
    protected function makeDirectory(string $path): string
    {
        if (! $this->files->isDirectory($path)) {
            $this->files->makeDirectory($path, 0777, true, true);
        }

        return $path;
    }

    /**
     * Return the stub file path
     */
    public function getStubPath(): string
    {
        $path = Str::of(__DIR__);

        $path = match (PHP_OS_FAMILY) {
            default => $path->replace('src/Commands', 'stubs/'),
            'Windows' => $path->replace('src\Commands', 'stubs\\')
        };

        return $path->append('Chart.stub');
    }

    /**
     **
     * Map the stub variables present in stub to its value
     */
    public function getStubVariables($namespace, $widget): array
    {
        return [
            'NAMESPACE' => $namespace,
            'CLASS_NAME' => $widget,
            'CHART_ID' => Str::of($widget)->camel(),
        ];
    }

    /**
     * Get the stub path and the stub variables
     */
    public function getSourceFile($namespace, $widget): Stringable
    {
        return $this->getStubContents($this->getStubPath(), $this->getStubVariables($namespace, $widget));
    }

    /**
     * Replace the stub variables(key) with the desire value
     */
    public function getStubContents($stub, array $stubVariables = []): Stringable
    {
        $contents = file_get_contents($stub);

        foreach ($stubVariables as $search => $replace) {
            $contents = Str::of($contents)->replace('$' . $search . '$', $replace);
        }

        return $contents;
    }

    private function welcomeMessage(): void
    {
        if ($this->confirm('Would you like to show some love by starring the repo?', true)) {
            if (PHP_OS_FAMILY == 'Darwin') {
                exec('open https://github.com/Asharif88/filament-plotly');
            }
            if (PHP_OS_FAMILY == 'Windows') {
                exec('start https://github.com/Asharif88/filament-plotly');
            }
            if (PHP_OS_FAMILY == 'Linux') {
                exec('xdg-open https://github.com/Asharif88/filament-plotly');
            }

            $this->line('Thanks! :)');
        }
    }
}
