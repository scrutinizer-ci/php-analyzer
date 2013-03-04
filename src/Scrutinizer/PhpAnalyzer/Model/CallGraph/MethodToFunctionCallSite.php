<?php

/*
 * Copyright 2013 Johannes M. Schmitt <johannes@scrutinizer-ci.com>
 * 
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * 
 *     http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Scrutinizer\PhpAnalyzer\Model\CallGraph;

use Doctrine\ORM\Mapping as ORM;
use Scrutinizer\PhpAnalyzer\Model\AbstractFunction;
use Scrutinizer\PhpAnalyzer\Model\GlobalFunction;
use Scrutinizer\PhpAnalyzer\Model\Method;

/**
 * @ORM\Entity(readOnly = true)
 *
 * @author Johannes
 */
class MethodToFunctionCallSite extends CallSite
{
    /** @ORM\ManyToOne(targetEntity = "Scrutinizer\PhpAnalyzer\Model\Method", inversedBy = "outFunctionCallSites") */
    private $sourceMethod;

    /** @ORM\ManyToOne(targetEntity = "Scrutinizer\PhpAnalyzer\Model\GlobalFunction", inversedBy = "inMethodCallSites") */
    private $targetFunction;

    public function getSource()
    {
        return $this->sourceMethod;
    }

    public function getTarget()
    {
        return $this->targetFunction;
    }

    protected function setSource(AbstractFunction $function)
    {
        if (!$function instanceof Method) {
            throw new \InvalidArgumentException(sprintf('Expected $function of type Method, but got "%s" instead.', get_class($function)));
        }

        $this->sourceMethod = $function;
    }

    protected function setTarget(AbstractFunction $function)
    {
        if (!$function instanceof GlobalFunction) {
            throw new \InvalidArgumentException(sprintf('Expected $function of type GlobalFunction, but got "%s" instead.', get_class($function)));
        }

        $this->targetFunction = $function;
    }
}