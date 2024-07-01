<?php

declare (strict_types = 1);

namespace Api\Module;

class module404 extends Module
{

    # **
    public function action(): void
    {

        $this->http->responseCode(404);

    }

}

?>