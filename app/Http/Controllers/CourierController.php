<?php

namespace App\Http\Controllers;

use App\Models\Courier;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CourierController extends Controller
{
    public function index(Request $request)
    {
        $query = Courier::query();

        if ($search = trim((string) $request->query('search', ''))) {
            $tokens = preg_split('/\s+/', $search, -1, PREG_SPLIT_NO_EMPTY);
            foreach ($tokens as $token) {
                $query->where('name', 'like', "%{$token}%");
            }
        }

        if ($request->filled('level')) {
            $levels = collect(explode(',', (string) $request->query('level')))
                ->map(fn ($v) => (int) trim($v))
                ->filter(fn ($v) => $v >= 1 && $v <= 5)
                ->unique()->values()->all();
            if ($levels !== []) {
                $query->whereIn('level', $levels);
            }
        }

        $sort = $request->query('sort', 'name');
        $sort = in_array($sort, ['name', 'registered_at'], true) ? $sort : 'name';
        $direction = strtolower((string) $request->query('direction', 'asc')) === 'desc' ? 'desc' : 'asc';

        return response()->json(
            $query->orderBy($sort, $direction)
                ->paginate(min((int) $request->query('per_page', 15), 100))
                ->appends($request->query())
        );
    }

    public function store(Request $request)
    {
        return response()->json(
            Courier::create($request->validate($this->rules())), 201
        );
    }

    public function show(Courier $courier)
    {
        return response()->json($courier);
    }

    public function update(Request $request, Courier $courier)
    {
        $courier->update($request->validate($this->rules($courier->id, true)));

        return response()->json($courier->refresh());
    }

    public function destroy(Courier $courier)
    {
        $courier->delete();

        return response()->noContent();
    }

    private function rules(?int $ignoreId = null, bool $forUpdate = false): array
    {
        $req = fn (string $rule) => array_filter([$forUpdate ? 'sometimes' : null, $rule]);

        return [
            'name' => [...$req('required'), 'string', 'max:255'],
            'phone' => [...$req('required'), 'string', 'max:20', Rule::unique('couriers', 'phone')->ignore($ignoreId)],
            'email' => ['nullable', 'email', 'max:255', Rule::unique('couriers', 'email')->ignore($ignoreId)],
            'level' => [...$req('required'), 'integer', 'between:1,5'],
            'vehicle_type' => ['nullable', 'string', 'max:50'],
            'vehicle_plate_number' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string'],
            'status' => ['sometimes', 'string', Rule::in(['active', 'inactive'])],
            'registered_at' => [...$req('required'), 'date'],
        ];
    }
}
