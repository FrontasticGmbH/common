<?php

namespace Frontastic\Common;

interface Translatable
{
    public function getTranslationCode(): string;

    public function getTranslationParameters(): object;
}
