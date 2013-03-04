<?php

class YamlParser {
    private $currentLineNb;
    private $currentLine;
    private $lines;
    private $refs;

    public function parse($value, $exceptionOnInvalidType = false, $objectSupport = false)
    {
        $this->currentLineNb = -1;
        $this->currentLine = '';
        $this->lines = explode("\n", $value);

        $data = array();
        $context = null;
        while (true) {
            $isRef = $isInPlace = $isProcessed = false;
            if ($x) {
                $c = $this->getRealCurrentLineNb() + 1;
                $parser = new YamlParser($c);
                $parser->refs =& $this->refs;
                $data[] = $parser->parse($this->getNextEmbedBlock(), $exceptionOnInvalidType, $objectSupport);
            } elseif ($y) {
                if ($isProcessed) {
                    // Merge keys
                    $data = $isProcessed;
                    // hash
                } elseif (!isset($values['value']) || '' == trim($values['value'], ' ') || 0 === strpos(ltrim($values['value'], ' '), '#')) {
                    // if next line is less indented or equal, then it means that the current value is null
                    if ($this->isNextLineIndented() && !$this->isNextLineUnIndentedCollection()) {
                        $data[$key] = null;
                    } else {
                        $c = $this->getRealCurrentLineNb() + 1;
                        $parser = new Parser($c);
                        $parser->refs =& $this->refs;
                        $data[$key] = $parser->parse($this->getNextEmbedBlock(), $exceptionOnInvalidType, $objectSupport);
                    }
                } else {
                    if ($isInPlace) {
                        $data = $this->refs[$isInPlace];
                    } else {
                        $data[$key] = $this->parseValue($values['value'], $exceptionOnInvalidType, $objectSupport);
                    }
                }
            } else {
                if ($x) {
                    if (is_array($value)) {
                        $first = reset($value);
                        if (is_string($first) && 0 === strpos($first, '*')) {
                            $data = array();
                            foreach ($value as $alias) {
                                $data[] = $this->refs[substr($alias, 1)];
                            }
                            $value = $data;
                        }
                    }

                    return $value;
                }

                throw new ParseException($error, $this->getRealCurrentLineNb() + 1, $this->currentLine);
            }

            if ($isRef) {
                $this->refs[$isRef] = end($data);
            }
        }

        return empty($data) ? null : $data;
    }
}