# For models which require some config values to be changed when switching instances

[![Latest Version on Packagist](https://img.shields.io/packagist/v/inmanturbo/laravel-delegator.svg?style=flat-square)](https://packagist.org/packages/inmanturbo/laravel-delegator)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/inmanturbo/laravel-delegator/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/inmanturbo/laravel-delegator/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/inmanturbo/laravel-delegator/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/inmanturbo/laravel-delegator/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/inmanturbo/laravel-delegator.svg?style=flat-square)](https://packagist.org/packages/inmanturbo/laravel-delegator)

Similar to tenancy, however this package doesn't limit you to a single model class, or "tenant".   
You can have a database model like `App\Models\TeamDatabase` or `App\Models\AppDatabase` that dynamically sets some config values (and any other action) when changed for the request, for example.
And/or You can also do stuff when switching users, or when switching teams, etc.

## Support us

## Installation

You can install the package via composer:

```bash
composer require inmanturbo/laravel-delegator
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="delegator-config"
```

This is the contents of the published config file:

```php
return [

    /*
     * The connection name to reach the delegator database
     */
    'delegator_database_connection_name' => null,

    'candidates' => [

        // 'team' => [

        //     /*
        //     * This class is responsible for determining which candidate should be current
        //     * for the given request.
        //     *
        //     * This class must implement the `Inmanturbo\Delegator\Contracts\CandidateFinder` interface.
        //     *
        //     */
        //     'candidate_finder' => null,

        //     /*
        //     * These fields are used by candidates:artisan command to match one or more tenant
        //     */
        //     'candidate_artisan_search_fields' => [
        //         'id',
        //     ],

        //     /*
        //     * These tasks will be performed when switching candidates.
        //     *
        //     * A valid task is any class that implements the `Inmanturbo\Delegator\Contracts\SwitchCandidateTask` interface.
        //     */
        //     'switch_candidate_tasks' => [
        //         \Inmanturbo\Delegator\Tasks\SwitchCandidateConfigTask::class,
        //     ],

        //     /*
        //     * This class is the model used for storing configuration on candidates.
        //     *
        //     * It must implemement the `Inmanturbo\Delegator\Models\Contracts\CandidateModel` interface.
        //     */
        //     'model' => \App\Models\Team::class,

        //     /*
        //     * The connection name to reach the candidate database.
        //     *
        //     * Set to `null` to use the default connection.
        //     */
        //     'candidate_database_connection_name' => null,

        //     /*
        //     * This key will be used to bind the current candidate in the container.
        //     */
        //     'current_candidate_container_key' => 'currentTeam',

        //     /*
        //     * You can customize some of the behavior of this package by using your own custom action.
        //     * Your custom action should always extend the default one.
        //     */
        //     'actions' => [
        //         'make_current_action' => \Inmanturbo\Delegator\Actions\MakeCandidateCurrentAction::class,
        //         'forget_current_action' => \Inmanturbo\Delegator\Actions\ForgetCandidateCurrentAction::class,
        //         'migrate_action' => \Inmanturbo\Delegator\Actions\MigrateCandidateAction::class,
        //     ],
        // ],
    ],
];
```

## Usage

First publish the config then uncomment the first `candidate` in the `candidates` array and fill in your correct values.

You can use any model that implements the `Inmanturbo\Delegator\Models\Contracts\CandidateModel` interface.   

You can use the trait `Inmanturbo\Delegator\Models\Concerns\HasCandidateMethods`, or write your own methods to satisfy the interface.

### Making a candidate Current

```php
$candidate = App\Models\Team::first(); // <-- must be a configured candidate `model` which implements

$candidate->makeCurrent();
```

The above will execute the `config('delegator.candidates.team.actions.make_current_action')` which will call `makeCurrent()` on all of the tasks listed under `config('delegator.candidates.team.actions.switch_candidate_tasks')`

You can use these tasks to change or set config keys and values, start a lawn mower, etc.

Calling `makeCurrent()` on another instance of the model of the same class will first execute `config('delegator.candidates.team.actions.forget_current_action')` which calls `forgetCurrent()` on all of the same tasks.

You can do this with as many model classes as you want by adding their configurations to the `candidates` array in the `delegator` config file.   
Each class will be tracked seperately, so you can have multiple current models of different types, but only one at a time of each type.

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [inmanturbo](https://github.com/inmanturbo)
- [All Contributors](../../contributors)
- This package was heavily inspired by [spatie/larvel-multitenancy](https://github.com/spatie/laravel-multitenancy)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
