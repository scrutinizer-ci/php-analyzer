<?php

/**
 * Return Types per Iteration:
 * 1st: array<integer,Node>{0:Node}
 * 2nd: array<integer,Node>{0:Node,1:Node}
 * 3rd: array<integer,Node>{0:Node,1:Node,2:Node}
 * .
 * .
 * .
 * nth: array<integer,Node>{0:Node,...,n:Node}
 *
 * The special problem here is that the method is called recursively and thus a change in the
 * returned items changes the return value in the next iteration. Type Inference only stops
 * when it reaches the maximum number of allowed iterations which can take some time, and
 * slows down reviews considerably.
 *
 * Possible Solutions:
 * 1. Drop the item types on array_merge if we don't know all types that are part of the
 *    array_merge (or other merge function) call. This should generally be done as we
 *    cannot say anything reliable about item types anyway if we do not know all
 *    argument types. This should fix that situation, not sure if it fixes all situations.
 */
class Node
{
    private $subNodes = array();

    public function addSubNode(Node $node)
    {
        $this->subNodes[] = $node;
    }

    public function getSubNodesRecursive()
    {
        $subNodes = array($this);
        foreach ($this->subNodes as $subNode) {
            $subNodes = array_merge($subNodes, $subNode->getSubNodesRecursive());
        }

        return $subNodes;
    }
}
