<?php

namespace Apility\Plugins\Contracts;

interface PolicyProvider
{
    public function getPolicies(): array;
    public function registerPolicies();
}
