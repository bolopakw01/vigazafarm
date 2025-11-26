<?php

namespace App\Http\Controllers;

use App\Models\FeedVitaminItem;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class FeedVitaminController extends Controller
{
    public function index()
    {
        $items = FeedVitaminItem::orderBy('category')
            ->orderBy('name')
            ->get()
            ->groupBy('category');

        return view('admin.pages.sistem.dashboard.setpakvit', [
            'feedItems' => $items->get('pakan', collect()),
            'vitaminItems' => $items->get('vitamin', collect()),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);

        FeedVitaminItem::create($data);

        return $this->redirectWithMessage('Data berhasil ditambahkan.');
    }

    public function update(Request $request, FeedVitaminItem $item)
    {
        $data = $this->validateData($request, $item->id);

        $item->update($data);

        return $this->redirectWithMessage('Data berhasil diperbarui.');
    }

    public function destroy(FeedVitaminItem $item)
    {
        $item->delete();

        return $this->redirectWithMessage('Data berhasil dihapus.');
    }

    public function options(Request $request)
    {
        $category = $request->get('category');

        $query = FeedVitaminItem::query()->active()->orderBy('name');
        if ($category && in_array($category, ['pakan', 'vitamin'])) {
            $query->where('category', $category);
        }

        return response()->json([
            'data' => $query->get(['id', 'category', 'name', 'unit', 'price'])->map(function ($item) {
                return [
                    'id' => $item->id,
                    'category' => $item->category,
                    'label' => $item->name,
                    'unit' => $item->unit,
                    'price' => (float) $item->price,
                ];
            }),
        ]);
    }

    protected function validateData(Request $request, ?int $ignoreId = null): array
    {
        $data = $request->validate([
            'category' => ['required', Rule::in(['pakan', 'vitamin'])],
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('vf_feed_vitamin_items')->where(fn ($query) => $query->where('category', $request->get('category')))->ignore($ignoreId),
            ],
            'price' => ['required', 'numeric', 'min:0'],
            'unit' => ['required', 'string', 'max:50'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active', true);

        return $data;
    }

    protected function redirectWithMessage(string $message)
    {
        if (request()->wantsJson()) {
            return response()->json(['success' => true, 'message' => $message]);
        }

        return redirect()->route('admin.sistem.pakanvitamin')->with('success', $message);
    }
}
