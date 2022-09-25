<?php

namespace App\Services;

use App\Repositories\Contracts\BaseRepositoryInterface;

class BaseService
{

    protected $repo;

    public function __construct(BaseRepositoryInterface $repo)
    {
        $this->repo = $repo;
    }

    /**
     * @param array $relations
     * @param array $scopes
     * @param array $columns
     * @param string $orders
     */
    public function getData(
        $relations = [], $scopes = [], $orders = ['created_at' => 'DESC'], $columns = ['*']
    ) {
        $this->repo->setRelations($relations);
        $this->repo->setScopes($scopes);
        return $this->repo->getData($columns, $orders);
    }

    /**
     * @param array $relations
     * @param array $scopes
     * @param array $columns
     * @param string $orders
     */
    public function getPaginated(
        $parPage = 15, $relations = [], $scopes = [], $orders = ['created_at' => 'DESC'], $columns = ['*']
    ) {
        $this->repo->setRelations($relations);
        $this->repo->setScopes($scopes);
        return $this->repo->getPaginated($parPage, $columns, $orders);
    }

    /**
     * @param  integer $id
     * @param array $relations
     * @param array $columns
     */
    public function show($id, $relations = [], $columns = ['*'])
    {
        $this->repo->setRelations($relations);
        return $this->repo->show($id, $columns);
    }

    /**
     * @param array $data
     * @param $relation
     */
    public function create($data, $relation = null)
    {
        return $this->repo->create($data, $relation);
    }

    /**
     * @param array $data
     * @param $relation
     */
    public function createMultiple($data, $relation = null)
    {
        return $this->repo->createMultiple($data, $relation);
    }

    /**
     * @param array $data
     * @param $relation
     */
    public function createWithRelations($data, $relation = null)
    {
        return $this->repo->createWithRelations($data, $relation);
    }

    /**
     * @param array $data
     * @param int|object $model
     */
    public function updateWithRelation($data, $model)
    {
        return $this->repo->updateWithRelation($data, $model);
    }

    /**
     * @param array $data
     * @param int|object $model
     */
    public function update($data, $model)
    {
        return $this->repo->update($data, $model);
    }

    /**
     * @param  $attr
     * @param  $value
     */
    public function updateOrCreate($attr, $value, $relation = null)
    {
        return $this->repo->updateOrCreate($attr, $value, $relation);
    }

    /**
     * @param object $model
     * @param array $data
     */
    public function createRelations($model, $data)
    {
        return $this->repo->createRelations($model, $data);
    }

    /**
     * @param object $model
     * @param array $data
     */
    public function updateRelations($model, $data)
    {
        return $this->repo->updateRelations($model, $data);
    }

    /**
     * @param int|array $id
     */
    public function delete($id)
    {
        return $this->repo->delete($id);
    }

    /**
     * @param string $relation
     * @param array $ids
     * @param array $pivots
     */
    public function sync($relation, $ids, $pivots = [])
    {
        return $this->repo->sync($relation, $ids, $pivots);
    }

    /**
     * @param  object $relation
     * @param  int $id
     */
    public function attach($relation, $id, $pivots = [])
    {
        return $this->repo->attach($relation,$id, $pivots);
    }

    /**
     * @param  object $relation
     * @param  int|array $id
     */
    public function detach($relation, $id = null)
    {
        return $this->repo->detach($relation, $id);
    }

    /**
     * @param string $relation
     * @param int $perPage
     * @param string $orderBy
     * @param string $sort
     */
    public function paginateRelation($relation, $perPage = 15, $columns = ['*'],
        $orderBy = 'created_at', $sort = 'DESC'
    ) {
        return $this->repo->paginateRelation($relation, $perPage, $columns, $orderBy, $sort);
    }

    /**
     * @param $id
     * @param array $columns
     */
    public function findOrFail($id, $columns = ['*'])
    {
        return $this->repo->findOrFail($id, $columns);
    }
    /**
     * @param $attribute
     * @param $value
     * @param array $relations
     * @param array $columns
     */
    public function findBy($attribute, $value, $relations = [], $columns = ['*'])
    {
        return $this->repo->findBy($attribute, $value, $relations, $columns);
    }

    /**
     * @param string attribute
     * @param array $values
     * @param array $relations
     */
    public function whereIn($attribute, array $values, $relations = [])
    {
        $this->repo->setRelations($relations);
        return $this->repo->whereIn($attribute, $values);
    }

    /**
     * @param string attribute
     * @param array $values
     * @param array $relations
     */
    public function whereNotIn($attribute, array $values, $relations = [])
    {
        $this->repo->setRelations($relations);
        return $this->repo->whereNotIn($attribute, $values);
    }

    /**
     * @param  array $data
     * @param  string $relation
     */
    public function updateSpecificHasManyRelation($data, $relation, $model = null)
    {
        return $this->repo->updateSpecificHasManyRelation($data, $relation, $model);
    }

    public function destroyFile($path, $id)
    {
        $this->deleteFile($path);
        $this->repo->forceDelete($id);

    }
}
