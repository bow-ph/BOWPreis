<?php declare(strict_types=1);

namespace BOW\Preishoheit\Search;

use Shopware\Core\Framework\DataAbstractionLayer\Search\Term\TokenizerInterface;

class NullTokenizer implements TokenizerInterface
{
    public function tokenize(string $string): array
    {
        // Simply return the original search term as a single token
        return [$string];
    }
}
