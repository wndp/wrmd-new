<?php

namespace App\Services;

use App\Enums\AttributeOptionName;
use App\Enums\Permissions;
use App\Exceptions\RequestRecordsSaverException;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class RequestRecordsSaver
{
    private bool $shouldDelete = true;

    private bool $shouldUpdate = true;

    private bool $shouldCreate = true;

    //private Collection $relationData;

    private array $attributableFields = [];

    // private ?Permissions $createPermission = null;
    // private ?User $createUser = null;

    // private ?Permissions $deletePermission = null;

    // private ?User $deleteUser = null;

    /**
     * @var ?callable
     */
    private $fillablesTransformCallback = null;

    private Collection $existingRecordsById;

    private Collection $requestRecordsById;

    public function __construct(
        private readonly Model $model,
        private readonly string $relation,
        private readonly string $relationModel,
        private readonly Collection $requestData,
    ) {
        //
    }

    public function disableDelete(): static
    {
        $this->shouldDelete = false;

        return $this;
    }

    public function disableUpdate(): static
    {
        $this->shouldUpdate = false;

        return $this;
    }

    public function disableCreate(): static
    {
        $this->shouldCreate = false;

        return $this;
    }

    // public function setRelationData(Collection $relationData): static
    // {
    //     $this->relationData = $relationData;

    //     return $this;
    // }

    // public function addAttributableField(string $attributableField, AttributeOptionName $attributeOptionName): static
    // {
    //     $this->attributableFields[$attributableField] = $attributeOptionName;

    //     return $this;
    // }

    // public function setCreatePermission(Permissions $permission, User|Authenticatable $user): static
    // {
    //     $this->createPermission = $permission;
    //     $this->createUser = $user;

    //     return $this;
    // }

    // public function setDeletePermission(Permissions $permission, User|Authenticatable $user): static
    // {
    //     $this->deletePermission = $permission;
    //     $this->deleteUser = $user;

    //     return $this;
    // }

    // public function setFillablesTransformCallback(callable $callback): static
    // {
    //     $this->fillablesTransformCallback = $callback;

    //     return $this;
    // }

    public function save(): void
    {
        // if (!empty($this->relationData)) {
        //     $this->existingRecordsById = $this->relationData->keyBy('id');
        // } else {
        $relation = $this->relation;
        $this->existingRecordsById = $this->model->$relation->keyBy('id');
        //}

        $this->requestRecordsById = $this->requestData->whereNotNull('id')
            ->keyBy('id');

        if ($this->shouldDelete) {
            $this->delete();
        }

        if ($this->shouldUpdate) {
            $this->update();
        }

        if ($this->shouldCreate) {
            $this->create();
        }
    }

    private function delete(): void
    {
        foreach ($this->existingRecordsById as $existingRecord) {
            // If the existing record's ID is not in the request then it is considered deleted.
            if (isset($this->requestRecordsById[$existingRecord->id])) {
                continue;
            }

            // if (isset($this->deletePermission) && $this->deleteUser->cannot($this->deletePermission->value)) {
            //     throw RequestRecordsSaverException::forUserDoesNotHaveDeletePermission(
            //         $this->deleteUser,
            //         $this->model
            //     );
            // }

            $existingRecord->delete();
        }
    }

    private function update(): void
    {
        foreach ($this->existingRecordsById as $existingRecord) {
            if (! isset($this->requestRecordsById[$existingRecord->id])) {
                continue;
            }

            $requestRecord = new Collection($this->requestRecordsById[$existingRecord->id]);

            $fillables = $requestRecord->except(array_merge([
                'id',
            ], array_keys($this->attributableFields)))
                ->toArray();

            if (! empty($this->fillablesTransformCallback)) {
                $fillables = call_user_func($this->fillablesTransformCallback, $fillables, $existingRecord);
            }

            $existingRecord->fill($fillables);
            $existingRecord->save();

            // if (!empty($this->attributableFields)) {
            //     $attributablesSaver = new AttributablesSaver($existingRecord, $requestRecord, $this->attributableFields);
            //     $attributablesSaver->save();
            // }
        }
    }

    private function create(): void
    {
        $relation = $this->relation;
        $relationModel = $this->relationModel;

        $newRequestRecords = $this->requestData->whereNull('id');

        foreach ($newRequestRecords as $newRequestRecord) {
            // if (isset($this->createPermission) && $this->createUser->cannot($this->createPermission->value)) {
            //     throw RequestRecordsSaverException::forUserDoesNotHaveCreatePermission(
            //         $this->createUser,
            //         $this->model
            //     );
            // }

            $newRequestRecordCollection = new Collection($newRequestRecord);

            $fillables = $newRequestRecordCollection->except(array_merge([
                'id',
            ], array_keys($this->attributableFields)))
                ->toArray();

            if (! empty($this->fillablesTransformCallback)) {
                $fillables = call_user_func($this->fillablesTransformCallback, $fillables);
            }

            $newModelRecord = new $relationModel($fillables);

            $this->model->$relation()->save($newModelRecord);

            //$newModelRecord->attributables = new Collection();

            // if (!empty($this->attributableFields)) {
            //     $attributablesSaver = new AttributablesSaver(
            //         $newModelRecord,
            //         $newRequestRecordCollection,
            //         $this->attributableFields
            //     );
            //     $attributablesSaver->save();
            // }
        }
    }
}
