<?php

declare(strict_types=1);

namespace Calcagno\Validator;

trait ValidateAttributes
{
  private function validateCpf(mixed $value): bool
  {
    if (!is_string($value)) {
      throw new \TypeError('Invalid type provided for CPF. Expected string.');
    }

    if (is_null($value) || empty($value)) {
      return true;
    }

    $value = preg_replace('/\D/', '', $value);

    if (mb_strlen($value) != 11)
      return false;

    if (preg_match('/^(.)\1*$/', $value))
      return false;

    for ($s = 10, $n = 0, $i = 0; $s >= 2; $n += $value[$i++] * $s--);

    if ($value[9] != ((($n %= 11) < 2) ? 0 : 11 - $n))
      return false;

    for ($s = 11, $n = 0, $i = 0; $s >= 2; $n += $value[$i++] * $s--);

    if ($value[10] != ((($n %= 11) < 2) ? 0 : 11 - $n))
      return false;

    return true;
  }

  private function validateCnpj(mixed $value): bool
  {
    if (!is_string($value)) {
      throw new \TypeError('Invalid type provided for CPF. Expected string.');
    }

    if (is_null($value) || empty($value)) {
      return true;
    }

    $c = preg_replace('/\D/', '', $value);
    $b = array(6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2);

    if (mb_strlen($c) != 14)
      return false;

    if (preg_match('/^(.)\1*$/', $value))
      return false;

    for ($i = 0, $n = 0; $i < 12; $n += $c[$i] * $b[++$i]);

    if ($c[12] != ((($n %= 11) < 2) ? 0 : 11 - $n))
      return false;

    for ($i = 0, $n = 0; $i <= 12; $n += $c[$i] * $b[$i++]);

    if ($c[13] != ((($n %= 11) < 2) ? 0 : 11 - $n))
      return false;

    return true;
  }

  public function validatePhone(mixed $value): bool
  {
    if (!is_string($value)) {
      throw new \TypeError('Invalid type provided for Telefone. Expected string.');
    }

    if (is_null($value) || empty($value)) {
      return true;
    }

    return preg_match('/^(\(0?\d{2}\)\s?|0?\d{2}[\s.-]?)\d{4,5}[\s.-]?\d{4}$/', $value);
  }

  public function validateRequired(mixed $value): bool
  {
    if (is_null($value)) {
      return false;
    }

    if ((is_string($value) || is_countable($value)) && empty($value)) {
      return false;
    }

    return (is_numeric($value)) ? (bool)$value : true;
  }
}
