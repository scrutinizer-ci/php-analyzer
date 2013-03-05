<?php

namespace Scrutinizer\PhpAnalyzer\Command\OutputFormatter;

use Scrutinizer\PhpAnalyzer\Command\OutputFormatterInterface;
use Symfony\Component\Console\Output\OutputInterface;

use JMS\Serializer\JsonSerializationVisitor;
use JMS\Serializer\XmlSerializationVisitor;
use JMS\Serializer\Naming\CamelCaseNamingStrategy;
use JMS\Serializer\Naming\SerializedNameAnnotationStrategy;
use JMS\Serializer\SerializerBuilder;

class SerializerFormatter implements OutputFormatterInterface
{
    /**
     * @var string
     */
    private $outputFile;

    /**
     * @var string
     */
    private $outputFormat;

    public function __construct($outputFile, $outputFormat = 'xml')
    {
        $this->outputFile = $outputFile;
        $this->outputFormat = $outputFormat;
    }

    public function write(OutputInterface $output, $fileCollection)
    {
        $commentedFiles = $fileCollection->getCommentedFiles();

        $jsonVisitor = new JsonSerializationVisitor(new SerializedNameAnnotationStrategy(new CamelCaseNamingStrategy()));
        $jsonVisitor->setOptions(JSON_PRETTY_PRINT);

        $xmlVisitor = new XmlSerializationVisitor(new SerializedNameAnnotationStrategy(new CamelCaseNamingStrategy()));

        $serializer = SerializerBuilder::create()
            ->setSerializationVisitor('json', $jsonVisitor)
            ->setSerializationVisitor('xml', $xmlVisitor)
            ->build();

        if ( ! empty($this->outputFile)) {
            file_put_contents($this->outputFile, $serializer->serialize($commentedFiles, $this->outputFormat));

            return;
        }

        $output->write($serializer->serialize($commentedFiles, $this->outputFormat));
    }
}
