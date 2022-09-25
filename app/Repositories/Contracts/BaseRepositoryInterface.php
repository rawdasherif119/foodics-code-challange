<?php

namespace App\Repositories\Contracts;

interface BaseRepositoryInterface
{
    public function setRelations($relations);

    public function getScopes();

    public function setScopes($scopes);

    public function paginate($perPage, $columns = ['*']);

    public function allQuery($skip = null, $limit = null);

    public function all($skip = null, $limit = null, $columns = ['*']);

    public function getData($columns = '*', $orders = ['created_at' => 'DESC']);

    public function getPaginated($parPage = 15, $columns = '*', $orders = ['created_at' => 'DESC']);

    public function create($data, $relation = null);

    public function createWithRelations($data, $relation = null);

    public function createRelations($model, $data);

    public function updateOrCreate($attr, $value);

    public function createMultiple(array $data);

    public function show($id, array $columns = ['*']);

    public function find($id, $columns = ['*']);

    public function findOrFail($id, $columns = ['*']);

    public function findBy($attribute, $value, $relations = [], $columns = array('*'));

    public function findByOrFail($attribute, $value, $relations = [], $columns = array('*'));

    public function update($data, $id);

    public function updateWithRelation($data, $id);

    public function delete($id);

    public function forceDelete($id);

    public function insert(array $data);

    public function exists();

    public function count();

    public function sync($relation, $data, $pivots = []);

    public function attach($relation, $id, $pivots = []);

    public function detach($relation, $id = null);

    public function paginateRelation($relation, $perPage = 15, $columns = ['*'],
        $orderBy = 'created_at', $sort = 'DESC'
    );

    public function whereIn($attribute, array $values);

    public function whereNotIn($attribute, array $values);

    public function where($attribute, $value);
}
