<?php

declare(strict_types=1);

namespace Akberkhanskypotential\Subscriptions\Providers;

use Akberkhanskypotential\Subscriptions\Models\Plan;
use Illuminate\Support\ServiceProvider;
use Akberkhanskypotential\Support\Traits\ConsoleTools;
use Akberkhanskypotential\Subscriptions\Models\PlanFeature;
use Akberkhanskypotential\Subscriptions\Models\PlanSubscription;
use Akberkhanskypotential\Subscriptions\Models\PlanSubscriptionUsage;
use Akberkhanskypotential\Subscriptions\Console\Commands\MigrateCommand;
use Akberkhanskypotential\Subscriptions\Console\Commands\PublishCommand;
use Akberkhanskypotential\Subscriptions\Console\Commands\RollbackCommand;

class SubscriptionsServiceProvider extends ServiceProvider
{
    use ConsoleTools;

    /**
     * The commands to be registered.
     *
     * @var array
     */
    protected $commands = [
        MigrateCommand::class => 'command.rinvex.subscriptions.migrate',
        PublishCommand::class => 'command.rinvex.subscriptions.publish',
        RollbackCommand::class => 'command.rinvex.subscriptions.rollback',
    ];

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(realpath(__DIR__.'/../../config/config.php'), 'rinvex.subscriptions');

        // Bind eloquent models to IoC container
        $this->app->singleton('rinvex.subscriptions.plan', $planModel = $this->app['config']['rinvex.subscriptions.models.plan']);
        $planModel === Plan::class || $this->app->alias('rinvex.subscriptions.plan', Plan::class);

        $this->app->singleton('rinvex.subscriptions.plan_feature', $planFeatureModel = $this->app['config']['rinvex.subscriptions.models.plan_feature']);
        $planFeatureModel === PlanFeature::class || $this->app->alias('rinvex.subscriptions.plan_feature', PlanFeature::class);

        $this->app->singleton('rinvex.subscriptions.plan_subscription', $planSubscriptionModel = $this->app['config']['rinvex.subscriptions.models.plan_subscription']);
        $planSubscriptionModel === PlanSubscription::class || $this->app->alias('rinvex.subscriptions.plan_subscription', PlanSubscription::class);

        $this->app->singleton('rinvex.subscriptions.plan_subscription_usage', $planSubscriptionUsageModel = $this->app['config']['rinvex.subscriptions.models.plan_subscription_usage']);
        $planSubscriptionUsageModel === PlanSubscriptionUsage::class || $this->app->alias('rinvex.subscriptions.plan_subscription_usage', PlanSubscriptionUsage::class);

        // Register console commands
        $this->registerCommands($this->commands);
    }

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot(): void
    {
        // Publish Resources
        $this->publishesConfig('akberkhanskypotential/subscriptions');
        $this->publishesMigrations('akberkhanskypotential/subscriptions');
        ! $this->autoloadMigrations('akberkhanskypotential/subscriptions') || $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');
    }
}
