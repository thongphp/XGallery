<?php

namespace Tests\Traits;

use App\Facades\FlickrClient;

trait FlickrClientMock
{
    /**
     * @param string $command
     * @param null|string $jsonFileName
     */
    protected function mockFlickrClientCommand(string $command, ?string $jsonFileName = null): void
    {
        $jsonFileName = $jsonFileName ?? $command;
        $jsonFile = __DIR__.'/../__fixtures__/'.$jsonFileName.'.json';

        if (!file_exists($jsonFile)) {
            return;
        }

        FlickrClient::shouldReceive($command)->andReturn(
            $this->removeContentObject(json_decode(file_get_contents($jsonFile)))
        );
    }

    /**
     * @param object $content
     *
     * @return object
     */
    private function removeContentObject(object $content): object
    {
        foreach ($content as $key => $value) {
            if (!is_object($value)) {
                continue;
            }

            $content->{$key} = property_exists($value, '_content') ?
                $value->_content : $this->removeContentObject($value);
        }

        return $content;
    }
}
