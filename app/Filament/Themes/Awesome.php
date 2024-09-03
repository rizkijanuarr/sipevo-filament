use Filament\Panel;
use Hasnayeen\Themes\Contracts\CanModifyPanelConfig;
use Hasnayeen\Themes\Contracts\Theme;

class Awesome implements CanModifyPanelConfig, Theme
{
    public static function getName(): string
    {
        return 'awesome';
    }

    public static function getPublicPath(): string
    {
        return 'resources/css/filament/app/themes/awesome.css';
    }

    public function getThemeColor(): array
    {
        return [
            'primary' => '#000',
            'secondary' => '#fff',
        ];
    }

    public function modifyPanelConfig(Panel $panel): Panel
    {
        return $panel
            ->viteTheme($this->getPath())
            ->topNavigation();
    }
}
