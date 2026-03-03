<?php

namespace App\Providers;

use App\Models\SeoMeta;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);

        View::composer(
            ['components.main-layout', 'layouts.app', 'layouts.guest', 'layouts.admin'],
            function ($view) {
                static $hasSeoTable = null;

                $defaultTitle = config('app.name', 'WatchMarket');
                $seo = [
                    'title' => $defaultTitle,
                    'description' => $defaultTitle,
                ];

                if ($hasSeoTable === null) {
                    $hasSeoTable = Schema::hasTable('seo_meta');
                }

                if ($hasSeoTable) {
                    $routeName = request()->route()?->getName();
                    if ($routeName) {
                        $meta = SeoMeta::query()
                            ->where('route_name', $routeName)
                            ->where('is_active', true)
                            ->first();

                        if ($meta) {
                            $seo['title'] = $meta->meta_title ?: $seo['title'];
                            $seo['description'] = $meta->meta_description ?: $seo['description'];
                        }
                    }
                }

                $view->with('seoMeta', $seo);
            }
        );
    }
}
