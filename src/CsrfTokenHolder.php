<?php

namespace tyam\radarx;

interface CsrfTokenHolder
{
    public function setCsrfToken(string $token): void;

    public function getCsrfToken(): string;
    
    public function hasCsrfToken(): bool;
}