<?php

declare(strict_types=1);

use Calcagno\Validator\ValidateAttributes;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

#[CoversClass(\Calcagno\Validator\ValidateAttributes::class)]
final class ValidateAttributesTest extends TestCase
{
  private object $traitStub;

  protected function setUp(): void
  {
    $this->traitStub = new class {
      use ValidateAttributes;

      public function callProtectedIsValidCpf(mixed $cpf): bool
      {
        return $this->isValidCpf($cpf);
      }
    };
  }

  #[TestWith(["606.158.490-32"])]
  #[TestWith(["417.406.010-09"])]
  #[TestWith(["517.605.850-25"])]
  #[TestWith(['529.982.247-25'])]
  #[TestWith(["60615849032"])]
  #[TestWith(["41740601009"])]
  #[TestWith(["51760585025"])]
  #[TestWith(['12345678909'])]
  #[TestWith(['11144477735'])]
  #[TestWith([null])]
  #[TestWith([''])]
  public function testValidCpfs(mixed $value)
  {
    $this->assertTrue($this->traitStub->callProtectedIsValidCpf($value), "Falhou CPF: $value");
  }

  #[TestWith(['000.000.000-00'])]
  #[TestWith(['12345678900'])]
  #[TestWith(['52998224724'])]
  #[TestWith(['5299822472'])]
  #[TestWith(['abc.def.ghi-jk'])]
  #[TestWith(["606.158.490-34"])]
  #[TestWith(["417.406.011-09"])]
  #[TestWith(["517.608.850-25"])]
  #[TestWith(["60615849034"])]
  #[TestWith(["41740601109"])]
  #[TestWith(["51760885025"])]
  #[TestWith(["5331515207"])]
  #[TestWith(["11111111111"])]
  #[TestWith(["22222222222"])]
  #[TestWith(["33333333333"])]
  #[TestWith(["44444444444"])]
  #[TestWith(["55555555555"])]
  #[TestWith(["66666666666"])]
  #[TestWith(["77777777777"])]
  #[TestWith(["88888888888"])]
  #[TestWith(["99999999999"])]
  #[TestWith(["00000000000"])]
  #[TestWith(["abc15849034"])]
  #[TestWith(["606def49034"])]
  #[TestWith(["606158ghi34"])]
  #[TestWith(["000000000jk"])]
  #[TestWith(["abcdefghijk"])]
  #[TestWith(["abcdefghij"])]
  #[TestWith(["julia.jacome"])]
  #[TestWith(["606abc158def490gh32"])]
  #[TestWith([array()])]
  #[TestWith([123])]
  #[TestWith([true])]
  public function testInvalidCpfs(mixed $value)
  {
    $this->assertFalse($this->traitStub->callProtectedIsValidCpf($value), "Falhou CPF: $value");
  }
}
