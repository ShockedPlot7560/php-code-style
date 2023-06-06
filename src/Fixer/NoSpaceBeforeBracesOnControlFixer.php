<?php

namespace Shockedplot7560\FixerCustomRules\Fixer;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Tokens;
use function defined;
use const T_CATCH;
use const T_DO;
use const T_ELSE;
use const T_ELSEIF;
use const T_FINALLY;
use const T_FOR;
use const T_FOREACH;
use const T_FUNC_C;
use const T_FUNCTION;
use const T_IF;
use const T_MATCH;
use const T_SWITCH;
use const T_TRY;
use const T_WHILE;

class NoSpaceBeforeBracesOnControlFixer extends AbstractFixer {
	private const STRUCTURE_TOKENS = [
		T_DO,
		T_ELSE,
		T_ELSEIF,
		T_FINALLY,
		T_FOR,
		T_FOREACH,
		T_IF,
		T_WHILE,
		T_TRY,
		T_CATCH,
		T_SWITCH,
		T_FUNCTION,
		T_FUNC_C,
	];

	public function getName() : string{
		$name = parent::getName();

		return "ShockedPlot7560/$name";
	}

	public function isCandidate(Tokens $tokens) : bool{
		$controlStructureTokens = $this->getControlStructureTokens();

		return $tokens->isAnyTokenKindsFound($controlStructureTokens);
	}

	public function isRisky() : bool{
		return false;
	}

	protected function applyFix(\SplFileInfo $file, Tokens $tokens) : void{
		$controlStructureTokens = $this->getControlStructureTokens();

		foreach($tokens as $index => $token){
			if ($token === null){
				continue;
			}
			if (!$token->isGivenKind($controlStructureTokens)){
				continue;
			}

			$openBraceIndex = $tokens->getNextTokenOfKind($index, ['{']);
			if ($openBraceIndex === null){
				continue;
			}

			$prevIndex = $tokens->getPrevMeaningfulToken($openBraceIndex);
			if ($prevIndex === null){
				continue;
			}
			$tokens->removeTrailingWhitespace($prevIndex);
		}
	}

	public function getDefinition() : FixerDefinitionInterface{
		return new FixerDefinition(
			"Remove space before opening brace in control structures, functions, and closures.",
			[
				new CodeSample("<?php
if (true){
    return true;
}
				"),
				new CodeSample("<?php
function test() : void{
	return;
}
				"),
			],
		);
	}

	/**
	 * Must run after BracesFixer and CurlyBracesPositionFixer
	 */
	public function getPriority() : int{
		return -10;
	}

	public function supports(\SplFileInfo $file) : bool{
		return true;
	}

	/**
	 * @return int[]
	 */
	private function getControlStructureTokens() : array{
		$controlStructureTokens = self::STRUCTURE_TOKENS;
		if (defined('T_MATCH')){
			$controlStructureTokens[] = T_MATCH;
		}
		return $controlStructureTokens;
	}
}
