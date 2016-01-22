<?php

class IdFly_Sniffs_ControlStructures_ControlSignatureSniff implements PHP_CodeSniffer_Sniff
{
    public $supportedTokenizers = ['PHP'];

    public function register()
    {
        return array(
            T_TRY,
            T_CATCH,
            T_DO,
            T_WHILE,
            T_FOR,
            T_IF,
            T_FOREACH,
            T_ELSE,
            T_ELSEIF,
            T_SWITCH,
        );
    }

    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        if (isset($tokens[($stackPtr + 1)]) === false) {
            return;
        }

        // Single space after the keyword.
        $found = 1;
        if ($tokens[($stackPtr + 1)]['code'] !== T_WHITESPACE) {
            $found = 0;
        } else if ($tokens[($stackPtr + 1)]['content'] !== ' ') {
            if (strpos($tokens[($stackPtr + 1)]['content'], $phpcsFile->eolChar) !== false) {
                $found = 'newline';
            } else {
                $found = strlen($tokens[($stackPtr + 1)]['content']);
            }
        }

        if ($found !== 0) {
            $error = 'Expected no space after %s keyword; %s found';
            $data  = array(
                      strtoupper($tokens[$stackPtr]['content']),
                      $found,
                     );

            $fix = $phpcsFile->addFixableError($error, $stackPtr, 'SpaceAfterKeyword', $data);
            if ($fix === true) {
                if ($found === 0) {
                    $phpcsFile->fixer->addContent($stackPtr, ' ');
                } else {
                    $phpcsFile->fixer->replaceToken(($stackPtr + 1), ' ');
                }
            }
        }
    }
}