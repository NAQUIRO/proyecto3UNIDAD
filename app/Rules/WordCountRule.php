<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class WordCountRule implements ValidationRule
{
    protected int $minWords;
    protected int $maxWords;

    public function __construct(int $minWords = 100, int $maxWords = 500)
    {
        $this->minWords = $minWords;
        $this->maxWords = $maxWords;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $wordCount = str_word_count(strip_tags($value));

        if ($wordCount < $this->minWords) {
            $fail("El campo :attribute debe tener al menos {$this->minWords} palabras. Actual: {$wordCount} palabras.");
        }

        if ($wordCount > $this->maxWords) {
            $fail("El campo :attribute no debe exceder {$this->maxWords} palabras. Actual: {$wordCount} palabras.");
        }
    }
}
