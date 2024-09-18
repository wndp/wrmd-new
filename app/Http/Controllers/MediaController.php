<?php

namespace App\Http\Controllers;

use App\Enums\MediaCollection;
use App\Enums\MediaResource;
use App\Exceptions\RecordNotOwned;
use App\Http\Requests\MediaUploadRequest;
use App\Models\Media;
use App\Wrmd;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Number;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileCannotBeAdded;

class MediaController extends Controller
{
    /**
     * Store a new media in storage.
     */
    public function store(MediaUploadRequest $request)
    {
        $mediaResource = $request->enum('resource', MediaResource::class);

        $owningModel = $mediaResource->owningModelInstance($request->integer('resource_id'))
            ->validateOwnership(Auth::user()->current_team_id);

        $mediaCollection = $request->enum('collection', MediaCollection::class);
        $tmpFilePath = "/tmp/{$request->input('uuid')}";

        $newCollectionCount = $owningModel->media()
            ->where('collection_name', '=', $mediaCollection->value)
            ->count() + 1;

        $validator = Validator::make([
            'content_type' => Storage::mimeType($tmpFilePath),
            'file_size' => Storage::size($tmpFilePath),
            'extension' => Str::lower($request->input('extension')),
            'file_count_limit' => $newCollectionCount,
        ], [
            'content_type' => [
                Rule::in($mediaCollection->supportedMimeTypes()),
            ],
            'file_size' => 'lte:'.config('media-library.max_file_size'),
            'extension' => [
                Rule::in($mediaCollection->supportedExtensions()),
            ],
            'file_count_limit' => [
                'integer',
                "max:{$mediaCollection->limitCount()}",
            ],
        ], [
            'file_size.lte' => __('The file size must be less than or equal to :maxFileSize', [
                'maxFileSize' => Number::fileSize(config('media-library.max_file_size'))
            ])
        ]);

        if ($validator->fails()) {
            Storage::delete($tmpFilePath);
        }

        $validator->validate();

        $urlFriendlyFileName = Str::slug(pathinfo($request->input('name'), PATHINFO_FILENAME));

        $owningModel->addMediaFromDisk($request->input('key'), 's3')
            ->usingName($urlFriendlyFileName)
            ->usingFileName($urlFriendlyFileName . ".{$request->input('extension')}")
            ->withCustomProperties(array_merge(
                ['obtained_at' => Carbon::now()],
                Arr::wrap($request->input('custom_properties'))
            ))
            ->withResponsiveImages()
            ->toMediaCollection(); // $mediaCollection->value

        return response()->noContent();
    }


    /**
     * Update the specified media in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Media $media)
    {
        $request->validate([
            'resource' => [
                'required',
                Rule::enum(MediaResource::class),
            ],
            'resource_id' => [
                'required',
                'integer',
            ],
            'name' => 'required',
            'obtained_at' => 'required|date',
        ]);

        $mediaResource = $request->enum('resource', MediaResource::class);

        $owningModel = $mediaResource->owningModelInstance($request->integer('resource_id'))
            ->validateOwnership(Auth::user()->current_team_id);

        abort_unless($owningModel->media->contains($media->id), new RecordNotOwned());

        foreach ($request->only(['obtained_at', 'source', 'description', 'is_evidence', 'is_correction']) as $key => $value) {
            $media->setCustomProperty($key, $value);
        }

        $media->name = $request->input('name');
        $media->save();

        return back();
    }

    /**
     * Remove the specified media from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Media $media)
    {
        $request->validate([
            'resource' => [
                'required',
                Rule::enum(MediaResource::class),
            ],
            'resource_id' => [
                'required',
                'integer',
            ],
        ]);

        $mediaResource = $request->enum('resource', MediaResource::class);

        $owningModel = $mediaResource->owningModelInstance($request->integer('resource_id'))
            ->validateOwnership(Auth::user()->current_team_id);

        abort_unless($owningModel->media->contains($media->id), new RecordNotOwned());

        $media->delete();

        return back();
    }
}
