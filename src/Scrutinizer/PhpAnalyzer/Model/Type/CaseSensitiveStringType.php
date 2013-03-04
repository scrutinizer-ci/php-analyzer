<?php

namespace Scrutinizer\PhpAnalyzer\Model\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

class CaseSensitiveStringType extends StringType
{
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        $declaration = parent::getSQLDeclaration($fieldDeclaration, $platform);

        switch ($platform->getName()) {
            case 'mysql':
                $declaration .= ' CHARACTER SET utf8 COLLATE utf8_bin';
                break;

            case 'sqlite':
                // The default sqlite collation is case-sensitive.
                break;

            default:
                throw new \LogicException(sprintf('The platform "%s" is not supported by "string_case" type.', $platform->getName()));
        }

        return $declaration;
    }

    public function getName()
    {
        return 'string_case';
    }
}