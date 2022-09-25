<?php

namespace App\Repositories;

use App\Repositories\Contracts\BaseRepositoryInterface;
use Illuminate\Container\Container as Application;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

abstract class BaseRepository implements BaseRepositoryInterface
{
    /**
     * @var Model
     */
    protected $model;

    /**
     * @var Application
     */
    protected $app;

    /**
     * @var Collection
     */
    protected $scopes = [];

    /**
     * @var Collection
     */
    protected $requiredRelationships = [];

    /**
     * @var filter
     */
    protected $filter;

    /**
     * @throws \Exception
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->makeModel();
        $this->filter = $this->filter ? $this->app->make($this->filter) : null;
    }

    /**
     * @return string
     */
    abstract public function model();

    /**
     * @return Model
     * @throws \Exception
     */
    public function makeModel()
    {
        $model = $this->app->make($this->model());

        if (!$model instanceof Model) {
            throw new \Exception(
                "Class {$this->model()} must be an instance of Illuminate\\Database\\Eloquent\\Model"
            );
        }

        return $this->model = $model;
    }

    /**
     * @param array $relations
     */
    public function setRelations($relations)
    {
        $this->requiredRelationships = $relations;
    }

    /**
     * @return array
     */
    public function getScopes()
    {
        return $this->scopes;
    }

    /**
     * @param array $scopes
     */
    public function setScopes($scopes)
    {
        $this->scopes = $scopes;
    }

    /**
     * @param int $perPage
     * @param array $columns
     *
     * @return LengthAwarePaginator
     */
    public function paginate($perPage, $columns = ['*'])
    {
        return $this->model->paginate($perPage, $columns);
    }

    /**
     * Retrieve all data.
     *
     * @param int|null $skip
     * @param int|null $limit
     * @return Builder
     */
    public function allQuery($skip = null, $limit = null)
    {
        $query = $this->model->newQuery();

        if (!is_null($skip)) {
            $query->skip($skip);
        }

        if (!is_null($limit)) {
            $query->limit($limit);
        }

        return $query;
    }

    /**
     * Retrieve all data.
     *
     * @param int|null $skip
     * @param int|null $limit
     * @param array $columns
     *
     * @return Collection
     */
    public function all($skip = null, $limit = null, $columns = ['*'])
    {
        $query = $this->allQuery($skip, $limit);
        return $query->get($columns);
    }

    /**
     * Retrieve all records with given filter criteria
     *
     * @param int|null $skip
     * @param int|null $limit
     * @param array $columns
     *
     * @return Collection
     */
    public function getData($columns = ['*'], $orders = ['created_at' => 'DESC'])
    {
        $query = $this->applyScopes($this->model->newQuery());

        $query->with($this->requiredRelationships);
        foreach ($orders as $column => $sort) {
            $query = $query->orderBy($column, $sort);
        }
        return $query;
    }

    /**
     * Retrieve paginated records with given filter criteria
     *
     * @param int|null $skip
     * @param int|null $limit
     * @param array $columns
     *
     * @return LengthAwarePaginator
     */
    public function getPaginated($parPage = 15, $columns = ['*'], $orders = ['created_at' => 'DESC'])
    {
        $query = $this->applyScopes($this->model->newQuery());
        $query->with($this->requiredRelationships);
        foreach ($orders as $column => $sort) {
            $query = $query->orderBy($column, $sort);
        }
        return $query->paginate($parPage, $columns);
    }

    /**
     * @param array $data
     * @return Model
     */
    public function create($data, $relation = null)
    {
        $model = $relation ? $relation : $this->model;
        return $model->create($data);
    }

    /**
     * @param array $data
     * @return Model
     */
    public function createWithRelations($data, $relation = null)
    {
        $model = $relation ? $relation : $this->model;
        $model = $model->create($data);
        $this->createRelations($model, $data);
        return $model;
    }

    /**
     * @param array $data
     * @return Model
     */
    public function createRelations($model, $data)
    {
        $this->createOrUpdateOnetoOneRelations($model, $data);
        $this->createOnetoManyRelations($model, $data);
        $this->createManytoManyRelations($model, $data);
    }

    /**
     * @param array $data
     * @return Model
     */
    protected function createOrUpdateOnetoOneRelations($model, $data)
    {
        //
    }

    /**
     * @param array $data
     * @return Model
     */
    protected function createOnetoManyRelations($model, $data)
    {
        //
    }

    /**
     * @param array $data
     * @return Model
     */
    protected function createManytoManyRelations($model, $data)
    {
        //
    }

    /**
     * @param  $attr
     * @param  $value
     * @param  $relation
     *
     * @return Model
     */
    public function updateOrCreate($attr, $value, $relation = null)
    {
        $model = $relation ? $relation : $this->model;
        return $model->updateOrCreate($attr, $value);
    }

    /**
     * @param array $data
     */
    public function createMultiple(array $data, $relation = null)
    {
        $model = $relation ? $relation : $this->model;
        return $model->createMany($data);
    }

