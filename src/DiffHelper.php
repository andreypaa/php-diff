<?php

declare(strict_types=1);

namespace Jfcherng\Diff;

use Jfcherng\Diff\Renderer\RendererConstant;
use Jfcherng\Diff\Utility\RendererFactory;

class DiffHelper
{
    /**
     * Get the information about available templates.
     *
     * @return array
     */
    public static function getTemplatesInfo(): array
    {
        static $templatesInfo;

        if (isset($templatesInfo)) {
            return $templatesInfo;
        }

        $glob = \implode(
            \DIRECTORY_SEPARATOR,
            [
                __DIR__,
                'Renderer',
                '{' . \implode(',', RendererConstant::TEMPLATE_TYPES) . '}',
                '*.php',
            ]
        );

        $files = \array_filter(
            \glob($glob, \GLOB_BRACE),
            // not an abstact class
            function (string $file): bool {
                return \strpos($file, 'Abstract') === false;
            }
        );

        // class name = file name without the extension
        $templates = \array_map(
            function (string $file): string {
                return \basename($file, '.php');
            },
            $files
        );

        $info = [];
        foreach ($templates as $template) {
            $info[$template] = RendererFactory::resolveTemplate($template)::INFO;
        }

        return $templatesInfo = $info;
    }

    /**
     * Get the available templates.
     *
     * @return string[] the available templates
     */
    public static function getAvailableTemplates(): array
    {
        return \array_keys(static::getTemplatesInfo());
    }

    /**
     * All-in-one static method to calculate the diff.
     *
     * @param string|string[] $old             the old string (or array of lines)
     * @param string|string[] $new             the new string (or array of lines)
     * @param string          $template        the template name
     * @param array           $diffOptions     the options for Diff object
     * @param array           $templateOptions the options for template object
     *
     * @return string the difference
     */
    public static function calculate($old, $new, string $template = 'Unified', array $diffOptions = [], array $templateOptions = []): string
    {
        // always convert into array form
        \is_string($old) && ($old = \explode("\n", $old));
        \is_string($new) && ($new = \explode("\n", $new));

        // the "no difference" situation may happen frequently
        // let's save some calculation if possible
        if ($old === $new) {
            return RendererFactory::resolveTemplate($template)::IDENTICAL_RESULT;
        }

        return Diff::getInstance()
            ->setA($old)
            ->setB($new)
            ->setOptions($diffOptions)
            ->render(
                RendererFactory::getInstance($template)
                    ->setOptions($templateOptions)
            );
    }
}
