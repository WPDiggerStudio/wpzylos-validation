<?php

declare(strict_types=1);

namespace WPZylos\Framework\Validation;

use WPZylos\Framework\Core\Contracts\ApplicationInterface;
use WPZylos\Framework\Core\ServiceProvider;
use WPZylos\Framework\I18n\Translator;

/**
 * Validation service provider.
 *
 * @package WPZylos\Framework\Validation
 */
class ValidationServiceProvider extends ServiceProvider
{
    /**
     * {@inheritDoc}
     */
    public function register(ApplicationInterface $app): void
    {
        parent::register($app);

        // Factory method for creating validators
        $this->bind('validator', function () {
            return function (array $data, array $rules, array $messages = [], array $attributes = []) {
                $translator = $this->app->has(Translator::class)
                    ? $this->make(Translator::class)
                    : null;

                return new Validator($data, $rules, $messages, $attributes, $translator);
            };
        });
    }
}
