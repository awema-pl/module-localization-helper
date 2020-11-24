<?php

namespace AwemaPL\LocalizationHelper\Contracts;

interface LocalizationHelper
{
    /**
     * Translate the given message
     *
     * @param $key
     * @param null $default
     * @param array $placeholders
     * @return mixed
     */
    public function trans($key, $default = null, $placeholders = []);

    /**
     * Auto copy translations
     *
     * @return mixed
     */
    public function autoCopyTranslations();
}
