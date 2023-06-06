<?php

namespace Shockedplot7560\FixerCustomRules\Tests\Fixer;

use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use function assert;
use function sprintf;
use function substr;

abstract class AbstractFixerTestCase extends TestCase{
	private const FIXER_NAMESPACE_PREFIX = "Shockedplot7560\\FixerCustomRules\\Fixer\\";

	protected FixerInterface $fixer;

	final protected function setUp() : void{
		$reflectionClass = new \ReflectionClass(static::class);

		$shortenClassName = substr($reflectionClass->getShortName(), 0, -4);
		$className = self::FIXER_NAMESPACE_PREFIX . $shortenClassName;

		$fixer = new $className();
		assert($fixer instanceof FixerInterface);

		$this->fixer = $fixer;
	}

	/**
	 * @param null|array<string, mixed> $configuration
	 * @throws Exception
	 */
	final protected function doTest(string $expected, ?string $input = null, ?array $configuration = null) : void{
		if ($expected === $input){
			throw new \InvalidArgumentException('Expected must be different to input.');
		}

		Tokens::clearCache();
		$expectedTokens = Tokens::fromCode($expected);

		Tokens::clearCache();
		$inputTokens = Tokens::fromCode($input);

		self::assertTrue($this->fixer->isCandidate($inputTokens));

		$this->fixer->fix($this->createMock(\SplFileInfo::class), $inputTokens);
		$inputTokens->clearEmptyTokens();

		self::assertSame(
			$expected,
			$actual = $inputTokens->generateCode(),
			sprintf(
				"Expected code:\n```\n%s\n```\nGot:\n```\n%s\n```\n",
				$expected,
				$actual,
			),
		);

		self::assertSameTokens($expectedTokens, $inputTokens);

		$this->fixer->fix($this->createMock(\SplFileInfo::class), $expectedTokens);

		self::assertSame($expected, $expectedTokens->generateCode());

		self::assertFalse($expectedTokens->isChanged());
	}

	private static function assertSameTokens(Tokens $expectedTokens, Tokens $inputTokens) : void{
		self::assertCount($expectedTokens->count(), $inputTokens, 'Both collections must have the same size.');

		/** @var Token $expectedToken */
		foreach ($expectedTokens as $index => $expectedToken){
			$inputToken = $inputTokens[$index];

			self::assertTrue(
				$expectedToken->equals($inputToken),
				sprintf("Token at index %d must be:\n%s,\ngot:\n%s.", $index, $expectedToken->toJson(), $inputToken->toJson()),
			);
		}
	}
}
