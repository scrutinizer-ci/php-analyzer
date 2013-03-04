<?php

class Token { }

class Stream
{
    public $previous;
    public $token;
    public $next;

    private $tokens;

    /**
     * @Assertions(0)
     */
    public function setInput()
    {
        $this->tokens = array(new Token());
        $this->previous = $this->token = $this->next = null;
    }

    /** @Assertions(3) */
    public function test()
    {
        /** @type object<Token>|null */
        $x = $this->token;

        /** @type object<Token>|null */
        $x = $this->previous;

        /** @type object<Token>|null */
        $x = $this->next;
    }

    /**
     * @Assertions(0)
     */
    public function moveNext()
    {
        $this->previous = $this->token;
        $this->token = $this->next;

        $this->next = null;
        $i = 0;
        while (true) {
            $nextToken = $this->tokens[$i];
            if ($foo) {
                continue;
            }

            $this->next = $nextToken;
            break;
        }
    }
}