    /**
     * @param  integer $id
     * @return Model
     */
    public function show($id, array $columns = ['*'])
    {
        $model = $this->model->query();
        return $model->with($this->requiredRelationships)
            ->findOrFail($id, $columns);
    }

    /**
     * @param int $id
     * @param array $columns
     *
     * @return Model|null
     */
    public function find($id, $columns = ['*'])
    {
        return $this->model->find($id, $columns);
    }

    /**
     * @param int $id
     * @param array $columns
     *
     * @return Model
     * @throws ModelNotFoundException
     */
    public function findOrFail($id, $columns = ['*'])
    {
        return $this->model->findOrFail($id, $columns);
    }

    /**
     * @param $attribute
     * @param $value
     * @param array $columns
     *
     * @return Model|null
     */
    public function findBy($attribute, $value, $relations = [], $columns = array('*'))
    {
        $query = $this->model->newQuery();

        return $query->with($relations)
            ->where($attribute, '=', $value)->first($columns);
    }

    /**
     * @param $attribute
     * @param $value
     * @param array $relations
     * @param array $columns
     *
     * @return Model
     * @throws ModelNotFoundException
     */
    public function findByOrFail($attribute, $value, $relations = [], $columns = array('*'))
    {
        $query = $this->model->newQuery();

        return $query->with($relations)
            ->where($attribute, '=', $value)->firstOrFail($columns);
    }

    /**
     * @param array $data
     * @param int $id
     *
     * @return Model
     */
    public function update($data, $id)
    {
        is_object($id) ?
            $model = $id :
            $model = $this->findOrFail($id);

        $model->fill($data)->save();

        return $model;
    }

    /**
     * @param array $data
     * @param int $id
     *
     * @return Model
     */
    public function updateWithRelation($data, $id)
    {
        is_object($id) ?
            $model = $id :
            $model = $this->findOrFail($id);

        $model->fill($data)->save();

        $this->createOrUpdateOnetoOneRelations($model, $data);

        return $model;
    }

    /**
     * @param int|array $id
     *
     * @throws \Exception
     */
    public function delete($id)
    {
        if (is_array($id)) {
            return $this->model->destroy($id);
        }

        is_object($id) ?
            $model = $id :
            $model = $this->findOrFail($id);

        return $model->delete();
    }

    /**
     * @param int|array $id
     *
     * @throws \Exception
     */
    public function forceDelete($id)
    {
        is_object($id) ?
            $model = $id :
            $model = $this->findOrFail($id);

        return $model->forceDelete();
    }

    /**
     * @param  array $data
     *
     * @throws \Exception
     */
    public function insert(array $data)
    {
        return $this->model->insert($data);
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function exists()
    {
        return $this->model->exists();
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function count($model = null)
    {
        return $model ? $model->count() : $this->model->count();
    }

    /**
     * @return Builder
     */
    public function applyScopes($query)
    {
        $scopes = $this->scopes;
        if (count($scopes) > 0) {
            foreach ($scopes as $method) {
                $query->$method();
            }
        }
        return $query;
    }

    /**
     * @return Builder
     */
    public function applyScope($scope)
    {
        return $this->model->$scope();
    }

    /**
     * @param object $relation
     * @param array $ids
     * @param array $pivots
     */
    public function sync($relation, $data, $pivots = [])
    {
        if (sizeof($pivots) > 0) {
            $pivotData = array_fill(0, count($data), $pivots);
            $data      = array_combine($data, $pivotData);
        }
        return $relation->sync($data);
    }

    /**
     * @param  object $relation
     * @param  int $id
     */
    public function attach($relation, $id, $pivots = [])
    {
        return $relation->attach($id, $pivots);
    }

    /**
     * @param  object $relation
     * @param  int|array $id
     */
    public function detach($relation, $id = null)
    {
        return $id ? $relation->detach($id) : $relation->detach();
    }

    /**
     * @param string $relation
     * @param int $perPage
     * @param array $columns
     * @param string $orderBy
     * @param string $sort
     *
     * @return LengthAwarePaginator
     */
    public function paginateRelation(
        $relation,
        $perPage = 15,
        $columns = ['*'],
        $orderBy = 'created_at',
        $sort = 'DESC'
    ) {
        return $relation
            ->filter($this->filter)
            ->orderBy($orderBy, $sort)
            ->paginate($perPage, $columns);
    }

    /**
     * @param string attribute
     * @param array $values
     */
    public function whereIn($attribute, array $values)
    {
        return $this->model->with($this->requiredRelationships)
            ->whereIn($attribute, $values);
    }

    /**
     * @param string attribute
     * @param array $values
     */
    public function whereNotIn($attribute, array $values)
    {
        return $this->model->with($this->requiredRelationships)
            ->whereNotIn($attribute, $values);
    }

    /**
     * @param string attribute
     * @param array $value
     */
    public function where($attribute, $value)
    {
        return $this->model->where($attribute, $value);
    }

    /**
     * @param  array $data
     * @param  string $relation
     */
    public function updateSpecificHasManyRelation($data, $relation, $model = null)
    {
        $model = $model ? $model->$relation() : $this->model->$relation();
        $model->forceDelete();
        return $model->createMany($data);
    }
}
