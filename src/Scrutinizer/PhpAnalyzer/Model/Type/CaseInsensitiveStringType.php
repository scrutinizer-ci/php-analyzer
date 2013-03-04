<?php

namespace Scrutinizer\PhpAnalyzer\Model\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

class CaseInsensitiveStringType extends StringType
{
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        $declaration = parent::getSQLDeclaration($fieldDeclaration, $platform);

        switch ($platform->getName()) {
            case 'mysql':
                // The default MySQL collation is case-insensitive.
                break;

            case 'sqlite':
                $declaration .= ' COLLATE NOCASE';
                break;

            default:
                throw new \LogicException(sprintf('The platform "%s" is not supported by "string_case" type.', $platform->getName()));
        }

        return $declaration;
    }

    public function getName()
    {
        return 'string_nocase';
    }
}