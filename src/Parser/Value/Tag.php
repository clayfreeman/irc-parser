<?php

declare(strict_types = 1);

namespace ClayFreeman\IRCParser\Parser\Value;

/**
 * Represents a single IRCv3 tag.
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
   * The fully-qualified name contains the client-only prefix (if applicable),
   * followed by an optional vendor name, followed by the base name of the tag.
   *
   * @return string
   *   The fully-qualified name of the tag.
   */
  public function name(): string {
    $prefix = $this->clientOnly ? '+' : '';

    $name = $this->name;
    if (isset($this->vendor)) {
      $name = "{$this->vendor}/{$this->name}";
    }

    return $prefix . $name;
  }

  /**
   * Renders the tag in the format specified by IRCv3.
   *
   * @return string
   *   The tag rendered in the format specified by IRCv3.
   */
  public function render(): string {
    $render = '';

    if ($this->value !== FALSE && strlen($this->value) !== 0) {
      $suffix = is_string($this->value) ? "={$this->value}" : '';
      $render = $this->name() . $suffix;
    }

    return $render;
  }

}
