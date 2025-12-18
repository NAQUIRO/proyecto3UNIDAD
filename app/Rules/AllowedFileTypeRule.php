<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class AllowedFileTypeRule implements ValidationRule
{
    protected array $allowedTypes;
    protected int $maxSize; // en kilobytes

    public function __construct(array $allowedTypes = ['pdf', 'doc', 'docx'], int $maxSize = 10240)
    {
        $this->allowedTypes = $allowedTypes;
        $this->maxSize = $maxSize;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!$value) {
            return; // Si no hay archivo, no validar (usar nullable en las reglas)
        }

        $extension = strtolower($value->getClientOriginalExtension());
        $sizeInKB = $value->getSize() / 1024;

        if (!in_array($extension, $this->allowedTypes)) {
            $allowed = implode(', ', $this->allowedTypes);
            $fail("El archivo :attribute debe ser de tipo: {$allowed}. Tipo recibido: {$extension}.");
        }

        if ($sizeInKB > $this->maxSize) {
            $maxSizeMB = round($this->maxSize / 1024, 2);
            $fail("El archivo :attribute no debe exceder {$maxSizeMB} MB. Tamaño actual: " . round($sizeInKB / 1024, 2) . " MB.");
        }
    }
}
