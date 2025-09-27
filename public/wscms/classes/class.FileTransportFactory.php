<?php

/**
 * Factory class for custom File Transport
 */

class FileTransportFactory
{
    public static function create(string $filePath): FileTransport
    {
        return new FileTransport($filePath);
    }
}
