<?php

declare(strict_types=1);

namespace Jfcherng\Diff\Renderer;

use Jfcherng\Diff\Utility\Language;

/**
 * Abstract class for diff renderers.
 */
abstract class AbstractRenderer implements RendererInterface
{
    /**
     * @var array information about this template
     */
    const INFO = [
        'desc' => 'default_desc',
    ];

    /**
     * @var bool Is this template pure text?
     */
    const IS_HTML_TEMPLATE = true;

    /**
     * @var string the output result when the old and the new are the same
     */
    const IDENTICAL_RESULT = '';

    /**
     * @var \Jfcherng\Diff\Diff the instance of the diff class that this renderer is generating the rendered diff for
     */
    public $diff;

    /**
     * @var Language the language translation object
     */
    protected $t;

    /**
     * @var array array of the default options that apply to this renderer
     */
    protected static $defaultOptions = [
        'language' => 'eng',
        'tabSize' => 4,
    ];

    /**
     * @var array array containing the user applied and merged default options for the renderer
     */
    protected $options = [];

    /**
     * The constructor. Instantiates the rendering engine and if options are passed,
     * sets the options for the renderer.
     *
     * @param array $options optionally, an array of the options for the renderer
     */
    public function __construct(array $options = [])
    {
        $this->setOptions($options);
    }

    /**
     * Set the options of the renderer to those supplied in the passed in array.
     * Options are merged with the default to ensure that there aren't any missing
     * options.
     *
     * @param array $options array of options to set
     *
     * @return self
     */
    public function setOptions(array $options): self
    {
        $newOptions = $options + static::$defaultOptions;

        $this->updateLanguage($this->options['language'], $newOptions['language']);

        $this->options = $newOptions;

        return $this;
    }

    /**
     * Get the options.
     *
     * @return array the options
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * Update the Language object.
     *
     * @param string $old the old language
     * @param string $new the new language
     *
     * @return self
     */
    protected function updateLanguage(string $old, string $new): self
    {
        if (!isset($this->t) || $old !== $new) {
            $this->t = new Language($new);
        }

        return $this;
    }

    /**
     * A shorthand to do translation.
     *
     * @param string $text       The text
     * @param bool   $escapeHtml Escape the translated text for HTML?
     *
     * @return string the translated text
     */
    protected function _(string $text, bool $escapeHtml = true): string
    {
        $text = $this->t->translate($text);

        return $escapeHtml ? \htmlspecialchars($text) : $text;
    }
}
