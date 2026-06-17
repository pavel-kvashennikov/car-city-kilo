<?php

declare(strict_types=1);

namespace Src\Shared\Infrastructure\Media;

use Illuminate\Http\UploadedFile;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaService
{
    public function addMedia(HasMedia $model, UploadedFile $file, string $collection = 'default'): Media
    {
        return $model
            ->addMedia($file)
            ->toMediaCollection($collection);
    }

    public function addMultipleMedia(HasMedia $model, array $files, string $collection = 'default'): void
    {
        foreach ($files as $file) {
            $this->addMedia($model, $file, $collection);
        }
    }

    public function removeMedia(Media $media): void
    {
        $media->delete();
    }

    public function getMediaUrl(HasMedia $model, string $collection = 'default', string $conversion = ''): ?string
    {
        $media = $model->getFirstMedia($collection);

        if (! $media) {
            return null;
        }

        return $conversion
            ? $media->getUrl($conversion)
            : $media->getUrl();
    }

    public function getMediaUrls(HasMedia $model, string $collection = 'default', string $conversion = ''): array
    {
        return $model->getMedia($collection)->map(function (Media $media) use ($conversion) {
            return $conversion
                ? $media->getUrl($conversion)
                : $media->getUrl();
        })->toArray();
    }

    public function setMainMedia(HasMedia $model, int $mediaId, string $collection = 'default'): void
    {
        $model->getMedia($collection)->each(function (Media $media) use ($mediaId) {
            $media->setCustomProperty('is_main', $media->id === $mediaId);
            $media->save();
        });
    }
}
