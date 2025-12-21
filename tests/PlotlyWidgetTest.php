<?php

use Asharif88\FilamentPlotly\Tests\TestCase;
use Asharif88\FilamentPlotly\Widgets\PlotlyWidget;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\DataProvider;

final class PlotlyWidgetTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->widgetClass = new class extends PlotlyWidget
        {
            protected static ?string $heading = 'Test Plotly Widget Heading';

            protected static ?string $subheading = 'Test Plotly Widget Subheading';

            protected static ?string $footer = 'Test Plotly Widget Footer';

            protected function getChartData(): array
            {
                return [
                    'data' => [
                        [
                            'x' => [1, 2, 3, 4, 5],
                            'y' => [10, 15, 13, 17, 22],
                            'type' => 'scatter',
                            'mode' => 'lines+markers',
                            'marker' => ['color' => 'red'],
                        ],
                    ],
                    'layout' => [
                        'title' => 'Test Plotly Chart',
                    ],
                ];
            }
        };
    }

    public function test_widget_renders_correctly()
    {
        Livewire::test($this->widgetClass)
            ->assertViewIs('filament-plotly::widgets.plotly-widget')
            ->assertSee('Test Plotly Widget Heading')
            ->assertSee('Test Plotly Widget Subheading')
            ->assertSee('Test Plotly Widget Footer');
    }

    public function test_widget_chart_id()
    {
        $this->assertInstanceOf(PlotlyWidget::class, $this->widgetClass);
        $this->assertTrue(method_exists($this->widgetClass, 'getChartId'));
        $this->assertTrue(property_exists($this->widgetClass, 'chartId'));
    }

    /**
     * Test Properties existence
     */
    #[DataProvider('widgetPropertyProvider')]
    public function test_widget_has_required_properties(string $propertyName): void
    {
        $this->assertTrue(
            property_exists($this->widgetClass, $propertyName),
            "The property '{$propertyName}' is missing ."
        );
    }

    /**
     * Test Methods existence
     */
    #[DataProvider('widgetMethodProvider')]
    public function test_widget_has_required_methods(string $methodName): void
    {
        $this->assertTrue(
            method_exists($this->widgetClass, $methodName),
            "The method '{$methodName}()' is missing."
        );
    }

    // --- Data Providers ---

    /**
     * Returns a simple list of required property names.
     */
    public static function widgetPropertyProvider(): array
    {
        return [
            'Heading' => ['heading'],
            'Content Height' => ['contentHeight'],
            'Footer' => ['footer'],
            'Loading Indicator' => ['loadingIndicator'],
            'Defer Loading' => ['deferLoading'],
            'Ready to load' => ['readyToLoad'],
        ];
    }

    /**
     * Returns method names
     */
    public static function widgetMethodProvider(): array
    {
        return [
            'Get Heading' => ['getHeading'],
            'Get Content Height' => ['getContentHeight'],
            'Get Footer' => ['getFooter'],
            'Get Loading Indicator' => ['getLoadingIndicator'],
            'Submit Filters' => ['submitFiltersForm'],
            'Reset Filters' => ['resetFiltersForm'],
            'Get Chart Data' => ['getChartData'],
            'Get Chart Layout' => ['getChartLayout'],
            'Get Chart Config' => ['getChartConfig'],
            'Update options' => ['updateOptions'],
        ];
    }
}
