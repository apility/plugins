<?php

namespace Apility\Plugins\Contracts;

interface PolicyProvider
{
    public function getPolicy(): string;
    public function registerPolicy();
}
