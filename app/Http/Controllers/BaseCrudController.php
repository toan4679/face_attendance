<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;

abstract class BaseCrudController extends Controller
{
    protected $model;          // Eloquent model class e.g. \App\Models\Khoa::class
    protected $searchable = []; // columns to search
    protected $rulesCreate = [];
    protected $rulesUpdate = [];

    public function index(Request $request)
    {
        $q = $this->model::query();

        if ($s = $request->get('search')) {
            $q->where(function($x) use ($s) {
                foreach ($this->searchable as $col) {
                    $x->orWhere($col, 'like', '%'.$s.'%');
                }
            });
        }

        // simple filters by query string equals
        foreach ($request->query() as $key => $val) {
            if (in_array($key, ['page','per_page','search','sort'])) continue;
            if (schema_has_column((new $this->model)->getTable(), $key)) {
                $q->where($key, $val);
            }
        }

        // sort
        if ($sort = $request->get('sort')) {
            foreach (explode(',', $sort) as $seg) {
                $dir = str_starts_with($seg, '-') ? 'desc' : 'asc';
                $col = ltrim($seg, '-');
                $q->orderBy($col, $dir);
            }
        } else {
            $q->latest();
        }

        $per = min((int)($request->get('per_page', 20)), 100);
        return response()->json($q->paginate($per));
    }

    public function show($id)
    {
        $data = $this->model::findOrFail($id);
        return response()->json($data);
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->rulesCreate);
        $data = $this->model::create($validated);
        return response()->json($data, 201);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate($this->rulesUpdate ?: $this->rulesCreate);
        $data = $this->model::findOrFail($id);
        $data->update($validated);
        return response()->json($data);
    }

    public function destroy($id)
    {
        $data = $this->model::findOrFail($id);
        $data->delete();
        return response()->json(null, 204);
    }
}

// helper check if column exists
if (!function_exists('schema_has_column')) {
    function schema_has_column($table, $column) {
        try { return \Illuminate\Support\Facades\Schema::hasColumn($table, $column); }
        catch (\Throwable $e) { return false; }
    }
}
