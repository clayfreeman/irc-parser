<?php

declare(strict_types = 1);

namespace ClayFreeman\IRCParser\Parser\Value;

/**
 * Represents a single IRCv3 lexeme.
 */
class Tag {

  /**
   * Determines whether the flag is client-only.
   *
   * @var bool
   */
  public bool $clientOnly = FALSE;

  /**
   * The base name of the tag (not including the vendor).
   *
   * @var string
   */
  public string $name = '';

  /**
   * The vendor-specific namespace of the tag, or NULL for the global namespace.
   *
   * @var string|null
   */
  public ?string $vendor = NULL;

  /**
   * The value of the tag.
   *
   * This property defaults to TRUE for tags without an explicit value set.
   *
   * @var bool|string
   */
  public bool|string $value = TRUE;

  /**
   * Gets the fully-qualified name of the tag.
   *
   * If a vendor is specified, the fully-qualified name of the tag will follow
   * the format "<vendor>/<name>". In all other cases, the fully-qualified name
   * is exactly the same as the base name.
   *
   * @return string
   *   The fully-qualified name of the tag.
   */
  public function name(): string {
    return isset($this->vendor) ? "{$this->vendor}/{$this->name}" : $this->name;
  }

  /**
   * Renders the tag in the format specified by IRCv3.
   *
   * @return string
   *   The tag rendered in the format specified by IRCv3.
   */
  public function render(): string {
    $render = '';

    $prefix = $this->clientOnly ? '+' : '';
    $suffix = is_string($this->value) ? "={$this->value}" : '';

    if ($this->value !== FALSE) {
      $render = $prefix . $this->name() . $suffix;
    }

    return $render;
  }

}
