<?php

namespace AwemaPL\LocalizationHelper;

use AwemaPL\LocalizationHelper\Contracts\LocalizationHelper as LocalizationHelperContract;
use Illuminate\Support\Str;
use Illuminate\Translation\Translator;

class LocalizationHelper implements LocalizationHelperContract
{
    protected $basePath;

    /** @var Translator $translator */
    protected $translator;

    /**
     * LocalizationHelper constructor.
     * @param $basePath
     */
    public function __construct($basePath)
    {
        $this->basePath = $basePath;

        $this->translator = app('translator');
    }

    /**
     * Translate the given message
     *
     * @param $key
     * @param $default
     * @param $placeholders
     * @return array|null|string
     */
    public function trans($key, $default = null, $placeholders = [])
    {

        $translation = __($key, $placeholders);

        if ($translation === $key){
            return $default;
        }

        if (is_array($translation)) {
            return $key;
        }

        if (config('app.debug')) {

            if (!is_array($default)) {
                $default = [$this->translator->getFallback() => $default];
            }

           if (config('localizationhelper.debug_auto_create_lang.active')){
               foreach ($default as $locale => $item) {
                   if (!in_array($locale, config('localizationhelper.debug_auto_create_lang.secure_override_language_files'))){
                       $this->updateTranslation($key, $item, $locale);
                   }
               }
           }
            $translation = __($key, $placeholders);
        }

        return $translation;
    }

    private function updateTranslation($key, $default, $locale)
    {
        $path = explode('.', $key);

        if ($this->canBeUpdated($default, $key, $locale) && !empty($path[1])) {

            $keyAddLines = (Str::contains($key, '::')) ? explode('::', $key)[1] : $key;
            $namespace =  (Str::contains($key, '::')) ? explode('::', $key)[0] : '*';

            $this->translator->addLines([$keyAddLines => $default], $locale, $namespace);

            $this->translator->addLines([$key => $default], $locale);

            $this->writeToLangFile(
                $locale,
                $this->translator->get($path[0], [], $locale),
                $path[0]
            );

        }
    }

    private function canBeUpdated($default, $key, $locale)
    {
        if (!$default || $this->translator->hasForLocale($key, $locale)) {
            return false;
        }
        $parsedKey = $this->translator->parseKey($key);
        [$namespace, $path, $item] = $parsedKey;
        $items = array_filter(explode('.', $item));
        foreach ($items as $item) {
            $path .= '.' . $item;
            if ($this->translator->hasForLocale($path, $locale)
                && is_string($this->translator->get($path, [], $locale))) {
                return false;
            }
        }

        return true;
    }

    /**
     * Write to language file
     *
     * @param $locale
     * @param $translations
     * @return bool
     */
    private function writeToLangFile($locale, $translations, $filename)
    {
        if (Str::contains($filename, '::')){
            $file = $this->getFileLangModule($locale, $filename);
        } else {
            $file = $this->basePath . "/{$locale}/{$filename}.php";
        }

        $dir = dirname($file);
    if (!file_exists($dir)){
        mkdir($dir, 0777, true);
    }

        try {
            if (($fp = fopen($file, 'w')) !== FALSE) {
                $header = "<?php\n\nreturn ";
                fputs($fp, $header . $this->var_export54($translations) . ";\n");
                fclose($fp);

                return true;
            }
        } catch (\Exception $e) {
            return false;
        }
    }

    private function getFileLangModule($locale, $filename)
    {
        $filenameExplode = explode('::', $filename);
        $filename = explode('.', $filenameExplode[1])[0];
        $moduleName = mb_strtolower($filenameExplode[0]);

        $parentDirModule = config('localizationhelper.debug_auto_create_lang.parent_dir_module');
        $prefixDirModule = config('localizationhelper.debug_auto_create_lang.prefix_dir_module');

        $moduleDir = $prefixDirModule . $moduleName;

        if (config('localizationhelper.debug_auto_create_lang.dir_module_ucfirst')){
            $moduleDir = Str::ucfirst($moduleDir);
        }

        return "$parentDirModule/$moduleDir/resources/lang/{$locale}/{$filename}.php";
    }

    /**
     * var_export to php5.4 array syntax
     * https://stackoverflow.com/questions/24316347/how-to-format-var-export-to-php5-4-array-syntax
     *
     * @param $var
     * @param string $indent
     * @return mixed|string
     */
    private function var_export54($var, $indent = "")
    {
        switch (gettype($var)) {
            case "string":
                return '"' . addcslashes($var, "\\\$\"\r\n\t\v\f") . '"';
            case "array":
                $indexed = array_keys($var) === range(0, count($var) - 1);
                $r = [];
                foreach ($var as $key => $value) {
                    $r[] = "$indent    "
                        . ($indexed ? "" : $this->var_export54($key) . " => ")
                        . $this->var_export54($value, "$indent    ");
                }
                return "[\n" . implode(",\n", $r) . "\n" . $indent . "]";
            case "boolean":
                return $var ? "TRUE" : "FALSE";
            default:
                return var_export($var, TRUE);
        }
    }
}
